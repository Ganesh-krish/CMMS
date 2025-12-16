<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">
            College Information
        </h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item active">College Settings</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>College Details</h5>
                    </div>
                    <div class="card-body">
                        <?php if(isset($college) && $college): ?>
                        <form method="post" action="" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">College Name *</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                               value="<?php echo isset($college['name']) ? htmlspecialchars($college['name']) : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="established_year">Established Year</label>
                                        <input type="number" class="form-control" id="established_year" name="established_year"
                                               value="<?php echo isset($college['established_year']) ? htmlspecialchars($college['established_year']) : ''; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3"><?php echo isset($college['address']) ? htmlspecialchars($college['address']) : ''; ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="city">City</label>
                                        <input type="text" class="form-control" id="city" name="city"
                                               value="<?php echo isset($college['city']) ? htmlspecialchars($college['city']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="state">State</label>
                                        <input type="text" class="form-control" id="state" name="state"
                                               value="<?php echo isset($college['state']) ? htmlspecialchars($college['state']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="phone_number">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone_number" name="phone_number"
                                               value="<?php echo isset($college['phone_number']) ? htmlspecialchars($college['phone_number']) : ''; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               value="<?php echo isset($college['email']) ? htmlspecialchars($college['email']) : ''; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4"><?php echo isset($college['description']) ? htmlspecialchars($college['description']) : ''; ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="logo">College Logo</label>
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                        <small class="form-text text-muted">Upload a logo image (PNG, JPG, JPEG, GIF). Max size: 2MB</small>
                                        <?php if(isset($college['logo']) && $college['logo']): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo base_url('uploads/college/' . $college['logo']); ?>" alt="Current Logo" style="max-width: 100px; max-height: 100px;">
                                                <p class="text-muted">Current logo</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="banner">College Banner</label>
                                        <input type="file" class="form-control" id="banner" name="banner" accept="image/*">
                                        <small class="form-text text-muted">Upload a banner image (PNG, JPG, JPEG, GIF). Max size: 5MB</small>
                                        <?php if(isset($college['banner']) && $college['banner']): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo base_url('uploads/college/' . $college['banner']); ?>" alt="Current Banner" style="max-width: 200px; max-height: 100px;">
                                                <p class="text-muted">Current banner</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather icon-save"></i> Update College Information
                                </button>
                            </div>
                        </form>
                        <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="feather icon-alert-triangle"></i> College information not found.
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>