<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;

class QuotesController extends Controller
{
    use DataTableTrait;

    public function index()
    {
        $config = $this->getQuoteDataTableConfig();
        return $this->renderDataTableView('admin.quotes.index', $config);
    }

    public function getData(Request $request)
    {
        $config = $this->getQuoteDataTableConfig();
        return $this->handleDataTableRequest($request, Quote::class, $config);
    }

    protected function getQuoteDataTableConfig(): array
    {
        return [
            'table_id' => 'quotes-datatable',
            'title' => 'Manage Quotes',
            'add_new_url' => route('admin.quotes.create'),
            'ajax_url' => route('admin.quotes.data'),
            'columns' => [
                'id' => ['title' => 'ID'],
                'name' => [
                    'title' => 'Quote Text',
                    // Truncate long quotes for better display in the table
                    'editColumn' => fn($row) => \Illuminate\Support\Str::limit($row->name, 100),
                ],
                'created_at' => ['title' => 'Created At', 'type' => 'date'],
            ],
            'actions' => [
                'edit' => ['url' => fn($row) => route('admin.quotes.edit', $row->id), 'class' => 'btn btn-sm btn-warning', 'text' => 'Edit'],
                'delete' => ['url' => fn($row) => route('admin.quotes.destroy', $row->id), 'class' => 'btn btn-sm btn-danger action-delete', 'text' => 'Delete'],
            ],
            'raw_columns' => ['action'],
        ];
    }

    public function create()
    {
        return view('admin.quotes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
        ]);

        Quote::create($validated);
        return redirect()->route('admin.quotes.index')->with('success', 'Quote created successfully.');
    }

    public function edit(Quote $quote)
    {
        return view('admin.quotes.edit', compact('quote'));
    }

    public function update(Request $request, Quote $quote)
    {
        $validated = $request->validate([
            'name' => 'required|string',
        ]);

        $quote->update($validated);
        return redirect()->route('admin.quotes.index')->with('success', 'Quote updated successfully.');
    }

    public function destroy(Quote $quote)
    {
        $quote->delete(); // Performs soft delete

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Quote moved to trash successfully.']);
        }
        return redirect()->route('admin.quotes.index')->with('success', 'Quote moved to trash successfully.');
    }
}
