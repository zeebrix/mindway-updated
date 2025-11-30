<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramDepartment;
use App\Models\ProgramDetail;
use App\Models\ProgramPlan;
use App\Models\User;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'relations' => 'ProgramDetail',
            'Relationwhere' => [
                ['ProgramDetail', 'program_type', '=', $status],
            ],
            'columns' => [
                'id' => ['title' => '#'],
                'ProgramDetail.company_name' => [
                    'title' => 'Company Name',
                    'name' => 'ProgramDetail.company_name',
                    'editColumn' => fn($row) => optional($row->ProgramDetail)->company_name ?? '-',
                ],
                'ProgramDetail.max_lic' => [
                    'title' => 'Licenses',
                    'name' => 'ProgramDetail.max_lic',
                    'editColumn' => fn($row) => optional($row->ProgramDetail)->max_lic ? $row->ProgramDetail->max_lic : '-',
                ],
                'ProgramDetail.max_sessions' => [
                    'title' => 'Max Session',
                    'name' => 'ProgramDetail.max_sessions',
                    'editColumn' => fn($row) => optional($row->ProgramDetail)->max_sessions ? $row->ProgramDetail->max_sessions : '-',
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
                'password' => Hash::make(Str::random(10)),
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

            DB::commit();

            return redirect()->back()->with('message', 'Program created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit(User $program)
    {
        $program->load(['programDetail', 'programDepartments', 'programPlan']);

        $customers = User::with('customerDetail:id,user_id,program_id,application_user,counselling_user')
            ->whereRelation('customerDetail', 'program_id', $program->id)
            ->select('users.*')
            ->get();

        $totalCustomers = $customers->count();

        return view('admin.programs.edit', compact('program', 'customers', 'totalCustomers'));
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
        return redirect()->route('admin.programs.edit', $program->id)->with('success', $message);
    }

    public function resetMaxSessions(User $program)
    {
        return back()->with('success', 'Max sessions have been reset for all employees.');
    }
}
