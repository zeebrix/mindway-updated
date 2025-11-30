/**
 * DataTable Utility Functions
 * 
 * Additional utility functions to enhance DataTable functionality
 */

(function($) {
    'use strict';

    /**
     * DataTable Utilities Namespace
     */
    window.DataTableUtils = {
        
        /**
         * Bulk actions functionality
         * @param {string} tableId - Table ID
         * @param {Array} actions - Array of action configurations
         */
        setupBulkActions: function(tableId, actions) {
            const dataTable = window.DataTableInstances[tableId];
            if (!dataTable) return;

            // Add checkboxes to first column
            dataTable.column(0).visible(false);
            
            // Add select all checkbox to header
            const $headerCheckbox = $('<input type="checkbox" id="select-all-' + tableId + '">');
            $(`#${tableId} thead tr th:first-child`).html($headerCheckbox);
            
            // Add checkboxes to each row
            dataTable.on('draw', function() {
                $(`#${tableId} tbody tr`).each(function(index) {
                    const $row = $(this);
                    const rowData = dataTable.row($row).data();
                    const $checkbox = $('<input type="checkbox" class="row-checkbox" value="' + rowData.id + '">');
                    $row.find('td:first-child').html($checkbox);
                });
            });
            
            // Handle select all
            $headerCheckbox.on('change', function() {
                const isChecked = $(this).is(':checked');
                $(`#${tableId} .row-checkbox`).prop('checked', isChecked);
                this.updateBulkActionButtons(tableId);
            });
            
            // Handle individual checkboxes
            $(document).on('change', `#${tableId} .row-checkbox`, function() {
                this.updateBulkActionButtons(tableId);
            });
            
            // Create bulk action buttons
            this.createBulkActionButtons(tableId, actions);
        },

        /**
         * Create bulk action buttons
         * @param {string} tableId - Table ID
         * @param {Array} actions - Array of action configurations
         */
        createBulkActionButtons: function(tableId, actions) {
            const $container = $(`#${tableId}`).closest('.card-body');
            const $bulkActions = $('<div class="bulk-actions mb-3" style="display: none;"></div>');
            
            actions.forEach(action => {
                const $button = $(`<button type="button" class="btn ${action.class || 'btn-primary'} btn-sm me-2" data-action="${action.name}">${action.text}</button>`);
                $button.on('click', function() {
                    const selectedIds = this.getSelectedIds(tableId);
                    if (selectedIds.length > 0) {
                        action.callback(selectedIds);
                    }
                });
                $bulkActions.append($button);
            });
            
            $container.prepend($bulkActions);
        },

        /**
         * Update bulk action button visibility
         * @param {string} tableId - Table ID
         */
        updateBulkActionButtons: function(tableId) {
            const selectedCount = $(`#${tableId} .row-checkbox:checked`).length;
            const $bulkActions = $(`#${tableId}`).closest('.card-body').find('.bulk-actions');
            
            if (selectedCount > 0) {
                $bulkActions.show().find('button').each(function() {
                    $(this).text($(this).text().replace(/\(\d+\)/, '') + ` (${selectedCount})`);
                });
            } else {
                $bulkActions.hide();
            }
        },

        /**
         * Get selected row IDs
         * @param {string} tableId - Table ID
         * @returns {Array} Array of selected IDs
         */
        getSelectedIds: function(tableId) {
            const selectedIds = [];
            $(`#${tableId} .row-checkbox:checked`).each(function() {
                selectedIds.push($(this).val());
            });
            return selectedIds;
        },

        /**
         * Setup advanced search functionality
         * @param {string} tableId - Table ID
         * @param {Object} searchConfig - Search configuration
         */
        setupAdvancedSearch: function(tableId, searchConfig) {
            const dataTable = window.DataTableInstances[tableId];
            if (!dataTable) return;

            // Create advanced search modal
            const modalHtml = this.createAdvancedSearchModal(tableId, searchConfig);
            $('body').append(modalHtml);
            
            // Add advanced search button
            const $advancedSearchBtn = $('<button type="button" class="btn btn-outline-secondary btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#advanced-search-' + tableId + '">Advanced Search</button>');
            $(`#${tableId}_filter`).append($advancedSearchBtn);
        },

        /**
         * Create advanced search modal HTML
         * @param {string} tableId - Table ID
         * @param {Object} searchConfig - Search configuration
         * @returns {string} Modal HTML
         */
        createAdvancedSearchModal: function(tableId, searchConfig) {
            let formFields = '';
            
            searchConfig.fields.forEach(field => {
                formFields += `
                    <div class="mb-3">
                        <label for="${field.name}" class="form-label">${field.label}</label>
                        ${this.createSearchField(field)}
                    </div>
                `;
            });
            
            return `
                <div class="modal fade" id="advanced-search-${tableId}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Advanced Search</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="advanced-search-form-${tableId}">
                                    ${formFields}
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" onclick="DataTableUtils.applyAdvancedSearch('${tableId}')">Search</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="DataTableUtils.clearAdvancedSearch('${tableId}')">Clear</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        },

        /**
         * Create search field HTML
         * @param {Object} field - Field configuration
         * @returns {string} Field HTML
         */
        createSearchField: function(field) {
            switch (field.type) {
                case 'select':
                    let options = '<option value="">All</option>';
                    field.options.forEach(option => {
                        options += `<option value="${option.value}">${option.text}</option>`;
                    });
                    return `<select class="form-select" name="${field.name}">${options}</select>`;
                
                case 'date':
                    return `<input type="date" class="form-control" name="${field.name}">`;
                
                case 'daterange':
                    return `
                        <div class="row">
                            <div class="col-6">
                                <input type="date" class="form-control" name="${field.name}_from" placeholder="From">
                            </div>
                            <div class="col-6">
                                <input type="date" class="form-control" name="${field.name}_to" placeholder="To">
                            </div>
                        </div>
                    `;
                
                case 'number':
                    return `<input type="number" class="form-control" name="${field.name}" placeholder="${field.placeholder || ''}">`;
                
                default:
                    return `<input type="text" class="form-control" name="${field.name}" placeholder="${field.placeholder || ''}">`;
            }
        },

        /**
         * Apply advanced search
         * @param {string} tableId - Table ID
         */
        applyAdvancedSearch: function(tableId) {
            const dataTable = window.DataTableInstances[tableId];
            if (!dataTable) return;

            const formData = $(`#advanced-search-form-${tableId}`).serializeArray();
            
            // Clear existing search
            dataTable.search('').columns().search('');
            
            // Apply new search criteria
            formData.forEach(item => {
                if (item.value) {
                    // Find column index by name
                    const columnIndex = dataTable.columns().header().toArray()
                        .findIndex(header => $(header).data('column') === item.name);
                    
                    if (columnIndex !== -1) {
                        dataTable.column(columnIndex).search(item.value);
                    }
                }
            });
            
            dataTable.draw();
            $(`#advanced-search-${tableId}`).modal('hide');
        },

        /**
         * Clear advanced search
         * @param {string} tableId - Table ID
         */
        clearAdvancedSearch: function(tableId) {
            $(`#advanced-search-form-${tableId}`)[0].reset();
            const dataTable = window.DataTableInstances[tableId];
            if (dataTable) {
                dataTable.search('').columns().search('').draw();
            }
        },

        /**
         * Setup column visibility toggle
         * @param {string} tableId - Table ID
         */
        setupColumnVisibility: function(tableId) {
            const dataTable = window.DataTableInstances[tableId];
            if (!dataTable) return;

            // Create column visibility dropdown
            const $dropdown = $(`
                <div class="dropdown d-inline-block ms-2">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Columns
                    </button>
                    <ul class="dropdown-menu" id="column-visibility-${tableId}">
                    </ul>
                </div>
            `);
            
            // Add column options
            dataTable.columns().every(function(index) {
                const column = this;
                const title = $(column.header()).text();
                
                if (title && title !== 'Actions') {
                    const $item = $(`
                        <li>
                            <label class="dropdown-item">
                                <input type="checkbox" ${column.visible() ? 'checked' : ''}> ${title}
                            </label>
                        </li>
                    `);
                    
                    $item.find('input').on('change', function() {
                        column.visible($(this).is(':checked'));
                    });
                    
                    $dropdown.find('.dropdown-menu').append($item);
                }
            });
            
            $(`#${tableId}_filter`).append($dropdown);
        },

        /**
         * Setup row details functionality
         * @param {string} tableId - Table ID
         * @param {Function} detailsCallback - Function to generate details content
         */
        setupRowDetails: function(tableId, detailsCallback) {
            const dataTable = window.DataTableInstances[tableId];
            if (!dataTable) return;

            // Add details column
            dataTable.column(0).visible(false);
            
            // Add click handler for row details
            $(`#${tableId} tbody`).on('click', 'tr', function() {
                const tr = $(this);
                const row = dataTable.row(tr);
                
                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    const details = detailsCallback(row.data());
                    row.child(details).show();
                    tr.addClass('shown');
                }
            });
        },

        /**
         * Setup auto-refresh functionality
         * @param {string} tableId - Table ID
         * @param {number} interval - Refresh interval in milliseconds
         */
        setupAutoRefresh: function(tableId, interval = 30000) {
            const dataTable = window.DataTableInstances[tableId];
            if (!dataTable) return;

            let refreshTimer;
            let isAutoRefreshEnabled = false;
            
            // Create auto-refresh toggle
            const $toggle = $(`
                <div class="form-check form-switch d-inline-block ms-2">
                    <input class="form-check-input" type="checkbox" id="auto-refresh-${tableId}">
                    <label class="form-check-label" for="auto-refresh-${tableId}">Auto Refresh</label>
                </div>
            `);
            
            $toggle.find('input').on('change', function() {
                isAutoRefreshEnabled = $(this).is(':checked');
                
                if (isAutoRefreshEnabled) {
                    refreshTimer = setInterval(function() {
                        dataTable.ajax.reload(null, false);
                    }, interval);
                } else {
                    clearInterval(refreshTimer);
                }
            });
            
            $(`#${tableId}_filter`).append($toggle);
        }
    };

})(jQuery);