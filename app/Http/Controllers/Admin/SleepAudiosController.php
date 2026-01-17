<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\SleepAudio;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SleepAudiosController extends Controller
{
    use DataTableTrait;

    public function index()
    {
        $config = $this->getSleepAudioDataTableConfig();
        return $this->renderDataTableView('admin.sleep_audios.index', $config);
    }

    public function getData(Request $request)
    {
        $config = $this->getSleepAudioDataTableConfig();
        return $this->handleDataTableRequest($request, SleepAudio::class, $config);
    }

    protected function getSleepAudioDataTableConfig(): array
    {
        return [
            'table_id' => 'sleep-audios-datatable',
            'title' => 'Sleep Audios',
            'add_new_url' => route('admin.sleep-audios.create'),
            'ajax_url' => route('admin.sleep-audios.data'),
            'relations' => ['course'],
            'columns' => [
                'id' => ['title' => 'ID'],
                'image' => [
                    'title' => 'Image', 'orderable' => false, 'searchable' => false,
                    'editColumn' => fn($row) => $row->image ? '<img src="' . Storage::url($row->image) . '" class="img-thumbnail" style="width: 80px;">' : 'No Image',
                ],
                'title' => ['title' => 'Title'],
                'course.title' => ['title' => 'Course', 'name' => 'course.title'],
                'audio' => [
                    'title' => 'Audio', 'orderable' => false, 'searchable' => false,
                    'editColumn' => fn($row) => $row->audio ? '<audio controls style="width: 200px;"><source src="' . Storage::url($row->audio) . '" type="audio/mpeg"></audio>' : 'No Audio',
                ],
                'total_play' => ['title' => 'Plays'],
            ],
            'actions' => [
                'edit' => ['url' => fn($row) => route('admin.sleep-audios.edit', $row->id), 'class' => 'btn btn-sm btn-warning', 'text' => 'Edit'],
                'delete' => ['url' => fn($row) => route('admin.sleep-audios.destroy', $row->id), 'class' => 'btn btn-sm btn-danger action-delete', 'text' => 'Delete'],
            ],
            'raw_columns' => ['action', 'image', 'audio'],
        ];
    }

    public function create()
    {
        $courses = Course::pluck('title', 'id');
        return view('admin.sleep_audios.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'duration' => 'required|string|max:100',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'audio' => 'required|file|mimes:mp3,wav,ogg',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('audio')) {
            $validated['audio'] = $request->file('audio')->store('public/sleep_audios/audio');
        }
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('public/sleep_audios/images');
        }

        SleepAudio::create($validated);
        return redirect()->route('admin.sleep-audios.index')->with('success', 'Sleep Audio created successfully.');
    }

    public function edit(SleepAudio $sleepAudio)
    {
        $courses = Course::pluck('title', 'id');
        return view('admin.sleep_audios.edit', compact('sleepAudio', 'courses'));
    }

    public function update(Request $request, SleepAudio $sleepAudio)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'duration' => 'required|string|max:100',
            'description' => 'nullable|string',
            'color' => 'nullable|string|max:7',
            'audio' => 'nullable|file|mimes:mp3,wav,ogg',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('audio')) {
            Storage::delete($sleepAudio->audio);
            $validated['audio'] = $request->file('audio')->store('public/sleep_audios/audio');
        }
        if ($request->hasFile('image')) {
            Storage::delete($sleepAudio->image);
            $validated['image'] = $request->file('image')->store('public/sleep_audios/images');
        }

        $sleepAudio->update($validated);
        return redirect()->route('admin.sleep-audios.index')->with('success', 'Sleep Audio updated successfully.');
    }

    public function destroy(SleepAudio $sleepAudio)
    {
        $sleepAudio->delete(); // Performs soft delete

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Sleep Audio moved to trash successfully.']);
        }
        return redirect()->route('admin.sleep-audios.index')->with('success', 'Sleep Audio moved to trash successfully.');
    }
}
