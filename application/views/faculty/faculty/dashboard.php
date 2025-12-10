<!-- faculty/dashboard.php -->
<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Faculty Dashboard</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item">Dashboard</li>
            </ol>
        </div>
        
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Stats Cards Row -->
        <div class="row mb-4">
            <!-- Total Students Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= $total_students ?></h4>
                                <p class="text-muted mb-0">Total Students</p>
                            </div>
                            <div class="bg-primary rounded p-3">
                                <i class="feather icon-users text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Courses Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= $total_courses ?></h4>
                                <p class="text-muted mb-0">My Courses</p>
                            </div>
                            <div class="bg-success rounded p-3">
                                <i class="feather icon-book text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Tests Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= $active_tests ?></h4>
                                <p class="text-muted mb-0">Active Tests</p>
                            </div>
                            <div class="bg-warning rounded p-3">
                                <i class="feather icon-edit text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Questions Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= $total_questions ?></h4>
                                <p class="text-muted mb-0">My Questions</p>
                            </div>
                            <div class="bg-info rounded p-3">
                                <i class="feather icon-help-circle text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department-wise Batch Count Table -->
        <div class="row mb-4">
            <div class="col-lg-12 col-md-12">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-header-title mb-0">Department-wise Batch Count</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Department</th>
                                        <?php foreach ($department_batch_table['years'] as $year): ?>
                                            <th class="text-center">Batch <?= $year ?></th>
                                        <?php endforeach; ?>
                                        <th class="text-center bg-light">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($department_batch_table['departments'] as $dept): ?>
                                        <tr>
                                            <td><strong><?= $dept['name'] ?></strong></td>
                                            <?php foreach ($department_batch_table['years'] as $year): ?>
                                                <td class="text-center"><?= isset($dept['batches'][$year]) ? $dept['batches'][$year] : 0 ?></td>
                                            <?php endforeach; ?>
                                            <td class="text-center bg-light"><strong><?= $dept['total'] ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <!-- Total row -->
                                    <tr class="bg-light">
                                        <td><strong>Total</strong></td>
                                        <?php 
                                        $batch_totals = [];
                                        foreach ($department_batch_table['years'] as $year) {
                                            $year_total = 0;
                                            foreach ($department_batch_table['departments'] as $dept) {
                                                $year_total += isset($dept['batches'][$year]) ? $dept['batches'][$year] : 0;
                                            }
                                            $batch_totals[$year] = $year_total;
                                        }
                                        ?>
                                        <?php foreach ($department_batch_table['years'] as $year): ?>
                                            <td class="text-center"><strong><?= $batch_totals[$year] ?></strong></td>
                                        <?php endforeach; ?>
                                        <td class="text-center"><strong><?= array_sum($batch_totals) ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Question Distribution -->
            <div class="col-lg-3 col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h5 class="card-header-title mb-0">Questions by Difficulty</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="questionDistributionChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            <!-- Question Bank Statistics -->
            <div class="col-lg-4 col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-header-title mb-0">Question Bank Statistics</h5>
                        <a href="<?= base_url($url . '/question') ?>" class="btn btn-sm btn-outline-primary">Manage Questions</a>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-primary rounded p-2 mr-3">
                                        <i class="feather icon-help-circle text-white"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">Total </p>
                                        <h5 class="mb-0"><?= $total_questions ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-success rounded p-2 mr-3">
                                        <i class="fas fa-code text-white"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">Code</p>
                                        <h5 class="mb-0"><?= $code_questions ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="bg-info rounded p-2 mr-3">
                                        <i class="feather icon-check-square text-white"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0">MCQ </p>
                                        <h5 class="mb-0"><?= $mcq_questions ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h6 class="mb-3">Questions by Difficulty</h6>
                            <div class="progress-wrapper mb-4">
                                <div class="progress-label d-flex justify-content-between mb-1">
                                    <span>Easy</span>
                                    <span><?= $easy_questions_percent ?>%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $easy_questions_percent ?>%"></div>
                                </div>
                            </div>
                            <div class="progress-wrapper mb-4">
                                <div class="progress-label d-flex justify-content-between mb-1">
                                    <span>Medium</span>
                                    <span><?= $medium_questions_percent ?>%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $medium_questions_percent ?>%"></div>
                                </div>
                            </div>
                            <div class="progress-wrapper">
                                <div class="progress-label d-flex justify-content-between mb-1">
                                    <span>Hard</span>
                                    <span><?= $hard_questions_percent ?>%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?= $hard_questions_percent ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Question Types Chart -->
            <div class="col-lg-5 col-md-12 mb-4">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-header-title mb-0">Question Types Distribution</h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#typesTab" role="tab">Types</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#subtypesTab" role="tab">Subtypes</a>
                            </li>
                        </ul>
                        <div class="tab-content mt-3">
                            <div class="tab-pane fade show active" id="typesTab" role="tabpanel">
                                <canvas id="questionTypesChart" height="200"></canvas>
                            </div>
                            <div class="tab-pane fade" id="subtypesTab" role="tabpanel">
                                <canvas id="questionSubtypesChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-header-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 mb-4">
                                <a href="<?= base_url($url . '/question/add') ?>" class="btn btn-outline-primary btn-block p-3">
                                    <i class="feather icon-plus-circle mb-2" style="font-size: 24px;"></i>
                                    <div>Add New Question</div>
                                </a>
                            </div>
                            <div class="col-6 mb-4">
                                <a href="<?= base_url($url . '/test/create') ?>" class="btn btn-outline-success btn-block p-3">
                                    <i class="feather icon-edit mb-2" style="font-size: 24px;"></i>
                                    <div>Create New Test</div>
                                </a>
                            </div>
                            <div class="col-6 mb-4">
                                <a href="<?= base_url($url . '/course/new') ?>" class="btn btn-outline-info btn-block p-3">
                                    <i class="feather icon-book mb-2" style="font-size: 24px;"></i>
                                    <div>Add New Course</div>
                                </a>
                            </div>
                            <div class="col-6 mb-4">
                                <a href="<?= $manage_student_url ?>" class="btn btn-outline-warning btn-block p-3">
                                    <i class="feather icon-user-plus mb-2" style="font-size: 24px;"></i>
                                    <div>Manage Students</div>
                                </a>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <div class="d-flex">
                                        <i class="feather icon-info mr-2" style="font-size: 20px;"></i>
                                        <div>
                                            <h6 class="alert-heading mb-1">Need help?</h6>
                                            <p class="mb-0">Check out the documentation or DrillU Team for assistance with any features.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url("/") ?>assets/packages/chart.min.js"></script>

