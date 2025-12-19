<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Inventory Reports & Dashboard</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/inventory'); ?>">Inventory</a></li>
                <li class="breadcrumb-item">Reports & Dashboard</li>
            </ol>
        </div>

        <!-- Key Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-primary"><?php echo $total_students; ?></h4>
                        <p class="mb-0">Total Students</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-info"><?php echo $total_staff; ?></h4>
                        <p class="mb-0">Total Staff</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-success"><?php echo $stats['total_instruments']; ?></h4>
                        <p class="mb-0">Total Instruments</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-warning"><?php echo $stats['available_instruments']; ?></h4>
                        <p class="mb-0">Available Instruments</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Performance Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card border-primary">
                    <div class="card-body text-center">
                        <h4 class="text-primary"><?php echo $student_performance['average_score']; ?>%</h4>
                        <p class="mb-0">Avg Test Score</p>
                        <small class="text-muted"><?php echo $student_performance['total_active_students']; ?> students</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <h4 class="text-success"><?php echo $student_performance['pass_rate']; ?>%</h4>
                        <p class="mb-0">Pass Rate</p>
                        <small class="text-muted"><?php echo $student_performance['total_tests_taken']; ?> tests</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <h4 class="text-info"><?php echo $student_performance['course_completion_rate']; ?>%</h4>
                        <p class="mb-0">Course Completion</p>
                        <small class="text-muted">80%+ progress</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <h4 class="text-warning"><?php echo max(0, $total_students - $student_performance['total_active_students']); ?></h4>
                        <p class="mb-0">Students Without Tests</p>
                        <small class="text-muted">No recorded scores</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Status Overview -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Instrument Status Distribution</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Instrument Categories</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="categoryChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Reports -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Availability Report</h6>
            </div>
            <div class="card-body">
                <div class="form-row align-items-center mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="available">Available</option>
                            <option value="issued">Issued</option>
                            <option value="maintenance">Under Maintenance</option>
                            <option value="damaged">Damaged</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Category</label>
                        <select class="form-control" id="categoryFilter">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $key => $name): ?>
                                <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-none d-md-block">&nbsp;</label>
                        <button class="btn btn-primary btn-block" onclick="applyFilters()">Generate Report</button>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-none d-md-block">&nbsp;</label>
                        <a href="<?php echo base_url($url.'/inventory'); ?>" class="btn btn-secondary btn-block">Back to Inventory</a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="availabilityTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Instrument</th>
                                <th>Category</th>
                                <th>Serial No</th>
                                <th>Status</th>
                                <th>Status Details</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($availability_report)) {
                                foreach ($availability_report as $item) { ?>
                                    <tr>
                                        <td><?php echo $item['name']; ?></td>
                                        <td><?php echo $categories[$item['category']] ?? $item['category']; ?></td>
                                        <td><?php echo $item['serial_no']; ?></td>
                                        <td>
                                            <span class="badge badge-<?php
                                                echo $item['availability_status'] == INSTRUMENT_STATUS_AVAILABLE ? 'success' :
                                                     ($item['availability_status'] == INSTRUMENT_STATUS_ISSUED ? 'warning' :
                                                     ($item['availability_status'] == INSTRUMENT_STATUS_MAINTENANCE ? 'info' : 'danger'));
                                            ?>">
                                                <?php
                                                switch($item['availability_status']) {
                                                    case INSTRUMENT_STATUS_AVAILABLE: echo 'Available'; break;
                                                    case INSTRUMENT_STATUS_ISSUED: echo 'Issued'; break;
                                                    case INSTRUMENT_STATUS_MAINTENANCE: echo 'Under Maintenance'; break;
                                                    case INSTRUMENT_STATUS_DAMAGED: echo 'Damaged'; break;
                                                    default: echo 'Unknown';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td><?php echo $item['status_details'] ?? '-'; ?></td>
                                        <td><?php echo date('d M Y', strtotime($item['updated_at'])); ?></td>
                                    </tr>
                            <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="6" class="text-center">No instruments found</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Overdue Returns Alert -->
        <?php if (!empty($overdue_returns)): ?>
        <div class="card border-danger mb-4">
            <div class="card-header bg-danger text-white">
                <h6 class="mb-0"><i class="feather icon-alert-triangle"></i> Overdue Returns (<?php echo count($overdue_returns); ?>)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Instrument</th>
                                <th>Serial No</th>
                                <th>Issued To</th>
                                <th>Expected Return</th>
                                <th>Days Overdue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($overdue_returns as $overdue): ?>
                                <tr>
                                    <td><?php echo $overdue['instrument_name']; ?></td>
                                    <td><?php echo $overdue['serial_no']; ?></td>
                                    <td><?php echo $overdue['issued_to']; ?></td>
                                    <td><?php echo date('d M Y', strtotime($overdue['expected_return_date'])); ?></td>
                                    <td>
                                        <span class="badge badge-danger">
                                            <?php echo floor((time() - strtotime($overdue['expected_return_date'])) / (60*60*24)); ?> days
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Maintenance Summary -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Maintenance Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-warning"><?php echo $stats['pending_maintenance']; ?></h4>
                                <p class="mb-0 small">Pending</p>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success"><?php echo $this->db_model->count(TABLE_INSTRUMENT_MAINTENANCE, ["status" => "completed"]); ?></h4>
                                <p class="mb-0 small">Completed</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Issue Summary</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-primary"><?php echo $stats['active_issues']; ?></h4>
                                <p class="mb-0 small">Currently Issued</p>
                            </div>
                            <div class="col-6">
                                <h4 class="text-info"><?php echo $this->db_model->count(TABLE_INSTRUMENT_ISSUES, ["status" => "returned"]); ?></h4>
                                <p class="mb-0 small">Total Returns</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function applyFilters() {
    const status = document.getElementById('statusFilter').value;
    const category = document.getElementById('categoryFilter').value;

    const url = new URL(window.location);
    if (status) url.searchParams.set('status', status);
    if (category) url.searchParams.set('category', category);

    window.location.href = url.toString();
}

// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Available', 'Issued', 'Under Maintenance', 'Damaged'],
        datasets: [{
            data: [
                <?php echo $stats['available_instruments']; ?>,
                <?php echo $stats['issued_instruments']; ?>,
                <?php echo $stats['maintenance_instruments']; ?>,
                <?php echo $stats['total_instruments'] - $stats['available_instruments'] - $stats['issued_instruments'] - $stats['maintenance_instruments']; ?>
            ],
            backgroundColor: ['#28a745', '#ffc107', '#17a2b8', '#dc3545']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Category Distribution Chart (simplified - would need actual category counts)
const categoryCtx = document.getElementById('categoryChart').getContext('2d');
const categoryChart = new Chart(categoryCtx, {
    type: 'bar',
    data: {
        labels: ['String', 'Percussion', 'Wind', 'Keyboard', 'Electronic'],
        datasets: [{
            label: 'Instruments by Category',
            data: [12, 8, 6, 4, 2], // This would need to be calculated from actual data
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1']
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Initialize DataTable
$(document).ready(function() {
    $('#availabilityTable').DataTable({
        "pageLength": 25,
        "order": [[ 0, "asc" ]]
    });
});
</script>