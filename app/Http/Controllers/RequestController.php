<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Request as BudgetRequest;
use App\Models\RequestItem;
use App\Models\ClientCode;
use App\Models\Approval;
use App\Services\CodeGeneratorService;
use App\Services\WorkflowService;
use App\Services\ReimbursementPolicyService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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
        // View submitted requests by the auth user
        $requests = BudgetRequest::where('user_id', Auth::id())->with('approvals')->latest()->get();
        return view('requests.index', compact('requests'));
    }

    public function create()
    {
        $clientCodes = ClientCode::all();
        return view('requests.create', compact('clientCodes'));
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
        ]);

        DB::beginTransaction();
        try {
            $budgetRequest = BudgetRequest::create([
                'user_id' => Auth::id(),
                'client_code_id' => $request->client_code_id,
                'type' => $request->type,
                'title' => $request->title,
                'description' => $request->description,
                'status' => 'submitted',
                'total_amount' => 0, // will calculate
            ]);

            $total = 0;
            foreach ($request->items as $index => $itemData) {
                // Policy Checks here
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
                ]);

                // Handle file uploads
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

            // Save surat tugas (request-level attachment)
            if ($request->hasFile('surat_tugas')) {
                $path = $request->file('surat_tugas')->store('surat_tugas', 'public');
                $budgetRequest->attachments()->create([
                    'request_id' => $budgetRequest->id,
                    'file_path' => $path,
                    'file_type' => 'surat_tugas',
                ]);
            }

            // Kickoff workflow
            $nextStep = $this->workflowService->getNextApproverStep($budgetRequest);
            if ($nextStep) {
                Approval::create([
                    'request_id' => $budgetRequest->id,
                    'approval_step_id' => $nextStep->id,
                    'status' => 'pending',
                ]);
            }

            DB::commit();
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

        // Admin and Auditor can view all requests
        $canView = $user->hasRole('admin') || $user->hasRole('auditor');

        // Creator can view their own requests
        if ($request->user_id === $user->id) {
            $canView = true;
        }

        // Approvers involved in the workflow can view the request
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
}
