<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Course Management</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item active">Courses</li>
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
            <!-- Total Courses -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="feather icon-book text-primary mr-2" style="font-size: 24px;"></i>
                            <h4 class="text-primary mb-0"><?php echo $stats['total_courses']; ?></h4>
                        </div>
                        <p class="mb-0 text-muted">Total Courses</p>
                    </div>
                </div>
            </div>

            <!-- Total Modules -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="feather icon-folder text-success mr-2" style="font-size: 24px;"></i>
                            <h4 class="text-success mb-0"><?php echo $stats['total_modules']; ?></h4>
                        </div>
                        <p class="mb-0 text-muted">Total Modules</p>
                    </div>
                </div>
            </div>

            <!-- Total Lessons -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="feather icon-file-text text-info mr-2" style="font-size: 24px;"></i>
                            <h4 class="text-info mb-0"><?php echo $stats['total_lessons']; ?></h4>
                        </div>
                        <p class="mb-0 text-muted">Total Lessons</p>
                    </div>
                </div>
            </div>

            <!-- Total Students Enrolled -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="feather icon-users text-warning mr-2" style="font-size: 24px;"></i>
                            <h4 class="text-warning mb-0"><?php echo $stats['total_students_enrolled']; ?></h4>
                        </div>
                        <p class="mb-0 text-muted">Students Enrolled</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Bar -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Course Management</h6>
                                <p class="mb-0">Manage courses, modules, and enrollments</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?php echo base_url($url.'/courses/add'); ?>" class="btn btn-primary">
                                    <i class="feather icon-plus"></i> Add Course
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Department Filter (for HOD and Staff) -->
        <?php if (isset($can_edit_all_courses) && !$can_edit_all_courses): ?>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="feather icon-info"></i> You can only view and manage courses in your department.
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Courses List -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($courses)): ?>
                            <div class="text-center py-5">
                                <i class="feather icon-book" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3">No Courses</h4>
                                <p class="text-muted">There are no courses to display.</p>
                                <a href="<?php echo base_url($url.'/courses/add'); ?>" class="btn btn-primary">
                                    Add First Course
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Course Code</th>
                                            <th>Course Name</th>
                                            <th>Department</th>
                                            <th>Created By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($courses as $course): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge badge-secondary"><?php echo htmlspecialchars($course['course_code'] ?? 'N/A'); ?></span>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($course['name']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars(substr($course['description'], 0, 100)); ?><?php echo strlen($course['description']) > 100 ? '...' : ''; ?></small>
                                                </td>
                                                <td>
                                                    <?php
                                                    $department_name = 'N/A';
                                                    if (!empty($course['department'])) {
                                                        foreach ($departments as $dept) {
                                                            if ($dept['id'] == $course['department']) {
                                                                $department_name = $dept['name'];
                                                                break;
                                                            }
                                                        }
                                                    }
                                                    echo htmlspecialchars($department_name);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($course['created_at'] ? date('M d, Y', strtotime($course['created_at'])) : 'N/A'); ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <!-- Modules -->
                                                        <a href="<?php echo base_url($url.'/courses/modules/'.$course['id']); ?>" class="btn btn-outline-info btn-sm" title="View Modules">
                                                            <i class="feather icon-layers"></i>
                                                        </a>

                                                        <!-- Enrollments -->
                                                        <a href="<?php echo base_url($url.'/courses/enrollments/'.$course['id']); ?>" class="btn btn-outline-success btn-sm" title="View Enrollments">
                                                            <i class="feather icon-users"></i>
                                                        </a>

                                                        <?php if (isset($can_edit_all_courses) && $can_edit_all_courses): ?>
                                                            <!-- Edit -->
                                                            <a href="<?php echo base_url($url.'/courses/edit/'.$course['id']); ?>" class="btn btn-outline-warning btn-sm" title="Edit Course">
                                                                <i class="feather icon-edit"></i>
                                                            </a>

                                                            <!-- Delete -->
                                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete(<?php echo $course['id']; ?>, '<?php echo htmlspecialchars($course['name']); ?>')" title="Delete Course">
                                                                <i class="feather icon-trash"></i>
                                                            </button>
                                                        <?php endif; ?>
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
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the course "<span id="courseName"></span>"? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a id="deleteBtn" href="#" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(courseId, courseName) {
    document.getElementById('courseName').textContent = courseName;
    document.getElementById('deleteBtn').href = '<?php echo base_url($url.'/courses/delete/'); ?>' + courseId;
    $('#deleteModal').modal('show');
}
</script>