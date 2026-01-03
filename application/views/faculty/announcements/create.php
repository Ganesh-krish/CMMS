<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Create Announcement</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/announcements'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/announcements'); ?>">Announcements</a></li>
                <li class="breadcrumb-item">Create</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <form action="<?php echo base_url($url.'/announcements/create'); ?>" method="post">
                            <div class="mb-3">
                                <label for="title" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-lg" id="title" name="title" required placeholder="Enter announcement title">
                                <?php echo form_error('title', '<div class="text-danger small">', '</div>'); ?>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label fw-bold">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="6" required placeholder="Enter your announcement message"></textarea>
                                <?php echo form_error('message', '<div class="text-danger small">', '</div>'); ?>
                            </div>

                            <div class="mb-3">
                                <label for="visibility" class="form-label">Visibility <span class="text-danger">*</span></label>
                                <select class="form-select" id="visibility" name="visibility" required onchange="toggleDepartmentField()" <?php echo (isset($force_department_visibility) && $force_department_visibility) ? 'disabled' : ''; ?>>
                                    <option value="">Select Visibility</option>
                                    <option value="all" <?php echo (isset($force_department_visibility) && $force_department_visibility) ? '' : 'selected'; ?>>All Users (Public)</option>
                                    <option value="department" <?php echo (isset($force_department_visibility) && $force_department_visibility) ? 'selected' : ''; ?>>Department Only</option>
                                </select>
                                <?php if (isset($force_department_visibility) && $force_department_visibility): ?>
                                    <input type="hidden" name="visibility" value="department">
                                <?php endif; ?>
                                <?php if (isset($force_department_visibility) && $force_department_visibility): ?>
                                    <small class="form-text text-muted">As a Department Administrator, you can only create department-specific announcements.</small>
                                <?php endif; ?>
                                <?php echo form_error('visibility', '<div class="text-danger">', '</div>'); ?>
                            </div>

                            <div class="mb-3" id="departmentField" style="<?php echo (isset($force_department_visibility) && $force_department_visibility) ? '' : 'display: none;'; ?>">
                                <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                <select class="form-select" id="department_id" name="department_id" <?php echo (isset($force_department_visibility) && $force_department_visibility) ? 'required' : ''; ?>>
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?php echo $dept['id']; ?>"
                                            <?php
                                            $is_selected = false;
                                            if (isset($selected_department) && $selected_department == $dept['id']) {
                                                $is_selected = true;
                                            }
                                            echo $is_selected ? 'selected' : '';
                                            ?>>
                                            <?php echo htmlspecialchars($dept['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($force_department_visibility) && $force_department_visibility): ?>
                                    <small class="form-text text-muted">Only your department will see this announcement.</small>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="normal">Normal</option>
                                    <option value="high">High Priority</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="<?php echo base_url($url.'/announcements'); ?>" class="btn btn-outline-secondary">
                                    <i class="feather icon-x me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="feather icon-plus me-1"></i>Create Announcement
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDepartmentField() {
    const visibility = document.getElementById('visibility').value;
    const departmentField = document.getElementById('departmentField');

    <?php if (!isset($force_department_visibility) || !$force_department_visibility): ?>
    // Only toggle visibility for non-HOD users
    if (visibility === 'department') {
        departmentField.style.display = 'block';
        document.getElementById('department_id').required = true;
    } else {
        departmentField.style.display = 'none';
        document.getElementById('department_id').required = false;
    }
    <?php endif; ?>
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleDepartmentField();

    // Initialize form selects
    const selects = document.querySelectorAll('select');
    selects.forEach(select => {
        select.classList.add('form-select');
    });
});
</script>







