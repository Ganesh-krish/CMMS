<!-- Add Instrument Modal -->
<div class="modal fade" id="addInstrumentModal" tabindex="-1" aria-labelledby="addInstrumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addInstrumentModalLabel">Add Musical Instrument</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($url . '/inventory/create') ?>" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Instrument Name *</label>
                            <select class="form-control" id="name" name="name" required>
                                <option value="">Select Instrument</option>
                                <?php
                                $common_instruments = [
                                    'guitar' => 'Guitar',
                                    'piano' => 'Piano',
                                    'violin' => 'Violin',
                                    'tabla' => 'Tabla',
                                    'drums' => 'Drums',
                                    'flute' => 'Flute',
                                    'saxophone' => 'Saxophone',
                                    'trumpet' => 'Trumpet',
                                    'keyboard' => 'Keyboard',
                                    'harmonium' => 'Harmonium',
                                    'sitar' => 'Sitar',
                                    'tambourine' => 'Tambourine',
                                    'bongos' => 'Bongos',
                                    'ukulele' => 'Ukulele',
                                    'cello' => 'Cello'
                                ];
                                foreach ($common_instruments as $key => $name): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                                <?php endforeach; ?>
                                <option value="other">Other (specify below)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="custom_name" class="form-label">Custom Name (if Other)</label>
                            <input type="text" class="form-control" id="custom_name" name="custom_name" placeholder="Enter custom instrument name">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category *</label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="string">String Instruments</option>
                                <option value="percussion">Percussion Instruments</option>
                                <option value="wind">Wind Instruments</option>
                                <option value="keyboard">Keyboard Instruments</option>
                                <option value="electronic">Electronic Instruments</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="serial_no" class="form-label">Serial Number *</label>
                            <input type="text" class="form-control" id="serial_no" name="serial_no" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="model" name="model">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="condition" class="form-label">Condition *</label>
                            <select class="form-control" id="condition" name="condition" required>
                                <option value="excellent">Excellent</option>
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                                <option value="damaged">Damaged</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="purchase_date" class="form-label">Purchase Date</label>
                            <input type="date" class="form-control" id="purchase_date" name="purchase_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="purchase_cost" class="form-label">Purchase Cost</label>
                            <input type="number" class="form-control" id="purchase_cost" name="purchase_cost" step="0.01">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Instrument</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Instrument Modal -->
<div class="modal fade" id="editInstrumentModal" tabindex="-1" aria-labelledby="editInstrumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editInstrumentModalLabel">Edit Musical Instrument</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($url . '/inventory/update') ?>" method="POST">
                <input type="hidden" id="edit_instrument_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_name" class="form-label">Instrument Name *</label>
                            <select class="form-control" id="edit_name" name="name" required>
                                <option value="">Select Instrument</option>
                                <?php foreach ($common_instruments as $key => $name): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
                                <?php endforeach; ?>
                                <option value="other">Other (specify below)</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_custom_name" class="form-label">Custom Name (if Other)</label>
                            <input type="text" class="form-control" id="edit_custom_name" name="custom_name">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_category" class="form-label">Category *</label>
                            <select class="form-control" id="edit_category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="string">String Instruments</option>
                                <option value="percussion">Percussion Instruments</option>
                                <option value="wind">Wind Instruments</option>
                                <option value="keyboard">Keyboard Instruments</option>
                                <option value="electronic">Electronic Instruments</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_serial_no" class="form-label">Serial Number *</label>
                            <input type="text" class="form-control" id="edit_serial_no" name="serial_no" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_model" class="form-label">Model</label>
                            <input type="text" class="form-control" id="edit_model" name="model">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_condition" class="form-label">Condition *</label>
                            <select class="form-control" id="edit_condition" name="condition" required>
                                <option value="excellent">Excellent</option>
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                                <option value="damaged">Damaged</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_purchase_date" class="form-label">Purchase Date</label>
                            <input type="date" class="form-control" id="edit_purchase_date" name="purchase_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_purchase_cost" class="form-label">Purchase Cost</label>
                            <input type="number" class="form-control" id="edit_purchase_cost" name="purchase_cost" step="0.01">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Instrument</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Issue Instrument Modal -->
