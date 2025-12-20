<!DOCTYPE html>

<html lang="en" class="default-style layout-fixed layout-navbar-fixed">

<head>
    <title>Drillu</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">

    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?= base_url("/") ?>assets/images/favicon.svg">
    <!-- Icon fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('') ?>assets/faculty/libs/select2/select2.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JS (Bundle includes Popper.js) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <!-- Icon fonts -->
    <link rel="stylesheet" href="<?= base_url('') ?>assets/faculty/fonts/ionicons.css">
    <link rel="stylesheet" href="<?= base_url('') ?>assets/faculty/fonts/feather.css">
    
    <!-- Core stylesheets -->
    <link rel="stylesheet" href="<?= base_url('') ?>assets/faculty/css/bootstrap-material.css">
    <link rel="stylesheet" href="<?= base_url('') ?>assets/faculty/css/shreerang-material.css">
    <link rel="stylesheet" href="<?= base_url('') ?>assets/faculty/css/uikit.css">
    <link rel="stylesheet" href="<?= base_url('') ?>assets/faculty/libs/perfect-scrollbar/perfect-scrollbar.css">
    <link rel="stylesheet" href="<?= base_url('') ?>assets/faculty/libs/bootstrap-multiselect/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.3/viewer.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.11.3/viewer.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= base_url('') ?>assets/faculty/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <link rel="stylesheet" href="<?= base_url('') ?>assets/faculty/css/pages/tickets.css">

    <link rel="stylesheet" href="<?= base_url("/") ?>assets/packages/highlight.min.css">
    <link rel="stylesheet" href="<?= base_url("/") ?>assets/packages/fontawesome-6.3.css">
    <link href="<?= base_url("/") ?>assets/packages/select2.min.css" rel="stylesheet" />

    <!-- DataTables CSS & Buttons -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css">

    <style>
        /* Sidebar Menu Styling */
    .sidenav-item {
        position: relative;
    }

    .sidenav-link {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        color: #fff;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .sidenav-link:hover {
        background-color: #ff4a00;
        color: #fff !important;
    }

    .sidenav-link:hover .sidenav-icon,
    .sidenav-link:hover div {
        color: #fff !important;
    }

    /* Submenu Styling */
    .submenu {
        display: none; /* Hide by default */
        list-style: none;
        padding-left: 20px;
        background-color: #ff4A00; /* Change submenu background */
        border-left: 3px solid #fff;
        margin: 0;
    }

    .submenu li {
        padding: 8px 10px;
        margin: 0;
    }

    .submenu li a {
        color: #fff;
        text-decoration: none;
        display: block;
        padding: 5px 0;
        transition: color 0.3s ease;
    }

    .submenu li a:hover,
    .submenu li a.active {
        color: rgba(255,255,255,0.8);
        font-weight: 500;
    }

    /* Active menu overlay styles */
    .sidenav-item.active .sidenav-link {
        background-color: #3da9fc;
        color: #000 !important;
        position: relative;
    }

    .sidenav-item.active .sidenav-link::after {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background-color: rgba(0,0,0,0.8);
    }

    .sidenav-item.active .sidenav-link .sidenav-icon,
    .sidenav-item.active .sidenav-link div {
        color: #000 !important;
    }

    /* Dashboard Chart Styles */
    .user-distribution {
        margin-bottom: 20px;
    }

    .distribution-item {
        margin-bottom: 12px;
    }

    .distribution-item .progress {
        background-color: #f8f9fa;
        border-radius: 4px;
    }

    .metric-card {
        padding: 15px;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        background: #f8f9fa;
    }

    .ratio-display {
        display: flex;
        align-items: baseline;
        min-width: 60px;
    }

    .ratio-number {
        font-size: 24px;
        font-weight: bold;
        color: #007bff;
    }

    .ratio-label {
        font-size: 14px;
        color: #6c757d;
        margin-left: 2px;
    }

    .ratio-bar {
        flex: 1;
        height: 60px;
        background: #e9ecef;
        border-radius: 4px;
        position: relative;
        overflow: hidden;
    }

    .ratio-fill {
        width: 100%;
        background: linear-gradient(180deg, #007bff 0%, #0056b3 100%);
        border-radius: 4px;
        transition: height 0.3s ease;
    }

    .ratio-fill.bg-success {
        background: linear-gradient(180deg, #28a745 0%, #1e7e34 100%);
    }

    /* Chart container responsive */
    @media (max-width: 768px) {
        .ratio-display {
            min-width: 50px;
        }

        .ratio-number {
            font-size: 20px;
        }

        .ratio-bar {
            height: 50px;
        }
    }

    </style>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 10000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })
    </script>
    <style>
        .swal2-container {
            z-index: 20000 !important;
        }
    </style>
    <script>
        function ShowModel(e) {
            e.preventDefault();
            document.getElementById('modals-top').classList.add("show");
        }
    </script>
    <script>
        function ImagePreview(id = 0) {
            console.log(id)
            let qrImages;
            if (id) {
                qrImages = document.getElementById(id);
            } else {
                qrImages = document.getElementById('qr-image');
            }
            console.log(qrImages)
            let viewer = new Viewer(qrImages, {
                url: 'data-original',
                toolbar: {
                    zoomIn: 1,
                    zoomOut: 1,
                    oneToOne: 1,
                    prev: function() {
                        viewer.prev(true);
                    },
                    play: {
                        show: 10,
                        size: 'large',
                    },
                    next: function() {
                        viewer.next(true);
                    },
                    rotateLeft: 1,
                    rotateRight: 1,
                    flipHorizontal: 1,
                    flipVertical: 1,
                },
            });
        }
    </script>
    <script>
    $(document).ready(function () {
        // Dropdown toggle functionality
        $(".dropdown-toggle").click(function (e) {
            e.preventDefault();

            // Close other open dropdowns
            $(".submenu").not($(this).next()).slideUp(300);

            // Toggle current dropdown
            $(this).next(".submenu").slideToggle(300);

            // Toggle active class on parent
            $(this).parent().toggleClass("active");
        });

        // Close dropdown when clicking outside
        $(document).click(function(e) {
            if (!$(e.target).closest('.sidenav-item').length) {
                $(".submenu").slideUp(300);
                $(".sidenav-item").removeClass("active");
            }
        });
    });
