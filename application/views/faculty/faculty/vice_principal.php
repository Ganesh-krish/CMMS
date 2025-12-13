<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Vice-Principal</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <!-- <li class="breadcrumb-item">Purchase</li> -->
                <li class="breadcrumb-item">Vice-Principal</li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php   } ?>
        <div class="card p-2">
            <div class="d-flex justify-content-end mb-2">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVicePrincipalModal">
                    <i class="feather icon-plus"></i> Add Vice-Principal
                </button>
            </div>
            <div class="table-responsive">
                <table id="dataTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($vice_principal)) {
                            $i = 1;
                            foreach ($vice_principal as $row) { ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $row['name']; ?></td>
                                    <td><?php echo $row['email']; ?></td>
                                    <td><?php echo $row['department']; ?></td>
                                    <td><?php echo $row['phone'] ?? '-'; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $row['is_active'] ? 'success' : 'danger'; ?>">
                                            <?php echo $row['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton<?php echo $row['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo $row['id']; ?>">
                                                <li><a class="dropdown-item" href="#" onclick="editVicePrincipal(<?php echo $row['id']; ?>, '<?php echo $row['name']; ?>', '<?php echo $row['email']; ?>', '<?php echo $row['phone'] ?? ''; ?>')"><i class="feather icon-edit"></i> Edit</a></li>
                                                <li><a class="dropdown-item" href="#" onclick="resetPassword(<?php echo $row['id']; ?>)"><i class="feather icon-lock"></i> Reset Password</a></li>
                                                <li><a class="dropdown-item text-danger" href="#" onclick="deleteVicePrincipal(<?php echo $row['id']; ?>)"><i class="feather icon-trash"></i> Delete</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                        <?php }
                        } else { ?>
                            <tr>
                                <td colspan="7" class="text-center">No Vice-Principals found</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Vice-Principal Modal -->
<div class="modal fade" id="addVicePrincipalModal" tabindex="-1" aria-labelledby="addVicePrincipalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVicePrincipalModalLabel">Add Vice-Principal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="<?= base_url($url . '/principal/add_vice_principal') ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <select class="form-control" id="department" name="department" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments ?? [] as $dept) { ?>
                                <option value="<?php echo $dept['id']; ?>"><?php echo $dept['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Vice-Principal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Vice-Principal Modal -->
<div class="modal fade" id="editVicePrincipalModal" tabindex="-1" aria-labelledby="editVicePrincipalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editVicePrincipalModalLabel">Edit Vice-Principal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post" action="<?= base_url($url . '/principal/edit_vice_principal') ?>">
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="edit_phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="edit_department" class="form-label">Department</label>
                        <select class="form-control" id="edit_department" name="department" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments ?? [] as $dept) { ?>
                                <option value="<?php echo $dept['id']; ?>"><?php echo $dept['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Vice-Principal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editVicePrincipal(id, name, email, phone) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_phone').value = phone;
    // Note: Department selection would need additional logic to set the correct department
    new bootstrap.Modal(document.getElementById('editVicePrincipalModal')).show();
}

function resetPassword(id) {
    if (confirm('Are you sure you want to reset the password?')) {
        // Create a form and submit it
        var form = document.createElement('form');
        form.method = 'post';
        form.action = '<?= base_url($url . "/principal/reset_password") ?>';

        var idField = document.createElement('input');
        idField.type = 'hidden';
        idField.name = 'id';
        idField.value = id;
        form.appendChild(idField);

        var passwordField = document.createElement('input');
        passwordField.type = 'hidden';
        passwordField.name = 'password';
        passwordField.value = '123456'; // Default password
        form.appendChild(passwordField);

        document.body.appendChild(form);
        form.submit();
    }
}

function deleteVicePrincipal(id) {
    if (confirm('Are you sure you want to delete this Vice-Principal?')) {
        window.location.href = '<?= base_url($url . "/principal/delete_vice_principal/") ?>' + id;
    }
}
</script>