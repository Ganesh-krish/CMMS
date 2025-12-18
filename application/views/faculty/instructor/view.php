<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Instructor Management</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/faculty/instructor'); ?>">Faculty</a></li>
                <li class="breadcrumb-item active">Instructor</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <!-- Total Departments -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="feather icon-briefcase text-primary mr-2" style="font-size: 24px;"></i>
                            <h4 class="text-primary mb-0"><?php echo $stats['total_departments']; ?></h4>
                        </div>
                        <p class="mb-0 text-muted">Total Departments</p>
                    </div>
                </div>
            </div>

            <!-- Total Instructors -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="feather icon-users text-success mr-2" style="font-size: 24px;"></i>
                            <h4 class="text-success mb-0"><?php echo $stats['total_instructors']; ?></h4>
                        </div>
                        <p class="mb-0 text-muted">Total Instructors</p>
                    </div>
                </div>
            </div>

            <!-- Department-wise Instructor Count -->
            <?php if (!empty($department_stats)): ?>
                <?php foreach ($department_stats as $dept_stat): ?>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <i class="feather icon-user-check text-info mr-2" style="font-size: 24px;"></i>
                                    <h4 class="text-info mb-0"><?php echo $dept_stat['instructor_count']; ?></h4>
                                </div>
                                <p class="mb-0 text-muted"><?php echo htmlspecialchars($dept_stat['name']); ?></p>
                                <small class="text-muted">Instructors</small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Actions Bar -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Instructor Management</h6>
                                <p class="mb-0">Manage teaching faculty members</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?php echo base_url($url.'/faculty/instructor/add'); ?>" class="btn btn-primary">
                                    <i class="feather icon-plus"></i> Add Instructor
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructors List -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($instructors)): ?>
                            <div class="text-center py-5">
                                <i class="feather icon-users" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3">No Instructors</h4>
                                <p class="text-muted">There are no instructors to display.</p>
                                <a href="<?php echo base_url($url.'/faculty/instructor/add'); ?>" class="btn btn-primary">
                                    Add First Instructor
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone Number</th>
                                            <th>Department</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($instructors as $instructor): ?>
                                            <tr>
                                                <td><?php echo $instructor['id']; ?></td>
                                                <td><?php echo htmlspecialchars($instructor['name']); ?></td>
                                                <td><?php echo htmlspecialchars($instructor['email']); ?></td>
                                                <td><?php echo htmlspecialchars($instructor['phone'] ?? '-'); ?></td>
                                                <td>
                                                    <?php
                                                    if (isset($instructor['department']) && $instructor['department']) {
                                                        $dept = $this->db_model->get_row(TABLE_DEPARTMENT, ["id" => $instructor['department']]);
                                                        echo $dept ? htmlspecialchars($dept['name']) : 'N/A';
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?php echo $instructor['is_active'] ? 'success' : 'secondary'; ?>">
                                                        <?php echo $instructor['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('d M Y', strtotime($instructor['created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="<?php echo base_url($url.'/faculty/instructor/edit/'.$instructor['id']); ?>" class="btn btn-sm btn-success" title="Edit">
                                                            <i class="feather icon-edit"></i>
                                                        </a>
                                                        <a href="#" onclick="confirmDelete(<?php echo $instructor['id']; ?>, '<?php echo htmlspecialchars($instructor['name']); ?>')" class="btn btn-sm btn-danger" title="Delete">
                                                            <i class="feather icon-trash"></i>
                                                        </a>
                                                        <a href="#" onclick="resetPassword(<?php echo $instructor['id']; ?>, '<?php echo htmlspecialchars($instructor['name']); ?>')" class="btn btn-sm btn-warning" title="Reset Password">
                                                            <i class="feather icon-lock"></i>
                                                        </a>
                                                    </div>
                                                </td>
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
    document.getElementById('deleteModalLabel').textContent = 'Delete Instructor';
    document.getElementById('deleteModalBody').innerHTML = 'Are you sure you want to delete the instructor "' + name + '"? This action cannot be undone.';

    // Store the delete URL
    window.deleteUrl = '<?php echo base_url($url.'/faculty/instructor/delete/'); ?>' + id;

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

function resetPassword(id, name) {
    // Set modal title and form action
    document.getElementById('passwordResetModalLabel').textContent = 'Reset Password for ' + name;
    document.getElementById('passwordResetForm').action = '<?php echo base_url($url.'/faculty/reset_password_instructor'); ?>';
    document.getElementById('resetUserId').value = id;

    // Reset form
    document.getElementById('passwordResetForm').reset();
    document.getElementById('confirmPassword').classList.remove('is-invalid');

    // Show modal
    var passwordResetModalElement = document.getElementById('passwordResetModal');
    var modal = new bootstrap.Modal(passwordResetModalElement);
    modal.show();

    // Store modal instance for dismissal
    window.currentPasswordModal = modal;

    // Initialize form validation after modal is shown
    setTimeout(initializePasswordResetValidation, 100);
}

// Form validation for password reset - initialized when modal is shown
function initializePasswordResetValidation() {
    var form = document.getElementById('passwordResetForm');
    var newPasswordField = document.getElementById('newPassword');
    var confirmPasswordField = document.getElementById('confirmPassword');

    if (!form || !newPasswordField || !confirmPasswordField) {
        return; // Elements not found
    }

    form.addEventListener('submit', function(e) {
        var password = newPasswordField.value;
        var confirmPassword = confirmPasswordField.value;

        if (password !== confirmPassword) {
            e.preventDefault();
            confirmPasswordField.classList.add('is-invalid');
            return false;
        }

        // Remove invalid class if validation passes
        confirmPasswordField.classList.remove('is-invalid');
    });

    // Reset validation on password change
    newPasswordField.addEventListener('input', function() {
        confirmPasswordField.classList.remove('is-invalid');
    });

    confirmPasswordField.addEventListener('input', function() {
        confirmPasswordField.classList.remove('is-invalid');
    });

    // Manually handle modal dismissal
    var cancelBtn = form.querySelector('.btn-secondary');
    var closeBtn = document.querySelector('#passwordResetModal .close');

    if (cancelBtn) {
        cancelBtn.onclick = function() {
            if (window.currentPasswordModal) {
                window.currentPasswordModal.hide();
            }
        };
    }

    if (closeBtn) {
        closeBtn.onclick = function() {
            if (window.currentPasswordModal) {
                window.currentPasswordModal.hide();
            }
        };
    }
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
                Are you sure you want to delete this instructor? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="proceedDelete()">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Password Reset Modal -->
<div class="modal fade" id="passwordResetModal" tabindex="-1" role="dialog" aria-labelledby="passwordResetModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordResetModalLabel">Reset Password</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="passwordResetForm" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="newPassword">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="password"
                               placeholder="Enter new password" required>
                        <small class="form-text text-muted">Password must be at least 6 characters long.</small>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword"
                               placeholder="Confirm new password" required>
                        <div class="invalid-feedback" id="passwordMatchError">
                            Passwords do not match.
                        </div>
                    </div>
                    <input type="hidden" name="id" id="resetUserId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>