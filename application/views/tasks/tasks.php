<?php if (!$this->session->user_id): ?>
    <div class="alert alert-danger text-center" style="margin: 40px auto; max-width: 600px;">
        <h4>OOPS - it seems like you are not logged in.</h4>
        <p>Please log in <a href="<?= base_url('users/login') ?>">here</a>.</p>
    </div>
<?php else: ?>
    <?php
    $user_id = $this->session->user_id;
    $project_permission = $this->session->userdata('project_permission') ?? null;
    $project_owner_id = $project->user_id ?? null;
    $can_edit = $user_id == $project_owner_id || $project_permission === 'edit';
    $can_view = $can_edit || $project_permission === 'view';
    ?>
    <?php if ($can_view): ?>
        <div class="tasks-container">
            <!-- Header Section -->
            <div class="tasks-header">
                <div class="header-top">
                    <a href="<?= base_url('projects') ?>" class="btn-clean">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <path
                                d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
                        </svg>
                        Back to Projects
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
                            <?php foreach ($tasks as $task):
                                $is_done = (int) $task->status === 1;
                                $is_late = !$is_done && !empty($task->due_date) && $task->due_date < date('Y-m-d');
                                $row_class = $is_done ? 'row-done' : ($is_late ? 'row-late' : '');
                                ?>
                                <tr id="task-<?= $task->task_id ?>" class="<?= $row_class ?>">
                                    <td>
                                        <div class="task-title">
                                            <?= htmlspecialchars($task->task_title) ?>
                                            <?php if ($is_done): ?>
                                                <span class="status-badge badge-success">✓ Done</span>
                                            <?php elseif ($is_late): ?>
                                                <span class="status-badge badge-danger">⚠ Late</span>
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
                                            <!-- Desktop Actions -->
                                            <div class="action-btns desktop-actions">
                                                <?php if ($can_edit): ?>
                                                    <?php if (!$is_done): ?>
                                                        <a href="<?= base_url("tasks/mark_as_done/{$task->project_id}/{$task->task_id}") ?>"
                                                            class="btn-action btn-success">Done</a>
                                                    <?php else: ?>
                                                        <a href="<?= base_url("tasks/mark_as_un_done/{$task->project_id}/{$task->task_id}") ?>"
                                                            class="btn-action btn-secondary">Undo</a>
                                                    <?php endif; ?>
                                                    <a href="<?= base_url("tasks/delete/{$task->project_id}/{$task->task_id}") ?>"
                                                        class="btn-action btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                                <?php endif; ?>
                                                <a href="<?= base_url("tasks/view/{$task->project_id}/{$task->task_id}") ?>"
                                                    class="btn-action btn-info">View</a>
                                            </div>

                                            <!-- Mobile Actions -->
                                            <div class="mobile-actions">
                                                <button class="btn-menu" onclick="toggleMenu(this)">⋮</button>
                                                <div class="actions-dropdown">
                                                    <?php if ($can_edit): ?>
                                                        <?php if (!$is_done): ?>
                                                            <a href="<?= base_url("tasks/mark_as_done/{$task->project_id}/{$task->task_id}") ?>"
                                                                class="dropdown-item item-success">Done</a>
                                                        <?php else: ?>
                                                            <a href="<?= base_url("tasks/mark_as_un_done/{$task->project_id}/{$task->task_id}") ?>"
                                                                class="dropdown-item item-secondary">Undo</a>
                                                        <?php endif; ?>
                                                        <a href="<?= base_url("tasks/delete/{$task->project_id}/{$task->task_id}") ?>"
                                                            class="dropdown-item item-danger"
                                                            onclick="return confirm('Are you sure?')">Delete</a>
                                                    <?php endif; ?>
                                                    <a href="<?= base_url("tasks/view/{$task->project_id}/{$task->task_id}") ?>"
                                                        class="dropdown-item item-info">View</a>
                                                </div>
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
    <?php else: ?>
        <div class="alert alert-danger text-center" style="margin: 40px auto; max-width: 600px;">
            <h3>You do not have permission to view this project.</h3>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Styles -->
