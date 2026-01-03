
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

        <!-- Quick Actions - Hidden for HODs -->
        <?php if (!isset($current_user_is_hod) || !$current_user_is_hod): ?>
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
        <?php endif; ?>

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
                                <th>Description</th>
                                <th>Category</th>
                                <th>Serial No</th>
                                <th>Model</th>
                                <th>Brand</th>
                                <th>Condition</th>
                                <th>Price</th>
                                <th>Created Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($instruments)) {
                                $i = 1;
                                foreach ($instruments as $instrument) { ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td>
                                            <?php if (!empty($instrument['instrument_image'])): ?>
                                                <img src="<?php echo base_url($instrument['instrument_image']); ?>" class="img-thumbnail" style="max-width: 50px; max-height: 50px;" alt="Instrument Image">
                                            <?php else: ?>
                                                <span class="text-muted">No Image</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $instrument['name']; ?></td>
                                        <td><?php echo $instrument['description'] ?? 'N/A'; ?></td>
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
                                        <td><?php echo $instrument['created_at'] ? date('d M Y', strtotime($instrument['created_at'])) : 'N/A'; ?></td>
                                        <td>
                                            <span class="badge badge-<?php
                                                $status = $instrument['availability_status'];
                                                $badge_class = 'secondary'; // default
                                                if ($status == INSTRUMENT_STATUS_AVAILABLE || $status == '1' || $status == 1) {
                                                    $badge_class = 'success';
                                                } elseif ($status == INSTRUMENT_STATUS_ISSUED || $status == '2' || $status == 2) {
                                                    $badge_class = 'warning';
                                                } elseif ($status == INSTRUMENT_STATUS_MAINTENANCE || $status == '3' || $status == 3) {
                                                    $badge_class = 'info';
                                                } elseif ($status == INSTRUMENT_STATUS_DAMAGED || $status == '4' || $status == 4) {
                                                    $badge_class = 'danger';
                                                }
                                                echo $badge_class;
                                            ?>">
                                                <?php
                                                $status = $instrument['availability_status'];
                                                if ($status == INSTRUMENT_STATUS_AVAILABLE || $status == '1' || $status == 1) {
                                                    echo 'Available';
                                                } elseif ($status == INSTRUMENT_STATUS_ISSUED || $status == '2' || $status == 2) {
                                                    echo 'Issued';
                                                } elseif ($status == INSTRUMENT_STATUS_MAINTENANCE || $status == '3' || $status == 3) {
                                                    echo 'Under Maintenance';
                                                } elseif ($status == INSTRUMENT_STATUS_DAMAGED || $status == '4' || $status == 4) {
                                                    echo 'Damaged';
                                                } else {
                                                    echo 'Unknown';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        <td class="d-flex gap-1" style="flex-wrap: wrap;">
                                            <?php if ($instrument['availability_status'] == INSTRUMENT_STATUS_AVAILABLE || $instrument['availability_status'] == '1' || $instrument['availability_status'] == 1): ?>
                                                <a href="<?php echo site_url($url.'/inventory/issue/'.$instrument['id']); ?>" class="btn btn-sm btn-warning" title="Issue instrument ID: <?php echo $instrument['id']; ?>">
                                                    <i class="feather icon-send"></i> Issue
                                                </a>
                                            <?php elseif ($instrument['availability_status'] == INSTRUMENT_STATUS_ISSUED || $instrument['availability_status'] == '2' || $instrument['availability_status'] == 2): ?>
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
                                    <td colspan="13" class="text-center">No instruments found</td>
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

                const editModal = new bootstrap.Modal(document.getElementById('editInstrumentModal'));
    editModal.show();
            }
        });
}

function returnInstrument(id, name) {
    document.getElementById('return_instrument_id').value = id;
    document.getElementById('return_instrument_name').textContent = name;
    const returnModal = new bootstrap.Modal(document.getElementById('returnInstrumentModal'));
    returnModal.show();
}

// Global variable for modal management
let instrumentToDelete = null;

function deleteInstrument(id, name) {
    // Store the instrument details
    instrumentToDelete = { id: id, name: name };

    // Update modal content
    document.getElementById('deleteInstrumentName').textContent = name;

    // Show the modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteInstrumentModal'));
    deleteModal.show();
}


$(document).ready(function() {
    // Handle confirm delete button click
    $('#confirmDeleteBtn').on('click', function() {
        if (instrumentToDelete) {
            // Hide the modal
            const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteInstrumentModal'));
            if (deleteModal) deleteModal.hide();

            // Redirect to delete URL
            window.location.href = '<?php echo base_url($url.'/inventory/delete/'); ?>' + instrumentToDelete.id;
        }
    });

    // Reset the global variable and modal content when modal is hidden
    document.getElementById('deleteInstrumentModal').addEventListener('hidden.bs.modal', function() {
        instrumentToDelete = null;
        document.getElementById('deleteInstrumentName').textContent = '';
    });
});
</script>