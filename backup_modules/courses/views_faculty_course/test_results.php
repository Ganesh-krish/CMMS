<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Test Results</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/'.$route) ?>">Courses</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/'.$route.'/'.'modules/'.$course['id']) ?>">Modules</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/'.$route.'/'.'module_tests/'.$course['id'].'/'.$module['id']) ?>">Module Tests</a></li>
                <li class="breadcrumb-item active">Test Results</li>
            </ol>
        </div>

        <!-- Test Overview -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="card-title mb-2"><?= $test['title'] ?></h5>
                        <p class="text-muted mb-0">Module: <?= $module['name'] ?></p>
                    </div>
                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                        <div class="d-flex flex-column">
                            <p class="mb-1"><strong>Start Date:</strong> <?= date('M d, Y h:i A', strtotime($test['course_start_date'])) ?></p>
                            <p class="mb-1"><strong>End Date:</strong> <?= date('M d, Y h:i A', strtotime($test['course_end_date'])) ?></p>
                            <p class="mb-0"><strong>Duration:</strong> <?= $test['duration'] ?> minutes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Submissions Table -->
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h5 class="mb-2">Test Results</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge badge-primary">Total: <?= $total_students ?></span>
                            <span class="badge badge-success">Completed: <?= $completed_submissions ?></span>
                            <span class="badge badge-warning">In Progress: <?= $in_progress_count ?></span>
                            <span class="badge badge-danger">Not Attempted: <?= $not_attempted_count ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-md-end gap-2">
                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#reportModal">
                                <i class="fas fa-file-alt"></i> Generate Report
                            </button>
                            <?php 
                            $show_sync = strtotime($test['course_end_date']) < time() && ($in_progress_count > 0 || $not_attempted_count > 0);
                            if ($show_sync): 
                            ?>
                            <button type="button" class="btn btn-primary" id="syncButton" onclick="syncTestResults()">
                                <i class="fas fa-sync-alt"></i> Sync Results
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="datatable table table-hover table-striped table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">#</th>
                                <th>Student Name</th>
                                <th>Registration No</th>
                                <th>Department</th>
                                <th class="text-center" style="width: 150px;">Score</th>
                                <th class="text-center" style="width: 100px;">Result</th>
                                <th class="text-center" style="width: 100px;">Status</th>
                                <th class="text-center" style="width: 120px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($submissions)): ?>
                                <?php $i = 1; foreach ($submissions as $submission): ?>
                                    <tr>
                                        <td class="text-center"><?= $i++ ?></td>
                                        <td><?= $submission['student_name'] ?></td>
                                        <td><?= $submission['registration_number'] ?></td>
                                        <td><?= $submission['department_name'] ?></td>
                                        <td class="text-center">
                                            <span class="font-weight-semibold"><?= number_format($submission['percentage'], 1) ?>%</span>
                                            <small class="text-muted d-block">(<?= $submission['earned_score'] ?>/<?= $submission['total_score'] ?>)</small>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($submission['status'] != 'completed'): ?>
                                                <span>-</span>
                                            <?php elseif ($submission['percentage'] >= $test['pass_percentage']): ?>
                                                <span class="badge badge-success">Pass</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Fail</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (isset($submission['status'])): ?>
                                                <?php if ($submission['status'] === 'completed'): ?>
                                                    <span class="badge badge-success">Completed</span>
                                                <?php elseif ($submission['status'] === 'in_progress'): ?>
                                                    <span class="badge badge-warning">In Progress</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Not Attempted</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge badge-success">Completed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if (isset($submission['status']) && $submission['status'] === 'completed'): ?>
                                                <a href="<?= base_url($url.'/'.$route.'/'.'student_test_report/'.$course['id'].'/'.$module['id'].'/'.$test['id'].'/'.$submission['student_id']) ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-file-alt mr-1"></i> View Report
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary" disabled>
                                                    <i class="fas fa-file-alt mr-1"></i> View Report
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                                            <p class="mb-0">No submissions found for this test.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Generate Test Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-file-csv fa-3x mb-3 text-primary"></i>
                                <h5 class="card-title">Minimal Report</h5>
                                <p class="card-text">Basic report with essential test results</p>
                                <a href="<?= base_url($url.'/course/export_all_test_results/'.$course['id'].'/'.$module['id'].'/'.$test['id']) ?>" class="btn btn-primary">
                                    <i class="fas fa-download mr-1"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <i class="fas fa-file-csv fa-3x mb-3 text-success"></i>
                                <h5 class="card-title">Detailed Report</h5>
                                <p class="card-text">Comprehensive report with detailed analysis</p>
                                <a href="<?= base_url($url.'/course/export_detailed_test_results/'.$course['id'].'/'.$module['id'].'/'.$test['id']) ?>" class="btn btn-success">
                                    <i class="fas fa-download mr-1"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table {
    margin-bottom: 0;
}
.table th {
    white-space: nowrap;
    background-color: #f8f9fa;
}
.table td {
    vertical-align: middle;
}
.badge {
    padding: 0.5em 0.75em;
    font-weight: 500;
}
.gap-2 {
    gap: 0.5rem;
}
@media (max-width: 768px) {
    .table-responsive {
        border: 0;
    }
    .table td, .table th {
        white-space: normal;
    }
    .card-header .row {
        flex-direction: column;
    }
    .card-header .col-md-6 {
        width: 100%;
    }
    .d-flex.justify-content-md-end {
        justify-content: flex-start !important;
    }
    .badge {
        font-size: 0.875rem;
    }
}
</style>

