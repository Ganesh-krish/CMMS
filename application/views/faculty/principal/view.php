<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Principal Dashboard</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item active">Dashboard</li>
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
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <h4 class="text-primary mb-1"><?php echo $value; ?></h4>
                                <p class="mb-0 text-muted"><?php echo ucwords(str_replace('_', ' ', $key)); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo base_url($url.'/departments'); ?>" class="btn btn-outline-primary btn-block">
                                    <i class="feather icon-layers"></i><br>
                                    Manage Departments
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo base_url($url.'/report'); ?>" class="btn btn-outline-success btn-block">
                                    <i class="feather icon-bar-chart"></i><br>
                                    View Reports
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo base_url($url.'/announcements'); ?>" class="btn btn-outline-info btn-block">
                                    <i class="feather icon-bell"></i><br>
                                    Announcements
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="<?php echo base_url($url.'/students'); ?>" class="btn btn-outline-warning btn-block">
                                    <i class="feather icon-users"></i><br>
                                    Manage Students
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity or Summary -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>System Overview</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Welcome to your principal dashboard. Use the quick actions above to manage your institution effectively.</p>
                        <div class="alert alert-info">
                            <strong>Tip:</strong> Use the Management dropdown in the sidebar to access administrator management features.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>