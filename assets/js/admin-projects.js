/* ============================================
   PROJECTS PAGE - JavaScript
   ============================================ */

$(document).ready(function () {
    initProjectsPage();
});

$(document).ready(function () {
    const alertBox = $('.soft-alert');

    if (alertBox.length) {
        setTimeout(function () {
            alertBox.fadeOut(400, function () {
                $(this).remove();
            });
        }, 3000);
    }
});


function initProjectsPage() {
    $(document).on('click', '.btn-edit', function (e) {
        e.preventDefault();

        const projectId = $(this).data('id');

        $.get(window.baseUrl + 'projects/edit_ajax_form/' + projectId, function (html) {
            $('#projectFormModalTitle').text('Edit Project');
            $('#project-form-modal-body').html(html);
            $('#projectFormModal').modal('show');
        });
    });

    $(document).on('submit', '#edit-project-form', function (e) {
        e.preventDefault();

        const form = $(this);
        const projectId = form.data('id');

        $.ajax({
            url: window.baseUrl + 'projects/edit_ajax/' + projectId,
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#projectFormModal').modal('hide');
                    location.reload(); // או עדכון שורה בטבלה
                } else {
                    $('#project-form-modal-body').html(response.html);
                }
            },
            error: function () {
                alert('Server error');
            }
        });
    });


    // Share Project
    $(document).on('click', '.share-project', function (e) {
        e.preventDefault();
        let projectId = $(this).data('id');
        $.get(window.baseUrl + 'shares/share_ajax_form/' + projectId, function (html) {
            $('#share-project-modal-body').html(html);
            $('#shareProjectModal').modal('show');
        });
    });

    // Submit Share Form
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

    // Cancel Add/Edit
    $(document).on('click', '#cancel-edit-project', function () {
        $('#projectFormModal').modal('hide')
    });
}
