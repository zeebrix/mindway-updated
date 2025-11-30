<?php

namespace App\Services;

use Yajra\DataTables\DataTables;
use Illuminate\Database\Eloquent\Builder;

class DataTableService
{
    /**
     * Build a DataTable response based on the provided configuration
     *
     * @param string $modelClass
     * @param array $config
     * @return \Illuminate\Http\JsonResponse
     */
    public function buildDataTable(string $modelClass, array $config = [])
    {
        // Initialize the query
        $query = $this->buildQuery($modelClass, $config);
        // Create DataTable instance
        $dataTable = DataTables::of($query);
        if (isset($config['columns'])) {
            foreach ($config['columns'] as $columnName => $columnConfig) {
                if (isset($columnConfig['editColumn']) && is_callable($columnConfig['editColumn'])) {
                    $dataTable->editColumn($columnName, $columnConfig['editColumn']);
                }
            }
        }
        if (isset($config['custom_columns'])) {
            foreach ($config['custom_columns'] as $column => $callback) {
                $dataTable->editColumn($column, $callback);
            }
        }

        // Add action column if actions are defined
        if (isset($config['actions'])) {
            $dataTable->addColumn('action', function ($row) use ($config) {
                return $this->buildActionButtons($row, $config['actions']);
            });
        }

        // Set raw columns for HTML content
        $rawColumns = ['action'];
        if (isset($config['raw_columns'])) {
            $rawColumns = array_merge($rawColumns, $config['raw_columns']);
        }
        $dataTable->rawColumns($rawColumns);

        // Apply filters if specified
        if (isset($config['filters'])) {
            $dataTable = $this->applyFilters($dataTable, $config['filters']);
        }

        return $dataTable->make(true);
    }

    /**
     * Build the initial query based on model and configuration
     *
     * @param string $modelClass
     * @param array $config
     * @return Builder
     */
    protected function buildQuery(string $modelClass, array $config): Builder
    {
        $query = $modelClass::query();

        // Apply relations
        if (isset($config['relations'])) {
            $query->with($config['relations']);
        }

        // Apply scope
        if (isset($config['scope'])) {
            $query->{$config['scope']}();
        }

        // Apply additional query constraints
        if (isset($config['where'])) {
            foreach ($config['where'] as $condition) {
                $query->where($condition[0], $condition[1], $condition[2] ?? null);
            }
        }
        if (isset($config['Relationwhere'])) {
            foreach ($config['Relationwhere'] as $condition) {

                $relation = $condition[0];
                $column = $condition[1];
                $operator = $condition[2] ?? '=';
                $value = $condition[3] ?? null;
                $query->whereRelation($relation, $column, $operator, $value);
            }
        }
        // Apply ordering
        if (isset($config['order_by'])) {
            $query->orderBy($config['order_by']['column'], $config['order_by']['direction'] ?? 'asc');
        }

        return $query;
    }

    /**
     * Build action buttons for each row
     *
     * @param mixed $row
     * @param array $actions
     * @return string
     */
    protected function buildActionButtons($row, array $actions): string
    {
        $buttons = [];
        $user = auth()->user();

        foreach ($actions as $actionName => $actionConfig) {
            // Check permission if specified
            if (isset($actionConfig['permission'])) {
                $hasPermission = is_callable($actionConfig['permission'])
                    ? $actionConfig['permission']($user)
                    : $user->can($actionConfig['permission']);

                if (!$hasPermission) {
                    if (isset($actionConfig['fallback'])) {
                        $buttons[] = $actionConfig['fallback'];
                    }
                    continue;
                }
            }

            // Build the button
            $url = is_callable($actionConfig['url'])
                ? $actionConfig['url']($row)
                : $actionConfig['url'];

            $class = $actionConfig['class'] ?? 'btn btn-sm btn-primary';
            $text = $actionConfig['text'] ?? ucfirst($actionName);
            $attributes = $actionConfig['attributes'] ?? '';

            $buttons[] = "<a href=\"{$url}\" class=\"{$class}\" {$attributes}>{$text}</a>";
        }

        return implode(' ', $buttons);
    }

    /**
     * Apply custom filters to the DataTable
     *
     * @param \Yajra\DataTables\DataTables $dataTable
     * @param array $filters
     * @return \Yajra\DataTables\DataTables
     */
    protected function applyFilters($dataTable, array $filters)
    {
        foreach ($filters as $column => $filter) {
            if (isset($filter['callback'])) {
                $dataTable->filterColumn($column, $filter['callback']);
            }
        }

        return $dataTable;
    }

    /**
     * Get column configuration for JavaScript
     *
     * @param array $config
     * @return array
     */
    public function getColumnConfig(array $config): array
    {
        $columns = [];

        if (isset($config['columns'])) {
            foreach ($config['columns'] as $name => $columnConfig) {
                $columns[] = [
                    'data' => $name,
                    'name' => $name,
                    'title' => $columnConfig['title'] ?? ucfirst(str_replace('_', ' ', $name)),
                    'searchable' => $columnConfig['searchable'] ?? true,
                    'orderable' => $columnConfig['orderable'] ?? true,
                    'type' => $columnConfig['type'] ?? 'string',
                ];
            }
        }

        // Add action column if actions are defined
        if (isset($config['actions'])) {
            $columns[] = [
                'data' => 'action',
                'name' => 'action',
                'title' => 'Actions',
                'searchable' => false,
                'orderable' => false,
            ];
        }

        return $columns;
    }

    /**
     * Get DataTable configuration for JavaScript
     *
     * @param array $config
     * @return array
     */
    public function getDataTableConfig(array $config): array
    {
        return [
            'processing' => true,
            'serverSide' => true,
            'ajax' => $config['ajax_url'] ?? '',
            'columns' => $this->getColumnConfig($config),
            'pageLength' => $config['page_length'] ?? 25,
            'lengthMenu' => $config['length_menu'] ?? [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            'order' => $config['default_order'] ?? [[0, 'asc']],
            'responsive' => $config['responsive'] ?? true,
            'language' => $config['language'] ?? [
                'search' => 'Search:',
                'lengthMenu' => 'Show _MENU_ entries per page',
                'info' => 'Showing _START_ to _END_ of _TOTAL_ entries',
                'infoEmpty' => 'No entries found',
                'infoFiltered' => '(filtered from _MAX_ total entries)',
                'paginate' => [
                    'first' => 'First',
                    'last' => 'Last',
                    'next' => 'Next',
                    'previous' => 'Previous'
                ],
                'emptyTable' => 'No data available',
                'zeroRecords' => 'No matching records found'
            ],
        ];
    }
}
