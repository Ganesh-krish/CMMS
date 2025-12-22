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
                                                    <?php if (!in_array($file_extension, ['txt', 'pdf', 'doc', 'docx'])): ?>
                                                        <div class="alert alert-info mb-3">
                                                            <small><i class="feather icon-info"></i> This file type supports download only. Content preview is available for TXT, PDF, and DOC files.</small>
                                                        </div>
                                                    <?php endif; ?>
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
                                                        <?php if (in_array($file_extension, ['txt', 'pdf', 'doc', 'docx'])): ?>
                                                            <br><small class="text-success"><i class="feather icon-eye"></i> Content preview available</small>
                                                        <?php endif; ?>
                                                    </p>

                                                    <div class="btn-group" role="group">
                                                        <a href="<?php echo base_url($file_path); ?>" target="_blank" class="btn btn-primary">
                                                            <i class="feather icon-external-link"></i> ACCESS LESSON MATERIAL
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <?php if (in_array($file_extension, ['txt', 'pdf', 'doc', 'docx'])): ?>
                                                <!-- Display text content for readable files -->
                                                <div class="card">
                                                    <div class="card-header">
                                                        <h6 class="mb-0">File Content Preview</h6>
                                                    </div>
                                                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                                        <?php
                                                        try {
                                                            // Check file readability and size
                                                            if (!is_readable($full_file_path)) {
                                                                echo '<div class="alert alert-danger">';
                                                                echo '<i class="feather icon-alert-triangle"></i> ';
                                                                echo 'File is not readable or accessible.';
                                                                echo '</div>';
                                                            } elseif (filesize($full_file_path) > 5242880) { // 5MB limit
                                                                echo '<div class="alert alert-warning">';
                                                                echo '<i class="feather icon-file-text"></i> ';
                                                                echo 'File is too large to preview (max 5MB). Click "View/Download" to open the file.';
                                                                echo '</div>';
                                                            } else {
                                                                if ($file_extension === 'txt') {
                                                                    // Handle TXT files - direct reading
                                                                    $content = file_get_contents($full_file_path);
                                                                    if ($content !== false) {
                                                                        echo '<div class="text-preview" style="font-family: monospace; font-size: 12px; line-height: 1.4; white-space: pre-wrap; background: #f8f9fa; padding: 10px; border-radius: 4px;">';
                                                                        echo htmlspecialchars($content);
                                                                        echo '</div>';
                                                                    } else {
                                                                        echo '<div class="alert alert-warning">Unable to read text file content.</div>';
                                                                    }
                                                                } elseif ($file_extension === 'pdf') {
                                                                    // Handle PDF files - try to extract text
                                                                    require_once APPPATH . '../vendor/autoload.php';
                                                                    try {
                                                                        $parser = new \Smalot\PdfParser\Parser();
                                                                        $pdf = $parser->parseFile($full_file_path);
                                                                        $text = $pdf->getText();

                                                                        if (!empty(trim($text))) {
                                                                            echo '<div class="pdf-preview" style="font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; max-height: 400px; overflow-y: auto;">';
                                                                            echo nl2br(htmlspecialchars(substr($text, 0, 2000))); // Limit preview to 2000 chars
                                                                            if (strlen($text) > 2000) {
                                                                                echo '<br><em>... (content truncated - download to read full document)</em>';
                                                                            }
                                                                            echo '</div>';
                                                                        } else {
                                                                            echo '<div class="alert alert-info">PDF appears to be image-based or empty. Click "View/Download" to open the file.</div>';
                                                                        }
                                                                    } catch (Exception $pdfError) {
                                                                        echo '<div class="alert alert-warning">Unable to extract PDF text: ' . htmlspecialchars($pdfError->getMessage()) . ' - Click "View/Download" to open the file.</div>';
                                                                    }
                                                                } elseif (in_array($file_extension, ['doc', 'docx'])) {
                                                                    // Handle DOC/DOCX files - try to extract text
                                                                    require_once APPPATH . '../vendor/autoload.php';
                                                                    try {
                                                                        $phpWord = \PhpOffice\PhpWord\IOFactory::load($full_file_path);
                                                                        $text = '';

                                                                        foreach ($phpWord->getSections() as $section) {
                                                                            foreach ($section->getElements() as $element) {
                                                                                if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                                                                                    foreach ($element->getElements() as $textElement) {
                                                                                        if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                                                                                            $text .= $textElement->getText() . ' ';
                                                                                        }
                                                                                    }
                                                                                } elseif ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                                                                                    $text .= $element->getText() . ' ';
                                                                                }
                                                                            }
                                                                        }

                                                                        if (!empty(trim($text))) {
                                                                            echo '<div class="doc-preview" style="font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; max-height: 400px; overflow-y: auto;">';
                                                                            echo nl2br(htmlspecialchars(substr($text, 0, 2000))); // Limit preview to 2000 chars
                                                                            if (strlen($text) > 2000) {
                                                                                echo '<br><em>... (content truncated - download to read full document)</em>';
                                                                            }
                                                                            echo '</div>';
                                                                        } else {
                                                                            echo '<div class="alert alert-info">Document appears to be image-based or empty. Click "View/Download" to open the file.</div>';
                                                                        }
                                                                    } catch (Exception $docError) {
                                                                        echo '<div class="alert alert-warning">Unable to extract DOC text: ' . htmlspecialchars($docError->getMessage()) . ' - Click "View/Download" to open the file.</div>';
                                                                    }
                                                                }
                                                            }
                                                        } catch (Exception $e) {
                                                            echo '<div class="alert alert-danger">';
                                                            echo '<i class="feather icon-alert-triangle"></i> ';
                                                            echo 'Error reading file: ' . htmlspecialchars($e->getMessage());
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