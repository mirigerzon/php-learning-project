$(document).on('click', '.assign-tasks-btn', function (e) {
    e.preventDefault();
    let userId = $(this).data('user-id');
    let projectId = $(this).data('project-id');
    console.log('Assign tasks for user ID:', userId, 'in project ID:', projectId);

    $('#modal-user-id').val(userId);
    $('#modal-project-id').val(projectId);

    // שולח GET ומכניס את המשימות ל-modal
    $.get(window.baseUrl + 'tasks/get_tasks_for_user/' + projectId + '/' + userId, function (html) {
        $('#tasks-checkboxes').html(html);
        $('#assignTasksModal').modal('show');
    });
});



$('#assign-tasks-form').on('submit', function (e) {
    e.preventDefault();
    
    console.log($(this).serialize());

    $.post(window.baseUrl + 'tasks/save_assigned_tasks', $(this).serialize(), function (res) {
        res = JSON.parse(res);
        if (res.status === 'success') {
            alert('Tasks assigned successfully!');
            $('#assignTasksModal').modal('hide');
        } else {
            alert('Error: ' + res.message);
        }
    });
});



