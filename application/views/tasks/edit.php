<?php if (!$this->session->user_id): ?>
    <h2 class="un_loged_in_message">OOPS - it is seem like you are not loged in. Please log in <a
            href="<?= base_url('users/login') ?>">here</a>.</h2>
<?php else: ?>

    <div class="title">
        <h2>Edit Task</h2>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?= $error ?>
        </div>
    <?php endif; ?>

    <?php echo form_open("tasks/edit/{$task->project_id}/{$task->task_id}"); ?>
    <div class="form-group">
        <label for="task_title">Task Title</label>
        <input type="text" name="task_title" id="task_title" class="form-control"
            value="<?= htmlspecialchars($task->task_title) ?>" required>
    </div>
    <div class="form-group">
        <label for="task_body">Task Description</label>
        <textarea name="task_body" id="task_body" class="form-control" rows="5"
            required><?= htmlspecialchars($task->task_body) ?></textarea>
    </div>

    <div class="form-group">
        <label for="task_due_date">Due Date</label>
        <input type="date" name="task_due_date" id="task_due_date" class="form-control" value="<?= $task->due_date ?>">
    </div>

    <button type="submit" class="btn btn-primary">Save Changes</button>
    <a href="<?= base_url('/tasks/index/' . $task->project_id) ?>" class="btn btn-default">Cancel</a>
    <?php echo form_close(); ?>
<?php endif; ?>