<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">

<!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3 justify-content-between">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= base_url('test') ?>">Tests</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= isset($test) ? 'Edit Test' : 'Create Test' ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0"><?= isset($test) ? 'Edit Test' : 'Create New Test' ?></h6>
            </nav>
            <div class="collapse navbar-collapse flex-grow-0 mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <ul class="navbar-nav justify-content-end">
                    <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <?php if ($this->session->flashdata('message')) { ?>
                        <div class="alert alert-<?= $this->session->flashdata('message')[0] ?> alert-dismissible" id="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">×</button>
                            <span class="alert-text" style="color:white">
                                <?= $this->session->flashdata('message')[1] ?>
                            </span>
                        </div>
                    <?php } ?>
                    
                    <?php if (validation_errors()) { ?>
                        <div class="alert alert-danger alert-dismissible" id="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">×</button>
                            <span class="alert-text" style="color:white">
                                <?= validation_errors() ?>
                            </span>
                        </div>
                    <?php } ?>

                    <div class="container-fluid py-4">
                        <div class="">
                            <form id="testForm" action="<?= isset($test) ? base_url($url.'/test/edit/'.$test->id) : base_url($url.'/test/create') ?>" method="post">
                                <!-- Basic Information Card -->
                                <div class="card mb-4">
                                    <div class="card-header pb-0">
                                        <h6 class="font-weight-bolder">Basic Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <label for="title" class="form-label">Test Title <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="title" name="title" 
                                                    value="<?= isset($test) ? htmlspecialchars($test->title ?? '') : '' ?>" required>
                                            </div>
                                            <div class="col-md-6" style="display: none;">
                                                <label for="module_id" class="form-label">Module (Optional)</label>
                                                <select class="form-control select2" id="module_id" name="module_id">
                                                    <option value="">-- Select Module --</option>
                                                    <?php if(!empty($modules)): ?>
                                                        <?php foreach($modules as $module): ?>
                                                            <?php if(is_array($module) && isset($module['id']) && isset($module['name'])): ?>
                                                                <option value="<?= htmlspecialchars($module['id']) ?>" 
                                                                    <?= isset($test) && $test->module_id == $module['id'] ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($module['name']) ?>
                                                                </option>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="pass_percentage" class="form-label">Pass Percentage <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="pass_percentage" name="pass_percentage" 
                                                        min="1" max="100" value="<?= isset($test) ? ($test->pass_percentage ?? 50) : 50 ?>" required>
                                                    <span class="input-group-text">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        
                                        <!-- Instructions Editor -->
                                        <div class="mb-4">
                                            <label for="instructions" class="form-label">Instructions <span class="text-danger">*</span></label>
                                            <div class="markdown-editor-wrapper">
                                                <div class="markdown-tabs">
                                                    <div class="tab-buttons">
                                                        <button type="button" class="tab-btn active" data-mode="write">WRITE</button>
                                                        <button type="button" class="tab-btn" data-mode="preview">PREVIEW</button>
                                                    </div>
                                                    <div class="formatting-toolbar">
                                                        <button type="button" class="tool-btn" data-command="heading" title="Heading"><i class="fas fa-heading"></i></button>
                                                        <button type="button" class="tool-btn" data-command="bold" title="Bold"><i class="fas fa-bold"></i></button>
                                                        <button type="button" class="tool-btn" data-command="italic" title="Italic"><i class="fas fa-italic"></i></button>
                                                        <button type="button" class="tool-btn" data-command="table" title="Table"><i class="fas fa-table"></i></button>
                                                        <button type="button" class="tool-btn" data-command="list-ul" title="Unordered List"><i class="fas fa-list-ul"></i></button>
                                                        <button type="button" class="tool-btn" data-command="list-ol" title="Ordered List"><i class="fas fa-list-ol"></i></button>
                                                        <button type="button" class="tool-btn" data-command="quote" title="Quote"><i class="fas fa-quote-right"></i></button>
                                                        <button type="button" class="tool-btn" data-command="code" title="Code"><i class="fas fa-code"></i></button>
                                                        <button type="button" class="tool-btn" data-command="link" title="Link"><i class="fas fa-link"></i></button>
                                                        <button type="button" class="tool-btn" data-command="image" title="Image"><i class="fas fa-image"></i></button>
                                                    </div>
                                                </div>
                                                <div class="editor-content-area">
                                                    <textarea class="form-control" id="markdown-editor" rows="8" placeholder="Enter test instructions"><?= isset($test) ? htmlspecialchars($test->instructions) : '' ?></textarea>
                                                    <div class="preview-content" style="display: none;"></div>
                                                </div>
                                                <!-- Hidden input to store markdown content for form submission -->
                                                <input type="hidden" id="instructions" name="instructions" 
                                                value="<?= isset($test) ? htmlspecialchars($test->instructions) : '' ?>" required>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-4" style="display: none;">
                                            <div class="col-md-6">
                                                <label for="start_date" class="form-label">Start Date & Time (Optional)</label>
                                                <input type="datetime-local" class="form-control" id="start_date" name="start_date" 
                                                    value="<?= isset($test) ? set_value('start_date', ($test->start_date ? date('Y-m-d\TH:i', strtotime($test->start_date)) : '')) : set_value('start_date') ?>"
                                                >
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <label for="end_date" class="form-label">End Date & Time (Optional)</label>
                                                <input type="datetime-local" class="form-control" id="end_date" name="end_date" 
                                                    value="<?= isset($test) ? set_value('end_date', ($test->end_date ? date('Y-m-d\TH:i', strtotime($test->end_date)) : '')) : set_value('end_date') ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-4">
                                            <div class="col-md-4">
                                                <label class="form-label">Duration <span class="text-danger">*</span></label>
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="input-group">
                                                            <input type="number" class="form-control" id="duration_hours" name="duration_hours" 
                                                                min="0" max="24" value="<?= isset($test) ? floor($test->duration / 60) : 0 ?>">
                                                            <span class="input-group-text">hrs</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="input-group">
                                                            <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" 
                                                                min="0" max="59" value="<?= isset($test) ? ($test->duration % 60) : 30 ?>">
                                                            <span class="input-group-text">min</span>
                                                        </div>
                                                        
                                                    </div>
                                                    <small class="text-muted">This sets the time limit for the challenge,  it will be closed when the total time allocated is over</small>
                                                </div>
                                                <!-- Hidden field to store total minutes -->
                                                <input type="hidden" id="duration" name="duration" 
                                                    value="<?= isset($test) ? $test->duration : 30 ?>">
                                            </div>
                                            <!-- <div class="col-md-6">
                                                <label for="duration" class="form-label">Challenge Time Limit (minutes)</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="duration" name="duration" 
                                                        value="<?= isset($test) ? $test->duration : 30 ?>" min="1" required>
                                                    <span class="input-group-text">minutes</span>
                                                </div>
                                                <small class="text-muted">This sets the time limit for the challenge, the user can open the challenge multiple times but it will be closed when the total time allocated is over</small>
                                            </div> -->
                                            <div class="col-md-4">
                                                <label for="maxattempts" class="form-label">Attempts</label>
                                                <div class="input-group">
                                                    <input type="number"  class="form-control" min="1" id="maxattempts" name="maxattempts" 
                                                        value="<?= isset($test) ? $test->no_of_attempts : 1 ?>" min="1" required>
                                                   
                                                </div>
                                                <small class="text-muted">This sets the maximum number of attempts a user can make to complete the challenge. Once the limit is reached, no further attempts will be allowed.</small>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="negative_mark_value" class="form-label">Negative Mark</label>
                                                    <div class="input-group">
                                                        <input type="number" step="0.5" class="form-control" id="negative_mark_value" name="negative_mark_value" 
                                                            value="<?= isset($test) ? $test->negative_mark_value : '0' ?>" max="0" min="-100" required>
                                                    </div>
                                                  <small class="text-muted">Deducts marks for each wrong answer in test.<br> Please enter a negative value such as -1, -0.5, or -2...</small>
                                            </div>
                                        </div>
                                        
                                          
                            
                                    </div>
                                </div>


                                <!-- Navigation Settings Card -->
                                <div class="card mb-4">
                                    <div class="card-header pb-0">
                                        <h6 class="font-weight-bolder">Navigation Options</h6>
                                    </div>
                                    <div class="card-body">
                                    <div class="row mb-4">

                                    <!-- <div class="col-md-6">
                                        <label for="prev_test_id" class="form-label">Previous Linked Test</label>
                                        <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="Specifies the test that must be completed before this test becomes available"></i>

                                        <select class="form-control select2" id="prev_test_id" name="prev_test_id">
                                            <option value="">-- Select Previous Test --</option>
                                            <?php if(!empty($tests)): ?>
                                                <?php foreach($tests as $linked_test): ?>
                                                    <?php if(isset($test) && $linked_test->id == $test->id) continue; // Skip current test ?>
                                                    <option value="<?= htmlspecialchars($linked_test->id) ?>" 
                                                        <?= isset($test) && $test->prev_test_id == $linked_test->id ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($linked_test->title) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="next_test_id" class="form-label">Next Linked Test</label>
                                        <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="Specifies the test that will be automatically unlocked after completing the current test"></i>
                                        <select class="form-control select2" id="next_test_id" name="next_test_id">
                                            <option value="">-- Select Next Test --</option>
                                            <?php if(!empty($tests)): ?>
                                                <?php foreach($tests as $linked_test): ?>
                                                    <?php if(isset($test) && $linked_test->id == $test->id) continue; // Skip current test ?>
                                                    <option value="<?= htmlspecialchars($linked_test->id) ?>" 
                                                        <?= isset($test) && $test->next_test_id == $linked_test->id ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($linked_test->title) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>

                                    </div> -->
            
                                
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="enable_finish_button" name="enable_finish_button" 
                                                        <?= isset($test) ? ($test->enable_finish_button ? 'checked' : '') : 'checked' ?>>
                                                    <label class="form-check-label" for="enable_finish_button">Enable Finish Button</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="If enabled, the user can finish the challenge otherwise challenge is open to modify submissions all the time"></i>
                                                </div>
                                            </div>
                                            
                                            <!-- <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="show_next_on_last_question" name="show_next_on_last_question" 
                                                        <?= isset($test) ? ($test->show_next_on_last_question ? 'checked':''):'' ?>>
                                                    <label class="form-check-label" for="show_next_on_last_question">Show Next on Last Problem Only</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="The 'Next' button will only appear when the user reaches the last problem, preventing premature navigation"></i>
                                                </div>
                                            </div> -->
                                            <div class="col-md-6">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="is_new_tab" name="is_new_tab" 
                                                        <?= isset($test) ? ($test->is_new_tab ? 'checked':''):'' ?>>
                                                    <label class="form-check-label" for="is_new_tab">Open Test in New Tab</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="If enabled, the test will open in a new browser tab instead of inside the app."></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Proctoring Settings Card -->
                                <div class="card mb-4">
                                    <div class="card-header pb-0">
                                        <h6 class="font-weight-bolder">Proctoring Options</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="disable_copy_paste" name="disable_copy_paste" 
                                                        <?= isset($test) ? ($test->disable_copy_paste ? 'checked' : '') : 'checked' ?>>
                                                    <label class="form-check-label" for="disable_copy_paste">Disable Copy, Paste</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="This stops copying/pasting the code from the editor. Also disables right click"></i>
                                                </div>
                                                
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="shuffle_questions" name="shuffle_questions"
                                                        <?= isset($test) ? ($test->shuffle_questions ? 'checked':''):'checked' ?>>
                                                    <label class="form-check-label" for="shuffle_questions">Shuffle Problem Order</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="Randomizes the order of problems for each user to prevent cheating and ensure fair assessment"></i>
                                                </div>
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="shuffle_answer_options" name="shuffle_answer_options"
                                                        <?= isset($test) ? ($test->shuffle_answer_options ? 'checked':''):'checked' ?>>
                                                    <label class="form-check-label" for="shuffle_answer_options">Shuffle MCQ options (MCQs only)</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="Randomizes the order of problem answer options for each user, preventing cheating and ensuring fair assessment"></i>
                                                </div>
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="plagiarism_check" name="plagiarism_check" 
                                                        <?= isset($test) ? ($test->plagiarism_check ? 'checked':''):'' ?>>
                                                    <label class="form-check-label" for="plagiarism_check">Plagiarism Checker</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="Analyzes submissions to detect potential plagiarism by comparing with other submissions and known solutions"></i>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="force_full_screen" name="force_full_screen" 
                                                        <?= isset($test) ? ($test->force_full_screen ? 'checked':''):'checked' ?>>
                                                    <label class="form-check-label" for="force_full_screen">Force Fullscreen</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="Forces the challenge to open in fullscreen mode to prevent users from accessing other applications or browser tabs"></i>
                                                </div>
                                                
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="show_tab_change_warning" name="show_tab_change_warning" 
                                                        <?= isset($test) ? ($test->show_tab_change_warning ? 'checked':''):'checked' ?>>
                                                    <label class="form-check-label" for="show_tab_change_warning">Show Tab Change Warning</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="This shows a warning message when the user tries to switch tabs on the Top middle section"></i>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <div class="form-check form-switch mb-2">
                                                        <input class="form-check-input" type="checkbox" id="close_after_tab_change" name="close_after_tab_change" 
                                                            <?= isset($test) ? ($test->close_after_tab_change ? 'checked':''):'checked' ?>>
                                                        <label class="form-check-label" for="close_after_tab_change">Close Challenge After Tab Switches</label>
                                                        <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="Challenge closed if users switch the tab more than x times"></i>
                                                    </div>
                                                    
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" id="close_after_tab_count" name="close_after_tab_count" 
                                                            value="<?= isset($test) ? ($test->close_after_tab_count ? $test->close_after_tab_count : ''):'3' ?>" min="1">
                                                        <span class="input-group-text">tab switches</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- User Tracking Settings Card -->
                                <div class="card mb-4">
                                    <div class="card-header pb-0">
                                        <h6 class="font-weight-bolder">User Tracking</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="capture_tab_change" name="capture_tab_change" 
                                                        <?= isset($test) ? ($test->capture_tab_change ? 'checked':''):'checked' ?>>
                                                    <label class="form-check-label" for="capture_tab_change">Capture Tab Changes</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="Records the number of times the user switches the tabs, required for Close challenge after x tab switches feature to work"></i>
                                                </div>
                                                
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="capture_user_image" name="capture_user_image" 
                                                        <?= isset($test) ? ($test->capture_user_image ? 'checked':''):'' ?>>
                                                    <label class="form-check-label" for="capture_user_image">Capture User Images</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="Capture feed of user photos via camera"></i>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="enable_time_tracking" name="enable_time_tracking" 
                                                        <?= isset($test) ? ($test->enable_time_tracking ? 'checked':''):'checked' ?>>
                                                    <label class="form-check-label" for="enable_time_tracking">Enable Time Tracking</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="This tracks the amount of time the user spends on each problem also at the complete challenge"></i>
                                                </div>
                                                
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="record_user_session" name="record_user_session" 
                                                        <?= isset($test) ? ($test->record_user_session ? 'checked':''):'' ?>>
                                                    <label class="form-check-label" for="record_user_session">Record User Session</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="Records the complete user session, video will be available in the report"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Information Options Card -->
                                <div class="card mb-4">
                                    <div class="card-header pb-0">
                                        <h6 class="font-weight-bolder">Information Options</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="show_instructions_on_start" name="show_instructions_on_start" 
                                                        <?= isset($test) ? ($test->show_instructions_on_start ? 'checked':''):'checked' ?>>
                                                    <label class="form-check-label" for="show_instructions_on_start">Show Instructions on Start</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="This shows an instructions page on Challenge Start. The challenge description is presented in the instructions"></i>
                                                </div>
                                                
                                                    <!-- <div class="form-check form-switch mb-3">
                                                        <input class="form-check-input" type="checkbox" id="hide_score_after_title" name="hide_score_after_title" 
                                                            <?= isset($test) ? ($test->hide_score_after_title ? 'checked':''):''  ?>>
                                                        <label class="form-check-label" for="hide_score_after_title">Hide Score after Problem Title</label>
                                                        <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="This hides the score in the problems list"></i>
                                                    </div> -->
                                                
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="show_report_on_finish" name="show_report_on_finish" 
                                                        <?= isset($test) ? ($test->show_report_on_finish ? 'checked':''):'' ?>>
                                                    <label class="form-check-label" for="show_report_on_finish">Show Report after Finish</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="This enables users to see the report after finishing the challenge"></i>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="hide_test_end_time" name="hide_test_end_time" 
                                                        <?= isset($test) ? ($test->hide_test_end_time ? 'checked':''):''  ?>>
                                                    <label class="form-check-label" for="hide_test_end_time">Hide Challenge End Time</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="Hides the challenge end time in the challenge footer"></i>
                                                </div>
                                                
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="auto_submit" name="auto_submit" 
                                                        <?= isset($test) ? ($test->auto_submit ? 'checked':''):'checked'  ?>>
                                                    <label class="form-check-label" for="auto_submit">Auto Submit (MCQs only)</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="Automatically submits multiple choice questions when an answer is selected, providing immediate feedback.Please turn off this option when creating fill-in-the-blanks tests, as answers are manually submitted for each problem."></i>
                                                </div>
                                                
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="hide_problem_menu" name="hide_problem_menu" 
                                                        <?= isset($test) ? ($test->hide_problem_menu ? 'checked':''):'' ?>>
                                                    <label class="form-check-label" for="hide_problem_menu">Hide Problems Menu</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="Conceals the problems navigation menu to prevent users from seeing all available problems at once"></i>
                                                </div>
                                                
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="enable_sections" name="enable_sections" 
                                                        <?= isset($test) ? ($test->enable_sections ? 'checked':''):'' ?>>
                                                    <label class="form-check-label" for="enable_sections">Enable Sections</label>
                                                    <i class="fas fa-info-circle ms-2" data-bs-toggle="tooltip" title="Enable sectional tests to organize questions into different sections (e.g., Quantitative, Verbal, Coding). Sections can be managed after creating/editing the test."></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Submit buttons -->
                                <div class="mt-4 d-flex justify-content-between">
                                    <a href="<?= base_url($url.'/test') ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to Tests
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> <?= isset($test) ? 'Update Test' : 'Create Test' ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Upload Modal -->
    <div class="modal fade" id="imageUploadModal" tabindex="-1" aria-labelledby="imageUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageUploadModalLabel">Insert Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="imageTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="url-tab" data-bs-toggle="tab" data-bs-target="#url-pane" type="button" role="tab" aria-controls="url-pane" aria-selected="true">Image URL</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload-pane" type="button" role="tab" aria-controls="upload-pane" aria-selected="false">Upload Image</button>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="imageTabContent">
                        <div class="tab-pane fade show active" id="url-pane" role="tabpanel" aria-labelledby="url-tab">
                            <div class="mb-3">
                                <label for="imageUrl" class="form-label">Image URL</label>
                                <input type="text" class="form-control" id="imageUrl" placeholder="https://example.com/image.jpg">
                            </div>
                            <div class="mb-3">
                                <label for="imageAltText" class="form-label">Alt Text (for accessibility)</label>
                                <input type="text" class="form-control" id="imageAltText" placeholder="Description of the image">
                            </div>
                        </div>
                        <div class="tab-pane fade" id="upload-pane" role="tabpanel" aria-labelledby="upload-tab">
                            <form id="imageUploadForm" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="imageFile" class="form-label">Select Image</label>
                                    <input class="form-control" type="file" id="imageFile" accept="image/*">
                                </div>
                                <div class="mb-3">
                                    <label for="uploadAltText" class="form-label">Alt Text (for accessibility)</label>
                                    <input type="text" class="form-control" id="uploadAltText" placeholder="Description of the image">
                                </div>
                                <div class="image-preview-container" style="display: none;">
                                    <p>Preview:</p>
                                    <img id="imagePreview" src="#" alt="Preview" style="max-width: 100%; max-height: 200px;">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="insertImageBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="uploadSpinner"></span>
                        <span id="insertImageText">Insert Image</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- CSS Styles -->
