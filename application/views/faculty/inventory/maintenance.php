<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Maintenance Log</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/inventory'); ?>">Inventory</a></li>
                <li class="breadcrumb-item">Maintenance Log</li>
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
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Type</label>
                        <select class="form-control" id="typeFilter">
                            <option value="">All Types</option>
                            <option value="repair">Repair</option>
                            <option value="cleaning">Cleaning</option>
                            <option value="tuning">Tuning</option>
                            <option value="replacement">Part Replacement</option>
                            <option value="inspection">Inspection</option>
                            <option value="other">Other</option>
                        </select>
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

        <!-- Maintenance Table -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Maintenance Records</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="maintenanceTable" class="datatable table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Instrument</th>
                                <th>Type</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Scheduled Date</th>
                                <th>Completed Date</th>
                                <th>Assigned To</th>
                                <th>Cost</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($maintenance_logs)) {
                                $i = 1;
                                foreach ($maintenance_logs as $log) {
                                    // Get instrument details
                                    $instrument = $this->db_model->get_row(TABLE_INSTRUMENTS, ["id" => $log['instrument_id']]);
                                    ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $instrument ? $instrument['name'] . ' (' . $instrument['serial_no'] . ')' : 'Unknown'; ?></td>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $log['maintenance_type'])); ?></td>
                                        <td>
                                            <span class="badge badge-<?php
                                                echo $log['priority'] == 'urgent' ? 'danger' :
                                                     ($log['priority'] == 'high' ? 'warning' :
                                                     ($log['priority'] == 'medium' ? 'info' : 'secondary'));
                                            ?>">
                                                <?php echo ucfirst($log['priority']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php
                                                echo $log['status'] == 'completed' ? 'success' :
                                                     ($log['status'] == 'in_progress' ? 'primary' :
                                                     ($log['status'] == 'pending' ? 'warning' : 'secondary'));
                                            ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $log['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $log['scheduled_date'] ? date('d M Y', strtotime($log['scheduled_date'])) : '-'; ?></td>
                                        <td><?php echo $log['completed_at'] ? date('d M Y', strtotime($log['completed_at'])) : '-'; ?></td>
                                        <td><?php echo $log['assigned_to'] ?: '-'; ?></td>
                                        <td><?php echo $log['estimated_cost'] ? '₹' . number_format($log['estimated_cost'], 2) : '-'; ?></td>
                                        <td class="d-flex gap-1" style="flex-wrap: wrap;">
                                            <button class="btn btn-sm btn-info" onclick="viewMaintenanceDetails(<?php echo $log['id']; ?>)">
                                                <i class="feather icon-eye"></i> Details
                                            </button>
                                            <?php if ($log['status'] != 'completed'): ?>
                                            <button class="btn btn-sm btn-success" onclick="updateMaintenanceStatus(<?php echo $log['id']; ?>, 'completed')">
                                                <i class="feather icon-check"></i> Complete
                                            </button>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-warning" onclick="editMaintenance(<?php echo $log['id']; ?>)">
                                                <i class="feather icon-edit"></i> Edit
                                            </button>
                                        </td>
                                    </tr>
                            <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="10" class="text-center">No maintenance records found</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Maintenance Details Modal -->
<div class="modal fade" id="maintenanceDetailsModal" tabindex="-1" aria-labelledby="maintenanceDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="maintenanceDetailsModalLabel">Maintenance Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="maintenanceDetailsContent">
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
    const type = document.getElementById('typeFilter').value;

    const url = new URL(window.location);
    if (status) url.searchParams.set('status', status);
    if (type) url.searchParams.set('type', type);

    window.location.href = url.toString();
}

function viewMaintenanceDetails(logId) {
    // For now, show a simple alert. In a real implementation, this would load detailed information
    alert('Detailed maintenance information would be displayed here for Log ID: ' + logId);
}

function updateMaintenanceStatus(logId, status) {
    const notes = prompt('Add completion notes (optional):');
    if (notes !== null) { // User didn't cancel
        const formData = new FormData();
        formData.append('maintenance_id', logId);
        formData.append('status', status);
        if (notes) formData.append('notes', notes);

        // This would need to be implemented with AJAX
        alert('Status update functionality would be implemented here.');
    }
}

function editMaintenance(logId) {
    // For now, show a simple alert. In a real implementation, this would open an edit modal
    alert('Edit maintenance functionality would be implemented here for Log ID: ' + logId);
}

// Initialize DataTable
$(document).ready(function() {
    $('#maintenanceTable').DataTable({
        "pageLength": 25,
        "order": [[ 5, "asc" ]] // Order by scheduled date ascending
    });
});
</script>