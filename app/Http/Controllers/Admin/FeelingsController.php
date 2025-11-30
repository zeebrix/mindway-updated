<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feeling;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;

class FeelingsController extends Controller
{
    use DataTableTrait;

    public function index()
    {
        $config = $this->getFeelingDataTableConfig();
        return $this->renderDataTableView('admin.feelings.index', $config);
    }

    public function getData(Request $request)
    {
        $config = $this->getFeelingDataTableConfig();
        return $this->handleDataTableRequest($request, Feeling::class, $config);
    }

    protected function getFeelingDataTableConfig(): array
    {
        return [
            'table_id' => 'feelings-datatable',
            'title' => 'Manage Feelings',
            'add_new_url' => route('admin.feelings.create'),
            'ajax_url' => route('admin.feelings.data'),
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
                'edit' => ['url' => fn($row) => route('admin.feelings.edit', $row->id), 'class' => 'btn btn-sm btn-warning', 'text' => 'Edit'],
                'delete' => ['url' => fn($row) => route('admin.feelings.destroy', $row->id), 'class' => 'btn btn-sm btn-danger action-delete', 'text' => 'Delete'],
            ],
            'raw_columns' => ['action', 'emoji'],
        ];
    }

    public function create()
    {
        return view('admin.feelings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'emoji' => 'required|string|max:10',
        ]);

        Feeling::create($validated);
        return redirect()->route('admin.feelings.index')->with('success', 'Feeling created successfully.');
    }

    public function edit(Feeling $feeling)
    {
        return view('admin.feelings.edit', compact('feeling'));
    }

    public function update(Request $request, Feeling $feeling)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'emoji' => 'required|string|max:10',
        ]);

        $feeling->update($validated);
        return redirect()->route('admin.feelings.index')->with('success', 'Feeling updated successfully.');
    }

    public function destroy(Feeling $feeling)
    {
        $feeling->delete(); // Performs soft delete

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Feeling moved to trash successfully.']);
        }
        return redirect()->route('admin.feelings.index')->with('success', 'Feeling moved to trash successfully.');
    }
}
