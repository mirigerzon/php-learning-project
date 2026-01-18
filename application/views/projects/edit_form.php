<div class="title">
    <h2>Edit Project</h2>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger">
        <?= $error ?>
    </div>
<?php endif; ?>

<?php echo form_open("projects/edit/{$project->project_id}"); ?>
<div class="form-group">
    <label for="project_title">Project Title</label>
    <input type="text" name="project_title" id="project_title" class="form-control"
        value="<?= htmlspecialchars($project->project_title) ?>" required>
</div>
<div class="form-group">
    <label for="project_body">Project Description</label>
    <textarea name="project_body" id="project_body" class="form-control" rows="5"
        required><?= htmlspecialchars($project->project_body) ?></textarea>
</div>
<button type="submit" class="btn btn-success">Save Changes</button>
<button type="button" id="cancel-edit-project" class="btn btn-default">Cancel</button>
<?php echo form_close(); ?>