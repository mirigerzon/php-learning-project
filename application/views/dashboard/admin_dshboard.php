<div class="container mt-4">
    <h2>Admin Dashboard</h2>

    <div class="row mt-3">
        <div class="col-md-4">
            <canvas id="projectsStatusChart"></canvas>
        </div>
        <div class="col-md-4">
            <canvas id="tasksStatusChartAdmin"></canvas>
        </div>
        <div class="col-md-4">
            <canvas id="tasksPerUserChart"></canvas>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-md-6">
            <canvas id="projectsPerUserChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="tasksDeadlineChartAdmin"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function () {
        // ---------------- PROJECTS STATUS ----------------
        const projectsStatusData = {
            labels: <?php echo json_encode(array_column($projects_status, 'project_status')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($projects_status, 'count')); ?>,
                backgroundColor: ['#28a745', '#17a2b8', '#6c757d']
            }]
        };
        new Chart(document.getElementById('projectsStatusChart'), { type: 'pie', data: projectsStatusData });

        // ---------------- TASKS STATUS ----------------
        const tasksStatusData = {
            labels: <?php echo json_encode(array_column($tasks_status, 'task_status')); ?>,
            datasets: [{
                data: <?php echo json_encode(array_column($tasks_status, 'count')); ?>,
                backgroundColor: ['#ffc107', '#17a2b8', '#28a745']
            }]
        };
        new Chart(document.getElementById('tasksStatusChartAdmin'), { type: 'pie', data: tasksStatusData });

        // ---------------- PROJECTS PER USER ----------------
        const projectsPerUserData = {
            labels: <?php echo json_encode(array_column($projects_per_user, 'username')); ?>,
            datasets: [{ label: 'Projects', data: <?php echo json_encode(array_column($projects_per_user, 'count')); ?>, backgroundColor: '#6f42c1' }]
        };
        new Chart(document.getElementById('projectsPerUserChart'), { type: 'bar', data: projectsPerUserData, options: { scales: { y: { beginAtZero: true } } } });

        // ---------------- TASKS PER USER ----------------
        const tasksPerUserData = {
            labels: <?= json_encode(array_column($tasks_per_user, 'username')) ?>,
            datasets: [{
                label: 'Total Tasks',
                data: <?= json_encode(array_column($tasks_per_user, 'total_tasks')) ?>,
                backgroundColor: '#007bff'
            }]
        };
        new Chart(document.getElementById('tasksPerUserChart'), {
            type: 'bar',
            data: tasksPerUserData,
            options: { scales: { y: { beginAtZero: true } } }
        });

        // ---------------- TASKS DEADLINE ----------------
        const tasksDeadlineData = {
            labels: <?= json_encode(array_column($tasks_per_user, 'username')) ?>,
            datasets: [{
                label: 'Overdue Tasks',
                data: <?= json_encode(array_column($tasks_per_user, 'overdue_tasks')) ?>,
                borderColor: '#dc3545',
                fill: false,
                tension: 0.3
            }]
        };
        new Chart(document.getElementById('tasksDeadlineChartAdmin'), {
            type: 'line',
            data: tasksDeadlineData,
            options: { scales: { y: { beginAtZero: true } } }
        });

    });
</script>