<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SleepScreen;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SleepScreensController extends Controller
{
    use DataTableTrait;

    public function index()
    {
        $config = $this->getSleepScreenDataTableConfig();
        return $this->renderDataTableView('admin.sleep_screens.index', $config);
    }

    public function getData(Request $request)
    {
        $config = $this->getSleepScreenDataTableConfig();
        return $this->handleDataTableRequest($request, SleepScreen::class, $config);
    }

    protected function getSleepScreenDataTableConfig(): array
    {
        return [
            'table_id' => 'sleep-screens-datatable',
            'title' => 'Manage Sleep Screen Audio',
            'add_new_url' => route('admin.sleep-screens.create'),
            'ajax_url' => route('admin.sleep-screens.data'),
            'columns' => [
                'id' => ['title' => 'ID'],
                'image' => [
                    'title' => 'Image', 'orderable' => false, 'searchable' => false,
                    'editColumn' => fn($row) => $row->image ? '<img src="' . Storage::url($row->image) . '" class="img-thumbnail" style="width: 80px;">' : 'No Image',
                ],
                'audio_title' => ['title' => 'Title'],
                'sleep_audio' => [
                    'title' => 'Audio', 'orderable' => false, 'searchable' => false,
                    'editColumn' => fn($row) => '<audio controls style="width: 250px;"><source src="' . Storage::url($row->sleep_audio) . '" type="audio/mpeg"></audio>',
                ],
                'total_play' => ['title' => 'Plays'],
            ],
            'actions' => [
                'edit' => ['url' => fn($row) => route('admin.sleep-screens.edit', $row->id), 'class' => 'btn btn-sm btn-warning', 'text' => 'Edit'],
                'delete' => ['url' => fn($row) => route('admin.sleep-screens.destroy', $row->id), 'class' => 'btn btn-sm btn-danger action-delete', 'text' => 'Delete'],
            ],
            'raw_columns' => ['action', 'image', 'sleep_audio'],
        ];
    }

    public function create()
    {
        return view('admin.sleep_screens.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'audio_title' => 'required|string|max:255',
            'duration' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sleep_audio' => 'required|file|mimes:mp3,wav,ogg',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('public/sleep_screens/images');
        }
        if ($request->hasFile('sleep_audio')) {
            $validated['sleep_audio'] = $request->file('sleep_audio')->store('public/sleep_screens/audio');
        }

        SleepScreen::create($validated);
        return redirect()->route('admin.sleep-screens.index')->with('success', 'Sleep Screen created successfully.');
    }

    public function edit(SleepScreen $sleepScreen)
    {
        return view('admin.sleep_screens.edit', compact('sleepScreen'));
    }

    public function update(Request $request, SleepScreen $sleepScreen)
    {
        $validated = $request->validate([
            'audio_title' => 'required|string|max:255',
            'duration' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'sleep_audio' => 'nullable|file|mimes:mp3,wav,ogg',
        ]);

        if ($request->hasFile('image')) {
            Storage::delete($sleepScreen->image);
            $validated['image'] = $request->file('image')->store('public/sleep_screens/images');
        }
        if ($request->hasFile('sleep_audio')) {
            Storage::delete($sleepScreen->sleep_audio);
            $validated['sleep_audio'] = $request->file('sleep_audio')->store('public/sleep_screens/audio');
        }

        $sleepScreen->update($validated);
        return redirect()->route('admin.sleep-screens.index')->with('success', 'Sleep Screen updated successfully.');
    }

    public function destroy(SleepScreen $sleepScreen)
    {
        $sleepScreen->delete(); // Performs soft delete

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Sleep Screen moved to trash successfully.']);
        }
        return redirect()->route('admin.sleep-screens.index')->with('success', 'Sleep Screen moved to trash successfully.');
    }
}
