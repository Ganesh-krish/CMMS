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
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addStudentsModal">
                                    <i class="feather icon-user-plus"></i> Add Students
                                </button>
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

<!-- Add Students Modal -->
<div class="modal fade" id="addStudentsModal" tabindex="-1" role="dialog" aria-labelledby="addStudentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentsModalLabel">Add Students to <?php echo htmlspecialchars($group['name']); ?> Group</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addStudentsForm" method="POST" action="<?php echo base_url($url.'/groups/add_students_to_group/'.$group['id']); ?>">
                <div class="modal-body">
                    <?php if (!empty($available_students)): ?>
                        <div class="form-group">
                            <label><i class="feather icon-users mr-2"></i>Select Students to Add:</label>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                <?php foreach ($available_students as $student): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="student_ids[]" value="<?php echo $student['id']; ?>" id="student_<?php echo $student['id']; ?>">
                                        <label class="form-check-label" for="student_<?php echo $student['id']; ?>">
                                            <strong><?php echo htmlspecialchars($student['name']); ?></strong>
                                            (<?php echo htmlspecialchars($student['email']); ?> - <?php echo htmlspecialchars($student['roll_no']); ?>)
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <small class="form-text text-muted">Select one or more students to add to this music group.</small>
                        </div>
                        <div class="mt-3">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" onclick="return validateSelection()">
                                <i class="feather icon-user-plus"></i> Add Selected Students
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="feather icon-user-check" style="font-size: 3rem; color: #28a745;"></i>
                            <h5 class="mt-3">All Students Added</h5>
                            <p class="text-muted">All available students are already members of this music group.</p>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function validateSelection() {
    var selectedStudents = document.querySelectorAll('input[name="student_ids[]"]:checked');

    if (selectedStudents.length === 0) {
        alert('Please select at least one student to add to the group.');
        return false;
    }

    return confirm('Add ' + selectedStudents.length + ' student(s) to this music group?');
}

// Select/Deselect All functionality (optional enhancement)
function toggleAllStudents(checked) {
    var checkboxes = document.querySelectorAll('input[name="student_ids[]"]');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = checked;
    });
}
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
                extend: 'copy',
                filename: 'musiccollege-data',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'csv',
                filename: 'musiccollege-data',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'excel',
                filename: 'musiccollege-data',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'pdf',
                filename: 'musiccollege-data',
                exportOptions: {
                    columns: ':not(:last-child)'
                }
            },
            {
                extend: 'print',
                title: 'Music College Data',
                exportOptions: {
                    columns: ':not(:last-child)'
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