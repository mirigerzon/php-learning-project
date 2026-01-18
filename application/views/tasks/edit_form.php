<?php
// $task נשמר כאן ממסירת הנתונים מהקונטרולר
?>
<?php echo form_open("tasks/edit_ajax/{$task->project_id}/{$task->task_id}", ['id' => 'edit-task-form', 'data-id' => $task->task_id]); ?>

<div class="form-group mb-2">
    <label>Task Title</label>
    <input type="text" name="task_title" class="form-control" value="<?= htmlspecialchars($task->task_title) ?>"
        required>
</div>

<div class="form-group mb-2">
    <label>Description</label>
    <textarea name="task_body" class="form-control" rows="4"
        required><?= htmlspecialchars($task->task_body) ?></textarea>
</div>

<div class="form-group mb-3">
    <label>Due Date</label>
    <input type="date" name="task_due_date" class="form-control" value="<?= $task->due_date ?>">
</div>

<button type="submit" class="btn btn-success">Save</button>
<button type="button" class="btn btn-secondary" id="cancel-edit-task">Cancel</button>

<?php echo form_close(); ?>