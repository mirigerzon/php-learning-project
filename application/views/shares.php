<div class="container mt-4">
    <?php if (!$this->session->user_id): ?>
        <div class="alert alert-warning">
            OOPS - it seems like you are not logged in. Please log in
            <a href="<?= base_url('users/login') ?>">here</a>.
        </div>
    <?php else: ?>
        <h2>Projects Shared With Me</h2>

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
            <div class="projects-container" style="display: flex; gap: 20px;">
                <!-- טבלת Shared Projects -->
                <div style="flex: 2;">
                    <div class="table-responsive">
                        <table id="shared-projects-table" class="display table table-striped table-bordered" style="width:100%">
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
                                    <?php $role_lower = strtolower($project['role']); ?>
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
                                                <a href="<?= base_url("tasks/index/{$project['project_id']}") ?>">View Tasks</a> |
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
                    <div id="shared-project-form-container" style="flex: 1; border-left: 1px solid #ccc; padding-left: 20px;">
                        <!-- AJAX form יוזרק לכאן -->
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
                            <!-- AJAX form יוזרק לכאן -->
                        </div>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <!-- JS Libraries -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function () {

            // Flash message hide
            setTimeout(() => $('#flash-message').fadeOut('slow'), 4000);

            // אתחול DataTable
            var sharedTable = $('#shared-projects-table').DataTable({
                "responsive": true,
                "order": [[0, 'asc']],
                "paging": true,
                "pageLength": 10
            });

            // ------------------ EDIT SHARED PROJECT ------------------
            $(document).on('click', '.edit-project', function (e) {
                e.preventDefault();
                let id = $(this).data('id');
                $.get('<?= base_url("projects/edit_ajax_form/") ?>' + id, function (html) {
                    $('#shared-project-form-container').html(html);
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
                    succ  ess: function (response) {
                        if (response.success) {
                            let updatedRow = `
                        <td>${response.project_title}</td>
                        <td><span class="badge bg-success">${response.role}</span></td>
                        <td>
                            ${response.role === 'admin' || response.role === 'editor' ? `<a href="#" class="edit-project" data-id="${response.project_id}">Edit</a> |` : ''}
                            ${response.role === 'admin' ? `<a href="<?= base_url("projects/delete/") ?>${response.project_id}" onclick="return confirm('Are you sure?')">Delete</a> |` : ''}
                            ${response.role === 'editor' || response.role === 'admin' ? `<a href="<?= base_url("tasks/index/") ?>${response.project_id}">View Tasks</a> |` : ''}
                            ${response.role === 'admin' ? `<a href="#" class="share-project" data-id="${response.project_id}">Share</a>` : ''}
                        </td>
                      `;
                            sharedTable.row($('#shared-project-' + response.project_id)).data($(updatedRow)).draw(false);
                            $('#shared-project-form-container').html('');
                        } el  se {
                            alert(response.message);
                        }
                    }
                });
            });

            $(document).on('click', '#cancel-edit-project', function () {
                $('#shared-project-form-container').html('');
            });

            // ------------------ SHARE PROJECT ------------------
            $(do cument).on('click', '.share-project', function (e) {
                e.preventDefault();
                let projectId = $(this).data('id');
                $.ge  t('<?= base_url("projects/share_ajax_form/") ?>' + projectId, function (html) {
                    $('#share-project-modal-body').html(html);
                    $('#shareProjectModal').modal('show');
                });
            });

            $(do   cument).on('submit', '#share-project-form', function (e) {
                e.preventDefault();
                $.aj    ax({
                    url: '<?= base_url("projects/share_ajax") ?>',
                    type: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    succ    ess: function (response) {
                        if (response.success) {
                            alert('Project shared successfully');
                            $('#shareProjectModal').modal('hide');
                        } el    se {
                            alert(response.message);
                        }
                    }
                });
            });

            $(do    cument).on('click', '#cancel-edit-project, #cancel-add-project', function () {
                $('#project-form-container').html('');
            });
        });
    </script>

    <style>
        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.8em;
        }

        .bg-success {
            background-color: #28a745;
            color: #fff;
        }

        .bg-secondary {
            background-color: #6c757d;
            color: #fff;
        }
    </style>