<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0"><?php echo isset($course) ? 'Edit Course' : 'Add Course'; ?></h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/courses'); ?>">Courses</a></li>
                <li class="breadcrumb-item active"><?php echo isset($course) ? 'Edit' : 'Add'; ?></li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="<?php echo isset($course) ? base_url($url.'/courses/edit/'.$course['id']) : base_url($url.'/courses/add'); ?>" method="post">
                            <?php if (isset($course)): ?>
                                <input type="hidden" name="id" value="<?php echo $course['id']; ?>">
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Course Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name"
                                               value="<?php echo isset($course) ? htmlspecialchars($course['name']) : set_value('name'); ?>"
                                               placeholder="Enter course name" required>
                                        <?php echo form_error('name', '<small class="text-danger">', '</small>'); ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="code">Course Code <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="code" name="code"
                                               value="<?php echo isset($course) ? htmlspecialchars($course['course_code'] ?? $course['code']) : set_value('code'); ?>"
                                               placeholder="Enter course code (e.g., CS101)" required>
                                        <?php echo form_error('code', '<small class="text-danger">', '</small>'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="department">Department</label>
                                        <select class="form-control select2" id="department" name="department">
                                            <option value="">Select Department</option>
                                            <?php foreach ($departments as $dept): ?>
                                                <option value="<?php echo $dept['id']; ?>"
                                                        <?php echo (isset($course) && $course['department'] == $dept['id']) ? 'selected' : set_select('department', $dept['id']); ?>>
                                                    <?php echo htmlspecialchars($dept['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php echo form_error('department', '<small class="text-danger">', '</small>'); ?>
                                        <small class="form-text text-muted">Leave empty if course is available to all departments</small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="description">Description <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="description" name="description" rows="3"
                                                  placeholder="Enter course description" required><?php echo isset($course) ? htmlspecialchars($course['description']) : set_value('description'); ?></textarea>
                                        <?php echo form_error('description', '<small class="text-danger">', '</small>'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="auto_enroll" name="auto_enroll" value="1" checked>
                                            <label class="custom-control-label" for="auto_enroll">
                                                Auto-enroll students from selected department
                                            </label>
                                        </div>
                                        <small class="form-text text-muted">
                                            If a department is selected, students from that department will be automatically enrolled in this course
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <hr>
                                    <div class="form-group text-right">
                                        <a href="<?php echo base_url($url.'/courses'); ?>" class="btn btn-secondary">
                                            <i class="feather icon-arrow-left"></i> Back to Courses
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="feather icon-save"></i> <?php echo isset($course) ? 'Update Course' : 'Create Course'; ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize Select2 for department dropdown
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Select department",
        allowClear: true
    });
});
</script>