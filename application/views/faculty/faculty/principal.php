<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Administrator Management</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item">Administrator</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Administrator Table -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Administrators</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPrincipalModal">
                    <i class="feather icon-plus"></i> Add Administrator
                </button>
            </div>
            <div class="card-body">
                <div class="card-datatable container table-responsive">
                    <table id="administratorTable" class="datatables-demo table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($principal)) {
                                $no = 0;
                                foreach ($principal as $row) {
                                    $no++ ?>
                                    <tr>
                                        <td> <?= $no; ?></td>
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
                                        <td><?php if (isset($row['created_at'])) {
                                                echo $this->common->display_date($row['created_at']);
                                            } else {
                                                echo "-";
                                            } ?>
                                        </td>
                                        <td class="d-flex gap-1" style="flex-wrap: wrap;">
                                            <a href="<?= base_url($url.'/principal/edit/'.$row['id']) ?>" class="btn btn-sm btn-info">Edit</a>
                                            <a href="<?= base_url($url.'/principal/delete/'.$row['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this administrator?');">Delete</a>
                                            <button type="button" onclick="model_open(<?= $row['id'] ?>)" class="btn btn-warning btn-sm">
                                                Reset Password
                                            </button>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Assistant Administrator Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Assistant Administrators</h5>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addVicePrincipalModal">
                    <i class="feather icon-plus"></i> Add Assistant Administrator
                </button>
            </div>
            <div class="card-body">
                <div class="card-datatable container table-responsive">
                    <table id="vicePrincipalTable" class="datatables-demo table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($vice_principal)) {
                                $no = 0;
                                foreach ($vice_principal as $row) {
                                    $no++ ?>
                                    <tr>
                                        <td> <?= $no; ?></td>
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
                                        <td><?php if (isset($row['created_at'])) {
                                                echo $this->common->display_date($row['created_at']);
                                            } else {
                                                echo "-";
                                            } ?>
                                        </td>
                                        <td class="d-flex gap-1" style="flex-wrap: wrap;">
                                            <a href="<?= base_url($url.'/principal/edit_vice_principal/'.$row['id']) ?>" class="btn btn-sm btn-info">Edit</a>
                                            <a href="<?= base_url($url.'/principal/delete_vice_principal/'.$row['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this assistant administrator?');">Delete</a>
                                            <button type="button" onclick="resetPasswordModal(<?= $row['id'] ?>)" class="btn btn-warning btn-sm">
                                                Reset Password
                                            </button>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Department Administrator Table -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Department Administrators</h5>
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#addDepartmentAdminModal">
                    <i class="feather icon-plus"></i> Add Department Administrator
                </button>
            </div>
            <div class="card-body">
                <div class="card-datatable container table-responsive">
                    <table id="departmentAdminTable" class="datatables-demo table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Department</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($department_admins)) {
                                $no = 0;
                                foreach ($department_admins as $row) {
                                    $no++ ?>
                                    <tr>
                                        <td> <?= $no; ?></td>
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
                                        <td><?php if (isset($row['phone'])) {
                                                echo $row['phone'];
                                            } else {
                                                echo "-";
                                            } ?>
                                        </td>
                                        <td><?php
                                            if (isset($row['department'])) {
                                                // Get department name
                                                $dept_name = "-";
                                                foreach ($departments as $dept) {
                                                    if ($dept['id'] == $row['department']) {
                                                        $dept_name = $dept['name'];
                                                        break;
                                                    }
                                                }
                                                echo $dept_name;
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
                                        <td class="d-flex gap-1" style="flex-wrap: wrap;">
                                            <a href="<?= base_url($url.'/principal/edit_hod/'.$row['id']) ?>" class="btn btn-sm btn-info">Edit</a>
                                            <a href="<?= base_url($url.'/principal/delete_hod/'.$row['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this department administrator?');">Delete</a>
                                            <button type="button" onclick="resetPasswordModalHOD(<?= $row['id'] ?>)" class="btn btn-warning btn-sm">
                                                Reset Password
                                            </button>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal for Department Administrators -->
<div class="modal fade" id="resetPasswordHOD" tabindex="-1" aria-labelledby="resetPasswordHOD" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordHOD">Reset Department Administrator Password</h5>
            </div>
            <div class=" ml-4 m-2">
                <form  action="<?= base_url($url.'/hod/reset_password') ?>" method="POST">
                    <input type="hidden" name="id" id="reset_hod_id">
                    <div class="mb-3 p-4">
                        <label for="hod_password" class="form-label">Password</label>
                        <input type="text" class="form-control" id="hod_password" name="password" required>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Reset</button>
                </form>
            </div>
        </div>
        <div class="modal-footer">
        </div>
    </div>
</div>

<!-- Reset Password Modal for Administrators -->
<div class="modal fade" id="resetpassword" tabindex="-1" aria-labelledby="resetpassword" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetpassword">Reset Password</h5>
            </div>
            <div class=" ml-4 m-2">
                <form  action="<?= $post_url ?>" method="POST">
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

<!-- Reset Password Modal for Assistant Administrators -->
<div class="modal fade" id="resetPasswordVicePrincipal" tabindex="-1" aria-labelledby="resetPasswordVicePrincipal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordVicePrincipal">Reset Assistant Administrator Password</h5>
            </div>
            <div class=" ml-4 m-2">
                <form  action="<?= $post_url ?>" method="POST">
                    <input type="hidden" name="id" id="reset_vice_principal_id">
                    <div class="mb-3 p-4">
                        <label for="vice_principal_password" class="form-label">Password</label>
                        <input type="text" class="form-control" id="vice_principal_password" name="password" required>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Reset</button>
                </form>
            </div>
        </div>
        <div class="modal-footer">
        </div>
    </div>
</div>

<!-- Add Administrator Modal -->
<div class="modal fade" id="addPrincipalModal" tabindex="-1" aria-labelledby="addPrincipalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPrincipalLabel">Add Administrator</h5>
            </div>
            <div class="ml-4 m-2">
                <form action="<?= $add_url ?>" method="POST">
                    <div class="p-4">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone_number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Assistant Administrator Modal -->
<div class="modal fade" id="addVicePrincipalModal" tabindex="-1" aria-labelledby="addVicePrincipalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addVicePrincipalLabel">Add Assistant Administrator</h5>
            </div>
            <div class="ml-4 m-2">
                <form action="<?= $add_vice_principal_url ?>" method="POST">
                    <div class="p-4">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone_number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Department Administrator Modal -->
<div class="modal fade" id="addDepartmentAdminModal" tabindex="-1" aria-labelledby="addDepartmentAdminLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addDepartmentAdminLabel">Add Department Administrator</h5>
            </div>
            <div class="ml-4 m-2">
                <form action="<?= $add_department_admin_url ?>" method="POST">
                    <div class="p-4">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone_number" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Department</label>
                            <select name="department" class="form-control select2" required>
                                <option value="">Select Department</option>
                                <?php
                                if (!empty($departments)) {
                                    foreach ($departments as $dept) { ?>
                                        <option value="<?= $dept['id'] ?>"><?= $dept['name'] ?></option>
                                    <?php }
                                } else {
                                    // Fallback: Show default departments if none loaded from DB
                                    ?>
                                    <option value="1">Computer Science</option>
                                    <option value="2">Information Technology</option>
                                    <option value="3">Electronics</option>
                                    <option value="4">Mechanical Engineering</option>
                                    <option value="5">Civil Engineering</option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function model_open(id){
        var resetModel = new bootstrap.Modal(document.getElementById('resetpassword'));
        document.getElementById("reset_id").value = id;
        resetModel.show();

        $('#resetpassword').on('shown.bs.modal', function() {
            $('#password').focus();
        });
    }

    function resetPasswordModal(id){
        var resetModel = new bootstrap.Modal(document.getElementById('resetPasswordVicePrincipal'));
        document.getElementById("reset_vice_principal_id").value = id;
        resetModel.show();

        $('#resetPasswordVicePrincipal').on('shown.bs.modal', function() {
            $('#vice_principal_password').focus();
        });
    }

    function resetPasswordModalHOD(id){
        var resetModel = new bootstrap.Modal(document.getElementById('resetPasswordHOD'));
        document.getElementById("reset_hod_id").value = id;
        resetModel.show();

        $('#resetPasswordHOD').on('shown.bs.modal', function() {
            $('#hod_password').focus();
        });
    }
</script>

<script>
$(document).ready(function() {
    // Initialize Administrator table
    $('#administratorTable').DataTable({
        "pageLength": 10,
        "responsive": true,
        "order": [[ 0, "asc" ]],
        "language": {
            "search": "Search Administrators:",
            "lengthMenu": "Show _MENU_ administrators per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ administrators"
        }
    });

    // Initialize Assistant Administrator table
    $('#vicePrincipalTable').DataTable({
        "pageLength": 10,
        "responsive": true,
        "order": [[ 0, "asc" ]],
        "language": {
            "search": "Search Assistant Administrators:",
            "lengthMenu": "Show _MENU_ assistant administrators per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ assistant administrators"
        }
    });

    // Initialize Department Administrator table
    $('#departmentAdminTable').DataTable({
        "pageLength": 10,
        "responsive": true,
        "order": [[ 0, "asc" ]],
        "language": {
            "search": "Search Department Administrators:",
            "lengthMenu": "Show _MENU_ department administrators per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ department administrators"
        }
    });

    // Initialize Select2 only when modal is shown
    $('#addDepartmentAdminModal').on('shown.bs.modal', function() {
        // Small delay to ensure modal is fully rendered
        setTimeout(function() {
            $('.select2').select2({
                placeholder: "Select Department",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#addDepartmentAdminModal')
            });
        }, 100);
    });

    // Clean up Select2 when modal is hidden
    $('#addDepartmentAdminModal').on('hidden.bs.modal', function() {
        if ($('.select2').hasClass('select2-hidden-accessible')) {
            $('.select2').select2('destroy');
        }
    });
});
</script>

<script src="<?= base_url('') ?>assets/faculty/libs/datatables/datatables.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="<?= base_url('') ?>assets/faculty/js/pages/forms_selects.js"></script>
<script src="<?= base_url('') ?>assets/faculty/libs/select2/select2.js"></script>

<style>
/* Fix Select2 dropdown z-index issue in modals */
.select2-container--open .select2-dropdown {
    z-index: 1060 !important; /* Above Bootstrap modals */
}

/* Ensure Select2 dropdown is properly positioned within modal */
.modal .select2-container {
    z-index: auto;
}

/* Additional fix for Select2 in Bootstrap 5 modals */
.select2-dropdown {
    z-index: 1060 !important;
}
</style>