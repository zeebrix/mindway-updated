<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerDetail;
use App\Models\ProgramCustomer;
use App\Models\ProgramDepartment;
use App\Models\ProgramDetail;
use App\Models\ProgramPlan;
use App\Models\Session;
use App\Models\User;
use App\Traits\DataTableTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgramsController extends Controller
{
    use DataTableTrait;

    public function index(Request $request)
    {
        $config = $this->getProgramDataTableConfig($request);
        $status = $request->query('status', '1');
        $isProgram = true;
        return $this->renderDataTableView('admin.programs.index', $config, compact('status', 'isProgram'));
    }

    public function getData(Request $request)
    {
        $config = $this->getProgramDataTableConfig($request);
        return $this->handleDataTableRequest($request, User::class, $config);
    }

    protected function getProgramDataTableConfig(Request $request): array
    {
        $status = $request->query('status', '1');
        return [
            'table_id' => 'programs-datatable',
            'title' => 'Manage Programs',
            'ajax_url' => route('admin.programs.data', ['status' => $request->query('status', '1')]),
            'scope' => 'ProgramOwners',
            'relations' => 'programDetail',
            'Relationwhere' => [
                ['programDetail', 'program_type', '=', $status],
            ],
            'columns' => [
                'id' => ['title' => '#'],
                'programDetail.company_name' => [
                    'title' => 'Company Name',
                    'name' => 'programDetail.company_name',
                    'editColumn' => fn($row) => optional($row->programDetail)->company_name ?? '-',
                ],
                'programDetail.max_lic' => [
                    'title' => 'Licenses',
                    'name' => 'programDetail.max_lic',
                    'editColumn' => fn($row) => optional($row->programDetail)->max_lic ? $row->programDetail->max_lic : '-',
                ],
                'programDetail.max_sessions' => [
                    'title' => 'Max Session',
                    'name' => 'programDetail.max_sessions',
                    'editColumn' => fn($row) => optional($row->programDetail)->max_sessions ? $row->programDetail->max_sessions : '-',
                ],
                'programPlan.renewal_date' => [
                    'title' => 'Renewal',
                    'name' => 'programPlan.renewal_date',
                    'editColumn' => fn($row) => optional($row->programPlan)->renewal_date ? Carbon::parse($row->programPlan->renewal_date)->format('M d') : '-',
                ],
            ],
            'actions' => [
                'manage' => ['url' => fn($row) => route('admin.programs.edit', $row->id), 'class' => 'btn btn-primary mindway-btn-blue', 'text' => 'Manage'],
                // 'delete' => ['url' => fn($row) => route('admin.programs.destroy', $row->id), 'class' => 'btn btn-sm btn-danger action-delete', 'text' => 'Delete'],
            ],
            'raw_columns' => ['action', 'program_type'],
        ];
    }

    public function create(Request $request)
    {
        $type = $request->query('type', '1');
        return view('admin.programs.create', compact('type'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'code' => 'required|string|max:50',
            'departments' => 'required|string',
            'max_lic' => 'required|integer',
            'max_session' => 'required|integer',
            'link' => 'required|url',
            'allow_employees' => 'required|in:yes,no',
            'full_name' => 'required|string|max:255',
            'admin_email' => 'required|email|max:255|unique:users,email',
            'plan_type' => 'nullable|string',
            'annual_fee' => 'nullable|numeric',
            'cost_per_session' => 'nullable|numeric',
            'renewal_date' => 'nullable|date_format:m/d',
            'gst_registered' => 'nullable|in:yes,no',
            'logo' => 'nullable|image|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $adminUser = User::create([
                'name' => $request->full_name,
                'email' => $request->admin_email,
                'password' => Hash::make('password'),
                'user_type' => 'program',
                'status' => 'active',
            ]);

            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('program_logos', 'public');
            }

            $programDetail = ProgramDetail::create([
                'user_id' => $adminUser->id,
                'company_name' => $request->company_name,
                'max_lic' => $request->max_lic,
                'logo' => $logoPath,
                'link' => $request->link,
                'code' => $request->code,
                'max_sessions' => $request->max_session,
                'program_type' => $request->program_type,
                'trial_expire' => $request->trial_expire ?? null,
            ]);
            if ($request->filled('renewal_date')) {
                $parts = explode('/', $request->renewal_date); // ['12', '22']
                $renewalDate = \Carbon\Carbon::createFromFormat('m/y', $parts[0] . '/' . $parts[1])->endOfMonth()->format('Y-m-d');
            } else {
                $renewalDate = null;
            }
            if ($request->program_type == 1) {
                ProgramPlan::create([
                    'user_id' => $adminUser->id,
                    'program_detail_id' => $programDetail->id,
                    'type' => $request->plan_type,
                    'annual_fee' => $request->annual_fee,
                    'session_cost' => $request->cost_per_session,
                    'renewal_date' => $renewalDate,
                    'gst_registered' => $request->gst_registered === 'yes' ? true : false,
                ]);
            }
            $departments = json_decode($request->departments, true) ?? [];
            foreach ($departments as $departmentName) {
                ProgramDepartment::create([
                    'name' => $departmentName,
                    'program_detail_id' => $programDetail->id,
                    'user_id' => $adminUser->id,
                    'status' => 'active',
                ]);
            }
            if ($request->allow_employees == 'yes') {
                $employeeUser = User::create([
                    'name' => $request->full_name,
                    'email' => $request->admin_email,
                    'user_type' => 'customer',
                    'password' => Hash::make('password'),
                ]);
                CustomerDetail::create([
                    'user_id' => $employeeUser->id,
                    'program_id' => $programDetail->id,
                    'company_name' => $request->company_name,
                    'max_sessions' => $request->max_lic,
                    'level' => 'admin',

                ]);
                ProgramCustomer::create([
                    'customer_id' => $employeeUser->id,
                    'program_id' => $adminUser->id,
                ]);
            }
            $template = $request->program_type == 1 ? 'emails.active-program' : 'emails.trial-program';
            $token = encrypt($adminUser->id);
            $resetLink = url("/set-password-view/?token={$token}&email={$request->admin_email}&type=programSetPassword&name{$request->full_name}");

            sendDynamicEmailFromTemplate($adminUser->email, 'Welcome to Mindway EAP', $template, [
                'full_name' => $adminUser->name,
                'company_name' => $request->company_name,
                'access_code' => $request->code,
                'resetLink' => $resetLink
            ]);
            DB::commit();

            return redirect()->back()->with('message', 'Program created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit(Request $req, $id)
    {
        $user = User::findorfail($id);
        $user->load(['programDetail', 'programDepartment', 'programPlan']);

        $customers = User::with('customerDetail:id,user_id,program_id,application_user,counselling_user')
            ->whereRelation('customerDetail', 'program_id', $user->id)
            ->select('users.*')
            ->get();

        $totalCustomers = $customers->count();
        return view('admin.programs.edit', compact('user', 'customers', 'totalCustomers'));
    }

    public function update(Request $request, User $program)
    {
        return redirect()->route('admin.programs.edit', $program->id)->with('success', 'Program updated successfully.');
    }

    public function destroy(User $program)
    {
        $program->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Program moved to trash.']);
        }
        return redirect()->route('admin.programs.index')->with('success', 'Program moved to trash.');
    }

    public function updateStatus(Request $request, User $program)
    {
        $validated = $request->validate(['action' => 'required|in:active,deactivate,extend_trial']);
        $message = 'An error occurred.';
        $program = $program->programDetail;
        switch ($validated['action']) {
            case 'deactivate':
                $program->program_type = '0';
                $message = 'Program Deactivated';
                break;
            case 'active':
                $program->program_type = '1';
                $program->trial_expire = null;
                $message = 'Program Activated';
                break;
            case 'extend_trial':
                $program->program_type = '2';
                $program->trial_expire = Carbon::now()->addDays(14);
                $message = 'Program Trial Extended';
                break;
        }
        $program->save();
        return redirect()->route('admin.programs.index')->with('success', $message);
    }

    public function resetMaxSessions(User $program)
    {
        return back()->with('success', 'Max sessions have been reset for all employees.');
    }


    public function getCustomerData(Request $request, $user_id)
    {
        $user = User::where('id', $user_id)
            ->first();
        $users = $user->allCustomers;
        return DataTables::of($users)
            ->addColumn('name_email', function ($user) {
                $iconHtml = '';
                $customer  = $user->customerDetail;

                if ($customer->level === 'admin') {
                    $icon = $customer->is_email_sent ?? true
                        ? asset('images/icons/blue-key.png')
                        : asset('images/icons/black-key.png');

                    $iconHtml = '<a href="' . route('program.employee.email.privilege', ['customer_id' => $customer->id, 'program_id' => $user->id]) . '" 
                                title="Toggle Email Send Privilege"
                                onclick="event.preventDefault(); toggleEmailPrivilege(' . $customer->id . ')">
                                <img src="' . $icon . '" alt="key" 
                                     style="width:16px; height:16px; margin-left:5px; cursor:pointer;">
                             </a>';
                }

                return '<span class="fw-semibold">' . htmlspecialchars($customer->user->name) . $iconHtml . '</span><br>
                    <span class="fw-normal">' . htmlspecialchars($customer->user->email) . '</span>';
            })
            ->editColumn('level', function ($user) {
                $customer  = $user->customerDetail;
                $badgeClass = ($customer->level === 'member') ? 'member-style' : 'admin-style';
                return '<span class="badge btn btn-primary theme-btn ' . $badgeClass . '" 
                        data-id="' . $customer->id . '" 
                        data-level="' . $customer->level . '" 
                        onclick="openLevelModal(' . $customer->id . ', \'' . $customer->level . '\')">
                        ' . $customer->level . '
                    </span>';
            })
            ->editColumn('max_session', function ($user) use ($user_id) {
                $customer  = $user->customerDetail;
                return intval($customer->max_sessions) . '
                <a href="' . route('program.employee.add-session', ['customer_detail_id' => $customer->id]) . '"
                    class="mindway-btn btn btn-success btn-sm remove-btn"
                    style="background-color: #E4E4E4 !important;color:#7C7C7C !important;margin-left:10px;">
                    Add
                </a>
                <a href="' . route('program.employee.remove-session', ['customer_detail_id' => $customer->id]) . '"
                    class="mindway-btn btn btn-success btn-sm remove-btn"
                    style="background-color: #E4E4E4 !important;color:#7C7C7C !important;margin-left:10px;">
                    Low
                </a>';
            })
            ->addColumn('action', function ($user) use ($user_id) {
                $customer  = $user->customerDetail;
                if ($user->user_type !== 'csm') {
                    return '<a href="' . route('program.employee.delete', ['customer_id' => $user->id, 'customer_detail_id' => $customer->id]) . '"
                    class="mindway-btn btn btn-success btn-sm remove-btn"
                    style="background-color: #E4E4E4 !important;color:#7C7C7C !important">
                    Remove
                    <i class="typcn typcn-view btn-icon-append"></i>
                </a>';
                }
                return '<span class="badge bg-secondary">No permission</span>';
            })
            ->rawColumns(['name_email', 'level', 'max_session', 'action']) // Render HTML correctly
            ->make(true);
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
        $allUsers = User::with('customerDetail:id,user_id,program_id,application_user,counselling_user')
            ->whereRelation('customerDetail', 'program_id', $user->id)
            ->select('users.*')
            ->addSelect([
                'is_adopted' => CustomerDetail::selectRaw(
                    '(application_user = 1 OR counselling_user = 1)'
                )->whereColumn('customer_details.user_id', 'users.id')
            ])
            ->get();

        $adoptedUsers = $allUsers->where('is_adopted', true)->count();

        list($growthData, $labels) = calculateGrowth($user, $department_id);
        list($growthDataSession, $labelsSession) =  calculateSessionGrowth($user, $department_id);
        list($sessionReasonLabel, $sessionReasonData) = sessionReasons($user, $department_id);

        $percentageData = getSessionsReasonRate($user, $department_id);
        $customerCount = $user->customerDetail ? $user->customerDetail->count() : 0;
        $totalCount = $customerCount + $newUserCount;
        $adoptionRate = adoptionRate($user, $department_id);
        $departments = ProgramDepartment::where('program_detail_id', $user_id)->get();
        return view('admin.programs.analytics', get_defined_vars());
    }
}
