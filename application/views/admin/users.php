<div class="container mt-4">
    <h2>Users</h2>

    <div id="flash-message">
        <?php if ($this->session->flashdata('success')): ?>
            <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
        <?php endif; ?>
    </div>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
    <?php endif; ?>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Username</th>
                <th>Full name</th>
                <th>Completed projects</th>
                <th>In-progress projects</th>
                <th>Admin</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u->user_id ?></td>
                    <td><?= htmlspecialchars($u->username) ?></td>
                    <td><?= isset($u->first_name) ? htmlspecialchars($u->first_name . ' ' . $u->last_name) : '' ?></td>
                    <td><?= (int) $u->completed_projects ?></td>
                    <td><?= (int) $u->in_progress_projects ?></td>
                    <td>
                        <?php if ((int) $u->user_id === (int) $this->session->userdata('user_id')): ?>
                            <span class="text-muted">(You)</span>
                        <?php else: ?>
                            <form method="post" action="<?= base_url('admin/toggle_admin') ?>" style="display:inline">
                                <input type="hidden" name="user_id" value="<?= (int) $u->user_id ?>">
                                <input type="hidden" name="is_admin" value="<?= $u->is_admin ? 0 : 1 ?>">
                                <button type="submit" class="btn btn-sm <?= $u->is_admin ? 'btn-warning' : 'btn-primary' ?>">
                                    <?= $u->is_admin ? 'Revoke admin' : 'Make admin' ?>
                                </button>
                            </form>

                            <form method="post" action="<?= base_url('admin/delete_user') ?>"
                                style="display:inline;margin-left:6px;" onsubmit="return confirmDelete(this);">
                                <input type="hidden" name="user_id" value="<?= (int) $u->user_id ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    function confirmDelete(form) {
        if (confirm('Are you sure you want to delete this user and all their projects/tasks? This action cannot be undone.')) {
            return true;
        }
        return false;
    }
</script>


<script>
    setTimeout(function () {
        var flash = document.getElementById('flash-message');
        if (flash) {
            flash.style.display = 'none';
        }
    }, 4000);
</script>