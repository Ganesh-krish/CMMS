<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Course Students Overview</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/courses'); ?>">Courses</a></li>
                <li class="breadcrumb-item active">Students Overview</li>
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
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="card-title">
                            <i class="feather icon-users" style="font-size: 2rem; color: #007bff;"></i>
                        </div>
                        <h4 class="mb-0"><?php echo $total_enrollments; ?></h4>
                        <small class="text-muted">Total Enrollments</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="card-title">
                            <i class="feather icon-check-circle" style="font-size: 2rem; color: #28a745;"></i>
                        </div>
                        <h4 class="mb-0"><?php echo $active_enrollments; ?></h4>
                        <small class="text-muted">Active Enrollments</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="card-title">
                            <i class="feather icon-clock" style="font-size: 2rem; color: #ffc107;"></i>
                        </div>
                        <h4 class="mb-0"><?php echo $total_enrollments - $active_enrollments; ?></h4>
                        <small class="text-muted">Inactive Enrollments</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="card-title">
                            <i class="feather icon-book" style="font-size: 2rem; color: #6c757d;"></i>
                        </div>
                        <h4 class="mb-0">
                            <?php
                            $unique_courses = array_unique(array_column($enrollments, 'course_id'));
                            echo count($unique_courses);
                            ?>
                        </h4>
                        <small class="text-muted">Courses with Enrollments</small>
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
                                <h6>Enrollment Overview</h6>
                                <p class="mb-0">View all student enrollments across courses</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?php echo base_url($url.'/courses'); ?>" class="btn btn-outline-secondary">
                                    <i class="feather icon-arrow-left"></i> Back to Courses
                                </a>
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
                                <p class="text-muted">There are no student enrollments to display.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Student</th>
                                            <th>Course</th>
                                            <th>Batch</th>
                                            <th>Department</th>
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
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($enrollment['student_name']); ?></strong>
                                                        <br>
                                                        <small class="text-muted"><?php echo htmlspecialchars($enrollment['student_email']); ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($enrollment['course_title']); ?></strong>
                                                        <br>
                                                        <small class="text-muted"><?php echo htmlspecialchars(substr($enrollment['course_description'], 0, 50)); ?><?php echo strlen($enrollment['course_description']) > 50 ? '...' : ''; ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-info"><?php echo htmlspecialchars($enrollment['batch'] ?? 'N/A'); ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-secondary"><?php echo htmlspecialchars($enrollment['department'] ?? 'N/A'); ?></span>
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
                                                    $statusIcon = 'check-circle';
                                                    if ($status === 'completed') {
                                                        $statusClass = 'success';
                                                        $statusIcon = 'check-circle';
                                                    } elseif ($status === 'in_progress') {
                                                        $statusClass = 'warning';
                                                        $statusIcon = 'clock';
                                                    } elseif ($status === 'dropped') {
                                                        $statusClass = 'danger';
                                                        $statusIcon = 'pause-circle';
                                                    } elseif ($status === 'enrolled') {
                                                        $statusClass = 'info';
                                                        $statusIcon = 'user-check';
                                                    }
                                                    ?>
                                                    <span class="badge badge-<?php echo $statusClass; ?>">
                                                        <i class="feather icon-<?php echo $statusIcon; ?>"></i>
                                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $status))); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($enrollment['enrolled_at'] ? date('M d, Y', strtotime($enrollment['enrolled_at'])) : 'N/A'); ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <!-- View Course Details -->
                                                        <a href="<?php echo base_url($url.'/courses/enrollments/'.$enrollment['course_id']); ?>" class="btn btn-outline-primary btn-sm" title="View Course Enrollments">
                                                            <i class="feather icon-eye"></i>
                                                        </a>

                                                        <!-- Update Status -->
                                                        <div class="dropdown">
                                                            <button class="btn btn-outline-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                <i class="feather icon-settings"></i>
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a class="dropdown-item" href="<?php echo base_url($url.'/courses/update_enrollment_status/'.$enrollment['id'].'/enrolled'); ?>">
                                                                    <i class="feather icon-user-check"></i> Mark Enrolled
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