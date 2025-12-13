<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Students</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <!-- <li class="breadcrumb-item">Principal</li> -->
                <li class="breadcrumb-item">Students</li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php   } ?>
        <!-- <div class="card mb-4">
            <form method="get" action="<?= base_url('Purchase/Invoice') ?>">
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
                            <th><input type="checkbox" id="select_all"></th>
                            <th>S.No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Registration No</th>
                            <th>Department</th>
                            <th>Batch</th>
                            <th>Joining Date</th>
                            <th>Expire Date</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($staff)) {
                            $no = 0;
                            foreach ($staff as $row) {
                                $no++ ?>
                                <tr>
                                    <td></td>
                                    <td data-id="<?php if (isset($row['id'])) {
                                                        echo $row['id'];
                                                    } ?>"> <?= $no; ?></td>
                                    <td><?php if (isset($row['name'])) {
                                            echo $row['name'];
                                        } else {
                                            echo "-";
                                        } ?>
                                    </td>
                                    <td><?php if (isset($row['email'])) {
                                            echo $row['email'];
                                        } else {
                                            echo "-";
                                        } ?>
                                    </td>
                                    <td><?php if (isset($row['phone_number'])) {
                                            echo $row['phone_number'];
                                        } else {
                                            echo "-";
                                        } ?>
                                    </td>
                                    <td><?php if (isset($row['registration_number'])) {
                                            echo $row['registration_number'];
                                        } else {
                                            echo "-";
                                        } ?>
                                    </td>
                                    <td><?php if (isset($row['department'])) {
                                            echo $row['department'];
                                        } else {
                                            echo "-";
                                        } ?>
                                    </td>

                                    <td><?php if (isset($row['batch'])) {
                                            echo $row['batch'];
                                        } else {
                                            echo "-";
                                        } ?>
                                    </td>

                                    <td><?php if (isset($row['joining_date'])) {
                                            echo $this->common->display_date($row['joining_date']);
                                        } else {
                                            echo "-";
                                        } ?>
                                    </td>
                                    <td><?php if (isset($row['expire_date'])) {
                                            echo $this->common->display_date($row['expire_date']);
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
                                        <button type="button" onclick="model_open(<?= $row['id'] ?>)" class="btn btn-warning btn-sm"><i class="feather icon-edit"></i>&nbsp;Reset Password </button>
                                        <a href="<?= base_url($url.'/course/student_overall_test_report/'.$row['id']) ?>" 
                                               class="btn btn-success btn-sm">
                                                <i class="feather icon-bar-chart-2"></i> Results
                                            </a>
                                    </td>
                                </tr>
                        <?php }
                        } ?>
                    </tbody>
                </table>
                <div class="row g-3">
                    <div class="col-auto">
                        <select class="form-select" id="groupSelect" aria-label="Select menu">
                            <option selected>Choose group</option>
                            <?php
                            if (!empty($groups)) {
                                foreach ($groups as $group_row) { ?>
                                    <option value="<?= $group_row['id'] ?>"><?= $group_row['group_name'] ?></option>
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
                    <a href="<?= base_url($url . '/groups/add') ?>" class="btn btn-primary mr-3">Add Group</a>
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
                                    <td><?php if (isset($row['group_name'])) {
                                            echo $row['group_name'];
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
                                        <a href="<?= base_url($url . '/groups/group_students/' . $row['id']) ?>" class="btn btn-info btn-sm"><i class="feather icon-users"></i>&nbsp;View Students </a>
                                        <a href="<?= base_url($url . '/groups/edit/' . $row['id']) ?>" class="btn btn-warning btn-sm"><i class="feather icon-edit"></i>&nbsp;Edit Group </a>
                                        <!-- <a href="<?= base_url($url . '/groups/delete_group/' . $row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete?')"><i class="feather icon-trash"></i>&nbsp;Delete </a> -->
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
                                    <option value="<?= $group_row['id'] ?>"><?= $group_row['group_name'] ?></option>
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
                    <a href="<?= base_url($url . '/groups/add') ?>" class="btn btn-primary mr-3">Add Group</a>
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
                                    <td><?php if (isset($row['group_name'])) {
                                            echo $row['group_name'];
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
                                        <a href="<?= base_url($url . '/groups/edit/' . $row['id']) ?>" class="btn btn-warning btn-sm"><i class="feather icon-edit"></i>&nbsp;Edit Group </a>
                                        <a href="<?= base_url($url . '/groups/delete_group/' . $row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete?')"><i class="feather icon-trash"></i>&nbsp;Delete </a>
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
        var table = $('#student_table').DataTable({
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
            columnDefs: [{
                orderable: false,
                className: 'select-checkbox', // DataTables auto adds checkboxes
                targets: 0
            }],
            select: {
                style: 'multi',
                selector: 'td:first-child '
            },
            order: [
                [1, 'asc']
            ]
        });

        // filter  if department is selected or  batch is selected using query string
        var url = new URL(window.location.href);
        var department = url.searchParams.get('department');
        var batch = url.searchParams.get('batch');
        if (department) {
            $('#department').val(department);
        }
        if (batch) {
            $('#batch').val(batch);
        }

        $('#department').on('change', function() {
            var department = $(this).val();
            if(!department){
                var url = new URL(window.location.href);
                url.searchParams.delete('department');
                window.location.href = url;
            }else {
                var url = new URL(window.location.href);
                url.searchParams.set('department', department);
                window.location.href = url;
            }
        });

        $('#batch').on('change', function() {
            var batch = $(this).val();
            if(!batch){
            var url = new URL(window.location.href);
            url.searchParams.delete('batch');
            window.location.href = url;
            }else {
                var url = new URL(window.location.href);
                url.searchParams.set('batch', batch);
                window.location.href = url;

            }
        });


        // Handle "Select All" checkbox
        $('#select_all').on('click', function() {
            if (this.checked) {
                table.rows().select(); // Select all rows
            } else {
                table.rows().deselect(); // Deselect all rows
            }
        });

        table.on('select deselect', function() {
            var allSelected = table.rows({
                selected: true
            }).count() === table.rows().count();
            $('#select_all').prop('checked', allSelected);
        });

        $('.delete-group').on('click', function (e) {
            e.preventDefault(); // Prevent default anchor behavior

            const groupId = $(this).data('id');

            if (confirm('Are you sure you want to delete?')) {
                $.ajax({
                    url: "<?= base_url($url . '/groups/delete_group/') ?>",
                    type: 'POST', 
                    data: { id: groupId },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                        alert(response.message);
                        location.reload();
                        } else {
                            alert(response.message || 'Deletion failed.');
                        }
                    },
                    error: function () {
                        alert('AnError occurred while deleting the group.');
                    }
                });
            }
        });
        $('#addToGroupBtn').click(function() {
            let selectedData = table.rows({
                selected: true
            }).nodes();
            let studentIds = [];

            // Extract Student IDs from DataTable
            $(selectedData).each(function() {
                let studentId = $(this).find('td:eq(1)').attr('data-id'); // Get from 'data-id'
                if (studentId) {
                    studentIds.push(studentId);
                }
            });



            let groupId = $('#groupSelect').val(); // Get selected group ID

            if (studentIds.length === 0) {
                alert("Please select at least one student.");
                return;
            }

            if (groupId === "" || groupId === null) {
                alert("Please select a group.");
                return;
            }



            $.ajax({
                url: "<?= base_url($url . '/groups/addMemberstoGroup/') ?>",
                type: "POST",
                data: {
                    student_ids: studentIds,
                    group_id: groupId
                },
                dataType: "json",
                success: function(response) {
                    alert(response.message);
                    location.reload();
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText); 
                    alert("Error: " + xhr.responseText);
                }
            });
        });
    });


    function model_open(id) {
        var resetModel = new bootstrap.Modal(document.getElementById('resetpassword'));
        document.getElementById("reset_id").value = id;
        resetModel.show();

        $('#resetpassword').on('shown.bs.modal', function() {
            $('#password').focus();
        });
    }
</script>

<script src="<?= base_url('') ?>assets/faculty/libs/datatables/datatables.js"></script>
<script src="<?= base_url('') ?>assets/faculty/js/pages/tables_datatables.js"></script>

<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<!-- Select Plugin for Row Selection -->
<script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
<!-- <script src="<?= base_url('') ?>assets/faculty/js/pages/forms_selects.js"></script>
<script src="<?= base_url('') ?>assets/faculty/libs/bootstrap-select/bootstrap-select.js"></script>
<script src="<?= base_url('') ?>assets/faculty/libs/select2/select2.js"></script> -->