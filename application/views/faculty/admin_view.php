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

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <?php if (isset($stats)): ?>
                <?php foreach ($stats as $key => $value): ?>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="text-primary"><?php echo $value; ?></h4>
                                <p class="mb-0"><?php echo ucwords(str_replace('_', ' ', $key)); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Role-specific content -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5><?php echo $title; ?> Details</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($show_admin_management) && $show_admin_management): ?>
                            <!-- Administrator Management content -->
                            <div class="alert alert-primary">
                                <h6>Administrator Management</h6>
                                <p>Manage system administrators and administrative settings.</p>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Administrator Actions:</h6>
                                    <ul>
                                        <li><a href="<?php echo base_url($url.'/departments'); ?>">Manage Departments</a></li>
                                        <li><a href="<?php echo base_url($url.'/manage_staff'); ?>">Manage Staff</a></li>
                                        <li><a href="<?php echo base_url($url.'/add_hod'); ?>">Add HOD</a></li>
                                        <li><a href="<?php echo base_url($url.'/college/edit/1'); ?>">College Settings</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>System Management:</h6>
                                    <ul>
                                        <li><a href="<?php echo base_url($url.'/report'); ?>">System Reports</a></li>
                                        <li><a href="<?php echo base_url($url.'/students'); ?>">Student Management</a></li>
                                        <li><a href="<?php echo base_url($url.'/system_courses'); ?>">System Courses</a></li>
                                    </ul>
                                </div>
                            </div>
                        <?php elseif (isset($show_full_admin) && $show_full_admin): ?>
                            <!-- Full admin content -->
                            <div class="alert alert-info">
                                <h6>Administrative Overview</h6>
                                <p>You have full administrative access to view and manage all system data.</p>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Quick Actions:</h6>
                                    <ul>
                                        <li><a href="<?php echo base_url($url.'/departments'); ?>">Manage Departments</a></li>
                                        <li><a href="<?php echo base_url($url.'/manage_staff'); ?>">Manage Staff</a></li>
                                        <li><a href="<?php echo base_url($url.'/add_hod'); ?>">Add HOD</a></li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Reports:</h6>
                                    <ul>
                                        <li><a href="<?php echo base_url($url.'/report'); ?>">System Reports</a></li>
                                        <li><a href="<?php echo base_url($url.'/students'); ?>">Student Overview</a></li>
                                    </ul>
                                </div>
                            </div>
                        <?php elseif (isset($show_department_admin) && $show_department_admin): ?>
                            <!-- Department admin content -->
                            <div class="alert alert-warning">
                                <h6>Department Administration</h6>
                                <p>You have administrative access to your department.</p>
                            </div>
                        <?php elseif (isset($show_staff_view) && $show_staff_view): ?>
                            <!-- Staff content -->
                            <div class="alert alert-success">
                                <h6>Staff Dashboard</h6>
                                <p>Welcome to your staff dashboard.</p>
                            </div>
                        <?php else: ?>
                            <!-- Default content -->
                            <div class="text-center py-4">
                                <p>Dashboard overview content will appear here based on your role and permissions.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>