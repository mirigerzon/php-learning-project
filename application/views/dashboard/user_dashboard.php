<div class="container mt-4">
    <h2>My Dashboard</h2>

    <div id="flash-message">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success">
                <?= $this->session->flashdata('success') ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="row mt-3">
        <div class="col-md-4">
            <canvas id="tasksStatusChart"></canvas>
        </div>
        <div class="col-md-4">
            <canvas id="tasksPerProjectChart"></canvas>
        </div>
        <div class="col-md-4">
            <canvas id="tasksDeadlineChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function () {
        // ---------------- TASKS STATUS ----------------
        <?php
        $status_count = [];
        foreach ($my_tasks as $task) {
            $status_count[$task['task_status']] = $task['count'];
        }
        $status_labels = array_keys($status_count);
        $status_data = array_values($status_count);
        ?>
        const tasksStatusData = {
            labels: <?php echo json_encode($status_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($status_data); ?>,
                backgroundColor: ['#ffc107', '#17a2b8', '#28a745']
            }]
        };
        new Chart(document.getElementById('tasksStatusChart'), { type: 'pie', data: tasksStatusData });

        // ---------------- TASKS PER PROJECT ----------------
        <?php
        $projects_labels = array_map(function ($p) {
            return $p['project_title'];
        }, $my_projects);
        $projects_tasks = array_map(function ($p) {
            return $p['task_count'];
        }, $my_projects);
        ?>
        const tasksPerProjectData = {
            labels: <?php echo json_encode($projects_labels); ?>,
            datasets: [{ label: 'My Tasks', data: <?php echo json_encode($projects_tasks); ?>, backgroundColor: '#007bff' }]
        };
        new Chart(document.getElementById('tasksPerProjectChart'), { type: 'bar', data: tasksPerProjectData, options: { scales: { y: { beginAtZero: true } } } });

        // ---------------- TASKS DEADLINE ----------------
        <?php
        $deadline_labels = array_map(function ($t) {
            return $t['task_status'];
        }, $my_tasks);
        $deadline_data = array_map(function ($t) {
            return $t['count'];
        }, $my_tasks);
        ?>
        const tasksDeadlineData = {
            labels: <?php echo json_encode($deadline_labels); ?>,
            datasets: [{
                label: 'Tasks Due',
                data: <?php echo json_encode($deadline_data); ?>,
                borderColor: '#dc3545',
                fill: false,
                tension: 0.3
            }]
        };
        new Chart(document.getElementById('tasksDeadlineChart'), { type: 'line', data: tasksDeadlineData, options: { scales: { y: { beginAtZero: true } } } });
    });
</script>