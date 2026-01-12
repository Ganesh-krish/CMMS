<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Module Lessons</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url('student-portal/dashboard'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url('student-portal/courses'); ?>">Courses</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url('student-portal/course-modules/' . $course['id']); ?>"><?php echo htmlspecialchars($course['name']); ?> - Modules</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($module['name']); ?> - Lessons</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Module Info -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bg-gradient-info text-white">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5><?php echo htmlspecialchars($course['name']); ?> - <?php echo htmlspecialchars($module['name']); ?></h5>
                                <p class="mb-1"><?php echo htmlspecialchars($module['description']); ?></p>
                                <small>Course: <?php echo htmlspecialchars($course['course_code'] ?? 'N/A'); ?> | Module Order: <?php echo $module['order']; ?></small>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo base_url('student-portal/course-modules/' . $course['id']); ?>" class="btn btn-light btn-sm">
                                    <i class="feather icon-arrow-left"></i> Back to Modules
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lessons Grid -->
        <div class="row">
            <?php if (empty($lessons)): ?>
                <div class="col-md-12">
                    <div class="text-center py-5">
                        <i class="feather icon-file-text" style="font-size: 4rem; color: #ccc;"></i>
                        <h4 class="mt-3">No Lessons Available</h4>
                        <p class="text-muted">This module doesn't have any lessons yet.</p>
                        <a href="<?php echo base_url('student-portal/course-modules/' . $course['id']); ?>" class="btn btn-primary">
                            Back to Modules
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($lessons as $lesson): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 lesson-card">
                            <div class="card-header bg-gradient-warning text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-light">Lesson <?php echo $lesson['order']; ?></span>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php
                                        // Show lesson status badge
                                        $lesson_status = isset($lesson['status']) ? $lesson['status'] : 'not_started';
                                        $status_class = 'secondary';
                                        $status_icon = 'circle';
                                        
                                        if ($lesson_status === 'in_progress') {
                                            $status_class = 'warning';
                                            $status_icon = 'clock';
                                        } elseif ($lesson_status === 'completed') {
                                            $status_class = 'success';
                                            $status_icon = 'check-circle';
                                        }
                                        ?>
                                        <span class="badge badge-<?php echo $status_class; ?>" title="<?php echo ucfirst(str_replace('_', ' ', $lesson_status)); ?>">
                                            <i class="feather icon-<?php echo $status_icon; ?>"></i>
                                        </span>
                                        <small>
                                            <?php
                                            $icon_class = 'icon-file-text';
                                            switch($lesson['type']) {
                                                case LESSON_TYPE_TEXT:
                                                    $icon_class = 'icon-file-text';
                                                    break;
                                                case LESSON_TYPE_VIDEO:
                                                    $icon_class = 'icon-video';
                                                    break;
                                                case LESSON_TYPE_FILE:
                                                    $icon_class = 'icon-file';
                                                    break;
                                            }
                                            ?>
                                            <i class="feather <?php echo $icon_class; ?>"></i>
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($lesson['title']); ?></h5>

                                <p class="card-text text-muted flex-grow-1">
                                    <?php echo htmlspecialchars(substr($lesson['content'], 0, 120)); ?>
                                    <?php echo strlen($lesson['content']) > 120 ? '...' : ''; ?>
                                </p>

                                <div class="mt-auto">
                                    <!-- Lesson Meta Info -->
                                    <div class="mb-3">
                                        <div class="row text-center">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Type</small>
                                                <span class="badge badge-secondary"><?php echo ucfirst($lesson['type']); ?></span>
                                            </div>
                                            <?php if ($lesson['duration']): ?>
                                                <div class="col-6">
                                                    <small class="text-muted d-block">Duration</small>
                                                    <span class="text-muted"><?php echo htmlspecialchars($lesson['duration']); ?></span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    <div class="d-grid">
                                        <a href="<?php echo base_url('student-portal/view-lesson/' . $course['id'] . '/' . $module['id'] . '/' . $lesson['id']); ?>"
                                           class="btn btn-primary btn-sm">
                                            <i class="feather icon-eye"></i> View Lesson
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
.lesson-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.lesson-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.card-header {
    border-bottom: none;
}

.badge {
    font-size: 0.75em;
}
</style>
