<!-- reports/index.php -->
<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Test Reports</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($url) ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item active">Reports</li>
            </ol>
        </div>
        
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-header-title mb-0">Filter Reports</h5>
            </div>
            <div class="card-body">
                <form method="get" action="<?= base_url($url . '/report') ?>" id="reportFilterForm">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="student_id">Student</label>
                            <select class="form-control select2" id="student_id" name="student_id">
                                <option value="">All Students</option>
                                <?php foreach ($students as $student): ?>
                                <option value="<?= $student['id'] ?>" <?= ($this->input->get('student_id') == $student['id']) ? 'selected' : '' ?>>
                                    <?= $student['name'] ?> (<?= $student['email'] ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4 form-group">
                            <label for="test_id">Test</label>
                            <select class="form-control select2" id="test_id" name="test_id">
                                <option value="">All Tests</option>
                                <?php foreach ($tests as $test): ?>
                                <option value="<?= $test['id'] ?>" <?= ($this->input->get('test_id') == $test['id']) ? 'selected' : '' ?>>
                                    <?= $test['title'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4 form-group">
                            <label for="course_id">Course</label>
                            <select class="form-control select2" id="course_id" name="course_id">
                                <option value="">All Courses</option>
                                <?php foreach ($courses as $course): ?>
                                <option value="<?= $course['id'] ?>" <?= ($this->input->get('course_id') == $course['id']) ? 'selected' : '' ?>>
                                    <?= $course['name'] ?> (<?= $course['course_code'] ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="<?= base_url($url . '/report') ?>" class="btn btn-outline-secondary">Reset</a>
                        <?php if (!empty($submissions)): ?>
                        <a href="<?= base_url($url . '/report/export_csv/all') ?>" class="btn btn-success">
                            <i class="feather icon-download"></i> Export to CSV
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Results Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-header-title mb-0">Test Results</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover card-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Test</th>
                                <!-- <th>Module</th> -->
                                <th>Submission Date</th>
                                <th>Score</th>
                                <th>Result</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($submissions)): ?>
                                <?php $i = 1; foreach ($submissions as $submission): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td>
                                            <a href="<?= base_url($url . '/report/student_detail/' . $submission['student_id']) ?>">
                                                <?= $submission['student_name'] ?>
                                            </a>
                                        </td>
                                        <td>
                                            <a href="<?= base_url($url . '/report/test_detail/' . $submission['test_id']) ?>">
                                                <?= $submission['test_title'] ?>
                                            </a>
                                        </td>
                                        <!-- <td><?= isset($module_map[$submission['module_id']]) ? $module_map[$submission['module_id']] : 'N/A' ?></td> -->
                                        <td><?= date('M d, Y h:i A', strtotime($submission['submission_time'])) ?></td>
                                        <td>
                                            <div class="progress" style="height: 6px; width: 100px;">
                                                <div class="progress-bar <?= ($submission['percentage'] >= $submission['pass_percentage']) ? 'bg-success' : 'bg-danger' ?>" 
                                                     role="progressbar" 
                                                     style="width: <?= $submission['percentage'] ?>%" 
                                                     aria-valuenow="<?= $submission['percentage'] ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                            <span class="small"><?= number_format($submission['percentage'], 1) ?>% (<?= $submission['earned_score'] ?>/<?= $submission['total_score'] ?>)</span>
                                        </td>
                                        <td>
                                            <?php if ($submission['percentage'] >= $submission['pass_percentage']): ?>
                                                <span class="badge badge-pill badge-success">Pass</span>
                                            <?php else: ?>
                                                <span class="badge badge-pill badge-danger">Fail</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-primary dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                    <i class="feather icon-more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="<?= base_url($url . '/report/student_detail/' . $submission['student_id']) ?>">
                                                        <i class="feather icon-user mr-2"></i> Student Details
                                                    </a>
                                                    <a class="dropdown-item" href="<?= base_url($url . '/report/test_detail/' . $submission['test_id']) ?>">
                                                        <i class="feather icon-file-text mr-2"></i> Test Details
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="#" onclick="printReport(<?= $submission['id'] ?>)">
                                                        <i class="feather icon-printer mr-2"></i> Print Report
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">No submissions found matching the current filters.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Total Submissions Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= $total_submissions ?></h4>
                                <p class="text-muted mb-0">Total Submissions</p>
                            </div>
                            <div class="bg-primary rounded p-3">
                                <i class="feather icon-file-text text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Passing Students Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= $pass_count ?></h4>
                                <p class="text-muted mb-0">Passing Submissions</p>
                            </div>
                            <div class="bg-success rounded p-3">
                                <i class="feather icon-check-circle text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pass Rate Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= number_format($pass_rate, 1) ?>%</h4>
                                <p class="text-muted mb-0">Pass Rate</p>
                            </div>
                            <div class="bg-info rounded p-3">
                                <i class="feather icon-percent text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Average Score Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= number_format($avg_score, 1) ?>%</h4>
                                <p class="text-muted mb-0">Average Score</p>
                            </div>
                            <div class="bg-warning rounded p-3">
                                <i class="feather icon-award text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>

      <!-- Overview Cards -->
    
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize select2 for better dropdown experience
        $('.select2').select2();
        
        // Auto-submit form when filters change
        $('#student_id, #test_id, #course_id').on('change', function() {
            $('#reportFilterForm').submit();
        });
    });
    
    function printReport(id) {
        // This would typically open a print-friendly version of the report
        window.open('<?= base_url($url . '/report/print/') ?>' + id, '_blank');
    }
</script>
        
      