<div class="modal fade" id="issueInstrumentModal" tabindex="-1" aria-labelledby="issueInstrumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="issueInstrumentModalLabel">Issue Instrument</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($url . '/inventory/issue') ?>" method="POST">
                <input type="hidden" id="issue_instrument_id" name="instrument_id">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Instrument:</strong> <span id="issue_instrument_name"></span>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="issued_to_type" class="form-label">Issue To Type *</label>
                            <select class="form-control" id="issued_to_type" name="issued_to_type" required onchange="toggleIssueFields()">
                                <option value="student">Student</option>
                                <option value="staff">Staff</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="issued_to_id" class="form-label">Issue To ID *</label>
                            <input type="text" class="form-control" id="issued_to_id" name="issued_to_id" required placeholder="Student/Staff ID or Name">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="expected_return_date" class="form-label">Expected Return Date *</label>
                            <input type="date" class="form-control" id="expected_return_date" name="expected_return_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="issue_date" class="form-label">Issue Date</label>
                            <input type="date" class="form-control" id="issue_date" name="issue_date" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="purpose" class="form-label">Purpose/Reason</label>
                        <textarea class="form-control" id="purpose" name="purpose" rows="2" placeholder="e.g., Practice session, Performance, etc."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Issue Instrument</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Return Instrument Modal -->
<div class="modal fade" id="returnInstrumentModal" tabindex="-1" aria-labelledby="returnInstrumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="returnInstrumentModalLabel">Return Instrument</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($url . '/inventory/return') ?>" method="POST">
                <input type="hidden" id="return_instrument_id" name="instrument_id">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Instrument:</strong> <span id="return_instrument_name"></span>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="return_date" class="form-label">Return Date *</label>
                            <input type="date" class="form-control" id="return_date" name="return_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="condition_on_return" class="form-label">Condition on Return *</label>
                            <select class="form-control" id="condition_on_return" name="condition_on_return" required>
                                <option value="excellent">Excellent</option>
                                <option value="good">Good</option>
                                <option value="fair">Fair</option>
                                <option value="poor">Poor</option>
                                <option value="damaged">Damaged</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="return_notes" class="form-label">Return Notes</label>
                        <textarea class="form-control" id="return_notes" name="notes" rows="3" placeholder="Any notes about the return condition or usage"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Return Instrument</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Maintenance Modal -->
<div class="modal fade" id="maintenanceModal" tabindex="-1" aria-labelledby="maintenanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="maintenanceModalLabel">Log Maintenance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($url . '/inventory/maintenance') ?>" method="POST">
                <input type="hidden" id="maintenance_instrument_id" name="instrument_id">
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Instrument:</strong> <span id="maintenance_instrument_name"></span>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="maintenance_type" class="form-label">Maintenance Type *</label>
                            <select class="form-control" id="maintenance_type" name="maintenance_type" required>
                                <option value="">Select Type</option>
                                <option value="repair">Repair</option>
                                <option value="cleaning">Cleaning</option>
                                <option value="tuning">Tuning</option>
                                <option value="replacement">Part Replacement</option>
                                <option value="inspection">Inspection</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="priority" class="form-label">Priority *</label>
                            <select class="form-control" id="priority" name="priority" required>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                                <option value="urgent">Urgent</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="scheduled_date" class="form-label">Scheduled Date</label>
                            <input type="date" class="form-control" id="scheduled_date" name="scheduled_date">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="estimated_cost" class="form-label">Estimated Cost</label>
                            <input type="number" class="form-control" id="estimated_cost" name="estimated_cost" step="0.01">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="maintenance_description" class="form-label">Description *</label>
                        <textarea class="form-control" id="maintenance_description" name="description" rows="3" required placeholder="Describe the maintenance needed"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assigned To</label>
                        <input type="text" class="form-control" id="assigned_to" name="assigned_to" placeholder="Technician or maintenance person">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Log Maintenance</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleIssueFields() {
    // This function can be extended to dynamically load student/staff lists
    // For now, it's a placeholder for future enhancement
}

$(document).ready(function() {
    // Set minimum date for expected return date
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('expected_return_date').setAttribute('min', today);
});
</script>