<script src="<?= base_url('') ?>assets/faculty/libs/datatables/datatables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
// Configure toastr
toastr.options = {
    "closeButton": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "timeOut": "3000"
};

$(document).ready(function() {
    // Initialize DataTable with responsive features
    $('.table').DataTable({
        responsive: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        pageLength: 10,
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search records..."
        }
    });

      // Auto-trigger sync if first-time
    const isFirstTimeSync = <?= $first_time_sync ? 'true' : 'false' ?>;
    const showSync = <?= $show_sync ? 'true' : 'false' ?>;

    if (showSync === true && isFirstTimeSync === true) {
        console.log("First-time sync & course expired. Auto-triggering sync...");
        syncTestResults();
    }
});

function syncTestResults() {
    const syncButton = document.getElementById('syncButton');
    syncButton.disabled = true;
    
    // Make the sync request
    $.ajax({
        url: '<?= base_url($url.'/'.$route.'/'.'sync_test_results/'.$course['id'].'/'.$module['id'].'/'.$test['id']) ?>',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            // Parse response if it's a string
            if (typeof response === 'string') {
                try {
                    response = JSON.parse(response);
                } catch (e) {
                    console.error('Failed to parse response:', e);
                    toastr.error('Invalid response from server');
                    syncButton.disabled = false;
                    return;
                }
            }

            if (response.status === 'success') {
                toastr.success('Sync process started. Results will be updated automatically.');
                // Start polling for updates
                pollSyncStatus();
            } else {
                toastr.error(response.message || 'Failed to start sync process');
                syncButton.disabled = false;
            }
        },
        error: function(xhr, status, error) {
            console.error('Sync request failed:', error);
            toastr.error('Failed to start sync process');
            syncButton.disabled = false;
        }
    });
}

function pollSyncStatus() {
    const syncButton = document.getElementById('syncButton');
    let pollCount = 0;
    const maxPolls = 60; // Poll for up to 5 minutes (5 seconds * 60)
    
    const pollInterval = setInterval(function() {
        pollCount++;
        console.log('Polling attempt:', pollCount); // Debug log
        
        $.ajax({
            url: '<?= base_url($url.'/'.$route.'/'.'get_sync_status/'.$course['id'].'/'.$module['id'].'/'.$test['id']) ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                // Parse response if it's a string
                if (typeof response === 'string') {
                    try {
                        response = JSON.parse(response);
                    } catch (e) {
                        console.error('Failed to parse polling response:', e);
                        return;
                    }
                }

                console.log('Polling response:', response); // Debug log

                if (response.status === 'completed') {
                    clearInterval(pollInterval);
                    toastr.success('Sync completed successfully');
                    syncButton.disabled = false;
                    // Reload the page to show updated results
                    window.location.reload();
                } else if (response.status === 'error') {
                    clearInterval(pollInterval);
                    toastr.error(response.message || 'Sync failed');
                    syncButton.disabled = false;
                } else if (pollCount >= maxPolls) {
                    clearInterval(pollInterval);
                    toastr.warning('Sync is taking longer than expected. Results will be updated when ready.');
                    syncButton.disabled = false;
                }
            },
            error: function(xhr, status, error) {
                console.error('Polling request failed:', error);
                clearInterval(pollInterval);
                toastr.error('Failed to check sync status');
                syncButton.disabled = false;
            }
        });
    }, 5000); // Poll every 5 seconds
}
</script> 