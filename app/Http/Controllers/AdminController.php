<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\programDetail;
use App\Models\CounsellingSession;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $recentCustomers = User::where('user_type', 'customer')
            ->latest()
            ->take(5)
            ->get();
        return view('admin.dashboard', [
            'recentCustomers' => $recentCustomers,
        ]);
    }
}
