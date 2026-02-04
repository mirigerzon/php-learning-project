<div class="admin-project-container">


    <!-- כפתור חזרה לרשימת הפרויקטים -->
    <div class="admin-project-back">
        <a href="<?= base_url('admin/projects') ?>" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                <path
                    d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
            </svg>
            Back to User Projects</a>
    </div>
    <hr>

    <!-- כותרת הפרויקט -->
    <div class="admin-project-header">
        <h2 class="project-title"><?= htmlspecialchars($project->project_title) ?></h2>
        <p>
            <strong>Owner ID:</strong> <?= htmlspecialchars($project->user_id) ?><br>
            <strong>Created:</strong> <?= date('d/m/Y', strtotime($project->created_at)) ?>
        </p>
    </div>

    <hr>

    <!-- סטטיסטיקות קצרות -->
    <div class="admin-project-stats">
        <p>
            <strong>Total Tasks:</strong> <?= count($tasks) ?><br>
            <strong>Completed:</strong> <?= count(array_filter($tasks, function ($t) {
                return $t->status == 1;
            })) ?><br>
            <strong>Open / Pending:</strong>
            <?= count(array_filter($tasks, function ($t) {
                return $t->status == 0;
            })) ?>

        </p>
    </div>

    <hr>

    <!-- רשימת המשימות -->
    <div class="admin-tasks-table">
        <h3>Tasks</h3>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Status</th>
                    <th>Due Date</th>
                    <th>Owner ID</th>
                    <th>Images</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($tasks)): ?>
                    <?php foreach ($tasks as $i => $task): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($task->task_title) ?></td>
                            <td>
                                <?= $task->status == 1 ? '<span class="badge badge-success">Done</span>' : '<span class="badge badge-warning">Open</span>' ?>
                            </td>
                            <td><?= $task->due_date ?? '-' ?></td>
                            <td><?= $task->project_owner_id ?></td>
                            <td>
                                <?php $images = $this->Task_model->get_task_images($task->task_id); ?>
                                <?= count($images) ?>         <?= count($images) === 1 ? 'image' : 'images' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No tasks found for this project.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <hr>
</div>

<!-- סגנון בסיסי -->
<style>
    .admin-project-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
    }

    .admin-project-header h2 {
        margin-bottom: 5px;
    }

    .admin-project-header p {
        color: #555;
    }

    .admin-project-stats p {
        font-weight: bold;
    }

    .admin-tasks-table table {
        width: 100%;
        border-collapse: collapse;
    }

    .admin-tasks-table th,
    .admin-tasks-table td {
        padding: 10px;
        text-align: left;
    }

    .admin-tasks-table th {
        background-color: #f5f5f5;
    }

    .badge-success {
        background-color: #28a745;
        color: white;
        padding: 3px 6px;
        border-radius: 4px;
    }

    .badge-warning {
        background-color: #ffc107;
        color: #212529;
        padding: 3px 6px;
        border-radius: 4px;
    }

    .admin-project-back {
        margin-top: 20px;
    }
</style>