<style>
/* Base styling */
body {
    background-color: #f8f9fa;
}

/* Info icon styling */
.fa-info-circle {
    color: #6c757d;
    cursor: pointer;
    transition: color 0.2s;
}

.fa-info-circle:hover {
    color: rgb(103, 78, 231);
}

/* Card styling */
.card {
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: none;
    margin-bottom: 20px;
}

.card-header {
    padding: 15px 20px;
    background-color: #fff;
    border-bottom: 1px solid #eee;
}

.card-body {
    padding: 20px;
}

/* Markdown editor styles */
.markdown-editor-wrapper {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background-color: #fff;
}

.markdown-tabs {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #f8f9fa;
    border-bottom: 1px solid #ddd;
    padding: 0;
}

.tab-buttons {
    display: flex;
}

.tab-btn {
    padding: 10px 20px;
    border: none;
    background: none;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    border-right: 1px solid #ddd;
}

.tab-btn.active {
    background-color: rgb(103, 78, 231);
    color: white;
}

.formatting-toolbar {
    display: flex;
    padding: 4px 8px;
}

.formatting-toolbar button {
    padding: 4px 8px;
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-right: 4px;
    cursor: pointer;
}

/* Form validation styling */
.is-invalid {
    border-color: #dc3545 !important;
}

