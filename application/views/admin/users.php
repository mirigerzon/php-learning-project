<div class="users-container">
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                Users Management
            </h1>
            <p class="page-subtitle">Manage user accounts, permissions, and access levels</p>
        </div>
    </div>

    <div id="flash-message"></div>

    <div class="users-card">
        <div class="table-container">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Projects</th>
                        <th>Admin</th>
                        <th>Permission</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td data-label="ID"><?= $u->user_id ?></td>

                            <td data-label="Username">
                                <div class="username-cell">
                                    <div class="user-avatar">
                                        <?= strtoupper(substr($u->username, 0, 2)) ?>
                                    </div>
                                    <?= htmlspecialchars($u->username) ?>
                                </div>
                            </td>

                            <td data-label="Full Name">
                                <?= $u->first_name ? htmlspecialchars($u->first_name . ' ' . $u->last_name) : '—' ?>
                            </td>

                            <td data-label="Projects">
                                <div class="projects-stats">
                                    <span class="stat-completed">✔ <?= (int) $u->completed_projects ?></span>
                                    <span class="stat-progress">⏳ <?= (int) $u->in_progress_projects ?></span>
                                </div>
                            </td>

                            <td data-label="Admin">
                                <?php if ($u->user_id == $this->session->userdata('user_id')): ?>
                                    <span class="badge-you">You</span>
                                <?php else: ?>
                                    <div class="action-buttons">
                                        <form method="post" action="<?= base_url('admin/toggle_admin') ?>">
                                            <input type="hidden" name="user_id" value="<?= $u->user_id ?>">
                                            <input type="hidden" name="is_admin" value="<?= $u->is_admin ? 0 : 1 ?>">
                                            <button class="btn-action <?= $u->is_admin ? 'btn-revoke' : 'btn-grant' ?>">
                                                <?= $u->is_admin ? 'Revoke' : 'Grant' ?>
                                            </button>
                                        </form>

                                        <form method="post" action="<?= base_url('admin/delete_user') ?>"
                                            onsubmit="return confirm('Delete this user?')">
                                            <input type="hidden" name="user_id" value="<?= $u->user_id ?>">
                                            <button class="btn-action btn-delete">Delete</button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td data-label="Permission">
                                <?php if ($u->is_admin): ?>
                                    <select class="permission-select project-permission" data-user-id="<?= $u->user_id ?>">
                                        <option value="view" <?= $u->project_permission === 'view' ? 'selected' : '' ?>>
                                            View Only
                                        </option>
                                        <option value="edit" <?= $u->project_permission === 'edit' ? 'selected' : '' ?>>
                                            Full Edit
                                        </option>
                                    </select>
                                <?php else: ?>
                                    <span class="badge-no-permission">No Access</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<style>
 * { box-sizing: border-box; }

.users-container {
    max-width: 1400px;
    margin: auto;
    padding: 1.5rem;
    background: #f8f9fa;
    min-height: 100vh;
    font-family: system-ui;
}

.header-content {
    background: white;
    padding: 1.5rem;
    border-radius: 14px;
    margin-bottom: 1.5rem;
}

.users-card {
    background: white;
    border-radius: 14px;
    overflow: hidden;
}

.users-table {
    width: 100%;
    border-collapse: collapse;
}

.users-table th,
.users-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.username-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #6366f1;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
}

.projects-stats {
    display: flex;
    gap: 0.75rem;
}

.stat-completed { color: #065f46; }
.stat-progress { color: #92400e; }

.action-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-action {
    padding: 0.45rem 0.8rem;
    border-radius: 8px;
    border: 1px solid;
    background: none;
    cursor: pointer;
}

.btn-grant { border-color: #93c5fd; color: #1e40af; }
.btn-revoke { border-color: #fde68a; color: #92400e; }
.btn-delete { border-color: #fecaca; color: #991b1b; }

.permission-select {
    width: 100%;
    padding: 0.45rem;
    border-radius: 8px;
}

.badge-you {
    background: #dbeafe;
    padding: 0.3rem 0.6rem;
    border-radius: 8px;
}

.badge-no-permission {
    color: #6b7280;
}

/* 📱 MOBILE VIEW */
@media (max-width: 640px) {

    .users-table thead {
        display: none;
    }

    .users-table,
    .users-table tbody,
    .users-table tr,
    .users-table td {
        display: block;
        width: 100%;
    }

    .users-table tr {
        background: white;
        border-radius: 14px;
        margin-bottom: 1rem;
        padding: 1rem;
        border: 1px solid #e5e7eb;
    }

    .users-table td {
        padding: 0.4rem 0;
        border: none;
    }

    .users-table td::before {
        content: attr(data-label);
        display: block;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        margin-bottom: 0.25rem;
        text-transform: uppercase;
    }

    .action-buttons {
        flex-direction: column;
    }

    .btn-action {
        width: 100%;
    }
}

</style>

<script>
    function confirmDelete(form) {
        return confirm('Are you sure you want to delete this user and all their projects/tasks? This action cannot be undone.');
    }

    $(document).ready(function () {
        $('.project-permission').change(function () {
            const $select = $(this);
            const userId = $select.data('user-id');
            const permission = $select.val();

            // Add loading state
            $select.addClass('loading');

            $.post('<?= base_url("admin/set_project_permission_ajax") ?>', {
                user_id: userId,
                permission: permission
            }, function (res) {
                $select.removeClass('loading');

                if (res.success) {
                    $('#flash-message').html(`
                        <div class="alert alert-success">
                            <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Permission updated successfully</span>
                        </div>
                    `);
                } else {
                    $('#flash-message').html(`
                        <div class="alert alert-danger">
                            <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>${res.message || 'Failed to update permission'}</span>
                        </div>
                    `);
                }

                // Auto-hide after 4 seconds
                setTimeout(() => {
                    const $alert = $('#flash-message .alert');
                    $alert.css({
                        'opacity': '0',
                        'transform': 'translateY(-10px)',
                        'transition': 'all 0.3s ease'
                    });
                    setTimeout(() => $('#flash-message').empty(), 300);
                }, 4000);
            }, 'json').fail(function () {
                $select.removeClass('loading');
                $('#flash-message').html(`
                    <div class="alert alert-danger">
                        <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span>Network error. Please try again.</span>
                    </div>
                `);
            });
        });

        // Auto-hide flash messages
        setTimeout(function () {
            const $flash = $('#flash-message .alert');
            if ($flash.length) {
                $flash.css({
                    'opacity': '0',
                    'transform': 'translateY(-10px)',
                    'transition': 'all 0.3s ease'
                });
                setTimeout(() => $('#flash-message').empty(), 300);
            }
        }, 4000);
    });
</script>