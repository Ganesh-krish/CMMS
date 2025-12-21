<!DOCTYPE html>
<html lang="en" class="default-style layout-fixed layout-navbar-fixed">

<head>
    <title><?php echo isset($college['name']) ? $college['name'] . ' - Student Portal' : 'Student Portal'; ?></title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= base_url("/") ?>assets/img/favicon.svg">

    <!-- Icon fonts -->
    <link rel="stylesheet" href="<?= base_url('') ?>assets/faculty/fonts/ionicons.css">
    <link rel="stylesheet" href="<?= base_url('') ?>assets/faculty/fonts/feather.css">

    <!-- Core stylesheets -->
    <link rel="stylesheet" href="<?= base_url('') ?>assets/faculty/css/bootstrap-material.css">
    <link rel="stylesheet" href="<?= base_url('') ?>assets/faculty/css/shreerang-material.css">
    <link rel="stylesheet" href="<?= base_url('') ?>assets/faculty/css/uikit.css">
    <link rel="stylesheet" href="<?= base_url('') ?>assets/faculty/libs/perfect-scrollbar/perfect-scrollbar.css">

    <!-- Student Portal Custom Styles -->
    <style>
        .layout-navbar-fixed .layout-wrapper.layout-navbar-top .layout-container .layout-page-header .page-header-content {
            padding: 0.5rem 1rem;
        }

        .student-sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .student-sidebar .nav-item .nav-link {
            color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
        }

        .student-sidebar .nav-item .nav-link:hover,
        .student-sidebar .nav-item .nav-link.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }

        .student-sidebar .nav-item .nav-link i {
            margin-right: 10px;
            font-size: 1.1em;
        }

        .student-welcome {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-2">
        <div class="layout-inner">

            <!-- Layout sidenav -->
            <div id="layout-sidenav" class="layout-sidenav sidenav sidenav-vertical bg-dark student-sidebar">

                <!-- Brand demo (see assets/css/demo/demo.css) -->
                <div class="app-brand demo">
                    <span class="app-brand-logo demo">
                        <?php if(isset($college['logo']) && !empty($college['logo'])): ?>
                            <img src="<?php echo base_url('uploads/college/' . $college['logo']); ?>" alt="College Logo" style="width: 40px; height: 40px; border-radius: 50%;">
                        <?php else: ?>
                            <i class="fas fa-graduation-cap" style="font-size: 40px; color: #fff;"></i>
                        <?php endif; ?>
                    </span>
                    <a href="javascript:void(0)" class="app-brand-text demo sidenav-text font-weight-normal ml-2" style="color: #fff;">
                        Student Portal
                    </a>
                    <a href="javascript:void(0)" class="layout-sidenav-toggle sidenav-link text-large ml-auto">
                        <i class="ion ion-md-menu align-middle"></i>
                    </a>
                </div>

                <div class="sidenav-divider mb-0"></div>

                <!-- Student Welcome Section -->
                <div class="student-welcome">
                    <div class="text-center">
                        <h6 class="mb-1">Welcome,</h6>
                        <strong><?php echo htmlspecialchars($student['name']); ?></strong>
                        <br>
                        <small><?php echo htmlspecialchars($student['roll_no'] ?? 'Student'); ?></small>
                    </div>
                </div>

                <div class="sidenav-divider mb-1"></div>

                <!-- Links -->
                <ul class="sidenav-inner py-1">

                    <!-- Dashboard -->
                    <li class="sidenav-item <?php echo ($this->uri->segment(2) == 'dashboard') ? 'active' : ''; ?>">
                        <a href="<?php echo base_url('student-portal/dashboard'); ?>" class="sidenav-link">
                            <i class="sidenav-icon feather icon-home"></i>
                            <div>Dashboard</div>
                        </a>
                    </li>

                    <!-- Courses -->
                    <li class="sidenav-item <?php echo ($this->uri->segment(2) == 'courses') ? 'active' : ''; ?>">
                        <a href="<?php echo base_url('student-portal/courses'); ?>" class="sidenav-link">
                            <i class="sidenav-icon feather icon-book"></i>
                            <div>Courses</div>
                        </a>
                    </li>

                    <!-- Announcements -->
                    <li class="sidenav-item <?php echo ($this->uri->segment(2) == 'announcements') ? 'active' : ''; ?>">
                        <a href="<?php echo base_url('student-portal/announcements'); ?>" class="sidenav-link">
                            <i class="sidenav-icon feather icon-bell"></i>
                            <div>Announcements</div>
                        </a>
                    </li>

                    <!-- Music Inventory -->
                    <li class="sidenav-item <?php echo ($this->uri->segment(2) == 'inventory') ? 'active' : ''; ?>">
                        <a href="<?php echo base_url('student-portal/inventory'); ?>" class="sidenav-link">
                            <i class="sidenav-icon feather icon-music"></i>
                            <div>Music Inventory</div>
                        </a>
                    </li>

                    <div class="sidenav-divider my-2"></div>

                    <!-- Logout -->
                    <li class="sidenav-item">
                        <a href="<?php echo base_url('student-portal/logout'); ?>" class="sidenav-link" onclick="return confirm('Are you sure you want to logout?')">
                            <i class="sidenav-icon feather icon-log-out"></i>
                            <div>Logout</div>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- / Layout sidenav -->

            <!-- Layout container -->
            <div class="layout-container">
                <!-- Layout navbar -->
                <nav class="layout-navbar navbar navbar-expand-lg align-items-lg-center bg-white container-p-x" id="layout-navbar">

                    <a href="javascript:void(0)" class="nav-link nav-link-style rounded-circle press-scale-down d-none d-lg-block" data-action="sidenav-pin">
                        <i class="ion ion-md-menu text-muted"></i>
                    </a>

                    <div class="navbar-nav flex-row ml-auto align-items-center">
                        <!-- College Name -->
                        <span class="d-none d-lg-block text-muted font-weight-light mx-3">
                            <?php echo htmlspecialchars($college['name'] ?? 'College Management System'); ?>
                        </span>

                        <!-- User dropdown -->
                        <div class="nav-item dropdown">
                            <a href="javascript:void(0)" class="nav-link nav-link-style rounded-circle press-scale-down" data-toggle="dropdown">
                                <i class="ion ion-md-person text-muted"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <div class="dropdown-item-text">
                                    <strong><?php echo htmlspecialchars($student['name']); ?></strong><br>
                                    <small><?php echo htmlspecialchars($student['email']); ?></small>
                                </div>
                                <div class="dropdown-divider"></div>
                                <a href="<?php echo base_url('student-portal/logout'); ?>" class="dropdown-item">
                                    <i class="ion ion-md-log-out text-danger"></i> &nbsp; Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </nav>
                <!-- / Layout navbar -->

                <!-- Layout content -->
                <div class="layout-content">

                    <!-- Page content container -->
                    <div class="container-fluid flex-grow-1 container-p-y">
