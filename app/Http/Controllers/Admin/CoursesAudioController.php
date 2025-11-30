<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseAudio;
use App\Traits\DataTableTrait;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CoursesAudioController extends Controller
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
        return $this->renderDataTableView('admin.courses-audio.index', $config, [
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'url' => route('admin.dashboard')],
                ['name' => 'Courses Audio', 'url' => null],
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
        return $this->handleDataTableRequest($request, CourseAudio::class, $config);
    }

    /**
     * Get the configuration for the users DataTable
     *
     * @return array
     */
    protected function getUserDataTableConfig(): array
    {
        return [
            'table_id' => 'courses-audio-datatable',
            'title' => 'Courses Audio',
            'add_new_url' => route('admin.courses-audio.create'),
            'add_new_text' => 'Add New Courses Audio',
            'ajax_url' => route('admin.courses-audio.data'),
            'page_length' => 25,
            'default_order' => [[0, 'asc']],
            'relations' => ['course'],
            'columns' => [
                'id' => ['title' => 'ID'],
                'course.course_title' => ['title' => 'Course', 'name' => 'course.course_title'],
                'audio_title' => ['title' => 'Audio Title'],
                'duration' => ['title' => 'Duration'],
                'audio' => [
                    'title' => 'Audio',
                    'orderable' => false,
                    'searchable' => false,
                    'editColumn' => function ($row) {
                        if ($row->audio) {
                            return '<audio controls><source src="' . Storage::url($row->audio) . '" type="audio/mpeg">Your browser does not support the audio element.</audio>';
                        }
                        return 'No Audio';
                    },
                ],
                'created_at' => ['title' => 'Created At', 'type' => 'date'],
            ],
            'actions' => [
                'edit' => ['url' => fn($row) => route('admin.courses-audio.edit', $row->id), 'class' => 'btn btn-sm btn-warning', 'text' => 'Edit'],
                'delete' => ['url' => fn($row) => route('admin.courses-audio.destroy', $row->id), 'class' => 'btn btn-sm btn-danger action-delete', 'text' => 'Delete'],
            ],

            'raw_columns' => ['audio'],

            'language' => [
                'search' => 'Search courses audio:',
                'lengthMenu' => 'Show _MENU_ courses per page',
                'info' => 'Showing _START_ to _END_ of _TOTAL_ courses audio',
                'infoEmpty' => 'No courses audio found',
                'infoFiltered' => '(filtered from _MAX_ total courses audio)',
                'emptyTable' => 'No user data available',
                'zeroRecords' => 'No matching courses audio found'
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
        $courses = Course::pluck('course_title', 'id');
        return view('admin.courses-audio.create', compact('courses'));
    }

    /**
     * Store a newly created user
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'audio_title' => 'required|string|max:255',
            'duration' => 'required|string|max:100',
            'audio' => 'required|file|mimes:mp3,wav,ogg',
        ]);

        $path = $request->file('audio')->store('courses_audio');

        CourseAudio::create([
            'course_id' => $validated['course_id'],
            'audio_title' => $validated['audio_title'],
            'duration' => $validated['duration'],
            'audio' => $path,
        ]);

        return redirect()->route('admin.courses-audio.index')->with('success', 'Audio added successfully.');
    }

    /**
     * Display the specified user
     *
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function show(CourseAudio $courseAudio)
    {
        return view('admin.courses-audio.show', compact('courseAudio'));
    }

    /**
     * Show the form for editing the specified user
     *
     * @param User $user
     * @return \Illuminate\View\View
     */
    public function edit(CourseAudio $courseAudio)
    {
        $courses = Course::pluck('course_title', 'id');
        return view('admin.courses-audio.edit', compact('courseAudio', 'courses'));
    }

    /**
     * Update the specified user
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, CourseAudio $courseAudio)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'audio_title' => 'required|string|max:255',
            'duration' => 'required|string|max:100',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg',
        ]);

        $data = $validated;
        if ($request->hasFile('audio')) {
            if ($courseAudio->audio && Storage::exists($courseAudio->audio)) {
                Storage::delete($courseAudio->audio);
            }
            $data['audio'] = $request->file('audio')->store('course_audios');
        }

        $courseAudio->update($data);
        return redirect()->route('admin.courses-audio.index')->with('success', 'Audio updated successfully.');
    }

    /**
     * Remove the specified user
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CourseAudio $courseAudio)
    {
        if ($courseAudio->audio && Storage::exists($courseAudio->audio)) {
            Storage::delete($courseAudio->audio);
        }
        $courseAudio->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Audio deleted successfully.']);
        }
        return redirect()->route('admin.courses-audio.index')->with('success', 'Audio deleted successfully.');
    }
}
