<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Course Enrollments - <?php echo $course['name']; ?></h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/courses') ?>">Courses</a></li>
                <li class="breadcrumb-item">Enrollments</li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php   } ?>

        <div class="card p-2">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <h6 class="mb-0">Enrollments for: <?php echo $course['name']; ?> (<?php echo $course['code']; ?>)</h6>
                    <small class="text-muted">Total Enrolled: <?php echo count($enrollments); ?> students</small>
                </div>
                <div>
                    <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addEnrollmentModal">
                        <i class="feather icon-user-plus"></i> Enroll Student
                    </button>
                    <a href="<?= base_url($url.'/courses') ?>" class="btn btn-secondary btn-sm">
                        <i class="feather icon-arrow-left"></i> Back to Courses
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table id="enrollmentsTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Student Name</th>
                            <th>Email</th>
                            <th>Enrollment Date</th>
                            <th>Status</th>
                            <th>Progress</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($enrollments)) {
                            $i = 1;
                            foreach ($enrollments as $enrollment) { ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $enrollment['student_name']; ?></td>
                                    <td><?php echo $enrollment['student_email']; ?></td>
                                    <td><?php echo $this->common->display_date($enrollment['enrolled_at']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $enrollment['status'] == 'active' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($enrollment['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar" style="width: <?php echo $enrollment['progress_percentage'] ?? 0; ?>%" aria-valuenow="<?php echo $enrollment['progress_percentage'] ?? 0; ?>" aria-valuemin="0" aria-valuemax="100">
                                                <?php echo $enrollment['progress_percentage'] ?? 0; ?>%
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-flex gap-1" style="flex-wrap: wrap;">
                                        <button class="btn btn-sm btn-info" onclick="viewProgress(<?php echo $enrollment['id']; ?>)">
                                            <i class="feather icon-eye"></i> View Progress
                                        </button>
                                        <?php if ($enrollment['status'] == 'active'): ?>
                                        <button class="btn btn-sm btn-warning" onclick="updateStatus(<?php echo $enrollment['id']; ?>, 'inactive')">
                                            <i class="feather icon-pause"></i> Deactivate
                                        </button>
                                        <?php else: ?>
                                        <button class="btn btn-sm btn-success" onclick="updateStatus(<?php echo $enrollment['id']; ?>, 'active')">
                                            <i class="feather icon-play"></i> Activate
                                        </button>
                                        <?php endif; ?>
                                        <button class="btn btn-sm btn-danger" onclick="unenrollStudent(<?php echo $enrollment['id']; ?>)">
                                            <i class="feather icon-user-minus"></i> Unenroll
                                        </button>
                                    </td>
                                </tr>
                        <?php }
                        } else { ?>
                            <tr>
                                <td colspan="7" class="text-center">No students enrolled in this course yet</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Enrollment Modal -->
<div class="modal fade" id="addEnrollmentModal" tabindex="-1" aria-labelledby="addEnrollmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEnrollmentModalLabel">Enroll Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($url . '/courses/enroll_student') ?>" method="POST">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Select Student *</label>
                        <select class="form-control" id="student_id" name="student_id" required>
                            <option value="">Select Student</option>
                            <?php
                            // Get students from the same department as the course
                            $course_dept = $course['department'];
                            $available_students = $this->db_model->get_all(TABLE_STUDENT, [
                                "is_active" => 1,
                                "college_id" => $this->session->userdata($url)['college_id'] ?? 1,
                                "department" => $course_dept
                            ]);

                            foreach ($available_students as $student) {
                                // Check if student is not already enrolled
                                $existing_enrollment = $this->db_model->get_row(TABLE_COURSE_ENROLLMENTS, [
                                    "student_id" => $student['id'],
                                    "course_id" => $course_id,
                                    "is_active" => 1
                                ]);

                                if (!$existing_enrollment) {
                                    echo '<option value="' . $student['id'] . '">' . $student['name'] . ' (' . $student['email'] . ')</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="enrollment_notes" class="form-label">Enrollment Notes</label>
                        <textarea class="form-control" id="enrollment_notes" name="enrollment_notes" rows="3" placeholder="Optional notes about enrollment"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Enroll Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function viewProgress(enrollmentId) {
    // For now, just show an alert. In a real implementation, this could show detailed progress
    alert('Detailed progress view would be implemented here for enrollment ID: ' + enrollmentId);
}

function updateStatus(enrollmentId, newStatus) {
    var action = newStatus === 'active' ? 'activate' : 'deactivate';
    if (confirm('Are you sure you want to ' + action + ' this enrollment?')) {
        window.location.href = '<?= base_url($url . "/courses/update_enrollment_status/") ?>' + enrollmentId + '/' + newStatus;
    }
}

function unenrollStudent(enrollmentId) {
    if (confirm('Are you sure you want to unenroll this student from the course? This action cannot be undone.')) {
        window.location.href = '<?= base_url($url . "/courses/unenroll_student/") ?>' + enrollmentId;
    }
}

// Initialize DataTable
$(document).ready(function() {
    $('#enrollmentsTable').DataTable({
        "pageLength": 25,
        "order": [[ 3, "desc" ]] // Order by enrollment date descending
    });
});
</script>

