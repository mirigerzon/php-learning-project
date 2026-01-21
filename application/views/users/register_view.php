<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 15px;
        }

        .register-container {
            width: 100%;
            max-width: 400px;
            padding: 30px 25px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .register-container h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .register-link {
            text-align: center;
            margin-top: 15px;
        }

        .error-msg {
            font-size: 0.875em;
            margin-top: 3px;
        }

        @media (max-width: 576px) {
            .register-container {
                padding: 20px 15px;
            }

            .register-container h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <div class="register-container">
        <h2>Register</h2>

        <div id="flash-message">
            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
            <?php endif; ?>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?= form_open('users/register') ?>

        <div class="mb-3">
            <?= form_label('First Name') ?>
            <?= form_input([
                'name' => 'first_name',
                'class' => 'form-control',
                'placeholder' => 'Enter first name',
                'value' => set_value('first_name')
            ]) ?>
            <?= form_error('first_name', '<div class="text-danger error-msg">', '</div>') ?>
        </div>

        <div class="mb-3">
            <?= form_label('Last Name') ?>
            <?= form_input([
                'name' => 'last_name',
                'class' => 'form-control',
                'placeholder' => 'Enter last name',
                'value' => set_value('last_name')
            ]) ?>
            <?= form_error('last_name', '<div class="text-danger error-msg">', '</div>') ?>
        </div>

        <div class="mb-3">
            <?= form_label('Username') ?>
            <?= form_input([
                'name' => 'username',
                'class' => 'form-control',
                'placeholder' => 'Enter username',
                'value' => set_value('username')
            ]) ?>
            <?= form_error('username', '<div class="text-danger error-msg">', '</div>') ?>
        </div>

        <div class="mb-3">
            <?= form_label('Password') ?>
            <?= form_password([
                'name' => 'password',
                'class' => 'form-control',
                'placeholder' => 'Enter password'
            ]) ?>
            <?= form_error('password', '<div class="text-danger error-msg">', '</div>') ?>
        </div>

        <div class="d-grid">
            <?= form_submit([
                'name' => 'submit',
                'class' => 'btn btn-primary',
                'value' => 'Register'
            ]) ?>
        </div>

        <?= form_close() ?>

        <div class="register-link">
            <p>Already have an account? <a href="<?= base_url('users/login') ?>">Login here</a></p>
        </div>
    </div>

    <script>
        setTimeout(function () {
            var flash = document.getElementById('flash-message');
            if (flash) {
                flash.style.display = 'none';
            }
        }, 4000);
    </script>

</body>

</html>