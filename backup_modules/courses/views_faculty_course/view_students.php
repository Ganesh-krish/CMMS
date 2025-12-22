<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Course Students</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/course') ?>">Courses</a></li>
                <li class="breadcrumb-item">Students in <?= $course['name'] ?></li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>
        
        <div class="card">
            <div style="display: flex; justify-content:space-between; align-items: center;
            border-bottom: 0 solid rgba(24, 28, 33, 0.13);
            border-color: rgba(24, 28, 33, 0.13);
            border-radius: 0.125rem 0.125rem 0 0; 
            border-bottom-width: 1px;">
                <h6 class="card-header" style="border:none">Students Enrolled in <?= $course['name'] ?></h6>
                <div class="card-header-elements ml-md-auto">
                    <a href="<?= base_url($url.'/course') ?>" class="btn btn-secondary btn-sm"><i class="feather icon-arrow-left"></i>&nbsp;Back to Courses</a>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h5 class="card-title">Course Details</h5>
                                <p class="mb-1"><strong>Code:</strong> <?= $course['course_code'] ?? '-' ?></p>
                                <p class="mb-1"><strong>Name:</strong> <?= $course['name'] ?? '-' ?></p>
                                <p class="mb-1"><strong>Expiry:</strong> <?= isset($course['course_expiry']) ? $this->common->display_date($course['course_expiry']) : '-' ?></p>
                                <p class="mb-1"><strong>Description:</strong> <?= $course['description'] ?? '-' ?></p>
                                <!-- start date and end date -->
                                <p class="mb-1"><strong>Start Date:</strong> <?= isset($course['start_date']) ? $this->common->display_date($course['start_date']) : '-' ?></p>
                                <p class="mb-1"><strong>End Date:</strong> <?= isset($course['end_date']) ? $this->common->display_date($course['end_date']) : '-' ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <div class="alert alert-info">
                            <p class="mb-0">
                                <i class="feather icon-info mr-2"></i>
                                This list shows all students enrolled in this course through various methods (direct addition, groups, and departments).
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table id="students-table" class="datatable datatables-demo table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Registration No</th>
                                <th>Phone Number</th>
                                <th>Department</th>
                                <th>Enrollment Source</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($students)): 
                                $no = 0;
                                foreach ($students as $student): 
                                    $no++; ?>
                                <tr>
                                    <td><?= $no ?></td>
                                    <td><?= $student['name'] ?? '-' ?></td>
                                    <td><?= $student['email'] ?? '-' ?></td>
                                    <td><?= $student['registration_number'] ?? '-' ?></td>
                                    <td><?= $student['phone'] ?? '-' ?></td>
                                    <td>
                                        <?php 
                                        if (isset($student['department_name'])) {
                                            echo $student['department_name'];
                                        } elseif (isset($student['department'])) {
                                            // Assuming department might be an ID and needs to be resolved
                                            $dept = $this->db_model->get_row(TABLE_DEPARTMENT, ['id' => $student['department']]);
                                            echo $dept ? $dept['name'] : '-';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td><?= $student['source'] ?? 'Direct Addition' ?></td>
                                </tr>
                            <?php endforeach; 
                            else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No students enrolled in this course</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="<?= base_url('') ?>assets/faculty/libs/datatables/datatables.js"></script>
<script src="<?= base_url('') ?>assets/faculty/js/pages/forms_selects.js"></script>
<script src="<?= base_url('') ?>assets/faculty/libs/bootstrap-select/bootstrap-select.js"></script>
<script src="<?= base_url('') ?>assets/faculty/libs/select2/select2.js"></script>

<script>
$(document).ready(function() {
    // Check if DataTable is already initialized before initializing
    if ($.fn.DataTable.isDataTable('#students-table')) {
        $('#students-table').DataTable().destroy();
    }
    
    $('#students-table').DataTable({
        "responsive": true,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "pageLength": 10,
        "pagingType": "full_numbers",
        "dom": '<"top"Bfl>rt<"bottom"ip><"clear">',
        "buttons": [
            {
                text: 'Copy',
                action: function(e, dt, button, config) {
                    showExportModal('copy', dt, 'musiccollege-students-' + new Date().toISOString().split('T')[0]);
                }
            },
            {
                text: 'CSV',
                action: function(e, dt, button, config) {
                    showExportModal('csv', dt, 'musiccollege-students-' + new Date().toISOString().split('T')[0]);
                }
            },
            {
                text: 'Excel',
                action: function(e, dt, button, config) {
                    showExportModal('excel', dt, 'musiccollege-students-' + new Date().toISOString().split('T')[0]);
                }
            },
            {
                text: 'PDF',
                action: function(e, dt, button, config) {
                    showExportModal('pdf', dt, 'musiccollege-students-' + new Date().toISOString().split('T')[0]);
                }
            },
            {
                text: 'Print',
                action: function(e, dt, button, config) {
                    showExportModal('print', dt, 'Music College Students Data');
                }
            }
        ],
        "language": {
            "paginate": {
                "first": '<i class="feather icon-chevrons-left"></i>',
                "previous": '<i class="feather icon-chevron-left"></i>',
                "next": '<i class="feather icon-chevron-right"></i>',
                "last": '<i class="feather icon-chevrons-right"></i>'
            }
        }
    });
});
</script>