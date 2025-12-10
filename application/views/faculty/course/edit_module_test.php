<?php
$courseModeClass = ($course['course_mode'] == 2) ? 'gamification-mode' : 'normal-mode';
?>
<!-- View file: application/views/course/edit_module_test.php -->
<div class="layout-content <?= $courseModeClass ?>">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Edit Test Schedule</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/course') ?>">Courses</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/course/modules/'.$course['id']) ?>">Modules</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/course/module_tests/'.$course['id'].'/'.$module['id']) ?>">Module Tests</a></li>
                <li class="breadcrumb-item active">Edit Test Schedule</li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>
        
        <div class="card p-2 mb-4">
            <div style="display: flex; justify-content:space-between; align-items: center;
            border-bottom: 0 solid rgba(24, 28, 33, 0.13);
            border-color: rgba(24, 28, 33, 0.13);
            border-radius: 0.125rem 0.125rem 0 0; 
            border-bottom-width: 1px;">
                <h6 class="card-header" style="border:none">Test Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Course:</strong> <?= $course['name'] ?> (<?= $course['course_code'] ?>)</p>
                        <p><strong>Module:</strong> <?= $module['name'] ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Test:</strong> <?= $course_test['test_name'] ?></p>
                        <p><strong>Description:</strong> <?= $course_test['description'] ?? 'No description' ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Edit Test Schedule Form -->
        <div class="card p-2">
            <div style="display: flex; justify-content:space-between; align-items: center;
            border-bottom: 0 solid rgba(24, 28, 33, 0.13);
            border-color: rgba(24, 28, 33, 0.13);
            border-radius: 0.125rem 0.125rem 0 0; 
            border-bottom-width: 1px;">
                <h6 class="card-header" style="border:none">Edit Test Schedule</h6>
            </div>
            <div class="card-body">
                <form action="<?= base_url($url.'/course/edit_module_test/'.$course['id'].'/'.$module['id'].'/'.$course_test['id']) ?>" method="post">
                    <?php if ($course['course_mode'] == 2): ?>
                    <!-- Gamification Mode Inputs -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="level">Level <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="level" id="level" min="1"  required
                                value="<?= $course_test['level'] ?? '' ?>">
                            </div>
                        </div>
                        <!-- <div class="col-md-6">
                            <div class="form-group">
                                <label for="pass_score">Pass Score<span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="pass_score" id="pass_score" min="0" max="100" required
                                value="<?= $course_test['pass_score'] ?? '' ?>">
                            </div>
                        </div> -->
                    </div>
                    <?php else: ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">Start Date (Optional)</label>
                                <input type="datetime-local" class="form-control" id="start_date" name="start_date" 
                                       value="<?= !empty($course_test['start_date']) ? date('Y-m-d\TH:i', strtotime($course_test['start_date'])) : '' ?>">
                                <small class="text-muted">Leave empty for no restriction</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">End Date (Optional)</label>
                                <input type="datetime-local" class="form-control" id="end_date" name="end_date"
                                       value="<?= !empty($course_test['end_date']) ? date('Y-m-d\TH:i', strtotime($course_test['end_date'])) : '' ?>">
                                <small class="text-muted">Leave empty for no restriction</small>
                            </div>
                        </div>
                    </div>

                    <!-- Explanation Section -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="d-flex align-items-center">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input" id="show_explanation" name="show_explanation" 
                                            <?= isset($course_test['show_explanation']) && $course_test['show_explanation'] ? 'checked' : '' ?>>
                                        <label class="custom-control-label" for="show_explanation" style="position: relative; padding-left: 1.5rem; margin-bottom: 0;">Show/Hide Test Result</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="explanation-dates" style="display: <?= isset($course_test['show_explanation']) && $course_test['show_explanation'] ? 'block' : 'none' ?>;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="explanation_start">Test Result Availability Start Date & Time</label>
                                    <input type="datetime-local" class="form-control" id="explanation_start" name="explanation_start_date"
                                           value="<?= !empty($course_test['explanation_start_date']) ? date('Y-m-d\TH:i', strtotime($course_test['explanation_start_date'])) : '' ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="explanation_end">Test Result Availability End Date & Time</label>
                                    <input type="datetime-local" class="form-control" id="explanation_end" name="explanation_end_date"
                                           value="<?= !empty($course_test['explanation_end_date']) ? date('Y-m-d\TH:i', strtotime($course_test['explanation_end_date'])) : '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Update Schedule</button>
                            <a href="<?= base_url($url.'/course/module_tests/'.$course['id'].'/'.$module['id']) ?>" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.custom-control-input:checked ~ .custom-control-label::before {
    background-color: #674CEF;
    border-color: #674CEF;
}
</style>

<script>
$(document).ready(function() {
    // Handle explanation toggle
    $('#show_explanation').change(function() {
        if($(this).is(':checked')) {
            $('.explanation-dates').slideDown();
            $('#explanation_start, #explanation_end').prop('required', true);
        } else {
            $('.explanation-dates').slideUp();
            $('#explanation_start, #explanation_end').prop('required', false);
        }
    });

    // Validate explanation dates
    $('#explanation_end').change(function() {
        let startDate = new Date($('#explanation_start').val());
        let endDate = new Date($(this).val());
        
        if(endDate < startDate) {
            alert('Result visibility end date should be greater than start date');
            $(this).val('');
        }
    });
    
    // Validate that explanation start date is after test end date
    $('#explanation_start').change(function() {
        let testEndDate = new Date($('#end_date').val());
        let explanationStartDate = new Date($(this).val());
        
        if($('#end_date').val() && $(this).val()) {
            if(explanationStartDate <= testEndDate) {
                alert('Result visibility start date must be after the test end date');
                $(this).val('');
            }
        }
    });
    
    // Also validate when test end date changes
    $('#end_date').change(function() {
        let testEndDate = new Date($(this).val());
        let explanationStartDate = new Date($('#explanation_start').val());
        
        if($(this).val() && $('#explanation_start').val()) {
            if(explanationStartDate <= testEndDate) {
                alert('Result visibility start date must be after the test end date. Please update the result visibility start date.');
                $('#explanation_start').val('');
            }
        }
    });
});
</script>

<style>
    .gamification-mode {
    background: linear-gradient(to bottom, #E9F9F0 0%, #D3D3D3 50%, #B3E6CB 100%);
}

.gamification-mode .card {
    border-left: 6px solid #62D493;
    box-shadow: 0 0 8px rgba(240, 173, 78, 0.2);
}

.badge-warning {
    background-color: #62D493;
    color: #fff;
    font-size: 0.8rem;
    font-weight: bold;
    /* border-radius: 12px; */
    padding: 4px 10px;
}
</style>