<!-- JavaScript for Chart Initialization -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Global Chart.js configuration to prevent memory leaks
    Chart.defaults.animation = false; // Disable all animations globally
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = true; // IMPORTANT: Using false causes memory leaks
    
    // Store all chart instances for proper cleanup
    let chartInstances = {};
    
    // Initialize charts only once
    initializeCharts();
    
    // Safely destroy all charts on page unload to free memory
    window.addEventListener('beforeunload', destroyAllCharts);
    
    // Clean up event listeners
    setupEventListeners();
    
    function setupEventListeners() {
        // Add only one event listener per element
        const testPerformanceFilter = document.getElementById('testPerformanceFilter');
        if (testPerformanceFilter) {
            testPerformanceFilter.removeEventListener('change', handlePerformanceFilterChange);
            testPerformanceFilter.addEventListener('change', handlePerformanceFilterChange);
        }
        
        const courseEnrollmentPeriod = document.getElementById('courseEnrollmentPeriod');
        if (courseEnrollmentPeriod) {
            courseEnrollmentPeriod.removeEventListener('change', handleEnrollmentPeriodChange);
            courseEnrollmentPeriod.addEventListener('change', handleEnrollmentPeriodChange);
        }
    }
    
    function handlePerformanceFilterChange() {
        console.log('Performance period changed to: ' + this.value);
        // Add AJAX call here when needed, but ensure you destroy the old chart before creating a new one
    }
    
    function handleEnrollmentPeriodChange() {
        console.log('Enrollment period changed to: ' + this.value);
        // Add AJAX call here when needed, but ensure you destroy the old chart before creating a new one
    }
    
    function initializeCharts() {
        console.log('Initializing charts once');
        
        // Question Distribution Chart
        createChart('questionDistributionChart', 'doughnut', {
            labels: ['Easy', 'Medium', 'Hard'],
            datasets: [{
                data: [
                    <?= $easy_questions_percent ?>, 
                    <?= $medium_questions_percent ?>, 
                    <?= $hard_questions_percent ?>
                ],
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)',
                    'rgba(220, 53, 69, 0.8)'
                ],
                borderWidth: 1
            }]
        }, {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.raw + '%';
                        }
                    }
                }
            }
        });

        // Question Types Chart
        const questionTypesData = {
            labels: <?= json_encode($question_types) ?>,
            counts: <?= json_encode($question_type_counts) ?>,
            colors: <?= json_encode($question_type_colors) ?>
        };

        createChart('questionTypesChart', 'pie', {
            labels: questionTypesData.labels,
            datasets: [{
                data: questionTypesData.counts,
                backgroundColor: questionTypesData.colors.slice(0, questionTypesData.labels.length),
                borderWidth: 1
            }]
        }, {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const total = context.dataset.data.reduce((a, b) => a + parseInt(b), 0);
                            const percentage = total > 0 ? Math.round((parseInt(value) / total) * 100) : 0;
                            return `${context.label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        });

        // Question Subtypes Chart
        const questionSubtypesData = {
            labels: <?= json_encode($question_subtypes) ?>,
            counts: <?= json_encode($question_subtype_counts) ?>,
            colors: <?= json_encode($question_subtype_colors) ?>
        };

        createChart('questionSubtypesChart', 'pie', {
            labels: questionSubtypesData.labels,
            datasets: [{
                data: questionSubtypesData.counts,
                backgroundColor: questionSubtypesData.colors.slice(0, questionSubtypesData.labels.length),
                borderWidth: 1
            }]
        }, {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const total = context.dataset.data.reduce((a, b) => a + parseInt(b), 0);
                            const percentage = total > 0 ? Math.round((parseInt(value) / total) * 100) : 0;
                            return `${context.label}: ${value} (${percentage}%)`;
                        }
                    }
                }
            }
        });
    }
    
    // Helper function to safely create charts
    function createChart(id, type, data, options = {}) {
        const canvas = document.getElementById(id);
        if (!canvas) {
            console.warn(`Canvas element with id ${id} not found`);
            return null;
        }
        
        // Destroy existing chart if it exists
        if (chartInstances[id]) {
            chartInstances[id].destroy();
            chartInstances[id] = null;
        }
        
        // Get 2D context with null check
        const ctx = canvas.getContext('2d');
        if (!ctx) {
            console.warn(`Could not get 2D context for canvas ${id}`);
            return null;
        }
        
        try {
            // Create new chart with safe defaults
            const defaultOptions = {
                animation: false,
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: true
                    }
                }
            };
            
            // Merge default options with provided options
            const mergedOptions = Object.assign({}, defaultOptions, options);
            
            // Create and store chart instance
            chartInstances[id] = new Chart(ctx, {
                type: type,
                data: data,
                options: mergedOptions
            });
            
            return chartInstances[id];
        } catch (error) {
            console.error(`Error creating chart ${id}:`, error);
            return null;
        }
    }
    
    // Helper function to destroy all charts
    function destroyAllCharts() {
        Object.keys(chartInstances).forEach(id => {
            if (chartInstances[id]) {
                try {
                    chartInstances[id].destroy();
                } catch (error) {
                    console.warn(`Error destroying chart ${id}:`, error);
                }
                chartInstances[id] = null;
            }
        });
        // Clear the object to help garbage collection
        chartInstances = {};
    }
    
    // Add a resize handler with debounce to prevent continuous resizing
    let resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(function() {
            // Resize all charts after window resize completes
            Object.keys(chartInstances).forEach(id => {
                if (chartInstances[id] && typeof chartInstances[id].resize === 'function') {
                    chartInstances[id].resize();
                }
            });
        }, 250);
    });
});
</script>