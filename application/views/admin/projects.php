<div class="container mt-4">
    <h2>All Projects</h2>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success">
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (empty($projects)): ?>
        <div class="alert alert-info">
            No projects found.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Owner</th>
                        <th>Created</th>
                        <th>Shared with</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $p): ?>
                        <tr>
                            <td>
                                <?= (int) $p->project_id ?>
                            </td>

                            <td>
                                <strong>
                                    <?= htmlspecialchars($p->project_title) ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars($p->owner_name == $this->session->userdata('username') ? 'you' : $p->owner_name ?? '-') ?>
                            </td>

                            <td>
                                <?= date('d/m/Y', strtotime($p->created_at)) ?>
                            </td>

                            <td>
                                <?= !empty($p->shared_users)
                                    ? count($p->shared_users)
                                    : 0 ?>
                            </td>

                            <td>
                                <!-- VIEW -->
                                <a href="<?= base_url('tasks/index/' . $p->project_id) ?>"
                                    class="btn btn-sm btn-outline-secondary">
                                    View
                                </a>

                                <?php if ($this->session->userdata('project_permission') === 'edit'): ?>

                                    <a href="<?= base_url('projects/edit/' . $p->project_id) ?>" class="btn btn-sm btn-primary">
                                        Edit
                                    </a>

                                    <button class="btn btn-sm btn-warning share-project" data-id="<?= (int) $p->project_id ?>">
                                        Share
                                    </button>

                                    <form method="post" action="<?= base_url('projects/delete/' . $p->project_id) ?>"
                                        style="display:inline" onsubmit="return confirmDelete();">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
                                    </form>

                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
    function confirmDelete() {
        return confirm('Are you sure you want to delete this project? This action cannot be undone.');
    }
</script>