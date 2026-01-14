<?php if (!$this->session->user_id): ?>
    <h2 class="un_loged_in_message">OOPS - it seems like you are not logged in. Please log in <a
            href="<?= base_url('users/login') ?>">here</a>.</h2>
<?php else: ?>

    <div class="title">
        <h2>Add Task</h2>
    </div>

    <div id="task-message"></div>

    <?php echo form_open("tasks/add_ajax/{$project_id}", ['id' => 'add-task-form']); ?>
    <div class="form-group">
        <label for="task_title">Task Title</label>
        <input type="text" name="task_title" id="task_title" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="task_body">Task Description</label>
        <textarea name="task_body" id="task_body" class="form-control" rows="5" required></textarea>
    </div>

    <div class="form-group">
        <label for="task_due_date">Due Date</label>
        <input type="date" name="task_due_date" id="task_due_date" class="form-control">
    </div>

    <button type="submit" class="btn btn-success">Add Task</button>
    <a href="<?= base_url("tasks/index/{$project_id}") ?>" class="btn btn-default">Cancel</a>

    <?php echo form_close(); ?>
<?php endif; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {

        $('#add-task-form').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: '<?= base_url("tasks/add_ajax/{$project_id}") ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // מציג הודעת הצלחה
                        $('#task-message').html('<div class="alert alert-success">' + response.message + '</div>');
                        $('#add-task-form')[0].reset(); // מאפס את הטופס

                        // מחכה 2 שניות לפני המעבר לדף המשימות
                        setTimeout(function () {
                            window.location.href = response.redirect;
                        }, 2000);

                    } else {
                        // מציג הודעת שגיאה
                        $('#task-message').html('<div class="alert alert-danger">' + response.message + '</div>');
                        setTimeout(() => $('#task-message').fadeOut('slow'), 4000);
                    }
                }
            });
        });

    });
</script>