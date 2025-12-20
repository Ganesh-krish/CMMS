<!-- reports/student_detail.php -->
<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Student Performance Report</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($url) ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url . '/report') ?>">Reports</a></li>
                <li class="breadcrumb-item active">Student Detail</li>
            </ol>
        </div>
        
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Student Profile Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-4 text-center mb-3 mb-md-0">
                        <div class="avatar avatar-xl mb-3">
                            <?php if (!empty($student['file_path'])): ?>
                                <img src="<?= base_url('assets/uploads/students/' . $student['file_path']) ?>" alt="Photo" class="img-fluid rounded-circle">
                            <?php else: ?>
                                <!-- <div class="avatar-initial rounded-circle bg-primary text-white">
                                    <?= strtoupper(substr($student['name'], 0, 1)) ?>
                                </div> -->
                            <?php endif; ?>
                        </div>
                        <h5 class="mb-1"><?= $student['name'] ?></h5>
                        <div class="text-muted small"><?= $student['email'] ?></div>
                        
                        <div class="mt-3">
                            <!-- <a href="<?= base_url($url . '/report/export_csv/student/' . $student['id']) ?>" class="btn btn-sm btn-outline-primary">
                                <i class="feather icon-download"></i> Export Report
                            </a> -->
                        </div>
                    </div>
                    
                    <div class="col-md-9 col-sm-8">
                        <div class="row">
                            <div class="col-sm-6 mb-3">
                                <div class="card shadow-none bg-light">
                                    <div class="card-body py-3 px-4">
                                        <div class="d-flex align-items-center">
                                            <i class="feather icon-edit-2 text-primary mr-3" style="font-size: 24px;"></i>
                                            <div>
                                                <div class="text-muted small">Total Tests</div>
                                                <h5 class="mb-0"><?= $total_submissions ?></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6 mb-3">
                                <div class="card shadow-none bg-light">
                                    <div class="card-body py-3 px-4">
                                        <div class="d-flex align-items-center">
                                            <i class="feather icon-check-circle text-success mr-3" style="font-size: 24px;"></i>
                                            <div>
                                                <div class="text-muted small">Completed Tests</div>
                                                <h5 class="mb-0"><?= $completed_tests ?></h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6 mb-3">
                                <div class="card shadow-none bg-light">
                                    <div class="card-body py-3 px-4">
                                        <div class="d-flex align-items-center">
                                            <i class="feather icon-award text-warning mr-3" style="font-size: 24px;"></i>
                                            <div>
                                                <div class="text-muted small">Average Score</div>
                                                <h5 class="mb-0"><?= number_format($avg_score, 1) ?>%</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-sm-6 mb-3">
                                <div class="card shadow-none bg-light">
                                    <div class="card-body py-3 px-4">
                                        <div class="d-flex align-items-center">
                                            <i class="feather icon-percent text-info mr-3" style="font-size: 24px;"></i>
                                            <div>
                                                <div class="text-muted small">Pass Rate</div>
                                                <h5 class="mb-0"><?= number_format($pass_rate, 1) ?>%</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card shadow-none bg-light">
                            <div class="card-body py-3">
                                <h6 class="mb-3">Student Information</h6>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="mb-2">
                                            <span class="text-muted small">Phone Number:</span>
                                            <div><?= $student['phone'] ?? '-' ?></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-2">
                                            <span class="text-muted small">Joining Date:</span>
                                            <div><?= date('M d, Y', strtotime($student['joining_date'])) ?></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-2">
                                            <span class="text-muted small">Department:</span>
                                            <div><?= $student['department'] ?? 'N/A' ?></div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="mb-2">
                                            <span class="text-muted small">Expiry Date:</span>
                                            <div><?= date('M d, Y', strtotime($student['expire_date'])) ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Performance Tab Navigation -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#testResults">Test Results</a>
            </li>
            <!-- <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#courseEnrollment">Course Enrollment</a>
            </li> -->
        </ul>
        
        <div class="tab-content">
            <!-- Test Results Tab -->
            <div class="tab-pane fade show active" id="testResults">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-header-title mb-0">Test Submissions</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="datatable table table-hover card-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Test Title</th>
                                        <th>Module</th>
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
                                                    <a href="<?= base_url($url . '/report/test_detail/' . $submission['test_id']) ?>">
                                                        <?= $submission['test_title'] ?>
                                                    </a>
                                                </td>
                                                <td><?= isset($module_map[$submission['module_id']]) ? $module_map[$submission['module_id']] : 'N/A' ?></td>
                                                <td><?= date('M d, Y h:i A', strtotime($submission['submission_time'])) ?></td>
                                                <td>
                                                    <div class="progress" style="height: 6px; width: 100px;">
                                                        <div class="progress-bar <?= ($submission['percentage'] >= 60) ? 'bg-success' : 'bg-danger' ?>" 
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
                                                    <?php if ($submission['percentage'] >= 60): ?>
                                                        <span class="badge badge-pill badge-success">Pass</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-pill badge-danger">Fail</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url($url . '/report/test_detail/' . $submission['test_id']) ?>" class="btn btn-sm btn-icon btn-outline-primary">
                                                        <i class="feather icon-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No test submissions found for this student.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Course Enrollment Tab -->
            <!-- <div class="tab-pane fade" id="courseEnrollment">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-header-title mb-0">Enrolled Courses</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="datatable table table-hover card-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Course Name</th>
                                        <th>Course Code</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($courses)): ?>
                                        <?php $i = 1; foreach ($courses as $course): ?>
                                            <tr>
                                                <td><?= $i++ ?></td>
                                                <td>
                                                    <a href="<?= base_url($url . '/report/course_detail/' . $course['id']) ?>">
                                                        <?= $course['name'] ?>
                                                    </a>
                                                </td>
                                                <td><?= $course['course_code'] ?></td>
                                                <td><?= date('M d, Y', strtotime($course['start_date'])) ?></td>
                                                <td><?= date('M d, Y', strtotime($course['end_date'])) ?></td>
                                                <td>
                                                    <a href="<?= base_url($url . '/report/course_detail/' . $course['id']) ?>" class="btn btn-sm btn-icon btn-outline-primary">
                                                        <i class="feather icon-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No courses found for this student.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // If URL has hash, activate the corresponding tab
        var hash = window.location.hash;
        if (hash) {
            $('.nav-tabs a[href="' + hash + '"]').tab('show');
        }
        
        // Change hash when tab is clicked
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash;
        });
    });
</script>