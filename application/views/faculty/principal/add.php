<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">
            <?php echo isset($user) ? 'Edit Principal' : 'Add New Principal'; ?>
        </h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/principal/view'); ?>">Management</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/principal/view'); ?>">Principal</a></li>
                <li class="breadcrumb-item active"><?php echo isset($user) ? 'Edit' : 'Add'; ?> Principal</li>
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
                        <h5>Principal Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="name"><i class="feather icon-user mr-2"></i>Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="<?php echo isset($user) ? htmlspecialchars($user['name']) : ''; ?>"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="email"><i class="feather icon-mail mr-2"></i>Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?php echo isset($user) ? htmlspecialchars($user['email']) : ''; ?>"
                                       required <?php echo isset($user) ? 'readonly' : ''; ?>>
                                <?php if(isset($user)): ?>
                                    <small class="form-text text-muted">Email cannot be changed after creation</small>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="phone"><i class="feather icon-phone mr-2"></i>Phone Number *</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       value="<?php echo isset($user) ? htmlspecialchars($user['phone']) : ''; ?>"
                                       required>
                            </div>

                            <?php if (!isset($user)): ?>
                                <div class="form-group">
                                    <label for="password"><i class="feather icon-lock mr-2"></i>Password *</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                           required minlength="6">
                                    <small class="form-text text-muted">Minimum 6 characters</small>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather icon-save"></i>
                                    <?php echo isset($user) ? 'Update Principal' : 'Add Principal'; ?>
                                </button>
                                <a href="<?php echo base_url($url.'/principal/view'); ?>" class="btn btn-secondary">
                                    <i class="feather icon-arrow-left"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Form label icons styling */
.form-group label i {
    color: #6c757d;
    font-size: 1rem;
    vertical-align: middle;
}

.form-group label {
    font-weight: 500;
    color: #495057;
    margin-bottom: 0.5rem;
}

/* Enhanced form styling */
.card-body .form-group {
    margin-bottom: 1.5rem;
}

.card-body .form-control {
    border-radius: 0.375rem;
    border: 1px solid #ced4da;
    padding: 0.75rem 1rem;
}

.card-body .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    padding: 0.75rem 2rem;
    font-weight: 500;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.btn-secondary {
    padding: 0.75rem 2rem;
    font-weight: 500;
}
</style>