</script>


    <style>
        /* .image_scale:hover{
            transform: scale(4.0);
        } */
        #qr-image {
            cursor: pointer;
        }

        @media screen and (min-width:1000px) {
            .logout {
                width: 100%;
            }
        }

        .dropdown-menu {
            max-height: 200px;
            overflow-y: scroll;
        }

        .dt-button {
            outline: none;
            border: none;
            border-radius: 2px;
            padding: 0.375rem 0.25rem;
            min-width: calc(1.5rem + 2px);
            font-size: .75rem;
            border-color: #674CEF !important;
            background-color: #674CEF !important;
            color: #fff !important;
        }
    </style>

    <style>
        /* Custom CSS to highlight focused elements while using tab key */
        /* .form-control:focus,
        .btn:focus {
            border-color: #6c757d;
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.25);
        }

        .btn:focus {
            outline: none;
            border-color: #6c757d;
            box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.5);
        } */
    </style>
    <script>
        // Disable Ctrl+P and print functionality
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                return false;
            }
        });

        // Disable right-click context menu
        // document.addEventListener('contextmenu', function(e) {
        //     e.preventDefault();
        //     return false;
        // });

        // Disable print functionality
        window.onbeforeprint = function() {
            return false;
        };
    </script>
</head>

<body>
    <!-- [ Preloader ] Start -->
    <div class="page-loader">
        <div class="bg-primary"></div>
    </div>
    <!-- [ Preloader ] End -->

    <!-- [ Layout wrapper ] Start -->
    <div class="layout-wrapper layout-2">
        <div class="layout-inner">
            <!-- [ Layout sidenav ] Start -->
            <div id="layout-sidenav" class="layout-sidenav sidenav sidenav-vertical bg-white logo-white">
                <!-- Brand demo (see assets/css/demo/demo.css) -->
    <div class="app-brand demo">
                    <!-- <span class="app-brand-logo demo"> -->
                    <img src="<?= base_url('assets/img/logo.svg') ?>" alt="Brand Logo" width="100px" height="100px">                    </span>
                    <!-- <a href="" class="app-brand-text demo sidenav-text font-weight-normal ml-2">Drillu</a> -->
                    <a href="javascript:" class="layout-sidenav-toggle sidenav-link text-large ml-auto">
                        <i class="ion ion-md-menu align-middle"></i>
                    </a>
                </div>
                <div class="sidenav-divider mt-0"></div>
                <ul class="sidenav-inner py-1 ps ps--active-y">
<?php
    // Get url from controller data, default to 'admin'
    $user = $this->session->userdata('user');

    if (!$user) {
        redirect('Welcome');
    }

    if (is_object($user)) {
        $user = (array) $user;
    }

    $designation = $user['role'];
