<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">
            <?php echo isset($vice_principal) ? 'Edit Assistant Administrator' : 'Add New Assistant Administrator'; ?>
        </h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/management/vice_principal'); ?>">Management</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/management/vice_principal'); ?>">Asst Administrator</a></li>
                <li class="breadcrumb-item active"><?php echo isset($vice_principal) ? 'Edit' : 'Add'; ?> Asst Administrator</li>
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
                        <h5>Assistant Administrator Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="<?php echo isset($vice_principal) ? htmlspecialchars($vice_principal['name']) : ''; ?>"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?php echo isset($vice_principal) ? htmlspecialchars($vice_principal['email']) : ''; ?>"
                                       required <?php echo isset($vice_principal) ? 'readonly' : ''; ?>>
                                <?php if(isset($vice_principal)): ?>
                                    <small class="form-text text-muted">Email cannot be changed after creation</small>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone Number *</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       value="<?php echo isset($vice_principal) ? htmlspecialchars($vice_principal['phone']) : ''; ?>"
                                       required>
                            </div>


                            <?php if (!isset($vice_principal)): ?>
                                <div class="form-group">
                                    <label for="password">Password *</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                           required minlength="6">
                                    <small class="form-text text-muted">Minimum 6 characters</small>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather icon-save"></i>
                                    <?php echo isset($vice_principal) ? 'Update Asst Administrator' : 'Add Asst Administrator'; ?>
                                </button>
                                <a href="<?php echo base_url($url.'/management/vice_principal'); ?>" class="btn btn-secondary">
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