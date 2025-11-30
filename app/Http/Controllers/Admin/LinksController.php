<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Traits\DataTableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LinksController extends Controller
{
    use DataTableTrait;

    public function index()
    {
        $config = $this->getLinkDataTableConfig();
        return $this->renderDataTableView('admin.links.index', $config);
    }

    public function getData(Request $request)
    {
        $config = $this->getLinkDataTableConfig();
        return $this->handleDataTableRequest($request, Link::class, $config);
    }

    protected function getLinkDataTableConfig(): array
    {
        return [
            'table_id' => 'links-datatable',
            'title' => 'Manage Links',
            'add_new_url' => route('admin.links.create'),
            'ajax_url' => route('admin.links.data'),
            'columns' => [
                'id' => ['title' => 'ID'],
                'icon' => [
                    'title' => 'Icon',
                    'orderable' => false,
                    'searchable' => false,
                    'editColumn' => function ($row) {
                        if (!$row->icon) return 'No Icon';
                        // Check if it's a path or a class
                        if (str_starts_with($row->icon, 'public/')) {
                            return '<img src="' . Storage::url($row->icon) . '" class="img-thumbnail" style="width: 50px;">';
                        }
                        // Assumes it's a Font Awesome class
                        return '<i class="' . e($row->icon) . ' fa-2x"></i>';
                    },
                ],
                'title' => ['title' => 'Title'],
                'sub_title' => ['title' => 'Sub-Title'],
                'url_name' => ['title' => 'URL'],
            ],
            'actions' => [
                'edit' => ['url' => fn($row) => route('admin.links.edit', $row->id), 'class' => 'btn btn-sm btn-warning', 'text' => 'Edit'],
                'delete' => ['url' => fn($row) => route('admin.links.destroy', $row->id), 'class' => 'btn btn-sm btn-danger action-delete', 'text' => 'Delete'],
            ],
            'raw_columns' => ['action', 'icon'],
        ];
    }

    public function create()
    {
        return view('admin.links.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'url_name' => 'required|string|max:255',
            'icon_type' => 'required|in:class,upload',
            'icon_class' => 'nullable|required_if:icon_type,class|string|max:255',
            'icon_upload' => 'nullable|required_if:icon_type,upload|image|max:2048',
        ]);

        $iconValue = $validated['icon_type'] === 'class' ? $validated['icon_class'] : null;
        if ($validated['icon_type'] === 'upload' && $request->hasFile('icon_upload')) {
            $iconValue = $request->file('icon_upload')->store('public/link_icons');
        }

        Link::create([
            'title' => $validated['title'],
            'sub_title' => $validated['sub_title'],
            'url_name' => $validated['url_name'],
            'icon' => $iconValue,
        ]);

        return redirect()->route('admin.links.index')->with('success', 'Link created successfully.');
    }

    public function edit(Link $link)
    {
        return view('admin.links.edit', compact('link'));
    }

    public function update(Request $request, Link $link)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sub_title' => 'nullable|string|max:255',
            'url_name' => 'required|string|max:255',
            'icon_type' => 'required|in:class,upload',
            'icon_class' => 'nullable|required_if:icon_type,class|string|max:255',
            'icon_upload' => 'nullable|image|max:2048',
        ]);

        $iconValue = $link->icon;
        if ($validated['icon_type'] === 'class') {
            $iconValue = $validated['icon_class'];
        } elseif ($validated['icon_type'] === 'upload' && $request->hasFile('icon_upload')) {
            Storage::delete($link->icon); // Delete old icon if it was an upload
            $iconValue = $request->file('icon_upload')->store('public/link_icons');
        }

        $link->update([
            'title' => $validated['title'],
            'sub_title' => $validated['sub_title'],
            'url_name' => $validated['url_name'],
            'icon' => $iconValue,
        ]);

        return redirect()->route('admin.links.index')->with('success', 'Link updated successfully.');
    }

    public function destroy(Link $link)
    {
        $link->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Link moved to trash successfully.']);
        }
        return redirect()->route('admin.links.index')->with('success', 'Link moved to trash successfully.');
    }
}
