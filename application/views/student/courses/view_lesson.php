<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Lesson Content</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url('student-portal/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url('student-portal/courses'); ?>">Courses</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url('student-portal/course-modules/'.$course['id']); ?>"><?php echo htmlspecialchars($course['name']); ?> - Modules</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url('student-portal/module-lessons/'.$course['id'].'/'.$module['id']); ?>"><?php echo htmlspecialchars($module['name']); ?> - Lessons</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($lesson['title']); ?></li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-<?php echo $this->session->flashdata('message')[0]; ?> alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <?php echo $this->session->flashdata('message')[1]; ?>
            </div>
        <?php } ?>
        
        <?php if ($this->session->flashdata('error')) { ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <?php echo $this->session->flashdata('error'); ?>
            </div>
        <?php } ?>

        <!-- Lesson Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bg-gradient-primary text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5><?php echo htmlspecialchars($lesson['title']); ?></h5>
                                <div class="mb-2">
                                    <span class="badge badge-light"><?php echo ucfirst(htmlspecialchars($lesson['type'])); ?></span>
                                    <?php if (!empty($lesson['duration'])): ?>
                                        <small class="text-white-50 ml-2"><i class="feather icon-clock"></i> <?php echo htmlspecialchars($lesson['duration']); ?></small>
                                    <?php endif; ?>
                                </div>
                                <p class="text-white-75 mb-0"><?php echo htmlspecialchars($lesson['content'] ?: 'No description available.'); ?></p>
                            </div>
                            <div class="col-md-4 text-right">
                                <?php
                                $lesson_status = isset($lesson_progress) && $lesson_progress ? $lesson_progress['status'] : 'not_started';
                                $status_badge_class = 'secondary';
                                $status_text = 'Not Started';
                                
                                if ($lesson_status === 'in_progress') {
                                    $status_badge_class = 'warning';
                                    $status_text = 'In Progress';
                                } elseif ($lesson_status === 'completed') {
                                    $status_badge_class = 'success';
                                    $status_text = 'Completed';
                                }
                                ?>
                                <div class="mb-2">
                                    <span class="badge badge-<?php echo $status_badge_class; ?>">
                                        <i class="feather icon-<?php echo $lesson_status === 'completed' ? 'check-circle' : ($lesson_status === 'in_progress' ? 'clock' : 'circle'); ?>"></i>
                                        <?php echo $status_text; ?>
                                    </span>
                                </div>
                                <a href="<?php echo base_url('student-portal/module-lessons/'.$course['id'].'/'.$module['id']); ?>" class="btn btn-light">
                                    <i class="feather icon-arrow-left"></i> Back to Lessons
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lesson Content Display -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">
                            <?php
                            $icon_class = 'icon-file-text';
                            $content_title = 'Lesson Content';

                            switch($lesson['type']) {
                                case LESSON_TYPE_TEXT:
                                    $icon_class = 'icon-file-text';
                                    $content_title = 'Text Content';
                                    break;
                                case LESSON_TYPE_VIDEO:
                                    $icon_class = 'icon-video';
                                    $content_title = 'Video Content';
                                    break;
                                case LESSON_TYPE_FILE:
                                    $icon_class = 'icon-file';
                                    $content_title = 'File Content';
                                    break;
                            }
                            ?>
                            <i class="feather <?php echo $icon_class; ?>"></i> <?php echo $content_title; ?>
                        </h6>
                    </div>
                    <div class="card-body">

                        <?php if ($lesson['type'] === LESSON_TYPE_TEXT && !empty($lesson['course_text'])): ?>
                            <!-- Text Content Display -->
                            <div class="text-content">
                                <div class="border p-4 rounded bg-light" style="white-space: pre-wrap; font-family: 'Courier New', monospace; line-height: 1.6;">
                                    <?php echo htmlspecialchars($lesson['course_text']); ?>
                                </div>
                            </div>

                        <?php elseif ($lesson['type'] === LESSON_TYPE_VIDEO && !empty($lesson['course_url'])): ?>
                            <!-- Video Content Display -->
                            <div class="video-content text-center">
                                <?php
                                $embed_code = '';
                                $video_url = $lesson['course_url'];

                                if (strpos($video_url, 'youtube.com') !== false || strpos($video_url, 'youtu.be') !== false) {
                                    // YouTube video
                                    $video_id = preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $video_url, $matches) ? $matches[1] : null;
                                    if ($video_id) {
                                        $embed_code = '<iframe width="100%" height="400" src="https://www.youtube.com/embed/' . $video_id . '" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
                                    }
                                } elseif (strpos($video_url, 'vimeo.com') !== false) {
                                    // Vimeo video
                                    $video_id = preg_match('/vimeo\.com\/(\d+)/', $video_url, $matches) ? $matches[1] : null;
                                    if ($video_id) {
                                        $embed_code = '<iframe width="100%" height="400" src="https://player.vimeo.com/video/' . $video_id . '" frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
                                    }
                                }

                                if ($embed_code): ?>
                                    <div class="embed-responsive embed-responsive-16by9 mb-3">
                                        <?php echo $embed_code; ?>
                                    </div>
                                    <div class="alert alert-info">
                                        <i class="feather icon-external-link"></i>
                                        <strong>Video Source:</strong> <a href="<?php echo htmlspecialchars($video_url); ?>" target="_blank"><?php echo htmlspecialchars($video_url); ?></a>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="feather icon-alert-triangle"></i>
                                        Unable to embed video. Please visit the direct link:
                                        <a href="<?php echo htmlspecialchars($video_url); ?>" target="_blank" class="btn btn-primary btn-sm ml-2">
                                            <i class="feather icon-external-link"></i> Open Video
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>

                        <?php elseif ($lesson['type'] === LESSON_TYPE_FILE && !empty($lesson['course_file'])): ?>
                            <!-- File Content Display -->
                            <div class="file-content">
                                <?php
                                $file_path = $lesson['course_file'];
                                $full_file_path = FCPATH . $file_path;
                                $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
                                $file_name = basename($file_path);

                                // Check if file exists
                                if (file_exists($full_file_path)): ?>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="card border-primary">
                                                <div class="card-body text-center">
                                                    <?php if (!in_array($file_extension, ['txt', 'pdf', 'doc', 'docx'])): ?>
                                                        <div class="alert alert-info mb-3">
                                                            <small><i class="feather icon-info"></i> This file type supports download only. Content preview is available for TXT, PDF, and DOC files.</small>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php
                                                    // Display appropriate icon based on file type
                                                    $file_icon = 'icon-file';
                                                    switch ($file_extension) {
                                                        case 'pdf':
                                                            $file_icon = 'icon-file-text';
                                                            break;
                                                        case 'doc':
                                                        case 'docx':
                                                            $file_icon = 'icon-file-text';
                                                            break;
                                                        case 'txt':
                                                            $file_icon = 'icon-file-text';
                                                            break;
                                                        case 'jpg':
                                                        case 'jpeg':
                                                        case 'png':
                                                        case 'gif':
                                                            $file_icon = 'icon-image';
                                                            break;
                                                        case 'mp4':
                                                        case 'avi':
                                                        case 'mov':
                                                            $file_icon = 'icon-video';
                                                            break;
                                                        case 'mp3':
                                                        case 'wav':
                                                            $file_icon = 'icon-volume-2';
                                                            break;
                                                    }
                                                    ?>
                                                    <i class="feather <?php echo $file_icon; ?> fa-3x text-primary mb-3"></i>
                                                    <h5><?php echo htmlspecialchars($file_name); ?></h5>
                                                    <p class="text-muted mb-3"><?php echo strtoupper($file_extension); ?> File</p>

                                                    <div class="btn-group">
                                                        <a href="<?php echo base_url($file_path); ?>" target="_blank" class="btn btn-primary">
                                                            <i class="feather icon-eye"></i> View File
                                                        </a>
                                                        <a href="<?php echo base_url($file_path); ?>" download class="btn btn-outline-primary">
                                                            <i class="feather icon-download"></i> Download
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="mb-0"><i class="feather icon-info"></i> File Information</h6>
                                                </div>
                                                <div class="card-body">
                                                    <dl class="row">
                                                        <dt class="col-sm-5">Name:</dt>
                                                        <dd class="col-sm-7"><?php echo htmlspecialchars($file_name); ?></dd>

                                                        <dt class="col-sm-5">Type:</dt>
                                                        <dd class="col-sm-7"><?php echo strtoupper($file_extension); ?></dd>

                                                        <dt class="col-sm-5">Size:</dt>
                                                        <dd class="col-sm-7"><?php echo number_format(filesize($full_file_path) / 1024, 1); ?> KB</dd>
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-danger">
                                        <i class="feather icon-alert-triangle"></i>
                                        File not found: <?php echo htmlspecialchars($file_name); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                        <?php else: ?>
                            <!-- No Content Available -->
                            <div class="text-center py-5">
                                <i class="feather icon-file-x fa-3x text-muted mb-3"></i>
                                <h5>No Content Available</h5>
                                <p class="text-muted">This lesson doesn't have any content yet.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lesson Actions -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <?php
                                // Get previous lesson
                                $this->db->select('id, title')
                                        ->from('course_module_lessons')
                                        ->where('module_id', $module['id'])
                                        ->where('order <', $lesson['order'])
                                        ->where('is_active', 1)
                                        ->order_by('order', 'DESC')
                                        ->limit(1);
                                $prev_lesson = $this->db->get()->row_array();

                                if ($prev_lesson): ?>
                                    <a href="<?php echo base_url('student-portal/view-lesson/'.$course['id'].'/'.$module['id'].'/'.$prev_lesson['id']); ?>" class="btn btn-outline-secondary">
                                        <i class="feather icon-chevron-left"></i> Previous: <?php echo htmlspecialchars(substr($prev_lesson['title'], 0, 30)); ?>
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-outline-secondary" disabled>
                                        <i class="feather icon-chevron-left"></i> Previous Lesson
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php
                                $lesson_status = isset($lesson_progress) && $lesson_progress ? $lesson_progress['status'] : 'not_started';
                                
                                // Show "Mark as Completed" button if not completed
                                if ($lesson_status !== 'completed'): ?>
                                    <a href="<?php echo base_url('student-portal/mark-lesson-completed/'.$course['id'].'/'.$module['id'].'/'.$lesson['id']); ?>" 
                                       class="btn btn-success" 
                                       onclick="return confirm('Mark this lesson as completed?');">
                                        <i class="feather icon-check-circle"></i> Mark as Completed
                                    </a>
                                <?php else: ?>
                                    <span class="badge badge-success p-2">
                                        <i class="feather icon-check-circle"></i> Completed
                                    </span>
                                <?php endif; ?>
                                
                                <?php
                                // Show "Request Certificate" button if:
                                // 1. This is the last lesson in the module
                                // 2. All lessons in course are completed
                                // 3. Certificate request doesn't exist or is rejected
                                if ($is_last_lesson && $all_lessons_completed): 
                                    $show_request_btn = false;
                                    if (!isset($certificate_request) || !$certificate_request) {
                                        $show_request_btn = true;
                                    } elseif ($certificate_request['status'] === 'rejected') {
                                        $show_request_btn = true;
                                    }
                                    
                                    if ($show_request_btn): ?>
                                        <a href="<?php echo base_url('student-portal/request-certificate/'.$course['id']); ?>" 
                                           class="btn btn-primary ml-2" 
                                           onclick="return confirm('Request certificate for this course? Your request will be reviewed by the principal.');">
                                            <i class="feather icon-award"></i> Request Certificate
                                        </a>
                                    <?php elseif (isset($certificate_request)): 
                                        $request_status = $certificate_request['status'];
                                        $status_class = $request_status === 'approved' ? 'success' : ($request_status === 'rejected' ? 'danger' : 'warning');
                                        $status_text = ucfirst($request_status);
                                    ?>
                                        <span class="badge badge-<?php echo $status_class; ?> p-2 ml-2">
                                            <i class="feather icon-<?php echo $request_status === 'approved' ? 'check' : ($request_status === 'rejected' ? 'x' : 'clock'); ?>"></i>
                                            Certificate Request: <?php echo $status_text; ?>
                                        </span>
                                        <?php if ($request_status === 'rejected' && !empty($certificate_request['rejection_reason'])): ?>
                                            <div class="mt-2">
                                                <small class="text-danger">
                                                    <strong>Reason:</strong> <?php echo htmlspecialchars($certificate_request['rejection_reason']); ?>
                                                </small>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php
                                // Get next lesson
                                $this->db->select('id, title')
                                        ->from('course_module_lessons')
                                        ->where('module_id', $module['id'])
                                        ->where('order >', $lesson['order'])
                                        ->where('is_active', 1)
                                        ->order_by('order', 'ASC')
                                        ->limit(1);
                                $next_lesson = $this->db->get()->row_array();

                                if ($next_lesson): ?>
                                    <a href="<?php echo base_url('student-portal/view-lesson/'.$course['id'].'/'.$module['id'].'/'.$next_lesson['id']); ?>" class="btn btn-primary ml-2">
                                        Next: <?php echo htmlspecialchars(substr($next_lesson['title'], 0, 30)); ?> <i class="feather icon-chevron-right"></i>
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-primary ml-2" disabled>
                                        Next Lesson <i class="feather icon-chevron-right"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.text-white-75 {
    color: rgba(255, 255, 255, 0.75) !important;
}

.text-white-50 {
    color: rgba(255, 255, 255, 0.5) !important;
}

.embed-responsive {
    position: relative;
    display: block;
    width: 100%;
    padding: 0;
    overflow: hidden;
}

.embed-responsive-16by9 {
    padding-bottom: 56.25%;
}

.embed-responsive iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
</style>
