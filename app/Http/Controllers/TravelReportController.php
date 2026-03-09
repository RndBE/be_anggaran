<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TravelReport;
use App\Models\TravelReportApproval;
use App\Models\Request as BudgetRequest;
use App\Services\AuditLogService;
use App\Services\TravelReportWorkflowService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TravelReportController extends Controller
{
    protected TravelReportWorkflowService $workflow;

    public function __construct(TravelReportWorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * List all travel reports.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasPermission('requests.view-all')) {
            $reports = TravelReport::with(['user.division', 'request'])
                ->latest()
                ->paginate(15);
        } else {
            $reports = TravelReport::with(['user.division', 'request'])
                ->where('user_id', $user->id)
                ->latest()
                ->paginate(15);
        }

        return view('travel-reports.index', compact('reports'));
    }

    /**
     * Show form to create a new travel report.
     */
    public function create(Request $httpRequest)
    {
        $user = Auth::user();

        $availableRequests = BudgetRequest::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'paid'])
            ->whereDoesntHave('travelReport')
            ->with('clientCode')
            ->latest()
            ->get();

        $selectedRequest = null;
        if ($httpRequest->has('request_id')) {
            $selectedRequest = BudgetRequest::with(['clientCode', 'participants', 'items', 'attachments'])
                ->find($httpRequest->request_id);
        }

        return view('travel-reports.create', compact('availableRequests', 'selectedRequest'));
    }

    /**
     * Store a new travel report with grouped activities.
     */
    public function store(Request $httpRequest)
    {
        $httpRequest->validate([
            'request_id' => 'nullable|exists:requests,id',
            'destination_city' => 'required|string|max:255',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after_or_equal:departure_date',
            'surat_tugas_no' => 'nullable|string|max:255',
            'surat_tugas_date' => 'nullable|date',
            'purpose' => 'required|string',
            'conclusion' => 'required|string',
            'recommendations' => 'nullable|array',
            'recommendations.*' => 'nullable|string',
            // Grouped activities
            'activities' => 'required|array|min:1',
            'activities.*.date' => 'required|date',
            'activities.*.description' => 'required|string',
            'activities.*.results' => 'nullable|array',
            'activities.*.results.*' => 'nullable|string',
            'activities.*.issues' => 'nullable|string',
            'activities.*.conclusion' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $report = TravelReport::create([
                'request_id' => $httpRequest->request_id,
                'user_id' => Auth::id(),
                'surat_tugas_no' => $httpRequest->surat_tugas_no,
                'surat_tugas_date' => $httpRequest->surat_tugas_date,
                'destination_city' => $httpRequest->destination_city,
                'departure_date' => $httpRequest->departure_date,
                'return_date' => $httpRequest->return_date,
                'purpose' => $httpRequest->purpose,
                'conclusion' => $httpRequest->conclusion,
                'recommendations' => $httpRequest->recommendations
                    ? array_values(array_filter($httpRequest->recommendations))
                    : null,
                'status' => 'submitted',
                'approval_status' => 'in_review',
                // Aggregate results & issues from activities for quick access
                'results' => null,
                'issues' => null,
            ]);

            // Save grouped activities
            foreach ($httpRequest->activities as $i => $activityData) {
                if (empty($activityData['description']))
                    continue;

                $results = isset($activityData['results'])
                    ? array_values(array_filter($activityData['results']))
                    : null;

                $activity = $report->activities()->create([
                    'activity_date' => $activityData['date'],
                    'description' => $activityData['description'],
                    'results' => $results && count($results) ? $results : null,
                    'issues' => $activityData['issues'] ?? null,
                    'conclusion' => $activityData['conclusion'] ?? null,
                    'sort_order' => $i,
                ]);

                // Save documents linked to this activity
                if ($httpRequest->hasFile("activities.{$i}.documents")) {
                    foreach ($httpRequest->file("activities.{$i}.documents") as $j => $file) {
                        $path = $file->store('travel-report-docs', 'public');
                        $report->documents()->create([
                            'travel_report_activity_id' => $activity->id,
                            'file_path' => $path,
                            'caption' => $httpRequest->input("activities.{$i}.document_captions.{$j}"),
                            'activity_date' => $activityData['date'],
                            'sort_order' => $j,
                        ]);
                    }
                }
            }

            DB::commit();

            // Kick off first approval step
            $firstStep = $this->workflow->getNextStep($report);
            if ($firstStep) {
                TravelReportApproval::create([
                    'travel_report_id' => $report->id,
                    'approval_step_id' => $firstStep->id,
                    'step' => $firstStep->role?->slug ?? ('level' . $firstStep->required_level),
                    'step_order' => $firstStep->step_order,
                    'status' => 'pending',
                ]);
            } else {
                // No approvers needed — auto-approve
                $report->update(['approval_status' => 'approved']);
            }

            AuditLogService::log('travel_report.created', $report, [
                'destination' => $report->destination_city,
                'departure' => $report->departure_date->format('Y-m-d'),
                'return' => $report->return_date->format('Y-m-d'),
            ]);

            return redirect()->route('travel-reports.show', $report)
                ->with('success', 'Laporan Hasil Perjalanan berhasil dibuat.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Show a single travel report detail.
     */
    public function show(TravelReport $travelReport)
    {
        $travelReport->load([
            'user.division',
            'user.roles',
            'request.clientCode',
            'activities.documents',
            'documents',
        ]);

        return view('travel-reports.show', compact('travelReport'));
    }

    /**
     * Print view - formal company format.
     */
    public function print(TravelReport $travelReport)
    {
        $travelReport->load([
            'user.division',
            'user.roles',
            'request.clientCode',
            'activities.documents',
            'documents',
            'approvals.approver',
            'approvals.approvalStep.role',
        ]);

        return view('travel-reports.print', compact('travelReport'));
    }

    /**
     * Delete a travel report.
     */
    public function destroy(TravelReport $travelReport)
    {
        if ($travelReport->user_id !== Auth::id() && !Auth::user()->hasPermission('requests.view-all')) {
            abort(403);
        }

        foreach ($travelReport->documents as $doc) {
            Storage::disk('public')->delete($doc->file_path);
        }

        $travelReport->delete();

        AuditLogService::log('travel_report.deleted', $travelReport, [
            'destination' => $travelReport->destination_city,
        ]);

        return redirect()->route('travel-reports.index')
            ->with('success', 'Laporan Hasil Perjalanan berhasil dihapus.');
    }
}
