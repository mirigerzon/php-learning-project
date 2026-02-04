/**
 * Admin Dashboard JavaScript Module
 * Handles Chart.js initialization for admin dashboard charts
 */

$(document).ready(function () {
    // ---------- PROJECTS STATUS ----------
    const projectsStatusData = {
        labels: window.projectsStatusLabels || [],
        datasets: [{
            data: window.projectsStatusData || [],
            backgroundColor: ['#28a745', '#17a2b8', '#6c757d']
        }]
    };
    var projectsStatusEl = document.getElementById('projectsStatusChart');
    if (projectsStatusEl) {
        new Chart(projectsStatusEl, {
            type: 'pie',
            data: projectsStatusData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // ---------- TASKS STATUS ----------
    const tasksStatusData = {
        labels: window.tasksStatusLabels || [],
        datasets: [{
            data: window.tasksStatusData || [],
            backgroundColor: ['#ffc107', '#17a2b8', '#28a745']
        }]
    };
    var tasksStatusEl = document.getElementById('tasksStatusChartAdmin');
    if (tasksStatusEl) {
        new Chart(tasksStatusEl, {
            type: 'pie',
            data: tasksStatusData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // ---------- PROJECTS PER USER ----------
    const projectsPerUserData = {
        labels: window.projectsPerUserLabels || [],
        datasets: [{
            label: 'Projects',
            data: window.projectsPerUserData || [],
            backgroundColor: '#6f42c1'
        }]
    };
    var projectsPerUserEl = document.getElementById('projectsPerUserChart');
    if (projectsPerUserEl) {
        new Chart(projectsPerUserEl, {
            type: 'bar',
            data: projectsPerUserData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // ---------- TASKS PER USER ----------
    const tasksPerUserData = {
        labels: window.tasksPerUserLabels || [],
        datasets: [{
            label: 'Total Tasks',
            data: window.tasksPerUserData || [],
            backgroundColor: '#007bff'
        }]
    };
    var tasksPerUserEl = document.getElementById('tasksPerUserChart');
    if (tasksPerUserEl) {
        new Chart(tasksPerUserEl, {
            type: 'bar',
            data: tasksPerUserData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // ---------- TASKS DEADLINE ----------
    const tasksDeadlineData = {
        labels: window.tasksDeadlineLabels || [],
        datasets: [{
            label: 'Overdue Tasks',
            data: window.tasksDeadlineData || [],
            backgroundColor: '#dc3545'
        }]
    };
    var tasksDeadlineEl = document.getElementById('tasksDeadlineChartAdmin');
    if (tasksDeadlineEl) {
        // Render as bar chart to match categorical counts
        new Chart(tasksDeadlineEl, {
            type: 'bar',
            data: tasksDeadlineData,
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});
