<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SingleCourse;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SingleCoursesController extends Controller
{
    use DataTableTrait;

    public function index()
    {
        $config = $this->getSingleCourseDataTableConfig();
        return $this->renderDataTableView('admin.single_courses.index', $config);
    }

    public function getData(Request $request)
    {
        $config = $this->getSingleCourseDataTableConfig();
        return $this->handleDataTableRequest($request, SingleCourse::class, $config);
    }

    protected function getSingleCourseDataTableConfig(): array
    {
        return [
            'table_id' => 'single-courses-datatable',
            'title' => 'Manage Single Courses',
            'add_new_url' => route('admin.single-courses.create'),
            'ajax_url' => route('admin.single-courses.data'),
            'columns' => [
                'id' => ['title' => 'ID'],
                'image' => [
                    'title' => 'Image', 'orderable' => false, 'searchable' => false,
                    'editColumn' => fn($row) => $row->image ? '<img src="' . Storage::url($row->image) . '" class="img-thumbnail" style="width: 80px;">' : 'No Image',
                ],
                'title' => ['title' => 'Title'],
                'single_audio' => [
                    'title' => 'Audio', 'orderable' => false, 'searchable' => false,
                    'editColumn' => fn($row) => '<audio controls style="width: 250px;"><source src="' . Storage::url($row->single_audio) . '" type="audio/mpeg"></audio>',
                ],
                'total_play' => ['title' => 'Plays'],
            ],
            'actions' => [
                'edit' => ['url' => fn($row) => route('admin.single-courses.edit', $row->id), 'class' => 'btn btn-sm btn-warning', 'text' => 'Edit'],
                'delete' => ['url' => fn($row) => route('admin.single-courses.destroy', $row->id), 'class' => 'btn btn-sm btn-danger action-delete', 'text' => 'Delete'],
            ],
            'raw_columns' => ['action', 'image', 'single_audio'],
        ];
    }

    public function create()
    {
        return view('admin.single_courses.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'duration' => 'required|string|max:100',
            'color' => 'nullable|string|max:7',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'single_audio' => 'required|file|mimes:mp3,wav,ogg',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('public/single_courses/images');
        }
        if ($request->hasFile('single_audio')) {
            $validated['single_audio'] = $request->file('single_audio')->store('public/single_courses/audio');
        }

        SingleCourse::create($validated);
        return redirect()->route('admin.single-courses.index')->with('success', 'Single Course created successfully.');
    }

    public function edit(SingleCourse $singleCourse)
    {
        return view('admin.single_courses.edit', compact('singleCourse'));
    }

    public function update(Request $request, SingleCourse $singleCourse)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'duration' => 'required|string|max:100',
            'color' => 'nullable|string|max:7',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'single_audio' => 'nullable|file|mimes:mp3,wav,ogg',
        ]);

        if ($request->hasFile('image')) {
            Storage::delete($singleCourse->image);
            $validated['image'] = $request->file('image')->store('public/single_courses/images');
        }
        if ($request->hasFile('single_audio')) {
            Storage::delete($singleCourse->single_audio);
            $validated['single_audio'] = $request->file('single_audio')->store('public/single_courses/audio');
        }

        $singleCourse->update($validated);
        return redirect()->route('admin.single-courses.index')->with('success', 'Single Course updated successfully.');
    }

    public function destroy(SingleCourse $singleCourse)
    {
        $singleCourse->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Single Course moved to trash successfully.']);
        }
        return redirect()->route('admin.single-courses.index')->with('success', 'Single Course moved to trash successfully.');
    }
}
