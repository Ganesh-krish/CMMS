<style>
/* Custom styles for instrument popover */
.popover {
    max-width: 420px !important;
    z-index: 1060;
}

.popover-header {
    background-color: #007bff;
    color: white;
    font-weight: 600;
}

.instrument-popover {
    font-size: 0.875rem;
}

.instrument-popover .no-gutters {
    margin-right: 0;
    margin-left: 0;
}

.instrument-popover .condition-notes {
    min-height: 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.instrument-popover .description {
    min-height: 60px;
    line-height: 1.4;
}

/* Table row hover effect */
#instrumentsTable tbody tr:hover {
    background-color: #e3f2fd !important;
    cursor: pointer;
    transition: background-color 0.3s ease;
    transform: scale(1.01);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Prevent text selection on hover */
#instrumentsTable tbody tr:hover * {
    user-select: none;
}

/* Custom popover arrow */
.popover.right .arrow {
    left: -11px;
}

.popover.right .arrow::after {
    border-right-color: #fff;
}
</style>

<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Musical Instrument Inventory</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item">Inventory</li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php   } ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-primary"><?php echo $stats['total_instruments']; ?></h4>
                        <p class="mb-0">Total Instruments</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-success"><?php echo $stats['available_instruments']; ?></h4>
                        <p class="mb-0">Available</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-warning"><?php echo $stats['issued_instruments']; ?></h4>
                        <p class="mb-0">Currently Issued</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="text-danger"><?php echo $stats['maintenance_instruments']; ?></h4>
                        <p class="mb-0">Under Maintenance</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <button class="btn btn-primary btn-block mb-2" data-bs-toggle="modal" data-bs-target="#addInstrumentModal">
                                    <i class="feather icon-plus"></i> Add Instrument
                                </button>
                            </div>
                            <div class="col-md-3">
                                <a href="<?php echo base_url($url.'/inventory/categories'); ?>" class="btn btn-info btn-block mb-2">
                                    <i class="feather icon-tag"></i> Add Categories
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="<?php echo base_url($url.'/inventory/issues'); ?>" class="btn btn-warning btn-block mb-2">
                                    <i class="feather icon-repeat"></i> Issue/Return Log
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="form-row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="statusFilter">
                            <option value="">All Status</option>
                            <option value="<?php echo INSTRUMENT_STATUS_AVAILABLE; ?>">Available</option>
                            <option value="<?php echo INSTRUMENT_STATUS_ISSUED; ?>">Issued</option>
                            <option value="<?php echo INSTRUMENT_STATUS_MAINTENANCE; ?>">Under Maintenance</option>
                            <option value="<?php echo INSTRUMENT_STATUS_DAMAGED; ?>">Damaged</option>
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
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" id="searchFilter" placeholder="Instrument name or serial...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-none d-md-block">&nbsp;</label>
                        <button class="btn btn-primary btn-block" onclick="applyFilters()">Apply Filters</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instruments Table -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Instrument Inventory</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="instrumentsTable" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Serial No</th>
                                <th>Model</th>
                                <th>Brand</th>
                                <th>Condition</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($instruments)) {
                                $i = 1;
                                foreach ($instruments as $instrument) { ?>
                                    <tr class="instrument-row" data-instrument-id="<?php echo $instrument['id']; ?>">
                                        <td><?php echo $i++; ?></td>
                                        <td>
                                            <?php if (!empty($instrument['instrument_image'])): ?>
                                                <img src="<?php echo base_url($instrument['instrument_image']); ?>" class="img-thumbnail" style="max-width: 50px; max-height: 50px;" alt="Instrument Image">
                                            <?php else: ?>
                                                <span class="text-muted">No Image</span>
                                            <?php endif; ?>
                                            <span class="instrument-image-src d-none"><?php echo !empty($instrument['instrument_image']) ? base_url($instrument['instrument_image']) : ''; ?></span>
                                            <span class="instrument-condition-notes d-none"><?php
                                                $notes = $instrument['condition_notes'] ?? '';
                                                echo htmlspecialchars(strlen($notes) > 100 ? substr($notes, 0, 100) . '...' : $notes);
                                            ?></span>
                                            <span class="instrument-description d-none"><?php
                                                $desc = $instrument['description'] ?? '';
                                                echo htmlspecialchars(strlen($desc) > 150 ? substr($desc, 0, 150) . '...' : $desc);
                                            ?></span>
                                        </td>
                                        <td><?php echo $instrument['name']; ?></td>
                                        <td><?php echo $categories[$instrument['category']] ?? $instrument['category']; ?></td>
                                        <td><?php echo $instrument['serial_no']; ?></td>
                                        <td><?php echo $instrument['model'] ?? 'N/A'; ?></td>
                                        <td><?php echo $instrument['brand'] ?? 'N/A'; ?></td>
                                        <td>
                                            <span class="badge badge-<?php
                                                echo $instrument['condition'] == INSTRUMENT_CONDITION_EXCELLENT ? 'success' :
                                                     ($instrument['condition'] == INSTRUMENT_CONDITION_GOOD ? 'primary' :
                                                     ($instrument['condition'] == INSTRUMENT_CONDITION_FAIR ? 'warning' :
                                                     ($instrument['condition'] == INSTRUMENT_CONDITION_POOR ? 'secondary' : 'danger')));
                                            ?>">
                                                <?php echo ucfirst($instrument['condition']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $instrument['instrument_price'] ? '₹' . number_format($instrument['instrument_price'], 2) : 'N/A'; ?></td>
                                        <td>
                                            <span class="badge badge-<?php
                                                echo $instrument['availability_status'] == INSTRUMENT_STATUS_AVAILABLE ? 'success' :
                                                     ($instrument['availability_status'] == INSTRUMENT_STATUS_ISSUED ? 'warning' :
                                                     ($instrument['availability_status'] == INSTRUMENT_STATUS_MAINTENANCE ? 'info' : 'danger'));
                                            ?>">
                                                <?php
                                                switch($instrument['availability_status']) {
                                                    case INSTRUMENT_STATUS_AVAILABLE: echo 'Available'; break;
                                                    case INSTRUMENT_STATUS_ISSUED: echo 'Issued'; break;
                                                    case INSTRUMENT_STATUS_MAINTENANCE: echo 'Under Maintenance'; break;
                                                    case INSTRUMENT_STATUS_DAMAGED: echo 'Damaged'; break;
                                                    default: echo 'Unknown';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td class="d-flex gap-1" style="flex-wrap: wrap;">
                                            <?php if ($instrument['availability_status'] == INSTRUMENT_STATUS_AVAILABLE): ?>
                                                <button class="btn btn-sm btn-warning" onclick="event.stopPropagation(); issueInstrument(<?php echo $instrument['id']; ?>, '<?php echo addslashes($instrument['name']); ?>')">
                                                    <i class="feather icon-send"></i> Issue
                                                </button>
                                            <?php elseif ($instrument['availability_status'] == INSTRUMENT_STATUS_ISSUED): ?>
                                                <button class="btn btn-sm btn-success" onclick="event.stopPropagation(); returnInstrument(<?php echo $instrument['id']; ?>, '<?php echo addslashes($instrument['name']); ?>')">
                                                    <i class="feather icon-rotate-ccw"></i> Return
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); editInstrument(<?php echo $instrument['id']; ?>)">
                                                <i class="feather icon-edit"></i> Edit
                                            </button>
                                            <?php if ($permissions['can_delete']): ?>
                                                <button class="btn btn-sm btn-danger" onclick="event.stopPropagation(); deleteInstrument(<?php echo $instrument['id']; ?>, '<?php echo addslashes($instrument['name']); ?>')">
                                                    <i class="feather icon-trash"></i> Delete
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                            <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="11" class="text-center">No instruments found</td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Modals -->
<?php $this->load->view('faculty/inventory/modals'); ?>

<script>
function applyFilters() {
    const status = document.getElementById('statusFilter').value;
    const category = document.getElementById('categoryFilter').value;
    const search = document.getElementById('searchFilter').value;

    // Reload page with filters
    const url = new URL(window.location);
    if (status) url.searchParams.set('status', status);
    if (category) url.searchParams.set('category', category);
    if (search) url.searchParams.set('search', search);

    window.location.href = url.toString();
}

function editInstrument(id) {
    // Load edit modal with data
    fetch('<?php echo base_url($url.'/inventory/get_instrument/'); ?>' + id)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const instrument = data.data;
                document.getElementById('edit_instrument_id').value = instrument.id;
                document.getElementById('edit_name').value = instrument.name;
                document.getElementById('edit_category').value = instrument.category;
                document.getElementById('edit_serial_no').value = instrument.serial_no;
                document.getElementById('edit_model').value = instrument.model || '';
                document.getElementById('edit_brand').value = instrument.brand || '';
                document.getElementById('edit_instrument_price').value = instrument.instrument_price || '';
                document.getElementById('edit_condition').value = instrument.condition || 'good';
                document.getElementById('edit_condition_notes').value = instrument.condition_notes || '';
                document.getElementById('edit_description').value = instrument.description || '';
                document.getElementById('edit_availability_status').value = instrument.availability_status || 'available';

                // Handle image display
                const imageContainer = document.getElementById('current_image_display');
                if (instrument.instrument_image) {
                    imageContainer.innerHTML = '<img src="<?php echo base_url(); ?>' + instrument.instrument_image + '" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">';
                } else {
                    imageContainer.innerHTML = '<small class="text-muted">No image uploaded</small>';
                }

                $('#editInstrumentModal').modal('show');
            }
        });
}

