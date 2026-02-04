<div id="task-message"></div>

<?php echo form_open('', ['id' => 'add-task-form']); ?>
<div class="form-group">
    <label>Task Title</label>
    <input type="text" name="task_title" class="form-control" required>
</div>

<div class="form-group">
    <label>Task Description</label>
    <textarea name="task_body" class="form-control" rows="5" required></textarea>
</div>

<div class="form-group">
    <label>Due Date</label>
    <input type="date" name="task_due_date" class="form-control">
</div>

<div class="text-right">
    <button type="submit" class="btn btn-success">Add Task</button>
    <button type="button" class="btn btn-secondary" id="cancel-add-task">Cancel</button>
</div>
<?php echo form_close(); ?>