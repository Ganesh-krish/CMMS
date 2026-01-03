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
            <!-- Total Administrators Card - Only for Principals -->
            <?php if (isset($show_full_admin) && $show_full_admin): ?>
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
            <?php endif; ?>

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
                    <div class="card-body p-4">
                        <?php
                        // Prepare chart data
                        $admin_count = isset($total_administrators) ? $total_administrators : 0;
                        $asst_admin_count = isset($total_asst_administrators) ? $total_asst_administrators : 0;
                        $dept_admin_count = isset($total_dept_administrators) ? $total_dept_administrators : 0;
                        $faculty_count = isset($total_faculty) ? $total_faculty : 0;
                        $custodian_count = isset($total_custodians) ? $total_custodians : 0;
                        $student_count = isset($total_students) ? $total_students : 0;
                        $department_count = isset($total_departments) ? $total_departments : 0;
                        $course_count = isset($total_courses) ? $total_courses : 0;
                        ?>

                        <!-- User Distribution Pie Chart -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-4">User Distribution</h6>
                            <div class="chart-container" style="height: 350px; max-width: 600px; margin: 0 auto;">
                                <canvas id="userDistributionChart"></canvas>
                            </div>
                        </div>

                        <!-- System Statistics Bar Chart -->
                        <div class="mb-4">
                            <h6 class="text-muted mb-4">System Statistics</h6>
                            <div class="chart-container" style="height: 300px;">
                                <canvas id="systemStatsChart"></canvas>
                            </div>
                        </div>

                        <!-- Statistics Summary Cards -->

                        <!-- Key Metrics Row -->
                        <!-- <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="bg-light p-4 rounded">
                                    <h6 class="text-muted mb-3">Student-to-Faculty Ratio</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-4">
                                            <span class="h3 text-primary">
                                                <?php
                                                $faculty_ratio_count = $faculty_count > 0 ? $faculty_count : 1;
                                                $ratio = round($student_count / $faculty_ratio_count, 1);
                                                echo $ratio;
                                                ?>
                                            </span>
                                            <span class="text-muted">:1</span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <small class="text-muted">Students per faculty member</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light p-4 rounded">
                                    <h6 class="text-muted mb-3">Courses per Department</h6>
                                    <div class="d-flex align-items-center">
                                        <div class="mr-4">
                                            <span class="h3 text-success">
                                                <?php
                                                $dept_ratio_count = $department_count > 0 ? $department_count : 1;
                                                $avg_courses = round($course_count / $dept_ratio_count, 1);
                                                echo $avg_courses;
                                                ?>
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <small class="text-muted">Average courses per department</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-3">
                            <!-- Course Management -->
                            <a href="<?php echo base_url($url.'/courses'); ?>" class="btn btn-outline-primary btn-lg p-4">
                                <i class="feather icon-book mr-3" style="font-size: 24px;"></i>
                                Manage Courses
                            </a>

                            <a href="<?php echo base_url($url.'/course/new'); ?>" class="btn btn-outline-info btn-lg p-4">
                                <i class="feather icon-plus-circle mr-3" style="font-size: 24px;"></i>
                                Add New Course
                            </a>

                            <!-- User Management -->
                            <a href="<?php echo base_url($url.'/students'); ?>" class="btn btn-outline-info btn-lg p-4">
                                <i class="feather icon-users mr-3" style="font-size: 24px;"></i>
                                Student Management
                            </a>

                            <a href="<?php echo base_url($url.'/faculty/instructor'); ?>" class="btn btn-outline-secondary btn-lg p-4">
                                <i class="feather icon-user-check mr-3" style="font-size: 24px;"></i>
                                Faculty Management
                            </a>

                            <!-- Communication & Organization -->
                            <a href="<?php echo base_url($url.'/announcements'); ?>" class="btn btn-outline-success btn-lg p-4">
                                <i class="feather icon-bell mr-3" style="font-size: 24px;"></i>
                                Announcements
                            </a>

                            <a href="<?php echo base_url($url.'/groups'); ?>" class="btn btn-outline-warning btn-lg p-4">
                                <i class="feather icon-users mr-3" style="font-size: 24px;"></i>
                                Student Groups
                            </a>

                            <!-- Resources & Administration -->
                            <a href="<?php echo base_url($url.'/departments'); ?>" class="btn btn-outline-dark btn-lg p-4">
                                <i class="feather icon-layers mr-3" style="font-size: 24px;"></i>
                                Manage Departments
                            </a>

                            <a href="<?php echo base_url($url.'/inventory'); ?>" class="btn btn-outline-danger btn-lg p-4">
                                <i class="feather icon-package mr-3" style="font-size: 24px;"></i>
                                Inventory
                            </a>

                            <!-- Settings -->
                            <a href="<?php echo base_url($url.'/college/edit/1'); ?>" class="btn btn-outline-warning btn-lg p-4">
                                <i class="feather icon-settings mr-3" style="font-size: 24px;"></i>
                                College Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

<script>
// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    // User Distribution Pie Chart
    const userDistributionCtx = document.getElementById('userDistributionChart');
    if (userDistributionCtx) {
        const userData = {
            labels: ['Administrators', 'Asst Administrators', 'Dept Administrators', 'Faculty', 'Custodians', 'Students'],
            datasets: [{
                data: [
                    <?php echo $admin_count; ?>,
                    <?php echo $asst_admin_count; ?>,
                    <?php echo $dept_admin_count; ?>,
                    <?php echo $faculty_count; ?>,
                    <?php echo $custodian_count; ?>,
                    <?php echo $student_count; ?>
                ],
                backgroundColor: [
                    '#dc3545', // danger
                    '#ffc107', // warning
                    '#17a2b8', // info
                    '#28a745', // success
                    '#007bff', // primary
                    '#6c757d'  // secondary
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        };

        new Chart(userDistributionCtx, {
            type: 'pie',
            data: userData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    },
                    datalabels: {
                        color: '#ffffff',
                        font: {
                            weight: 'bold',
                            size: 14
                        },
                        formatter: function(value, context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return percentage > 5 ? percentage + '%' : '';
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    }

    // System Statistics Bar Chart
    const systemStatsCtx = document.getElementById('systemStatsChart');
    if (systemStatsCtx) {
        const systemData = {
            labels: ['Administrators', 'Faculty', 'Custodians', 'Students', 'Departments', 'Courses'],
            datasets: [{
                label: 'Count',
                data: [
                    <?php echo $admin_count + $asst_admin_count + $dept_admin_count; ?>,
                    <?php echo $faculty_count; ?>,
                    <?php echo $custodian_count; ?>,
                    <?php echo $student_count; ?>,
                    <?php echo $department_count; ?>,
                    <?php echo $course_count; ?>
                ],
                backgroundColor: [
                    '#dc3545',
                    '#28a745',
                    '#007bff',
                    '#6c757d',
                    '#17a2b8',
                    '#ffc107'
                ],
                borderColor: [
                    '#c82333',
                    '#218838',
                    '#0056b3',
                    '#545b62',
                    '#138496',
                    '#e0a800'
                ],
                borderWidth: 1
            }]
        };

        new Chart(systemStatsCtx, {
            type: 'bar',
            data: systemData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
});
</script>