function issueInstrument(id, name) {
    document.getElementById('issue_instrument_id').value = id;
    document.getElementById('issue_instrument_name').textContent = name;

    // Reset form fields
    document.getElementById('issued_to_type').value = '';
    document.getElementById('issued_to_id').innerHTML = '<option value="">Select Student/Staff</option>';
    document.getElementById('issued_to_id').disabled = true;
    document.getElementById('expected_return_date').value = '';
    document.getElementById('purpose').value = '';

    // Set default issue date
    document.getElementById('issue_date').value = '<?php echo date('Y-m-d'); ?>';

    // Reset select2 elements
    $('#issued_to_type').val('').trigger('change');
    $('#issued_to_id').val('').trigger('change');

    // Initialize select2 for issue modal when shown
    $('#issueInstrumentModal').on('shown.bs.modal', function() {
        // Small delay to ensure modal is fully rendered
        setTimeout(function() {
            if (typeof $.fn.select2 !== 'undefined') {
                $('#issued_to_type').select2({
                    placeholder: "Select Type",
                    allowClear: true,
                    minimumResultsForSearch: Infinity,
                    width: '100%'
                });

                $('#issued_to_id').select2({
                    placeholder: "Select Student/Staff",
                    allowClear: true,
                    width: '100%'
                });

                // Bind change event for issue type selection
                $('#issued_to_type').on('change', function() {
                    toggleIssueFields();
                });
            }
        }, 100);
    });

    $('#issueInstrumentModal').modal('show');

    // Clean up select2 when modal is hidden
    $('#issueInstrumentModal').on('hidden.bs.modal', function() {
        if ($('#issued_to_type').hasClass('select2-hidden-accessible')) {
            $('#issued_to_type').select2('destroy');
        }
        if ($('#issued_to_id').hasClass('select2-hidden-accessible')) {
            $('#issued_to_id').select2('destroy');
        }
    });
}

