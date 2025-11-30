<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryCourse;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryCoursesController extends Controller
{
    use DataTableTrait;

    public function index()
    {
        $config = $this->getCategoryCourseDataTableConfig();
        return $this->renderDataTableView('admin.category_courses.index', $config);
    }

    public function getData(Request $request)
    {
        $config = $this->getCategoryCourseDataTableConfig();
        return $this->handleDataTableRequest($request, CategoryCourse::class, $config);
    }

    protected function getCategoryCourseDataTableConfig(): array
    {
        return [
            'table_id' => 'category-courses-datatable',
            'title' => 'Category Courses',
            'add_new_url' => route('admin.category-courses.create'),
            'ajax_url' => route('admin.category-courses.data'),
            'relations' => ['category'], // Eager load the category relationship
            'columns' => [
                'id' => ['title' => 'ID'],
                'thumbnail' => [
                    'title' => 'Thumbnail',
                    'orderable' => false, 'searchable' => false,
                    'editColumn' => function ($row) {
                        if ($row->thumbnail) {
                            return '<img src="' . Storage::url($row->thumbnail) . '" alt="Thumbnail" class="img-thumbnail" style="width: 100px;">';
                        }
                        return 'No Image';
                    },
                ],
                'title' => ['title' => 'Title'],
                'category.name' => ['title' => 'Category', 'name' => 'category.name'],
                'created_at' => ['title' => 'Created At', 'type' => 'date'],
            ],
            'actions' => [
                'edit' => ['url' => fn($row) => route('admin.category-courses.edit', $row->id), 'class' => 'btn btn-sm btn-warning', 'text' => 'Edit'],
                'delete' => ['url' => fn($row) => route('admin.category-courses.destroy', $row->id), 'class' => 'btn btn-sm btn-danger action-delete', 'text' => 'Delete'],
            ],
            'raw_columns' => ['action', 'thumbnail'],
        ];
    }

    public function create()
    {
        $categories = Category::where('status', 'active')->pluck('name', 'id');
        return view('admin.category_courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('public/category_course_thumbnails');
        }

        CategoryCourse::create($validated);
        return redirect()->route('admin.category-courses.index')->with('success', 'Course created successfully.');
    }

    public function edit(CategoryCourse $categoryCourse)
    {
        $categories = Category::where('status', 'active')->pluck('name', 'id');
        return view('admin.category_courses.edit', compact('categoryCourse', 'categories'));
    }

    public function update(Request $request, CategoryCourse $categoryCourse)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('thumbnail')) {
            if ($categoryCourse->thumbnail) {
                Storage::delete($categoryCourse->thumbnail);
            }
            $validated['thumbnail'] = $request->file('thumbnail')->store('public/category_course_thumbnails');
        }

        $categoryCourse->update($validated);
        return redirect()->route('admin.category-courses.index')->with('success', 'Course updated successfully.');
    }

    public function destroy(CategoryCourse $categoryCourse)
    {
        $categoryCourse->delete();
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Course moved to trash successfully.']);
        }
        return redirect()->route('admin.category-courses.index')->with('success', 'Course moved to trash successfully.');
    }
}
