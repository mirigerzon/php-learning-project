<div class="row">
    <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title text-center panel-title-heading">Register</h3>
            </div>
            <div class="panel-body">
                <div id="flash-message">
                    <?php if ($this->session->flashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                            <?= $this->session->flashdata('success') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <?= form_open('users/register') ?>

                <div class="form-group">
                    <?= form_label('First Name', 'first_name') ?>
                    <?= form_input([
                        'name' => 'first_name',
                        'id' => 'first_name',
                        'class' => 'form-control',
                        'placeholder' => 'Enter first name',
                        'value' => set_value('first_name'),
                        'required' => 'required'
                    ]) ?>
                    <?= form_error('first_name', '<span class="help-block">', '</span>') ?>
                </div>

                <div class="form-group">
                    <?= form_label('Last Name', 'last_name') ?>
                    <?= form_input([
                        'name' => 'last_name',
                        'id' => 'last_name',
                        'class' => 'form-control',
                        'placeholder' => 'Enter last name',
                        'value' => set_value('last_name'),
                        'required' => 'required'
                    ]) ?>
                    <?= form_error('last_name', '<span class="help-block">', '</span>') ?>
                </div>

                <div class="form-group">
                    <?= form_label('Username', 'username') ?>
                    <?= form_input([
                        'name' => 'username',
                        'id' => 'username',
                        'class' => 'form-control',
                        'placeholder' => 'Enter username',
                        'value' => set_value('username'),
                        'required' => 'required'
                    ]) ?>
                    <?= form_error('username', '<span class="help-block">', '</span>') ?>
                </div>

                <div class="form-group">
                    <?= form_label('Password', 'password') ?>
                    <?= form_password([
                        'name' => 'password',
                        'id' => 'password',
                        'class' => 'form-control',
                        'placeholder' => 'Enter password',
                        'required' => 'required'
                    ]) ?>
                    <?= form_error('password', '<span class="help-block">', '</span>') ?>
                </div>

                <div class="form-group">
                    <?= form_submit([
                        'name' => 'submit',
                        'class' => 'btn btn-primary btn-block',
                        'value' => 'Register'
                    ]) ?>
                </div>

                <?= form_close() ?>

                <hr>
                <p class="text-center no-margin">
                    Already have an account? <a href="<?= base_url('users/login') ?>">Login here</a>
                </p>
            </div>
        </div>
    </div>
</div>