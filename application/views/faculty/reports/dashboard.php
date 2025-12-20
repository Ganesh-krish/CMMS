<!-- enhanced-dashboard.php -->
<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Advanced Analytics Dashboard</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($url) ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url . '/report') ?>">Reports</a></li>
                <li class="breadcrumb-item active">Analytics Dashboard</li>
            </ol>
        </div>
        
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Summary Cards -->
        <div class="row">
            <!-- Total Tests Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= $total_tests ?></h4>
                                <p class="text-muted mb-0">Total Tests</p>
                            </div>
                            <div class="bg-primary rounded p-3">
                                <i class="feather icon-file-text text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Total Students Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= $total_students ?></h4>
                                <p class="text-muted mb-0">Active Students</p>
                            </div>
                            <div class="bg-success rounded p-3">
                                <i class="feather icon-users text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Completion Rate Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= number_format($completion_rate, 1) ?>%</h4>
                                <p class="text-muted mb-0">Test Completion Rate</p>
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
                                <h4 class="mb-0"><?= number_format($overall_avg_score, 1) ?>%</h4>
                                <p class="text-muted mb-0">Overall Average Score</p>
                            </div>
                            <div class="bg-warning rounded p-3">
                                <i class="feather icon-award text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Time Range -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-header-title mb-0">Analysis Filters</h5>
            </div>
            <div class="card-body">
                <form method="get" action="<?= base_url($url . '/report/dashboard') ?>" id="dashboardFilterForm">
                    <div class="row">
                        <div class="col-md-3 form-group">
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
                        
                        <div class="col-md-3 form-group">
                            <label for="module_id">Module</label>
                            <select class="form-control select2" id="module_id" name="module_id">
                                <option value="">All Modules</option>
                                <?php foreach ($modules as $module): ?>
                                <option value="<?= $module['id'] ?>" <?= ($this->input->get('module_id') == $module['id']) ? 'selected' : '' ?>>
                                    <?= $module['name'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3 form-group">
                            <label for="date_range">Date Range</label>
                            <input type="text" class="form-control daterangepicker" id="date_range" name="date_range" value="<?= $this->input->get('date_range') ?? '' ?>" placeholder="Select date range">
                        </div>
                        
                        <div class="col-md-3 form-group">
                            <label for="export_format">Export Format</label>
                            <select class="form-control" id="export_format" name="export_format">
                                <option value="csv">CSV</option>
                                <option value="excel">Excel</option>
                                <option value="pdf">PDF</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                        <a href="<?= base_url($url . '/report/dashboard') ?>" class="btn btn-outline-secondary">Reset</a>
                        <button type="button" id="exportBtn" class="btn btn-success">
                            <i class="feather icon-download"></i> Export Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Performance Overview -->
        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-header-title mb-0">Overall Performance Trends</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="timeframeDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Last 30 Days
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="timeframeDropdown">
                                <a class="dropdown-item time-option" href="#" data-value="7">Last 7 Days</a>
                                <a class="dropdown-item time-option" href="#" data-value="30">Last 30 Days</a>
                                <a class="dropdown-item time-option" href="#" data-value="90">Last 90 Days</a>
                                <a class="dropdown-item time-option" href="#" data-value="180">Last 6 Months</a>
                                <a class="dropdown-item time-option" href="#" data-value="365">Last Year</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceTrendsChart" height="350"></canvas>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-header-title mb-0">Test Completion Analysis</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="testCompletionPieChart" height="220"></canvas>
                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success rounded-circle mr-2" style="width: 10px; height: 10px;"></div>
                                    <span>Completed</span>
                                </div>
                                <span class="font-weight-bold"><?= $completed_tests ?> (<?= number_format($completion_rate, 1) ?>%)</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning rounded-circle mr-2" style="width: 10px; height: 10px;"></div>
                                    <span>In Progress</span>
                                </div>
                                <span class="font-weight-bold"><?= $in_progress_tests ?> (<?= number_format($in_progress_rate, 1) ?>%)</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger rounded-circle mr-2" style="width: 10px; height: 10px;"></div>
                                    <span>Not Started</span>
                                </div>
                                <span class="font-weight-bold"><?= $not_started_tests ?> (<?= number_format($not_started_rate, 1) ?>%)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Student Performance and Question Analysis -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-header-title mb-0">Top Performing Students</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="datatable table table-hover card-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Student</th>
                                        <th>Tests</th>
                                        <th>Avg. Score</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($top_students)): ?>
                                        <?php $i = 1; foreach ($top_students as $student): ?>
                                            <tr>
                                                <td><?= $i++ ?></td>
                                                <td>
                                                    <a href="<?= base_url($url . '/report/student_detail/' . $student['id']) ?>">
                                                        <?= $student['name'] ?>
                                                    </a>
                                                </td>
                                                <td><?= $student['tests_completed'] ?>/<?= $student['total_tests'] ?></td>
                                                <td class="font-weight-bold"><?= number_format($student['avg_score'], 1) ?>%</td>
                                                <td>
                                                    <div class="progress" style="height: 6px; width: 100px;">
                                                        <div class="progress-bar bg-success" 
                                                             role="progressbar" 
                                                             style="width: <?= $student['completion_percentage'] ?>%" 
                                                             aria-valuenow="<?= $student['completion_percentage'] ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No student performance data available.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-header-title mb-0">Question Difficulty Analysis</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <canvas id="difficultyDistributionChart" height="200"></canvas>
                            </div>
                            <div class="col-md-6">
                                <canvas id="successRateByDifficultyChart" height="200"></canvas>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <h6 class="mt-4 mb-3">Most Challenging Questions</h6>
                            <div class="table-responsive">
                                <table class="datatable table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Question</th>
                                            <th>Test</th>
                                            <th>Success Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($challenging_questions)): ?>
                                            <?php foreach ($challenging_questions as $question): ?>
                                                <tr>
                                                    <td class="text-truncate" style="max-width: 200px;" title="<?= htmlspecialchars($question['title']) ?>">
                                                        <?= htmlspecialchars($question['title']) ?>
                                                    </td>
                                                    <td class="text-truncate" style="max-width: 150px;">
                                                        <?= $question['test_title'] ?>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="progress flex-grow-1 mr-2" style="height: 5px;">
                                                                <div class="progress-bar bg-danger" 
                                                                     role="progressbar" 
                                                                     style="width: <?= $question['success_rate'] ?>%" 
                                                                     aria-valuenow="<?= $question['success_rate'] ?>" 
                                                                     aria-valuemin="0" 
                                                                     aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                            <span class="small"><?= number_format($question['success_rate'], 1) ?>%</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="3" class="text-center">No question analysis data available.</td>
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
        
        <!-- Time Spent Analysis -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-header-title mb-0">Time Spent Analysis</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 mb-4 mb-lg-0">
                        <div class="card shadow-none bg-light">
                            <div class="card-body">
                                <h6 class="text-center mb-3">Average Time Spent per Test</h6>
                                <div class="d-flex justify-content-center">
                                    <div class="text-center">
                                        <div class="display-4 font-weight-bold text-primary"><?= floor($avg_time_per_test / 60) ?></div>
                                        <div class="text-muted">minutes</div>
                                    </div>
                                    <div class="text-center ml-3">
                                        <div class="display-4 font-weight-bold text-primary"><?= $avg_time_per_test % 60 ?></div>
                                        <div class="text-muted">seconds</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-8">
                        <canvas id="timeSpentByModuleChart" height="250"></canvas>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="mb-3">Time Spent Distribution by Question Difficulty</h6>
                        <div class="table-responsive">
                            <table class="datatable table">
                                <thead>
                                    <tr>
                                        <th>Difficulty</th>
                                        <th>Average Time</th>
                                        <th>Min Time</th>
                                        <th>Max Time</th>
                                        <th>Distribution</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <span class="badge badge-success">Easy</span>
                                        </td>
                                        <td><?= floor($avg_time_easy / 60) ?>m <?= $avg_time_easy % 60 ?>s</td>
                                        <td><?= floor($min_time_easy / 60) ?>m <?= $min_time_easy % 60 ?>s</td>
                                        <td><?= floor($max_time_easy / 60) ?>m <?= $max_time_easy % 60 ?>s</td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: <?= ($avg_time_easy / $max_time_total) * 100 ?>%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="badge badge-warning">Medium</span>
                                        </td>
                                        <td><?= floor($avg_time_medium / 60) ?>m <?= $avg_time_medium % 60 ?>s</td>
                                        <td><?= floor($min_time_medium / 60) ?>m <?= $min_time_medium % 60 ?>s</td>
                                        <td><?= floor($max_time_medium / 60) ?>m <?= $max_time_medium % 60 ?>s</td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?= ($avg_time_medium / $max_time_total) * 100 ?>%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="badge badge-danger">Hard</span>
                                        </td>
                                        <td><?= floor($avg_time_hard / 60) ?>m <?= $avg_time_hard % 60 ?>s</td>
                                        <td><?= floor($min_time_hard / 60) ?>m <?= $min_time_hard % 60 ?>s</td>
                                        <td><?= floor($max_time_hard / 60) ?>m <?= $max_time_hard % 60 ?>s</td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?= ($avg_time_hard / $max_time_total) * 100 ?>%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab Navigation for Detailed Analysis -->
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#testAnalysis">Test Analysis</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#courseAnalysis">Course Analysis</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tabSwitchAnalysis">Tab Switch Analysis</a>
            </li>
        </ul>
        
        <div class="tab-content">
            <!-- Test Analysis Tab -->
            <div class="tab-pane fade show active" id="testAnalysis">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-header-title mb-0">Test Performance Breakdown</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="datatable table table-hover card-table" id="testAnalysisTable">
                                <thead>
                                    <tr>
                                        <th>Test Title</th>
                                        <th>Module</th>
                                        <th>Submissions</th>
                                        <th>Avg. Score</th>
                                        <th>Pass Rate</th>
                                        <th>Avg. Time</th>
                                        <th>Difficulty</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($test_analysis)): ?>
                                        <?php foreach ($test_analysis as $test): ?>
                                            <tr>
                                                <td>
                                                    <a href="<?= base_url($url . '/report/test_detail/' . $test['id']) ?>">
                                                        <?= $test['title'] ?>
                                                    </a>
                                                </td>
                                                <td><?= isset($module_map[$test['module_id']]) ? $module_map[$test['module_id']] : 'N/A' ?></td>
                                                <td class="text-center"><?= $test['submission_count'] ?>/<?= $test['student_count'] ?></td>
                                                <td>
                                                    <div class="progress" style="height: 6px; width: 80px;">
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
                                                <td><?= number_format($test['pass_rate'], 1) ?>%</td>
                                                <td><?= floor($test['avg_time'] / 60) ?>m <?= $test['avg_time'] % 60 ?>s</td>
                                                <td>
                                                    <?php
                                                    $difficulty_badge = 'badge-secondary';
                                                    $difficulty_text = 'N/A';
                                                    
                                                    if ($test['avg_difficulty'] > 0 && $test['avg_difficulty'] <= 1.67) {
                                                        $difficulty_badge = 'badge-success';
                                                        $difficulty_text = 'Easy';
                                                    } elseif ($test['avg_difficulty'] > 1.67 && $test['avg_difficulty'] <= 2.33) {
                                                        $difficulty_badge = 'badge-warning';
                                                        $difficulty_text = 'Medium';
                                                    } elseif ($test['avg_difficulty'] > 2.33) {
                                                        $difficulty_badge = 'badge-danger';
                                                        $difficulty_text = 'Hard';
                                                    }
                                                    ?>
                                                    <span class="badge <?= $difficulty_badge ?>"><?= $difficulty_text ?></span>
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
                                            <td colspan="8" class="text-center">No test analysis data available.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Course Analysis Tab -->
            <div class="tab-pane fade" id="courseAnalysis">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-header-title mb-0">Course Performance Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-lg-6">
                                <canvas id="courseComparisonChart" height="300"></canvas>
                            </div>
                            <div class="col-lg-6">
                                <canvas id="courseOverTimeChart" height="300"></canvas>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="courseAnalysisTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Course Name</th>
                                        <th>Code</th>
                                        <th>Students</th>
                                        <th>Tests</th>
                                        <th>Completion</th>
                                        <th>Avg. Score</th>
                                        <th>Pass Rate</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($course_analysis)): ?>
                                        <?php foreach ($course_analysis as $course): ?>
                                            <tr>
                                                <td>
                                                    <a href="<?= base_url($url . '/report/course_detail/' . $course['id']) ?>">
                                                        <?= $course['name'] ?>
                                                    </a>
                                                </td>
                                                <td><?= $course['course_code'] ?></td>
                                                <td><?= $course['student_count'] ?></td>
                                                <td><?= $course['test_count'] ?></td>
                                                <td>
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar bg-info" 
                                                             role="progressbar" 
                                                             style="width: <?= $course['completion_rate'] ?>%" 
                                                             aria-valuenow="<?= $course['completion_rate'] ?>" 
                                                             aria-valuemin="0" 
                                                             aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                    <span class="small"><?= number_format($course['completion_rate'], 1) ?>%</span>
                                                </td>
                                                <td class="font-weight-bold"><?= number_format($course['avg_score'], 1) ?>%</td>
                                                <td><?= number_format($course['pass_rate'], 1) ?>%</td>
                                                <td>
                                                    <a href="<?= base_url($url . '/report/course_detail/' . $course['id']) ?>" class="btn btn-sm btn-icon btn-outline-primary">
                                                        <i class="feather icon-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center">No course analysis data available.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tab Switch Analysis Tab -->
            <div class="tab-pane fade" id="tabSwitchAnalysis">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-header-title mb-0">Tab Switch Behavior Analysis</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-lg-4">
                                <div class="card shadow-none bg-light">
                                    <div class="card-body">
                                        <h6 class="text-center mb-3">Tab Switch Statistics</h6>
                                        <div class="d-flex flex-column">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Average Tab Switches:</span>
                                                <span class="font-weight-bold"><?= number_format($avg_tab_switches, 1) ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Min Tab Switches:</span>
                                                <span class="font-weight-bold"><?= $min_tab_switches ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Max Tab Switches:</span>
                                                <span class="font-weight-bold"><?= $max_tab_switches ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Median Tab Switches:</span>
                                                <span class="font-weight-bold"><?= $median_tab_switches ?></span>
                                            </div>
                                            <div class="d-flex justify-content-between">
                                                <span>Students Exceeding Limit:</span>
                                                <span class="font-weight-bold text-<?= ($exceeded_tab_switch_percentage > 20) ? 'danger' : 'success' ?>">
                                                    <?= $exceeded_tab_switch_count ?> (<?= number_format($exceeded_tab_switch_percentage, 1) ?>%)
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-8">
                                <canvas id="tabSwitchesDistributionChart" height="250"></canvas>
                            </div>
                        </div>
                        
                        <h6 class="mb-3">Tab Switch Correlation with Performance</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tabSwitchCorrelationTable">
                                <thead>
                                    <tr>
                                        <th>Tab Switch Range</th>
                                        <th>Student Count</th>
                                        <th>Avg. Score</th>
                                        <th>Pass Rate</th>
                                        <th>Correlation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>0 Switches</td>
                                        <td><?= $tab_switch_0_count ?></td>
                                        <td><?= number_format($tab_switch_0_avg_score, 1) ?>%</td>
                                        <td><?= number_format($tab_switch_0_pass_rate, 1) ?>%</td>
                                        <td>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-success" 
                                                     role="progressbar" 
                                                     style="width: <?= $tab_switch_0_avg_score ?>%" 
                                                     aria-valuenow="<?= $tab_switch_0_avg_score ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>1-2 Switches</td>
                                        <td><?= $tab_switch_1_2_count ?></td>
                                        <td><?= number_format($tab_switch_1_2_avg_score, 1) ?>%</td>
                                        <td><?= number_format($tab_switch_1_2_pass_rate, 1) ?>%</td>
                                        <td>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-primary" 
                                                     role="progressbar" 
                                                     style="width: <?= $tab_switch_1_2_avg_score ?>%" 
                                                     aria-valuenow="<?= $tab_switch_1_2_avg_score ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>3-5 Switches</td>
                                        <td><?= $tab_switch_3_5_count ?></td>
                                        <td><?= number_format($tab_switch_3_5_avg_score, 1) ?>%</td>
                                        <td><?= number_format($tab_switch_3_5_pass_rate, 1) ?>%</td>
                                        <td>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-warning" 
                                                     role="progressbar" 
                                                     style="width: <?= $tab_switch_3_5_avg_score ?>%" 
                                                     aria-valuenow="<?= $tab_switch_3_5_avg_score ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>6+ Switches</td>
                                        <td><?= $tab_switch_6_plus_count ?></td>
                                        <td><?= number_format($tab_switch_6_plus_avg_score, 1) ?>%</td>
                                        <td><?= number_format($tab_switch_6_plus_pass_rate, 1) ?>%</td>
                                        <td>
                                            <div class="progress" style="height: 6px;">
                                                <div class="progress-bar bg-danger" 
                                                     role="progressbar" 
                                                     style="width: <?= $tab_switch_6_plus_avg_score ?>%" 
                                                     aria-valuenow="<?= $tab_switch_6_plus_avg_score ?>" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
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
    // Initialize select2 and date range picker
    $('.select2').select2();
    
    $('.daterangepicker').daterangepicker({
        opens: 'left',
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'YYYY-MM-DD'
        }
    });
    
    $('.daterangepicker').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
    });
    
    $('.daterangepicker').on('cancel.daterangepicker', function() {
        $(this).val('');
    });
    
    // Auto-submit form when filters change
    $('#course_id, #module_id').on('change', function() {
        $('#dashboardFilterForm').submit();
    });
    
    // Export button functionality
    $('#exportBtn').on('click', function() {
        const format = $('#export_format').val();
        let url = '<?= base_url($url . '/report/export_dashboard/') ?>' + format;
        
        // Add filters to export URL
        const courseId = $('#course_id').val();
        const moduleId = $('#module_id').val();
        const dateRange = $('#date_range').val();
        
        if (courseId) {
            url += '?course_id=' + courseId;
        }
        
        if (moduleId) {
            url += (courseId ? '&' : '?') + 'module_id=' + moduleId;
        }
        
        if (dateRange) {
            url += (courseId || moduleId ? '&' : '?') + 'date_range=' + encodeURIComponent(dateRange);
        }
        
        window.location.href = url;
    });
    
    // Initialize datatables
    $('#testAnalysisTable, #courseAnalysisTable, #tabSwitchCorrelationTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'copy',
                filename: 'musiccollege-data',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'excel',
                filename: 'musiccollege-data',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdf',
                filename: 'musiccollege-data',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'print',
                title: 'Music College Data',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            }
        ],
        pageLength: 10,
        responsive: true
    });
    
    // Time period selection for performance trends chart
    $('.time-option').on('click', function(e) {
        e.preventDefault();
        const days = $(this).data('value');
        $('#timeframeDropdown').text($(this).text());
        
        // Update chart with AJAX call (would be implemented in actual system)
        console.log('Update chart for ' + days + ' days');
    });
    
    // Chart initializations - Performance Trends Chart
    const performanceTrendsCtx = document.getElementById('performanceTrendsChart').getContext('2d');
    const performanceTrendsChart = new Chart(performanceTrendsCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($trend_labels) ?>,
            datasets: [
                {
                    label: 'Average Score',
                    data: <?= json_encode($trend_avg_scores) ?>,
                    borderColor: 'rgba(84, 105, 212, 1)',
                    backgroundColor: 'rgba(84, 105, 212, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Completion Rate',
                    data: <?= json_encode($trend_completion_rates) ?>,
                    borderColor: 'rgba(40, 167, 69, 1)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true
                }
            ]
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
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + context.raw + '%';
                        }
                    }
                },
                legend: {
                    position: 'top'
                }
            },
            interaction: {
                mode: 'nearest',
                intersect: false
            }
        }
    });
    
    // Test Completion Pie Chart
    const testCompletionPieCtx = document.getElementById('testCompletionPieChart').getContext('2d');
    const testCompletionPieChart = new Chart(testCompletionPieCtx, {
        type: 'pie',
        data: {
            labels: ['Completed', 'In Progress', 'Not Started'],
            datasets: [{
                data: [
                    <?= $completed_tests ?>,
                    <?= $in_progress_tests ?>,
                    <?= $not_started_tests ?>
                ],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',  // Green for Completed
                    'rgba(255, 193, 7, 0.8)',  // Yellow for In Progress
                    'rgba(220, 53, 69, 0.8)'   // Red for Not Started
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((context.raw / total) * 100);
                            return context.label + ': ' + context.raw + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });
    
    // Difficulty Distribution Chart
    const difficultyDistributionCtx = document.getElementById('difficultyDistributionChart').getContext('2d');
    const difficultyDistributionChart = new Chart(difficultyDistributionCtx, {
        type: 'doughnut',
        data: {
            labels: ['Easy', 'Medium', 'Hard'],
            datasets: [{
                data: [
                    <?= $easy_questions_count ?>,
                    <?= $medium_questions_count ?>,
                    <?= $hard_questions_count ?>
                ],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',  // Green for Easy
                    'rgba(255, 193, 7, 0.8)',  // Yellow for Medium
                    'rgba(220, 53, 69, 0.8)'   // Red for Hard
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Question Difficulty Distribution'
                },
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    // Success Rate By Difficulty Chart
    const successRateByDifficultyCtx = document.getElementById('successRateByDifficultyChart').getContext('2d');
    const successRateByDifficultyChart = new Chart(successRateByDifficultyCtx, {
        type: 'bar',
        data: {
            labels: ['Easy', 'Medium', 'Hard'],
            datasets: [{
                label: 'Success Rate (%)',
                data: [
                    <?= $easy_questions_success_rate ?>,
                    <?= $medium_questions_success_rate ?>,
                    <?= $hard_questions_success_rate ?>
                ],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',  // Green for Easy
                    'rgba(255, 193, 7, 0.8)',  // Yellow for Medium
                    'rgba(220, 53, 69, 0.8)'   // Red for Hard
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Success Rate by Difficulty'
                },
                legend: {
                    display: false
                }
            },
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
            }
        }
    });
    
    // Time Spent By Module Chart
    const timeSpentByModuleCtx = document.getElementById('timeSpentByModuleChart').getContext('2d');
    const timeSpentByModuleChart = new Chart(timeSpentByModuleCtx, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($module_time_data, 'name')) ?>,
            datasets: [{
                label: 'Average Time (minutes)',
                data: <?= json_encode(array_map(function($time) { return round($time / 60, 1); }, array_column($module_time_data, 'avg_time'))) ?>,
                backgroundColor: 'rgba(23, 162, 184, 0.8)',
                borderColor: 'rgba(23, 162, 184, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Average Time Spent by Module'
                },
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + ' mins';
                        }
                    }
                }
            }
        }
    });
    
    // Course Comparison Chart
    const courseComparisonCtx = document.getElementById('courseComparisonChart').getContext('2d');
    const courseComparisonChart = new Chart(courseComparisonCtx, {
        type: 'radar',
        data: {
            labels: ['Completion Rate', 'Avg. Score', 'Pass Rate', 'Engagement', 'Student Satisfaction'],
            datasets: <?= json_encode($course_radar_data) ?>
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Course Performance Comparison'
                }
            },
            scales: {
                r: {
                    angleLines: {
                        display: true
                    },
                    suggestedMin: 0,
                    suggestedMax: 100
                }
            }
        }
    });
    
    // Course Over Time Chart
    const courseOverTimeCtx = document.getElementById('courseOverTimeChart').getContext('2d');
    const courseOverTimeChart = new Chart(courseOverTimeCtx, {
        type: 'line',
        data: {
            labels: <?= json_encode($course_trend_labels) ?>,
            datasets: <?= json_encode($course_trend_data) ?>
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Course Performance Over Time'
                }
            },
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
            }
        }
    });
    
    // Tab Switches Distribution Chart
    const tabSwitchesDistributionCtx = document.getElementById('tabSwitchesDistributionChart').getContext('2d');
    const tabSwitchesDistributionChart = new Chart(tabSwitchesDistributionCtx, {
        type: 'bar',
        data: {
            labels: ['0', '1', '2', '3', '4', '5', '6+'],
            datasets: [{
                label: 'Number of Students',
                data: [
                    <?= $tab_switch_distribution[0] ?? 0 ?>,
                    <?= $tab_switch_distribution[1] ?? 0 ?>,
                    <?= $tab_switch_distribution[2] ?? 0 ?>,
                    <?= $tab_switch_distribution[3] ?? 0 ?>,
                    <?= $tab_switch_distribution[4] ?? 0 ?>,
                    <?= $tab_switch_distribution[5] ?? 0 ?>,
                    <?= $tab_switch_distribution[6] ?? 0 ?>
                ],
                backgroundColor: 'rgba(84, 105, 212, 0.8)',
                borderColor: 'rgba(84, 105, 212, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Tab Switch Distribution'
                },
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Number of Tab Switches'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Students'
                    }
                }
            }
        }
    });
});
</script>