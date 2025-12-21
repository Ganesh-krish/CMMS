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

                                        <?php if (isset($course['enrollment_status'])): ?>
                                            <div class="text-center">
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
