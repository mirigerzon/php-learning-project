<?php if (!$this->session->user_id): ?>
    <h2 class="un_loged_in_message text-center">
        OOPS - it seems like you are not logged in.
        Please log in <a href="<?= base_url('users/login') ?>">here</a>.
    </h2>
<?php else: ?>

    <div class="projects-container">

        <!-- Projects list -->
        <div class="projects-list">
            <h2>My Projects</h2>

            <?php if ($this->session->flashdata('success')): ?>
                <div id="flash-message" class="alert alert-success">
                    <?= $this->session->flashdata('success') ?>
                </div>
            <?php endif; ?>

            <button id="show-add-form" class="btn btn-success mb-15">
                Add New Project
            </button>

            <div class="table-responsive">
                <table id="projects-table" class="display table table-striped" style="width:100%">
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
                                <td>
                                    <span
                                        class="badge <?= $project->project_status == 'Open' ? 'badge-success' : 'badge-secondary' ?>">
                                        <?= $project->project_status ?>
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
                                            href="<?= base_url("tasks/index/{$project->project_id}") ?>">Tasks</a>
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
                                                href="<?= base_url("tasks/index/{$project->project_id}") ?>">Tasks</a>
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

<!-- JS -->
<script>
    $(document).ready(function () {

        setTimeout(() => $('#flash-message').fadeOut('slow'), 4000);

        var projectsTable = $('#projects-table').DataTable({
            responsive: true,
            order: [[0, 'asc']],
            paging: true,
            pageLength: 10
        });

        function isMobile() {
            return window.innerWidth <= 768;
        }

        function openProjectForm(html, title) {
            if (isMobile()) {
                $('#projectFormModalTitle').text(title);
                $('#project-form-modal-body').html(html);
                $('#projectFormModal').modal('show');
            } else {
                $('#project-form-container').html(html);
            }
        }

        // ADD
        $('#show-add-form').on('click', function () {
            $.get('<?= base_url("projects/add_ajax_form") ?>', function (html) {
                openProjectForm(html, 'Add New Project');
            });
        });

        $(document).on('submit', '#add-project-form', function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?= base_url("projects/add_ajax") ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        let row = `
<tr id="project-${response.project_id}">
    <td>${$('#project_title').val()}</td>
    <td>${$('#project_body').val()}</td>
    <td><span class="badge badge-success">Open</span></td>
    <td class="actions">
        <!-- Large screens: show all buttons -->
        <div class="actions-large d-none d-md-flex">
            <a class="btn btn-sm btn-primary edit-project" href="#" data-id="${response.project_id}">Edit</a>
            <a class="btn btn-sm btn-danger" href="<?= base_url("projects/delete/") ?>${response.project_id}" onclick="return confirm('Are you sure?')">Delete</a>
            <a class="btn btn-sm btn-info" href="<?= base_url("tasks/index/") ?>${response.project_id}">Tasks</a>
            <a class="btn btn-sm btn-warning share-project" href="#" data-id="${response.project_id}">Share</a>
        </div>

        <!-- Small screens: dropdown with three dots -->
        <div class="dropdown d-md-none">
            <button class="btn btn-secondary dropdown-toggle" type="button"
                id="dropdownMenuButton-${response.project_id}" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="three-dots">⋮</span>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton-${response.project_id}">
                <a class="dropdown-item edit-project" href="#" data-id="${response.project_id}">Edit</a>
                <a class="dropdown-item" href="<?= base_url("projects/delete/") ?>${response.project_id}" onclick="return confirm('Are you sure?')">Delete</a>
                <a class="dropdown-item" href="<?= base_url("tasks/index/") ?>${response.project_id}">Tasks</a>
                <a class="dropdown-item share-project" href="#" data-id="${response.project_id}">Share</a>
            </div>
        </div>
    </td>
</tr>`;

                        projectsTable.row.add($(row)).draw(false);

                        isMobile()
                            ? $('#projectFormModal').modal('hide')
                            : $('#project-form-container').html('');
                    } else {
                        alert(response.message);
                    }
                }
            });
        });

        // EDIT
        $(document).on('click', '.edit-project', function (e) {
            e.preventDefault();
            let id = $(this).data('id');
            $.get('<?= base_url("projects/edit_ajax_form/") ?>' + id, function (html) {
                openProjectForm(html, 'Edit Project');
            });
        });

        $(document).on('submit', '#edit-project-form', function (e) {
            e.preventDefault();
            let id = $(this).data('id');
            $.ajax({
                url: '<?= base_url("projects/edit_ajax/") ?>' + id,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        let row = `
<tr id="project-${response.project_id}">
    <td>${$('#project_title').val()}</td>
    <td>${$('#project_body').val()}</td>
    <td><span class="badge badge-success">Open</span></td>
    <td class="actions">
        <!-- Large screens: show all buttons -->
        <div class="actions-large d-none d-md-flex">
            <a class="btn btn-sm btn-primary edit-project" href="#" data-id="${response.project_id}">Edit</a>
            <a class="btn btn-sm btn-danger" href="<?= base_url("projects/delete/") ?>${response.project_id}" onclick="return confirm('Are you sure?')">Delete</a>
            <a class="btn btn-sm btn-info" href="<?= base_url("tasks/index/") ?>${response.project_id}">Tasks</a>
            <a class="btn btn-sm btn-warning share-project" href="#" data-id="${response.project_id}">Share</a>
        </div>

        <!-- Small screens: dropdown with three dots -->
        <div class="dropdown d-md-none">
            <button class="btn btn-secondary dropdown-toggle" type="button"
                id="dropdownMenuButton-${response.project_id}" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="three-dots">⋮</span>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton-${response.project_id}">
                <a class="dropdown-item edit-project" href="#" data-id="${response.project_id}">Edit</a>
                <a class="dropdown-item" href="<?= base_url("projects/delete/") ?>${response.project_id}" onclick="return confirm('Are you sure?')">Delete</a>
                <a class="dropdown-item" href="<?= base_url("tasks/index/") ?>${response.project_id}">Tasks</a>
                <a class="dropdown-item share-project" href="#" data-id="${response.project_id}">Share</a>
            </div>
        </div>
    </td>
</tr>`;
                        projectsTable.row($('#project-' + id)).data($(row)).draw(false);

                        isMobile()
                            ? $('#projectFormModal').modal('hide')
                            : $('#project-form-container').html('');
                    } else {
                        alert(response.message);
                    }
                }
            });
        });

        // CANCEL
        $(document).on('click', '#cancel-add-project, #cancel-edit-project', function () {
            isMobile()
                ? $('#projectFormModal').modal('hide')
                : $('#project-form-container').html('');
        });

        // SHARE
        $(document).on('click', '.share-project', function (e) {
            e.preventDefault();
            let id = $(this).data('id');
            $.get('<?= base_url("projects/share_ajax_form/") ?>' + id, function (html) {
                $('#share-project-modal-body').html(html);
                $('#shareProjectModal').modal('show');
            });
        });

        // SUBMIT SHARE FORM
        $(document).on('submit', '#share-project-form', function (e) {
            e.preventDefault();

            let form = $(this);

            $.ajax({
                url: '<?= base_url("projects/share_ajax") ?>',
                type: 'POST',
                data: form.serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('#shareProjectModal').modal('hide');
                        alert('Project shared successfully');
                    } else {
                        alert(response.message || 'Something went wrong');
                    }
                },
                error: function () {
                    alert('Server error');
                }
            });
        });

    });
