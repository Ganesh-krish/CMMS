<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Courses Management</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item">Courses</li>
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
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                    <i class="feather icon-plus"></i> Add Course
                </button>
            </div>

            <div class="table-responsive">
                <table id="coursesTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Course Name</th>
                            <th>Course Code</th>
                            <th>Department</th>
                            <th>Description</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($courses)) {
                            $i = 1;
                            foreach ($courses as $course) { ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $course['name']; ?></td>
                                    <td><?php echo $course['code']; ?></td>
                                    <td><?php
                                        $dept = $this->db_model->get_row(TABLE_DEPARTMENT, ["id" => $course['department']]);
                                        echo $dept ? $dept['name'] : 'Unknown';
                                    ?></td>
                                    <td><?php echo substr($course['description'], 0, 50) . (strlen($course['description']) > 50 ? '...' : ''); ?></td>
                                    <td><?php echo $this->common->display_date($course['created_at']); ?></td>
                                    <td class="d-flex gap-1" style="flex-wrap: wrap;">
                                        <a href="<?php echo base_url($url.'/courses/modules/'.$course['id']); ?>" class="btn btn-sm btn-info">
                                            <i class="feather icon-layers"></i> Modules
                                        </a>
                                        <a href="<?php echo base_url($url.'/courses/enrollments/'.$course['id']); ?>" class="btn btn-sm btn-success">
                                            <i class="feather icon-users"></i> Enrollments
                                        </a>
                                        <?php if (isset($can_edit_all_courses) && $can_edit_all_courses): ?>
                                        <button class="btn btn-sm btn-warning" onclick="editCourse(<?php echo $course['id']; ?>, '<?php echo addslashes($course['name']); ?>', '<?php echo addslashes($course['code']); ?>', '<?php echo addslashes($course['description']); ?>', '<?php echo $course['department']; ?>')">
                                            <i class="feather icon-edit"></i> Edit
                                        </button>
                                        <?php endif; ?>
                                        <?php if (isset($can_delete_courses) && $can_delete_courses): ?>
                                        <button class="btn btn-sm btn-danger" onclick="deleteCourse(<?php echo $course['id']; ?>)">
                                            <i class="feather icon-trash"></i> Delete
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                        <?php }
                        } else { ?>
                            <tr>
                                <td colspan="7" class="text-center">No courses found</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCourseModalLabel">Add Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($url . '/courses/add') ?>" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Course Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="code" class="form-label">Course Code *</label>
                            <input type="text" class="form-control" id="code" name="code" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label">Department *</label>
                            <select class="form-control" id="department" name="department" required>
                                <option value="">Select Department</option>
                                <?php if (!empty($departments)) {
                                    foreach ($departments as $dept) { ?>
                                        <option value="<?php echo $dept['id']; ?>"><?php echo $dept['name']; ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCourseModalLabel">Edit Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($url . '/courses/edit') ?>" method="POST">
                <input type="hidden" id="edit_course_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_name" class="form-label">Course Name *</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_code" class="form-label">Course Code *</label>
                            <input type="text" class="form-control" id="edit_code" name="code" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_department" class="form-label">Department *</label>
                            <select class="form-control" id="edit_department" name="department" required>
                                <option value="">Select Department</option>
                                <?php if (!empty($departments)) {
                                    foreach ($departments as $dept) { ?>
                                        <option value="<?php echo $dept['id']; ?>"><?php echo $dept['name']; ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description *</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editCourse(id, name, code, description, department) {
    document.getElementById('edit_course_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_code').value = code;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_department').value = department;
    new bootstrap.Modal(document.getElementById('editCourseModal')).show();
}

function deleteCourse(id) {
    if (confirm('Are you sure you want to delete this course? This will also delete all modules and lessons.')) {
        window.location.href = '<?= base_url($url . "/courses/delete/") ?>' + id;
    }
}

// Initialize DataTable
$(document).ready(function() {
    $('#coursesTable').DataTable({
        "pageLength": 25,
        "order": [[ 0, "asc" ]]
    });
});
</script>
