/* ============================================
   DASHBOARD PAGE - JavaScript
   ============================================ */

$(document).ready(function () {
    initDashboardPage();
});

function initDashboardPage() {
    // Get chart data from window object (should be set by the PHP page)
    var statusLabels = window.statusLabels || [];
    var statusData = window.statusData || [];
    var projectLabels = window.projectLabels || [];
    var projectData = window.projectData || [];
    var deadlineLabels = window.deadlineLabels || [];
    var deadlineData = window.deadlineData || [];

    // Chart 1: Tasks Status (Pie Chart)
    var tasksStatusEl = document.getElementById('tasksStatusChart');
    if (tasksStatusEl) {
        const tasksStatusData = {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: ['#ffc107', '#17a2b8', '#28a745']
            }]
        };
        new Chart(tasksStatusEl, {
            type: 'pie',
            data: tasksStatusData,
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Chart 2: Tasks Per Project (Bar Chart)
    var tasksPerProjectEl = document.getElementById('tasksPerProjectChart');
    if (tasksPerProjectEl) {
        const tasksPerProjectData = {
            labels: projectLabels,
            datasets: [{
                label: 'My Tasks',
                data: projectData,
                backgroundColor: '#007bff'
            }]
        };
        new Chart(tasksPerProjectEl, {
            type: 'bar',
            data: tasksPerProjectData,
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Chart 3: Tasks Deadline (Bar Chart)
    var tasksDeadlineEl = document.getElementById('tasksDeadlineChart');
    if (tasksDeadlineEl) {
        const tasksDeadlineData = {
            labels: deadlineLabels,
            datasets: [{
                label: 'Tasks Due',
                data: deadlineData,
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                fill: true,
                tension: 0.3
            }]
        };
        new Chart(tasksDeadlineEl, {
            type: 'bar',
            data: tasksDeadlineData,
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }
}