.is-invalid + .invalid-feedback,
.is-invalid ~ .invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

/* For responsive design */
@media (max-width: 768px) {
    .formatting-toolbar {
        flex-wrap: wrap;
    }
    
    .tool-btn {
        padding: 6px;
        margin: 2px;
    }
}

/* Image upload modal styling */
#imageUploadModal .modal-content {
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

#imageUploadModal .nav-tabs .nav-link {
    padding: 0.5rem 1rem;
    border: none;
    color: #6c757d;
    font-weight: 500;
}

#imageUploadModal .nav-tabs .nav-link.active {
    color: rgb(103, 78, 231);
    border-bottom: 2px solid rgb(103, 78, 231);
    background-color: transparent;
}

.image-preview-container {
    margin-top: 1rem;
    padding: 1rem;
    background-color: #f8f9fa;
    border: 1px dashed #ddd;
    border-radius: 0.375rem;
    text-align: center;
}

#imagePreview {
    max-width: 100%;
    max-height: 200px;
    margin: 0 auto;
    display: block;
}

/* Add these styles to your existing CSS */
.spinner-border {
    margin-right: 8px;
}

#insertImageBtn:disabled {
    cursor: not-allowed;
    opacity: 0.7;
}
</style>

<!-- Required scripts -->
<script src="<?= base_url("/") ?>assets/js/font-awesome-all-min.js"></script>
<script src="<?= base_url("/") ?>assets/packages/marked.min.js"></script>
<script src="<?= base_url("/") ?>assets/packages/highlight.min.js"></script>
<script src="<?= base_url("/") ?>assets/packages/bootstrap.bundle.min.js"></script>
<script src="<?= base_url("/") ?>assets/packages/select2.min.js"></script>