?>
                    <li class="sidenav-item <?php if ($classname == "home") {
                                                echo "active";
                                            } ?>">
                        <a href="<?= base_url($url.'/dashboard') ?>" class="sidenav-link ">
                            <i class="sidenav-icon feather icon-home"></i>
                            <div>Dashboard</div>
                        </a>
                    </li>
                    <?php if (in_array($designation, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD])): ?>
                        <li class="sidenav-item">
                            <a href="#" class="sidenav-link dropdown-toggle">
                                <i class="sidenav-icon feather icon-settings"></i>
                                <div>Management</div>
                            </a>
                            <ul class="submenu">
                                <li><a href="<?php echo base_url($url.'/management/principal'); ?>" class="<?php if ($classname == "management_principal") echo "active"; ?>">Administrator</a></li>
                                <li><a href="<?php echo base_url($url.'/management/vice_principal'); ?>" class="<?php if ($classname == "management_vice_principal") echo "active"; ?>">Asst Administrator</a></li>
                                <li><a href="<?php echo base_url($url.'/management/hod'); ?>" class="<?php if ($classname == "management_hod") echo "active"; ?>">Dept Administrator</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array($designation, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD])): ?>
                        <li class="sidenav-item">
                            <a href="#" class="sidenav-link dropdown-toggle">
                                <i class="sidenav-icon feather icon-users"></i>
                                <div>Faculty</div>
                            </a>
                            <ul class="submenu">
                                <li><a href="<?php echo base_url($url.'/faculty/instructor'); ?>" class="<?php if ($classname == "faculty_instructor") echo "active"; ?>">Instructor</a></li>
                                <li><a href="<?php echo base_url($url.'/faculty/custodian'); ?>" class="<?php if ($classname == "faculty_custodian") echo "active"; ?>">Custodian</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <!-- System Administration (Principal and Vice-Principal) -->
                    <?php if (in_array($designation, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL])): ?>
                        <li class="sidenav-item <?php if ($classname == "departments") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= base_url($url . "/departments") ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-layers"></i>
                                <div>Departments</div>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array($designation, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD, ROLE_STAFF])): ?>

                        <li class="sidenav-item <?php if ($classname == "students") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= base_url($url . "/students") ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-user"></i>
                                <div>Students</div>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array($designation, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD, ROLE_STAFF])): ?>

                        <li class="sidenav-item <?php if ($classname == "groups") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= base_url($url . "/groups") ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-users"></i>
                                <div>Music Groups</div>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array($designation, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD, ROLE_STAFF])): ?>

                        <li class="sidenav-item <?php if ($classname == "courses") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= base_url($url . "/courses") ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-book"></i>
                                <div>Courses</div>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array($designation, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD, ROLE_STAFF])): ?>

                        <li class="sidenav-item <?php if ($classname == "inventory" || $classname == "inventory_issues" || $classname == "inventory_maintenance" || $classname == "inventory_reports") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= base_url($url . "/inventory") ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-music"></i>
                                <div>Musical Instruments</div>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array($designation, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD, ROLE_STAFF])): ?>
                        <!--
                        <li class="sidenav-item <?php if ($classname == "reports") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= base_url($url . "/report") ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-bar-chart-2"></i>
                                <div>Reports</div>
                            </a>
                        </li>
                        -->
                    <?php endif; ?>

                    <?php if (in_array($designation, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_HOD])): ?>
                        <li class="sidenav-item <?php if ($classname == "announcements") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= base_url($url . "/announcements") ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-bell"></i>
                                <div>Announcements</div>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- <?php 
                    if (in_array($this->session->userdata($url)['role'] ?? ROLE_PRINCIPAL, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL, ROLE_STAFF])): ?>

                        <li class="sidenav-item <?php if ($classname == "groups") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= "$sidebar_href/groups" ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-user-plus"></i>
                                <div>Groups</div>
                            </a>
                        </li>
                    <?php endif; ?> -->




                    

                    
            

                    <?php if (in_array($designation, [ROLE_PRINCIPAL, ROLE_VICE_PRINCIPAL])): ?>
                        <li class="sidenav-item <?php if ($classname == "profile") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= base_url("$url/college/view") ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-settings"></i>
                                <div>Settings Page</div>
                            </a>
                        </li>

                    <?php endif; ?>

                    <!-- Logout Menu Item -->
                    <li class="sidenav-item">
                        <a href="<?= base_url("logout") ?>" class="sidenav-link">
                            <i class="sidenav-icon feather icon-log-out"></i>
                            <div>Logout</div>
                        </a>
                    </li>
                </ul>
            </div>


            <div class="layout-container" style="padding-top:62.2265625px !important">
                <nav class="layout-navbar navbar navbar-expand-lg align-items-lg-center bg-white container-p-x">
                    <a href="" class="navbar-brand app-brand demo d-lg-none py-0 mr-4">
                        <span class="app-brand-logo demo" style="margin-left: 10px;">
                            <img src="<?= base_url('assets/images/logo.svg') ?>" alt="Brand Logo" width="100px" height="70px">
                        </span>
                        <!-- <span class="app-brand-text demo font-weight-normal ml-2">Nutz</span> -->
                    </a>

                    <!-- Sidenav toggle (see assets/css/demo/demo.css) -->
                    <div class="layout-sidenav-toggle navbar-nav d-lg-none align-items-lg-center mr-auto">
                        <a class="nav-item nav-link px-0 mr-lg-4" href="javascript:">
                            <i class="ion ion-md-menu text-large align-middle"></i>
                        </a>
                    </div>
                    <!-- Divider -->
                    <!-- Divider -->
                    <div class="demo-navbar-user nav-item dropdown logout" style="display:flex;justify-content:end;">
                        <!-- <div class="d-inline-flex flex-lg-row-reverse align-items-center align-middle cursor-pointer" onclick=ShowModel() data-toggle="modal" data-target="#modals-top"><i class="feather icon-help-circle text-danger"></i></div> -->
                        <a class="nav-link  dropdown-toggle" href="#" data-toggle="dropdown">
                            <span class="d-inline-flex flex-lg-row-reverse align-items-center align-middle">
                                <!-- <img src="" alt class="d-block ui-w-30 rounded-circle"> -->
                                <span class="px-1 mr-lg-2 ml-2 ml-lg-0"><?php
                                                                            $session_data = $this->session->userdata($url);
                                                                            if ($session_data && isset($session_data['name']) && $session_data['name']) {
                                                                                echo $session_data['name'];
                                                                            } else {
                                                                                echo 'Administrator';
                                                                            }
                                                                        ?></span>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right overflow-hidden">
                            <a href="<?= base_url("$url/logout") ?>" class="dropdown-item">
                                <i class="feather icon-power text-danger"></i> &nbsp; Log Out
                            </a>
                        </div>
                    </div>
                </nav>
                <div class="modal modal-top fade" id="modals-top">
                    <div class="modal-dialog">
                        <form class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Shortcuts Keys
                                    <!-- <span class="font-weight-light">Information</span> -->
                                    <br>
                                    <!-- <small class="text-muted">We need payment information to process your order.</small> -->
                                </h5>
                                <!-- <button type="button" class="close" data-dismiss="modal" aria-label="Close">×</button> -->
                            </div>
                            <div class="modal-body">

                                <div class="form-row">
                                    <div class="form-group col-12 mb-2">
                                        <span><strong>Shift+RightArrow : </strong>
                                            Add single product and move to next product </span>
                                    </div>
                                    <div class="form-group col-12 mb-2">
                                        <span><strong>Shift+DownArrow : </strong>
                                            Move to Bill to Details </span>
                                    </div>
                                    <div class="form-group col-12 mb-2">
                                        <span><strong>Shift+Enter : </strong>
                                            Print Bill</span>
                                    </div>
                                    <div class="form-group col-12 mb-0">
                                        <span><strong>Esc : </strong>
                                            To close the Print Bill page</span>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>

<!-- DataTables JS & Buttons -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="<?= base_url('') ?>assets/faculty/js/datatables-config.js"></script>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- DataTables Export Filename Modal -->
<div class="modal fade" id="exportFilenameModal" tabindex="-1" role="dialog" aria-labelledby="exportFilenameModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportFilenameModalLabel">Export Data</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="exportFilename" class="form-label">Enter filename for export:</label>
                    <input type="text" class="form-control" id="exportFilename" placeholder="Enter filename...">
                    <small class="form-text text-muted">Leave blank to use default filename</small>
                </div>
                <div class="form-group mt-3">
                    <label for="exportTitle" class="form-label d-none" id="exportTitleLabel">Enter title for print view:</label>
                    <input type="text" class="form-control d-none" id="exportTitle" placeholder="Enter title...">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmExportBtn">Export</button>
            </div>
        </div>
    </div>
</div>