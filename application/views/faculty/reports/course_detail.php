<!-- reports/course_detail.php -->
<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Course Performance Report</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($url) ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url . '/report') ?>">Reports</a></li>
                <li class="breadcrumb-item active">Course Detail</li>
            </ol>
        </div>
        
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Course Overview Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success text-white rounded p-3 mr-4">
                                <i class="feather icon-book" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-1"><?= $course['name'] ?></h5>
                                <div class="text-muted small">
                                    Course Code: <?= $course['course_code'] ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4 col-sm-6 mb-3">
                                <div class="text-muted small">Start Date:</div>
                                <div><?= date('M d, Y', strtotime($course['start_date'])) ?></div>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-3">
                                <div class="text-muted small">End Date:</div>
                                <div><?= date('M d, Y', strtotime($course['end_date'])) ?></div>
                            </div>
                            <div class="col-md-4 col-sm-6 mb-3">
                                <div class="text-muted small">Department:</div>
                                <div><?= $course['department'] ?? 'N/A' ?></div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="text-muted small mb-2">Course Description:</div>
                            <div class="p-3 bg-light rounded">
                                <?= $course['description'] ?? 'No description available.' ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="text-center">
                            <a href="<?= base_url($url . '/report/export_csv/course/' . $course['id']) ?>" class="btn btn-primary mb-3">
                                <i class="feather icon-download mr-2"></i> Export Report
                            </a>
                            
                            <div class="row">
                                <div class="col-6">
                                    <div class="card shadow-none bg-light mb-3">
                                        <div class="card-body py-3 px-2">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="feather icon-users text-primary mb-2" style="font-size: 24px;"></i>
                                                <h3 class="mb-0"><?= $total_students ?></h3>
                                                <div class="text-muted small">Students</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-6">
                                    <div class="card shadow-none bg-light mb-3">
                                        <div class="card-body py-3 px-2">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="feather icon-file-text text-success mb-2" style="font-size: 24px;"></i>
                                                <h3 class="mb-0"><?= $total_tests ?></h3>
                                                <div class="text-muted small">Tests</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card shadow-none bg-light">
                                <div class="card-body py-3">
                                    <div class="font-weight-bold mb-2">Course Expiry</div>
                                    <h5 class="text-<?= strtotime($course['course_expiry']) > time() ? 'primary' : 'danger' ?>">
                                        <?= date('M d, Y', strtotime($course['course_expiry'])) ?>
                                    </h5>
                                    <div class="text-muted small">
                                        <?php 
                                        $days_left = ceil((strtotime($course['course_expiry']) - time()) / (60 * 60 * 24));
                                        if ($days_left > 0) {
                                            echo $days_left . ' days remaining';
                                        } else {
                                            echo 'Expired ' . abs($days_left) . ' days ago';
                                        }
                                        ?>
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
                <a class="nav-link active" data-toggle="tab" href="#enrolledStudents">Enrolled Students</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#courseTests">Course Tests</a>
            </li>
        </ul>
        
        <div class="tab-content">
            <!-- Enrolled Students Tab -->
            <div class="tab-pane fade show active" id="enrolledStudents">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-header-title mb-0">Enrolled Students</h5>
                        <button class="btn btn-sm btn-outline-primary" onclick="window.location.href='<?= base_url($url . '/staff/students') ?>'">
                            <i class="feather icon-user-plus mr-1"></i> Manage Students
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover card-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student Name</th>
                                        <th>Email</th>
                                        <th>Joining Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($students)): ?>
                                        <?php $i = 1; foreach ($students as $student): ?>
                                            <tr>
                                                <td><?= $i++ ?></td>
                                                <td>
                                                    <a href="<?= base_url($url . '/report/student_detail/' . $student['id']) ?>">
                                                        <?= $student['name'] ?>
                                                    </a>
                                                </td>
                                                <td><?= $student['email'] ?></td>
                                                <td><?= date('M d, Y', strtotime($student['joining_date'])) ?></td>
                                                <td>
                                                    <a href="<?= base_url($url . '/report/student_detail/' . $student['id']) ?>" class="btn btn-sm btn-icon btn-outline-primary">
                                                        <i class="feather icon-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No students enrolled in this course.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Course Tests Tab -->
            <div class="tab-pane fade" id="courseTests">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-header-title mb-0">Course Tests</h5>
                        <button class="btn btn-sm btn-outline-primary" onclick="window.location.href='<?= base_url($url . '/tests/add') ?>'">
                            <i class="feather icon-plus-circle mr-1"></i> Add Test
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Test Performance Chart -->
                        <div class="mb-4">
                            <h6 class="mb-3">Test Performance Overview</h6>
                            <canvas id="testPerformanceChart" height="250"></canvas>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Test Title</th>
                                        <th>Module</th>
                                        <th>Time Period</th>
                                        <th>Submissions</th>
                                        <th>Avg. Score</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($tests)): ?>
                                        <?php $i = 1; foreach ($tests as $test): ?>
                                            <tr>
                                                <td><?= $i++ ?></td>
                                                <td>
                                                    <a href="<?= base_url($url . '/report/test_detail/' . $test['id']) ?>">
                                                        <?= $test['title'] ?>
                                                    </a>
                                                </td>
                                                <td><?= isset($module_map[$test['module_id']]) ? $module_map[$test['module_id']] : 'N/A' ?></td>
                                                <td>
                                                    <small>
                                                        <?= date('M d, Y', strtotime($test['start_date'])) ?> - 
                                                        <?= date('M d, Y', strtotime($test['end_date'])) ?>
                                                    </small>
                                                </td>
                                                <td class="text-center"><?= $test['submission_count'] ?></td>
                                                <td>
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar <?= ($test['avg_score'] >= 60) ? 'bg-success' : 'bg-danger' ?>" 
                                                             role="progressbar" 
                                                             style="width: <?= $test['avg_score'] ?>%" 
                                                             aria-valuenow="<?= $test['avg_score'] ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <span class="small"><?= number_format($test['avg_score'], 1) ?>%</span>
                                                </td>
                                                <td>
                                                    <a href="<?= base_url($url . '/report/test_detail/' . $test['id']) ?>" class="btn btn-sm btn-icon btn-outline-primary">
                                                        <i class="feather icon-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">No tests found for this course.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
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
        
        // Test performance chart
        var testLabels = [];
        var testScores = [];
        
        <?php foreach ($tests as $test): ?>
            testLabels.push("<?= addslashes($test['title']) ?>");
            testScores.push(<?= $test['avg_score'] ?>);
        <?php endforeach; ?>
        
        var testCtx = document.getElementById('testPerformanceChart').getContext('2d');
        var testPerformanceChart = new Chart(testCtx, {
            type: 'bar',
            data: {
                labels: testLabels,
                datasets: [{
                    label: 'Average Score (%)',
                    data: testScores,
                    backgroundColor: 'rgba(84, 105, 212, 0.8)',
                    borderColor: 'rgba(84, 105, 212, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw + '%';
                            }
                        }
                    }
                }
            }
        });
    });
</script>