<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Learner</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <!-- <li class="breadcrumb-item">Principal</li> -->
                <li class="breadcrumb-item">Learner</li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php   } ?>
        <!-- <div class="card mb-4">
            <form method="get" action="<?php echo base_url('Purchase/Invoice'); ?>">
                <div class="card-body">
                    <div class="form-row align-items-center">
                        <div class="col-md my-2">
                            <label class="form-label">Search</label>
                            <input type="text" name="search" value="<?= $this->input->get("search") ?>" placeholder="Search Invoice No" class="form-control">
                        </div>
                        <div class="col-md my-2">
                            <label class="form-label">Customer</label>
                            <input type="text" name="customer_search" value="<?= $this->input->get("customer_search") ?>" placeholder="Search Customer" class="form-control">

                        </div>
                        <div class="col-md my-2">
                            <label class="form-label">Start date</label>
                            <input type="date" name="form_date" value="<?php echo $this->input->get('form_date') ? $this->input->get('form_date') : date('Y-m-d', strtotime('-7 days')); ?>" class="form-control">
                        </div>
                        <div class="col-md my-2">
                            <label class="form-label">End date</label>
                            <input type="date" name="to_date" value="<?php echo $this->input->get('to_date') ? $this->input->get('to_date') : date('Y-m-d'); ?>" class="form-control">
                        </div>
                        <div class="col-md col-xl-2 my-2">
                            <label class="form-label d-none d-md-block">&nbsp;</label>
                            <button class="btn btn-primary btn-block">Show</button>
                        </div>
                    </div>
                </div>
            </form>
        </div> -->
        <div class="card p-2">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Student Management</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStudentModal">
                    <i class="feather icon-plus"></i> Add Student
                </button>
            </div>

            <!-- Filter for Department and Batch -->
            <div class="card-body">
                <div class="form-row align-items-center">
                    <div class="col-md my-2">
                        <label class="form-label">Department</label>
                        <select class="form-control" id="department">
                            <option value="">All</option>
                            <?php if (!empty($departments)) {
                                foreach ($departments as $row) { ?>
                                    <option value="<?= $row['id'] ?>"><?= $row['name'] ?></option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="col-md my-2">
                        <label class="form-label">Batch</label>
                        <select class="form-control" id="batch">
                            <option value="">All</option>
                            <?php if (!empty($batches)) {
                                foreach ($batches as $row) { ?>
                                    <option value="<?= $row ?>"><?= $row ?></option>
                            <?php }
                            } ?>
                            }
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="card-datatable container table-responsive">
                <table id="student_table" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAllStudents"></th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Roll No</th>
                            <th>Department</th>
                            <th>Batch</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded via AJAX for server-side processing -->
                    </tbody>
                </table>
                <div class="row g-3">
                    <div class="col-auto">
                        <select class="form-select" id="groupSelect" aria-label="Select menu">
                            <option selected>Choose group</option>
                            <?php
                            if (!empty($groups)) {
                                foreach ($groups as $group_row) { ?>
                                    <option value="<?= $group_row['id'] ?>"><?= $group_row['name'] ?></option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="button" id="addToGroupBtn" class="btn btn-primary mb-3">Add to group</button>
                    </div>
                </div>
            </div>
        </div>
        <h4 class="font-weight-bold py-3 mb-0">Groups</h4>
        <div class="card p-2">
            <div style="display: flex; justify-content:space-between; align-items: center;
            border-bottom: 0 solid rgba(24, 28, 33, 0.13);
            border-color: rgba(24, 28, 33, 0.13);
            border-radius: 0.125rem 0.125rem 0 0; 
            border-bottom-width: 1px;">
                <h6 class="card-header" style="border:none">List of Groups</h6>
                <div>
                    <a href="<?php echo base_url($url . '/groups/add'); ?>" class="btn btn-primary mr-3">Add Group</a>
                </div>
            </div>
            <div class="card-datatable container table-responsive">
                <table id="mytable" class="datatables-demo table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Group Name</th>
                            <th>No.of.Students</th>
                            <!-- <th>Group Expiry</th> -->
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($memgroups)) {
                            $no = 0;
                            foreach ($memgroups as $row) {
                                $no++ ?>
                                <tr>
                                    <td> <?= $no; ?></td>
                                    <td><?php if (isset($row['name'])) {
                                            echo $row['name'];
                                        } else {
                                            echo "-";
                                        } ?>
                                    </td>
                                    <td> <?php
                                            if (isset($row['students'])) {
                                                echo $row['students'];
                                            } else {
                                                echo "-";
                                            }
                                            ?>
                                    </td>
                                    <td><?php if (isset($row['created_at'])) {
                                            echo $this->common->display_date($row['created_at']);
                                        } else {
                                            echo "-";
                                        } ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo base_url($url . '/groups/group_students/' . $row['id']); ?>" class="btn btn-info btn-sm"><i class="feather icon-users"></i>&nbsp;View Students </a>
                                        <a href="<?php echo base_url($url . '/groups/edit/' . $row['id']); ?>" class="btn btn-warning btn-sm"><i class="feather icon-edit"></i>&nbsp;Edit Group </a>
                                        <!-- <a href="<?php echo base_url($url . '/groups/delete_group/' . $row['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete?')"><i class="feather icon-trash"></i>&nbsp;Delete </a> -->
                                        <a href="javascript:void(0);" class="btn btn-danger btn-sm delete-group" data-id="<?= $row['id'] ?>"><i class="feather icon-trash"></i>&nbsp;Delete</a>
                                    </td>
                                </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>
                <!-- <div class="row g-3">
                    <div class="col-auto">
                        <select class="form-select" id="groupSelect" aria-label="Select menu">
                            <option selected>Choose Cource</option>
                            <?php
                            if (!empty($groups)) {
                                foreach ($groups as $group_row) { ?>
                                    <option value="<?= $group_row['id'] ?>"><?= $group_row['name'] ?></option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="button" id="addToGroupBtn" class="btn btn-primary mb-3">Add to group</button>
                    </div>
                </div> -->
            </div>
        </div>
        <!-- <h4 class="font-weight-bold py-3 mb-0">Added Groups of Cource</h4>
        <div class="card p-2">
            <div style="display: flex; justify-content:space-between; align-items: center;
                border-bottom: 0 solid rgba(24, 28, 33, 0.13);
                border-color: rgba(24, 28, 33, 0.13);
                border-radius: 0.125rem 0.125rem 0 0; 
                border-bottom-width: 1px;">
                <h6 class="card-header" style="border:none">List of Groups</h6>
                <div>
                    <a href="<?php echo base_url($url . '/groups/add'); ?>" class="btn btn-primary mr-3">Add Group</a>
                </div>
            </div>
            <div class="card-datatable container table-responsive">
                <table id="mytable" class="datatables-demo table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Group Name</th>
                            <th>No.of.Members</th>
                            <th>Group Expiry</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($memgroups)) {
                            $no = 0;
                            foreach ($memgroups as $row) {
                                $no++ ?>
                                <tr>
                                    <td> <?= $no; ?></td>
                                    <td><?php if (isset($row['name'])) {
                                            echo $row['name'];
                                        } else {
                                            echo "-";
                                        } ?>
                                    </td>
                                    <td> <?php
                                            if (isset($row['students'])) {
                                                echo $row['students'];
                                            } else {
                                                echo "-";
                                            }
                                            ?>
                                    </td>
                                    <td><?php if (isset($row['group_expiry'])) {
                                            echo $row['group_expiry'];
                                        } else {
                                            echo "-";
                                        } ?>
                                    </td>
                                    <td><?php if (isset($row['created_at'])) {
                                            echo $this->common->display_date($row['created_at']);
                                        } else {
                                            echo "-";
                                        } ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo base_url($url . '/groups/edit/' . $row['id']); ?>" class="btn btn-warning btn-sm"><i class="feather icon-edit"></i>&nbsp;Edit Group </a>
                                        <a href="<?php echo base_url($url . '/groups/delete_group/' . $row['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete?')"><i class="feather icon-trash"></i>&nbsp;Delete </a>
                                    </td>
                                </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>
            </div>
        </div> -->
    </div>
</div>
<div class="modal fade" id="resetpassword" tabindex="-1" aria-labelledby="resetpassword" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetpassword">Reset Password</h5>
            </div>
            <div class=" ml-4 m-2">
                <form action="<?= $post_url ?>" method="POST">
                    <input type="hidden" name="id" id="reset_id">
                    <div class="mb-3 p-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="text" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveVoucherBtn">Reset</button>
                </form>
            </div>
        </div>
        <div class="modal-footer">
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Check if DataTable is already initialized and destroy it
    if ($.fn.DataTable.isDataTable('#student_table')) {
        $('#student_table').DataTable().destroy();
        $('#student_table').empty(); // Clear any existing content
    }

    // Small delay to ensure DOM is ready
    setTimeout(function() {
        // Initialize DataTable
        $('#student_table').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": dashboardUrl + "students",
                "type": "GET",
                "data": function(d) {
                    // Add any additional data if needed
                    return d;
                }
            },
            "columns": [
                { "data": null, "orderable": false, "searchable": false, "render": function(data, type, row) {
                    return '<input type="checkbox" class="student-checkbox" value="' + row[0] + '">';
                }}, // Checkbox
                { "data": 0, "name": "id", "visible": false }, // ID (hidden)
                { "data": 1, "name": "name" }, // Name
                { "data": 2, "name": "email" }, // Email
                { "data": 3, "name": "phone" }, // Phone
                { "data": 4, "name": "roll_no" }, // Roll No
                { "data": 5, "name": "department" }, // Department
                { "data": 6, "name": "batch" }, // Batch
                { "data": 7, "orderable": false, "searchable": false } // Actions
            ],
            "pageLength": 10,
            "responsive": true,
            "order": [[1, 'asc']], // Order by name by default
            "language": {
                "processing": "Loading...",
                "search": "Search students:",
                "lengthMenu": "Show _MENU_ students per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ students",
                "infoEmpty": "No students found",
                "emptyTable": "No students available"
            },
            "initComplete": function() {
                console.log('DataTable initialized successfully');
            },
            "error": function(xhr, error, thrown) {
                console.error('DataTable error:', error, thrown);
            }
        });
    }, 100); // Small delay

    // Initialize Select2 after DataTable is ready
    setTimeout(function() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('.select2').select2({
                placeholder: "Select Department",
                allowClear: true,
                width: '100%'
            });

            // Re-initialize Select2 when modals are shown
            $('#addStudentModal, #editStudentModal').on('shown.bs.modal', function() {
                setTimeout(function() {
                    if (typeof $.fn.select2 !== 'undefined') {
                        $('.select2').each(function() {
                            var $element = $(this);
                            // Safely destroy if already initialized
                            if ($element.hasClass('select2-hidden-accessible')) {
                                try {
                                    $element.select2('destroy');
                                } catch (e) {
                                    // Ignore destroy errors
                                }
                            }
                            // Initialize
                            $element.select2({
                                placeholder: "Select Department",
                                allowClear: true,
                                width: '100%'
                            });
                        });
                    }
                }, 100);
            });

            console.log('Select2 initialized successfully');
        } else {
            console.error('Select2 library not loaded');
        }
    }, 200); // Small delay after DataTable
});
</script>

