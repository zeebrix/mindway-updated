<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Services\DataTableService;

class DataTableController extends Controller
{
    protected $dataTableService;

    public function __construct(DataTableService $dataTableService)
    {
        $this->dataTableService = $dataTableService;
    }

    /**
     * Generic method to handle DataTable requests
     *
     * @param Request $request
     * @param string $modelClass
     * @param array $config
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request, string $modelClass, array $config = [])
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        return $this->dataTableService->buildDataTable($modelClass, $config);
    }

    /**
     * Handle Users DataTable
     */
    public function getUsers(Request $request)
    {
        $config = [
            'relations' => ['customer-details'],
            'scope' => 'customers',
            'columns' => [
                'id' => [
                    'title' => 'ID',
                    'searchable' => true,
                    'orderable' => true,
                ],
                'name' => [
                    'title' => 'Name',
                    'searchable' => true,
                    'orderable' => true,
                ],
                'email' => [
                    'title' => 'Email',
                    'searchable' => true,
                    'orderable' => true,
                ],
                'role' => [
                    'title' => 'Role',
                    'searchable' => true,
                    'orderable' => true,
                ],
                'status' => [
                    'title' => 'Status',
                    'searchable' => true,
                    'orderable' => true,
                ],
                'created_at' => [
                    'title' => 'Created At',
                    'searchable' => false,
                    'orderable' => true,
                    'type' => 'date',
                ],
            ],
            'custom_columns' => [
                'improve' => function ($row) {
                    return $row->improve ?? 'Not selected';
                },
                'goal_id' => function ($row) {
                    return $row->improve ?? 'Not Added';
                },
            ],
            'actions' => [
                'delete' => [
                    'url' => function ($row) {
                        return url('/manage-admin/delete-customer', ['id' => $row->id]);
                    },
                    'class' => 'btn btn-sm btn-danger',
                    'text' => 'Delete',
                    'permission' => function ($user) {
                        return $user->user_type !== 'csm';
                    },
                    'fallback' => '<span class="badge bg-secondary">You don\'t have permission</span>',
                ],
            ],
        ];

        return $this->getData($request, \App\Models\User::class, $config);
    }

    /**
     * Handle Sessions DataTable
     */
    public function getSessions(Request $request)
    {
        $config = [
            'relations' => ['brevoUser.counselor'],
            'columns' => [
                'id' => [
                    'title' => 'ID',
                    'searchable' => true,
                    'orderable' => true,
                ],
                'client_name' => [
                    'title' => 'Client',
                    'searchable' => true,
                    'orderable' => true,
                ],
                'company' => [
                    'title' => 'Company',
                    'searchable' => true,
                    'orderable' => true,
                ],
                'date' => [
                    'title' => 'Date',
                    'searchable' => true,
                    'orderable' => true,
                    'type' => 'date',
                ],
                'time' => [
                    'title' => 'Time',
                    'searchable' => false,
                    'orderable' => true,
                ],
                'format' => [
                    'title' => 'Format',
                    'searchable' => true,
                    'orderable' => true,
                ],
                'status' => [
                    'title' => 'Status',
                    'searchable' => true,
                    'orderable' => true,
                ],
            ],
            'custom_columns' => [
                'client_name' => function ($row) {
                    return optional($row->brevoUser)->name ?? 'N/A';
                },
                'company' => function ($row) {
                    return optional($row->brevoUser->counselor)->name ?? '-';
                },
                'date' => function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('M d, Y');
                },
                'time' => function ($row) {
                    return \Carbon\Carbon::parse($row->created_at)->format('H:i');
                },
                'format' => function ($row) {
                    return ucfirst($row->communication_method);
                },
                'status' => function ($row) {
                    return ucfirst($row->status ?? 'completed');
                },
            ],
            'actions' => [
                'view' => [
                    'url' => function ($row) {
                        return route('sessions.show', $row->id);
                    },
                    'class' => 'btn btn-sm btn-primary',
                    'text' => 'View',
                ],
                'edit' => [
                    'url' => function ($row) {
                        return route('sessions.edit', $row->id);
                    },
                    'class' => 'btn btn-sm btn-warning',
                    'text' => 'Edit',
                    'permission' => function ($user) {
                        return $user->can('edit-sessions');
                    },
                ],
            ],
        ];

        return $this->getData($request, \App\Models\Booking::class, $config);
    }
}