<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Request as BudgetRequest;
use App\Models\ApprovalFlow;
use App\Models\ApprovalStep;

class ReportController extends Controller
{
    public function index()
    {
        $requests = BudgetRequest::with(['user', 'clientCode'])->latest()->get();
        return view('reports.index', compact('requests'));
    }

    public function show(BudgetRequest $report)
    {
        $report->load([
            'user.division',
            'clientCode',
            'items',
            'attachments',
            'approvals.step.role',
            'approvals.approver',
            'participants',
        ]);

        // Determine which flow was used
        $flowId = $report->approvals->first()?->step?->approval_flow_id;
        $allSteps = $flowId
            ? ApprovalStep::with('role')->where('approval_flow_id', $flowId)->orderBy('step_order')->get()
            : collect();

        // Map approvals by step id for quick lookup
        $approvalsByStep = $report->approvals->keyBy('approval_step_id');

        return view('reports.show', compact('report', 'allSteps', 'approvalsByStep'));
    }

    public function exportCsv()
    {
        $requests = BudgetRequest::with(['user', 'clientCode'])->latest()->get();

        $filename = "reports_export_" . date('Y-m-d') . ".csv";
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID', 'Requestor', 'Client Code', 'Type', 'Total Amount', 'Status', 'Submitted At'];

        $callback = function () use ($requests, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($requests as $req) {
                $client = $req->clientCode ? $req->clientCode->prefix . '-' . $req->clientCode->instansi_singkat : '-';
                $row['ID'] = 'REQ-' . str_pad($req->id, 4, '0', STR_PAD_LEFT);
                $row['Requestor'] = $req->user->name;
                $row['Client Code'] = $client;
                $row['Type'] = ucfirst($req->type);
                $row['Total Amount'] = $req->total_amount;
                $row['Status'] = strtoupper($req->status);
                $row['Submitted At'] = $req->created_at->format('Y-m-d H:i:s');

                fputcsv($file, [$row['ID'], $row['Requestor'], $row['Client Code'], $row['Type'], $row['Total Amount'], $row['Status'], $row['Submitted At']]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
