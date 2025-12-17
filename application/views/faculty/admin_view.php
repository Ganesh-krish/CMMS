<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0"><?php echo $title; ?></h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active"><?php echo $title; ?></li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Statistics Cards - 8 Total -->
        <div class="row mb-4">
            <!-- Total Administrators Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0 text-danger"><?php echo isset($total_administrators) ? $total_administrators : 0; ?></h4>
                                <p class="text-muted mb-0">Total Administrators</p>
                            </div>
                            <div class="bg-danger rounded p-3">
                                <i class="feather icon-user text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Asst Administrators Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0 text-warning"><?php echo isset($total_asst_administrators) ? $total_asst_administrators : 0; ?></h4>
                                <p class="text-muted mb-0">Total Asst Administrators</p>
                            </div>
                            <div class="bg-warning rounded p-3">
                                <i class="feather icon-user-check text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Dept Administrators Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0 text-info"><?php echo isset($total_dept_administrators) ? $total_dept_administrators : 0; ?></h4>
                                <p class="text-muted mb-0">Total Dept Administrators</p>
                            </div>
                            <div class="bg-info rounded p-3">
                                <i class="feather icon-settings text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Faculty Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0 text-success"><?php echo isset($total_faculty) ? $total_faculty : 0; ?></h4>
                                <p class="text-muted mb-0">Total Faculty</p>
                            </div>
                            <div class="bg-success rounded p-3">
                                <i class="feather icon-users text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Custodians Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0 text-primary"><?php echo isset($total_custodians) ? $total_custodians : 0; ?></h4>
                                <p class="text-muted mb-0">Total Custodians</p>
                            </div>
                            <div class="bg-primary rounded p-3">
                                <i class="feather icon-package text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Students Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0 text-secondary"><?php echo isset($total_students) ? $total_students : 0; ?></h4>
                                <p class="text-muted mb-0">Total Students</p>
                            </div>
                            <div class="bg-secondary rounded p-3">
                                <i class="feather icon-users text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Departments Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0 text-dark"><?php echo isset($total_departments) ? $total_departments : 0; ?></h4>
                                <p class="text-muted mb-0">Total Departments</p>
                            </div>
                            <div class="bg-dark rounded p-3">
                                <i class="feather icon-layers text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Courses Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0 text-warning"><?php echo isset($total_courses) ? $total_courses : 0; ?></h4>
                                <p class="text-muted mb-0">Total Courses</p>
                            </div>
                            <div class="bg-warning rounded p-3">
                                <i class="feather icon-book text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Analysis Section -->
        <div class="row mb-4">
            <div class="col-lg-8 col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">System Analysis</h5>
                    </div>
                    <div class="card-body">
                        <!-- User Distribution Chart -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-3">User Distribution</h6>
                            <div class="chart-container">
                                <?php
                                $admin_count = isset($total_administrators) ? $total_administrators : 0;
                                $asst_admin_count = isset($total_asst_administrators) ? $total_asst_administrators : 0;
                                $dept_admin_count = isset($total_dept_administrators) ? $total_dept_administrators : 0;
                                $faculty_count = isset($total_faculty) ? $total_faculty : 0;
                                $custodian_count = isset($total_custodians) ? $total_custodians : 0;
                                $student_count = isset($total_students) ? $total_students : 0;

                                $total_users = $admin_count + $asst_admin_count + $dept_admin_count + $faculty_count + $custodian_count + $student_count;
                                if ($total_users > 0) {
                                    $admin_percent = round(($admin_count / $total_users) * 100);
                                    $asst_admin_percent = round(($asst_admin_count / $total_users) * 100);
                                    $dept_admin_percent = round(($dept_admin_count / $total_users) * 100);
                                    $faculty_percent = round(($faculty_count / $total_users) * 100);
                                    $custodian_percent = round(($custodian_count / $total_users) * 100);
                                    $student_percent = round(($student_count / $total_users) * 100);
                                } else {
                                    $admin_percent = $asst_admin_percent = $dept_admin_percent = $faculty_percent = $custodian_percent = $student_percent = 0;
                                }
                                ?>
                                <div class="user-distribution">
                                    <div class="distribution-item">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-danger small">Administrators</span>
                                            <span class="text-danger small"><?php echo $admin_count; ?> (<?php echo $admin_percent; ?>%)</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-danger" style="width: <?php echo $admin_percent; ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="distribution-item">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-warning small">Asst Administrators</span>
                                            <span class="text-warning small"><?php echo $asst_admin_count; ?> (<?php echo $asst_admin_percent; ?>%)</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-warning" style="width: <?php echo $asst_admin_percent; ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="distribution-item">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-info small">Dept Administrators</span>
                                            <span class="text-info small"><?php echo $dept_admin_count; ?> (<?php echo $dept_admin_percent; ?>%)</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-info" style="width: <?php echo $dept_admin_percent; ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="distribution-item">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-success small">Faculty</span>
                                            <span class="text-success small"><?php echo $faculty_count; ?> (<?php echo $faculty_percent; ?>%)</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: <?php echo $faculty_percent; ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="distribution-item">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-primary small">Custodians</span>
                                            <span class="text-primary small"><?php echo $custodian_count; ?> (<?php echo $custodian_percent; ?>%)</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" style="width: <?php echo $custodian_percent; ?>%"></div>
                                        </div>
                                    </div>
                                    <div class="distribution-item">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-secondary small">Students</span>
                                            <span class="text-secondary small"><?php echo $student_count; ?> (<?php echo $student_percent; ?>%)</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-secondary" style="width: <?php echo $student_percent; ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Key Metrics -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="metric-card">
                                    <h6 class="text-muted">Student-to-Faculty Ratio</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="ratio-display mr-3">
                                            <span class="ratio-number">
                                                <?php
                                                $faculty_ratio_count = isset($total_faculty) ? $total_faculty : 1;
                                                $student_ratio_count = isset($total_students) ? $total_students : 0;
                                                $ratio = $faculty_ratio_count > 0 ? round($student_ratio_count / $faculty_ratio_count, 1) : 0;
                                                echo $ratio;
                                                ?>
                                            </span>
                                            <span class="ratio-label">:1</span>
                                        </div>
                                        <div class="ratio-bar">
                                            <div class="ratio-fill" style="height: <?php echo min($ratio * 10, 100); ?>%"></div>
                                        </div>
                                    </div>
                                    <p class="text-muted small mt-1">Students per faculty member</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="metric-card">
                                    <h6 class="text-muted">Courses per Department</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="ratio-display mr-3">
                                            <span class="ratio-number">
                                                <?php
                                                $dept_ratio_count = isset($total_departments) ? $total_departments : 1;
                                                $course_ratio_count = isset($total_courses) ? $total_courses : 0;
                                                $avg_courses = $dept_ratio_count > 0 ? round($course_ratio_count / $dept_ratio_count, 1) : 0;
                                                echo $avg_courses;
                                                ?>
                                            </span>
                                        </div>
                                        <div class="ratio-bar">
                                            <div class="ratio-fill bg-success" style="height: <?php echo min($avg_courses * 20, 100); ?>%"></div>
                                        </div>
                                    </div>
                                    <p class="text-muted small mt-1">Average courses per department</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="<?php echo base_url($url.'/departments'); ?>" class="btn btn-outline-primary btn-sm">
                                <i class="feather icon-layers"></i> Manage Departments
                            </a>
                            <a href="<?php echo base_url($url.'/report'); ?>" class="btn btn-outline-success btn-sm">
                                <i class="feather icon-bar-chart"></i> View Reports
                            </a>
                            <a href="<?php echo base_url($url.'/students'); ?>" class="btn btn-outline-info btn-sm">
                                <i class="feather icon-users"></i> Student Management
                            </a>
                            <a href="<?php echo base_url($url.'/college/edit/1'); ?>" class="btn btn-outline-warning btn-sm">
                                <i class="feather icon-settings"></i> College Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>