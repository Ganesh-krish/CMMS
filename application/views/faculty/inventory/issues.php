<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Instrument Issue/Return Log</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/inventory'); ?>">Inventory</a></li>
                <li class="breadcrumb-item">Issue/Return Log</li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php   } ?>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="form-row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="issued">Currently Issued</option>
                            <option value="returned">Returned</option>
                            <option value="overdue">Overdue</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Search Issued To</label>
                        <input type="text" class="form-control" id="issuedToFilter" placeholder="Student/Staff name or ID">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-none d-md-block">&nbsp;</label>
                        <button class="btn btn-primary btn-block" onclick="applyFilters()">Apply Filters</button>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-none d-md-block">&nbsp;</label>
                        <a href="<?php echo base_url($url.'/inventory'); ?>" class="btn btn-secondary btn-block">Back to Inventory</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Issues Table -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Issue/Return History</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="issuesTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Instrument</th>
                                <th>Serial No</th>
                                <th>Issued To</th>
                                <th>Issue Date</th>
                                <th>Expected Return</th>
                                <th>Actual Return</th>
                                <th>Status</th>
                                <th>Condition</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($issues)) {
                                $i = 1;
                                foreach ($issues as $issue) {
                                    // Get instrument details
                                    $instrument = $this->db_model->get_row(TABLE_INSTRUMENTS, ["id" => $issue['instrument_id']]);
                                    $is_overdue = ($issue['status'] == 'issued' && strtotime($issue['expected_return_date']) < time());
                                    ?>
                                    <tr class="<?php echo $is_overdue ? 'table-warning' : ''; ?>">
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $instrument ? $instrument['name'] : 'Unknown'; ?></td>
                                        <td><?php echo $instrument ? $instrument['serial_no'] : '-'; ?></td>
                                        <td><?php echo $issue['issued_to']; ?></td>
                                        <td><?php echo date('d M Y', strtotime($issue['issue_date'])); ?></td>
                                        <td><?php echo date('d M Y', strtotime($issue['expected_return_date'])); ?></td>
                                        <td><?php echo $issue['return_date'] ? date('d M Y', strtotime($issue['return_date'])) : '-'; ?></td>
                                        <td>
                                            <span class="badge badge-<?php
                                                echo $issue['status'] == 'issued' ? 'warning' :
                                                     ($issue['status'] == 'returned' ? 'success' : 'secondary');
                                            ?>">
                                                <?php echo ucfirst($issue['status']); ?>
                                                <?php if ($is_overdue) echo ' (Overdue)'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo isset($issue['condition_on_return']) ? ucfirst($issue['condition_on_return']) : '-'; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" onclick="viewIssueDetails(<?php echo $issue['id']; ?>)">
                                                <i class="feather icon-eye"></i> Details
                                            </button>
                                            <?php if ($issue['status'] == 'issued' && !$is_overdue): ?>
                                            <button class="btn btn-sm btn-success" onclick="markAsReturned(<?php echo $issue['id']; ?>)">
                                                <i class="feather icon-check"></i> Quick Return
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                            <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="10" class="text-center">No issue/return records found</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Issue Details Modal -->
<div class="modal fade" id="issueDetailsModal" tabindex="-1" aria-labelledby="issueDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="issueDetailsModalLabel">Issue/Return Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="issueDetailsContent">
                <!-- Details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function applyFilters() {
    const status = document.getElementById('statusFilter').value;
    const issuedTo = document.getElementById('issuedToFilter').value;

    const url = new URL(window.location);
    if (status) url.searchParams.set('status', status);
    if (issuedTo) url.searchParams.set('issued_to', issuedTo);

    window.location.href = url.toString();
}

function viewIssueDetails(issueId) {
    // For now, show a simple alert. In a real implementation, this would load detailed information
    alert('Detailed issue information would be displayed here for Issue ID: ' + issueId);
}

function markAsReturned(issueId) {
    if (confirm('Mark this instrument as returned?')) {
        // Create a simple return action
        const formData = new FormData();
        formData.append('instrument_id', issueId); // This would need to be the instrument ID, not issue ID
        formData.append('return_date', new Date().toISOString().split('T')[0]);
        formData.append('condition_on_return', 'good');
        formData.append('notes', 'Quick return');

        // This would need to be implemented properly on the backend
        alert('Quick return functionality would be implemented here.');
    }
}

// Initialize DataTable
$(document).ready(function() {
    $('#issuesTable').DataTable({
        "pageLength": 25,
        "order": [[ 4, "desc" ]] // Order by issue date descending
    });
});
</script>