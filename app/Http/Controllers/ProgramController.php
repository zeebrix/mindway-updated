<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CustomerDetail;
use App\Models\ProgramDepartment;
use App\Models\Session;
use App\Models\SessionRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Database\QueryException;

class ProgramController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }
    public function dashboard()
    {



        $program = Auth::user();
        if (!$program->ProgramOwners()) {
            Auth::logout();
            return redirect()->route('login');
        }
        list($leftDays, $is_trial) = getProgramTrialInfo($program);
        $allUsers = $program->allCustomers;
        $adoptedUsers = 0;
        foreach ($allUsers as $key => $u) {
            if ($u?->customerDetail?->application_user == 1 || $u?->customerDetail?->counselling_user == 1) {
                $adoptedUsers = $adoptedUsers + 1;
            }
        }

        $totalUsers = $allUsers->count();
        $adoptionRate = $totalUsers > 0
            ? round(($adoptedUsers / $totalUsers) * 100, 2)
            : 0;
        return view('program.dashboard', [
            'program' => $program,
            'is_trial' => $is_trial,
            'allUsers' => $allUsers,
            'adoptedUsers' => $adoptedUsers,
            'adoptionRate' => $adoptionRate,
            'leftDays'  => $leftDays
        ]);
    }
    public function getAnalytics(Request $request)
    {
        $department_id = null;
        if ($request->has('department')) {
            $department_id = $request->department;
        }
        $user = Auth::user();
        $user_id = $user->id;
        $newUserCount = Session::where('new_user', 'Yes')
            ->where('user_id', $user_id);
        if ($department_id) {
            $newUserCount->where('department_id', $department_id);
        }
        $newUserCount = $newUserCount->count();

        $totalSessions = Session::where('user_id', $user_id);

        if ($department_id) {
            $totalSessions->where('department_id', $department_id);
        }
        $totalSessions = $totalSessions->count();
        list($leftDays, $is_trial) = getProgramTrialInfo($user);
        $allUsers = $user->allCustomers;

        $adoptedUsers = 0;
        foreach ($allUsers as $key => $u) {
            if ($u?->customerDetail?->application_user == 1 || $u?->customerDetail?->counselling_user == 1) {
                $adoptedUsers = $adoptedUsers + 1;
            }
        }

        list($growthData, $labels) = calculateGrowth($user, $department_id);
        list($growthDataSession, $labelsSession) =  calculateSessionGrowth($user, $department_id);
        list($sessionReasonLabel, $sessionReasonData) = sessionReasons($user, $department_id);

        $percentageData = getSessionsReasonRate($user, $department_id);
        $customerCount = $user->customerDetail ? $user->customerDetail->count() : 0;
        $totalCount = $customerCount + $newUserCount;
        $totalUsers = $allUsers->count();
        $adoptionRate = $totalUsers > 0
            ? round(($adoptedUsers / $totalUsers) * 100, 2)
            : 0;
        $departments = ProgramDepartment::where('program_detail_id', $user_id)->get();
        return view('program.analytics.index', get_defined_vars());
    }
    public function getEmployees()
    {
        $user = Auth::user();
        list($leftDays, $is_trial) = getProgramTrialInfo($user);
        $user_id = $user->id;
        $customers = $user->allCustomers;
        return view('program.employees.index', get_defined_vars());
    }
    public function getRequests(Request $request)
    {
        $status = $request->get('status', 'pending');
        $user = Auth::user();
        $requests = SessionRequest::where(['status' => $status, 'program_id' => $user->id])->orderBy('created_at', 'desc')->paginate(10);
        return view('program.session-request.index', get_defined_vars());
    }
    public function getSettings()
    {

        $user = Auth::user();

        list($leftDays, $is_trial) = getProgramTrialInfo($user);

        $plan = $user->programPlan;
        $secret = null;
        $qrCodeUrl = null;
        if ($user->is_2fa_enabled) {
            $google2fa = new Google2FA();
            $secret = $user->google2fa_secret;
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            );
        }
        return view('program.settings.index', get_defined_vars());
    }
    public function storeEmployee(StoreCustomerRequest $request)
    {
        $user = auth()->user();

        if (! $user->can('access-admin-panel') && ! $user->can('access-program-panel')) {
            abort(403, 'Unauthorized');
        }
        try {
            $programUser = User::where('id', $request->programUser)->first();
            $this->customerService->createCustomer($request->validated(), $programUser);
            return back()->with('message', 'Record added successfully.');
        } catch (QueryException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                return back()->with('error', 'User is already registered. Duplicate emails are not allowed.');
            }
            return back()->with('error', 'A database error occurred: ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
    public function addEmployeeSession(Request $request)
    {
        $user = auth()->user();

        if (! $user->can('access-admin-panel') && ! $user->can('access-program-panel')) {
            abort(403, 'Unauthorized');
        }
        try {
            $customerDetail = CustomerDetail::where('id', $request->customer_detail_id)->first();
            $customerDetail->max_sessions = $customerDetail->max_sessions + 1;
            $customerDetail->save();
            return back()->with('message', 'Session added successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
    public function removeEmployeeSession(Request $request)
    {
        $user = auth()->user();

        if (! $user->can('access-admin-panel') && ! $user->can('access-program-panel')) {
            abort(403, 'Unauthorized');
        }
        try {
            $customerDetail = CustomerDetail::where('id', $request->customer_detail_id)->first();
            $customerDetail->max_sessions = $customerDetail->max_sessions - 1;
            $customerDetail->save();
            return back()->with('message', 'Session removed successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
    public function removeEmployee(Request $request)
    {
        $user = auth()->user();

        if (! $user->can('access-admin-panel') && ! $user->can('access-program-panel')) {
            abort(403, 'Unauthorized');
        }
        try {
            User::where('id', $request->customer_id)->delete();
            CustomerDetail::where('id', $request->customer_detail_id)->delete();
            return back()->with('message', 'Customer deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
}
