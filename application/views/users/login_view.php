<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap -->
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

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 30px 25px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .register-link {
            text-align: center;
            margin-top: 15px;
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 20px 15px;
            }

            .login-container h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <h2>Login</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <?php echo form_open('users/login'); ?>

        <div class="mb-3">
            <?php echo form_label('Username'); ?>
            <?php echo form_input(['name' => 'username', 'class' => 'form-control', 'placeholder' => 'Enter username']); ?>
        </div>

        <div class="mb-3">
            <?php echo form_label('Password'); ?>
            <?php echo form_password(['name' => 'password', 'class' => 'form-control', 'placeholder' => 'Enter password']); ?>
        </div>

        <div class="d-grid">
            <?php echo form_submit(['name' => 'submit', 'class' => 'btn btn-primary', 'value' => 'Login']); ?>
        </div>

        <?php echo form_close(); ?>

        <div class="register-link">
            <p>Don't have an account? <a href="<?= base_url('users/register') ?>">Register here</a></p>
        </div>
    </div>

</body>

</html>