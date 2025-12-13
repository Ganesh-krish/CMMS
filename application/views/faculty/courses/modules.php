<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Course Modules - <?php echo $course['name']; ?></h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/courses') ?>">Courses</a></li>
                <li class="breadcrumb-item">Modules</li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php   } ?>

        <div class="card p-2">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Modules for: <?php echo $course['name']; ?> (<?php echo $course['code']; ?>)</h6>
                <div>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModuleModal">
                        <i class="feather icon-plus"></i> Add Module
                    </button>
                    <a href="<?= base_url($url.'/courses') ?>" class="btn btn-secondary btn-sm">
                        <i class="feather icon-arrow-left"></i> Back to Courses
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table id="modulesTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Module Name</th>
                            <th>Description</th>
                            <th>Order</th>
                            <th>Lessons Count</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($modules)) {
                            $i = 1;
                            foreach ($modules as $module) {
                                // Count lessons for this module
                                $lessons_count = $this->db_model->count('course_module_lessons', [
                                    'module_id' => $module['id'],
                                    'is_active' => 1
                                ]);
                                ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $module['name']; ?></td>
                                    <td><?php echo substr($module['description'], 0, 50) . (strlen($module['description']) > 50 ? '...' : ''); ?></td>
                                    <td><?php echo $module['order']; ?></td>
                                    <td><?php echo $lessons_count; ?></td>
                                    <td><?php echo $this->common->display_date($module['created_at']); ?></td>
                                    <td class="d-flex gap-1" style="flex-wrap: wrap;">
                                        <a href="<?php echo base_url($url.'/courses/lessons/'.$course_id.'/'.$module['id']); ?>" class="btn btn-sm btn-info">
                                            <i class="feather icon-file-text"></i> Lessons
                                        </a>
                                        <button class="btn btn-sm btn-warning" onclick="editModule(<?php echo $module['id']; ?>, '<?php echo addslashes($module['name']); ?>', '<?php echo addslashes($module['description']); ?>', '<?php echo $module['order']; ?>')">
                                            <i class="feather icon-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteModule(<?php echo $module['id']; ?>)">
                                            <i class="feather icon-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                        <?php }
                        } else { ?>
                            <tr>
                                <td colspan="7" class="text-center">No modules found for this course</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Module Modal -->
<div class="modal fade" id="addModuleModal" tabindex="-1" aria-labelledby="addModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModuleModalLabel">Add Module</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($url . '/courses/add_module') ?>" method="POST">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Module Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="order" class="form-label">Order *</label>
                            <input type="number" class="form-control" id="order" name="order" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Module</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Module Modal -->
<div class="modal fade" id="editModuleModal" tabindex="-1" aria-labelledby="editModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModuleModalLabel">Edit Module</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($url . '/courses/edit_module') ?>" method="POST">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <input type="hidden" id="edit_module_id" name="module_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="edit_name" class="form-label">Module Name *</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_order" class="form-label">Order *</label>
                            <input type="number" class="form-control" id="edit_order" name="order" min="1" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description *</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Module</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editModule(id, name, description, order) {
    document.getElementById('edit_module_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_order').value = order;
    new bootstrap.Modal(document.getElementById('editModuleModal')).show();
}

function deleteModule(id) {
    if (confirm('Are you sure you want to delete this module? This will also delete all lessons in this module.')) {
        window.location.href = '<?= base_url($url . "/courses/delete_module/") ?><?php echo $course_id; ?>/' + id;
    }
}

// Initialize DataTable
$(document).ready(function() {
    $('#modulesTable').DataTable({
        "pageLength": 25,
        "order": [[ 3, "asc" ]] // Order by module order
    });
});
</script>
