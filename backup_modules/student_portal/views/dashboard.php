<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Student Dashboard</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-1">Welcome back, <?php echo htmlspecialchars($student['name']); ?>!</h4>
                                <p class="mb-0 opacity-75">Here's what's happening in your student portal today.</p>
                            </div>
                            <div class="col-md-4 text-center">
                                <i class="fas fa-graduation-cap" style="font-size: 4rem; opacity: 0.3;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <!-- Enrolled Courses -->
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="feather icon-book text-primary mr-2" style="font-size: 24px;"></i>
                            <h4 class="text-primary mb-0">
                                <?php
                                // Get enrolled courses count
                                $this->db->where('student_id', $student['id']);
                                $this->db->where('status !=', 'dropped');
                                echo $this->db->count_all_results('course_enrollments');
                                ?>
                            </h4>
                        </div>
                        <p class="mb-0 text-muted">Enrolled Courses</p>
                    </div>
                </div>
            </div>

            <!-- Available Instruments -->
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="feather icon-music text-success mr-2" style="font-size: 24px;"></i>
                            <h4 class="text-success mb-0">
                                <?php
                                // Get available instruments count
                                $this->db->where('college_id', $college['id']);
                                $this->db->where('is_active', 1);
                                $this->db->where('availability_status', 'available');
                                echo $this->db->count_all_results('instruments');
                                ?>
                            </h4>
                        </div>
                        <p class="mb-0 text-muted">Available Instruments</p>
                    </div>
                </div>
            </div>

            <!-- Announcements -->
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="feather icon-bell text-warning mr-2" style="font-size: 24px;"></i>
                            <h4 class="text-warning mb-0">
                                <?php
                                // Get announcements count (you may need to adjust this based on your announcements table)
                                echo $this->db->count_all_results('announcements');
                                ?>
                            </h4>
                        </div>
                        <p class="mb-0 text-muted">Announcements</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Analytics -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Student Analytics</h5>
                    </div>
                    <div class="card-body p-4">
                        <?php
                        // Get course enrollment data for pie chart
                        $enrolled_count = $this->db->where('student_id', $student['id'])
                                                  ->where('status', 'enrolled')
                                                  ->count_all_results('course_enrollments');

                        $completed_count = $this->db->where('student_id', $student['id'])
                                                   ->where('status', 'completed')
                                                   ->count_all_results('course_enrollments');

                        $in_progress_count = $this->db->where('student_id', $student['id'])
                                                     ->where('status', 'in_progress')
                                                     ->count_all_results('course_enrollments');

                        $dropped_count = $this->db->where('student_id', $student['id'])
                                                 ->where('status', 'dropped')
                                                 ->count_all_results('course_enrollments');

                        // Get instrument data for bar chart
                        $total_instruments = $this->db->where('college_id', $college['id'])
                                                    ->where('is_active', 1)
                                                    ->count_all_results('instruments');

                        $available_instruments = $this->db->where('college_id', $college['id'])
                                                        ->where('is_active', 1)
                                                        ->where('availability_status', 'available')
                                                        ->count_all_results('instruments');

                        $issued_instruments = $this->db->where('college_id', $college['id'])
                                                     ->where('is_active', 1)
                                                     ->where('availability_status', 'issued')
                                                     ->count_all_results('instruments');
                        ?>

                        <div class="row">
                            <!-- Course Enrollment Status Pie Chart -->
                            <div class="col-lg-6 col-md-12 mb-4">
                                <h6 class="text-muted mb-4">Course Enrollment Status</h6>
                                <div class="chart-container" style="height: 300px;">
                                    <canvas id="enrollmentStatusChart"></canvas>
                                </div>
                            </div>

                            <!-- Instrument Availability Bar Chart -->
                            <div class="col-lg-6 col-md-12 mb-4">
                                <h6 class="text-muted mb-4">Instrument Availability</h6>
                                <div class="chart-container" style="height: 300px;">
                                    <canvas id="instrumentAvailabilityChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Statistics Summary -->
                        <div class="row mt-4">
                            <div class="col-md-3 mb-3">
                                <div class="text-center p-3 bg-success text-white rounded">
                                    <h4 class="mb-1"><?php echo $enrolled_count; ?></h4>
                                    <small>Enrolled</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="text-center p-3 bg-info text-white rounded">
                                    <h4 class="mb-1"><?php echo $completed_count; ?></h4>
                                    <small>Completed</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="text-center p-3 bg-warning text-white rounded">
                                    <h4 class="mb-1"><?php echo $in_progress_count; ?></h4>
                                    <small>In Progress</small>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="text-center p-3 bg-primary text-white rounded">
                                    <h4 class="mb-1"><?php echo $available_instruments; ?></h4>
                                    <small>Available Instruments</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo base_url('student-portal/courses'); ?>" class="btn btn-primary btn-block p-3">
                                    <i class="feather icon-book mb-2" style="font-size: 24px;"></i>
                                    <div>My Courses</div>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo base_url('student-portal/inventory'); ?>" class="btn btn-success btn-block p-3">
                                    <i class="feather icon-music mb-2" style="font-size: 24px;"></i>
                                    <div>Music Inventory</div>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo base_url('student-portal/announcements'); ?>" class="btn btn-warning btn-block p-3">
                                    <i class="feather icon-bell mb-2" style="font-size: 24px;"></i>
                                    <div>Announcements</div>
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo base_url('student-portal/logout'); ?>" class="btn btn-danger btn-block p-3" onclick="return confirm('Are you sure you want to logout?')">
                                    <i class="feather icon-log-out mb-2" style="font-size: 24px;"></i>
                                    <div>Logout</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity or Quick Info -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Getting Started</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="feather icon-book-open text-primary" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3">Browse Courses</h5>
                                    <p class="text-muted">Explore your enrolled courses and access learning materials.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="feather icon-music text-success" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3">Music Inventory</h5>
                                    <p class="text-muted">View available musical instruments for practice and performance.</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <i class="feather icon-bell text-warning" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3">Stay Updated</h5>
                                    <p class="text-muted">Check announcements for important updates and notices.</p>
                                </div>
                            </div>
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
    // Course Enrollment Status Pie Chart
    const enrollmentCtx = document.getElementById('enrollmentStatusChart');
    if (enrollmentCtx) {
        const enrollmentData = {
            labels: ['Enrolled', 'Completed', 'In Progress', 'Dropped'],
            datasets: [{
                data: [
                    <?php echo $enrolled_count; ?>,
                    <?php echo $completed_count; ?>,
                    <?php echo $in_progress_count; ?>,
                    <?php echo $dropped_count; ?>
                ],
                backgroundColor: [
                    '#28a745', // success
                    '#17a2b8', // info
                    '#ffc107', // warning
                    '#dc3545'  // danger
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        };

        new Chart(enrollmentCtx, {
            type: 'pie',
            data: enrollmentData,
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
                            size: 12
                        },
                        formatter: function(value, context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                            return percentage > 0 ? percentage + '%' : '';
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
    }

    // Instrument Availability Bar Chart
    const instrumentCtx = document.getElementById('instrumentAvailabilityChart');
    if (instrumentCtx) {
        const instrumentData = {
            labels: ['Available', 'Issued', 'Total'],
            datasets: [{
                label: 'Instruments',
                data: [
                    <?php echo $available_instruments; ?>,
                    <?php echo $issued_instruments; ?>,
                    <?php echo $total_instruments; ?>
                ],
                backgroundColor: [
                    '#28a745', // success
                    '#ffc107', // warning
                    '#17a2b8'  // info
                ],
                borderColor: [
                    '#218838', // success border
                    '#e0a800', // warning border
                    '#138496'  // info border
                ],
                borderWidth: 1
            }]
        };

        new Chart(instrumentCtx, {
            type: 'bar',
            data: instrumentData,
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