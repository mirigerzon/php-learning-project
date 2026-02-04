<div class="users-container">
    <div id="flash-message"></div>
    <h2 class="title"> Users Management</h2>

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

<!-- Users Admin Page Scripts -->
<script src="<?= base_url('assets/js/admin-users.js') ?>"></script>
<style>
    .users-container {
        height: 5vh;
    }

    .alert-icon path {
        stroke-width: 3;
    }

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

    .btn-grant {
        border-color: #93c5fd;
        color: #1e40af;
    }

    .btn-revoke {
        border-color: #fde68a;
        color: #92400e;
    }

    .btn-delete {
        border-color: #fecaca;
        color: #991b1b;
    }

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