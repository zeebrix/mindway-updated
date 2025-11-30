<?php

namespace App\Http\Controllers\Counselor;

use App\Http\Controllers\Controller;
use App\Models\CounsellingSession;
use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SessionController extends Controller
{

    public function index()
    {
        $user = loginUser();
        return view('counselor.session.index', get_defined_vars());
    }
    public function create() {}

    public function store(Request $request) {}

    public function edit(Session $session) {}

    public function update(Request $request, Session $session) {}

    public function destroy(Session $session) {}
    public function history()
    {
        $counselor = loginUser();
        $bookings = CounsellingSession::where('counselor_id', $counselor->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($session) {
                return Carbon::parse($session->created_at)->format('F Y');
            });

        return view('counselor.session.history', compact('bookings'));
    }
}
