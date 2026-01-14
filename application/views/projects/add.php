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
<a href="<?= base_url('projects') ?>" class="btn btn-default">Cancel</a>

<?php echo form_close(); ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {

        $('#add-project-form').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: '<?= base_url("projects/add_ajax") ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        // מציג הודעת הצלחה
                        $('#project-message').html('<div class="alert alert-success">' + response.message + '</div>');
                        $('#add-project-form')[0].reset();

                        // מחכה 2 שניות לפני המעבר לדף הפרויקטים
                        setTimeout(function () {
                            window.location.href = response.redirect;
                        }, 2000);

                    } else {
                        $('#project-message').html('<div class="alert alert-danger">' + response.message + '</div>');
                        setTimeout(() => $('#project-message').fadeOut('slow'), 4000);
                    }
                }
            });
        });

    });
</script>