<div class="container mt-4">
    <?php if (!$this->session->user_id): ?>
        <div class="alert alert-warning">
            OOPS - it seems like you are not logged in. Please log in
            <a href="<?= base_url('users/login') ?>">here</a>.
        </div>
    <?php else: ?>
        <h2 class="title">Projects Shared With Me</h2>

        <div id="flash-message">
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger"><?= $this->session->flashdata('error') ?></div>
            <?php endif; ?>
        </div>

        <?php if (empty($shared_projects)): ?>
            <div class="alert alert-info">No projects have been shared with you yet.</div>
        <?php else: ?>
            <div class="projects-container">
                <!-- טבלת Shared Projects -->
                <div class="shared-projects-list">
                    <div class="table-responsive">
                        <table id="shared-projects-table" class="display table table-striped table-bordered w-100">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Project Name</th>
                                    <th>My Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($shared_projects as $index => $project): ?>
                                    <?php $role_lower = strtolower($project['user_role']); ?>
                                    <?php $badge_class = ($role_lower === 'editor' || $role_lower === 'admin') ? 'badge bg-success' : 'badge bg-secondary'; ?>
                                    <tr id="shared-project-<?= $project['project_id'] ?>">
                                        <td><?= $index + 1 ?></td>
                                        <td><?= htmlspecialchars($project['project_title']) ?></td>
                                        <td><span class="<?= $badge_class ?>"><?= ucfirst($role_lower) ?></span></td>
                                        <td>
                                            <?php if ($role_lower === 'editor' || $role_lower === 'admin'): ?>
                                                <a href="#" class="edit-project" data-id="<?= $project['project_id'] ?>">Edit</a> |
                                            <?php endif; ?>
                                            <?php if ($role_lower === 'admin'): ?>
                                                <a href="<?= base_url("projects/delete/{$project['project_id']}") ?>"
                                                    onclick="return confirm('Are you sure?')">Delete</a> |
                                            <?php endif; ?>
                                            <?php if ($role_lower === 'editor' || $role_lower === 'admin'): ?>
                                                <a href="<?= base_url("tasks/index/{$project['project_id']}?from=shares") ?>">Tasks</a>
                                                |
                                            <?php endif; ?>
                                            <?php if ($role_lower === 'admin'): ?>
                                                <a href="#" class="share-project" data-id="<?= $project['project_id'] ?>">Share</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="shared-project-form-container" class="shared-project-form">
                    </div>
                </div>
            <?php endif; ?>

            <!-- SHARE PROJECT MODAL -->
            <div class="modal fade" id="shareProjectModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Share Project</h4>
                        </div>
                        <div class="modal-body" id="share-project-modal-body">
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>
    <!-- Shares Page Scripts -->
    <script src="<?= base_url('assets/js/shares.js') ?>"></script>