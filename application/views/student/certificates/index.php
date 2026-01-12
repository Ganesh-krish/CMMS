<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">My Certificates</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url('student-portal/dashboard'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Certificates</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('error') ?></span>
            </div>
        <?php endif; ?>

        <?php if ($this->session->flashdata('message')): ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php endif; ?>

        <!-- Certificates Grid -->
        <div class="row">
            <?php if (empty($certificates)): ?>
                <div class="col-md-12">
                    <div class="text-center py-5">
                        <i class="feather icon-award" style="font-size: 4rem; color: #ccc;"></i>
                        <h4 class="mt-3">No Certificates Yet</h4>
                        <p class="text-muted">You haven't completed any courses yet. Complete a course to earn your first certificate!</p>
                        <a href="<?php echo base_url('student-portal/courses'); ?>" class="btn btn-primary">
                            View My Courses
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($certificates as $cert): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 certificate-card">
                            <div class="card-header bg-gradient-success text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <i class="feather icon-award" style="font-size: 1.5rem;"></i>
                                    <span class="badge badge-light"><?php echo htmlspecialchars($cert['course_code'] ?? 'N/A'); ?></span>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($cert['course_name']); ?></h5>

                                <div class="mt-auto">
                                    <div class="mb-3">
                                        <small class="text-muted d-block">Certificate Number</small>
                                        <strong><?php echo htmlspecialchars($cert['certificate_number']); ?></strong>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted d-block">Issued On</small>
                                        <strong><?php echo date('M d, Y', strtotime($cert['issued_at'])); ?></strong>
                                    </div>

                                    <?php if (!empty($cert['completed_at'])): ?>
                                        <div class="mb-3">
                                            <small class="text-muted d-block">Completed On</small>
                                            <strong><?php echo date('M d, Y', strtotime($cert['completed_at'])); ?></strong>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Action Buttons -->
                                    <div class="d-grid gap-2">
                                        <a href="<?php echo base_url('student-portal/certificate/' . $cert['id']); ?>"
                                           class="btn btn-success btn-sm" target="_blank">
                                            <i class="feather icon-eye"></i> View Certificate
                                        </a>
                                        <a href="<?php echo base_url('student-portal/download_certificate/' . $cert['id']); ?>"
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="feather icon-download"></i> Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.certificate-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.certificate-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}
</style>
