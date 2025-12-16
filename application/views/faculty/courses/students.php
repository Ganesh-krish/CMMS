<!-- courses/students.php -->
<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Course Enrollments</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/courses') ?>">Courses</a></li>
                <li class="breadcrumb-item active">Students</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Stats Cards Row -->
        <div class="row mb-4">
            <!-- Total Enrollments Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= $total_enrollments ?></h4>
                                <p class="text-muted mb-0">Total Enrollments</p>
                            </div>
                            <div class="bg-primary rounded p-3">
                                <i class="feather icon-users text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Enrollments Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= $active_enrollments ?></h4>
                                <p class="text-muted mb-0">Active Enrollments</p>
                            </div>
                            <div class="bg-success rounded p-3">
                                <i class="feather icon-check-circle text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enrollments Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-header-title mb-0">All Course Enrollments</h5>
                <a href="<?= base_url($url . '/report') ?>" class="btn btn-primary btn-sm">
                    <i class="feather icon-bar-chart-2"></i> Detailed Reports
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover card-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Course</th>
                                <th>Enrollment Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($enrollments)): ?>
                                <?php $i = 1; foreach ($enrollments as $enrollment): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td>
                                            <div>
                                                <strong><?= $enrollment['student_name'] ?></strong><br>
                                                <small class="text-muted"><?= $enrollment['student_email'] ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= $enrollment['course_title'] ?></strong><br>
                                                <small class="text-muted">
                                                    <?php
                                                    if (isset($enrollment['department'])) {
                                                        $dept = $this->db_model->get_row(TABLE_DEPARTMENT, ['id' => $enrollment['department']]);
                                                        echo $dept ? $dept['name'] : 'N/A';
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                    ?> • Batch: <?= $enrollment['batch'] ?? 'N/A' ?>
                                                </small>
                                            </div>
                                        </td>
                                        <td><?= date('M d, Y h:i A', strtotime($enrollment['enrolled_at'])) ?></td>
                                        <td>
                                            <?php if ($enrollment['is_active']): ?>
                                                <span class="badge badge-pill badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-pill badge-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button type="button" class="btn btn-sm btn-icon btn-outline-primary dropdown-toggle hide-arrow" data-toggle="dropdown">
                                                    <i class="feather icon-more-vertical"></i>
                                                </button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="<?= base_url($url . '/report/student_detail/' . $enrollment['student_id']) ?>">
                                                        <i class="feather icon-user mr-2"></i> Student Details
                                                    </a>
                                                    <a class="dropdown-item" href="<?= base_url($url . '/courses/enrollments/' . $enrollment['course_id']) ?>">
                                                        <i class="feather icon-book mr-2"></i> Course Enrollments
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No course enrollments found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