</script>

<!-- CSS -->
<style>
    .projects-container {
        display: flex;
        gap: 20px;
    }

    .projects-list {
        flex: 2;
    }

    .dropdown {
        display: none;
    }

    .project-form {
        flex: 1;
        border-left: 1px solid #ccc;
        padding-left: 20px;
    }

    .actions a {
        display: inline-block;
        margin-right: 6px;
    }

    .three-dots {
        font-size: 18px;
        line-height: 1;
    }

    .badge {
        padding: 4px 8px;
        font-size: 12px;
        border-radius: 4px;
    }

    .badge-success {
        background: #28a745;
        color: #fff;
    }

    .badge-secondary {
        background: #6c757d;
        color: #fff;
    }

    .three-dots {
        display: none;
        /* מוסתר במסכים גדולים */
        font-size: 18px;
        line-height: 1;
    }

    @media (max-width: 768px) {
        .projects-container {
            flex-direction: column;
        }

        .project-form {
            display: none;
        }

        .actions a {
            display: block;
            margin-bottom: 5px;
        }

        .three-dots {
            display: inline-block;
        }

        .dropdown {
            display: inline;
        }
    }

    .actions a.btn {
        margin-right: 6px;
        font-size: 13px;
    }

    .three-dots {
        font-size: 18px;
        line-height: 1;
    }

    .actions-large a.btn {
        margin-right: 5px;
    }

    /* רקע קל לפעולות במסך גדול */
    .actions-large a.btn {
        background-color: #f8f9fa;
        color: #333;
    }

    .actions-large a.btn:hover {
        background-color: #e2e6ea;
    }

    /* Media query */
    @media (max-width: 768px) {
        .actions-large {
            display: none !important;
        }

        .actions a.btn {
            display: block;
            margin-bottom: 5px;
            width: 100%;
        }
    }
</style>