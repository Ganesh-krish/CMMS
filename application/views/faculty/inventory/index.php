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
                            <div class="col-md-3">
                                <a href="<?php echo base_url($url.'/inventory/maintenance'); ?>" class="btn btn-secondary btn-block mb-2">
                                    <i class="feather icon-settings"></i> Maintenance Log
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
                                <th>Status</th>
                                <th>Issue Date</th>
                                <th>Due Date</th>
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
                                        <td><?php echo $categories[$instrument['category']] ?? $instrument['category']; ?></td>
                                        <td><?php echo $instrument['serial_no']; ?></td>
                                        <td>
                                            <span class="badge badge-<?php
                                                echo $instrument['availability_status'] == 'available' ? 'success' :
                                                     ($instrument['availability_status'] == 'issued' ? 'warning' :
                                                     ($instrument['availability_status'] == 'maintenance' ? 'info' : 'danger'));
                                            ?>">
                                                <?php echo ucfirst($instrument['availability_status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $instrument['issue_date'] ? date('d M Y', strtotime($instrument['issue_date'])) : 'N/A'; ?></td>
                                        <td><?php echo $instrument['due_date'] ? date('d M Y', strtotime($instrument['due_date'])) : 'N/A'; ?></td>
                                        <td class="d-flex gap-1" style="flex-wrap: wrap;">
                                            <button class="btn btn-sm btn-info" onclick="viewInstrument(<?php echo $instrument['id']; ?>)">
                                                <i class="feather icon-eye"></i> View
                                            </button>
                                            <?php if ($instrument['availability_status'] == 'available'): ?>
                                                <button class="btn btn-sm btn-warning" onclick="issueInstrument(<?php echo $instrument['id']; ?>, '<?php echo addslashes($instrument['name']); ?>')">
                                                    <i class="feather icon-send"></i> Issue
                                                </button>
                                            <?php elseif ($instrument['availability_status'] == 'issued'): ?>
                                                <button class="btn btn-sm btn-success" onclick="returnInstrument(<?php echo $instrument['id']; ?>, '<?php echo addslashes($instrument['name']); ?>')">
                                                    <i class="feather icon-rotate-ccw"></i> Return
                                                </button>
                                            <?php endif; ?>
                                            <button class="btn btn-sm btn-secondary" onclick="logMaintenance(<?php echo $instrument['id']; ?>, '<?php echo addslashes($instrument['name']); ?>')">
                                                <i class="feather icon-settings"></i> Maintenance
                                            </button>
                                            <button class="btn btn-sm btn-primary" onclick="editInstrument(<?php echo $instrument['id']; ?>)">
                                                <i class="feather icon-edit"></i> Edit
                                            </button>
                                        </td>
                                    </tr>
                            <?php }
                            } else { ?>
                                <tr>
                                    <td colspan="9" class="text-center">No instruments found</td>
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

function viewInstrument(id) {
    window.location.href = '<?php echo base_url($url.'/inventory/view/'); ?>' + id;
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
                document.getElementById('edit_description').value = instrument.description || '';
                document.getElementById('edit_issue_date').value = instrument.issue_date ? instrument.issue_date.substring(0, 10) : '';
                document.getElementById('edit_instrument_price').value = instrument.instrument_price || '';
                document.getElementById('edit_due_date').value = instrument.due_date ? instrument.due_date.substring(0, 10) : '';
                document.getElementById('edit_condition').value = instrument.condition || 'good';

                // Handle image display
                const imageContainer = document.getElementById('current_image_display');
                if (instrument.instrument_image) {
                    imageContainer.innerHTML = '<img src="<?php echo base_url(); ?>' + instrument.instrument_image + '" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">';
                } else {
                    imageContainer.innerHTML = '<small class="text-muted">No image uploaded</small>';
                }

                new bootstrap.Modal(document.getElementById('editInstrumentModal')).show();
            }
        });
}

function issueInstrument(id, name) {
    document.getElementById('issue_instrument_id').value = id;
    document.getElementById('issue_instrument_name').textContent = name;
    new bootstrap.Modal(document.getElementById('issueInstrumentModal')).show();
}

function returnInstrument(id, name) {
    document.getElementById('return_instrument_id').value = id;
    document.getElementById('return_instrument_name').textContent = name;
    new bootstrap.Modal(document.getElementById('returnInstrumentModal')).show();
}

function logMaintenance(id, name) {
    document.getElementById('maintenance_instrument_id').value = id;
    document.getElementById('maintenance_instrument_name').textContent = name;
    new bootstrap.Modal(document.getElementById('maintenanceModal')).show();
}

</script>