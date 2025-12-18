<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Course Modules</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/courses'); ?>">Courses</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($course['name']); ?> - Modules</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Course Info -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5><?php echo htmlspecialchars($course['name']); ?></h5>
                                <p class="mb-1"><?php echo htmlspecialchars($course['description']); ?></p>
                                <small class="text-muted">
                                    Course Code: <?php echo htmlspecialchars($course['course_code'] ?? 'N/A'); ?> |
                                    Created: <?php echo htmlspecialchars($course['created_at'] ? date('M d, Y', strtotime($course['created_at'])) : 'N/A'); ?>
                                </small>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="<?php echo base_url($url.'/courses'); ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="feather icon-arrow-left"></i> Back to Courses
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Bar -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Module Management</h6>
                                <p class="mb-0">Manage course modules and lessons</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModuleModal">
                                    <i class="feather icon-plus"></i> Add Module
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modules List -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($modules)): ?>
                            <div class="text-center py-5">
                                <i class="feather icon-layers" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3">No Modules</h4>
                                <p class="text-muted">There are no modules in this course yet.</p>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addModuleModal">
                                    Add First Module
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order</th>
                                            <th>Module Name</th>
                                            <th>Description</th>
                                            <th>Lessons</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($modules as $module): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge badge-info"><?php echo htmlspecialchars($module['order']); ?></span>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($module['name']); ?></strong>
                                                </td>
                                                <td>
                                                    <small><?php echo htmlspecialchars(substr($module['description'], 0, 100)); ?><?php echo strlen($module['description']) > 100 ? '...' : ''; ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-success">Lessons Available</span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <!-- Lessons -->
                                                        <a href="<?php echo base_url($url.'/courses/lessons/'.$course_id.'/'.$module['id']); ?>" class="btn btn-outline-primary btn-sm" title="View Lessons">
                                                            <i class="feather icon-file-text"></i>
                                                        </a>

                                                        <!-- Edit Module -->
                                                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="editModule(<?php echo $module['id']; ?>, '<?php echo htmlspecialchars(addslashes($module['name'])); ?>', '<?php echo htmlspecialchars(addslashes($module['description'])); ?>', <?php echo $module['order']; ?>)" title="Edit Module">
                                                            <i class="feather icon-edit"></i>
                                                        </button>

                                                        <!-- Delete Module -->
                                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDeleteModule(<?php echo $module['id']; ?>, '<?php echo htmlspecialchars(addslashes($module['name'])); ?>')" title="Delete Module">
                                                            <i class="feather icon-trash"></i>
                                                        </button>
                                                    </div>
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

<!-- Add Module Modal -->
<div class="modal fade" id="addModuleModal" tabindex="-1" role="dialog" aria-labelledby="addModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModuleModalLabel">Add Module</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo base_url($url.'/courses/add_module'); ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">

                    <div class="form-group">
                        <label for="module_name">Module Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="module_name" name="name" required>
                        <?php echo form_error('name', '<small class="text-danger">', '</small>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="module_description">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="module_description" name="description" rows="3" required></textarea>
                        <?php echo form_error('description', '<small class="text-danger">', '</small>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="module_order">Order <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="module_order" name="order" min="1" value="<?php echo count($modules) + 1; ?>" required>
                        <?php echo form_error('order', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Module</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Module Modal -->
<div class="modal fade" id="editModuleModal" tabindex="-1" role="dialog" aria-labelledby="editModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModuleModalLabel">Edit Module</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" id="editModuleForm">
                <div class="modal-body">
                    <input type="hidden" name="module_id" id="edit_module_id">

                    <div class="form-group">
                        <label for="edit_module_name">Module Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_module_name" name="name" required>
                        <?php echo form_error('name', '<small class="text-danger">', '</small>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="edit_module_description">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_module_description" name="description" rows="3" required></textarea>
                        <?php echo form_error('description', '<small class="text-danger">', '</small>'); ?>
                    </div>

                    <div class="form-group">
                        <label for="edit_module_order">Order <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="edit_module_order" name="order" min="1" required>
                        <?php echo form_error('order', '<small class="text-danger">', '</small>'); ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Module</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Module Confirmation Modal -->
<div class="modal fade" id="deleteModuleModal" tabindex="-1" role="dialog" aria-labelledby="deleteModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModuleModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the module "<span id="moduleName"></span>"? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a id="deleteModuleBtn" href="#" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
function editModule(id, name, description, order) {
    document.getElementById('edit_module_id').value = id;
    document.getElementById('edit_module_name').value = name;
    document.getElementById('edit_module_description').value = description;
    document.getElementById('edit_module_order').value = order;
    document.getElementById('editModuleForm').action = '<?php echo base_url($url.'/courses/edit_module/'.$course_id.'/'); ?>' + id;
    $('#editModuleModal').modal('show');
}

function confirmDeleteModule(moduleId, moduleName) {
    document.getElementById('moduleName').textContent = moduleName;
    document.getElementById('deleteModuleBtn').href = '<?php echo base_url($url.'/courses/delete_module/'.$course_id.'/'); ?>' + moduleId;
    $('#deleteModuleModal').modal('show');
}
</script>