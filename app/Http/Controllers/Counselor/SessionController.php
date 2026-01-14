<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\CounsellingSession;
use App\Models\Session;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SessionController extends Controller
{

    public function index()
    {
        $user = loginUser();
        return view('counselor.client.index', get_defined_vars());
    }
    public function create() {}

    public function store(Request $request) {}

    public function edit(Session $session) {}

    public function update(Request $request, Session $session) {}

    public function destroy(Session $session) {}
    public function history()
    {
        $counselor = loginUser();
        $bookings = CounsellingSession::
            where('counselor_id', $counselor->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($session) {
                return Carbon::parse($session->created_at)->format('F Y');
            });

        return view('counselor.client.history', compact('bookings'));
    }
    public function getClientData(Request $request)
    {
        $user = User::customers();
        return DataTables::of($user)
            ->addColumn('name_email', function ($user) {
                if (!$user->customerDetail) {
                    return '-';
                }

                return $user->name . '<br><small>' . $user->email . '</small>';
            })
            ->addColumn('company_name', function ($user) {
                if (!$user->customerDetail) {
                    return '-';
                }

                return $user?->customerDetail?->company_name;
            })


            ->addColumn('max_session', function ($user) {
                $max_session = $user?->customerDetail->max_sessions ?? 0;

                if ($user->pendingRequest && $user->pendingRequest->status === 'pending') {
                    return view('partials.datatables.max_session', [
                        'max_session' => $max_session,
                        'customer' => $user,
                        'requested' => true
                    ])->render();
                }

                return view('partials.datatables.max_session', [
                    'max_session' => $max_session,
                    'customer' => $user,
                    'requested' => false
                ])->render();
            })
            ->addColumn('action', function ($user) {
                return view('partials.datatables.action_buttons', [
                    'customer' => $user
                ])->render();
            })
            ->rawColumns(['name_email', 'max_session', 'action'])
            ->make(true);
    }
}
