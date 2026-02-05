<?php if (!$this->session->user_id): ?>
    <div class="alert alert-warning">
        OOPS - it seems like you are not logged in. Please log in
        <a href="<?= base_url('users/login') ?>">here</a>.
    </div>
<?php else: ?>

    <div class="projects-container">

        <!-- Projects list -->
        <div class="projects-list">
            <h2 class="title">My Projects</h2>

            <?php if ($this->session->flashdata('success')): ?>
                <div id="flash-message" class="alert alert-success">
                    <?= $this->session->flashdata('success') ?>
                </div>
            <?php endif; ?>

            <button id="show-add-form" class="btn btn-success mb-15">
                Add New Project
            </button>

            <div class="table-responsive">
                <table id="projects-table" class="display table table-striped w-100">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project): ?>
                            <tr id="project-<?= $project->project_id ?>">
                                <td><?= htmlspecialchars($project->project_title) ?></td>
                                <td><?= htmlspecialchars($project->project_body) ?></td>
                                <?php
                                $badge_class = 'badge-secondary';
                                if ($project->project_status == 'Open')
                                    $badge_class = 'badge-success';
                                elseif ($project->project_status == 'no missions')
                                    $badge_class = 'badge-info';
                                ?>
                                <td>
                                    <span class="badge <?= $badge_class ?>">
                                        <?= htmlspecialchars($project->project_status) ?>
                                        (<?= $project->task_count ?>)
                                    </span>
                                </td>
                                <td class="actions">
                                    <!-- Large screens: show all buttons -->
                                    <div class="actions-large d-none d-md-flex">
                                        <a class="btn btn-sm btn-primary edit-project" href="#"
                                            data-id="<?= $project->project_id ?>">Edit</a>
                                        <a class="btn btn-sm btn-danger"
                                            href="<?= base_url("projects/delete/{$project->project_id}") ?>"
                                            onclick="return confirm('Are you sure?')">Delete</a>
                                        <a class="btn btn-sm btn-info"
                                            href="<?= base_url("tasks/index/{$project->project_id}?from=projects") ?>">Tasks</a>
                                        <a class="btn btn-sm btn-warning share-project" href="#"
                                            data-id="<?= $project->project_id ?>">Share</a>
                                    </div>

                                    <!-- Small screens: dropdown with three dots -->
                                    <div class="dropdown d-md-none">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            id="dropdownMenuButton-<?= $project->project_id ?>" data-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <span class="three-dots">⋮</span>
                                        </button>
                                        <div class="dropdown-menu"
                                            aria-labelledby="dropdownMenuButton-<?= $project->project_id ?>">
                                            <a class="dropdown-item edit-project" href="#"
                                                data-id="<?= $project->project_id ?>">Edit</a>
                                            <a class="dropdown-item"
                                                href="<?= base_url("projects/delete/{$project->project_id}") ?>"
                                                onclick="return confirm('Are you sure?')">Delete</a>
                                            <a class="dropdown-item"
                                                href="<?= base_url("tasks/index/{$project->project_id}?from=projects") ?>">Tasks</a>
                                            <a class="dropdown-item share-project" href="#"
                                                data-id="<?= $project->project_id ?>">Share</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Desktop form container -->
        <div id="project-form-container" class="project-form"></div>

    </div>

    <!-- ADD / EDIT MODAL (mobile only usage) -->
    <div class="modal fade" id="projectFormModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="projectFormModalTitle"></h4>
                </div>

                <div class="modal-body" id="project-form-modal-body"></div>

            </div>
        </div>
    </div>

    <!-- SHARE MODAL -->
    <div class="modal fade" id="shareProjectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Share Project</h4>
                </div>

                <div class="modal-body" id="share-project-modal-body"></div>

            </div>
        </div>
    </div>

<?php endif; ?>


<!-- Projects Page Scripts -->
<script src="<?= base_url('assets/js/projects.js') ?>"></script>