<!-- DataTable scripts (require jQuery) - Load BEFORE custom scripts -->
<script src="<?php echo base_url(''); ?>assets/faculty/libs/datatables/datatables.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>

<!-- Select2 (requires jQuery) -->
<script src="<?php echo base_url(''); ?>assets/faculty/libs/select2/select2.js"></script>

<!-- Custom scripts that depend on the above libraries -->
<script src="<?php echo base_url(''); ?>assets/faculty/js/pages/tables_datatables.js"></script>
<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentLabel">Add Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo base_url('Dashboard/students'); ?>" method="POST">
                <input type="hidden" name="action" value="create">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="roll_no" class="form-label">Roll Number</label>
                            <input type="text" class="form-control" id="roll_no" name="roll_no">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="department" class="form-label">Department *</label>
                            <select class="form-control select2" id="department" name="department" required>
                                <option value="">Select Department</option>
                                <?php if (!empty($departments)) {
                                    foreach ($departments as $dept) { ?>
                                        <option value="<?= $dept['id'] ?>"><?= $dept['name'] ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="batch" class="form-label">Batch</label>
                            <input type="text" class="form-control" id="batch" name="batch" placeholder="e.g., 2023-2024">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password *</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="editStudentLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editStudentLabel">Edit Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo base_url('Dashboard/students'); ?>" method="POST">
                <input type="hidden" name="action" value="update">
                <input type="hidden" id="edit_student_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_name" class="form-label">Name *</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="edit_phone_number" name="phone_number">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_roll_no" class="form-label">Roll Number</label>
                            <input type="text" class="form-control" id="edit_roll_no" name="roll_no">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_department" class="form-label">Department *</label>
                            <select class="form-control select2" id="edit_department" name="department" required>
                                <option value="">Select Department</option>
                                <?php if (!empty($departments)) {
                                    foreach ($departments as $dept) { ?>
                                        <option value="<?= $dept['id'] ?>"><?= $dept['name'] ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_batch" class="form-label">Batch</label>
                            <input type="text" class="form-control" id="edit_batch" name="batch" placeholder="e.g., 2023-2024">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Student</button>
                </div>
            </form>
        </div>
    </div>
</div>


<style>
/* Fix Select2 dropdown z-index issue in modals */
.select2-container--open .select2-dropdown {
    z-index: 1060 !important; /* Above Bootstrap modals */
}

/* Ensure Select2 dropdown is properly positioned within modal */
.modal .select2-container {
    z-index: auto;
}

/* Style for selected table rows */
#student_table tbody tr.selected {
    background-color: #e3f2fd !important;
    border: 2px solid #2196f3 !important;
}

#student_table tbody tr.selected:hover {
    background-color: #bbdefb !important;
}
</style>

<script>
$(document).ready(function() {
    // Set PHP variables as JavaScript variables
    var baseUrl = '<?php echo base_url(); ?>';
    var urlSegment = '<?php echo $url ?? 'admin'; ?>';
    var dashboardUrl = baseUrl + 'Dashboard/';

    // Student edit and delete functions
    window.editStudent = function(id) {
    // Fetch student data via AJAX and populate edit modal
    $.get(dashboardUrl + 'get_student/' + id)
        .done(function(data) {
            const student = JSON.parse(data);
            $('#edit_student_id').val(student.id);
            $('#edit_name').val(student.name);
            $('#edit_email').val(student.email);
            $('#edit_phone_number').val(student.phone);
            $('#edit_roll_no').val(student.roll_no);
            $('#edit_department').val(student.department).trigger('change');
            $('#edit_batch').val(student.batch);

            $('#editStudentModal').modal('show');
        })
        .fail(function() {
            alert('Failed to load student data');
        });
    };

    window.deleteStudent = function(id) {
    if (confirm('Are you sure you want to delete this student?')) {
        const form = $('<form>', {
            'method': 'POST',
            'action': dashboardUrl + 'students'
        });

        form.append($('<input>', {
            'type': 'hidden',
            'name': 'action',
            'value': 'delete'
        }));

        form.append($('<input>', {
            'type': 'hidden',
            'name': 'id',
            'value': id
        }));

        $('body').append(form);
        form.submit();
    }

    // Group management functionality
    let studentTable;
    let selectedStudents = [];

    // Initialize student table reference after DataTable is created
    setTimeout(function() {
        studentTable = $('#student_table').DataTable();
    }, 200);

    // Handle select all checkbox
    $('#selectAllStudents').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.student-checkbox').prop('checked', isChecked).trigger('change');
    });

    // Handle individual student checkbox changes
    $(document).on('change', '.student-checkbox', function() {
        const studentId = $(this).val();

        if ($(this).is(':checked')) {
            if (!selectedStudents.includes(studentId)) {
                selectedStudents.push(studentId);
            }
        } else {
            selectedStudents = selectedStudents.filter(id => id !== studentId);
            // Uncheck "select all" if any individual checkbox is unchecked
            $('#selectAllStudents').prop('checked', false);
        }

        console.log('Selected students:', selectedStudents);
    });

    // Handle "Add to group" button click
    $('#addToGroupBtn').on('click', function() {
        const groupId = $('#groupSelect').val();

        if (!groupId || groupId === 'Choose group') {
            alert('Please select a group first.');
            return;
        }

        if (selectedStudents.length === 0) {
            alert('Please select at least one student.');
            return;
        }

        if (confirm('Are you sure you want to add ' + selectedStudents.length + ' student(s) to the selected group?')) {
            // Make AJAX call to add students to group
            $.ajax({
                url: baseUrl + urlSegment + '/groups/addMemberstoGroup',
                type: 'POST',
                data: {
                    group_id: groupId,
                    student_ids: selectedStudents
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        // Clear selection and refresh table
                        selectedStudents = [];
                        $('.student-checkbox').prop('checked', false);
                        $('#selectAllStudents').prop('checked', false);
                        if (studentTable) {
                            studentTable.ajax.reload();
                        }
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('An error occurred while adding students to the group. Please try again.');
                }
            });
        }
    });

    // Handle group deletion
    $(document).on('click', '.delete-group', function() {
        const groupId = $(this).data('id');
        const groupName = $(this).closest('tr').find('td:nth-child(2)').text().trim();

        if (confirm('Are you sure you want to delete the group "' + groupName + '"? This action cannot be undone.')) {
            // Make AJAX call to delete group
            $.ajax({
                url: baseUrl + urlSegment + '/groups/deleteGroup',
                type: 'POST',
                data: {
                    id: groupId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        alert(response.message);
                        // Refresh the page to update the groups table
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('An error occurred while deleting the group. Please try again.');
                }
            });
        }
    });
});
</script>