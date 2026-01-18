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
                        <select name="roles[<?= $user->user_id ?>]" class="form-control">
                            <option value="">-</option>
                            <option value="viewer" <?= $user->role === 'viewer' ? 'selected' : '' ?>>View</option>
                            <option value="editor" <?= $user->role === 'editor' ? 'selected' : '' ?>>Edit</option>
                            <option value="admin" <?= $user->role === 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <button type="submit" class="btn btn-primary">Save Changes</button>
</form>