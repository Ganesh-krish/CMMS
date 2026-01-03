<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0"><?php echo $title; ?></h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item active"><?php echo $title; ?></li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Inventory Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="feather icon-music text-primary mr-2" style="font-size: 24px;"></i>
                            <h4 class="text-primary mb-0"><?php echo isset($total_instruments) ? $total_instruments : 0; ?></h4>
                        </div>
                        <p class="mb-0 text-muted">Total Instruments</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="feather icon-check-circle text-success mr-2" style="font-size: 24px;"></i>
                            <h4 class="text-success mb-0"><?php echo isset($available_instruments) ? $available_instruments : 0; ?></h4>
                        </div>
                        <p class="mb-0 text-muted">Available</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="feather icon-send text-warning mr-2" style="font-size: 24px;"></i>
                            <h4 class="text-warning mb-0"><?php echo isset($issued_instruments) ? $issued_instruments : 0; ?></h4>
                        </div>
                        <p class="mb-0 text-muted">Currently Issued</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Announcements -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Announcements</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($recent_announcements)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($recent_announcements as $announcement): ?>
                                    <div class="list-group-item px-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1"><?php echo htmlspecialchars($announcement['title']); ?></h6>
                                                <p class="mb-1 text-muted small"><?php echo htmlspecialchars(substr($announcement['message'], 0, 100)); ?><?php echo strlen($announcement['message']) > 100 ? '...' : ''; ?></p>
                                                <small class="text-muted">
                                                    <?php echo date('M d, Y', strtotime($announcement['created_at'])); ?>
                                                    <?php if ($announcement['visibility'] == 'department'): ?>
                                                        <span class="badge badge-info">Department</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-success">Public</span>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="feather icon-bell-off" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="text-muted mt-2">No announcements available</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <a href="<?php echo base_url($url.'/inventory'); ?>" class="btn btn-primary btn-block">
                                    <i class="feather icon-music"></i> Manage Instruments
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="<?php echo base_url($url.'/announcements'); ?>" class="btn btn-info btn-block">
                                    <i class="feather icon-bell"></i> View Announcements
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
