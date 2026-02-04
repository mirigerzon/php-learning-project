/* ============================================
   TASKS PAGE - JavaScript
   ============================================ */

$(document).ready(function () {
    initTasksPage();
});

function initTasksPage() {

    // Get project_id from page
    var projectId = window.projectId || null;
    if (!projectId) {
        console.error('projectId not defined');
        return;
    }

    // קבלת הפרמטר from מה-URL
    const urlParams = new URLSearchParams(window.location.search);
    const fromPage = urlParams.get('from'); // 'projects', 'shares' או 'admin'
    console.log('Came from page:', fromPage);

    // אפשר לשמור גלובלית אם צריך להשתמש במקומות אחרים
    window.fromPage = fromPage;

    // דוגמה: לשנות כפתורים לפי המקור
    if (fromPage === 'projects') {
        $('#some-button').show();
    } else if (fromPage === 'shares') {
        $('#some-button').hide();
    } else if (fromPage === 'admin') {
        $('#some-button').text('Admin view');
    }

    // Get already-initialized DataTable instance (initialized in app.js)
    var tasksTable = $('#tasks-table').DataTable();

    $(document).off('click', '#show-add-task-form');
    $(document).on('click', '#show-add-task-form', function (e) {
        e.preventDefault();
        console.log('Opening add task form for project:', projectId);

        $.get(baseUrl + 'tasks/add_ajax_form/' + projectId, function (html) {
            console.log('Form loaded, HTML length:', html.length);
            $('#add-task-container').html(html);

            // Debug: check if modal element exists
            var modal = $('#addTaskModal');
            console.log('Modal element exists:', modal.length > 0);
            console.log('Modal classes before:', modal.attr('class'));

            // Use Bootstrap's modal method
            modal.modal({
                backdrop: 'static',
                keyboard: true
            });

            // Debug info
            setTimeout(function () {
                console.log('Modal classes after:', modal.attr('class'));
                console.log('Modal is visible:', modal.is(':visible'));
                console.log('Modal display style:', modal.css('display'));
                console.log('Inputs in modal:', modal.find('input, textarea').length);

                // Try to focus first input
                var firstInput = modal.find('input').first();
                if (firstInput.length) {
                    console.log('First input found, attempting focus');
                    firstInput.focus();
                }
            }, 100);
        }).fail(function (jqXHR, textStatus, errorThrown) {
            console.error('Error loading form:', textStatus, errorThrown);
            alert('Error loading form. Please try again.');
        });
    });


    $(document).on('submit', '#add-task-form', function (e) {
        e.preventDefault();

        // שולח את הבקשה עם dataType 'json' כדי ש-jQuery יהפוך את התגובה ל-object אוטומטית
        $.ajax({
            url: window.baseUrl + 'tasks/add_ajax/' + projectId,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json', // <--- מוודא שהתגובה מגיעה כאובייקט JS
            success: function (response) {
                console.log('Server response:', response.success);

                if (response.success) {
                    const t = response.task;
                    const is_done = t.status === 1;
                    const is_late = !is_done && t.due_date && new Date(t.due_date) < new Date();
                    const row_class = is_done ? 'row-done' : (is_late ? 'row-late' : '');

                    let actionLinks = '';

                    // קישורים לפעולות בהתאם להרשאות
                    if (window.canEdit) {
                        if (!is_done) {
                            actionLinks += `<a href="${window.baseUrl}tasks/mark_as_done/${projectId}/${t.task_id}" class="btn-action btn-success">Done</a>`;
                        } else {
                            actionLinks += `<a href="${window.baseUrl}tasks/mark_as_un_done/${projectId}/${t.task_id}" class="btn-action btn-secondary">Undo</a>`;
                        }
                        actionLinks += `<a href="${window.baseUrl}tasks/delete/${projectId}/${t.task_id}" class="btn-action btn-danger" onclick="return confirm('Are you sure?')">Delete</a>`;
                    }
                    actionLinks += `<a href="${window.baseUrl}tasks/view/${projectId}/${t.task_id}" class="btn-action btn-info">View</a>`;

                    const rowNode = tasksTable.row.add([
                        `<div class="task-title"> <h4>${sanitizeHtml(t.task_title)}</h4>${is_done ? '<span class="status-badge badge-success"> ✓ Done</span>' : (is_late ? '<span class="status-badge badge-danger">⚠ Late</span>' : '')}</div>`,
                        `<div class="task-description">${sanitizeHtml(t.task_body).replace(/\n/g, '<br>')}</div>`,
                        new Date(t.created_at).toLocaleDateString(),
                        t.due_date ? new Date(t.due_date).toLocaleDateString() : '—',
                        '0', // image count
                        actionLinks
                    ]).draw(false).node();

                    $(rowNode).addClass(row_class);
                    closeTaskModal();
                } else {
                    console.log('Error response from server:', response);
                    $('#task-message').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function () {
                $('#task-message').html('<div class="alert alert-danger">Error saving task. Please try again.</div>');
            }
        });
    });

    $(document).on('click', '#cancel-add-task', function () {
        closeTaskModal();
    });
}

// Helper function to properly close the modal
function closeTaskModal() {
    var modal = $('#addTaskModal');
    if (modal.length) {
        modal.modal('hide');
        // Ensure backdrop is removed
        setTimeout(function () {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
        }, 100);
    }
}
