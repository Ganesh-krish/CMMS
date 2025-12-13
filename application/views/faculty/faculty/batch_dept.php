<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Batch & Department Management</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item">Batch & Dept</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php   } ?>

        <!-- Departments Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Departments</h5>
                <div class="card-header-actions">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                        <i class="feather icon-plus"></i> Add Department
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Department Name</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($departments)): ?>
                                <?php foreach ($departments as $dept): ?>
                                    <tr>
                                        <td><?= $dept['id'] ?></td>
                                        <td><?= $dept['name'] ?></td>
                                        <td>
                                            <span class="badge badge-<?= $dept['is_active'] ? 'success' : 'danger' ?>">
                                                <?= $dept['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td><?= date('d M Y', strtotime($dept['created_at'])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="editDepartment(<?= $dept['id'] ?>, '<?= $dept['name'] ?>')">
                                                <i class="feather icon-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteDepartment(<?= $dept['id'] ?>, '<?= $dept['name'] ?>')">
                                                <i class="feather icon-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">No departments found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Batches Section -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Batches</h5>
                <div class="card-header-actions">
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addBatchModal">
                        <i class="feather icon-plus"></i> Add Batch
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Batch Name</th>
                                <th>Year</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($batches)): ?>
                                <?php foreach ($batches as $batch): ?>
                                    <tr>
                                        <td><?= $batch['id'] ?></td>
                                        <td><?= $batch['name'] ?></td>
                                        <td><?= $batch['year'] ?? '-' ?></td>
                                        <td>
                                            <span class="badge badge-<?= $batch['is_active'] ? 'success' : 'danger' ?>">
                                                <?= $batch['is_active'] ? 'Active' : 'Inactive' ?>
                                            </span>
                                        </td>
                                        <td><?= date('d M Y', strtotime($batch['created_at'])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="editBatch(<?= $batch['id'] ?>, '<?= $batch['name'] ?>', '<?= $batch['year'] ?>')">
                                                <i class="feather icon-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteBatch(<?= $batch['id'] ?>, '<?= $batch['name'] ?>')">
                                                <i class="feather icon-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No batches found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Department Modal -->
<div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDepartmentModalLabel">Add Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addDepartmentForm" action="<?= base_url($url . '/principal/add_department') ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="departmentName" class="form-label">Department Name</label>
                        <input type="text" class="form-control" id="departmentName" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Department Modal -->
<div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editDepartmentModalLabel">Edit Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editDepartmentForm" action="" method="post">
                <input type="hidden" id="editDepartmentId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editDepartmentName" class="form-label">Department Name</label>
                        <input type="text" class="form-control" id="editDepartmentName" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Department</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Batch Modal -->
<div class="modal fade" id="addBatchModal" tabindex="-1" aria-labelledby="addBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBatchModalLabel">Add Batch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addBatchForm" action="<?= base_url($url . '/principal/add_batch') ?>" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="batchName" class="form-label">Batch Name</label>
                        <input type="text" class="form-control" id="batchName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="batchYear" class="form-label">Year (Optional)</label>
                        <input type="text" class="form-control" id="batchYear" name="year" placeholder="e.g., 2023-2024">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Batch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Batch Modal -->
<div class="modal fade" id="editBatchModal" tabindex="-1" aria-labelledby="editBatchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBatchModalLabel">Edit Batch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBatchForm" action="" method="post">
                <input type="hidden" id="editBatchId" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editBatchName" class="form-label">Batch Name</label>
                        <input type="text" class="form-control" id="editBatchName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editBatchYear" class="form-label">Year (Optional)</label>
                        <input type="text" class="form-control" id="editBatchYear" name="year" placeholder="e.g., 2023-2024">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Batch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editDepartment(id, name) {
    document.getElementById('editDepartmentId').value = id;
    document.getElementById('editDepartmentName').value = name;
    document.getElementById('editDepartmentForm').action = '<?= base_url($url . "/principal/edit_department/") ?>' + id;
    new bootstrap.Modal(document.getElementById('editDepartmentModal')).show();
}

function deleteDepartment(id, name) {
    if (confirm('Are you sure you want to delete department "' + name + '"?')) {
        window.location.href = '<?= base_url($url . "/principal/delete_department/") ?>' + id;
    }
}

function editBatch(id, name, year) {
    document.getElementById('editBatchId').value = id;
    document.getElementById('editBatchName').value = name;
    document.getElementById('editBatchYear').value = year || '';
    document.getElementById('editBatchForm').action = '<?= base_url($url . "/principal/edit_batch/") ?>' + id;
    new bootstrap.Modal(document.getElementById('editBatchModal')).show();
}

function deleteBatch(id, name) {
    if (confirm('Are you sure you want to delete batch "' + name + '"?')) {
        window.location.href = '<?= base_url($url . "/principal/delete_batch/") ?>' + id;
    }
}
</script>