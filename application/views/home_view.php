<div id="flash-message">
    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert alert-success">
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>
</div>

<div class="jumbotron text-center" style="margin-top:20px;">
    <?php if ($this->session->userdata('user_id')): ?>
        <h2>Welcome <?= htmlspecialchars($this->session->userdata('username')) ?>!</h2>
        <p>You can navigate to your projects using the navigation above.</p>
    <?php else: ?>
        <h2>Welcome!</h2>
        <p>Please login or register to continue.</p>
        <a href="<?= base_url('users/login') ?>" class="btn btn-primary">Login</a>
        <a href="<?= base_url('users/register') ?>" class="btn btn-success">Register</a>
    <?php endif; ?>
</div>

<script>
    setTimeout(function () {
        var flash = document.getElementById('flash-message');
        if (flash) {
            flash.style.display = 'none';
        }
    }, 4000);
</script>