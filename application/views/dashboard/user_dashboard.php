<?php if (!$this->session->user_id): ?>
    <div class="alert alert-warning">
        OOPS - it seems like you are not logged in. Please log in
        <a href="<?= base_url('users/login') ?>">here</a>.
    </div>
<?php else: ?>
    <!-- Set chart data for JavaScript -->
    <script>
        window.statusLabels = <?php echo json_encode(array_keys(array_reduce($my_tasks, function ($carry, $task) {
            $carry[$task['task_status']] = $task['count'];
            return $carry; }, []))); ?>;
        window.statusData = <?php echo json_encode(array_values(array_reduce($my_tasks, function ($carry, $task) {
            $carry[$task['task_status']] = $task['count'];
            return $carry; }, []))); ?>;
        window.projectLabels = <?php echo json_encode(array_map(function ($p) {
            return $p['project_title']; }, $my_projects)); ?>;
        window.projectData = <?php echo json_encode(array_map(function ($p) {
            return $p['task_count']; }, $my_projects)); ?>;
        window.deadlineLabels = <?php echo json_encode(array_map(function ($t) {
            return $t['task_status']; }, $my_tasks)); ?>;
        window.deadlineData = <?php echo json_encode(array_map(function ($t) {
            return $t['count']; }, $my_tasks)); ?>;
    </script>

    <div class="container mt-4">
        <h2 class="title">My Dashboard</h2>

        <div id="flash-message">
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success">
                    <?= $this->session->flashdata('success') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="row mt-3">
            <div class="col-md-4">
                <h3>Tasks Status</h3>
                <?php if (!empty($my_tasks)): ?>
                    <canvas id="tasksStatusChart"></canvas>
                <?php else: ?>
                    <div class="alert alert-info">there is no mission to show now</div>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <h3>Tasks Per Project</h3>
                <?php if (!empty($my_projects)): ?>
                    <canvas id="tasksPerProjectChart"></canvas>
                <?php else: ?>
                    <div class="alert alert-info">there is no mission to show now</div>
                <?php endif; ?>
            </div>
            <div class="col-md-4">
                <h3>Tasks Deadline</h3>
                <?php if (!empty($my_tasks)): ?>
                    <canvas id="tasksDeadlineChart"></canvas>
                <?php else: ?>
                    <div class="alert alert-info">there is no mission to show now</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif; ?>


<!-- Dashboard Page Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="<?= base_url('assets/js/dashboard.js') ?>"></script>