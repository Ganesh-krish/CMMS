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

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5>Student Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo base_url($url.'/students'); ?>">
                            <input type="hidden" name="action" value="<?php echo isset($student) ? 'update' : 'create'; ?>">
                            <?php if(isset($student)): ?>
                                <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Full Name *</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                               value="<?php echo isset($student) ? htmlspecialchars($student['name']) : ''; ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               value="<?php echo isset($student) ? htmlspecialchars($student['email']) : ''; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                               value="<?php echo isset($student) ? htmlspecialchars($student['phone']) : ''; ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="enrollment_no">Enrollment Number *</label>
                                        <input type="text" class="form-control" id="enrollment_no" name="enrollment_no"
                                               value="<?php echo isset($student) ? htmlspecialchars($student['enrollment_no']) : ''; ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="department">Department *</label>
                                        <select class="form-control" id="department" name="department" required>
                                            <option value="">Select Department</option>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?php echo $dept['id']; ?>"
                                                    <?php echo (isset($student) && $student['department'] == $dept['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($dept['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Password *</label>
                                        <input type="password" class="form-control" id="password" name="password"
                                               <?php echo !isset($student) ? 'required' : ''; ?>>
                                        <small class="form-text text-muted">
                                            <?php echo isset($student) ? 'Leave blank to keep current password' : 'Minimum 6 characters'; ?>
                                        </small>
                                    </div>
                                </div>
                            </div>

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