<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <!-- קישור ל-Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f7f7f7;
        }

        .login-container {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .login-container h2 {
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

    <div class="login-container">
        <h2>Login</h2>

        <?php if (isset($error))
            echo '<div class="alert alert-danger">' . $error . '</div>'; ?>

        <?php echo form_open('users/login'); ?>

        <div class="mb-3">
            <?php echo form_label('Username'); ?>
            <?php echo form_input(['name' => 'username', 'class' => 'form-control', 'placeholder' => 'Enter username']); ?>
        </div>

        <div class="mb-3">
            <?php echo form_label('Password'); ?>
            <?php echo form_password(['name' => 'password', 'class' => 'form-control', 'placeholder' => 'Enter password']); ?>
        </div>

        <div class="d-grid gap-2">
            <?php echo form_submit(['name' => 'submit', 'class' => 'btn btn-primary', 'value' => 'Login']); ?>
        </div>

        <?php echo form_close(); ?>

        <div class="register-link">
            <p>Don't have an account? <a href="<?= base_url('users/register') ?>">Register here</a></p>
        </div>
    </div>

</body>

</html>