<?php
$courseModeClass = ($course['course_mode'] == 2) ? 'gamification-mode' : 'normal-mode';
?>
<!-- View file: application/views/course/module_tests.php -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="layout-content <?= $courseModeClass ?>">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Module Tests</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/allcourses') ?>">All Courses</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/allcourses/modules/'.$course['id']) ?>">Modules</a></li>
                <li class="breadcrumb-item active">Module Tests</li>
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
                <h6 class="card-header" style="border:none">Course & Module Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Course Code:</strong> <?= $course['course_code'] ?></p>
                        <p><strong>Course Name:</strong> <?= $course['name'] ?></p>
                        <a href="<?= base_url($url.'/course/export_module_report/'.$course['id'].'/'.$module['id']) ?>" class="btn btn-success">
                            <i class="fas fa-file-csv"></i> Export Module Report
                        </a>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Module Name:</strong> <?= $module['name'] ?></p>
                        <!-- <p><strong>Description:</strong> <?= $module['description'] ?></p> -->
                         <?php if ($course['course_mode'] == 2): ?>
                            <p><strong>Module Mode :</strong>
                            <span class="badge badge-warning ml-2">Gamification</span>
                         <?php endif; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Available Tests Table -->
        
        <!-- Module Tests List -->
        <div class="card p-2">
            <div style="display: flex; justify-content:space-between; align-items: center;
            border-bottom: 0 solid rgba(24, 28, 33, 0.13);
            border-color: rgba(24, 28, 33, 0.13);
            border-radius: 0.125rem 0.125rem 0 0; 
            border-bottom-width: 1px;">
                <h6 class="card-header" style="border:none">Tests in Module</h6>
            </div>
            <div class="card-datatable container table-responsive">
                <?php if(empty($module_tests)): ?>
                    <div class="alert alert-info">
                        No tests added to this module yet.
                    </div>
                <?php else: ?>
                    <table class="datatable datatables-demo table table-striped table-bordered" id="moduleTestsTable">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Test Name</th>
                                <!-- <th>Description</th> -->
                                <th>Duration</th>
                                <!-- <th>Submitted Students</th>
                                <th>Total Students</th> -->
                            <?php if ($course['course_mode'] == 2): ?>
                                <th>Level</th>
                                <th>Pass Score</th>
                            <?php else: ?>
                                <th>Start Date</th>
                                <th>End Date</th>
                            <?php endif; ?>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 0; foreach($module_tests as $test): $no++; ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= $test['title'] ?></td>
                                    <!-- <td><?= $test['description'] ?? '-' ?></td> -->
                                    <td><?= isset($test['duration']) ? $test['duration'].' minutes' : '-' ?></td>
                                    <!-- <td align="center"><?= isset($test['submitted_students']) ? $test['submitted_students'] : 0 ?></td>
                                    <td align="center"><?= isset($test['total_students']) ? $test['total_students'] : 0 ?></td> -->
                                <?php if($course['course_mode'] == 2):?>
                                    <td><?= $test['level'] ?? '-' ?></td>
                                    <td><?= $test['pass_percentage'] ?? '-' ?></td>
                                <?php else:?>
                                    <td>
                                        <?= (!empty($test['start_date'])) ? $this->common->display_date($test['start_date']) : 'Not set' ?>
                                    </td>
                                    <td>
                                        <?= (!empty($test['end_date'])) ? $this->common->display_date($test['end_date']) : 'Not set' ?>
                                    </td>
                                <?php endif; ?>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?= base_url($url.'/allcourses/test_questions/'.$test['id']) ?>" 
                                               class="btn btn-info btn-sm">
                                                <i class="feather icon-list"></i> Questions
                                            </a>

                                            <a href="<?= base_url($url.'/allcourses/test_results/'.$course['id'].'/'.$module['id'].'/'.$test['test_id']) ?>" 
                                               class="btn btn-success btn-sm">
                                                <i class="feather icon-bar-chart-2"></i> Results
                                            </a>

                                            <!-- <a href="<?= base_url($url.'/course/edit_module_test/'.$course['id'].'/'.$module['id'].'/'.$test['id']) ?>" 
                                               class="btn btn-warning btn-sm">
                                                <i class="feather icon-edit"></i> Edit
                                            </a>

                                            <a href="<?= base_url($url.'/course/remove_test_from_module/'.$course['id'].'/'.$module['id'].'/'.$test['id']) ?>" 
                                               class="btn btn-danger btn-sm" 
                                               onclick="return confirm('Are you sure you want to remove this test from the module?');">
                                                <i class="feather icon-trash"></i> Remove
                                            </a> -->
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add Test Modal -->
<div class="modal fade" id="addTestModal" tabindex="-1" role="dialog" aria-labelledby="addTestModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTestModalLabel">Add Test to Module</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url($url.'/course/add_tests_to_module/') ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                    <input type="hidden" name="module_id" value="<?= $module['id'] ?>">
                    <input type="hidden" name="test_ids[]" id="modalTestId">
                    
                    <div class="form-group">
                        <label for="testName">Test Name</label>
                        <input type="text" class="form-control" id="testName" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="start_date">Start Date (Optional)</label>
                        <input type="datetime-local" class="form-control" id="start_date" name="start_date">
                        <small class="text-muted">Leave empty for no restriction</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="end_date">End Date (Optional)</label>
                        <input type="datetime-local" class="form-control" id="end_date" name="end_date">
                        <small class="text-muted">Leave empty for no restriction</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Test</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Multiple Tests Modal -->
