<div class="admin-projects-container">
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success soft-alert">
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>

    <h2 class="title">All Projects</h2>

    <?php if (empty($projects)): ?>
        <div class="alert alert-info soft-alert">
            No projects found.
        </div>
    <?php else: ?>
        <div class="projects-card">
            <div class="table-wrapper">
                <table class="projects-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Owner</th>
                            <th>Created</th>
                            <th>Shared</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $p): ?>
                            <tr>
                                <td data-label="ID"><?= (int) $p->project_id ?></td>

                                <td data-label="Title">
                                    <strong class="project-title">
                                        <?= htmlspecialchars($p->project_title) ?>
                                    </strong>
                                </td>

                                <td data-label="Owner">
                                    <?= htmlspecialchars(
                                        $p->owner_name == $this->session->userdata('username')
                                        ? 'You'
                                        : ($p->owner_name ?? '-')
                                    ) ?>
                                </td>

                                <td data-label="Created">
                                    <?= date('d/m/Y', strtotime($p->created_at)) ?>
                                </td>

                                <td data-label="Shared with">
                                    <span class="shared-badge">
                                        <?= !empty($p->shared_users) ? count($p->shared_users) : 0 ?>
                                    </span>
                                </td>

                                <td data-label="Actions">
                                    <div class="action-group">
                                        <a href="<?= base_url('tasks/admin_view/' . $p->project_id) ?>"
                                            class="btn-action btn-view">
                                            View
                                        </a>

                                        <?php if ($this->session->userdata('project_permission') === 'edit'): ?>
                                            <a href="<?= base_url('projects/edit/' . $p->project_id) ?>" class="btn-action btn-edit"
                                                data-id="<?= (int) $p->project_id ?>">
                                                Edit
                                            </a>

                                            <button class="btn-action btn-share share-project"
                                                data-id="<?= (int) $p->project_id ?>">
                                                Share
                                            </button>

                                            <form method="post"
                                                action="<?= base_url('projects/delete/' . $p->project_id . '/admin') ?>"
                                                onsubmit="return confirmDelete();">
                                                <button type="submit" class="btn-action btn-delete">
                                                    Delete
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <div class="modal fade" id="shareProjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Share Project</h4>
                </div>

                <div class="modal-body" id="share-project-modal-body">
                    <!-- ajax content -->
                </div>

            </div>
        </div>
    </div>

    <div class="modal fade" id="projectFormModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="projectFormModalTitle">Edit Project</h4>
                </div>

                <div class="modal-body" id="project-form-modal-body">
                    <!-- ajax content -->
                </div>

            </div>
        </div>
    </div>


</div>

<script src="<?= base_url('assets/js/admin-projects.js') ?>"></script>