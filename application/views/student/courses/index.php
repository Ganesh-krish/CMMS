<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">My Courses</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url('student-portal/dashboard'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Courses</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Courses Grid -->
        <div class="row">
            <?php if (empty($courses)): ?>
                <div class="col-md-12">
                    <div class="text-center py-5">
                        <i class="feather icon-book" style="font-size: 4rem; color: #ccc;"></i>
                        <h4 class="mt-3">No Courses Enrolled</h4>
                        <p class="text-muted">You haven't been enrolled in any courses yet.</p>
                        <a href="<?php echo base_url('student-portal/dashboard'); ?>" class="btn btn-primary">
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($courses as $course): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 course-card">
                            <div class="card-header bg-gradient-primary text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-light"><?php echo htmlspecialchars($course['course_code'] ?? 'N/A'); ?></span>
                                    <small><?php echo ucfirst($course['enrollment_status']); ?></small>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($course['name']); ?></h5>

                                <p class="card-text text-muted flex-grow-1">
                                    <?php echo htmlspecialchars(substr($course['description'], 0, 120)); ?>
                                    <?php echo strlen($course['description']) > 120 ? '...' : ''; ?>
                                </p>

                                <div class="mt-auto">
                                    <!-- Progress Bar -->
                                    <?php if (isset($course['progress_percentage'])): ?>
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between">
                                                <small class="text-muted">Progress</small>
                                                <small class="text-muted"><?php echo $course['progress_percentage']; ?>%</small>
                                            </div>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-success" role="progressbar"
                                                     style="width: <?php echo $course['progress_percentage']; ?>%"
                                                     aria-valuenow="<?php echo $course['progress_percentage']; ?>"
                                                     aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Action Buttons -->
                                    <div class="d-grid gap-2">
                                        <a href="<?php echo base_url('student-portal/course-modules/' . $course['id']); ?>"
                                           class="btn btn-primary btn-sm">
                                            <i class="feather icon-layers"></i> View Modules
                                        </a>

                                        <?php 
                                        // Show "Request Certificate" button if:
                                        // 1. All lessons are completed
                                        // 2. Certificate doesn't exist
                                        // 3. Request doesn't exist or was rejected
                                        $show_request_btn = false;
                                        if (isset($course['all_lessons_completed']) && $course['all_lessons_completed']) {
                                            if (!isset($certificates_map[$course['id']])) {
                                                if (!isset($course['certificate_request']) || !$course['certificate_request']) {
                                                    $show_request_btn = true;
                                                } elseif (isset($course['certificate_request']['status']) && $course['certificate_request']['status'] === 'rejected') {
                                                    $show_request_btn = true;
                                                }
                                            }
                                        }
                                        
                                        if ($show_request_btn): ?>
                                            <a href="<?php echo base_url('student-portal/request-certificate/'.$course['id']); ?>" 
                                               class="btn btn-warning btn-sm" 
                                               onclick="return confirm('Request certificate for this course? Your request will be reviewed by the principal.');">
                                                <i class="feather icon-award"></i> Request Certificate
                                            </a>
                                        <?php elseif (isset($course['certificate_request']) && $course['certificate_request']): 
                                            $request_status = $course['certificate_request']['status'];
                                            $status_class = $request_status === 'approved' ? 'success' : ($request_status === 'rejected' ? 'danger' : 'warning');
                                            $status_text = ucfirst($request_status);
                                        ?>
                                            <span class="badge badge-<?php echo $status_class; ?> p-2">
                                                <i class="feather icon-<?php echo $request_status === 'approved' ? 'check' : ($request_status === 'rejected' ? 'x' : 'clock'); ?>"></i>
                                                Request: <?php echo $status_text; ?>
                                            </span>
                                            <?php if ($request_status === 'rejected' && !empty($course['certificate_request']['rejection_reason'])): ?>
                                                <small class="text-danger d-block mt-1" title="<?php echo htmlspecialchars($course['certificate_request']['rejection_reason']); ?>">
                                                    <i class="feather icon-info"></i> View reason
                                                </small>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if ($course['enrollment_status'] === 'completed' && isset($certificates_map[$course['id']])): ?>
                                            <a href="<?php echo base_url('student-portal/certificate/' . $certificates_map[$course['id']]['id']); ?>"
                                               class="btn btn-success btn-sm" target="_blank">
                                                <i class="feather icon-award"></i> View Certificate
                                            </a>
                                        <?php endif; ?>

                                        <?php if (isset($course['enrolled_at'])): ?>
                                            <div class="text-center mt-2">
                                                <small class="text-muted">
                                                    Enrolled: <?php echo date('M d, Y', strtotime($course['enrolled_at'])); ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
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
.course-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}
</style>
