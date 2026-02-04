/* ============================================
   PROJECTS PAGE - JavaScript
   ============================================ */

$(document).ready(function () {
    initProjectsPage();
});

function initProjectsPage() {
    // Use existing DataTable instance
    var projectsTable = $('#projects-table').DataTable();

    // Show Add Project Form
    $('#show-add-form').on('click', function () {
        $.get(window.baseUrl + 'projects/add_ajax_form', function (html) {
            openProjectForm(html, 'Add New Project');
        });
    });

    // Add Project
    $(document).on('submit', '#add-project-form', function (e) {
        e.preventDefault();
        $.ajax({
            url: window.baseUrl + 'projects/add_ajax',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Add new row (matches original columns indexes) and set row id
                    var proj = response.project || response;
                    var added = projectsTable.row.add(buildProjectRowData(proj)).draw(false);
                    var addedNode = (typeof added.node === 'function') ? added.node() : projectsTable.row(':last').node();
                    var pid = proj.project_id || proj.id || response.project_id;
                    if (addedNode && pid) $(addedNode).attr('id', 'project-' + pid);

                    // Close form
                    isMobile()
                        ? $('#projectFormModal').modal('hide')
                        : $('#project-form-container').html('');
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Edit Project Form
    $(document).on('click', '.edit-project', function (e) {
        e.preventDefault();
        let id = $(this).data('id');
        $.get(window.baseUrl + 'projects/edit_ajax_form/' + id, function (html) {
            openProjectForm(html, 'Edit Project');
        });
    });

    // Edit Project Submit
    $(document).on('submit', '#edit-project-form', function (e) {
        e.preventDefault();
        let id = $(this).data('id');
        $.ajax({
            url: window.baseUrl + 'projects/edit_ajax/' + id,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Update existing row using row index or selector
                    var proj = response.project || response;
                    projectsTable.row('#project-' + id).data(buildProjectRowData(proj)).draw(false);

                    // Close form
                    isMobile()
                        ? $('#projectFormModal').modal('hide')
                        : $('#project-form-container').html('');
                } else {
                    alert(response.message);
                }
            }
        });
    });

    // Cancel Add/Edit
    $(document).on('click', '#cancel-add-project, #cancel-edit-project', function () {
        isMobile()
            ? $('#projectFormModal').modal('hide')
            : $('#project-form-container').html('');
    });

    // Share Project
    $(document).on('click', '.share-project', function (e) {
        e.preventDefault();
        let id = $(this).data('id');
        $.get(window.baseUrl + 'shares/share_ajax_form/' + id, function (html) {
            $('#share-project-modal-body').html(html);
            $('#shareProjectModal').modal('show');
        });
    });

    $(document).on('submit', '#share-project-form', function (e) {
        e.preventDefault();
        $.ajax({
            url: window.baseUrl + 'shares/share_ajax',
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
            },
            error: function () {
                alert('Server error');
            }
        });
    });
}

// Opens the Add/Edit form in modal or container
function openProjectForm(html, title) {
    if (isMobile()) {
        $('#projectFormModalTitle').text(title);
        $('#project-form-modal-body').html(html);
        $('#projectFormModal').modal('show');
    } else {
        $('#project-form-container').html(html);
    }
}

// Build row array for DataTable (matches column indexes)
function buildProjectRowData(project) {
    let statusText = project.project_status || 'no missions';
    let badgeClass = statusText === 'Open' ? 'badge-success' :
        statusText === 'no missions' ? 'badge-info' :
            'badge-secondary';
    var taskCount = (typeof project.task_count !== 'undefined') ? project.task_count : 0;

    var statusCell = `<span class="badge ${badgeClass}">${statusText} (${taskCount})</span>`;

    var actionsCell = `
        <div class="actions-large d-none d-md-flex">
            <a class="btn btn-sm btn-primary edit-project" href="#" data-id="${project.project_id}">Edit</a>
            <a class="btn btn-sm btn-danger" href="${window.baseUrl}projects/delete/${project.project_id}" onclick="return confirm('Are you sure?')">Delete</a>
            <a class="btn btn-sm btn-info" href="${window.baseUrl}tasks/index/${project.project_id}?from=projects">Tasks</a>
            <a class="btn btn-sm btn-warning share-project" href="#" data-id="${project.project_id}">Share</a>
        </div>
        <div class="dropdown d-md-none">
            <button class="btn btn-secondary dropdown-toggle" type="button"
                id="dropdownMenuButton-${project.project_id}" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="three-dots">⋮</span>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton-${project.project_id}">
                <a class="dropdown-item edit-project" href="#" data-id="${project.project_id}">Edit</a>
                <a class="dropdown-item" href="${window.baseUrl}projects/delete/${project.project_id}" onclick="return confirm('Are you sure?')">Delete</a>
                <a class="dropdown-item" href="${window.baseUrl}tasks/index/${project.project_id}?from=projects">Tasks</a>
                <a class="dropdown-item share-project" href="#" data-id="${project.project_id}">Share</a>
            </div>
        </div>`;

    // Return array of cell values in the same column order as the table
    return [
        project.project_title,
        project.project_body,
        statusCell,
        actionsCell
    ];
}