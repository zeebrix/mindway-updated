<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    use DataTableTrait;

    public function index()
    {
        $config = $this->getCategoryDataTableConfig();
        return $this->renderDataTableView('admin.categories.index', $config);
    }

    public function getData(Request $request)
    {
        $config = $this->getCategoryDataTableConfig();
        return $this->handleDataTableRequest($request, Category::class, $config);
    }

    protected function getCategoryDataTableConfig(): array
    {
        return [
            'table_id' => 'categories-datatable',
            'title' => 'Categories',
            'add_new_url' => route('admin.categories.create'),
            'ajax_url' => route('admin.categories.data'),
            'columns' => [
                'id' => ['title' => 'ID'],
                'name' => ['title' => 'Name'],
                'status' => [
                    'title' => 'Status',
                    'editColumn' => function ($row) {
                        $class = $row->status === 'active' ? 'bg-success' : 'bg-secondary';
                        return '<span class="badge ' . $class . '">' . ucfirst($row->status) . '</span>';
                    },
                ],
                'created_at' => ['title' => 'Created At', 'type' => 'date'],
            ],
            'actions' => [
                'edit' => ['url' => fn($row) => route('admin.categories.edit', $row->id), 'class' => 'btn btn-sm btn-warning', 'text' => 'Edit'],
                'delete' => ['url' => fn($row) => route('admin.categories.destroy', $row->id), 'class' => 'btn btn-sm btn-danger action-delete', 'text' => 'Delete'],
            ],
            'raw_columns' => ['action', 'status'],
        ];
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'status' => 'required|in:active,inactive',
        ]);

        Category::create($validated);
        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'status' => 'required|in:active,inactive',
        ]);

        $category->update($validated);
        return redirect()->route('admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Category moved to trash successfully.']);
        }
        return redirect()->route('admin.categories.index')->with('success', 'Category moved to trash successfully.');
    }
}
