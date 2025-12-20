<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Lesson Content</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/courses'); ?>">Courses</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/courses/modules/'.$course_id); ?>"><?php echo htmlspecialchars($course['name']); ?> - Modules</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/courses/lessons/'.$course_id.'/'.$module_id); ?>"><?php echo htmlspecialchars($module['name']); ?> - Lessons</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($lesson['title']); ?></li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Lesson Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5><?php echo htmlspecialchars($lesson['title']); ?></h5>
                                <div class="mb-2">
                                    <span class="badge badge-secondary"><?php echo htmlspecialchars($lesson['type']); ?></span>
                                    <?php if ($lesson['duration']): ?>
                                        <small class="text-muted ml-2"><i class="feather icon-clock"></i> <?php echo htmlspecialchars($lesson['duration']); ?></small>
                                    <?php endif; ?>
                                </div>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars($lesson['content'] ?: 'No description available.'); ?></p>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo base_url($url.'/courses/lessons/'.$course_id.'/'.$module_id); ?>" class="btn btn-outline-secondary">
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
                    <div class="card-header">
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
                                                    <?php
                                                    // Display appropriate icon based on file type
                                                    $icon_class = 'icon-file';
                                                    $icon_color = '#6c757d';

                                                    switch($file_extension) {
                                                        case 'pdf':
                                                            $icon_class = 'icon-file-text';
                                                            $icon_color = '#dc3545';
                                                            break;
                                                        case 'doc':
                                                        case 'docx':
                                                            $icon_class = 'icon-file-text';
                                                            $icon_color = '#007bff';
                                                            break;
                                                        case 'ppt':
                                                        case 'pptx':
                                                            $icon_class = 'icon-file-text';
                                                            $icon_color = '#fd7e14';
                                                            break;
                                                        case 'txt':
                                                            $icon_class = 'icon-file-text';
                                                            $icon_color = '#28a745';
                                                            break;
                                                        case 'jpg':
                                                        case 'jpeg':
                                                        case 'png':
                                                        case 'gif':
                                                            $icon_class = 'icon-image';
                                                            $icon_color = '#20c997';
                                                            break;
                                                    }
                                                    ?>

                                                    <i class="feather <?php echo $icon_class; ?>" style="font-size: 4rem; color: <?php echo $icon_color; ?>;"></i>
                                                    <h4 class="mt-3"><?php echo htmlspecialchars($file_name); ?></h4>
                                                    <p class="text-muted mb-3">
                                                        File Type: <?php echo strtoupper($file_extension); ?> |
                                                        Size: <?php echo number_format(filesize($full_file_path) / 1024, 1); ?> KB
                                                    </p>

                                                    <div class="btn-group" role="group">
                                                        <a href="<?php echo base_url($file_path); ?>" target="_blank" class="btn btn-primary">
                                                            <i class="feather icon-external-link"></i> View/Download
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <?php if (in_array($file_extension, ['txt', 'pdf'])): ?>
                                                <!-- Display text content for readable files -->
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h6 class="mb-0">File Preview</h6>
                                                    </div>
                                                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                                        <?php
                                                        try {
                                                            if ($file_extension === 'txt') {
                                                                $content = file_get_contents($full_file_path);
                                                                echo '<pre style="white-space: pre-wrap; font-family: monospace; font-size: 12px;">' . htmlspecialchars($content) . '</pre>';
                                                            } elseif ($file_extension === 'pdf') {
                                                                // For PDF, we'll show a message since we can't easily extract text
                                                                echo '<div class="alert alert-info">';
                                                                echo '<i class="feather icon-file-text"></i> ';
                                                                echo 'PDF files cannot be previewed directly. Click "View/Download" to open the file.';
                                                                echo '</div>';
                                                            }
                                                        } catch (Exception $e) {
                                                            echo '<div class="alert alert-warning">';
                                                            echo '<i class="feather icon-alert-triangle"></i> ';
                                                            echo 'Unable to read file content.';
                                                            echo '</div>';
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            <?php elseif (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                                                <!-- Display image preview -->
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h6 class="mb-0">Image Preview</h6>
                                                    </div>
                                                    <div class="card-body text-center">
                                                        <img src="<?php echo base_url($file_path); ?>" alt="<?php echo htmlspecialchars($file_name); ?>" class="img-fluid rounded" style="max-height: 300px;">
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-danger">
                                        <i class="feather icon-alert-triangle"></i>
                                        <strong>File Not Found:</strong> The uploaded file could not be located.
                                        <br><small>File path: <?php echo htmlspecialchars($file_path); ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>

                        <?php else: ?>
                            <!-- No Content Available -->
                            <div class="text-center py-5">
                                <i class="feather icon-file-x" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3">No Content Available</h4>
                                <p class="text-muted">This lesson doesn't have any specific content configured.</p>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>