<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">
            <?php echo isset($college) && $college ? 'Edit College Information' : 'Add College Information'; ?>
        </h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/college/view'); ?>">College Settings</a></li>
                <li class="breadcrumb-item active"><?php echo isset($college) && $college ? 'Edit' : 'Add'; ?></li>
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
                        <h5>College Information</h5>
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
                                        <label for="phone">Phone Number</label>
                                        <input type="tel" class="form-control" id="phone" name="phone"
                                               value="<?php echo isset($college['phone']) ? htmlspecialchars($college['phone']) : ''; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email Address *</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               value="<?php echo isset($college['email']) ? htmlspecialchars($college['email']) : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="website">Website</label>
                                        <input type="url" class="form-control" id="website" name="website"
                                               value="<?php echo isset($college['website']) ? htmlspecialchars($college['website']) : ''; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="correspondent">Correspondent</label>
                                        <input type="text" class="form-control" id="correspondent" name="correspondent"
                                               value="<?php echo isset($college['correspondent']) ? htmlspecialchars($college['correspondent']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="vice_correspondent">Vice-Correspondent</label>
                                        <input type="text" class="form-control" id="vice_correspondent" name="vice_correspondent"
                                               value="<?php echo isset($college['vice_correspondent']) ? htmlspecialchars($college['vice_correspondent']) : ''; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="our_vision">Our Vision</label>
                                <textarea class="form-control" id="our_vision" name="our_vision" rows="4"><?php echo isset($college['our_vision']) ? htmlspecialchars($college['our_vision']) : ''; ?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="our_mission">Our Mission</label>
                                <textarea class="form-control" id="our_mission" name="our_mission" rows="4"><?php echo isset($college['our_mission']) ? htmlspecialchars($college['our_mission']) : ''; ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="logo">College Logo</label>
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                        <small class="form-text text-muted">Upload a logo image (PNG, JPG, JPEG, GIF). Max size: 2MB</small>
                                        <?php if(isset($college['logo']) && $college['logo']): ?>
                                            <div class="mt-2">
                                                <img src="<?php echo base_url('uploads/college/' . $college['logo']); ?>" alt="Current Logo" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                                <p class="mb-0 mt-1"><small>Current logo - leave empty to keep existing</small></p>
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
                                                <img src="<?php echo base_url('uploads/college/' . $college['banner']); ?>" alt="Current Banner" class="img-thumbnail" style="max-width: 200px; max-height: 100px;">
                                                <p class="mb-0 mt-1"><small>Current banner - leave empty to keep existing</small></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <i class="feather icon-save"></i> Update College Information
                                </button>
                                <a href="<?php echo base_url($url.'/college/view'); ?>" class="btn btn-secondary">
                                    <i class="feather icon-arrow-left"></i> Cancel
                                </a>
                            </div>
                        </form>
                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="feather icon-building" style="font-size: 4rem; color: #ccc;"></i>
                            <h4 class="mt-3">College Information Not Found</h4>
                            <p class="text-muted">Unable to load college information for editing.</p>
                            <a href="<?php echo base_url($url.'/college/view'); ?>" class="btn btn-secondary">
                                <i class="feather icon-arrow-left"></i> Back to College View
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>










