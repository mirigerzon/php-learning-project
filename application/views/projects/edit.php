<?php if (!$this->session->user_id): ?>
    <h2 class="un_loged_in_message">OOPS - it is seem like you are not loged in. Please log in <a
            href="<?= base_url('users/login') ?>">here</a>.</h2>
<?php else: ?>

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
    <button type="submit" class="btn btn-primary">Save Changes</button>
    <a href="<?= base_url('projects') ?>" class="btn btn-default">Cancel</a>
    <?php echo form_close(); ?>
<?php endif; ?>