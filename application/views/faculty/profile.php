<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">User Profile</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" readonly>
                                </div>
                            </div>
                        </div>

                        <?php if (isset($user['department'])): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Department</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['department'] ?? 'N/A'); ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Role</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($this->common->get_role_name($user['role'] ?? $user['designation'])); ?>" readonly>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>College</label>
                                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($this->college['name'] ?? 'N/A'); ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Account Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="<?php echo base_url($url.'/reset_password'); ?>" class="btn btn-primary btn-block mb-2">
                            <i class="feather icon-lock"></i> Change Password
                        </a>
                        <a href="<?php echo base_url('logout'); ?>" class="btn btn-outline-danger btn-block">
                            <i class="feather icon-log-out"></i> Logout
                        </a>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5>Permissions</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <?php foreach ($permissions as $key => $value): ?>
                                <?php if ($value): ?>
                                    <li><i class="feather icon-check text-success"></i> <?php echo ucwords(str_replace('_', ' ', $key)); ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>