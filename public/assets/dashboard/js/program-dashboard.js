/**
 * Program Dashboard JavaScript
 * CSP Compliant - External JavaScript File
 * Handles dynamic content and interactions
 */

(function() {
    'use strict';

    /**
     * Dashboard Configuration
     */
    const DashboardConfig = {
        animationDuration: 300,
        updateInterval: 60000, // 1 minute
        numberFormat: {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    };

    /**
     * Dashboard Utilities
     */
    const DashboardUtils = {
        /**
         * Format number with locale-specific formatting
         * @param {number} value - The value to format
         * @param {number} decimals - Number of decimal places
         * @returns {string} Formatted number
         */
        formatNumber: function(value, decimals = 2) {
            return parseFloat(value).toLocaleString(undefined, {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        },

        /**
         * Calculate adoption rate percentage
         * @param {number} adopted - Number of adopted users
         * @param {number} total - Total number of users
         * @returns {number} Adoption rate percentage
         */
        calculateAdoptionRate: function(adopted, total) {
            if (total === 0) return 0;
            return (adopted / total) * 100;
        },

        /**
         * Format percentage with specific decimal places
         * @param {number} value - The percentage value
         * @param {number} decimals - Number of decimal places
         * @returns {string} Formatted percentage
         */
        formatPercentage: function(value, decimals = 2) {
            return this.formatNumber(value, decimals) + ' %';
        },

        /**
         * Add fade-in animation to element
         * @param {HTMLElement} element - The element to animate
         */
        fadeIn: function(element) {
            if (element) {
                element.classList.add('fade-in');
            }
        },

        /**
         * Update progress bar width
         * @param {HTMLElement} progressBar - The progress bar element
         * @param {number} percentage - The percentage to set
         */
        updateProgressBar: function(progressBar, percentage) {
            if (progressBar) {
                const clampedPercentage = Math.min(Math.max(percentage, 0), 100);
                progressBar.style.width = clampedPercentage + '%';
                progressBar.setAttribute('aria-valuenow', Math.round(clampedPercentage));
            }
        },

        /**
         * Safely get element by ID
         * @param {string} id - The element ID
         * @returns {HTMLElement|null} The element or null
         */
        getElement: function(id) {
            return document.getElementById(id);
        },

        /**
         * Safely get elements by class name
         * @param {string} className - The class name
         * @returns {HTMLCollection} Collection of elements
         */
        getElements: function(className) {
            return document.getElementsByClassName(className);
        }
    };

    /**
     * Dashboard Data Handler
     */
    const DashboardDataHandler = {
        /**
         * Update adoption metrics
         * @param {Object} data - The adoption data
         */
        updateAdoptionMetrics: function(data) {
            if (!data) return;

            // Update adopted users count
            const adoptedCountElement = document.querySelector('[data-metric="adopted-count"]');
            if (adoptedCountElement && data.adoptedUsers !== undefined) {
                adoptedCountElement.textContent = DashboardUtils.formatNumber(data.adoptedUsers, 0);
                DashboardUtils.fadeIn(adoptedCountElement);
            }

            // Update adoption rate
            const adoptionRateElement = document.querySelector('[data-metric="adoption-rate"]');
            if (adoptionRateElement && data.adoptionRate !== undefined) {
                adoptionRateElement.textContent = DashboardUtils.formatPercentage(data.adoptionRate);
                DashboardUtils.fadeIn(adoptionRateElement);
            }

            // Update progress bar
            const progressBar = document.querySelector('[data-metric="adoption-progress"]');
            if (progressBar && data.adoptionRate !== undefined) {
                DashboardUtils.updateProgressBar(progressBar, data.adoptionRate);
            }

            // Update min and max values
            const minValueElement = document.querySelector('[data-metric="min-value"]');
            if (minValueElement) {
                minValueElement.textContent = '0';
            }

            const maxValueElement = document.querySelector('[data-metric="max-value"]');
            if (maxValueElement && data.totalUsers !== undefined) {
                maxValueElement.textContent = DashboardUtils.formatNumber(data.totalUsers, 0);
            }
        },

        /**
         * Update license metrics
         * @param {Object} data - The license data
         */
        updateLicenseMetrics: function(data) {
            if (!data) return;

            const licenseElement = document.querySelector('[data-metric="licenses"]');
            if (licenseElement && data.maxLicenses !== undefined) {
                licenseElement.textContent = DashboardUtils.formatNumber(data.maxLicenses, 0);
                DashboardUtils.fadeIn(licenseElement);
            }
        },

        /**
         * Update trial information
         * @param {Object} data - The trial data
         */
        updateTrialInfo: function(data) {
            if (!data) return;

            const trialBadgeElement = document.querySelector('[data-metric="trial-badge"]');
            const trialDaysElement = document.querySelector('[data-metric="trial-days"]');

            if (data.isTrialActive) {
                if (trialBadgeElement) {
                    trialBadgeElement.style.display = 'inline';
                    trialBadgeElement.textContent = 'On Free Trial:';
                }
                if (trialDaysElement) {
                    trialDaysElement.style.display = 'inline';
                    trialDaysElement.textContent = data.daysLeft + ' days left of trial';
                }
            } else {
                if (trialBadgeElement) {
                    trialBadgeElement.style.display = 'none';
                }
                if (trialDaysElement) {
                    trialDaysElement.style.display = 'none';
                }
            }
        },

        /**
         * Update welcome message
         * @param {string} userName - The user's name
         */
        updateWelcomeMessage: function(userName) {
            const welcomeElement = document.querySelector('[data-metric="welcome-name"]');
            if (welcomeElement && userName) {
                welcomeElement.textContent = 'Welcome ' + userName + ' 👋';
                DashboardUtils.fadeIn(welcomeElement);
            }
        }
    };

    /**
     * Dashboard Event Handlers
     */
    const DashboardEventHandlers = {
        /**
         * Initialize event listeners
         */
        init: function() {
            // Add any event listeners here
            document.addEventListener('DOMContentLoaded', this.onDOMReady.bind(this));
        },

        /**
         * Handle DOM ready event
         */
        onDOMReady: function() {
            // Initialize animations
            const elements = document.querySelectorAll('[data-animate="true"]');
            elements.forEach(function(element) {
                DashboardUtils.fadeIn(element);
            });

            // Set up periodic updates if needed
            this.setupPeriodicUpdates();
        },

        /**
         * Setup periodic updates for dashboard data
         */
        setupPeriodicUpdates: function() {
            // Uncomment to enable periodic updates
            // setInterval(function() {
            //     DashboardDataHandler.refreshDashboardData();
            // }, DashboardConfig.updateInterval);
        }
    };

    /**
     * Public API
     */
    const Dashboard = {
        /**
         * Initialize the dashboard
         */
        init: function() {
            DashboardEventHandlers.init();
        },

        /**
         * Update dashboard data
         * @param {Object} data - The data to update
         */
        updateData: function(data) {
            if (data.adoption) {
                DashboardDataHandler.updateAdoptionMetrics(data.adoption);
            }
            if (data.license) {
                DashboardDataHandler.updateLicenseMetrics(data.license);
            }
            if (data.trial) {
                DashboardDataHandler.updateTrialInfo(data.trial);
            }
            if (data.userName) {
                DashboardDataHandler.updateWelcomeMessage(data.userName);
            }
        },

        /**
         * Format number utility
         * @param {number} value - The value to format
         * @param {number} decimals - Number of decimal places
         * @returns {string} Formatted number
         */
        formatNumber: function(value, decimals) {
            return DashboardUtils.formatNumber(value, decimals);
        },

        /**
         * Format percentage utility
         * @param {number} value - The percentage value
         * @param {number} decimals - Number of decimal places
         * @returns {string} Formatted percentage
         */
        formatPercentage: function(value, decimals) {
            return DashboardUtils.formatPercentage(value, decimals);
        },

        /**
         * Calculate adoption rate utility
         * @param {number} adopted - Number of adopted users
         * @param {number} total - Total number of users
         * @returns {number} Adoption rate percentage
         */
        calculateAdoptionRate: function(adopted, total) {
            return DashboardUtils.calculateAdoptionRate(adopted, total);
        }
    };

    // Initialize dashboard when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            Dashboard.init();
        });
    } else {
        Dashboard.init();
    }

    // Expose Dashboard API globally
    window.Dashboard = Dashboard;

})();