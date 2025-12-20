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
                                        <td><?php echo $issue['instrument_name'] ?: 'Unknown'; ?></td>
                                        <td><?php echo $issue['serial_no'] ?: '-'; ?></td>
                                        <td><?php echo $issue['issued_to_name'] ?: 'Unknown'; ?> (<?php echo $issue['issued_to_identifier'] ?: '-'; ?>)</td>
                                        <td><?php echo date('d M Y', strtotime($issue['issue_date'])); ?></td>
                                        <td><?php echo $issue['expected_return_date'] ? date('d M Y', strtotime($issue['expected_return_date'])) : '-'; ?></td>
                                        <td><?php echo $issue['actual_return_date'] ? date('d M Y', strtotime($issue['actual_return_date'])) : '-'; ?></td>
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
    // Load issue details via AJAX
    fetch('<?php echo base_url($url.'/api/get_issue_details'); ?>?issue_id=' + issueId)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                populateIssueDetailsModal(data.data);
                $('#issueDetailsModal').modal('show');
            } else {
                alert('Error loading issue details: ' + data.data);
            }
        })
        .catch(error => {
            console.error('Error loading issue details:', error);
            alert('Error loading issue details. Please try again.');
        });
}

function populateIssueDetailsModal(issue) {
    const content = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Instrument Information</h6>
                <table class="table table-sm">
                    <tr><th>Name:</th><td>${issue.instrument_name || 'N/A'}</td></tr>
                    <tr><th>Serial No:</th><td>${issue.serial_no || 'N/A'}</td></tr>
                    <tr><th>Model:</th><td>${issue.model || 'N/A'}</td></tr>
                    <tr><th>Brand:</th><td>${issue.brand || 'N/A'}</td></tr>
                    <tr><th>Condition:</th><td>${issue.condition || 'N/A'}</td></tr>
                    <tr><th>Price:</th><td>${issue.instrument_price ? '₹' + parseFloat(issue.instrument_price).toFixed(2) : 'N/A'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">Issue Information</h6>
                <table class="table table-sm">
                    <tr><th>Issue ID:</th><td>${issue.id}</td></tr>
                    <tr><th>Status:</th><td><span class="badge badge-${issue.status === 'issued' ? 'warning' : issue.status === 'returned' ? 'success' : 'secondary'}">${issue.status}</span></td></tr>
                    <tr><th>Issued To:</th><td>${issue.issued_to_name || 'N/A'} (${issue.issued_to_type})</td></tr>
                    <tr><th>Identifier:</th><td>${issue.issued_to_identifier || 'N/A'}</td></tr>
                    <tr><th>Email:</th><td>${issue.issued_to_email || 'N/A'}</td></tr>
                    <tr><th>Issued By:</th><td>${issue.issued_by_name || 'N/A'}</td></tr>
                    <tr><th>Issue Date:</th><td>${issue.issue_date ? new Date(issue.issue_date).toLocaleDateString() : 'N/A'}</td></tr>
                    <tr><th>Expected Return:</th><td>${issue.expected_return_date ? new Date(issue.expected_return_date).toLocaleDateString() : 'N/A'}</td></tr>
                    <tr><th>Actual Return:</th><td>${issue.actual_return_date ? new Date(issue.actual_return_date).toLocaleDateString() : 'N/A'}</td></tr>
                </table>
            </div>
        </div>
        ${issue.condition_on_issue || issue.condition_on_return || issue.notes ? `
        <div class="row mt-3">
            <div class="col-12">
                <h6 class="text-primary">Additional Information</h6>
                ${issue.condition_on_issue ? `<p><strong>Condition on Issue:</strong> ${issue.condition_on_issue}</p>` : ''}
                ${issue.condition_on_return ? `<p><strong>Condition on Return:</strong> ${issue.condition_on_return}</p>` : ''}
                ${issue.notes ? `<p><strong>Notes:</strong> ${issue.notes}</p>` : ''}
            </div>
        </div>
        ` : ''}
    `;

    document.getElementById('issueDetailsContent').innerHTML = content;
}

// Global variable to store current issue ID for return
let currentReturnIssueId = null;
let currentReturnButton = null;

function markAsReturned(issueId) {
    // Find the row data to show instrument info
    const row = event.target.closest('tr');
    const instrumentName = row.cells[1].textContent.trim();
    const serialNo = row.cells[2].textContent.trim() || 'N/A';
    const issuedTo = row.cells[3].textContent.trim();

    // Store the issue ID and button reference
    currentReturnIssueId = issueId;
    currentReturnButton = event.target.closest('button');

    // Populate modal with instrument info
    document.getElementById('returnInstrumentInfo').innerHTML = `
        <strong>Instrument:</strong> ${instrumentName}<br>
        <strong>Serial No:</strong> ${serialNo}<br>
        <strong>Issued To:</strong> ${issuedTo}
    `;

    // Show the modal
    $('#returnConfirmModal').modal('show');
}

function confirmReturn() {
    if (!currentReturnIssueId || !currentReturnButton) return;

    // Hide the modal
    $('#returnConfirmModal').modal('hide');

    // Show loading state
    const originalText = currentReturnButton.innerHTML;
    currentReturnButton.innerHTML = '<i class="feather icon-loader"></i> Processing...';
    currentReturnButton.disabled = true;

    // Create return data
    const formData = new FormData();
    formData.append('issue_id', currentReturnIssueId);
    formData.append('return_date', new Date().toISOString().split('T')[0]);
    formData.append('condition_on_return', 'good');
    formData.append('notes', 'Quick return via issues page');

    // Make API call
    fetch('<?php echo base_url($url.'/api/return_instrument'); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Show success message
            showAlert('success', 'Instrument returned successfully!');

            // Reload the page to show updated status after a short delay
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('error', 'Error: ' + data.data);
            // Restore button
            currentReturnButton.innerHTML = originalText;
            currentReturnButton.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error returning instrument:', error);
        showAlert('error', 'An error occurred while processing the return. Please try again.');
        // Restore button
        currentReturnButton.innerHTML = originalText;
        currentReturnButton.disabled = false;
    });
}

// Helper function to show alerts
function showAlert(type, message) {
    // Remove any existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());

    // Create new alert
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        ${message}
    `;

    // Insert at the top of the page content
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alertDiv, container.firstChild);

    // Auto-hide after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            $(alertDiv).alert('close');
        }
    }, 5000);
}

// Initialize DataTable and modal event listeners
$(document).ready(function() {
    // Initialize DataTable
    $('#issuesTable').DataTable({
        "pageLength": 25,
        "order": [[ 4, "desc" ]] // Order by issue date descending
    });

    // Initialize modal event listeners
    $('#confirmReturnBtn').on('click', confirmReturn);

    // Reset variables when modal is hidden
    $('#returnConfirmModal').on('hidden.bs.modal', function() {
        currentReturnIssueId = null;
        currentReturnButton = null;
    });
});
</script>