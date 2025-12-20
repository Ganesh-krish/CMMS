<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Departments</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Departments</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Actions Bar -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Department Management</h6>
                                <p class="mb-0">View and manage department information</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php if (isset($can_manage) && $can_manage): ?>
                                    <a href="<?php echo base_url($url.'/departments/add'); ?>" class="btn btn-success">
                                        <i class="feather icon-plus"></i> Add Department
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Departments List -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($departments)): ?>
                            <div class="text-center py-5">
                                <i class="feather icon-home" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3">No Departments</h4>
                                <p class="text-muted">There are no departments to display.</p>
                                <?php if (isset($can_manage) && $can_manage): ?>
                                    <a href="<?php echo base_url($url.'/departments/add'); ?>" class="btn btn-primary">
                                        Add First Department
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="datatable table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Department Name</th>
                                            <th>Status</th>
                                            <?php if (isset($can_manage) && $can_manage): ?>
                                                <th>Actions</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($departments as $dept): ?>
                                            <tr>
                                                <td><?php echo $dept['id']; ?></td>
                                                <td><?php echo htmlspecialchars($dept['name']); ?></td>
                                                <td>
                                                    <span class="badge badge-<?php echo $dept['is_active'] ? 'success' : 'secondary'; ?>">
                                                        <?php echo $dept['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <?php if (isset($can_manage) && $can_manage): ?>
                                                    <td>
                                                        <div class="btn-group" role="group">
                                                            <a href="<?php echo base_url($url.'/departments/edit/'.$dept['id']); ?>" class="btn btn-sm btn-success" title="Edit Department">
                                                                <i class="feather icon-edit"></i>
                                                            </a>
                                                            <a href="#" onclick="confirmDelete(<?php echo $dept['id']; ?>, '<?php echo htmlspecialchars($dept['name']); ?>')" class="btn btn-sm btn-danger" title="Delete Department">
                                                                <i class="feather icon-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                <?php endif; ?>
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
function confirmDelete(id, name) {
    // Set modal content
    document.getElementById('deleteModalLabel').textContent = 'Delete Department';
    document.getElementById('deleteModalBody').innerHTML = 'Are you sure you want to delete the department "' + name + '"? This action cannot be undone.';

    // Store the delete URL
    window.deleteUrl = '<?php echo base_url($url.'/departments/delete/'); ?>' + id;

    // Show modal using Bootstrap's JavaScript API
    var deleteModalElement = document.getElementById('deleteModal');
    var modal = new bootstrap.Modal(deleteModalElement);
    modal.show();

    // Manually handle modal dismissal for delete modal
    setTimeout(function() {
        var cancelBtn = document.querySelector('#deleteModal .btn-secondary');
        var closeBtn = document.querySelector('#deleteModal .close');

        if (cancelBtn) {
            cancelBtn.onclick = function() {
                modal.hide();
            };
        }

        if (closeBtn) {
            closeBtn.onclick = function() {
                modal.hide();
            };
        }
    }, 100);
}

function proceedDelete() {
    if (window.deleteUrl) {
        window.location.href = window.deleteUrl;
    }
}
</script>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="deleteModalBody">
                Are you sure you want to delete this department? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="proceedDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>