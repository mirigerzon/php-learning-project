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
<button type="button" class="btn btn-secondary" id="cancel-add-task">Cancel</button>
<?php echo form_close(); ?>