<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Staff Dashboard</h4>
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
                        <h5>Teaching Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="<?php echo base_url($url.'/courses'); ?>" class="btn btn-outline-primary btn-block">
                                    <i class="feather icon-book"></i><br>
                                    My Courses
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="<?php echo base_url($url.'/students'); ?>" class="btn btn-outline-success btn-block">
                                    <i class="feather icon-users"></i><br>
                                    My Students
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="<?php echo base_url($url.'/test'); ?>" class="btn btn-outline-info btn-block">
                                    <i class="feather icon-file-text"></i><br>
                                    Assessments
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff Info -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Teaching Overview</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Welcome to your staff dashboard. Access your courses, manage students, and create assessments.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>