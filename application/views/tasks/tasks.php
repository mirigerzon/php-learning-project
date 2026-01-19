<?php if (!$this->session->user_id): ?>
    <h2 class="un_loged_in_message">
        OOPS - it seems like you are not logged in.
        Please log in <a href="<?= base_url('users/login') ?>">here</a>.
    </h2>
<?php else: ?>

    <?php
    // משתנים מרכזיים להרשאות
    $user_id = $this->session->user_id;
    $project_permission = $this->session->userdata('project_permission') ?? null;
    $project_owner_id = $project->user_id ?? null;
    $can_edit = $user_id == $project_owner_id || $project_permission === 'edit';
    $can_view = $can_edit || $project_permission === 'view';
    ?>

    <?php if ($can_view): ?>
        <a href="<?= base_url('projects') ?>" class="btn btn-default">← Back to Projects</a>

        <div class="title">
            <h2>
                Tasks for project: <?= htmlspecialchars($project->project_title) ?>
            </h2>
        </div>
        <?php if ($can_edit): ?>
            <button id="show-add-task-form" class="btn btn-success" style="margin-bottom: 15px;">
                + Add New Task
            </button>
        <?php endif; ?>

        <!-- Filter buttons -->
        <div style="margin-bottom:15px;">
            <a href="<?= base_url("tasks/index/{$project_id}") ?>"
                class="btn btn-secondary <?= !$status_filter ? 'active' : '' ?>">All</a>
            <a href="<?= base_url("tasks/index/{$project_id}?status=pending") ?>"
                class="btn btn-warning <?= $status_filter === 'pending' ? 'active' : '' ?>">Pending</a>
            <a href="<?= base_url("tasks/index/{$project_id}?status=done") ?>"
                class="btn btn-success <?= $status_filter === 'done' ? 'active' : '' ?>">Done</a>
            <a href="<?= base_url("tasks/index/{$project_id}?status=late") ?>"
                class="btn btn-danger <?= $status_filter === 'late' ? 'active' : '' ?>">Late</a>
        </div>

        <?php if (empty($tasks)): ?>
            <p>No tasks found for this project.</p>
        <?php else: ?>
            <table id="tasks-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Task Title</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Due Date</th>
                        <th>Images</th>
                        <?php if ($can_edit): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tasks as $task): ?>
                        <?php
                        $is_done = (int) $task->status === 1;
                        $is_late = !$is_done && !empty($task->due_date) && $task->due_date < date('Y-m-d');
                        $row_class = $is_done ? 'task-done' : ($is_late ? 'task-late' : '');
                        ?>
                        <tr id="task-<?= $task->task_id ?>" class="<?= $row_class ?>">
                            <td>
                                <?= htmlspecialchars($task->task_title) ?>
                                <?php if ($is_done): ?>
                                    <span class="badge badge-success">Done</span>
                                <?php elseif ($is_late): ?>
                                    <span class="badge badge-danger" style="background-color: #f38e96;">Late</span>
                                <?php endif; ?>
                            </td>
                            <td><?= nl2br(htmlspecialchars($task->task_body)) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($task->created_at)) ?></td>
                            <td><?= $task->due_date ?: '-' ?></td>
                            <td><?= $task->image_count ?></td>

                            <?php if ($can_edit): ?>
                                <td>
                                    <?php if (!$is_done): ?>
                                        <a href="<?= base_url("tasks/mark_as_done/{$task->project_id}/{$task->task_id}") ?>"
                                            class="btn btn-primary btn-xs">Mark as done</a>
                                    <?php else: ?>
                                        <a href="<?= base_url("tasks/mark_as_un_done/{$task->project_id}/{$task->task_id}") ?>"
                                            class="btn btn-primary btn-xs">Mark as un done</a>
                                    <?php endif; ?>
                                    <a href="<?= base_url("tasks/view/{$task->project_id}/{$task->task_id}") ?>"
                                        class="btn btn-info btn-xs">View</a>
                                    <a href="<?= base_url("tasks/delete/{$task->project_id}/{$task->task_id}") ?>"
                                        class="btn btn-danger btn-xs" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            <?php elseif ($project_permission === 'view'): ?>
                                <td>
                                    <a href="<?= base_url("tasks/view/{$task->project_id}/{$task->task_id}") ?>"
                                        class="btn btn-info btn-xs">View</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

    <?php else: ?>
        <h3>You do not have permission to view this project.</h3>
    <?php endif; ?>

    <!-- Modal Bootstrap 3 -->
    <div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog" aria-labelledby="addTaskModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="addTaskModalLabel">Add New Task</h4>
                </div>
                <div class="modal-body" id="add-task-container">
                    <!-- כאן יגיע הטופס ב-AJAX -->
                </div>
            </div>
        </div>
    </div>


<?php endif; ?>



<!-- Bootstrap 3 CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap 3 JS -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>


<script>
    $(document).ready(function () {

        // אתחול DataTable למשימות
        var tasksTable = $('#tasks-table').DataTable({
            "order": [[0, 'asc']], // מיון ברירת מחדל לפי Task Title
            "pageLength": 10
        });

        // פתיחת Modal והטענת הטופס
        $('#show-add-task-form').on('click', function () {
            $.get('<?= base_url("tasks/add_ajax_form/{$project_id}") ?>', function (html) {
                $('#add-task-container').html(html);
                $('#addTaskModal').modal('show'); // Bootstrap 3
            });
        });

        // שליחת הטופס ב-AJAX
        $(document).on('submit', '#add-task-form', function (e) {
            e.preventDefault();
            $.post('<?= base_url("tasks/add_ajax/{$project_id}") ?>', $(this).serialize(), function (response) {
                if (response.success) {
                    // מוסיפים את השורה החדשה ל-DataTable במקום append רגיל
                    tasksTable.row.add($(response.html)).draw(false);

                    // סוגרים את ה-Modal
                    $('#addTaskModal').modal('hide');
                } else {
                    $('#task-message').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            }, 'json');
        });

    });
</script>

<style>
    .badge {
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.8em;
    }

    .badge-success {
        background-color: #28a745;
        color: #fff;
    }

    .badge-danger {
        background-color: #dc3545;
        color: #fff;
    }

    .task-done {
        background-color: #e6ffe6;
    }

    /* ירוק בהיר לשורה סגורה */
    .task-late {
        background-color: #ffe6e6;
    }

    /* אדום בהיר לשורה מאוחרת */
</style>