function returnInstrument(id, name) {
    document.getElementById('return_instrument_id').value = id;
    document.getElementById('return_instrument_name').textContent = name;
    $('#returnInstrumentModal').modal('show');
}

// Global variable to store instrument ID for deletion
let instrumentToDelete = null;

function deleteInstrument(id, name) {
    // Store the instrument details
    instrumentToDelete = { id: id, name: name };

    // Update modal content
    document.getElementById('deleteInstrumentName').textContent = name;

    // Show the modal
    $('#deleteInstrumentModal').modal('show');
}

function toggleIssueFields() {
    const type = document.getElementById('issued_to_type').value;
    const idField = document.getElementById('issued_to_id');
    const idLabel = document.querySelector('label[for="issued_to_id"]');

    if (type === 'student') {
        idLabel.textContent = 'Issue To Student *';
        loadStudents();
    } else if (type === 'staff') {
        idLabel.textContent = 'Issue To Staff *';
        loadStaff();
    } else {
        idField.innerHTML = '<option value="">Select Student/Staff</option>';
        idField.disabled = true;
        // Reset select2
        if ($('#issued_to_id').hasClass('select2-hidden-accessible')) {
            $('#issued_to_id').select2('destroy');
            $('#issued_to_id').select2({
                placeholder: "Select Student/Staff",
                allowClear: true,
                width: '100%'
            });
        }
    }
}

function loadStudents() {
    const selectField = document.getElementById('issued_to_id');
    selectField.disabled = true;
    selectField.innerHTML = '<option value="">Loading...</option>';

    fetch('<?php echo base_url($url.'/api/get_students'); ?>')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                let options = '<option value="">Select Student</option>';
                data.data.forEach(student => {
                    options += `<option value="${student.id}">${student.student_id} - ${student.name}</option>`;
                });
                selectField.innerHTML = options;
                selectField.disabled = false;

                // Reinitialize select2
                if ($('#issued_to_id').hasClass('select2-hidden-accessible')) {
                    $('#issued_to_id').select2('destroy');
                }
                $('#issued_to_id').select2({
                    placeholder: "Select Student",
                    allowClear: true,
                    width: '100%'
                });
            } else {
                selectField.innerHTML = '<option value="">Error loading students</option>';
            }
        })
        .catch(error => {
            console.error('Error loading students:', error);
            selectField.innerHTML = '<option value="">Error loading students</option>';
        });
}