<script>

// Execute when the document is ready
$(document).ready(function() {
    
    // Initialize marked.js for markdown parsing
    if (typeof marked !== 'undefined') {
        marked.setOptions({
            breaks: true,
            gfm: true
        });
    }
    
    // Switch between write and preview modes in the markdown editor
    $('.tab-btn').on('click', function() {
        const mode = $(this).data('mode');
        
        // Update active button
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        
        if (mode === 'write') {
            $('#markdown-editor').show();
            $('.preview-content').hide();
        } else {
            // Render markdown content
            const markdownContent = $('#markdown-editor').val();
            if (typeof marked !== 'undefined') {
                $('.preview-content').html(marked.parse(markdownContent));
            } else {
                // Fallback if marked.js is not available
                $('.preview-content').html('<p>' + markdownContent.replace(/\n/g, '<br>') + '</p>');
            }
            
            $('.preview-content').show();
            $('#markdown-editor').hide();
        }
    });
    
    // Handle markdown editor toolbar buttons
    $('.tool-btn').on('click', function() {
        const command = $(this).data('command');
        
        // If it's the image button, show modal instead of direct insertion
        if (command === 'image') {
            $('#imageUploadModal').modal('show');
            return;
        }
        
        const textarea = $('#markdown-editor')[0];
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        let selectedText = text.substring(start, end);
        let replacement = '';
        
        switch(command) {
            case 'heading':
                replacement = `## ${selectedText || 'Heading'}`;
                break;
            case 'bold':
                replacement = `**${selectedText || 'bold text'}**`;
                break;
            case 'italic':
                replacement = `*${selectedText || 'italic text'}*`;
                break;
            case 'table':
                replacement = `| Header | Header |\n| ------ | ------ |\n| Cell   | Cell   |\n| Cell   | Cell   |`;
                break;
            case 'list-ul':
                replacement = selectedText ? selectedText.split('\n').map(line => `- ${line}`).join('\n') : '- List item\n- List item\n- List item';
                break;
            case 'list-ol':
                replacement = selectedText ? selectedText.split('\n').map((line, i) => `${i+1}. ${line}`).join('\n') : '1. List item\n2. List item\n3. List item';
                break;
            case 'quote':
                replacement = selectedText ? selectedText.split('\n').map(line => `> ${line}`).join('\n') : '> Blockquote';
                break;
            case 'code':
                replacement = selectedText ? '```\n' + selectedText + '\n```' : '```\ncode block\n```';
                break;
            case 'link':
                replacement = `[${selectedText || 'link text'}](https://example.com)`;
                break;
        }
        
        // Insert the replacement text
        textarea.value = text.substring(0, start) + replacement + text.substring(end);
        
        // Update the cursor position
        const newPosition = start + replacement.length;
        textarea.selectionStart = newPosition;
        textarea.selectionEnd = newPosition;
        
        // Update hidden input with markdown content
        $('#instructions').val(textarea.value);
        
        // Focus back on the textarea
        textarea.focus();
    });
    
    // Function to insert content into the markdown editor
    function insertIntoEditor(content) {
        const textarea = $('#markdown-editor')[0];
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        
        // Insert the content
        textarea.value = text.substring(0, start) + content + text.substring(end);
        
        // Update the cursor position
        const newPosition = start + content.length;
        textarea.selectionStart = newPosition;
        textarea.selectionEnd = newPosition;
        
        // Update hidden input with markdown content
        $('#instructions').val(textarea.value);
        
        // Focus back on the textarea
        textarea.focus();
    }
    
    // Update hidden input with markdown content on change
    $('#markdown-editor').on('input', function() {
        $('#instructions').val($(this).val());
    });
    
    // Handle image preview when file selected
    $('#imageFile').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
                $('.image-preview-container').show();
            };
            
            reader.readAsDataURL(file);
        } else {
            $('.image-preview-container').hide();
        }
    });
    
    // Function to upload image to the server
    function uploadImage(file, altText) {
        // Show loading state
        const $spinner = $('#uploadSpinner');
        const $button = $('#insertImageBtn');
        const $buttonText = $('#insertImageText');
        
        $spinner.removeClass('d-none');
        $button.prop('disabled', true);
        $buttonText.text('Uploading...');
        
        const formData = new FormData();
        formData.append('image', file);
        
        $.ajax({
            url: "<?= base_url($url.'/test') ?>" + '/upload_image',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const parsedResponse = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (parsedResponse.success) {
                        const imageCode = `![${altText}](${parsedResponse.file_url})`;
                        insertIntoEditor(imageCode);
                        $('#imageUploadModal').modal('hide');
                        $("#imageFile").val('');
                        $('#imagePreview').attr('src', '');
                        $('.image-preview-container').hide();
                    } else {
                        alert(parsedResponse.message || 'Error uploading image');
                    }
                } catch (e) {
                    console.error("Error processing upload response:", e);
                    alert('Error processing upload response');
                }
            },
            error: function(xhr) {
                console.error("Upload error:", xhr.responseText);
                alert('Error uploading image. Please try again.');
            },
            complete: function() {
                // Reset button state
                $spinner.addClass('d-none');
                $button.prop('disabled', false);
                $buttonText.text('Insert Image');
            }
        });
    }
    
    // Handle insert image button click
    $('#insertImageBtn').on('click', function() {
        const activeTab = $('#imageTabs .nav-link.active').attr('id');
        let imageCode = '';
        
        if (activeTab === 'url-tab') {
            const url = $('#imageUrl').val().trim();
            const alt = $('#imageAltText').val().trim() || 'Image';
            
            if (url) {
                imageCode = `![${alt}](${url})`;
                insertIntoEditor(imageCode);
                $('#imageUploadModal').modal('hide');
            } else {
                alert('Please enter an image URL');
            }
        } else {
            const file = $('#imageFile')[0].files[0];
            const alt = $('#uploadAltText').val().trim() || 'Image';
            
            if (file) {
                // Upload the file to the server
                uploadImage(file, alt);
            } else {
                alert('Please select an image file');
            }
        }
    });

    $('#duration_hours, #duration_minutes').on('change keyup', function() {
        let hours = parseInt($('#duration_hours').val()) || 0;
        let minutes = parseInt($('#duration_minutes').val()) || 0;
        // Clamp ranges
        if (hours < 0) hours = 0; if (hours > 24) hours = 24;
        if (minutes < 0) minutes = 0; if (minutes > 59) minutes = 59;
        // Prevent accidental 0 total duration
        if ((hours * 60) + minutes === 0) {
            minutes = 30; // default to 30 minutes
            $('#duration_minutes').val(30);
        }
        $('#duration').val((hours * 60) + minutes);
    });

    $('#negative_mark_value').on('keyup', function () {
        if($('#negative_mark_value').val()>0){
            $('#negative_mark_value').val($('#negative_mark_value').val()*-1)
        }
        var neg_value = $('#negative_mark_value').val();
        if(!neg_value){
            $('#negative_mark_value').val(0)
        }
    });
    
    $('#maxattempts').on('keyup', function () {
        var max_value = $('#maxattempts').val();
        if (max_value < 0) {
            $('#maxattempts').val(max_value * -1); // convert negative to positive
        }

        if (!max_value || max_value == 0) {
            $('#maxattempts').val(1); 
        }
    });
    
    // Validation for the test form
    $('#testForm').on('submit', function(e) {
        let isValid = true;
        
        // Reset validation states
        $('.is-invalid').removeClass('is-invalid');
        
        // Validate required fields
        if ($('#title').val().trim() === '') {
            $('#title').addClass('is-invalid');
            isValid = false;
        }
        
        if ($('#instructions').val().trim() === '') {
            $('#markdown-editor').addClass('is-invalid');
            isValid = false;
        }
        
        if ($('#duration').val() === '' || parseInt($('#duration').val()) <= 0) {
            $('#duration').addClass('is-invalid');
            isValid = false;
        }
        
        // Validate date ranges if both are provided
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            
            if (start >= end) {
                $('#start_date').addClass('is-invalid');
                $('#end_date').addClass('is-invalid');
                isValid = false;
                
                // Show error message
                if (!$('#date-error-message').length) {
                    $('<div id="date-error-message" class="text-danger mt-2">End date must be after start date</div>')
                        .insertAfter($('#end_date'));
                }
            } else {
                $('#date-error-message').remove();
            }
        }
        
        // Handle the "close after tab change" checkbox and related input
        if ($('#close_after_tab_change').is(':checked')) {
            const tabCount = $('#close_after_tab_count').val();
            if (tabCount === '' || parseInt(tabCount) < 1) {
                $('#close_after_tab_count').addClass('is-invalid');
                isValid = false;
            }
        }


        // Prevent form submission if validation fails
        if (!isValid) {
            e.preventDefault();
            
            // Scroll to the first invalid element
            const firstInvalidElement = $('.is-invalid:first');
            if (firstInvalidElement.length) {
                $('html, body').animate({
                    scrollTop: firstInvalidElement.offset().top - 100
                }, 500);
            }
            
            // Show validation message at the top
            if (!$('#validation-error-alert').length) {
                const alertHtml = `
                    <div id="validation-error-alert" class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        <span class="alert-text">Please fix the errors in the form before submitting.</span>
                    </div>
                `;
                $(alertHtml).prependTo($('.card-body'));
            }
        }
    });
    
    // Enable/disable tab count input based on checkbox
    $('#close_after_tab_change').on('change', function() {
        $('#close_after_tab_count').prop('disabled', !$(this).is(':checked'));
        
        if (!$(this).is(':checked')) {
            $('#close_after_tab_count').val('3'); // Reset to default
        }
    });
    
    // Initialize disabled state for tab count input
    if (!$('#close_after_tab_change').is(':checked')) {
        $('#close_after_tab_count').prop('disabled', true);
    }

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert:not(#validation-error-alert)').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
    
    // Define BASE_URL if not already defined
    if (typeof BASE_URL === 'undefined') {
        // Try to get base URL from a meta tag or construct from window location
        const metaBaseUrl = $('meta[name="base-url"]').attr('content');
        window.BASE_URL = metaBaseUrl || window.location.origin + '/';
        
        // Make sure it ends with a slash
        if (!window.BASE_URL.endsWith('/')) {
            window.BASE_URL += '/';
        }
    }
    
    // Initialize tooltips if Bootstrap 5 is loaded
    if (typeof bootstrap !== 'undefined' && typeof bootstrap.Tooltip !== 'undefined') {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});

</script>


