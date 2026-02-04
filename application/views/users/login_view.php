<div class="row">
    <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-center panel-title-heading">Login</h3>
            </div>
            <div class="panel-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <?php echo form_open('users/login'); ?>

                <div class="form-group">
                    <?php echo form_label('Username', 'username'); ?>
                    <?php echo form_input(['name' => 'username', 'id' => 'username', 'class' => 'form-control', 'placeholder' => 'Enter username', 'required' => 'required']); ?>
                </div>

                <div class="form-group">
                    <?php echo form_label('Password', 'password'); ?>
                    <?php echo form_password(['name' => 'password', 'id' => 'password', 'class' => 'form-control', 'placeholder' => 'Enter password', 'required' => 'required']); ?>
                </div>

                <div class="form-group">
                    <?php echo form_submit(['name' => 'submit', 'class' => 'btn btn-primary btn-block', 'value' => 'Login']); ?>
                </div>

                <?php echo form_close(); ?>

                <hr>
                <p class="text-center no-margin">
                    Don't have an account? <a href="<?= base_url('users/register') ?>">Register here</a>
                </p>
            </div>
        </div>
    </div>
</div>