<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RequestSession;
use App\Models\Program;
use App\Models\Customer;
use App\Models\SessionRequest;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RequestSessionsController extends Controller
{
    use DataTableTrait;

    /**
     * Display the listing of the resource (the main datatable view).
     */
    public function index(Request $request)
    {
        $config = $this->getRequestSessionDataTableConfig($request);
        $status = $request->query('status', 'pending'); // Default to 'pending' tab
        return $this->renderDataTableView('admin.request-sessions.index', $config, compact('status'));
    }

    public function getData(Request $request)
    {
        $config = $this->getRequestSessionDataTableConfig($request);
        $query = SessionRequest::class;

        // if ($request->has('status') && in_array($request->status, ['pending', 'denied', 'accepted'])) {
        //     $query->where('status', $request->status);
        // }

        return $this->handleDataTableRequest($request, $query, $config);
    }

    protected function getRequestSessionDataTableConfig(Request $request): array
    {
        return [
            'table_id' => 'request-sessions-datatable',
            'title' => 'Requested Sessions',
            'relations' => ['customer','counselor'],
            'ajax_url' => route('admin.request-sessions.data', ['status' => $request->query('status', 'pending')]),
            'columns' => [
                'id' => ['title' => 'Request ID'],
                'request_date' => [
                    'title' => 'Date Requested',
                    'editColumn' => fn($row) => Carbon::parse($row->request_date)->format('d/m/Y'),
                ],
                'request_days' => [
                    'title' => 'Requested',
                    'name' => 'status', // So it can be filtered by status text
                    'editColumn' => function ($row) {
                        $statusMap = [
                            'pending' => 'further',
                            'accepted' => 'approved',
                            'denied' => 'denied',
                        ];
                        return "{$row->request_days} {$statusMap[$row->status]}";
                    },
                ],
                'customre_brevo_data_id' => ['title' => 'Empl. ID'],
            ],
            'actions' => [
                'review' => [
                    'url' => fn($row) => route('admin.request-sessions.show', $row->id),
                    'class' => 'btn btn-sm btn-primary review-btn mindway-btn',
                    'text' => 'Review Request',
                ],
            ],
        ];
    }

    /**
     * Display the specified resource. (Used to fetch data for the modal).
     */
    public function show(RequestSession $request_session)
    {
        // Route Model Binding gives us the object automatically.
        $request_session->load(['customer', 'counselor']);

        return response()->json([
            'success' => true,
            'client_name' => $request_session->customer->name ?? 'N/A',
            'client_email' => $request_session->customer->email ?? 'N/A',
            'client_id' => $request_session->customer->id ?? 'N/A',
            'counselor_name' => $request_session->counselor->name ?? 'N/A',
            'reasons' => $request_session->reasons ?? 'N/A',
            'requested_date' => Carbon::parse($request_session->request_date)->format('d/m/Y'),
            'approved_date' => $request_session->accepted_date ? Carbon::parse($request_session->accepted_date)->format('d/m/Y') : 'N/A',
            'denied_date' => $request_session->denied_date ? Carbon::parse($request_session->denied_date)->format('d/m/Y') : 'N/A',
            'requested_days' => $request_session->request_days ?? 'N/A',
            'request_id' => $request_session->id,
        ]);
    }

    /**
     * Approve the session request.
     */
    public function approve(Request $request, RequestSession $request_session)
    {
        $validated = $request->validate(['request_session_count' => 'required|integer|min:1|max:5']);
        
        // Update RequestSession
        $request_session->update([
            'request_days' => $validated['request_session_count'],
            'status' => 'accepted',
            'accepted_date' => now(),
        ]);

        // Update Customer/Program sessions
        $customer = $request_session->customer;
        if ($customer) {
            $customer->increment('max_session', $validated['request_session_count']);
            
            // Also update the associated app_customer if it exists
            if ($customer->app_customer_id && $appCustomer = Customer::find($customer->app_customer_id)) {
                $appCustomer->increment('max_session', $validated['request_session_count']);
            }
        }
        
        // Also increment the main program's session count
        if ($program = Program::find($request_session->program_id)) {
            $program->increment('max_session', $validated['request_session_count']);
        }

        // Send notification emails
        $this->sendApprovalEmails($request_session);

        return back()->with('message', 'Request Approved Successfully!');
    }

    /**
     * Deny the session request.
     */
    public function deny(RequestSession $request_session)
    {
        $request_session->update([
            'status' => 'denied',
            'denied_date' => now(),
        ]);

        // Send notification emails
        $this->sendDenialEmails($request_session);

        return back()->with('message', 'Request Denied!');
    }

    // --- Helper Methods for Email Notifications ---
    private function sendApprovalEmails(RequestSession $request_session)
    {
        $admin = Auth::user();
        // Send email to employer/admin
        sendDynamicEmailFromTemplate(
            $admin->email,
            'Employer Notification – Sessions Approved (Request #' . $request_session->id . ')',
            'emails.request-sessions.employer-notification-approve',
            [
                'admin_name' => $admin->name,
                'approval_date' => now()->format('d/m/Y'),
                'approved_quantity' => $request_session->request_days,
                'approved_status' => 'Yes',
                'request_id' => $request_session->id,
            ]
        );
        // Send email to counselor
        // $this->sendEmailToCounselor($request_session, 'accepted');
    }

    private function sendDenialEmails(RequestSession $request_session)
    {
        $admin = Auth::user();
        // Send email to employer/admin
        sendDynamicEmailFromTemplate(
            $admin->email,
            'Session Denial Confirmation (Request #' . $request_session->id . ')',
            'emails.request-sessions.employer-notification-denied',
            [
                'admin_name' => $admin->name,
                'denial_date' => now()->format('d/m/Y'),
                'approved_quantity' => 0,
                'approved_status' => 'No',
                'request_id' => $request_session->id,
            ]
        );
        // Send email to counselor
        // $this->sendEmailToCounselor($request_session, 'denied');
    }
}
