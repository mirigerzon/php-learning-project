<form id="share-project-form">
    <input type="hidden" name="project_id" value="<?= $project_id ?>">

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>User</th>
                <th>Current Role</th>
                <th>Edit Role</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user->username) ?></td>
                    <td><?= $user->role ? ucfirst($user->role) : '-' ?></td>
                    <td>
                        <?php if ($user->user_id == $this->session->user_id): ?>
                            <em>N/A (You)</em>
                        <?php else: ?>
                            <select name="roles[<?= $user->user_id ?>]" class="form-control">
                                <option value="">-</option>
                                <option value="viewer" <?= $user->role === 'viewer' ? 'selected' : '' ?>>View</option>
                                <option value="editor" <?= $user->role === 'editor' ? 'selected' : '' ?>>Edit</option>
                                <option value="admin" <?= $user->role === 'admin' ? 'selected' : '' ?>>Admin</option>
                            </select>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($user->user_id != $this->session->user_id && ($user->role === 'editor' || $user->role === 'admin')): ?>
                            <button type="button" class="btn btn-sm btn-secondary assign-tasks-btn"
                                data-user-id="<?= $user->user_id ?>" data-project-id="<?= $project_id ?>">
                                Assign Tasks
                            </button>
                        <?php endif; ?>
                    </td>


                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>

<div class="modal fade" id="assignTasksModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form id="assign-tasks-form">
            <input type="hidden" name="user_id" id="modal-user-id" value="">
            <input type="hidden" name="project_id" value="<?= $project_id ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Tasks</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body" id="tasks-checkboxes">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    var PROJECT_ID = <?= json_encode($project_id) ?>;
</script>
<script src="<?= base_url('assets/js/assign_tasks.js') ?>"></script>