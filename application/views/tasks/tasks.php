<?php if (!$this->session->user_id): ?>
    <div class="alert alert-warning">
        OOPS - it seems like you are not logged in. Please log in
        <a href="<?= base_url('users/login') ?>">here</a>.
    </div>
<?php else: ?>
    <?php
    $user_id = $this->session->user_id;
    $project_permission = $this->session->userdata('project_permission') ?? null;
    $project_owner_id = $project->user_id ?? null;
    $can_edit = $user_id == $project_owner_id || $project_permission === 'edit' || $project_permission === 'admin' || $project->user_role === 'editor' || $project->user_role === 'admin';
    $can_view = $can_edit || $project_permission === 'view';
    ?>
    <?php if ($can_view): ?>
        <script>
            window.projectId = <?= $project_id ?>;
            window.canEdit = <?= $can_edit ? 'true' : 'false' ?>;
        </script>

        <div class="tasks-container">
            <!-- Header Section -->
            <div class="tasks-header">
                <div class="header-top">
                    <?php
                    $from = $_GET['from'] ?? 'projects';
                    $back_url = ($from === 'shares') ? base_url('shares') : (($from === 'admin') ? base_url('admin') : base_url('projects'));
                    $back_text = ($from === 'shares') ? ' Shares' : (($from === 'admin') ? 'Back to Admin' : 'Back to Projects');
                    ?>
                    <a href="<?= $back_url ?>" class="btn-back">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path
                                d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                        </svg>
                        <?= $back_text ?>
                    </a>

                    <?php if ($can_edit): ?>
                        <button id="show-add-task-form" class="btn-primary">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path
                                    d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                            </svg>
                            Add Task
                        </button>
                    <?php endif; ?>
                </div>

                <h1 class="page-title"><?= htmlspecialchars($project->project_title) ?></h1>

                <!-- Filter Pills -->
                <div class="filter-pills">
                    <a href="<?= base_url("tasks/index/{$project_id}") ?>"
                        class="pill <?= !$status_filter ? 'active' : '' ?>">All</a>
                    <a href="<?= base_url("tasks/index/{$project_id}?status=pending") ?>"
                        class="pill pill-warning <?= $status_filter === 'pending' ? 'active' : '' ?>">Pending</a>
                    <a href="<?= base_url("tasks/index/{$project_id}?status=done") ?>"
                        class="pill pill-success <?= $status_filter === 'done' ? 'active' : '' ?>">Done</a>
                    <a href="<?= base_url("tasks/index/{$project_id}?status=late") ?>"
                        class="pill pill-danger <?= $status_filter === 'late' ? 'active' : '' ?>">Late</a>
                </div>
            </div>

            <!-- Tasks Content -->
            <?php if (empty($tasks)): ?>
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                        <path
                            d="M4.285 9.567a.5.5 0 0 1 .683.183A3.498 3.498 0 0 0 8 11.5a3.498 3.498 0 0 0 3.032-1.75.5.5 0 1 1 .866.5A4.498 4.498 0 0 1 8 12.5a4.498 4.498 0 0 1-3.898-2.25.5.5 0 0 1 .183-.683z" />
                    </svg>
                    <p>No tasks found for this project</p>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table id="tasks-table" class="modern-table">
                        <thead>
                            <tr>
                                <th>Task</th>
                                <th>Description</th>
                                <th>Created</th>
                                <th>Due Date</th>
                                <th class="text-center">Images</th>
                                <?php if ($can_edit || $project_permission === 'view'): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <script> console.log(<?= json_encode($project) ?>); console.log(<?= json_encode($tasks) ?>); </script>

                            <?php foreach ($tasks as $task):
                                // מצב משימה
                                if ($from === 'shares') {
                                    $is_done = (int) ($task->is_done ?? 0) === 1;
                                    $all_done = false; // משתמש רגיל לא רואה את כולם
                                } else {
                                    $is_done = (int) $task->status === 1;
                                    $all_done = isset($task->all_done) && $task->all_done;
                                }

                                $is_late = !$is_done && !empty($task->due_date) && $task->due_date < date('Y-m-d');
                                $row_class = $is_done || $all_done ? 'row-done' : ($is_late ? 'row-late' : '');
                                ?>
                                <tr id="task-<?= $task->task_id ?>" class="<?= $row_class ?>">
                                    <td>
                                        <div class="task-title">
                                            <h4><?= htmlspecialchars($task->task_title) ?></h4>
                                            <?php if ($from === 'shares'): ?>
                                                <?php if ($is_done): ?>
                                                    <span class="status-badge badge-success">✓ Done</span>
                                                <?php elseif ($is_late): ?>
                                                    <span class="status-badge badge-danger">⚠ Late</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php if ($all_done): ?>
                                                    <span class="status-badge badge-success">✓ All Done</span>
                                                <?php elseif ($is_late): ?>
                                                    <span class="status-badge badge-danger">⚠ Late</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="task-description"><?= nl2br(htmlspecialchars($task->task_body)) ?></div>
                                    </td>
                                    <td class="date-cell"><?= date('d/m/Y', strtotime($task->created_at)) ?></td>
                                    <td class="date-cell"><?= $task->due_date ? date('d/m/Y', strtotime($task->due_date)) : '—' ?></td>
                                    <td class="text-center"><?= $task->image_count ?></td>
                                    <?php if ($can_edit || $project_permission === 'view'): ?>
                                        <td class="actions-cell">
                                            <div class="action-btns desktop-actions">
                                                <?php if ($can_edit): ?>
                                                    <?php if (!$is_done && !$all_done): ?>
                                                        <a href="<?= $from === 'shares'
                                                            ? base_url("tasks/mark_as_done_for_user/{$task->task_id}?user_id={$user_id}")
                                                            : base_url("tasks/mark_as_done/{$task->project_id}/{$task->task_id}") ?>"
                                                            class="btn-action btn-success">Done</a>
                                                    <?php else: ?>
                                                        <a href="<?= $from === 'shares'
                                                            ? base_url("tasks/mark_as_undone_for_user/{$task->task_id}?user_id={$user_id}")
                                                            : base_url("tasks/mark_as_un_done/{$task->project_id}/{$task->task_id}") ?>"
                                                            class="btn-action btn-secondary">Undo</a>
                                                    <?php endif; ?>
                                                    <a href="<?= base_url("tasks/delete/{$task->project_id}/{$task->task_id}") ?>"
                                                        class="btn-action btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                                <?php endif; ?>
                                                <a href="<?= base_url("tasks/view/{$task->project_id}/{$task->task_id}?from={$from}") ?>"
                                                    class="btn-action btn-info">View</a>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addTaskModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add New Task</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body" id="add-task-container"></div>
                </div>
            </div>
        </div>

        <script src="<?= base_url('assets/js/tasks.js') ?>"></script>
        <script>
            $(document).ready(function () {
                const urlParams = new URLSearchParams(window.location.search);
                const fromPage = urlParams.get('from');
                const btn = $('#show-add-task-form');

                if (!btn.length) return;

                if (fromPage === 'projects') {
                    btn.show();
                    btn.html(`<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                              </svg> Add Task to Project`);
                } else if (fromPage === 'shares') {
                    btn.hide();
                } else if (fromPage === 'admin') {
                    btn.show();
                    btn.html(`<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                              </svg> Admin Add Task`);
                }
            });
        </script>

    <?php else: ?>
        <div class="alert alert-danger text-center alert-large">
            <h3>You do not have permission to view this project.</h3>
        </div>
    <?php endif; ?>
<?php endif; ?>