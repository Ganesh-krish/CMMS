<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">College Settings</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item">Settings</li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php   } ?>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">College Information</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url($url . '/principal/profile') ?>" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">College Name *</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $college['name'] ?? ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $college['email'] ?? ''; ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone *</label>
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $college['phone'] ?? ''; ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="website" class="form-label">Website</label>
                            <input type="url" class="form-control" id="website" name="website" value="<?php echo $college['website'] ?? ''; ?>" placeholder="https://www.example.com">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Address *</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required><?php echo $college['address'] ?? ''; ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Brief description about the college"><?php echo $college['description'] ?? ''; ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Logo</label>
                            <div class="mb-2">
                                <?php if (!empty($logo)): ?>
                                    <img src="<?php echo base_url($logo); ?>" alt="College Logo" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                <?php else: ?>
                                    <div class="text-muted">No logo uploaded</div>
                                <?php endif; ?>
                            </div>
                            <label for="logo" class="form-label">Update Logo</label>
                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                            <small class="form-text text-muted">Supported formats: JPG, PNG, GIF. Max size: 2MB</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Current Banner</label>
                            <div class="mb-2">
                                <?php if (!empty($banner)): ?>
                                    <img src="<?php echo base_url($banner); ?>" alt="College Banner" class="img-thumbnail" style="max-width: 200px; max-height: 100px;">
                                <?php else: ?>
                                    <div class="text-muted">No banner uploaded</div>
                                <?php endif; ?>
                            </div>
                            <label for="banner" class="form-label">Update Banner</label>
                            <input type="file" class="form-control" id="banner" name="banner" accept="image/*">
                            <small class="form-text text-muted">Supported formats: JPG, PNG, GIF. Max size: 2MB</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Created Date</label>
                            <input type="text" class="form-control" value="<?php echo isset($college['created_at']) ? date('d M Y, H:i', strtotime($college['created_at'])) : 'N/A'; ?>" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Last Updated</label>
                            <input type="text" class="form-control" value="<?php echo isset($college['updated_at']) ? date('d M Y, H:i', strtotime($college['updated_at'])) : 'N/A'; ?>" readonly>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="feather icon-save"></i> Update Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- College Statistics -->
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-primary">
                            <?php echo $this->db_model->count_rows(TABLE_FACULTY, ["college_id" => $college['id'], "is_active" => 1]); ?>
                        </h4>
                        <p class="mb-0">Total Faculty</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-success">
                            <?php echo $this->db_model->count_rows(TABLE_STUDENT, ["college_id" => $college['id'], "is_active" => 1]); ?>
                        </h4>
                        <p class="mb-0">Total Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-info">
                            <?php echo $this->db_model->count_rows(TABLE_COURCES, ["college_id" => $college['id'], "is_active" => 1]); ?>
                        </h4>
                        <p class="mb-0">Total Courses</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-warning">
                            <?php echo $this->db_model->count_rows(TABLE_DEPARTMENT, ["college_id" => $college['id'], "is_active" => 1]); ?>
                        </h4>
                        <p class="mb-0">Total Departments</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// File upload preview
document.getElementById('logo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Could add preview functionality here
        };
        reader.readAsDataURL(file);
    }
});

document.getElementById('banner').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Could add preview functionality here
        };
        reader.readAsDataURL(file);
    }
});
</script>