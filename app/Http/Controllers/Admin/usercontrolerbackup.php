<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\DataTableTrait;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use DataTableTrait;

    /**
     * Display the users index page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $config = $this->getUserDataTableConfig();
        return $this->renderDataTableView('admin.users.index', $config, [
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['name' => 'Users', 'url' => null],
            ],
        ]);
    }

    /**
     * Handle AJAX requests for users DataTable
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getData(Request $request)
    {
        $config = $this->getUserDataTableConfig();
        return $this->handleDataTableRequest($request, User::class, $config);
    }

    /**
     * Get the configuration for the users DataTable
     *
     * @return array
     */
    protected function getUserDataTableConfig(): array
    {
        return [
            'table_id' => 'users-datatable',
            'title' => 'Users',
            'add_new_url' => route('admin.users.create'),
            'add_new_text' => 'Add New User',
            'ajax_url' => route('admin.users.data'),
            'page_length' => 25,
            'default_order' => [[0, 'asc']],
            'relations' => ['customerDetail'],
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
                'user_type' => [
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
                'created_at' => function ($row) {
                    return $row->created_at->format('M d, Y H:i');
                },
                'status' => function ($row) {
                    $statusClass = $row->status === 'active' ? 'success' : 'secondary';
                    return "<span class=\"badge bg-{$statusClass}\">" . ucfirst($row->status) . "</span>";
                },
            ],

            'actions' => [
                'view' => [
                    'url' => function ($row) {
                        return route('admin.users.show', $row->id);
                    },
                    'class' => 'btn btn-sm btn-info',
                    'text' => 'View',
                ],
                'edit' => [
                    'url' => function ($row) {
                        return route('admin.users.edit', $row->id);
                    },
                    'class' => 'btn btn-sm btn-warning',
                    'text' => 'Edit',
                ],
                'delete' => [
                    'url' => function ($row) {
                        return route('admin.users.destroy', $row->id);
                    },
                    'class' => 'btn btn-sm btn-danger',
                    'text' => 'Delete',
                    'attributes' => 'onclick="return confirm(\'Are you sure you want to delete this user?\')"',
                    'permission' => function ($user) {
                        return $user->user_type !== 'csm';
                    },
                    'fallback' => '<span class="badge bg-secondary">No Permission</span>',
                ],
            ],

            'raw_columns' => ['status'],

            'language' => [
                'search' => 'Search users:',
                'lengthMenu' => 'Show _MENU_ users per page',
                'info' => 'Showing _START_ to _END_ of _TOTAL_ users',
                'infoEmpty' => 'No users found',
                'infoFiltered' => '(filtered from _MAX_ total users)',
                'emptyTable' => 'No user data available',
                'zeroRecords' => 'No matching users found'
            ],
        ];
    }

    /**
     * Show the form for creating a new user
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Implementation for storing user
        // ...

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user
     *
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     *
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        // Implementation for updating user
        // ...

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        // Implementation for deleting user
        // ...

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
