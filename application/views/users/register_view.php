<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }

        .register-container {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .register-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .register-link {
            text-align: center;
            margin-top: 15px;
        }
    </style>
</head>

<body>

    <div class="register-container">
        <h2>Register</h2>

        <?php if (isset($error))
            echo '<div class="alert alert-danger">' . $error . '</div>'; ?>

        <?php echo form_open('users/register'); ?>

        <div class="mb-3">
            <?php echo form_label('First Name'); ?>
            <?php echo form_input([
                'name' => 'first_name',
                'class' => 'form-control',
                'placeholder' => 'Enter first name'
            ]); ?>
        </div>

        <div class="mb-3">
            <?php echo form_label('Last Name'); ?>
            <?php echo form_input([
                'name' => 'last_name',
                'class' => 'form-control',
                'placeholder' => 'Enter last name'
            ]); ?>
        </div>

        <div class="mb-3">
            <?php echo form_label('Username'); ?>
            <?php echo form_input([
                'name' => 'username',
                'class' => 'form-control',
                'placeholder' => 'Enter username'
            ]); ?>
        </div>

        <div class="mb-3">
            <?php echo form_label('Password'); ?>
            <?php echo form_password([
                'name' => 'password',
                'class' => 'form-control',
                'placeholder' => 'Enter password'
            ]); ?>
        </div>

        <div class="d-grid gap-2">
            <?php echo form_submit([
                'name' => 'submit',
                'class' => 'btn btn-primary',
                'value' => 'Register'
            ]); ?>
        </div>

        <?php echo form_close(); ?>

        <div class="register-link">
            <p>Already have an account? <a href="<?= base_url('users/login') ?>">Login here</a></p>
        </div>
    </div>

</body>

</html>