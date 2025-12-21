<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Course Enrollments</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/courses'); ?>">Courses</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($course['name']); ?> - Enrollments</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Course Info -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5><?php echo htmlspecialchars($course['name']); ?></h5>
                                <p class="mb-1"><?php echo htmlspecialchars($course['description']); ?></p>
                                <small class="text-muted">
                                    Course Code: <?php echo htmlspecialchars($course['course_code'] ?? 'N/A'); ?> |
                                    Total Enrollments: <?php echo count($enrollments); ?>
                                </small>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo base_url($url.'/courses'); ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="feather icon-arrow-left"></i> Back to Courses
                                </a>
                            </div>
                        </div>
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
                                <h6>Enrollment Management</h6>
                                <p class="mb-0">Manage student enrollments for this course</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#enrollStudentModal">
                                    <i class="feather icon-user-plus"></i> Enroll Student
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollments List -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($enrollments)): ?>
                            <div class="text-center py-5">
                                <i class="feather icon-users" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3">No Enrollments</h4>
                                <p class="text-muted">No students are enrolled in this course yet.</p>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#enrollStudentModal">
                                    Enroll First Student
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="datatable table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Email</th>
                                            <th>Batch</th>
                                            <th>Progress</th>
                                            <th>Status</th>
                                            <th>Enrolled Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($enrollments as $enrollment): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($enrollment['student_name']); ?></strong>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($enrollment['student_email']); ?>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info"><?php echo htmlspecialchars($enrollment['batch'] ?? 'N/A'); ?></span>
                                                </td>
                                                <td>
                                                    <div class="progress" style="width: 100px;">
                                                        <div class="progress-bar" role="progressbar"
                                                             style="width: <?php echo htmlspecialchars($enrollment['progress_percentage'] ?? 0); ?>%"
                                                             aria-valuenow="<?php echo htmlspecialchars($enrollment['progress_percentage'] ?? 0); ?>"
                                                             aria-valuemin="0" aria-valuemax="100">
                                                            <?php echo htmlspecialchars($enrollment['progress_percentage'] ?? 0); ?>%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                    $status = $enrollment['status'] ?? 'enrolled';
                                                    $statusClass = 'success';
                                                    if ($status === 'completed') $statusClass = 'success';
                                                    elseif ($status === 'in_progress') $statusClass = 'warning';
                                                    elseif ($status === 'dropped') $statusClass = 'danger';
                                                    ?>
                                                    <span class="badge badge-<?php echo $statusClass; ?>">
                                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $status))); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($enrollment['enrolled_at'] ? date('M d, Y', strtotime($enrollment['enrolled_at'])) : 'N/A'); ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <!-- Update Status -->
                                                        <div class="dropdown">
                                                            <button class="btn btn-outline-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <i class="feather icon-settings"></i>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item" href="<?php echo base_url($url.'/courses/update_enrollment_status/'.$enrollment['id'].'/enrolled'); ?>">
                                                                    <i class="feather icon-play-circle"></i> Mark Enrolled
                                                                </a>
                                                                <a class="dropdown-item" href="<?php echo base_url($url.'/courses/update_enrollment_status/'.$enrollment['id'].'/in_progress'); ?>">
                                                                    <i class="feather icon-clock"></i> Mark In Progress
                                                                </a>
                                                                <a class="dropdown-item" href="<?php echo base_url($url.'/courses/update_enrollment_status/'.$enrollment['id'].'/completed'); ?>">
                                                                    <i class="feather icon-check-circle"></i> Mark Completed
                                                                </a>
                                                                <a class="dropdown-item" href="<?php echo base_url($url.'/courses/update_enrollment_status/'.$enrollment['id'].'/dropped'); ?>">
                                                                    <i class="feather icon-pause-circle"></i> Mark Dropped
                                                                </a>
                                                            </div>
                                                        </div>

                                                        <!-- Unenroll Student -->
                                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmUnenroll(<?php echo $enrollment['id']; ?>, '<?php echo htmlspecialchars(addslashes($enrollment['student_name'])); ?>')" title="Unenroll Student">
                                                            <i class="feather icon-user-minus"></i>
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

<!-- Enroll Student Modal -->
<div class="modal fade" id="enrollStudentModal" tabindex="-1" role="dialog" aria-labelledby="enrollStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="enrollStudentModalLabel">Enroll Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo base_url($url.'/courses/enroll_student'); ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">

                    <div class="form-group">
                        <label for="student_id">Select Student <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="student_id" name="student_id" required>
                            <option value="">Choose a student...</option>
                            <?php
                            // Get all students from the college
                            $students = $this->db_model->get_all(TABLE_STUDENT, [
                                "is_active" => 1,
                                "college_id" => $this->college['id']
                            ]);

                            foreach ($students as $student): ?>
                                <option value="<?php echo $student['id']; ?>">
                                    <?php echo htmlspecialchars($student['name']); ?> (<?php echo htmlspecialchars($student['email']); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php echo form_error('student_id', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Enroll Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Unenroll Confirmation Modal -->
<div class="modal fade" id="unenrollModal" tabindex="-1" role="dialog" aria-labelledby="unenrollModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="unenrollModalLabel">Confirm Unenroll</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to unenroll "<span id="studentName"></span>" from this course? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a id="unenrollBtn" href="#" class="btn btn-danger">Unenroll</a>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize Select2 for student dropdown
$(document).ready(function() {
    $('.select2').select2({
        placeholder: "Select a student",
        allowClear: true
    });
});

function confirmUnenroll(enrollmentId, studentName) {
    document.getElementById('studentName').textContent = studentName;
    document.getElementById('unenrollBtn').href = '<?php echo base_url($url.'/courses/unenroll_student/'); ?>' + enrollmentId;
    $('#unenrollModal').modal('show');
}
</script>