function loadStaff() {
    const selectField = document.getElementById('issued_to_id');
    selectField.disabled = true;
    selectField.innerHTML = '<option value="">Loading...</option>';

    fetch('<?php echo base_url($url.'/api/get_staff'); ?>')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                let options = '<option value="">Select Staff</option>';
                data.data.forEach(staff => {
                    options += `<option value="${staff.id}">${staff.designation} - ${staff.name}</option>`;
                });
                selectField.innerHTML = options;
                selectField.disabled = false;

                // Reinitialize select2
                if ($('#issued_to_id').hasClass('select2-hidden-accessible')) {
                    $('#issued_to_id').select2('destroy');
                }
                $('#issued_to_id').select2({
                    placeholder: "Select Staff",
                    allowClear: true,
                    width: '100%'
                });
            } else {
                selectField.innerHTML = '<option value="">Error loading staff</option>';
            }
        })
        .catch(error => {
            console.error('Error loading staff:', error);
            selectField.innerHTML = '<option value="">Error loading staff</option>';
        });
}

$(document).ready(function() {
    // Initialize popovers for instrument table rows
    $('.instrument-row').popover({
        container: 'body',
        boundary: 'viewport',
        delay: { show: 500, hide: 200 },
        html: true,
        trigger: 'hover focus',
        placement: 'right auto',
        title: function() {
            var instrumentId = $(this).data('instrument-id');
            // Find the instrument name from the table row
            var instrumentName = $(this).find('td:nth-child(3)').text();
            return '<strong>' + instrumentName + '</strong>';
        },
        content: function() {
            var row = $(this);
            var imageSrc = row.find('.instrument-image-src').text();
            var conditionNotes = row.find('.instrument-condition-notes').text() || 'No condition notes available.';
            var description = row.find('.instrument-description').text() || 'No description available.';

            // Create content using DOM manipulation instead of string concatenation
            var $content = $('<div class="instrument-popover"></div>');
            var $row = $('<div class="row no-gutters"></div>');
            var $imageCol = $('<div class="col-5 pr-2"></div>');
            var $textCol = $('<div class="col-7 pl-2"></div>');

            // Add image
            if (imageSrc) {
                $('<img>', {
                    src: imageSrc,
                    class: 'img-fluid rounded',
                    style: 'width: 100%; max-height: 120px; object-fit: cover;',
                    alt: 'Instrument Image'
                }).appendTo($imageCol);
            } else {
                var $noImageDiv = $('<div>', {
                    class: 'text-center text-muted p-2 border rounded',
                    style: 'height: 120px; display: flex; align-items: center; justify-content: center;'
                }).appendTo($imageCol);

                $('<div>').append(
                    $('<i>', { class: 'feather icon-image', style: 'font-size: 2rem;' }),
                    $('<br>'),
                    $('<small>').text('No Image')
                ).appendTo($noImageDiv);
            }

            // Add condition notes
            $('<div>', {
                class: 'condition-notes mb-2 p-2 bg-light rounded',
                style: 'font-size: 0.85rem;'
            }).append(
                $('<strong>', { class: 'text-warning' }).text('📝 Condition:'),
                $('<br>'),
                conditionNotes
            ).appendTo($textCol);

            $row.append($imageCol).append($textCol);
            $content.append($row);

            // Add description
            $('<div>', {
                class: 'description mt-2 p-2 bg-light rounded',
                style: 'font-size: 0.85rem;'
            }).append(
                $('<strong>', { class: 'text-info' }).text('📄 Description:'),
                $('<br>'),
                description
            ).appendTo($content);

            return $content[0].outerHTML;
        }
    });

    // Hide popover when clicking elsewhere
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.instrument-row, .popover').length) {
            $('.instrument-row').popover('hide');
        }
    });

    // Clean up popovers when page unloads
    $(window).on('beforeunload', function() {
        $('.instrument-row').popover('dispose');
    });

    // Handle confirm delete button click
    $('#confirmDeleteBtn').on('click', function() {
        if (instrumentToDelete) {
            // Hide the modal
            $('#deleteInstrumentModal').modal('hide');

            // Redirect to delete URL
            window.location.href = '<?php echo base_url($url.'/inventory/delete/'); ?>' + instrumentToDelete.id;
        }
    });

    // Reset the global variable and modal content when modal is hidden
    $('#deleteInstrumentModal').on('hidden.bs.modal', function() {
        instrumentToDelete = null;
        document.getElementById('deleteInstrumentName').textContent = '';
    });
});
</script>