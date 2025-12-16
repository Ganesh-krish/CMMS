<!-- faculty/dashboard.php -->
<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Faculty Dashboard</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item">Dashboard</li>
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
            <!-- Total Students Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= $total_students ?></h4>
                                <p class="text-muted mb-0">Total Students</p>
                            </div>
                            <div class="bg-primary rounded p-3">
                                <i class="feather icon-users text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Courses Card -->
            <div class="col-lg-3 col-md-6 col-sm-12 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="mb-0"><?= $total_courses ?></h4>
                                <p class="text-muted mb-0">My Courses</p>
                            </div>
                            <div class="bg-success rounded p-3">
                                <i class="feather icon-book text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Department-wise Batch Count Table -->
        <div class="row mb-4">
            <div class="col-lg-12 col-md-12">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-header-title mb-0">Department-wise Batch Count</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Department</th>
                                        <?php foreach ($department_batch_table['years'] as $year): ?>
                                            <th class="text-center">Batch <?= $year ?></th>
                                        <?php endforeach; ?>
                                        <th class="text-center bg-light">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($department_batch_table['departments'] as $dept): ?>
                                        <tr>
                                            <td><strong><?= $dept['name'] ?></strong></td>
                                            <?php foreach ($department_batch_table['years'] as $year): ?>
                                                <td class="text-center"><?= isset($dept['batches'][$year]) ? $dept['batches'][$year] : 0 ?></td>
                                            <?php endforeach; ?>
                                            <td class="text-center bg-light"><strong><?= $dept['total'] ?></strong></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <!-- Total row -->
                                    <tr class="bg-light">
                                        <td><strong>Total</strong></td>
                                        <?php 
                                        $batch_totals = [];
                                        foreach ($department_batch_table['years'] as $year) {
                                            $year_total = 0;
                                            foreach ($department_batch_table['departments'] as $dept) {
                                                $year_total += isset($dept['batches'][$year]) ? $dept['batches'][$year] : 0;
                                            }
                                            $batch_totals[$year] = $year_total;
                                        }
                                        ?>
                                        <?php foreach ($department_batch_table['years'] as $year): ?>
                                            <td class="text-center"><strong><?= $batch_totals[$year] ?></strong></td>
                                        <?php endforeach; ?>
                                        <td class="text-center"><strong><?= array_sum($batch_totals) ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Quick Actions Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-header-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-4">
                                <a href="<?= base_url($url . '/course/new') ?>" class="btn btn-outline-info btn-block p-3">
                                    <i class="feather icon-book mb-2" style="font-size: 24px;"></i>
                                    <div>Add New Course</div>
                                </a>
                            </div>
                            <div class="col-6 mb-4">
                                <a href="<?= $manage_student_url ?>" class="btn btn-outline-warning btn-block p-3">
                                    <i class="feather icon-user-plus mb-2" style="font-size: 24px;"></i>
                                    <div>Manage Students</div>
                                </a>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info mb-0">
                                    <div class="d-flex">
                                        <i class="feather icon-info mr-2" style="font-size: 20px;"></i>
                                        <div>
                                            <h6 class="alert-heading mb-1">Need help?</h6>
                                            <p class="mb-0">Check out the documentation or DrillU Team for assistance with any features.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>