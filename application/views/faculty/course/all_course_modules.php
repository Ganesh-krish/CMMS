<?php
$courseModeClass = ($course['course_mode'] == 2) ? 'gamification-mode' : 'normal-mode';
?>
<!-- View file: application/views/course/modules.php -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="layout-content <?= $courseModeClass?>">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Course Modules</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/allcourses') ?>">All Courses</a></li>
                <li class="breadcrumb-item active">Modules</li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>
        
        <div class="card p-2 mb-4">
            <div style="display: flex; justify-content:space-between; align-items: center;
            border-bottom: 0 solid rgba(24, 28, 33, 0.13);
            border-color: rgba(24, 28, 33, 0.13);
            border-radius: 0.125rem 0.125rem 0 0; 
            border-bottom-width: 1px;">
                <h6 class="card-header" style="border:none">Course Details</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Course Code:</strong> <?= $course['course_code'] ?></p>
                        <p><strong>Course Name:</strong> <?= $course['name'] ?></p>
                        <a href="<?= base_url($url.'/course/export_performance_report/'.$course['id']) ?>" class="btn btn-success">
                            <i class="fas fa-file-csv"></i> Export Participation Report
                        </a>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Description:</strong> <?= $course['description'] ?></p>
                        <!-- <p><strong>Expiry:</strong> <?= $this->common->display_date($course['course_expiry']) ?></p> -->
                         <?php if ($course['course_mode'] == 2): ?>
                            <p><strong>Module Mode :</strong>
                            <span class="badge badge-warning ml-2">Gamification</span>
                         <?php endif; ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Add Module Form -->
        
        <!-- Modules List -->
        <div class="card p-2">
            <div style="display: flex; justify-content:space-between; align-items: center;
            border-bottom: 0 solid rgba(24, 28, 33, 0.13);
            border-color: rgba(24, 28, 33, 0.13);
            border-radius: 0.125rem 0.125rem 0 0; 
            border-bottom-width: 1px;">
                <h6 class="card-header" style="border:none">Course Modules</h6>
            </div>
            <div class="card-body">
                <?php if (empty($modules)): ?>
                    <div class="alert alert-info">
                        No modules added to this course yet. Use the form above to add modules.
                    </div>
                <?php else: ?>
                    <div class="accordion" id="accordionModules">
                        <?php foreach ($modules as $index => $module): ?>
                            <div class="card mb-2">
                                <div class="card-header" id="heading<?= $module['id'] ?>">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <button class="btn btn-link" type="button" data-toggle="collapse" 
                                                    data-target="#collapse<?= $module['id'] ?>" aria-expanded="<?= ($index === 0) ? 'true' : 'false' ?>" 
                                                    aria-controls="collapse<?= $module['id'] ?>">
                                                <?= $module['name'] ?>
                                            </button>
                                        </h5>
                                        <div class="btn-group">
                                            <a href="<?= base_url($url.'/allcourses/module_tests/'.$course['id'].'/'.$module['id']) ?>" 
                                               class="btn btn-sm btn-info">
                                                <i class="feather icon-list"></i> Manage Tests
                                            </a>
                                            <!-- <a href="<?= base_url($url.'/course/edit_module/'.$course['id'].'/'.$module['id']) ?>" 
                                               class="btn btn-sm btn-warning">
                                                <i class="feather icon-edit"></i> Edit
                                            </a>
                                            <a href="<?= base_url($url.'/course/delete_module/'.$course['id'].'/'.$module['id']) ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete this module?');">
                                                <i class="feather icon-trash"></i> Delete
                                            </a> -->
                                        </div>
                                    </div>
                                </div>
                                <div id="collapse<?= $module['id'] ?>" class="collapse <?= ($index === 0) ? 'show' : '' ?>" 
                                     aria-labelledby="heading<?= $module['id'] ?>" data-parent="#accordionModules">
                                    <!-- <div class="card-body">
                                        <p><strong>Description:</strong> <?= $module['description'] ?></p>
                                        
                                        <?php if (isset($module_tests[$module['id']]) && !empty($module_tests[$module['id']])): ?>
                                            <h6 class="mt-3">Tests in this module:</h6>
                                            <table class="table table-bordered table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Test Name</th>
                                                        <th>Start Date</th>
                                                        <th>End Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($module_tests[$module['id']] as $test): ?>
                                                        <tr>
                                                            <td><?= $test['name'] ?></td>
                                                            <td>
                                                                <?= (!empty($test['start_date'])) ? $this->common->display_date($test['start_date']) : 'Not set' ?>
                                                            </td>
                                                            <td>
                                                                <?= (!empty($test['end_date'])) ? $this->common->display_date($test['end_date']) : 'Not set' ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php else: ?>
                                            <div class="alert alert-info mt-3">
                                                No tests added to this module yet. Click "Manage Tests" to add tests.
                                            </div>
                                        <?php endif; ?>
                                    </div> -->
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .gamification-mode {
    background: linear-gradient(to bottom, #E9F9F0 0%, #D3D3D3 50%, #B3E6CB 100%);
}

.gamification-mode .card {
    border-left: 6px solid #62D493;
    box-shadow: 0 0 8px rgba(240, 173, 78, 0.2);
}

.badge-warning {
    background-color: #62D493;
    color: #fff;
    font-size: 0.8rem;
    font-weight: bold;
    /* border-radius: 12px; */
    padding: 4px 10px;
}
</style>