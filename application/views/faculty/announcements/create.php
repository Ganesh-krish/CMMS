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
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required placeholder="Enter announcement title">
                                <?php echo form_error('title', '<div class="text-danger">', '</div>'); ?>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="8" required placeholder="Enter your announcement message"></textarea>
                                <?php echo form_error('message', '<div class="text-danger">', '</div>'); ?>
                            </div>

                            <div class="mb-3">
                                <label for="visibility" class="form-label">Visibility <span class="text-danger">*</span></label>
                                <select class="form-control" id="visibility" name="visibility" required onchange="toggleDepartmentField()">
                                    <option value="">Select Visibility</option>
                                    <option value="all">All Users (Public)</option>
                                    <option value="department">Department Only</option>
                                </select>
                                <?php echo form_error('visibility', '<div class="text-danger">', '</div>'); ?>
                            </div>

                            <div class="mb-3" id="departmentField" style="display: none;">
                                <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="department_id" name="department_id">
                                    <option value="">Select Department</option>
                                    <?php foreach ($departments as $dept): ?>
                                        <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select class="form-control" id="priority" name="priority">
                                    <option value="normal">Normal</option>
                                    <option value="high">High Priority</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="<?php echo base_url($url.'/announcements'); ?>" class="btn btn-secondary">Cancel</a>
                                <button type="submit" class="btn btn-primary">Create Announcement</button>
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

    if (visibility === 'department') {
        departmentField.style.display = 'block';
        document.getElementById('department_id').required = true;
    } else {
        departmentField.style.display = 'none';
        document.getElementById('department_id').required = false;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleDepartmentField();

    // Initialize Select2
    $('.select2').select2({
        placeholder: "Select an option",
        allowClear: true
    });
});
</script>







