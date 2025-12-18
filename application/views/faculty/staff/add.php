<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">
            <?php echo isset($user) ? 'Edit Staff' : 'Add New Staff'; ?>
        </h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/staff/view'); ?>">Faculty</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/staff/view'); ?>">Staff</a></li>
                <li class="breadcrumb-item active"><?php echo isset($user) ? 'Edit' : 'Add'; ?> Staff</li>
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
                        <h5>Staff Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="<?php echo isset($user) ? htmlspecialchars($user['name']) : ''; ?>"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?php echo isset($user) ? htmlspecialchars($user['email']) : ''; ?>"
                                       required <?php echo isset($user) ? 'readonly' : ''; ?>>
                                <?php if(isset($user)): ?>
                                    <small class="form-text text-muted">Email cannot be changed after creation</small>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number *</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       value="<?php echo isset($user) ? htmlspecialchars($user['phone']) : ''; ?>"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label for="department_id" class="form-label">Department *</label>
                                <select class="form-control select2" id="department_id" name="department_id" required>
                                    <option value="">Select Department</option>
                                    <?php if(isset($departments) && !empty($departments)): ?>
                                        <?php foreach($departments as $dept): ?>
                                            <option value="<?php echo $dept['id']; ?>"
                                                <?php echo (isset($user) && $user['department_id'] == $dept['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($dept['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <?php if (!isset($user)): ?>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password *</label>
                                    <input type="password" class="form-control" id="password" name="password"
                                           required minlength="6">
                                    <small class="form-text text-muted">Minimum 6 characters</small>
                                </div>
                            <?php endif; ?>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather icon-save"></i>
                                    <?php echo isset($user) ? 'Update Staff' : 'Add Staff'; ?>
                                </button>
                                <a href="<?php echo base_url($url.'/staff/view'); ?>" class="btn btn-secondary">
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

<script>
$(document).ready(function() {
    // Initialize Select2 for dropdowns
    $('.select2').select2({
        placeholder: "Select an option",
        allowClear: true
    });
});
</script>