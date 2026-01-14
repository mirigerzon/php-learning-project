<?php if (!$this->session->user_id): ?>
    <h2 class="un_loged_in_message">
        OOPS - it seems like you are not logged in.
        Please log in <a href="<?= base_url('users/login') ?>">here</a>.
    </h2>
<?php else: ?>

    <div class="title">
        <h2>
            Tasks for project:
            <?= htmlspecialchars($project->project_title) ?>
        </h2>
    </div>

    <a href="<?= base_url("tasks/add/{$project_id}") ?>" class="btn btn-success" style="margin-bottom:15px;">
        + Add New Task
    </a>

    <!-- Filter buttons -->
    <div style="margin-bottom:15px;">
        <a href="<?= base_url("tasks/index/{$project_id}") ?>"
            class="btn btn-secondary <?= !$status_filter ? 'active' : '' ?>">
            All
        </a>

        <a href="<?= base_url("tasks/index/{$project_id}?status=pending") ?>"
            class="btn btn-warning <?= $status_filter === 'pending' ? 'active' : '' ?>">
            Pending
        </a>

        <a href="<?= base_url("tasks/index/{$project_id}?status=done") ?>"
            class="btn btn-success <?= $status_filter === 'done' ? 'active' : '' ?>">
            Done
        </a>

        <a href="<?= base_url("tasks/index/{$project_id}?status=late") ?>"
            class="btn btn-danger <?= $status_filter === 'late' ? 'active' : '' ?>">
            Late
        </a>
    </div>

    <?php if (empty($tasks)): ?>
        <p>No tasks found for this project.</p>
    <?php else: ?>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Task Title</th>
                    <th>Description</th>
                    <th>Created At</th>
                    <th>Due Date</th>
                    <th>Images</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($tasks as $task): ?>

                    <?php
                    $is_done = (int) $task->status === 1;
                    $is_late = !$is_done
                        && !empty($task->due_date)
                        && $task->due_date < date('Y-m-d');

                    $row_class = $is_done
                        ? 'task-done'
                        : ($is_late ? 'task-late' : '');
                    ?>

                    <tr class="<?= $row_class ?>">
                        <td>
                            <a href="<?= base_url("tasks/view/{$task->task_id}") ?>">
                                <?= htmlspecialchars($task->task_title) ?>
                            </a>

                            <?php if ($is_done): ?>
                                <span class="badge badge-success">Done</span>
                            <?php elseif ($is_late): ?>
                                <span class="badge badge-danger" style="background-color: #f38e96;">Late</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?= nl2br(htmlspecialchars($task->task_body)) ?>
                        </td>

                        <td>
                            <?= date('d/m/Y H:i', strtotime($task->created_at)) ?>
                        </td>

                        <td>
                            <?= $task->due_date ?: '-' ?>
                        </td>

                        <td>
                            <?= $task->image_count ?>
                        </td>

                        <td>
                            <?php if (!$is_done): ?>
                                <a href="<?= base_url("tasks/mark_as_done/{$task->project_id}/{$task->task_id}") ?>"
                                    class="btn btn-primary btn-xs">
                                    Mark as done
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url("tasks/mark_as_un_done/{$task->project_id}/{$task->task_id}") ?>"
                                    class="btn btn-primary btn-xs">
                                    Mark as un done
                                </a>
                            <?php endif; ?>

                            <a href="<?= base_url("tasks/edit/{$task->project_id}/{$task->task_id}") ?>"
                                class="btn btn-info btn-xs">
                                Edit
                            </a>

                            <a href="<?= base_url("tasks/delete/{$task->project_id}/{$task->task_id}") ?>"
                                class="btn btn-danger btn-xs" onclick="return confirm('Are you sure?')">
                                Delete
                            </a>
                        </td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>

    <a href="<?= base_url('projects') ?>" class="btn btn-default">
        ← Back to Projects
    </a>

<?php endif; ?>