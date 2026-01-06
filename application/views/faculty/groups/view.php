<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Music Groups</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item active">Music Groups</li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php   } ?>

        <!-- Statistics Cards - Commented out to hide from UI -->
        <!--
        <div class="row mb-4">
            <!-- Total Groups -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="feather icon-users text-primary mr-2" style="font-size: 24px;"></i>
                            <h4 class="text-primary mb-0"><?php echo $stats['total_groups']; ?></h4>
                        </div>
                        <p class="mb-0 text-muted">Total Groups</p>
                    </div>
                </div>
            </div>

            <!-- Total Students in Groups -->
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <i class="feather icon-user-check text-success mr-2" style="font-size: 24px;"></i>
                            <h4 class="text-success mb-0"><?php echo $stats['total_students_in_groups']; ?></h4>
                        </div>
                        <p class="mb-0 text-muted">Students in Groups</p>
                    </div>
                </div>
            </div>

            <!-- Group-wise Student Count -->
            <?php if (!empty($groups)): ?>
                <?php foreach ($groups as $group): ?>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="d-flex align-items-center justify-content-center mb-2">
                                    <i class="feather icon-music text-info mr-2" style="font-size: 24px;"></i>
                                    <h4 class="text-info mb-0"><?php echo $group['student_count']; ?></h4>
                                </div>
                                <p class="mb-0 text-muted"><?php echo htmlspecialchars($group['name']); ?></p>
                                <small class="text-muted">Students</small>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        -->

        <!-- Actions Bar -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Music Groups Management</h6>
                                <p class="mb-0">Create and manage music groups for students</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <?php if (!isset($current_user_role) || $current_user_role != ROLE_STAFF): ?>
                                <a href="<?php echo base_url($url.'/groups/add'); ?>" class="btn btn-success">
                                    <i class="feather icon-plus"></i> Add Music Group
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Groups List -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($groups)): ?>
                            <div class="text-center py-5">
                                <i class="feather icon-users" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3">No Music Groups</h4>
                                <p class="text-muted">There are no music groups to display.</p>
                                <a href="<?php echo base_url($url.'/groups/add'); ?>" class="btn btn-primary">
                                    Add First Music Group
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="datatable table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Group Name</th>
                                            <th>Description</th>
                                            <th>Students</th>
                                            <th>Status</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($groups as $group): ?>
                                            <tr>
                                                <td><?php echo $group['id']; ?></td>
                                                <td><?php echo htmlspecialchars($group['name']); ?></td>
                                                <td><?php echo htmlspecialchars($group['description'] ?? '-'); ?></td>
                                                <td>
                                                    <a href="<?php echo base_url($url.'/groups/group_students/'.$group['id']); ?>" class="btn btn-sm btn-outline-info" title="View Students">
                                                        <i class="feather icon-users"></i> <?php echo $group['student_count'] ?? 0; ?> Students
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?php echo $group['is_active'] ? 'success' : 'secondary'; ?>">
                                                        <?php echo $group['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('d M Y', strtotime($group['created_at'])); ?></td>
                                                <td>
                                                    <?php if (!isset($current_user_role) || $current_user_role != ROLE_STAFF): ?>
                                                    <div class="btn-group" role="group">
                                                        <a href="<?php echo base_url($url.'/groups/edit/'.$group['id']); ?>" class="btn btn-sm btn-success" title="Edit Group">
                                                            <i class="feather icon-edit"></i>
                                                        </a>
                                                        <a href="#" onclick="confirmDelete(<?php echo $group['id']; ?>, '<?php echo htmlspecialchars($group['name']); ?>')" class="btn btn-sm btn-danger" title="Delete Group">
                                                            <i class="feather icon-trash"></i>
                                                        </a>
                                                    </div>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    // Set modal content
    document.getElementById('deleteModalLabel').textContent = 'Delete Music Group';
    document.getElementById('deleteModalBody').innerHTML = 'Are you sure you want to delete the music group "' + name + '"? This action cannot be undone.';

    // Store the delete URL
    window.deleteUrl = '<?php echo base_url($url.'/groups/delete/'); ?>' + id;

    // Show modal using Bootstrap's JavaScript API
    var deleteModalElement = document.getElementById('deleteModal');
    var modal = new bootstrap.Modal(deleteModalElement);
    modal.show();

    // Manually handle modal dismissal for delete modal
    setTimeout(function() {
        var cancelBtn = document.querySelector('#deleteModal .btn-secondary');
        var closeBtn = document.querySelector('#deleteModal .close');

        if (cancelBtn) {
            cancelBtn.onclick = function() {
                modal.hide();
            };
        }

        if (closeBtn) {
            closeBtn.onclick = function() {
                modal.hide();
            };
        }
    }, 100);
}

function proceedDelete() {
    if (window.deleteUrl) {
        window.location.href = window.deleteUrl;
    }
}
</script>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="deleteModalBody">
                Are you sure you want to delete this music group? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="proceedDelete()">Delete</button>
            </div>
        </div>
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

<script src="<?= base_url('') ?>assets/libs/datatables/datatables.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
<script src="<?= base_url('') ?>assets/faculty/js/pages/forms_selects.js"></script>
<script src="<?= base_url('') ?>assets/libs/bootstrap-select/bootstrap-select.js"></script>
<script src="<?= base_url('') ?>assets/libs/select2/select2.js"></script>