<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Groups</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <!-- <li class="breadcrumb-item">Principal</li> -->
                <li class="breadcrumb-item">Groups</li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php   } ?>
        <div class="card p-2">
            <div style="display: flex; justify-content:space-between; align-items: center;
            border-bottom: 0 solid rgba(24, 28, 33, 0.13);
            border-color: rgba(24, 28, 33, 0.13);
            border-radius: 0.125rem 0.125rem 0 0; 
            border-bottom-width: 1px;">
                <h6 class="card-header" style="border:none">List of Groups</h6>
                <div>
                    <a href="<?= base_url($url.'/groups/add') ?>" class="btn btn-primary mr-3">Add Groupss</a>
                    </button>
                </div>
            </div>
            <div class="card-datatable container table-responsive">
                <table id="mytable" class="datatables-demo table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Group Name</th>
                            <th>Description</th>
                            <th>Group Expiry</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        if (!empty($groups)) { $no=0;
                            // var_dump($groups);die;
                            foreach ($groups as $row) {  $no++?>
                                <tr>
                                    <td> <?= $no;?></td>   
                                    <td><?php if (isset($row['name'])) {
                                            echo $row['name'];
                                        } else {
                                            echo "-";
                                        } ?>
                                    </td>   
                                    <td> <?php 
                                    if (isset($row['group_description'])) {
                                        echo $row['group_description'];
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
                                        <a href="<?= base_url($url.'/groups/edit/'.$row['id']) ?>" class="btn btn-warning btn-sm" ><i class="feather icon-edit"></i>&nbsp;Edit Group </a>
                                        <a href="<?= base_url($url.'/groups/delete_group/'.$row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete?')"><i class="feather icon-trash"></i>&nbsp;Delete </a>
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
<!-- Create Course Modal for add cource -->
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCourseModalLabel">Add New Course</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= isset($course)?base_url($url."/cource/edit/".$cource['id']):base_url($url."/cource/add/$college_id")?>" method="post">
                    <div class="form-group">
                        <label for="course_code">Course Code</label>
                        <input type="text" class="form-control" id="course_code" name="course_code" required>
                    </div>
                    <div class="form-group">
                        <label for="course_name">Course Name</label>
                        <input type="text" class="form-control" id="course_name" name="course_name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="course_expiry">Course_expiry</label>
                        <input type="number" class="form-control" id="course_expiry" name="course_expiry" required>
                    </div>
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select class="form-control" id="department" name="department" required>
                            <option value="">Select Department</option>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Mathematics">Mathematics</option>
                            <option value="Physics">Physics</option>
                            <option value="Biology">Biology</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Course</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCourseModalLabel">Edit Course</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('faculty/update_course') ?>" method="POST">
                    <input type="hidden" id="edit_course_id" name="course_id">

                    <div class="form-group">
                        <label for="edit_course_code">Course Code</label>
                        <input type="text" class="form-control" id="edit_course_code" name="course_code" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_course_name">Course Name</label>
                        <input type="text" class="form-control" id="edit_course_name" name="course_name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_description">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_duration">Course Expiry</label>
                        <input type="date" class="form-control" id="edit_duration" name="duration" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_department">Department</label>
                        <select class="form-control" id="edit_department" name="department" required>
                            <option value="Computer Science">Computer Science</option>
                            <option value="Mathematics">Mathematics</option>
                            <option value="Physics">Physics</option>
                            <option value="Biology">Biology</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Update Course</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </form>
            </div>
        </div>
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
<script src="<?= base_url('') ?>assets/faculty/js/pages/forms_selects.js"></script>
<script src="<?= base_url('') ?>assets/faculty/libs/bootstrap-select/bootstrap-select.js"></script>
<script src="<?= base_url('') ?>assets/faculty/libs/select2/select2.js"></script>