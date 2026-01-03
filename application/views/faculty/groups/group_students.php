<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Students in <?php echo htmlspecialchars($group['name']); ?> Group</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/groups'); ?>">Music Groups</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($group['name']); ?> Students</li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Group Info -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="feather icon-users mr-2"></i><?php echo htmlspecialchars($group['name']); ?> Music Group</h6>
                                <p class="mb-0"><?php echo htmlspecialchars($group['description'] ?? 'No description available'); ?></p>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?php echo base_url($url.'/groups/add_students/'.$group['id']); ?>" class="btn btn-primary">
                                    <i class="feather icon-user-plus"></i> Add Students
                                </a>
                                <a href="<?php echo base_url($url.'/groups'); ?>" class="btn btn-secondary">
                                    <i class="feather icon-arrow-left"></i> Back to Groups
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Students List -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">

                        <?php if (empty($group_students)): ?>
                            <div class="text-center py-5">
                                <i class="feather icon-user-x" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3">No Students in Group</h4>
                                <p class="text-muted">This music group doesn't have any students yet.</p>
                                <a href="<?php echo base_url($url.'/groups/group_students/'.$group['id']); ?>" class="btn btn-primary">
                                    <i class="feather icon-user-plus"></i> Add Students to Group
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="datatable table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone Number</th>
                                            <th>Registration No</th>
                                            <th>Batch</th>
                                            <th>Department</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($group_students as $student): ?>
                                            <tr>
                                                <td><?php echo $student['id']; ?></td>
                                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                                <td><?php echo htmlspecialchars($student['email']); ?></td>
                                                <td><?php echo htmlspecialchars($student['phone'] ?? '-'); ?></td>
                                                <td><?php echo htmlspecialchars($student['roll_no'] ?? '-'); ?></td>
                                                <td><?php echo htmlspecialchars($student['batch'] ?? '-'); ?></td>
                                                <td>
                                                    <?php
                                                    if (isset($student['department']) && $student['department']) {
                                                        $dept = $this->db_model->get_row(TABLE_DEPARTMENT, ["id" => $student['department']]);
                                                        echo $dept ? htmlspecialchars($dept['name']) : 'N/A';
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="<?php echo base_url($url.'/groups/remove_student/'.$group['id'].'/'.$student['id']); ?>"
                                                       onclick="return confirm('Remove <?php echo htmlspecialchars($student['name']); ?> from this music group?')"
                                                       class="btn btn-sm btn-outline-danger" title="Remove from Group">
                                                        <i class="feather icon-user-minus"></i> Remove
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <p class="text-muted">Total Students: <strong><?php echo count($group_students); ?></strong></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</script>
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
                    showExportModal('copy', dt, 'musiccollege-group-students-' + new Date().toISOString().split('T')[0]);
                }
            },
            {
                text: 'CSV',
                action: function(e, dt, button, config) {
                    showExportModal('csv', dt, 'musiccollege-group-students-' + new Date().toISOString().split('T')[0]);
                }
            },
            {
                text: 'Excel',
                action: function(e, dt, button, config) {
                    showExportModal('excel', dt, 'musiccollege-group-students-' + new Date().toISOString().split('T')[0]);
                }
            },
            {
                text: 'PDF',
                action: function(e, dt, button, config) {
                    showExportModal('pdf', dt, 'musiccollege-group-students-' + new Date().toISOString().split('T')[0]);
                }
            },
            {
                text: 'Print',
                action: function(e, dt, button, config) {
                    showExportModal('print', dt, 'Music College Group Students Data');
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