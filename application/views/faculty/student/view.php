<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Student Management</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item active">Students</li>
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

            <!-- Total Students -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="feather icon-users text-success mr-2" style="font-size: 24px;"></i>
                            <h4 class="text-success mb-0"><?php echo $stats['total_students']; ?></h4>
                        </div>
                        <p class="mb-0 text-muted">Total Students</p>
                    </div>
                </div>
            </div>

            <!-- Department-wise Student Count -->
            <?php if (!empty($department_stats)): ?>
                <?php foreach ($department_stats as $dept_stat): ?>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <i class="feather icon-user-check text-info mr-2" style="font-size: 24px;"></i>
                                    <h4 class="text-info mb-0"><?php echo $dept_stat['student_count']; ?></h4>
                                </div>
                                <p class="mb-0 text-muted"><?php echo htmlspecialchars($dept_stat['name']); ?></p>
                                <small class="text-muted">Students</small>
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
                                <h6>Student Management</h6>
                                <p class="mb-0">Manage student information and enrollment</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?php echo base_url($url.'/students/add'); ?>" class="btn btn-primary">
                                    <i class="feather icon-plus"></i> Add Student
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students List -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($students)): ?>
                            <div class="text-center py-5">
                                <i class="feather icon-users" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3">No Students</h4>
                                <p class="text-muted">There are no students to display.</p>
                                <a href="<?php echo base_url($url.'/students/add'); ?>" class="btn btn-primary">
                                    Add First Student
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="datatable table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Enrollment No</th>
                                            <th>Department</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($students as $student): ?>
                                            <tr>
                                                <td><?php echo $student['id']; ?></td>
                                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                                <td><?php echo htmlspecialchars($student['roll_no']); ?></td>
                                                <td>
                                                    <?php
                                                    if (isset($student['department']) && $student['department']) {
                                                        $dept = $this->db_model->get_row(TABLE_DEPARTMENT, ["id" => $student['department']]);
                                                        echo $dept ? htmlspecialchars($dept['name']) : 'N/A';
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?php echo $student['is_active'] ? 'success' : 'secondary'; ?>">
                                                        <?php echo $student['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('d M Y', strtotime($student['created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="<?php echo base_url($url.'/students/edit/'.$student['id']); ?>" class="btn btn-success btn-sm" title="Edit">
                                                            <i class="feather icon-edit"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-danger btn-sm" title="Delete" onclick="openDeleteModal(<?php echo $student['id']; ?>, '<?php echo htmlspecialchars($student['name']); ?>')">
                                                            <i class="feather icon-trash"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-warning btn-sm" title="Reset Password" onclick="openPasswordResetModal(<?php echo $student['id']; ?>, '<?php echo htmlspecialchars($student['name']); ?>')">
                                                            <i class="feather icon-lock"></i>
                                                        </button>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Student</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the student <strong id="deleteStudentName"></strong>? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
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
                    <p>Reset password for student: <strong id="resetStudentName"></strong></p>
                    <div class="form-group">
                        <label for="newPassword"><i class="feather icon-lock mr-2"></i>New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="password"
                               placeholder="Enter new password" required>
                        <small class="form-text text-muted">Password must be at least 6 characters long.</small>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword"><i class="feather icon-lock mr-2"></i>Confirm Password</label>
                        <input type="password" class="form-control" id="confirmPassword"
                               placeholder="Confirm new password" required>
                        <div class="invalid-feedback" id="passwordMatchError">
                            Passwords do not match.
                        </div>
                    </div>
                    <input type="hidden" name="id" id="resetStudentId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="resetPasswordBtn">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openDeleteModal(id, name) {
    document.getElementById('deleteStudentName').textContent = name;
    document.getElementById('confirmDeleteBtn').onclick = function() {
        window.location.href = '<?php echo base_url($url.'/students/delete/'); ?>' + id;
    };
    $('#deleteModal').modal('show');
}

function openPasswordResetModal(id, name) {
    document.getElementById('passwordResetModalLabel').textContent = 'Reset Password for ' + name;
    document.getElementById('passwordResetForm').action = '<?php echo base_url($url.'/students/reset_password'); ?>';
    document.getElementById('resetStudentId').value = id;

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

    // Password confirmation validation
    function validatePasswords() {
        var password = newPasswordField.value;
        var confirmPassword = confirmPasswordField.value;

        if (confirmPassword && password !== confirmPassword) {
            confirmPasswordField.classList.add('is-invalid');
            return false;
        } else {
            confirmPasswordField.classList.remove('is-invalid');
            return true;
        }
    }

    // Real-time validation
    confirmPasswordField.addEventListener('input', validatePasswords);
    newPasswordField.addEventListener('input', function() {
        if (confirmPasswordField.value) {
            validatePasswords();
        }
    });

    // Form submission validation
    form.addEventListener('submit', function(e) {
        var password = newPasswordField.value;

        // Check password length
        if (password.length < 6) {
            e.preventDefault();
            alert('Password must be at least 6 characters long.');
            newPasswordField.focus();
            return false;
        }

        // Check password confirmation
        if (!validatePasswords()) {
            e.preventDefault();
            alert('Passwords do not match.');
            confirmPasswordField.focus();
            return false;
        }

        // Disable submit button to prevent double submission
        var submitBtn = document.getElementById('resetPasswordBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="feather icon-loader"></i> Resetting...';
    });
}
</script>