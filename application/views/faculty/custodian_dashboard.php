<!-- faculty/custodian_dashboard.php -->
<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Custodian Dashboard</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
        
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Stats Cards Row -->
        <div class="row mb-4">
            <!-- Total Instruments Card -->
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= isset($total_instruments) ? $total_instruments : 0 ?></h4>
                                <p class="text-muted mb-0">Total Instruments</p>
                            </div>
                            <div class="bg-primary rounded p-3">
                                <i class="feather icon-package text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Available Instruments Card -->
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= isset($available_instruments) ? $available_instruments : 0 ?></h4>
                                <p class="text-muted mb-0">Available Instruments</p>
                            </div>
                            <div class="bg-success rounded p-3">
                                <i class="feather icon-check-circle text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Issued Instruments Card -->
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= isset($issued_instruments) ? $issued_instruments : 0 ?></h4>
                                <p class="text-muted mb-0">Issued Instruments</p>
                            </div>
                            <div class="bg-warning rounded p-3">
                                <i class="feather icon-share text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Announcements -->
        <div class="row mb-4">
            <div class="col-lg-12 col-md-12">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-header-title mb-0">Recent Announcements</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($recent_announcements)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_announcements as $announcement): ?>
                                            <tr>
                                                <td><strong><?= htmlspecialchars($announcement['title'] ?? 'N/A') ?></strong></td>
                                                <td><?= htmlspecialchars(substr($announcement['description'] ?? '', 0, 100)) . (strlen($announcement['description'] ?? '') > 100 ? '...' : '') ?></td>
                                                <td><?= isset($announcement['created_at']) ? date('d M Y', strtotime($announcement['created_at'])) : 'N/A' ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="feather icon-bell" style="font-size: 3rem; color: #ccc;"></i>
                                <h5 class="mt-3 text-muted">No Recent Announcements</h5>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-header-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="<?= base_url($url . '/inventory') ?>" class="btn btn-outline-primary btn-block">
                                    <i class="feather icon-package mr-2"></i> View Inventory
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="<?= base_url($url . '/inventory/issue') ?>" class="btn btn-outline-success btn-block">
                                    <i class="feather icon-plus-circle mr-2"></i> Issue Item
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="<?= base_url($url . '/inventory/issues') ?>" class="btn btn-outline-warning btn-block">
                                    <i class="feather icon-list mr-2"></i> View Issues
                                </a>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <a href="<?= base_url($url . '/inventory/maintenance') ?>" class="btn btn-outline-info btn-block">
                                    <i class="feather icon-wrench mr-2"></i> Maintenance
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
