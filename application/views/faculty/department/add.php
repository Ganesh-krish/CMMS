<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">
            <?php echo isset($department) ? 'Edit Department' : 'Add New Department'; ?>
        </h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/departments'); ?>">Departments</a></li>
                <li class="breadcrumb-item active"><?php echo isset($department) ? 'Edit' : 'Add'; ?> Department</li>
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
                        <h5>Department Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label"><i class="feather icon-briefcase mr-2"></i>Department Name *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="<?php echo isset($department) ? htmlspecialchars($department['name']) : ''; ?>"
                                       required>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary">
                                    <?php echo isset($department) ? 'Update Department' : 'Add Department'; ?>
                                </button>
                                <a href="<?php echo base_url($url.'/departments'); ?>" class="btn btn-secondary">Cancel</a>
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