<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Request as BudgetRequest;
use App\Models\RequestItem;
use App\Models\ClientCode;
use App\Models\TravelZone;
use App\Models\User;
use App\Models\Approval;
use App\Services\CodeGeneratorService;
use App\Services\WorkflowService;
use App\Services\ReimbursementPolicyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogService;

class RequestController extends Controller
{
    protected $codeGenerator;
    protected $workflowService;
    protected $policyService;

    public function __construct(CodeGeneratorService $codeGen, WorkflowService $workflow, ReimbursementPolicyService $policy)
    {
        $this->codeGenerator = $codeGen;
        $this->workflowService = $workflow;
        $this->policyService = $policy;
    }

    public function index()
    {

        $requests = BudgetRequest::where('user_id', Auth::id())->with('approvals')->latest()->get();
        return view('requests.index', compact('requests'));
    }

    public function create()
    {
        $clientCodes = ClientCode::all();
        $travelZones = TravelZone::orderBy('zone')->get();
        $users = User::orderBy('name')->get(['id', 'name']);
        return view('requests.create', compact('clientCodes', 'travelZones', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:budget,reimbursement',
            'title' => 'required|string|max:255',
            'client_code_id' => 'required|exists:client_codes,id',
            'items' => 'required|array',
            'items.*.type' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
            'surat_tugas' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'surat_tugas_urut' => 'nullable|integer|min:1',
            'surat_tugas_date' => 'nullable|date',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            // Generate surat tugas number
            $suratTugasNo = null;
            if ($request->surat_tugas_urut && $request->surat_tugas_date) {
                $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
                $stDate = \Carbon\Carbon::parse($request->surat_tugas_date);
                $urut = str_pad($request->surat_tugas_urut, 3, '0', STR_PAD_LEFT);
                $bulan = $romanMonths[$stDate->month - 1];
                $tahun = $stDate->year;
                $suratTugasNo = "{$urut}/ST-ATC/{$bulan}/{$tahun}";
            }

            $budgetRequest = BudgetRequest::create([
                'user_id' => Auth::id(),
                'client_code_id' => $request->client_code_id,
                'type' => $request->type,
                'title' => $request->title,
                'description' => $request->description,
                'status' => 'submitted',
                'total_amount' => 0,
                'surat_tugas_urut' => $request->surat_tugas_urut,
                'surat_tugas_date' => $request->surat_tugas_date,
                'surat_tugas_no' => $suratTugasNo,
            ]);

            $total = 0;
            foreach ($request->items as $index => $itemData) {

                if ($request->type === 'reimbursement') {
                    if ($itemData['type'] === 'transport' && !$this->policyService->validateTransport($itemData['description'] ?? '')) {
                        throw new \Exception("Public transport cannot be reimbursed.");
                    }
                    if ($itemData['type'] === 'meal_customer' && !$this->policyService->validateMealCustomer($itemData['amount'])) {
                        throw new \Exception("Meal customer exceeds limit of 250,000.");
                    }
                }

                $reqItem = $budgetRequest->items()->create([
                    'type' => $itemData['type'],
                    'description' => $itemData['description'] ?? '',
                    'amount' => $itemData['amount'],
                    'travel_zone_id' => ($itemData['type'] === 'lumpsum' && !empty($itemData['travel_zone_id']))
                        ? $itemData['travel_zone_id'] : null,
                    'person_count' => ($itemData['type'] === 'lumpsum' && !empty($itemData['person_count']))
                        ? (int) $itemData['person_count'] : null,
                    'day_count' => ($itemData['type'] === 'lumpsum' && !empty($itemData['day_count']))
                        ? (int) $itemData['day_count'] : null,
                ]);


                if ($request->hasFile("items.{$index}.attachment")) {
                    $file = $request->file("items.{$index}.attachment");
                    $path = $file->store('attachments', 'public');
                    $reqItem->attachments()->create([
                        'request_id' => $budgetRequest->id,
                        'file_path' => $path,
                        'file_type' => 'receipt'
                    ]);
                }

                $total += $itemData['amount'];
            }

            $budgetRequest->update(['total_amount' => $total]);


            if ($request->hasFile('surat_tugas')) {
                $path = $request->file('surat_tugas')->store('surat_tugas', 'public');
                $budgetRequest->attachments()->create([
                    'request_id' => $budgetRequest->id,
                    'file_path' => $path,
                    'file_type' => 'surat_tugas',
                ]);
            }


            if ($request->filled('participants')) {
                $budgetRequest->participants()->sync($request->participants);
            }


            $nextStep = $this->workflowService->getNextApproverStep($budgetRequest);
            if ($nextStep) {
                Approval::create([
                    'request_id' => $budgetRequest->id,
                    'approval_step_id' => $nextStep->id,
                    'status' => 'pending',
                ]);
            } else {

                $budgetRequest->update(['status' => 'approved']);
            }

            DB::commit();
            AuditLogService::log('request.created', $budgetRequest, [
                'title' => $budgetRequest->title,
                'type' => $budgetRequest->type,
                'total_amount' => $budgetRequest->total_amount,
            ]);
            return redirect()->route('requests.index')->with('success', 'Request submitted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function show(BudgetRequest $request)
    {
        $request->load(['items.attachments', 'approvals.step.role', 'approvals.approver', 'clientCode']);

        $user = Auth::user();


        $canView = $user->hasRole('admin') || $user->hasRole('auditor');


        if ($request->user_id === $user->id) {
            $canView = true;
        }


        if (!$canView) {
            $approverRoles = $request->approvals->map(function ($approval) {
                return $approval->step->role->slug;
            })->toArray();

            foreach ($approverRoles as $roleSlug) {
                if ($user->hasRole($roleSlug)) {
                    $canView = true;
                    break;
                }
            }
        }

        if (!$canView) {
            abort(403, 'Unauthorized action. You are not authorized to view this request.');
        }

        return view('requests.show', compact('request'));
    }

    public function destroy(BudgetRequest $request)
    {
        $user = auth()->user();


        $isOwner = $request->user_id === $user->id;
        $canForce = $user->hasPermission('requests.delete');

        if (!$isOwner && !$canForce) {
            abort(403, 'Anda tidak berhak menghapus request ini.');
        }


        $deletableStatuses = ['draft', 'submitted', 'revision_requested'];
        if (!$canForce && !in_array($request->status, $deletableStatuses)) {
            return back()->withErrors(['error' => 'Request yang sudah diproses tidak dapat dihapus.']);
        }

        $title = $request->title;


        DB::beginTransaction();
        try {
            $request->approvals()->delete();
            $request->attachments()->delete();
            $request->items->each(fn(\App\Models\RequestItem $item) => $item->attachments()->delete());
            $request->items()->delete();
            $request->participants()->detach();
            $request->delete();

            AuditLogService::log('request.deleted', null, [
                'request_id' => $request->id,
                'title' => $title,
                'deleted_by' => $user->id,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menghapus: ' . $e->getMessage()]);
        }

        return redirect()->route('requests.index')
            ->with('success', "Request \"{$title}\" berhasil dihapus.");
    }
}
