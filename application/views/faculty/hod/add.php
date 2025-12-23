<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">
            <?php echo isset($hod) ? 'Edit Department Administrator' : 'Add New Department Administrator'; ?>
        </h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/management/hod'); ?>">Management</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/management/hod'); ?>">Dept Administrator</a></li>
                <li class="breadcrumb-item active"><?php echo isset($hod) ? 'Edit' : 'Add'; ?> Dept Administrator</li>
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
                        <h5>Department Administrator Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo isset($hod) ? base_url($url.'/management/hod/edit/'.$hod['id']) : base_url($url.'/management/hod/add'); ?>">
                            <div class="form-group">
                                <label for="name"><i class="feather icon-user mr-2"></i>Full Name *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="<?php echo isset($hod) ? htmlspecialchars($hod['name']) : ''; ?>"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="email"><i class="feather icon-mail mr-2"></i>Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?php echo isset($hod) ? htmlspecialchars($hod['email']) : ''; ?>"
                                       required <?php echo isset($hod) ? 'readonly' : ''; ?>>
                                <?php if(isset($hod)): ?>
                                    <small class="form-text text-muted">Email cannot be changed after creation</small>
                                <?php endif; ?>
                            </div>

                            <div class="form-group">
                                <label for="phone"><i class="feather icon-phone mr-2"></i>Phone Number *</label>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       value="<?php echo isset($hod) ? htmlspecialchars($hod['phone']) : ''; ?>"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="department"><i class="feather icon-briefcase mr-2"></i>Department *</label>
                                <select class="form-control select2" id="department" name="department" required>
                                    <option value="">Select Department</option>
                                    <?php if(isset($departments) && !empty($departments)): ?>
                                        <?php foreach($departments as $dept): ?>
                                            <option value="<?php echo $dept['id']; ?>"
                                                <?php echo (isset($hod) && $hod['department'] == $dept['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($dept['name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <?php if (!isset($hod)): ?>
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
                                    <?php echo isset($hod) ? 'Update Dept Administrator' : 'Add Dept Administrator'; ?>
                                </button>
                                <a href="<?php echo base_url($url.'/management/hod'); ?>" class="btn btn-secondary">
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

.card-body .form-control, .card-body .form-select {
    border-radius: 0.375rem;
    border: 1px solid #ced4da;
    padding: 0.75rem 1rem;
}

.card-body .form-control:focus, .card-body .form-select:focus {
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: "Select an option",
        allowClear: true,
        width: '100%'
    });
});
</script>