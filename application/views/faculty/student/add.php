<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0"><?php echo isset($student) ? 'Edit Student' : 'Add New Student'; ?></h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/students'); ?>">Students</a></li>
                <li class="breadcrumb-item active"><?php echo isset($student) ? 'Edit' : 'Add'; ?> Student</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <style>
            .form-group label {
                font-weight: 600;
                color: #495057;
                margin-bottom: 8px;
            }
            .form-group label i {
                color: #6c757d;
            }
            .form-control {
                border-radius: 6px;
                border: 1px solid #ced4da;
                padding: 0.75rem;
            }
            .form-control:focus {
                border-color: #80bdff;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            }
            .card {
                border: none;
                border-radius: 8px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }
            .card-header {
                background-color: #f8f9fa;
                border-bottom: 1px solid #e9ecef;
                border-radius: 8px 8px 0 0 !important;
            }
        </style>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Student Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo isset($student) ? base_url($url.'/students/edit/'.$student['id']) : base_url($url.'/students/add'); ?>">
                            <input type="hidden" name="action" value="<?php echo isset($student) ? 'update' : 'create'; ?>">
                            <?php if(isset($student)): ?>
                                <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name"><i class="feather icon-user mr-2"></i>Full Name *</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                               value="<?php echo isset($student) ? htmlspecialchars($student['name']) : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email"><i class="feather icon-mail mr-2"></i>Email *</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               value="<?php echo isset($student) ? htmlspecialchars($student['email']) : ''; ?>" required
                                               <?php echo isset($student) ? 'readonly' : ''; ?>>
                                        <?php if(isset($student)): ?>
                                            <small class="form-text text-muted">Email cannot be changed after creation</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone"><i class="feather icon-phone mr-2"></i>Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                               value="<?php echo isset($student) ? htmlspecialchars($student['phone']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="roll_no"><i class="feather icon-hash mr-2"></i>Enrollment Number *</label>
                                        <input type="text" class="form-control" id="roll_no" name="roll_no"
                                               value="<?php echo isset($student) ? htmlspecialchars($student['roll_no']) : ''; ?>" required
                                               <?php echo isset($student) ? 'readonly' : ''; ?>>
                                        <?php if(isset($student)): ?>
                                            <small class="form-text text-muted">Enrollment number cannot be changed after creation</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="department"><i class="feather icon-briefcase mr-2"></i>Department *</label>
                                        <select class="form-control select2" id="department" name="department" required>
                                            <option value="">Select Department</option>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?php echo $dept['id']; ?>"
                                                    <?php
                                                    $is_selected = false;
                                                    if (isset($student) && $student['department'] == $dept['id']) {
                                                        $is_selected = true;
                                                    } elseif (isset($selected_department) && $selected_department == $dept['id']) {
                                                        $is_selected = true;
                                                    }
                                                    echo $is_selected ? 'selected' : '';
                                                    ?>>
                                                    <?php echo htmlspecialchars($dept['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="batch"><i class="feather icon-calendar mr-2"></i>Batch Year *</label>
                                        <input type="number" class="form-control" id="batch" name="batch"
                                               value="<?php echo isset($student) ? htmlspecialchars($student['batch']) : date('Y'); ?>"
                                               placeholder="e.g. 2024" required min="2000" max="2030"
                                               <?php echo isset($student) ? 'readonly' : ''; ?>>
                                        <?php if(isset($student)): ?>
                                            <small class="form-text text-muted">Batch year cannot be changed after creation</small>
                                        <?php else: ?>
                                            <small class="form-text text-muted">Enter the batch year (e.g. 2024)</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if (!isset($student)): ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password"><i class="feather icon-lock mr-2"></i>Password *</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password" name="password" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                    <i class="feather icon-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <small class="form-text text-muted">Minimum 6 characters</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <!-- Additional fields can go here if needed -->
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    <?php echo isset($student) ? 'Update Student' : 'Add Student'; ?>
                                </button>
                                <a href="<?php echo base_url($url.'/students'); ?>" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: "Select an option",
        allowClear: true,
        width: '100%'
    });

    // Password visibility toggle functionality
    function togglePasswordVisibility(passwordField, toggleButton) {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);

        const icon = toggleButton.querySelector('i');
        if (type === 'password') {
            icon.className = 'feather icon-eye';
        } else {
            icon.className = 'feather icon-eye-off';
        }
    }

    // Add event listener for password toggle button
    const togglePasswordBtn = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');

    if (togglePasswordBtn && passwordField) {
        togglePasswordBtn.addEventListener('click', function() {
            togglePasswordVisibility(passwordField, togglePasswordBtn);
        });
    }
});
</script>