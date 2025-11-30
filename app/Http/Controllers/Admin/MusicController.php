<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Music;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MusicController extends Controller
{
    use DataTableTrait;

    public function index()
    {
        $config = $this->getMusicDataTableConfig();
        return $this->renderDataTableView('admin.music.index', $config);
    }

    public function getData(Request $request)
    {
        $config = $this->getMusicDataTableConfig();
        return $this->handleDataTableRequest($request, Music::class, $config);
    }

    protected function getMusicDataTableConfig(): array
    {
        return [
            'table_id' => 'music-datatable',
            'title' => 'Manage Music',
            'add_new_url' => route('admin.music.create'),
            'ajax_url' => route('admin.music.data'),
            'columns' => [
                'id' => ['title' => 'ID'],
                'image' => [
                    'title' => 'Image', 'orderable' => false, 'searchable' => false,
                    'editColumn' => fn($row) => $row->image ? '<img src="' . Storage::url($row->image) . '" class="img-thumbnail" style="width: 80px;">' : 'No Image',
                ],
                'title' => ['title' => 'Title'],
                'subtitle' => ['title' => 'Subtitle'],
                'music_audio' => [
                    'title' => 'Audio', 'orderable' => false, 'searchable' => false,
                    'editColumn' => fn($row) => '<audio controls style="width: 250px;"><source src="' . Storage::url($row->music_audio) . '" type="audio/mpeg"></audio>',
                ],
                'total_play' => ['title' => 'Plays'],
            ],
            'actions' => [
                'edit' => ['url' => fn($row) => route('admin.music.edit', $row->id), 'class' => 'btn btn-sm btn-warning', 'text' => 'Edit'],
                'delete' => ['url' => fn($row) => route('admin.music.destroy', $row->id), 'class' => 'btn btn-sm btn-danger action-delete', 'text' => 'Delete'],
            ],
            'raw_columns' => ['action', 'image', 'music_audio'],
        ];
    }

    public function create()
    {
        return view('admin.music.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'duration' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'music_audio' => 'required|file|mimes:mp3,wav,ogg',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('public/music/images');
        }
        if ($request->hasFile('music_audio')) {
            $validated['music_audio'] = $request->file('music_audio')->store('public/music/audio');
        }

        Music::create($validated);
        return redirect()->route('admin.music.index')->with('success', 'Music created successfully.');
    }

    public function edit(Music $music)
    {
        return view('admin.music.edit', compact('music'));
    }

    public function update(Request $request, Music $music)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'duration' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'music_audio' => 'nullable|file|mimes:mp3,wav,ogg',
        ]);

        if ($request->hasFile('image')) {
            Storage::delete($music->image);
            $validated['image'] = $request->file('image')->store('public/music/images');
        }
        if ($request->hasFile('music_audio')) {
            Storage::delete($music->music_audio);
            $validated['music_audio'] = $request->file('music_audio')->store('public/music/audio');
        }

        $music->update($validated);
        return redirect()->route('admin.music.index')->with('success', 'Music updated successfully.');
    }

    public function destroy(Music $music)
    {
        $music->delete(); // Performs soft delete

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Music moved to trash successfully.']);
        }
        return redirect()->route('admin.music.index')->with('success', 'Music moved to trash successfully.');
    }
}
