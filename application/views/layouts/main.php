<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : "My App" ?></title>

    <!-- Set Base URL for JavaScript -->
    <script>
        window.baseUrl = '<?= base_url() ?>';
    </script>

    <!-- Bootstrap 3 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">

    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>

    <!-- Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <!-- Global Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

    <!-- Global JavaScript -->
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
</head>

<body>

    <?php
    $segment1 = $this->uri->segment(1);
    $segment2 = $this->uri->segment(2);

    $active = $segment1;

    // TASKS
    if ($segment1 === 'tasks') {
        // משימות מתוך מנהל
        if ($segment2 === 'admin_view') {
            $active = 'admin_projects';
        }
        // משימות רגיל
        else {
            $active = $_GET['from'] ?? 'projects';
        }
    }

    // ADMIN
    if ($segment1 === 'admin') {
        if ($segment2 === 'projects') {
            $active = 'admin_projects';
        } else {
            $active = 'admin';
        }
    }

    // ADMIN DASHBOARD
    if ($segment1 === 'admin_dashboard') {
        $active = 'admin_dashboard';
    }
    ?>


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
                    <li class="<?= $active === 'projects' ? 'active' : '' ?>">
                        <a href="<?= base_url('projects') ?>">Projects</a>
                    </li>

                    <li class="<?= $active === 'shares' ? 'active' : '' ?>">
                        <a href="<?= base_url('shares') ?>">Shares</a>
                    </li>

                    <li class="<?= $this->uri->segment(1) == 'dashboard' ? 'active' : '' ?>">
                        <a href="<?= base_url('dashboard') ?>">Dashboard</a>
                    </li>

                    <?php if ($this->session->userdata('is_admin')): ?>
                        <li class="<?= $active === 'admin' ? 'active' : '' ?>">
                            <a href="<?= base_url('admin') ?>">Users control</a>
                        </li>

                        <li class="<?= $active === 'admin_dashboard' ? 'active' : '' ?>">
                            <a href="<?= base_url('admin_dashboard') ?>">Admin Dashboard</a>
                        </li>

                        <li class="<?= $active === 'admin_projects' ? 'active' : '' ?>">
                            <a href="<?= base_url('admin/projects') ?>">Users Projects</a>
                        </li>
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