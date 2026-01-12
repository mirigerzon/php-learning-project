<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : "My App" ?></title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="<?= base_url('home') ?>">Home</a>
            </div>
            <ul class="nav navbar-nav">
                <li><a href="<?= base_url('projects') ?>">Projects</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php if ($this->session->userdata('user_id')): ?>
                    <li><a href="<?= base_url('users/logout') ?>"><span class="glyphicon glyphicon-log-out"></span>
                            Logout</a></li>
                <?php else: ?>
                    <li><a href="<?= base_url('users/login') ?>"><span class="glyphicon glyphicon-log-in"></span> Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Main content -->
    <div class="container" style="margin-top: 20px;">
        <?php $this->load->view($main_view); ?>
    </div>

</body>

</html>