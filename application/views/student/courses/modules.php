<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Course Modules</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url('student-portal/dashboard'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url('student-portal/courses'); ?>">Courses</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($course['name']); ?> - Modules</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Course Info -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5><?php echo htmlspecialchars($course['name']); ?></h5>
                                <p class="mb-1"><?php echo htmlspecialchars($course['description']); ?></p>
                                <small>Course Code: <?php echo htmlspecialchars($course['course_code'] ?? 'N/A'); ?></small>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo base_url('student-portal/courses'); ?>" class="btn btn-light btn-sm">
                                    <i class="feather icon-arrow-left"></i> Back to Courses
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modules Grid -->
        <div class="row">
            <?php if (empty($modules)): ?>
                <div class="col-md-12">
                    <div class="text-center py-5">
                        <i class="feather icon-folder" style="font-size: 4rem; color: #ccc;"></i>
                        <h4 class="mt-3">No Modules Available</h4>
                        <p class="text-muted">This course doesn't have any modules yet.</p>
                        <a href="<?php echo base_url('student-portal/courses'); ?>" class="btn btn-primary">
                            Back to Courses
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($modules as $module): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 module-card">
                            <div class="card-header bg-gradient-success text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-light">Module <?php echo $module['order']; ?></span>
                                    <small>Order: <?php echo $module['order']; ?></small>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($module['name']); ?></h5>

                                <p class="card-text text-muted flex-grow-1">
                                    <?php echo htmlspecialchars(substr($module['description'], 0, 150)); ?>
                                    <?php echo strlen($module['description']) > 150 ? '...' : ''; ?>
                                </p>

                                <div class="mt-auto">
                                    <!-- Module Stats -->
                                    <?php
                                    // Get lessons count for this module
                                    $this->db->where('module_id', $module['id']);
                                    $this->db->where('is_active', 1);
                                    $lessons_count = $this->db->count_all_results('course_module_lessons');
                                    ?>
                                    <div class="mb-3">
                                        <small class="text-muted">
                                            <i class="feather icon-file-text"></i>
                                            <?php echo $lessons_count; ?> Lesson<?php echo $lessons_count !== 1 ? 's' : ''; ?>
                                        </small>
                                    </div>

                                    <!-- Action Button -->
                                    <div class="d-grid">
                                        <a href="<?php echo base_url('student-portal/module-lessons/' . $course['id'] . '/' . $module['id']); ?>"
                                           class="btn btn-success btn-sm">
                                            <i class="feather icon-play-circle"></i> Start Learning
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
.module-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.module-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.card-header {
    border-bottom: none;
}
</style>