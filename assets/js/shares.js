/* ============================================
   SHARES PAGE - JavaScript
   ============================================ */

$(document).ready(function () {
    initSharesPage();
});

function initSharesPage() {
    // Get already-initialized DataTable instance (initialized in app.js)
    var sharedTable = $('#shared-projects-table').DataTable();

    // Store current project ID for form submission
    let currentProjectId = null;

    // EDIT SHARED PROJECT
    $(document).on('click', '.edit-project', function (e) {
        e.preventDefault();
        let id = $(this).data('id');
        currentProjectId = id;
        $.get(window.baseUrl + 'projects/edit_ajax_form/' + id, function (html) {
            $('#shared-project-form-container').html(html);
        });
    });

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
                    // Build actions based on role
                    let actions = '';
                    const roleLC = (response.role || 'viewer').toLowerCase();

                    if (roleLC === 'editor' || roleLC === 'admin') {
                        actions += `<a href="#" class="edit-project" data-id="${response.project_id}">Edit</a> | `;
                    }
                    if (roleLC === 'admin') {
                        actions += `<a href="${window.baseUrl}projects/delete/${response.project_id}" onclick="return confirm('Are you sure?')">Delete</a> | `;
                    }
                    if (roleLC === 'editor' || roleLC === 'admin') {
                        actions += `<a href="${window.baseUrl}tasks/index/${response.project_id}?from=shares">View Tasks</a>`;
                        if (roleLC === 'admin') {
                            actions += ' | ';
                        }
                    }
                    if (roleLC === 'admin') {
                        actions += `<a href="#" class="share-project" data-id="${response.project_id}">Share</a>`;
                    }

                    // Find the row in DataTable
                    var $row = $('#shared-project-' + response.project_id);

                    if ($row.length) {
                        // Update the row via DataTable API
                        sharedTable.row($row).data([
                            $row.find('td').eq(0).text(), // keep the index
                            response.project_title,
                            `<span class="badge ${roleLC === 'editor' || roleLC === 'admin' ? 'bg-success' : 'bg-secondary'}">${response.role}</span>`,
                            actions
                        ]).draw(false); // redraw without affecting pagination
                    }

                    // Clear the form
                    $('#shared-project-form-container').html('');
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert('Server error');
            }
        });
    });

    $(document).on('click', '#cancel-edit-project', function () {
        $('#shared-project-form-container').html('');
    });

    // SHARE PROJECT
    $(document).on('click', '.share-project', function (e) {
        e.preventDefault();
        let projectId = $(this).data('id');
        $.get(window.baseUrl + 'shares/share_ajax_form/' + projectId, function (html) {
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
