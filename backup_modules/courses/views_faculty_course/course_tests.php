<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Course Tests</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/course') ?>">Courses</a></li>
                <li class="breadcrumb-item">Tests for <?= $course['name'] ?></li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>
        
        <!-- Tests Already Added to the Course -->
        <div class="card p-2 mb-4">
            <div style="display: flex; justify-content:space-between; align-items: center;
            border-bottom: 0 solid rgba(24, 28, 33, 0.13);
            border-color: rgba(24, 28, 33, 0.13);
            border-radius: 0.125rem 0.125rem 0 0; 
            border-bottom-width: 1px;">
                <h6 class="card-header" style="border:none">Tests in <?= $course['name'] ?></h6>
            </div>
            <div class="card-datatable container table-responsive">
                <table id="courseTestsTable" class="datatable datatables-demo table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Test Title</th>
                            <th>Duration (mins)</th>
                            <th>Module</th>
                            <th>Attempts Allowed</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        if (!empty($course_tests)) { $no=0;
                            foreach ($course_tests as $test) { $no++; ?>
                                <tr>
                                    <td><?= $no; ?></td>   
                                    <td><?= $test['title'] ?? '-'; ?></td>   
                                    <td><?= $test['duration'] ?? '-'; ?></td>   
                                    <td><?php if (isset($test['module'])) {
                                                echo $test['module'];
                                                } else {
                                                    echo "-";
                                                } ?>
                                            </td>
                                    <td><?= $test['no_of_attempts'] ?? '-'; ?></td>   
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="<?= base_url($url.'/course/remove_test_from_course/'.$course['id'].'/'.$test['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this test from the course?');"><i class="feather icon-trash"></i>&nbsp;Remove </a>
                                        </div>
                                    </td>
                                </tr>
                        <?php }
                        } else { ?>
                            <tr>
                                <td colspan="7" class="text-center">No tests added to this course yet.</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Available Tests to Add -->
        <div class="card p-2">
            <div style="display: flex; justify-content:space-between; align-items: center;
            border-bottom: 0 solid rgba(24, 28, 33, 0.13);
            border-color: rgba(24, 28, 33, 0.13);
            border-radius: 0.125rem 0.125rem 0 0; 
            border-bottom-width: 1px;">
                <h6 class="card-header" style="border:none">Available Tests</h6>
            </div>
            <div class="card-body">
                <form action="<?= base_url($url.'/course/add_test_to_course') ?>" method="POST">
                    <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                    <div class="card-datatable container table-responsive">
                        <table id="availableTestsTable" class="datatable datatables-demo table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="5%"><input type="checkbox" id="selectAll"></th>
                                    <th>Test Title</th>
                                    <th>Duration (mins)</th>
                                    <th>Module</th>
                                    <th>Attempts Allowed</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                                if (!empty($available_tests)) {
                                    foreach ($available_tests as $test) { ?>
                                        <tr>
                                            <td><input type="checkbox" name="test_ids[]" value="<?= $test['id'] ?>"></td>
                                            <td><?= $test['title'] ?? '-'; ?></td>   
                                            <td><?= $test['duration'] ?? '-'; ?></td>   
                                            <td><?php if (isset($test['module'])) {
                                                echo $test['module'];
                                                } else {
                                                    echo "-";
                                                } ?>
                                            </td>
                                            <td><?= $test['no_of_attempts'] ?? '-'; ?></td>   
                                        </tr>
                                <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No additional tests available. Please create tests first.</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if (!empty($available_tests)) { ?>
                        <div class="text-right mt-3">
                            <button type="submit" class="btn btn-primary">Add Selected Tests to Course</button>
                        </div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('') ?>assets/faculty/libs/datatables/datatables.js"></script>
<script>
$(document).ready(function() {
    // Destroy existing DataTable instance if it exists, then reinitialize
    if ($.fn.DataTable.isDataTable('#courseTestsTable')) {
        $('#courseTestsTable').DataTable().destroy();
    }
    $('#courseTestsTable').DataTable({
        "pageLength": 10,
        "responsive": true
    });
    
    if ($.fn.DataTable.isDataTable('#availableTestsTable')) {
        $('#availableTestsTable').DataTable().destroy();
    }
    $('#availableTestsTable').DataTable({
        "pageLength": 10,
        "responsive": true
    });
    
    // Select all checkboxes
    $('#selectAll').click(function() {
        $('input:checkbox').prop('checked', this.checked);
    });
});
</script>