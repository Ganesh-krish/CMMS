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
    <link rel="stylesheet" href="<?= base_url('') ?>assets/faculty/libs/datatables/datatables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.7.0/css/select.dataTables.min.css">
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
        background-color: #673AB7; /* Change color on hover */
    }

    /* Submenu Styling */
    .submenu {
        display: none; /* Hide by default */
        list-style: none;
        padding-left: 20px;
        background-color: #4A2FBD; /* Change submenu background */
        border-left: 3px solid #fff;
    }

    .submenu li {
        padding: 8px 10px;
    }

    .submenu li a {
        color: #ddd;
        text-decoration: none;
    }

    .submenu li a:hover {
        color: #fff;
        font-weight: bold;
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
        $(".sidenav-link").click(function () {
            $(this).next(".submenu").slideToggle(300); // Smooth dropdown effect
            $(this).find(".arrow-icon").toggleClass("rotate"); // Rotate arrow
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
    $url = $url ?? 'admin';
    $designation = $this->session->userdata($url)['role'] ?? ROLE_SUPERADMIN;
    $fallbackHref = base_url($url ? "$url/principal" : "Dashboard");
?>
                    <li class="sidenav-item <?php if ($classname == "home") {
                                                echo "active";
                                            } ?>">
                        <a href="<?= $sidebar_href ?? $fallbackHref ?>" class="sidenav-link ">
                            <i class="sidenav-icon feather icon-home"></i>
                            <div>Dashboard</div>
                        </a>
                    </li>
                    <?php if (in_array($designation, [ROLE_SUPERADMIN])): ?>
                        <li class="sidenav-item <?php if ($classname == "principal") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= base_url($url . "/principal/view") ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-user-check"></i>
                                <div>Administrator</div>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array($designation, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL, ROLE_HOD])): ?>
                        <li class="sidenav-item <?php if ($classname == "staff") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= base_url($url . "/principal/staff") ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-users"></i>
                                <div>Instructor</div>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array($designation, [ROLE_SUPERADMIN])): ?>
                        <li class="sidenav-item <?php if ($classname == "departments") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= base_url($url . "/principal/departments") ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-home"></i>
                                <div>Departments</div>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array($designation, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL, ROLE_HOD, ROLE_STAFF])): ?>

                        <li class="sidenav-item <?php if ($classname == "courses") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= base_url($url . "/courses") ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-book"></i>
                                <div>Courses</div>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array($designation, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL, ROLE_HOD, ROLE_STAFF])): ?>

                        <li class="sidenav-item <?php if ($classname == "inventory" || $classname == "inventory_issues" || $classname == "inventory_maintenance" || $classname == "inventory_reports") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= base_url($url . "/inventory") ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-music"></i>
                                <div>Musical Instruments</div>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array($designation, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL, ROLE_HOD, ROLE_STAFF])): ?>
                        <li class="sidenav-item <?php if ($classname == "reports") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= base_url($url . "/report") ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-bar-chart-2"></i>
                                <div>Reports</div>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (in_array($designation, [ROLE_SUPERADMIN, ROLE_VICE_PRINCIPAL, ROLE_HOD])): ?>
                        <li class="sidenav-item <?php if ($classname == "announcements") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= base_url($url . "/announcements") ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-bell"></i>
                                <div>Announcements</div>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (in_array($designation, [ROLE_SUPERADMIN, ROLE_ADMIN, ROLE_STAFF])): ?>

                        <li class="sidenav-item <?php if ($classname == "students") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= "$sidebar_href/students" ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-user"></i>
                                <div>Learner</div>
                            </a>
                        </li>
                    <?php endif; ?>

                    <!-- <?php 
                    if (in_array($this->session->userdata($url)['role'] ?? ROLE_SUPERADMIN, [ROLE_SUPERADMIN, ROLE_ADMIN, ROLE_STAFF])): ?>

                        <li class="sidenav-item <?php if ($classname == "groups") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= "$sidebar_href/groups" ?>" class="sidenav-link ">
                                <i class="sidenav-icon feather icon-user-plus"></i>
                                <div>Groups</div>
                            </a>
                        </li>
                    <?php endif; ?> -->




                    

                    
                    <!-- <?php 
                    if (in_array($this->session->userdata($url)['role'] ?? ROLE_SUPERADMIN, [ROLE_SUPERADMIN, ROLE_ADMIN, ROLE_STAFF])): ?>

                        <li class="sidenav-item <?php if ($classname == "questions") {
                                                    echo "active";
                                                } ?>">
                            <a href="#questionBankSubmenu" class="sidenav-link" data-toggle="collapse" aria-expanded="false">
                                <i class="sidenav-icon feather icon-users"></i>
                                <div>Question Bank</div>
                            </a>
                            <ul class="collapse list-unstyled" id="questionBankSubmenu">
                                <li class="sidenav-item">
                                    <a href="<?= "$sidebar_href/questions" ?>" class="sidenav-link">All Questions</a>
                                </li>
                                <li class="sidenav-item">
                                    <a href="<?= "$sidebar_href/topics" ?>" class="sidenav-link">Topics</a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?> -->

                    <?php if (in_array($designation, [ROLE_SUPERADMIN])): ?>
                        <li class="sidenav-item <?php if ($classname == "profile") {
                                                    echo "active";
                                                } ?>">
                            <a href="<?= base_url("$url/principal/profile") ?>" class="sidenav-link ">
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