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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>College Details</h5>
                        <a href="<?php echo base_url($url.'/college/edit/'.SINGLE_COLLEGE_ID); ?>" class="btn btn-primary">
                            <i class="feather icon-edit"></i> Edit Settings
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if(isset($college) && $college): ?>
                        <div class="row">
                            <!-- College Logo -->
                            <?php if(!empty($college['logo'])): ?>
                            <div class="col-md-12 mb-4 text-center">
                                <img src="<?php echo base_url('uploads/college/' . $college['logo']); ?>" alt="College Logo" class="img-fluid rounded" style="max-height: 150px;">
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">College Name:</label>
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($college['name'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Established Year:</label>
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($college['established_year'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Address:</label>
                            <p class="form-control-plaintext"><?php echo nl2br(htmlspecialchars($college['address'] ?? 'N/A')); ?></p>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">City:</label>
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($college['city'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">State:</label>
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($college['state'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Phone:</label>
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($college['phone'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email:</label>
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($college['email'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Website:</label>
                                    <p class="form-control-plaintext"><?php echo htmlspecialchars($college['website'] ?? 'N/A'); ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description:</label>
                            <p class="form-control-plaintext"><?php echo nl2br(htmlspecialchars($college['description'] ?? 'N/A')); ?></p>
                        </div>

                        <!-- College Banner -->
                        <?php if(!empty($college['banner'])): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Banner:</label>
                                    <div class="mt-2">
                                        <img src="<?php echo base_url('uploads/college/' . $college['banner']); ?>" alt="College Banner" class="img-fluid rounded" style="max-height: 200px; width: 100%; object-fit: cover;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php else: ?>
                        <div class="text-center py-5">
                            <i class="feather icon-building" style="font-size: 4rem; color: #ccc;"></i>
                            <h4 class="mt-3">No College Information</h4>
                            <p class="text-muted">College information has not been configured yet.</p>
                            <a href="<?php echo base_url($url.'/college/edit/'.SINGLE_COLLEGE_ID); ?>" class="btn btn-primary">
                                <i class="feather icon-plus"></i> Add College Information
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>