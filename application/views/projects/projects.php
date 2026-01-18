<?php if (!$this->session->user_id): ?>
    <h2 class="un_loged_in_message">
        OOPS - it seems like you are not logged in. Please log in
        <a href="<?= base_url('users/login') ?>">here</a>.
    </h2>
<?php else: ?>
    <div style="display: flex; gap: 20px;">
        <!-- עמודת רשימת הפרויקטים (שמאל) -->
        <div style="flex: 2;">
            <div class="title">
                <h2>My Projects</h2>
            </div>

            <?php if ($this->session->flashdata('success')): ?>
                <div id="flash-message" class="alert alert-success">
                    <?= $this->session->flashdata('success') ?>
                </div>
            <?php endif; ?>

            <div style="margin-bottom: 15px;">
                <button id="show-add-form" class="btn btn-success">Add New Project</button>
            </div>

            <table id="projects-table" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($projects)): ?>
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
                                <td>
                                    <a href="#" class="edit-project" data-id="<?= $project->project_id ?>">Edit</a> |
                                    <a href="<?= base_url("projects/delete/{$project->project_id}") ?>"
                                        onclick="return confirm('Are you sure?')">Delete</a> |
                                    <a href="<?= base_url("tasks/index/{$project->project_id}") ?>">View Tasks</a> |
                                    <a href="#" class="share-project" data-id="<?= $project->project_id ?>">Share</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- עמודה אחת לטופס הוספה/עריכה -->
        <div id="project-form-container" style="flex: 1; border-left: 1px solid #ccc; padding-left: 20px;">
            <!-- כאן נטען טופס add/edit ב-AJAX -->
        </div>
    </div>

    <!-- SHARE PROJECT MODAL (Bootstrap 3) -->
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

<!-- CSS + JS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function () {

        // Flash message hide
        setTimeout(() => $('#flash-message').fadeOut('slow'), 4000);

        // אתחול DataTable
        var projectsTable = $('#projects-table').DataTable({
            "order": [[0, 'asc']],
            "paging": true,
            "pageLength": 10
        });

        // ------------------ ADD PROJECT ------------------
        $('#show-add-form').on('click', function () {
            $.get('<?= base_url("projects/add_ajax_form") ?>', function (html) {
                $('#project-form-container').html(html);
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
                        let newRow = `
                    <tr id="project-${response.project_id}">
                        <td>${$('#project_title').val()}</td>
                        <td>${$('#project_body').val()}</td>
                        <td><span class="badge badge-success">Open</span></td>
                        <td>
                            <a href="#" class="edit-project" data-id="${response.project_id}">Edit</a> |
                            <a href="<?= base_url("projects/delete/") ?>${response.project_id}" onclick="return confirm('Are you sure?')">Delete</a> |
                            <a href="<?= base_url("tasks/index/") ?>${response.project_id}">View Tasks</a>
                        </td>
                    </tr>
                    `;
                        projectsTable.row.add($(newRow)).draw(false);
                        $('#project-form-container').html('');
                    } else {
                        alert(response.message);
                    }
                }
            });
        });

        $(document).on('click', '#cancel-add-project', function () {
            $('#project-form-container').html('');
        });

        // ------------------ EDIT PROJECT ------------------
        $(document).on('click', '.edit-project', function (e) {
            e.preventDefault();
            let id = $(this).data('id');
            $.get('<?= base_url("projects/edit_ajax_form/") ?>' + id, function (html) {
                $('#project-form-container').html(html);
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
                        let updatedRow = `
                    <td>${response.project_title}</td>
                    <td>${response.project_body}</td>
                    <td><span class="badge badge-success">Open</span></td>
                    <td>
                        <a href="#" class="edit-project" data-id="${response.project_id}">Edit</a> |
                        <a href="<?= base_url("projects/delete/") ?>${response.project_id}" onclick="return confirm('Are you sure?')">Delete</a> |
                        <a href="<?= base_url("tasks/add/") ?>${response.project_id}">Add Task</a> |
                        <a href="<?= base_url("tasks/index/") ?>${response.project_id}">View Tasks</a>
                    </td>
                    `;
                        projectsTable.row($('#project-' + response.project_id)).data($(updatedRow)).draw(false);
                        $('#project-form-container').html('');
                    } else {
                        alert(response.message);
                    }
                }
            });
        });

        $(document).on('click', '#cancel-edit-project', function () {
            $('#project-form-container').html('');
        });

        // ------------------ SHARE PROJECT ------------------
        $(document).on('click', '.share-project', function (e) {
            e.preventDefault();
            let projectId = $(this).data('id');
            console.log("share project:" + projectId);
            $.get('<?= base_url("projects/share_ajax_form/") ?>' + projectId, function (html) {
                $('#share-project-modal-body').html(html);
                $('#shareProjectModal').modal('show');
            });
        });

        $(document).on('submit', '#share-project-form', function (e) {
            e.preventDefault();
            $.ajax({
                url: '<?= base_url("projects/share_ajax") ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Project shared successfully');
                        $('#shareProjectModal').modal('hide');
                    } else {
                        alert(response.message);
                    }
                }
            });
        });

    });
</script>

<style>
    .badge {
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.8em;
    }

    .badge-success {
        background-color: #28a745;
        color: #fff;
    }

    .badge-secondary {
        background-color: #6c757d;
        color: #fff;
    }
</style>