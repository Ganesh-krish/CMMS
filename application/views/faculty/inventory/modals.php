<!-- Add Instrument Modal -->
<div class="modal fade" id="addInstrumentModal" tabindex="-1" aria-labelledby="addInstrumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addInstrumentModalLabel">Add Musical Instrument</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($url . '/inventory/create') ?>" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Instrument Image -->
                    <div class="mb-3">
                        <label for="instrument_image" class="form-label">Instrument Image</label>
                        <input type="file" class="form-control" id="instrument_image" name="instrument_image" accept="image/*">
                        <small class="text-muted">Upload a photo of the instrument (JPG, PNG, GIF)</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Instrument Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required placeholder="Enter instrument name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="availability_status" class="form-label">Availability Status *</label>
                            <select class="form-control" id="availability_status" name="availability_status" required>
                                <option value="available">Available</option>
                                <option value="issued">Issued</option>
                                <option value="maintenance">Under Maintenance</option>
                                <option value="damaged">Damaged</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category *</label>
                            <select class="form-control select2" id="category" name="category" required>
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
                            <label for="brand" class="form-label">Brand</label>
                            <input type="text" class="form-control" id="brand" name="brand">
                        </div>
                    </div>

                    <div class="row">
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
                        <div class="col-md-6 mb-3">
                            <label for="instrument_price" class="form-label">Instrument Price</label>
                            <input type="number" class="form-control" id="instrument_price" name="instrument_price" step="0.01">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="condition_notes" class="form-label">Condition Notes</label>
                        <textarea class="form-control" id="condition_notes" name="condition_notes" rows="2" placeholder="Additional notes about the instrument's condition"></textarea>
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
            <form action="<?= base_url($url . '/inventory/update') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="edit_instrument_id" name="id">
                <div class="modal-body">
                    <!-- Current Instrument Image Display -->
                    <div class="mb-3" id="current_image_container">
                        <label class="form-label">Current Image</label>
                        <div id="current_image_display">
                            <small class="text-muted">No image uploaded</small>
                        </div>
                    </div>

                    <!-- Instrument Image Upload -->
                    <div class="mb-3">
                        <label for="edit_instrument_image" class="form-label">Update Instrument Image</label>
                        <input type="file" class="form-control" id="edit_instrument_image" name="instrument_image" accept="image/*">
                        <small class="text-muted">Leave empty to keep current image (JPG, PNG, GIF)</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_name" class="form-label">Instrument Name *</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required placeholder="Enter instrument name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_availability_status" class="form-label">Availability Status *</label>
                            <select class="form-control" id="edit_availability_status" name="availability_status" required>
                                <option value="available">Available</option>
                                <option value="issued">Issued</option>
                                <option value="maintenance">Under Maintenance</option>
                                <option value="damaged">Damaged</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_category" class="form-label">Category *</label>
                            <select class="form-control select2" id="edit_category" name="category" required>
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
                            <label for="edit_brand" class="form-label">Brand</label>
                            <input type="text" class="form-control" id="edit_brand" name="brand">
                        </div>
                    </div>

                    <div class="row">
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
                        <div class="col-md-6 mb-3">
                            <label for="edit_instrument_price" class="form-label">Instrument Price</label>
                            <input type="number" class="form-control" id="edit_instrument_price" name="instrument_price" step="0.01">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_condition_notes" class="form-label">Condition Notes</label>
                        <textarea class="form-control" id="edit_condition_notes" name="condition_notes" rows="2" placeholder="Additional notes about the instrument's condition"></textarea>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteInstrumentModal" tabindex="-1" role="dialog" aria-labelledby="deleteInstrumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteInstrumentModalLabel">
                    <i class="feather icon-alert-triangle"></i> Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="feather icon-trash-2 text-danger" style="font-size: 3rem;"></i>
                    </div>
                    <h5 class="mb-3">Are you sure you want to delete this instrument?</h5>
                    <p class="text-muted mb-3" id="deleteInstrumentName"></p>
                    <div class="alert alert-warning">
                        <i class="feather icon-alert-circle"></i>
                        <strong>Warning:</strong> This action cannot be undone. The instrument will be permanently removed from the system.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="feather icon-x"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="feather icon-trash"></i> Delete Instrument
                </button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Initialize Select2 for category dropdowns only
    $('#category, #edit_category').select2({
        placeholder: "Select Category",
        allowClear: true
    });

    // Set minimum date for expected return date
    const today = new Date().toISOString().split('T')[0];
    if (document.getElementById('expected_return_date')) {
        document.getElementById('expected_return_date').setAttribute('min', today);
    }
});
</script>