<style>
    * {
        box-sizing: border-box;
    }

    .tasks-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 24px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
    }

    .tasks-header {
        margin-bottom: 32px;
    }

    .header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        gap: 16px;
    }

    .btn-clean,
    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 500;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-clean {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .btn-clean:hover {
        background: #f1f5f9;
        color: #1e293b;
        text-decoration: none;
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    .page-title {
        font-size: 28px;
        font-weight: 600;
        color: #0f172a;
        margin: 0 0 24px 0;
        line-height: 1.3;
    }

    /* Pills */
    .filter-pills {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .pill {
        padding: 8px 16px;
        font-size: 14px;
        font-weight: 500;
        border-radius: 20px;
        background: #f8fafc;
        color: #64748b;
        text-decoration: none;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }

    .pill:hover {
        background: #f1f5f9;
        color: #475569;
    }

    .pill.active {
        background: #0f172a;
        color: white;
        border-color: #0f172a;
    }

    .pill-warning.active {
        background: #f59e0b;
        border-color: #f59e0b;
    }

    .pill-success.active {
        background: #10b981;
        border-color: #10b981;
    }

    .pill-danger.active {
        background: #ef4444;
        border-color: #ef4444;
    }

    /* Table */
    .table-wrapper {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
        font-size: 14px;
    }

    .modern-table thead {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .modern-table th {
        padding: 16px;
        text-align: left;
        font-weight: 600;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 13px;
    }

    .modern-table td {
        padding: 16px;
        border-top: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .modern-table tbody tr:hover {
        background: #fafbfc;
    }

    .row-done {
        background: #f0fdf4;
    }

    .row-late {
        background: #fef2f2;
    }

    .task-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 500;
        color: #0f172a;
        font-size: 14px;
    }

    .task-description {
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
        max-width: 400px;
    }

    .date-cell {
        color: #64748b;
        font-size: 13px;
        white-space: nowrap;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 500;
        border-radius: 12px;
        white-space: nowrap;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    /* Action Buttons */
    .action-btns {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 500;
        border-radius: 6px;
        text-decoration: none;
        border: 1px solid;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .btn-success {
        background: #f0fdf4;
        color: #166534;
        border-color: #bbf7d0;
    }

    .btn-success:hover {
        background: #dcfce7;
        text-decoration: none;
    }

    .btn-danger {
        background: #fef2f2;
        color: #991b1b;
        border-color: #fecaca;
    }

    .btn-danger:hover {
        background: #fee2e2;
        text-decoration: none;
    }

    .btn-info {
        background: #eff6ff;
        color: #1e40af;
        border-color: #bfdbfe;
    }

    .btn-info:hover {
        background: #dbeafe;
        text-decoration: none;
    }

    .btn-secondary {
        background: #f8fafc;
        color: #475569;
        border-color: #e2e8f0;
    }

    .btn-secondary:hover {
        background: #f1f5f9;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 64px 24px;
        color: #94a3b8;
    }

    .empty-state svg {
        margin-bottom: 16px;
        opacity: 0.5;
    }

    /* Mobile Actions */
    .mobile-actions {
        display: none;
        position: relative;
    }

    .btn-menu {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 16px;
        cursor: pointer;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .actions-dropdown {
        display: none;
        position: absolute;
        top: 36px;
        right: 0;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        z-index: 100;
        min-width: 140px;
        flex-direction: column;
    }

    .actions-dropdown a {
        padding: 8px 12px;
        display: block;
        text-decoration: none;
        color: #475569;
        font-size: 13px;
    }

    .actions-dropdown a:hover {
        background: #f1f5f9;
    }

    /* Media Queries */
    @media (max-width:768px) {
        .tasks-container {
            padding: 16px;
        }

        .header-top {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-clean,
        .btn-primary {
            justify-content: center;
            width: 100%;
        }

        .page-title {
            font-size: 22px;
            margin-bottom: 16px;
        }

        .task-description {
            max-width: 200px;
            font-size: 13px;
        }

        .action-btns {
            display: none;
        }

        .mobile-actions {
            display: flex;
        }

        .btn-action {
            width: 100%;
            text-align: center;
        }

        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }

    @media (max-width:480px) {
        .page-title {
            font-size: 20px;
        }

        .task-description {
            max-width: 150px;
        }

        .table-wrapper {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
    }
</style>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        var tasksTable = $('#tasks-table').DataTable({
            responsive: true,
            order: [[0, 'asc']],
            pageLength: 10
        });

        $('#show-add-task-form').on('click', function () {
            $.get('<?= base_url("tasks/add_ajax_form/{$project_id}") ?>', function (html) {
                $('#add-task-container').html(html);
                $('#addTaskModal').modal('show');
            });
        });

        $(document).on('submit', '#add-task-form', function (e) {
            e.preventDefault();
            $.post('<?= base_url("tasks/add_ajax/{$project_id}") ?>', $(this).serialize(), function (response) {
                if (response.success) {
                    const t = response.task;
                    const is_done = t.status === 1;
                    const is_late = !is_done && t.due_date && new Date(t.due_date) < new Date();
                    const row_class = is_done ? 'row-done' : (is_late ? 'row-late' : '');

                    // כאן בונים את הקישורים לפי ההרשאות
                    let actionLinks = '';
                            <?php if ($can_edit): ?>
                        if (!is_done) {
                            actionLinks += `<a href="<?= base_url("tasks/mark_as_done/{$project_id}/") ?>${t.task_id}" class="btn-action btn-success">Done</a>`;
                        } else {
                            actionLinks += `<a href="<?= base_url("tasks/mark_as_un_done/{$project_id}/") ?>${t.task_id}" class="btn-action btn-secondary">Undo</a>`;
                        }
                        actionLinks += `<a href="<?= base_url("tasks/delete/{$project_id}/") ?>${t.task_id}" class="btn-action btn-danger" onclick="return confirm('Are you sure?')">Delete</a>`;
                            <?php endif; ?>
                    actionLinks += `<a href="<?= base_url("tasks/view/{$project_id}/") ?>${t.task_id}" class="btn-action btn-info">View</a>`;

                    const rowNode = tasksTable.row.add([
                        `<div class="task-title">${t.task_title}${is_done ? '<span class="status-badge badge-success">✓ Done</span>' : (is_late ? '<span class="status-badge badge-danger">⚠ Late</span>' : '')}</div>`,
                        `<div class="task-description">${t.task_body.replace(/\n/g, '<br>')}</div>`,
                        new Date(t.created_at).toLocaleDateString(),
                        t.due_date ? new Date(t.due_date).toLocaleDateString() : '—',
                        '0', // image count
                        actionLinks
                    ]).draw(false).node();

                    $(rowNode).addClass(row_class);
                    $('#addTaskModal').modal('hide');
                } else {
                    $('#task-message').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            }, 'json');
        });
    });

    // Mobile dropdown toggle
    function toggleMenu(button) {
        const dropdown = button.nextElementSibling;
        // סוגר את כל שאר ה-dropdowns
        document.querySelectorAll('.actions-dropdown').forEach(d => {
            if (d !== dropdown) d.style.display = 'none';
        });
        // פותח/סוגר את ה-dropdown הנוכחי
        dropdown.style.display = (dropdown.style.display === 'flex' ? 'none' : 'flex');
    }

    // סוגר את כל ה-dropdowns אם לוחצים מחוץ להם
    document.addEventListener('click', function (e) {
        if (!e.target.closest('.mobile-actions')) {
            document.querySelectorAll('.actions-dropdown').forEach(d => d.style.display = 'none');
        }
    });
</script>