<div class="modal fade" id="multipleTestsModal" tabindex="-1" role="dialog" aria-labelledby="multipleTestsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="multipleTestsModalLabel">Add Selected Tests to Module</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="multipleTestsForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label id="selectedTestsCount"></label>
                    </div>
                    
                    <div class="form-group">
                        <label for="multiple_start_date">Start Date (Optional)</label>
                        <input type="datetime-local" class="form-control" id="multiple_start_date" name="start_date">
                        <small class="text-muted">Leave empty for no restriction</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="multiple_end_date">End Date (Optional)</label>
                        <input type="datetime-local" class="form-control" id="multiple_end_date" name="end_date">
                        <small class="text-muted">Leave empty for no restriction</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" id="submitMultipleTests" class="btn btn-primary">Add Tests</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="<?= base_url('') ?>assets/faculty/libs/datatables/datatables.js"></script>
<script src="<?= base_url('') ?>assets/faculty/js/pages/forms_selects.js"></script>
<script src="<?= base_url('') ?>assets/faculty/libs/bootstrap-select/bootstrap-select.js"></script>
<script src="<?= base_url('') ?>assets/faculty/libs/select2/select2.js"></script>


<script>
$(document).ready(function() {
    // Destroy existing DataTable instances if they exist before initializing
    if ($.fn.DataTable.isDataTable('#availableTestsTable')) {
        $('#availableTestsTable').DataTable().destroy();
    }
    
    if ($.fn.DataTable.isDataTable('#moduleTestsTable')) {
        $('#moduleTestsTable').DataTable().destroy();
    }
    
    // Initialize DataTables
    $('#availableTestsTable').DataTable({
        responsive: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pageLength: 10
    });
    
    $('#moduleTestsTable').DataTable({
        responsive: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pageLength: 10
    });
    
    // Rest of your code remains the same
    // Select All checkboxes
    $('#selectAll').on('click', function() {
        $('.test-checkbox').prop('checked', this.checked);
    });
    
    // Add single test button
    $('.add-test-btn').on('click', function() {
        var testId = $(this).data('test-id');
        var testName = $(this).data('test-name');
        
        $('#modalTestId').val(testId);
        $('#testName').val(testName);
        $('#addTestModal').modal('show');
    });
    
    // Add selected tests button
    $('#addSelectedTests').on('click', function() {
        var selectedTests = $('.test-checkbox:checked');
        
        if (selectedTests.length === 0) {
            alert('Please select at least one test to add.');
            return;
        }
        
        $('#selectedTestsCount').text(selectedTests.length + ' tests selected');
        $('#multipleTestsModal').modal('show');
    });
    
    // Submit multiple tests
    $('#submitMultipleTests').on('click', function() {
        var startDate = $('#multiple_start_date').val();
        var endDate = $('#multiple_end_date').val();
        
        // Set the dates in the main form
        $('#addTestForm').find('input[name="start_date"]').val(startDate);
        $('#addTestForm').find('input[name="end_date"]').val(endDate);
        
        // Submit the main form
        $('#addTestForm').submit();
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