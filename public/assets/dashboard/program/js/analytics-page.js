// External dependencies (Chart.js and datalabels plugin)
// These should ideally be loaded in the main layout file, but for this refactoring,
// we'll assume they are loaded before this script, or we can include them here
// if the user wants a standalone file. Since the original file had them in the @section('js'),
// I will keep the imports in the modified HTML for now, and only move the custom logic.

// --- Custom Chart Logic ---

const growthProgramCtx = document.getElementById('growthProgramChart').getContext('2d');

// Data variables are passed from the server-side (Blade) and will be available in the global scope
// or need to be passed via a data attribute or a dedicated script block in the HTML.
// For now, I'll assume the data variables are available globally as they were in the original script block.
// const growthData = @json($growthData); // This line will be in the HTML
// const labels = @json($labels); // This line will be in the HTML

// Calculate the min and max values from the dataset
// Assuming growthData and labels are defined in a preceding script block in the HTML
if (typeof growthData !== 'undefined' && typeof labels !== 'undefined') {
    const minValue = Math.min(...growthData);
    const maxValue = Math.max(...growthData);

    // Chart initialization
    new Chart(growthProgramCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Growth of Program',
                data: growthData,
                borderColor: '#688EDC',
                fill: false,
                tension: 0.3, // Makes the line smooth
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false, // Remove vertical grid lines
                    }
                },
                y: {
                    beginAtZero: false,
                    min: minValue,
                    max: maxValue,
                    ticks: {
                        callback: function(value) {
                            if (value === minValue || value === maxValue) {
                                return value;
                            }
                            return '';
                        }
                    },
                    grid: {
                        display: true, // Keep horizontal grid lines
                    },
                    title: {
                        display: true,
                        text: 'Program Count',
                    }
                }
            }
        }
    });
}


const sessionsCtx = document.getElementById('sessionsChart').getContext('2d');

// Assuming labelsSession and growthDataSession are defined in a preceding script block in the HTML
if (typeof labelsSession !== 'undefined' && typeof growthDataSession !== 'undefined') {
    // Calculate the min and max values from the dataset
    const minValueSession = Math.min(...growthDataSession);
    const maxValueSession = Math.max(...growthDataSession);

    // Chart initialization
    new Chart(sessionsCtx, {
        type: 'line',
        data: {
            labels: labelsSession,
            datasets: [{
                label: 'Growth of Sessions',
                data: growthDataSession,
                borderColor: '#688EDC',
                fill: false,
                tension: 0.3, // Makes the line smooth
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false, // Remove vertical grid lines
                    }
                },
                y: {
                    beginAtZero: false,
                    min: minValueSession,
                    max: maxValueSession,
                    ticks: {
                        callback: function(value) {
                            if (value === minValueSession || value === maxValueSession) {
                                return value;
                            }
                            return '';
                        }
                    },
                    grid: {
                        display: true, // Keep horizontal grid lines
                    },
                    title: {
                        display: true,
                        text: 'Sessions Count',
                    }
                }
            }
        }
    });
}


const breakdownCtx = document.getElementById('breakdownChart').getContext('2d');

// Assuming sessionReasonLabel and sessionReasonData are defined in a preceding script block in the HTML
if (typeof sessionReasonLabel !== 'undefined' && typeof sessionReasonData !== 'undefined' && typeof ChartDataLabels !== 'undefined') {
    new Chart(breakdownCtx, {
        type: 'bar',
        data: {
            labels: sessionReasonLabel, // Sorted labels from backend
            datasets: [{
                label: 'Work-Related Sessions',
                data: sessionReasonData, // Sorted data from backend
                backgroundColor: Array(sessionReasonData.length).fill('#9E9E9E'),
                borderRadius: 20,
                borderSkipped: false
            }]
        },
        options: {
            indexAxis: 'y', // Ensures the bars are displayed horizontally
            plugins: {
                legend: {
                    display: false // Hides the legend if not needed
                },
                datalabels: {
                    anchor: 'end', // Position the labels at the end of the bars
                    align: 'right', // Align the labels to the right
                    color: '#000', // Set the label color
                    font: {
                        size: 12, // Adjust the font size
                        weight: 'bold'
                    },
                    formatter: (value) => value > 0 ? value : '' // Show value only if it's greater than 0
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false // Hides the x-axis gridlines
                    },
                    ticks: {
                        display: false // Hides the x-axis tick marks
                    },
                    beginAtZero: true, // Ensure the bars start at 0
                    max: Math.max(...sessionReasonData), // Dynamically set the maximum length based on data
                    // Add padding to the x-axis to prevent clipping of the largest bar
                    padding: 20
                },
                y: {
                    grid: {
                        display: false // Hides the y-axis gridlines
                    },
                    ticks: {
                        display: true, // Keeps the row labels visible
                        font: {
                            size: 12 // Adjust font size for visibility
                        },
                        color: '#000', // Optional: Set label color
                        padding: 10 // Reduce padding for Y-axis to make space for labels
                    },
                    title: {
                        display: true, // Shows the left-axis title
                        text: '', // Custom title text
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        color: '#000'
                    }
                }
            },
            layout: {
                padding: {
                    left: 0, // Increase the left padding to prevent the largest bar from getting cut off
                    right: 40
                }
            }
        },
        plugins: [ChartDataLabels] // Add the DataLabels plugin
    });
}