<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : "My App" ?></title>

    <!-- Bootstrap 3 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <!-- Inline Responsive CSS -->
    <style>
        body {
            padding-top: 60px;
        }

        .navbar-brand {
            font-weight: bold;
        }

        /* Mobile & Tablet */
        @media (max-width: 768px) {

            .navbar-nav {
                text-align: center;
            }

            .navbar-nav>li {
                float: none;
                display: block;
            }

            .navbar-right {
                float: none !important;
            }

            .container {
                padding-left: 10px;
                padding-right: 10px;
            }

            h1,
            h2,
            h3 {
                text-align: center;
            }

            .btn {
                width: 100%;
                margin-bottom: 8px;
            }

            table {
                font-size: 14px;
            }
        }

        /* Small phones */
        @media (max-width: 480px) {

            .navbar-brand {
                font-size: 16px;
            }

            table {
                font-size: 13px;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">

            <div class="navbar-header">
                <!-- Mobile toggle button -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#main-navbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <a class="navbar-brand" href="<?= base_url('home') ?>">Home</a>
            </div>

            <div class="collapse navbar-collapse" id="main-navbar">

                <ul class="nav navbar-nav">
                    <li><a href="<?= base_url('projects') ?>">Projects</a></li>
                    <li><a href="<?= base_url('shares') ?>">Shares</a></li>
                    <li><a href="<?= base_url('dashboard') ?>">Dashboard</a></li>

                    <?php if ($this->session->userdata('is_admin')): ?>
                        <li><a href="<?= base_url('admin') ?>">Users control</a></li>
                        <li><a href="<?= base_url('admin_dashboard') ?>">Admin Dashboard</a></li>
                        <li><a href="<?= base_url('admin/projects') ?>">Users Projects</a></li>
                    <?php endif; ?>
                </ul>

                <ul class="nav navbar-nav navbar-right">
                    <?php if ($this->session->userdata('user_id')): ?>
                        <li>
                            <a href="<?= base_url('users/logout') ?>">
                                <span class="glyphicon glyphicon-log-out"></span> Logout
                            </a>
                        </li>
                    <?php else: ?>
                        <li>
                            <a href="<?= base_url('users/login') ?>">
                                <span class="glyphicon glyphicon-log-in"></span> Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

            </div>
        </div>
    </nav>

    <!-- Main content -->
    <div class="container">
        <?php $this->load->view($main_view); ?>
    </div>

</body>

</html>