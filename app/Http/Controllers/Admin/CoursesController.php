<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Traits\DataTableTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CoursesController extends Controller
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
        return $this->renderDataTableView('admin.courses.index', $config, [
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['name' => 'Courses', 'url' => null],
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
        return $this->handleDataTableRequest($request, Course::class, $config);
    }

    /**
     * Get the configuration for the users DataTable
     *
     * @return array
     */
    protected function getUserDataTableConfig(): array
    {
        return [
            'table_id' => 'courses-datatable',
            'title' => 'Courses',
            'add_new_url' => route('admin.courses.create'),
            'add_new_text' => 'Add New Courses',
            'ajax_url' => route('admin.courses.data'),
            'page_length' => 25,
            'default_order' => [[0, 'asc']],
            'relations' => [],
            'columns' => [
                'id' => [
                    'title' => 'ID',
                    'searchable' => true,
                    'orderable' => true,
                ],
                'course_title' => [
                    'title' => 'Course Title',
                    'searchable' => true,
                    'orderable' => true,
                ],
                'course_description' => [
                    'title' => 'Course Description',
                    'searchable' => true,
                    'orderable' => true,
                    'editColumn' => function ($row) {
                        return \Illuminate\Support\Str::limit($row->course_description, 50);
                    },
                ],
                'course_thumbnail' => [
                    'title' => 'Thumbnail',
                    'orderable' => false,
                    'searchable' => false,
                    'editColumn' => function ($row) {
                        if ($row->course_thumbnail) {
                            return '<img src="' . url($row->course_thumbnail) . '" alt="' . e($row->course_title) . '" class="img-thumbnail" style="width: 100px; height: auto;">';
                        }
                        return '<span class="text-muted">No Image</span>';
                    },
                ],
                'course_duration' => [
                    'title' => 'Course Duration'
                ],
                'created_at' => [
                    'title' => 'Created At',
                    'searchable' => false,
                    'orderable' => true,
                    'type' => 'date',
                ],
            ],
            'actions' => [
                'edit' => [
                    'url' => function ($row) {
                        return route('admin.courses.edit', $row->id);
                    },
                    'class' => 'btn btn-sm btn-warning',
                    'text' => 'Edit',
                ],
                'delete' => [
                    'url' => function ($row) {
                        return route('admin.courses.destroy', $row->id);
                    },
                    'class' => 'btn btn-sm btn-danger action-delete',
                    'text' => 'Delete',
                ],
            ],

            'raw_columns' => ['course_thumbnail'],

            'language' => [
                'search' => 'Search courses:',
                'lengthMenu' => 'Show _MENU_ courses per page',
                'info' => 'Showing _START_ to _END_ of _TOTAL_ courses',
                'infoEmpty' => 'No courses found',
                'infoFiltered' => '(filtered from _MAX_ total courses)',
                'emptyTable' => 'No user data available',
                'zeroRecords' => 'No matching courses found'
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
        $course = new Course();
        return view('admin.courses.create', compact('course'));
    }

    /**
     * Store a newly created user
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_title' => 'required|string|max:255',
            'course_description' => 'nullable|string',
            'course_duration' => 'required|string|max:100',
            'course_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['_token', 'course_thumbnail']);

        if ($request->hasFile('course_thumbnail')) {
            $path = $request->file('course_thumbnail')->store('course_thumbnails');
            $data['course_thumbnail'] = Storage::url($path);
        }

        Course::create($data);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified user
     *
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function show(Course $courses)
    {
        return view('admin.courses.show', compact('courses'));
    }

    /**
     * Show the form for editing the specified user
     *
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function edit(Course $course)
    {
        return view('admin.courses.edit', compact('course'));
    }

    /**
     * Update the specified user
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'course_title' => 'required|string|max:255',
            'course_description' => 'nullable|string',
            'course_duration' => 'required|string|max:100',
            'course_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $request->except(['_token', '_method', 'course_thumbnail']);

        if ($request->hasFile('course_thumbnail')) {
            // Delete old thumbnail if it exists
            if ($course->course_thumbnail) {
                Storage::delete($course->course_thumbnail);
            }
            $path = $request->file('course_thumbnail')->store('course_thumbnails');
            $data['course_thumbnail'] = Storage::url($path);
        }

        $course->update($data);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified user
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Course $course)
    {
        if ($course->course_thumbnail) {
            Storage::delete($course->course_thumbnail);
        }
        $course->delete();
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Course deleted successfully.']);
        }

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
