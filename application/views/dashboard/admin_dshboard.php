<div class="container mt-4 admin-dashboard">
    <h2 class="dashboard-title">Admin Dashboard</h2>

    <div class="row mt-3 chart-row">
        <div class="col-md-4 mb-4">
            <div class="chart-card">
                <canvas id="projectsStatusChart"></canvas>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="chart-card">
                <canvas id="tasksStatusChartAdmin"></canvas>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="chart-card">
                <canvas id="tasksPerUserChart"></canvas>
            </div>
        </div>
    </div>

    <div class="row mt-4 chart-row">
        <div class="col-md-6 mb-4">
            <div class="chart-card">
                <canvas id="projectsPerUserChart"></canvas>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="chart-card">
                <canvas id="tasksDeadlineChartAdmin"></canvas>
            </div>
        </div>
    </div>
</div>

<style>
/* ================== Typography ================== */
.admin-dashboard {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #343a40;
}
.dashboard-title {
    font-weight: 600;
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
    color: #495057;
}

/* ================== Chart Cards ================== */
.chart-card {
    background: #ffffff;
    padding: 1rem;
    border-radius: 0.75rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: transform 0.2s, box-shadow 0.2s;
}
.chart-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

/* ================== Responsiveness ================== */
@media (max-width: 991px) {
    .chart-card {
        padding: 0.8rem;
    }
    .dashboard-title {
        font-size: 1.6rem;
    }
}

@media (max-width: 575px) {
    .chart-card {
        padding: 0.6rem;
    }
    .dashboard-title {
        font-size: 1.4rem;
        text-align: center;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function () {
    // ---------- PROJECTS STATUS ----------
    const projectsStatusData = {
        labels: <?= json_encode(array_column($projects_status, 'project_status')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($projects_status, 'count')) ?>,
            backgroundColor: ['#28a745', '#17a2b8', '#6c757d']
        }]
    };
    new Chart(document.getElementById('projectsStatusChart'), { type: 'pie', data: projectsStatusData, options: { responsive: true, plugins: { legend: { position: 'bottom' } } } });

    // ---------- TASKS STATUS ----------
    const tasksStatusData = {
        labels: <?= json_encode(array_column($tasks_status, 'task_status')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($tasks_status, 'count')) ?>,
            backgroundColor: ['#ffc107', '#17a2b8', '#28a745']
        }]
    };
    new Chart(document.getElementById('tasksStatusChartAdmin'), { type: 'pie', data: tasksStatusData, options: { responsive: true, plugins: { legend: { position: 'bottom' } } } });

    // ---------- PROJECTS PER USER ----------
    const projectsPerUserData = {
        labels: <?= json_encode(array_column($projects_per_user, 'username')) ?>,
        datasets: [{ label: 'Projects', data: <?= json_encode(array_column($projects_per_user, 'count')) ?>, backgroundColor: '#6f42c1' }]
    };
    new Chart(document.getElementById('projectsPerUserChart'), { type: 'bar', data: projectsPerUserData, options: { responsive: true, scales: { y: { beginAtZero: true } } } });

    // ---------- TASKS PER USER ----------
    const tasksPerUserData = {
        labels: <?= json_encode(array_column($tasks_per_user, 'username')) ?>,
        datasets: [{ label: 'Total Tasks', data: <?= json_encode(array_column($tasks_per_user, 'total_tasks')) ?>, backgroundColor: '#007bff' }]
    };
    new Chart(document.getElementById('tasksPerUserChart'), { type: 'bar', data: tasksPerUserData, options: { responsive: true, scales: { y: { beginAtZero: true } } } });

    // ---------- TASKS DEADLINE ----------
    const tasksDeadlineData = {
        labels: <?= json_encode(array_column($tasks_per_user, 'username')) ?>,
        datasets: [{ label: 'Overdue Tasks', data: <?= json_encode(array_column($tasks_per_user, 'overdue_tasks')) ?>, borderColor: '#dc3545', fill: false, tension: 0.3 }]
    };
    new Chart(document.getElementById('tasksDeadlineChartAdmin'), { type: 'line', data: tasksDeadlineData, options: { responsive: true, scales: { y: { beginAtZero: true } } } });
});
</script>
