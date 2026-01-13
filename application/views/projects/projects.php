<?php if (!$this->session->user_id): ?>
    <h2 class="un_loged_in_message">OOPS - it is seem like you are not loged in. Please log in <a
            href="<?= base_url('users/login') ?>">here</a>.</h2>
<?php else: ?>
    <div class="title">
        <h2>My Projects</h2>
    </div>

    <?php if ($this->session->flashdata('success')): ?>
        <div id="flash-message" class="alert alert-success">
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($projects)): ?>
        <ul>
            <?php foreach ($projects as $project): ?>
                <li>
                    <strong><?= htmlspecialchars($project->project_title) ?></strong> -
                    <?= htmlspecialchars($project->project_body) ?>

                    <div style="margin-top: 5px;">
                        <a href="<?= base_url("projects/edit/{$project->project_id}") ?>">Edit</a> |
                        <a href="<?= base_url("projects/delete/{$project->project_id}") ?>"
                            onclick="return confirm('Are you sure?')">Delete</a> |
                        <a href="<?= base_url("tasks/add/{$project->project_id}") ?>">Add Task</a> |
                        <a href="<?= base_url("tasks/index/{$project->project_id}") ?>">View Tasks</a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No projects found.</p>
    <?php endif; ?>

    <div style="margin-top: 20px;">
        <a href="<?= base_url('projects/add') ?>" class="btn btn-success">Add New Project</a>
    </div>

<?php endif; ?>


<script>
    // מחיקת הודעה אחרי 4 שניות
    setTimeout(function () {
        var flash = document.getElementById('flash-message');
        if (flash) {
            flash.style.display = 'none';
        }
    }, 4000);
</script>