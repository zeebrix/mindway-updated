<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feeling;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;

class HomeEmojisController extends Controller
{
    use DataTableTrait;

    public function index()
    {
        $config = $this->getHomeEmojiDataTableConfig();
        return $this->renderDataTableView('admin.home_emojis.index', $config);
    }

    public function getData(Request $request)
    {
        $config = $this->getHomeEmojiDataTableConfig();
        return $this->handleDataTableRequest($request, Feeling::class, $config);
    }

    protected function getHomeEmojiDataTableConfig(): array
    {
        return [
            'table_id' => 'home-emojis-datatable',
            'title' => 'Manage Home Emojis',
            'add_new_url' => route('admin.home-emojis.create'),
            'ajax_url' => route('admin.home-emojis.data'),
            'columns' => [
                'id' => ['title' => 'ID'],
                'emoji' => [
                    'title' => 'Emoji',
                    'editColumn' => fn($row) => '<span style="font-size: 1.5rem;">' . e($row->emoji) . '</span>',
                ],
                'name' => ['title' => 'Name'],
                'created_at' => ['title' => 'Created At', 'type' => 'date'],
            ],
            'actions' => [
                'edit' => ['url' => fn($row) => route('admin.home-emojis.edit', $row->id), 'class' => 'btn btn-sm btn-warning', 'text' => 'Edit'],
                'delete' => ['url' => fn($row) => route('admin.home-emojis.destroy', $row->id), 'class' => 'btn btn-sm btn-danger action-delete', 'text' => 'Delete'],
            ],
            'raw_columns' => ['action', 'emoji'],
        ];
    }

    public function create()
    {
        return view('admin.home_emojis.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'emoji' => 'required|string|max:10',
        ]);

        // Automatically set the type to 'home'
        $validated['type'] = 'home';

        Feeling::create($validated);
        return redirect()->route('admin.home-emojis.index')->with('success', 'Home Emoji created successfully.');
    }

    public function edit(Feeling $homeEmoji) // Laravel will bind the Feeling model
    {
        return view('admin.home_emojis.edit', compact('homeEmoji'));
    }

    public function update(Request $request, Feeling $homeEmoji)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'emoji' => 'required|string|max:10',
        ]);

        $homeEmoji->update($validated);
        return redirect()->route('admin.home-emojis.index')->with('success', 'Home Emoji updated successfully.');
    }

    public function destroy(Feeling $homeEmoji)
    {
        if ($homeEmoji->type !== 'home') {
            abort(403, 'Unauthorized action.');
        }

        $homeEmoji->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Home Emoji moved to trash successfully.']);
        }
        return redirect()->route('admin.home-emojis.index')->with('success', 'Home Emoji moved to trash successfully.');
    }
}
