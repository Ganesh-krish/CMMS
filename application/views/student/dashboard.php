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