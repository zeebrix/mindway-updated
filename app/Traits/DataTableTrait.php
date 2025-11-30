<?php

namespace App\Traits;

use Illuminate\Http\Request;
use App\Services\DataTableService;

trait DataTableTrait
{
    protected $dataTableService;

    /**
     * Initialize the DataTable service
     */
    protected function initializeDataTableService()
    {
        if (!$this->dataTableService) {
            $this->dataTableService = app(DataTableService::class);
        }
    }

    /**
     * Handle DataTable AJAX requests
     *
     * @param Request $request
     * @param string $modelClass
     * @param array $config
     * @return \Illuminate\Http\JsonResponse
     */
    protected function handleDataTableRequest(Request $request, string $modelClass, array $config = [])
    {
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $this->initializeDataTableService();
        return $this->dataTableService->buildDataTable($modelClass, $config);
    }

    /**
     * Get DataTable configuration for the view
     *
     * @param array $config
     * @return array
     */
    protected function getDataTableViewConfig(array $config): array
    {
        $this->initializeDataTableService();
        return $this->dataTableService->getDataTableConfig($config);
    }

    /**
     * Render a DataTable view with configuration
     *
     * @param string $view
     * @param array $config
     * @param array $additionalData
     * @return \Illuminate\View\View
     */
    protected function renderDataTableView(string $view, array $config, array $additionalData = [])
    {
        $dataTableConfig = $this->getDataTableViewConfig($config);
        
        $viewData = array_merge([
            'dataTableConfig' => $dataTableConfig,
            'tableId' => $config['table_id'] ?? 'data-table',
            'title' => $config['title'] ?? 'Data Table',
            'addNewUrl' => $config['add_new_url'] ?? null,
            'addNewText' => $config['add_new_text'] ?? 'Add New',
            'addNewBtnId' => $config['add_new_btn_id'] ?? 'add-counselor',
        ], $additionalData);

        return view($view, $viewData);
    }
}