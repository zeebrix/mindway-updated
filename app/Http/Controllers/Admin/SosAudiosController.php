<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\SosAudio;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SosAudiosController extends Controller
{
    use DataTableTrait;

    public function index()
    {
        $config = $this->getSosAudioDataTableConfig();
        return $this->renderDataTableView('admin.sos_audios.index', $config);
    }

    public function getData(Request $request)
    {
        $config = $this->getSosAudioDataTableConfig();
        return $this->handleDataTableRequest($request, SosAudio::class, $config);
    }

    protected function getSosAudioDataTableConfig(): array
    {
        return [
            'table_id' => 'sos-audios-datatable',
            'title' => 'SOS Audios',
            'add_new_url' => route('admin.sos-audios.create'),
            'ajax_url' => route('admin.sos-audios.data'),
            'relations' => ['course'], // Eager load the relationship
            'columns' => [
                'id' => ['title' => 'ID'],
                'course.course_title' => ['title' => 'Course', 'name' => 'course.course_title'],
                'audio_title' => ['title' => 'Audio Title'],
                'duration' => ['title' => 'Duration'],
                'total_play' => ['title' => 'Play Count'],
                'sos_audio' => [
                    'title' => 'Audio',
                    'orderable' => false,
                    'searchable' => false,
                    'editColumn' => function ($row) {
                        if ($row->sos_audio) {
                            return '<audio controls style="width: 250px;"><source src="' . Storage::url($row->sos_audio) . '" type="audio/mpeg"></audio>';
                        }
                        return 'No Audio';
                    },
                ],
            ],
            'actions' => [
                'edit' => ['url' => fn($row) => route('admin.sos-audios.edit', $row->id), 'class' => 'btn btn-sm btn-warning', 'text' => 'Edit'],
                'delete' => ['url' => fn($row) => route('admin.sos-audios.destroy', $row->id), 'class' => 'btn btn-sm btn-danger action-delete', 'text' => 'Delete'],
            ],
            'raw_columns' => ['action', 'sos_audio'],
        ];
    }

    public function create()
    {
        $courses = Course::pluck('course_title', 'id');
        return view('admin.sos_audios.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'audio_title' => 'required|string|max:255',
            'duration' => 'required|string|max:100',
            'sos_audio' => 'required|file|mimes:mp3,wav,ogg',
        ]);

        $path = $request->file('sos_audio')->store('public/sos_audios');

        SosAudio::create([
            'course_id' => $validated['course_id'],
            'audio_title' => $validated['audio_title'],
            'duration' => $validated['duration'],
            'sos_audio' => $path,
        ]);

        return redirect()->route('admin.sos-audios.index')->with('success', 'SOS Audio added successfully.');
    }

    public function edit(SosAudio $sosAudio)
    {
        $courses = Course::pluck('course_title', 'id');
        return view('admin.sos_audios.edit', compact('sosAudio', 'courses'));
    }

    public function update(Request $request, SosAudio $sosAudio)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'audio_title' => 'required|string|max:255',
            'duration' => 'required|string|max:100',
            'sos_audio' => 'nullable|file|mimes:mp3,wav,ogg',
        ]);

        $data = $validated;
        if ($request->hasFile('sos_audio')) {
            if ($sosAudio->sos_audio && Storage::exists($sosAudio->sos_audio)) {
            Storage::delete($sosAudio->sos_audio);
        }
            $data['sos_audio'] = $request->file('sos_audio')->store('public/sos_audios');
        }

        $sosAudio->update($data);
        return redirect()->route('admin.sos-audios.index')->with('success', 'SOS Audio updated successfully.');
    }

    public function destroy(SosAudio $sosAudio)
    {
        if ($sosAudio->sos_audio && Storage::exists($sosAudio->sos_audio)) {
            Storage::delete($sosAudio->sos_audio);
        }
        $sosAudio->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'SOS Audio deleted successfully.']);
        }
        return redirect()->route('admin.sos-audios.index')->with('success', 'SOS Audio deleted successfully.');
    }
}
