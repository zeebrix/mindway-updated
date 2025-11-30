/**
 * Dynamic DataTable Initialization Script
 * 
 * This script automatically initializes DataTables based on data attributes
 * and provides a reusable solution for all DataTable implementations.
 * 
 * Usage:
 * 1. Include this script after DataTables libraries
 * 2. Add data-config attribute to your table with JSON configuration
 * 3. The script will automatically initialize the DataTable
 */

(function($) {
    'use strict';

    // Global DataTable instances storage
    window.DataTableInstances = window.DataTableInstances || {};

    /**
     * Default DataTable configuration
     */
    const defaultConfig = {
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        order: [[0, 'asc']],
        language: {
            processing: '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading Frhan is here...</span></div></div>',
            search: 'Search:',
            lengthMenu: 'Show _MENU_ entries per page',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'No entries found',
            infoFiltered: '(filtered from _MAX_ total entries)',
            paginate: {
                first: 'First',
                last: 'Last',
                next: 'Next',
                previous: 'Previous'
            },
            emptyTable: 'No data available',
            zeroRecords: 'No matching records found'
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        drawCallback: function(settings) {
            // Add Bootstrap classes to pagination
            $(this).closest('.dataTables_wrapper').find('.paginate_button').addClass('page-link');
            $(this).closest('.dataTables_wrapper').find('.paginate_button.current').addClass('active');
        }
    };

    /**
     * Initialize a single DataTable
     * @param {jQuery} $table - The table element
     * @param {Object} config - DataTable configuration
     */
    function initializeDataTable($table, config) {
        const tableId = $table.attr('id');
        
        try {
            // Merge default config with provided config
            const finalConfig = $.extend(true, {}, defaultConfig, config);
            
            // Add export buttons if configured
            if (config.exportButtons && config.exportButtons.length > 0) {
                finalConfig.dom = 'B' + finalConfig.dom;
                finalConfig.buttons = buildExportButtons(config.exportButtons);
            }
            
            // Initialize DataTable
            const dataTable = $table.DataTable(finalConfig);
            
            // Store instance globally
            window.DataTableInstances[tableId] = dataTable;
            
            // Setup custom filters
            setupCustomFilters(tableId, dataTable);
            
            // Setup export functionality
            setupExportFunctionality(tableId, dataTable);
            
            // Setup loading overlay
            setupLoadingOverlay(tableId, dataTable);
            
            console.log(`DataTable initialized successfully: ${tableId}`);
            
        } catch (error) {
            console.error(`Error initializing DataTable ${tableId}:`, error);
        }
    }

    /**
     * Build export buttons configuration
     * @param {Array} exportButtons - Export button configurations
     * @returns {Array} DataTables buttons configuration
     */
    function buildExportButtons(exportButtons) {
        const buttons = [];
        
        exportButtons.forEach(button => {
            let buttonConfig = {
                extend: button.type,
                text: button.text || button.type.toUpperCase(),
                className: button.class || 'btn btn-secondary btn-sm'
            };
            
            // Add specific configurations for different export types
            switch (button.type) {
                case 'csv':
                    buttonConfig.filename = button.filename || 'export';
                    break;
                case 'excel':
                    buttonConfig.extend = 'excelHtml5';
                    buttonConfig.filename = button.filename || 'export';
                    break;
                case 'pdf':
                    buttonConfig.extend = 'pdfHtml5';
                    buttonConfig.filename = button.filename || 'export';
                    buttonConfig.orientation = button.orientation || 'landscape';
                    break;
                case 'print':
                    buttonConfig.extend = 'print';
                    break;
            }
            
            buttons.push(buttonConfig);
        });
        
        return buttons;
    }

    /**
     * Setup custom filters for a DataTable
     * @param {string} tableId - Table ID
     * @param {Object} dataTable - DataTable instance
     */
    function setupCustomFilters(tableId, dataTable) {
        // Apply filters button
        $(`#apply-filters`).on('click', function() {
            applyFilters(dataTable);
        });
        
        // Clear filters button
        $(`#clear-filters`).on('click', function() {
            clearFilters(dataTable);
        });
        
        // Auto-apply filters on change (optional)
        $('[data-filter]').on('change keyup', function() {
            if ($(this).data('auto-filter') !== false) {
                applyFilters(dataTable);
            }
        });
    }

    /**
     * Apply filters to DataTable
     * @param {Object} dataTable - DataTable instance
     */
    function applyFilters(dataTable) {
        $('[data-filter]').each(function() {
            const $filter = $(this);
            const column = $filter.data('filter');
            const value = $filter.val();
            
            if (column === 'global') {
                dataTable.search(value);
            } else if (column.endsWith('_from') || column.endsWith('_to')) {
                // Handle date range filters
                handleDateRangeFilter(dataTable, column, value);
            } else {
                // Find column index by name
                const columnIndex = dataTable.columns().header().toArray()
                    .findIndex(header => $(header).data('column') === column);
                
                if (columnIndex !== -1) {
                    dataTable.column(columnIndex).search(value);
                }
            }
        });
        
        dataTable.draw();
    }

    /**
     * Clear all filters
     * @param {Object} dataTable - DataTable instance
     */
    function clearFilters(dataTable) {
        $('[data-filter]').val('');
        dataTable.search('').columns().search('').draw();
    }

    /**
     * Handle date range filtering
     * @param {Object} dataTable - DataTable instance
     * @param {string} column - Column name
     * @param {string} value - Filter value
     */
    function handleDateRangeFilter(dataTable, column, value) {
        const baseColumn = column.replace('_from', '').replace('_to', '');
        const fromValue = $(`[data-filter="${baseColumn}_from"]`).val();
        const toValue = $(`[data-filter="${baseColumn}_to"]`).val();
        
        // Custom date range filtering logic would go here
        // This is a simplified example
        if (fromValue || toValue) {
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                // Implement date range filtering logic
                return true; // Placeholder
            });
        }
    }

    /**
     * Setup export functionality
     * @param {string} tableId - Table ID
     * @param {Object} dataTable - DataTable instance
     */
    function setupExportFunctionality(tableId, dataTable) {
        // Custom export buttons (outside of DataTables buttons)
        $(`[data-export][data-table="${tableId}"]`).on('click', function() {
            const exportType = $(this).data('export');
            
            switch (exportType) {
                case 'csv':
                    exportToCSV(dataTable, tableId);
                    break;
                case 'excel':
                    exportToExcel(dataTable, tableId);
                    break;
                case 'pdf':
                    exportToPDF(dataTable, tableId);
                    break;
                default:
                    console.warn(`Unknown export type: ${exportType}`);
            }
        });
    }

    /**
     * Export DataTable to CSV
     * @param {Object} dataTable - DataTable instance
     * @param {string} tableId - Table ID
     */
    function exportToCSV(dataTable, tableId) {
        const data = dataTable.data().toArray();
        const headers = dataTable.columns().header().toArray().map(th => $(th).text());
        
        let csvContent = headers.join(',') + '\n';
        
        data.forEach(row => {
            const cleanRow = row.map(cell => {
                // Remove HTML tags and escape quotes
                const cleanCell = $('<div>').html(cell).text().replace(/"/g, '""');
                return `"${cleanCell}"`;
            });
            csvContent += cleanRow.join(',') + '\n';
        });
        
        downloadFile(csvContent, `${tableId}-export.csv`, 'text/csv');
    }

    /**
     * Setup loading overlay
     * @param {string} tableId - Table ID
     * @param {Object} dataTable - DataTable instance
     */
    function setupLoadingOverlay(tableId, dataTable) {
        const $loadingOverlay = $(`#${tableId}-loading`);
        
        if ($loadingOverlay.length) {
            dataTable.on('processing.dt', function(e, settings, processing) {
                if (processing) {
                    $loadingOverlay.show();
                } else {
                    $loadingOverlay.hide();
                }
            });
        }
    }

    /**
     * Download file helper
     * @param {string} content - File content
     * @param {string} filename - File name
     * @param {string} mimeType - MIME type
     */
    function downloadFile(content, filename, mimeType) {
        const blob = new Blob([content], { type: mimeType });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
    }

    /**
     * Utility function to refresh a DataTable
     * @param {string} tableId - Table ID
     */
    window.refreshDataTable = function(tableId) {
        if (window.DataTableInstances[tableId]) {
            window.DataTableInstances[tableId].ajax.reload();
        }
    };

    /**
     * Utility function to get DataTable instance
     * @param {string} tableId - Table ID
     * @returns {Object|null} DataTable instance
     */
    window.getDataTableInstance = function(tableId) {
        return window.DataTableInstances[tableId] || null;
    };

    /**
     * Initialize all DataTables on page load
     */
    $(document).ready(function() {
        // Find all tables with data-config attribute
        $('table[data-config]').each(function() {
            const $table = $(this);
            const configData = $table.data('config');
            
            if (configData && typeof configData === 'object') {
                initializeDataTable($table, configData);
            } else {
                console.warn('Invalid or missing data-config for table:', $table.attr('id'));
            }
        });
        
        // Initialize tables with basic configuration (fallback)
        $('table[id$="-datatable"]:not([data-config])').each(function() {
            const $table = $(this);
            console.warn('Table without data-config found, using default configuration:', $table.attr('id'));
            initializeDataTable($table, {});
        });
    });

    /**
     * Handle dynamic content loading
     */
    $(document).on('DOMNodeInserted', function(e) {
        const $target = $(e.target);
        
        // Check if new table was added
        if ($target.is('table[data-config]') || $target.find('table[data-config]').length) {
            setTimeout(function() {
                $target.find('table[data-config]').each(function() {
                    const $table = $(this);
                    const tableId = $table.attr('id');
                    
                    // Only initialize if not already initialized
                    if (!window.DataTableInstances[tableId]) {
                        const configData = $table.data('config');
                        if (configData && typeof configData === 'object') {
                            initializeDataTable($table, configData);
                        }
                    }
                });
            }, 100);
        }
    });

})(jQuery);