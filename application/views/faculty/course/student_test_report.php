<!-- View file: application/views/course/student_test_report.php -->
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="font-weight-bold mb-5">Student Test Report</h4>
                <div class="text-muted small mt-0 d-block breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url($url.'/'.$route) ?>">Courses</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url($url.'/'.$route.'/'.'modules/'.$course['id']) ?>">Modules</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url($url.'/'.$route.'/'.'module_tests/'.$course['id'].'/'.$module['id']) ?>">Module Tests</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url($url.'/'.$route.'/'.'test_results/'.$course['id'].'/'.$module['id'].'/'.$test['id']) ?>">Test Results</a></li>
                        <li class="breadcrumb-item active">Student Report</li>
                    </ol>
                </div>
            </div>
            <div class="export-buttons">
                <!-- <a href="<?= base_url($url.'/course/export_test_report/'.$course['id'].'/'.$module['id'].'/'.$test['id'].'/'.$student['id']) ?>" class="btn btn-primary mr-2">
                    <i class="fas fa-file-export"></i> Export HTML Report
                </a> -->
                <a href="<?= base_url($url.'/course/export_test_report_csv/'.$course['id'].'/'.$module['id'].'/'.$test['id'].'/'.$student['id']) ?>" class="btn btn-success">
                    <i class="fas fa-file-csv"></i> Export CSV Report
                </a>
            </div>
        </div>

        <!-- Student Overview Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded p-3 mr-4">
                                <i class="feather icon-user" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-1"><?= $student['name'] ?></h5>
                                <div class="text-muted small">
                                    <?= $student['email'] ?> | Department: <?= $student['department_name'] ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4 col-sm-6 mb-3">
                                <div class="text-muted small">Test:</div>
                                <div><?= $test['title'] ?></div>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-3">
                                <div class="text-muted small">Module:</div>
                                <div><?= $module['name'] ?></div>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-3">
                                <div class="text-muted small">Submission Date:</div>
                                <div><?= date('M d, Y h:i A', strtotime($submission['submission_time'])) ?></div>
                            </div>
                        </div>

                        <!-- Additional Submission Details -->
                        <!-- <div class="submission-details mt-4">
                            <h6 class="text-primary mb-3">Submission Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-item mb-2">
                                        <span class="text-muted">IP Address:</span>
                                        <span class="ml-2"><?= $submission['user_ip'] ?? 'N/A' ?></span>
                                    </div>
                                    <div class="detail-item mb-2">
                                        <span class="text-muted">Browser:</span>
                                        <span class="ml-2"><?= $submission['user_agent'] ?? 'N/A' ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item mb-2">
                                        <span class="text-muted">Screen Resolution:</span>
                                        <span class="ml-2">
                                            <?= isset($submission['screen_resolution']) ? 
                                                $submission['screen_resolution']['width'] . 'x' . $submission['screen_resolution']['height'] : 
                                                'N/A' ?>
                                        </span>
                                    </div>
                                    <div class="detail-item mb-2">
                                        <span class="text-muted">Window Size:</span>
                                        <span class="ml-2">
                                            <?= isset($submission['window_resolution']) ? 
                                                $submission['window_resolution']['width'] . 'x' . $submission['window_resolution']['height'] : 
                                                'N/A' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <?php 
                            $details = json_decode($submission['details'], true);
                            
                             
                            $recordings = $details['sessionRecordings'] ?? [];
                            // print_r($recordings);
                            
                            $images = $details['trackingData'] ?? [];
                            // print_r($images);
                            // exit;
                        ?>

                       <div class="row mt-4">
                            <!-- Left: Recording iframe -->
                            <div class="col-md-12 mb-3"> <!-- Full width -->
                                <h6 class="text-primary">Session Recording</h6>
                                <?php if (!empty($recordings)): ?>
                                    <div class="embed-responsive embed-responsive-16by9">
                                        <iframe class="embed-responsive-item" src="<?= $recordings[0] ?>" allowfullscreen></iframe>
                                    </div>
                                    <!-- Open in new tab -->
                                    <a href="<?= $recordings[0] ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                        Open in New Tab
                                    </a>
                                <?php else: ?>
                                    <p class="text-muted">No session recording available.</p>
                                <?php endif; ?>
                            </div>

                            <!-- Right: Captured Images -->
                            <div class="col-md-12 mb-3">
                                <h6 class="text-primary">Captured Images</h6>
                                <?php if (!empty($images)): ?>
                                    <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                        <table class="datatable table table-bordered table-sm">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Image</th>
                                                    <th>Captured At</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($images as $img): ?>
                                                    <tr>
                                                        <td>
                                                            <img src="<?= $img['url'] ?>" 
                                                                alt="User Image" 
                                                                width="100" 
                                                                height="80" 
                                                                style="cursor: pointer;" 
                                                                onclick="showImageModal('<?= $img['url'] ?>')" />
                                                        </td>
                                                        <td><?= date('M d, Y h:i A', strtotime($img['createdAt'])) ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">No proctoring images captured.</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Image Modal -->
                        <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-body text-center">
                                        <img id="modalImage" src="" class="img-fluid" alt="Enlarged Image" />
                                    </div>
                                </div>
                            </div>
                        </div>




                    </div>
                    
                    <div class="col-lg-3">
                        <div class="text-center">
                            <div class="card shadow-none bg-light mb-3">
                                <div class="card-body py-3">
                                    <div class="font-weight-bold mb-2">Score</div>
                                    <h3 class="text-primary"><?= number_format($submission['percentage'], 1) ?>%</h3>
                                    <div class="text-muted small"><?= $submission['earned_score'] ?>/<?= $submission['total_score'] ?> points</div>
                                </div>
                            </div>
                            
                            <div class="card shadow-none bg-light mb-3">
                                <div class="card-body py-3">
                                    <div class="font-weight-bold mb-2">Result</div>
                                    <?php if ($submission['percentage'] >= $test['pass_percentage']): ?>
                                        <span class="badge badge-pill badge-success" style="font-size: 1.2em;">Pass</span>
                                    <?php else: ?>
                                        <span class="badge badge-pill badge-danger" style="font-size: 1.2em;">Fail</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php if ($monitoring->enable_time_tracking): ?>
                            <div class="card shadow-none bg-light">
                                <div class="card-body py-3">
                                    <div class="font-weight-bold mb-2">Time Spent</div>
                                    <?php
                                    $total_time_spent = $submission['time_spent'];
                                    $total_hours = floor($total_time_spent / 3600);
                                    $total_minutes = floor(($total_time_spent % 3600) / 60);
                                    $total_seconds = $total_time_spent % 60;
                                    
                                    $total_time_display = '';
                                    if ($total_hours > 0) {
                                        $total_time_display .= $total_hours . 'h ';
                                    }
                                    if ($total_minutes > 0 || $total_hours > 0) {
                                        $total_time_display .= $total_minutes . 'm ';
                                    }
                                    $total_time_display .= $total_seconds . 's';
                                    ?>
                                    <h3 class="text-primary"><?= $total_time_display ?></h3>
                                    <div class="text-muted small">of <?= $test['duration'] ?> min allowed</div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if ($monitoring->capture_tab_change): ?>
                            <div class="card shadow-none bg-light mt-3">
                                <div class="card-body py-3">
                                    <div class="font-weight-bold mb-2">Tab Changes</div>
                                    <h3 class="text-primary"><?= $submission['tab_changes'] ?></h3>
                                    <div class="text-muted small">times switched tabs</div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question Analysis -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-header-title mb-0">Question Analysis</h5>
            </div>
            <div class="card-body">
                <?php
                    $sections_enabled = $sections_enabled ?? false;
                    $questions_by_section = $questions_by_section ?? [];
                    $section_map = $section_map ?? [];
                    $section_order = $section_order ?? [];

                    $render_question_cards = function($questions_set, $section_color = null) use ($student_solutions, $difficulty_map, $monitoring) {
                        foreach ($questions_set as $question) {
                            $solution = isset($student_solutions[$question['question_id']]) ? $student_solutions[$question['question_id']] : null;
                            $card_style = $section_color ? ' style="border-left: 4px solid ' . htmlspecialchars($section_color, ENT_QUOTES, 'UTF-8') . ';"' : '';
                            ?>
                            <div class="question-card"<?php echo $card_style; ?>>
                                <div class="question-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5>Question <?php echo $question['question_order']; ?></h5>
                                        <div>
                                            <?php if ($question['type'] == 1): ?>
                                                <span class="badge badge-info">MCQ</span>
                                            <?php elseif ($question['type'] == 2): ?>
                                                <span class="badge badge-warning">Code</span>
                                            <?php elseif ($question['type'] == 3): ?>
                                                <span class="badge badge-secondary">Fill in Blank</span>
                                            <?php endif; ?>
                                            
                                            <?php 
                                            $difficulty = isset($difficulty_map[$question['difficulty_level']]) ? $difficulty_map[$question['difficulty_level']] : 'Unknown';
                                            $badge_class = 'badge-secondary';
                                            
                                            if ($difficulty == 'Easy') {
                                                $badge_class = 'badge-success';
                                            } elseif ($difficulty == 'Medium') {
                                                $badge_class = 'badge-warning';
                                            } elseif ($difficulty == 'Hard') {
                                                $badge_class = 'badge-danger';
                                            }
                                            ?>
                                            <span class="badge <?php echo $badge_class; ?>"><?php echo $difficulty; ?></span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="question-content">
                                    <div class="mb-4">
                                        <h5 class="text-primary"><?php echo $question['question_title']; ?></h5>
                                        <div class="markdown-content">
                                            <?php echo $question['question_content']; ?>
                                        </div>
                                    </div>
                                    
                                    <?php if ($solution): ?>
                                        <?php if ($question['type'] == 2 && !empty($solution['language']) && $solution['language'] !== 'N/A'): ?>
                                            <div class="submission-details mb-4">
                                                <div class="detail-item mb-2">
                                                    <span class="text-muted">Language:</span>
                                                    <span class="ml-2"><?= $solution['language'] ?></span>
                                                </div>
                                            </div>
                                            <?php if (!empty($solution['plagiarism_score'])): ?>
                                                <div class="font-weight-bold mb-2">Plagiarism Score:</div>
                                                <div class="p-3 bg-white rounded">
                                                    <span class="badge badge-warning">
                                                        <?= $solution['plagiarism_score'] ?>
                                                    </span>
                                                </div>
                                            <?php else: ?>
                                                <div class="font-weight-bold mb-2">Plagiarism Score:</div>
                                                <div class="p-3 bg-white rounded">
                                                    <span class="badge badge-secondary">N/A</span>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <div class="font-weight-bold mb-2">Student's Answer:</div>
                                                <div class="p-3 bg-white rounded">
                                                    <?php if ($question['type'] == 1): // MCQ ?>
                                                        <div class="mcq-options">
                                                            <?php 
                                                            $answered_options = isset($solution['answered_options']) ? $solution['answered_options'] : [];
                                                            if (!empty($answered_options) && isset($solution['options']) && is_array($solution['options'])) {
                                                                foreach ($solution['options'] as $option) {
                                                                    $is_selected = false;
                                                                    foreach ($answered_options as $answered) {
                                                                        if ($answered['id'] == $option['id']) {
                                                                            $is_selected = true;
                                                                            break;
                                                                        }
                                                                    }
                                                                    if ($is_selected) {
                                                                        ?>
                                                                        <div class="mcq-option">
                                                                            <div class="option-content">
                                                                                <?php echo $option['option_text']; ?>
                                                                                <span class="badge badge-info">Selected</span>
                                                                            </div>
                                                                        </div>
                                                                        <?php
                                                                    }
                                                                }
                                                            } else {
                                                                echo '<div class="text-muted">No answer submitted</div>';
                                                            }
                                                            ?>
                                                        </div>
                                                    <?php elseif ($question['type'] == 2): // Code ?>
                                                        <div class="font-weight-bold mb-2">Submitted Code:</div>
                                                        <?php 
                                                        $code_files = [];

                                                        if (isset($solution['solution'])) {
                                                            $decoded_solution = json_decode($solution['solution'], true);
                                                            if (isset($decoded_solution['files'])) {
                                                                $code_files = $decoded_solution['files'];
                                                            }
                                                        }
                                                        if (!empty($code_files)) {
                                                            foreach ($code_files as $file) {
                                                                if (isset($file['name']) && isset($file['content'])) {
                                                                    ?>
                                                                    <div class="code-file mb-3">
                                                                        <div class="code-file-header">
                                                                            <i class="fas fa-file-code"></i>
                                                                            <span class="ml-2"><?php echo htmlspecialchars($file['name']); ?></span>
                                                                        </div>
                                                                        <pre class="code-content"><code class="language-java"><?php echo htmlspecialchars($file['content']); ?></code></pre>
                                                                    </div>
                                                                    <?php
                                                                }
                                                            }
                                                        } else {
                                                            echo '<div class="text-muted">No code submitted</div>';
                                                        }
                                                        ?>
                                                    <?php elseif ($question['type'] == 3): // Fill in Blank ?>
                                                        <?php
                                                        $student_answer = '';
                                                        if (!empty($solution['answered_text'])) {
                                                            $decoded = json_decode($solution['answered_text'], true);
                                                            $student_answer = $decoded['answer'] ?? '';
                                                        }
                                                        ?>
                                                        <?php if (!empty($student_answer)): ?>
                                                            <?php echo htmlspecialchars($student_answer); ?>
                                                        <?php else: ?>
                                                            <div class="text-muted">No answer submitted</div>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="font-weight-bold mb-2">Correct Answer:</div>
                                                <div class="p-3 bg-white rounded">
                                                    <?php if ($question['type'] == 1): // MCQ ?>
                                                        <div class="mcq-options">
                                                            <?php foreach ($solution['options'] as $option): ?>
                                                                <div class="mcq-option <?php echo $option['is_correct'] ? 'correct-option' : ''; ?>">
                                                                    <div class="option-content">
                                                                        <?php echo $option['option_text']; ?>
                                                                        <?php if ($option['is_correct']): ?>
                                                                            <span class="badge badge-success">Correct Answer</span>
                                                                        <?php else: ?>
                                                                            <span class="badge badge-secondary">Option</span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php elseif ($question['type'] == 2): // Code ?>
                                                        <div class="font-weight-bold mb-2">Test Cases:</div>
                                                        <?php if (!empty($solution['test_cases'])): ?>
                                                            <div class="test-cases-container">
                                                                <?php foreach ($solution['test_cases'] as $index => $test_case): ?>
                                                                    <div class="test-case-card">
                                                                        <div class="test-case-header">
                                                                            <span class="test-case-number">Test Case #<?php echo $index + 1; ?></span>
                                                                            <?php if (isset($test_case['status'])): ?>
                                                                                <?php if ($test_case['status'] === 1): ?>
                                                                                    <span class="badge badge-success">Passed</span>
                                                                                <?php else: ?>
                                                                                    <span class="badge badge-danger">Failed</span>
                                                                                <?php endif; ?>
                                                                            <?php else: ?>
                                                                                <span class="badge badge-warning">N/A</span>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                        <div class="test-case-content">
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <div class="test-case-section">
                                                                                        <div class="section-header">
                                                                                            <i class="fas fa-arrow-right"></i>
                                                                                            <span>Input</span>
                                                                                        </div>
                                                                                        <pre class="test-case-code"><?php echo htmlspecialchars($test_case['input']); ?></pre>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="test-case-section">
                                                                                        <div class="section-header">
                                                                                            <i class="fas fa-arrow-left"></i>
                                                                                            <span>Expected Output</span>
                                                                                        </div>
                                                                                        <pre class="test-case-code"><?php echo isset($test_case['expectedOutput']) ? htmlspecialchars($test_case['expectedOutput']) : htmlspecialchars($test_case['output']); ?></pre>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <?php if (isset($test_case['actual_output'])): ?>
                                                                                <div class="row mt-3">
                                                                                    <div class="col-12">
                                                                                        <div class="test-case-section">
                                                                                            <div class="section-header">
                                                                                                <i class="fas fa-code"></i>
                                                                                                <span>Actual Output</span>
                                                                                            </div>
                                                                                            <pre class="test-case-code"><?php echo htmlspecialchars($test_case['actual_output']); ?></pre>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                            <?php if (isset($test_case['error'])): ?>
                                                                                <div class="row mt-3">
                                                                                    <div class="col-12">
                                                                                        <div class="test-case-section error-section">
                                                                                            <div class="section-header">
                                                                                                <i class="fas fa-exclamation-triangle"></i>
                                                                                                <span>Error</span>
                                                                                            </div>
                                                                                            <pre class="test-case-code error-message"><?php echo htmlspecialchars($test_case['error']); ?></pre>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            <?php endif; ?>
                                                                        </div>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="alert alert-info">
                                                                <i class="fas fa-info-circle"></i>
                                                                No test cases available
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php elseif ($question['type'] == 3): // Fill in Blank ?>
                                                        <?php echo htmlspecialchars($solution['correct_answer']); ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php if (!empty($solution['feedback'])): ?>
                                            <div class="explanation-section">
                                                <div class="explanation-header">
                                                    <i class="fas fa-lightbulb text-warning"></i>
                                                    <strong class="ms-2">Feedback:</strong>
                                                </div>
                                                <div class="explanation-content markdown-content mt-2">
                                                    <?php echo $solution['feedback']; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="row mt-3">
                                            <div class="col-md-3">
                                                <div class="font-weight-bold mb-2">Score:</div>
                                                <div class="p-3 bg-white rounded">
                                                    <?php echo $solution['score']; ?>/<?php echo $solution['max_score']; ?> points
                                                </div>
                                            </div>
                                            <?php if (!empty($monitoring) && $monitoring->enable_time_tracking): ?>
                                            <div class="col-md-3">
                                                <div class="font-weight-bold mb-2">Time Spent:</div>
                                                <div class="p-3 bg-white rounded">
                                                    <?php
                                                    $time_spent = $solution['time_spent'];
                                                    $hours = floor($time_spent / 3600);
                                                    $minutes = floor(($time_spent % 3600) / 60);
                                                    $seconds = $time_spent % 60;
                                                    
                                                    $time_display = '';
                                                    if ($hours > 0) {
                                                        $time_display .= $hours . 'h ';
                                                    }
                                                    if ($minutes > 0 || $hours > 0) {
                                                        $time_display .= $minutes . 'm ';
                                                    }
                                                    $time_display .= $seconds . 's';
                                                    echo $time_display;
                                                    ?>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <div class="col-md-3">
                                                <div class="font-weight-bold mb-2">Result:</div>
                                                <div class="p-3 bg-white rounded">
                                                    <?php
                                                    $answer_submitted = false;
                                                    
                                                    if ($question['type'] == 1) {
                                                        $answered_options = isset($solution['answered_options']) ? $solution['answered_options'] : [];
                                                        $answer_submitted = !empty($answered_options) && isset($solution['options']) && is_array($solution['options']);
                                                    } elseif ($question['type'] == 2) {
                                                        $code_files = [];
                                                        if (isset($solution['solution'])) {
                                                            $decoded_solution = json_decode($solution['solution'], true);
                                                            if (isset($decoded_solution['files'])) {
                                                                $code_files = $decoded_solution['files'];
                                                            }
                                                        }
                                                        $answer_submitted = !empty($code_files);
                                                    } elseif ($question['type'] == 3) {
                                                        $answer_submitted = !empty($solution['answered_text']);
                                                    }
                                                    
                                                    if (!$answer_submitted): ?>
                                                        <span class="badge  badge-info">Not Attempted</span>
                                                    <?php elseif ($solution['score'] == $solution['max_score']): ?>
                                                        <span class="badge badge-success">Correct</span>
                                                    <?php elseif ($solution['score'] > 0): ?>
                                                        <span class="badge badge-warning">Partial</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Incorrect</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="submission-details mt-4">
                                            <h6 class="text-primary mb-3">Submission Details</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="detail-item mb-2">
                                                        <span class="text-muted">IP Address:</span>
                                                        <span class="ml-2"><?php echo $solution['submission_details']['ip_address']; ?></span>
                                                    </div>
                                                    <div class="detail-item mb-2">
                                                        <span class="text-muted">Browser:</span>
                                                        <span class="ml-2"><?php echo $solution['submission_details']['browser']; ?></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="detail-item mb-2">
                                                        <span class="text-muted">Screen Resolution:</span>
                                                        <span class="ml-2"><?php echo $solution['submission_details']['screen_resolution']; ?></span>
                                                    </div>
                                                    <div class="detail-item mb-2">
                                                        <span class="text-muted">Window Size:</span>
                                                        <span class="ml-2"><?php echo $solution['submission_details']['window_size']; ?></span>
                                                    </div>

                                                    <div class="detail-item mb-2">
                                                        <span class="text-muted">Submission Count:</span>
                                                        <span class="ml-2"><?php echo isset($solution['submission_count']) ? $solution['submission_count'] : 0; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning">
                                            No answer submitted for this question.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php
                        }
                    };
                ?>
                <?php if (!empty($questions)): ?>
                    <?php if (!empty($sections_enabled) && !empty($questions_by_section)): ?>
                        <?php
                            $section_colors = [
                                '#674eeb',
                                '#28a745',
                                '#dc3545',
                                '#ffc107',
                                '#17a2b8',
                                '#6f42c1',
                                '#e83e8c',
                                '#fd7e14',
                            ];
                            $ordered_sections = !empty($section_order) ? $section_order : array_keys($questions_by_section);
                            $section_index = 0;
                        ?>
                        <?php foreach ($ordered_sections as $section_key): ?>
                            <?php
                                if (!isset($questions_by_section[$section_key])) {
                                    continue;
                                }
                                $section_questions = $questions_by_section[$section_key];
                                $section_color = $section_colors[$section_index % count($section_colors)];
                                $section_index++;
                                $section_name = isset($section_map[$section_key])
                                    ? $section_map[$section_key]
                                    : (is_numeric($section_key) ? 'Section ' . $section_key : 'Unassigned Questions');
                            ?>
                            <div class="section-header-row" style="--section-color: <?php echo htmlspecialchars($section_color, ENT_QUOTES, 'UTF-8'); ?>;">
                                <div class="d-flex align-items-center py-3 px-3">
                                    <i class="fas fa-layer-group me-2" style="color: <?php echo htmlspecialchars($section_color, ENT_QUOTES, 'UTF-8'); ?>;"></i>
                                    <strong class="section-title">
                                        <?php echo htmlspecialchars($section_name); ?>
                                    </strong>
                                    <span class="badge bg-secondary section-badge">
                                        <?php echo count($section_questions); ?> question(s)
                                    </span>
                                </div>
                            </div>
                            <?php $render_question_cards($section_questions, $section_color); ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php $render_question_cards($questions); ?>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-info">
                        No questions found for this test.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .question-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 24px;
        padding: 32px;
        transition: all 0.2s ease;
        border: 1px solid #f0f0f0;
    }
    .question-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .question-header {
        border-bottom: 1px solid #f0f0f0;
        margin-bottom: 20px;
        padding-bottom: 16px;
    }
    
    .section-header-row {
        background: #f8f9ff;
        border-radius: 12px;
        margin-bottom: 16px;
        border-left: 4px solid var(--section-color, #674eeb);
        border-right: 1px solid #e0e5ff;
        border-top: 1px solid #e0e5ff;
        border-bottom: 1px solid #e0e5ff;
    }

    .section-header-row .section-title {
        color: var(--section-color, #674eeb);
        font-size: 1.1em;
    }

    .section-header-row .section-badge {
        background: linear-gradient(135deg, var(--section-color, #674eeb) 0%, rgba(103, 78, 235, 0.85) 100%) !important;
        color: #fff;
        margin-left: 12px;
    }
    
    .badge {
        padding: 0.5em 0.85em;
        font-size: 0.85rem;
        font-weight: 500;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 80px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .badge-success {
        background-color: #f0fff4;
        color: #38a169;
        border: 1px solid #c6f6d5;
    }
    
    .badge-danger {
        background-color: #fff5f5;
        color: #e53e3e;
        border: 1px solid #fed7d7;
    }
    
    .badge-secondary {
        background-color: #f7fafc;
        color: #4a5568;
        border: 1px solid #e2e8f0;
    }
    
    .badge-info {
        background-color: #f0f7ff;
        color: #4a6cf7;
        border: 1px solid #e2e8f0;
    }
    
    .badge-warning {
        background-color: #fff8e6;
        color: #ffa800;
        border: 1px solid #feebc8;
    }
    
    .explanation-section {
        margin-top: 1.2rem;
        padding-top: 1rem;
        border-top: 1px solid #f0f0f0;
    }
    
    .explanation-header {
        color: #4a6cf7;
        margin-bottom: 0.5rem;
        font-size: 1rem;
    }
    
    .explanation-content {
        color: #333;
        line-height: 1.7;
    }

    /* Submission Details Styling */
    .submission-details {
        background-color: #f8fafc;
        border-radius: 8px;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
    }

    .detail-item {
        display: flex;
        align-items: center;
        font-size: 0.95rem;
    }

    .detail-item .text-muted {
        min-width: 120px;
    }

    /* Markdown Content Styling */
    .markdown-content {
        line-height: 1.6;
        color: #333;
        font-size: 1.1rem;
        padding: 1.5rem;
        background-color: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        margin-top: 1rem;
        word-wrap: break-word;
        overflow-wrap: break-word;
        max-width: 100%;
    }

    .markdown-content h1 { font-size: 2rem; }
    .markdown-content h2 { font-size: 1.75rem; }
    .markdown-content h3 { font-size: 1.5rem; }
    .markdown-content h4 { font-size: 1.25rem; }
    .markdown-content h5 { font-size: 1.15rem; }
    .markdown-content h6 { font-size: 1.1rem; }

    .markdown-content p {
        margin-bottom: 1em;
        font-size: 1.1rem;
    }

    .markdown-content ul,
    .markdown-content ol {
        margin-bottom: 1em;
        padding-left: 1.5em;
        font-size: 1.1rem;
    }

    .markdown-content li {
        margin-bottom: 0.5em;
    }

    .markdown-content blockquote {
        margin: 1em 0;
        padding: 1em 1.25em;
        border-left: 4px solid #4a6cf7;
        background-color: #f8f9fa;
        color: #666;
        font-size: 1.1rem;
    }

    .markdown-content pre {
        background-color: #f8f9fa;
        padding: 1.25rem;
        border-radius: 4px;
        margin: 1em 0;
        border: 1px solid #e2e8f0;
        font-size: 1rem;
        white-space: pre-wrap;
        word-wrap: break-word;
        overflow-x: auto;
    }

    .markdown-content code {
        background-color: #f8f9fa;
        padding: 0.2rem 0.4rem;
        border-radius: 3px;
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 1rem;
    }

    .markdown-content table {
        width: 100%;
        margin: 1em 0;
        border-collapse: collapse;
        font-size: 1.1rem;
        overflow-x: auto;
        display: block;
    }

    .markdown-content table th,
    .markdown-content table td {
        padding: 0.75em;
        border: 1px solid #e2e8f0;
        min-width: 100px;
    }

    .markdown-content table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .markdown-content img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        margin: 1em auto;
        display: block;
    }

    .markdown-content p img {
        margin: 1em auto;
    }

    .markdown-content hr {
        margin: 1.5em 0;
        border: none;
        border-top: 1px solid #e2e8f0;
    }

    /* Code Block Styling */
    pre {
        /* background-color: #1e1e1e !important; */
        /* border-radius: 6px !important;
        padding: 1rem !important;
        margin: 1rem 0 !important;
        border: 0.2px solid #2d2d2d !important; */
    }

    pre code {
        color:rgb(0, 0, 0) !important;
        font-family: 'Fira Code', 'Consolas', monospace !important;
        font-size: 0.9rem !important;
        line-height: 1.5 !important;
    }

    /* Card Styling */
    .card {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border-radius: 12px;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 1.25rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Alert Styling */
    .alert {
        border: none;
        border-radius: 8px;
        padding: 1rem 1.25rem;
    }

    .alert-warning {
        background-color: #fff8e6;
        color: #ffa800;
    }

    .alert-info {
        background-color: #f0f7ff;
        color: #4a6cf7;
    }

    /* Test Cases Styling */
    .test-cases-container {
        margin-top: 1rem;
    }

    .test-case-card {
        background: #fff;
        border: 1px solid #edf2f7;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.2s ease;
        margin-bottom: 1.5rem;
    }

    .test-case-card:hover {
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .test-case-header {
        background-color: #f8fafc;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .test-case-number {
        font-weight: 500;
        color: #4a5568;
    }

    .test-case-content {
        padding: 1rem;
    }

    .test-case-section {
        background: #f8fafc;
        border-radius: 6px;
        padding: 1rem;
        height: 100%;
    }

    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
        color: #4a5568;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .section-header i {
        margin-right: 0.5rem;
        font-size: 0.9rem;
    }

    .test-case-code {
        background-color: #1e1e1e;
        color: rgb(255, 255, 255);
        padding: 0.75rem;
        border-radius: 4px;
        margin: 0;
        font-family: 'Fira Code', 'Consolas', monospace;
        font-size: 0.9rem;
        line-height: 1.5;
        overflow-x: auto;
        border: 1px solid #2d2d2d;
    }

    .error-section {
        background-color: #2d1e1e;
        border: 1px solid #3d2e2e;
    }

    .error-message {
        color: #ff6b6b;
        background-color: #2d1e1e;
        border-color: #3d2e2e;
    }

    /* MCQ Options Styling */
    .mcq-options {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .mcq-option {
        position: relative;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        transition: all 0.2s ease;
        margin-bottom: 0.5rem;
    }

    .mcq-option:last-child {
        margin-bottom: 0;
    }

    .mcq-option:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .correct-option {
        background: #f0fff4;
        border-color: #c6f6d5;
    }

    .correct-option:hover {
        background: #e6ffed;
        border-color: #9ae6b4;
    }

    .option-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .option-content .badge {
        flex-shrink: 0;
    }

    /* Add styles for selected options */
    .selected-option {
        background: #f0f7ff;
        border-color: #c6f6d5;
    }

    .selected-option:hover {
        background: #e6ffed;
        border-color: #9ae6b4;
    }

    .selected-option .badge-info {
        background-color: #f0f7ff;
        color: #4a6cf7;
        border: 1px solid #e2e8f0;
    }

    /* Text muted style */
    .text-muted {
        color: #718096;
        /* font-style: italic; */
    }

    /* Update test case badges */
    .test-case-header .badge {
        min-width: 90px;
        font-size: 0.8rem;
        padding: 0.4em 0.8em;
    }

    /* Update result badges */
    .badge-pill {
        padding: 0.6em 1.2em;
        font-size: 0.9rem;
        border-radius: 50px;
    }

    /* Responsive adjustments for badges */
    @media (max-width: 768px) {
        .badge {
            padding: 0.4em 0.7em;
            font-size: 0.8rem;
            min-width: 70px;
        }
        
        .option-content {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .option-content .badge {
            align-self: flex-start;
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .test-case-content {
            padding: 0.75rem;
        }
        
        .test-case-section {
            margin-bottom: 1rem;
        }
        
        .test-case-section:last-child {
            margin-bottom: 0;
        }
    }

    /* Code File Styling */
    .code-file {
        background: #1e1e1e;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .code-file-header {
        background: #2d2d2d;
        padding: 0.75rem 1rem;
        color: #d4d4d4;
        font-size: 0.9rem;
        border-bottom: 1px solid #3d3d3d;
        display: flex;
        align-items: center;
    }

    .code-file-header i {
        color: #4a6cf7;
        margin-right: 0.5rem;
    }

    .code-content {
        margin: 0;
        padding: 1rem;
        background: #1e1e1e;
        border: none;
        border-radius: 0;
    }

    .code-content code {
        font-family: 'Fira Code', 'Consolas', monospace;
        font-size: 0.9rem;
        line-height: 1.5;
        color: #d4d4d4;
    }
</style>

<!-- Include marked.js for markdown parsing -->
<script src="<?= base_url("/") ?>assets/packages/highlight.min.js"></script>
<script src="<?= base_url("/") ?>assets/packages/marked.min.js"></script>
<script>
    function showImageModal(url) {
    document.getElementById('modalImage').src = url;
    $('#imageModal').modal('show');
    }
    document.addEventListener('DOMContentLoaded', function() {
        // Configure marked.js
        marked.setOptions({
            highlight: function(code, lang) {
                if (lang && hljs.getLanguage(lang)) {
                    return hljs.highlight(code, { language: lang }).value;
                }
                return hljs.highlightAuto(code).value;
            },
            breaks: true,
            gfm: true,
            headerIds: true,
            mangle: false,
            pedantic: false,
            smartLists: true,
            smartypants: true
        });

        // Parse all markdown content
        document.querySelectorAll('.markdown-content').forEach(function(element) {
            let content = element.innerHTML.trim();
            
            // Check if content contains markdown elements
            const hasMarkdown = content.includes('```') || 
                              content.includes('~~~') || 
                              content.includes('![') || 
                              content.includes('#') ||
                              content.includes('*') ||
                              content.includes('_') ||
                              content.includes('>') ||
                              content.includes('- ') ||
                              content.includes('1. ');

            if (hasMarkdown) {
                // For content with markdown elements, use marked
                element.innerHTML = marked.parse(content);
            } else {
                // For simple text, preserve line breaks and escape HTML
                element.innerHTML = content
                    .replace(/&(?![a-zA-Z]+;)/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/\n/g, '<br>');
            }
        });

        // Apply syntax highlighting to code blocks
        if (typeof hljs !== 'undefined') {
            document.querySelectorAll('pre code').forEach((block) => {
                hljs.highlightElement(block);
            });
        }

        // Ensure images are properly sized and responsive
        document.querySelectorAll('.markdown-content img').forEach(img => {
            img.style.maxWidth = '100%';
            img.style.height = 'auto';
            img.style.display = 'block';
            img.style.margin = '1em auto';
        });
    });
</script> 