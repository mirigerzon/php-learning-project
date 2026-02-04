<div class="container mt-4 admin-dashboard">
    <h2 class="dashboard-title">Admin Dashboard</h2>

    <!-- Set chart data for JavaScript -->
    <script>
        window.projectsStatusLabels = <?php echo json_encode(array_column($projects_status, 'project_status')); ?>;
        window.projectsStatusData = <?php echo json_encode(array_column($projects_status, 'count')); ?>;
        window.tasksStatusLabels = <?php echo json_encode(array_column($tasks_status, 'task_status')); ?>;
        window.tasksStatusData = <?php echo json_encode(array_column($tasks_status, 'count')); ?>;
        window.projectsPerUserLabels = <?php echo json_encode(array_column($projects_per_user, 'username')); ?>;
        window.projectsPerUserData = <?php echo json_encode(array_column($projects_per_user, 'count')); ?>;
        window.tasksPerUserLabels = <?php echo json_encode(array_column($tasks_per_user, 'username')); ?>;
        window.tasksPerUserData = <?php echo json_encode(array_column($tasks_per_user, 'total_tasks')); ?>;
        window.tasksDeadlineLabels = <?php echo json_encode(array_column($tasks_per_user, 'username')); ?>;
        window.tasksDeadlineData = <?php echo json_encode(array_column($tasks_per_user, 'overdue_tasks')); ?>;
    </script>

    <div class="row mt-3 chart-row">
        <div class="col-md-4 mb-4">
            <div class="chart-card">
                <h3>Projects Status</h3>
                <?php if (!empty($projects_status)): ?>
                    <canvas id="projectsStatusChart"></canvas>
                <?php else: ?>
                    <div class="alert alert-info">אין פרויקטים להצגה.</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="chart-card">
                <h3>Tasks Status</h3>
                <?php if (!empty($tasks_status)): ?>
                    <canvas id="tasksStatusChartAdmin"></canvas>
                <?php else: ?>
                    <div class="alert alert-info">אין משימות להצגה.</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="chart-card">
                <h3>Tasks Per User</h3>
                <?php if (!empty($tasks_per_user)): ?>
                    <canvas id="tasksPerUserChart"></canvas>
                <?php else: ?>
                    <div class="alert alert-info">אין משימות למשתמשים להצגה.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row mt-4 chart-row">
        <div class="col-md-6 mb-4">
            <div class="chart-card">
                <h3>Projects Per User</h3>
                <?php if (!empty($projects_per_user)): ?>
                    <canvas id="projectsPerUserChart"></canvas>
                <?php else: ?>
                    <div class="alert alert-info">אין פרויקטים למשתמשים להצגה.</div>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="chart-card">
                <h3>Tasks Deadline</h3>
                <?php if (!empty($tasks_per_user)): ?>
                    <canvas id="tasksDeadlineChartAdmin"></canvas>
                <?php else: ?>
                    <div class="alert alert-info">אין מועדי סיום להצגה.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/js/admin-dashboard.js') ?>"></script>