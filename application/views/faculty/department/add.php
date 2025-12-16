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
                            <div class="form-group">
                                <label for="name">Department Name *</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       value="<?php echo isset($department) ? htmlspecialchars($department['name']) : ''; ?>"
                                       required>
                            </div>

                            <div class="form-group">
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