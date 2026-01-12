<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Certificate Requests</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/courses'); ?>">Courses</a></li>
                <li class="breadcrumb-item active">Certificate Requests</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Pending Requests Section -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="feather icon-clock"></i> Pending Requests
                            <span class="badge badge-light ml-2"><?php echo count($pending_requests); ?></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($pending_requests)): ?>
                            <div class="text-center py-5">
                                <i class="feather icon-check-circle" style="font-size: 4rem; color: #28a745;"></i>
                                <h4 class="mt-3 text-success">No Pending Requests</h4>
                                <p class="text-muted">All certificate requests have been processed.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Course</th>
                                            <th>Progress</th>
                                            <th>Requested Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pending_requests as $request): ?>
                                            <tr>
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($request['student_name']); ?></strong><br>
                                                        <small class="text-muted">
                                                            <?php echo htmlspecialchars($request['student_email']); ?><br>
                                                            Roll No: <?php echo htmlspecialchars($request['roll_no'] ?? 'N/A'); ?>
                                                        </small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($request['course_name']); ?></strong><br>
                                                        <small class="text-muted">Code: <?php echo htmlspecialchars($request['course_code']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="progress" style="width: 100px; height: 20px;">
                                                        <div class="progress-bar bg-success" role="progressbar"
                                                             style="width: <?php echo htmlspecialchars($request['progress_percentage'] ?? 100); ?>%"
                                                             aria-valuenow="<?php echo htmlspecialchars($request['progress_percentage'] ?? 100); ?>"
                                                             aria-valuemin="0" aria-valuemax="100">
                                                            <?php echo htmlspecialchars($request['progress_percentage'] ?? 100); ?>%
                                                        </div>
                                                    </div>
                                                    <small class="text-muted">Enrolled: <?php echo date('M d, Y', strtotime($request['enrolled_at'])); ?></small>
                                                </td>
                                                <td>
                                                    <?php echo date('M d, Y H:i', strtotime($request['requested_at'])); ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-success" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#approveModal<?php echo $request['id']; ?>">
                                                            <i class="feather icon-check"></i> Approve
                                                        </button>
                                                        <button type="button" class="btn btn-danger" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#rejectModal<?php echo $request['id']; ?>">
                                                            <i class="feather icon-x"></i> Reject
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Approve Modal -->
                                            <div class="modal fade" id="approveModal<?php echo $request['id']; ?>" tabindex="-1" aria-labelledby="approveModalLabel<?php echo $request['id']; ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-success text-white">
                                                            <h5 class="modal-title" id="approveModalLabel<?php echo $request['id']; ?>">
                                                                <i class="feather icon-check-circle"></i> Approve Certificate Request
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="<?php echo base_url($url.'/courses/approve_certificate_request/'.$request['id']); ?>" method="post">
                                                            <div class="modal-body">
                                                                <div class="alert alert-info">
                                                                    <strong>Student:</strong> <?php echo htmlspecialchars($request['student_name']); ?><br>
                                                                    <strong>Course:</strong> <?php echo htmlspecialchars($request['course_name']); ?><br>
                                                                    <strong>Progress:</strong> <?php echo htmlspecialchars($request['progress_percentage'] ?? 100); ?>%
                                                                </div>
                                                                <p>Are you sure you want to approve this certificate request? A certificate will be automatically generated upon approval.</p>
                                                                <div class="form-group">
                                                                    <label for="notes<?php echo $request['id']; ?>">Notes (Optional)</label>
                                                                    <textarea class="form-control" id="notes<?php echo $request['id']; ?>" name="notes" rows="3" placeholder="Add any notes about this approval..."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-success">
                                                                    <i class="feather icon-check"></i> Approve & Generate Certificate
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Reject Modal -->
                                            <div class="modal fade" id="rejectModal<?php echo $request['id']; ?>" tabindex="-1" aria-labelledby="rejectModalLabel<?php echo $request['id']; ?>" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title" id="rejectModalLabel<?php echo $request['id']; ?>">
                                                                <i class="feather icon-x-circle"></i> Reject Certificate Request
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="<?php echo base_url($url.'/courses/reject_certificate_request/'.$request['id']); ?>" method="post">
                                                            <div class="modal-body">
                                                                <div class="alert alert-warning">
                                                                    <strong>Student:</strong> <?php echo htmlspecialchars($request['student_name']); ?><br>
                                                                    <strong>Course:</strong> <?php echo htmlspecialchars($request['course_name']); ?>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="rejection_reason<?php echo $request['id']; ?>">Rejection Reason <span class="text-danger">*</span></label>
                                                                    <textarea class="form-control" id="rejection_reason<?php echo $request['id']; ?>" name="rejection_reason" rows="4" required placeholder="Please provide a reason for rejection. This will be shown to the student."></textarea>
                                                                    <small class="form-text text-muted">This reason will be visible to the student.</small>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="reject_notes<?php echo $request['id']; ?>">Internal Notes (Optional)</label>
                                                                    <textarea class="form-control" id="reject_notes<?php echo $request['id']; ?>" name="notes" rows="2" placeholder="Internal notes (not visible to student)..."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-danger">
                                                                    <i class="feather icon-x"></i> Reject Request
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- All Requests History -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="feather icon-list"></i> All Requests History
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($all_requests)): ?>
                            <div class="text-center py-5">
                                <i class="feather icon-inbox" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3">No Certificate Requests</h4>
                                <p class="text-muted">No certificate requests have been submitted yet.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover datatable">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Course</th>
                                            <th>Status</th>
                                            <th>Requested Date</th>
                                            <th>Reviewed By</th>
                                            <th>Reviewed Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($all_requests as $request): ?>
                                            <tr>
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($request['student_name']); ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($request['student_email']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($request['course_name']); ?></strong><br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($request['course_code']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status_class = 'secondary';
                                                    $status_icon = 'clock';
                                                    if ($request['status'] === 'approved') {
                                                        $status_class = 'success';
                                                        $status_icon = 'check-circle';
                                                    } elseif ($request['status'] === 'rejected') {
                                                        $status_class = 'danger';
                                                        $status_icon = 'x-circle';
                                                    }
                                                    ?>
                                                    <span class="badge badge-<?php echo $status_class; ?>">
                                                        <i class="feather icon-<?php echo $status_icon; ?>"></i>
                                                        <?php echo ucfirst($request['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo date('M d, Y H:i', strtotime($request['requested_at'])); ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($request['reviewer_name'] ?? 'N/A'); ?>
                                                </td>
                                                <td>
                                                    <?php echo $request['reviewed_at'] ? date('M d, Y H:i', strtotime($request['reviewed_at'])) : 'N/A'; ?>
                                                </td>
                                                <td>
                                                    <?php if ($request['status'] === 'rejected' && !empty($request['rejection_reason'])): ?>
                                                        <button type="button" class="btn btn-sm btn-outline-info" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#reasonModal<?php echo $request['id']; ?>">
                                                            <i class="feather icon-info"></i> View Reason
                                                        </button>
                                                        
                                                        <!-- Rejection Reason Modal -->
                                                        <div class="modal fade" id="reasonModal<?php echo $request['id']; ?>" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Rejection Reason</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <p><strong>Reason:</strong></p>
                                                                        <p><?php echo nl2br(htmlspecialchars($request['rejection_reason'])); ?></p>
                                                                        <?php if (!empty($request['notes'])): ?>
                                                                            <hr>
                                                                            <p><strong>Internal Notes:</strong></p>
                                                                            <p class="text-muted"><?php echo nl2br(htmlspecialchars($request['notes'])); ?></p>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize DataTables if available
    if ($.fn.DataTable) {
        $('.datatable').DataTable({
            "order": [[3, "desc"]], // Sort by requested date descending
            "pageLength": 25
        });
    }
});
</script>
