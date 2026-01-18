<div class="title">
    <h2>Add Project</h2>
</div>

<div id="project-message"></div>
<?php echo form_open('projects/add_ajax', ['id' => 'add-project-form']); ?>
<div class="form-group">
    <label for="project_title">Project Title</label>
    <input type="text" name="project_title" id="project_title" class="form-control" required>
</div>
<div class="form-group">
    <label for="project_body">Project Description</label>
    <textarea name="project_body" id="project_body" class="form-control" rows="5" required></textarea>
</div>
<button type="submit" class="btn btn-success">Add Project</button>
<button type="button" class="btn btn-secondary" id="cancel-add-project">Cancel</button>
<?php echo form_close(); ?>