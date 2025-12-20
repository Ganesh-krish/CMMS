<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Course Lessons</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/courses'); ?>">Courses</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/courses/modules/'.$course_id); ?>"><?php echo htmlspecialchars($course['name']); ?> - Modules</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($module['name']); ?> - Lessons</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Course & Module Info -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5><?php echo htmlspecialchars($course['name']); ?> - <?php echo htmlspecialchars($module['name']); ?></h5>
                                <p class="mb-1"><?php echo htmlspecialchars($module['description']); ?></p>
                                <small class="text-muted">
                                    Course: <?php echo htmlspecialchars($course['course_code'] ?? 'N/A'); ?> |
                                    Module Order: <?php echo htmlspecialchars($module['order']); ?>
                                </small>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?php echo base_url($url.'/courses/modules/'.$course_id); ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="feather icon-arrow-left"></i> Back to Modules
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
                                <h6>Lesson Management</h6>
                                <p class="mb-0">Manage lessons within this module</p>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addLessonModal">
                                    <i class="feather icon-plus"></i> Add Lesson
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lessons List -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($lessons)): ?>
                            <div class="text-center py-5">
                                <i class="feather icon-file-text" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3">No Lessons</h4>
                                <p class="text-muted">There are no lessons in this module yet.</p>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addLessonModal">
                                    Add First Lesson
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="datatable table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order</th>
                                            <th>Lesson Title</th>
                                            <th>Type</th>
                                            <th>Duration</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($lessons as $lesson): ?>
                                            <tr>
                                                <td>
                                                    <span class="badge badge-info"><?php echo htmlspecialchars($lesson['order']); ?></span>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($lesson['title']); ?></strong>
                                                    <br>
                                                    <small class="text-muted"><?php echo htmlspecialchars(substr($lesson['content'], 0, 80)); ?><?php echo strlen($lesson['content']) > 80 ? '...' : ''; ?></small>
                                                </td>
                                                <td>
                                                    <span class="badge badge-secondary"><?php echo htmlspecialchars($lesson['type']); ?></span>
                                                </td>
                                                <td>
                                                    <?php echo htmlspecialchars($lesson['duration'] ?? 'N/A'); ?>
                                                </td>
                                                <td>
                                                    <?php if ($lesson['is_active']): ?>
                                                        <span class="badge badge-success">Active</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-danger">Inactive</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <!-- Edit Lesson -->
                                                        <button type="button" class="btn btn-outline-warning btn-sm" onclick="editLesson(<?php echo $lesson['id']; ?>, '<?php echo htmlspecialchars(addslashes($lesson['title'])); ?>', '<?php echo htmlspecialchars(addslashes($lesson['type'])); ?>', '<?php echo htmlspecialchars(addslashes($lesson['content'])); ?>', '<?php echo htmlspecialchars(addslashes($lesson['duration'] ?? '')); ?>', <?php echo $lesson['order']; ?>, <?php echo $lesson['is_active'] ? 1 : 0; ?>)" title="Edit Lesson">
                                                            <i class="feather icon-edit"></i>
                                                        </button>

                                                        <!-- Delete Lesson -->
                                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDeleteLesson(<?php echo $lesson['id']; ?>, '<?php echo htmlspecialchars(addslashes($lesson['title'])); ?>')" title="Delete Lesson">
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

<!-- Add Lesson Modal -->
<div class="modal fade" id="addLessonModal" tabindex="-1" role="dialog" aria-labelledby="addLessonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLessonModalLabel">Add Lesson</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo base_url($url.'/courses/add_lesson'); ?>" method="post">
                <div class="modal-body">
                    <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                    <input type="hidden" name="module_id" value="<?php echo $module_id; ?>">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="lesson_title">Lesson Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="lesson_title" name="title" required>
                                <?php echo form_error('title', '<small class="text-danger">', '</small>'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="lesson_order">Order <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="lesson_order" name="order" min="1" value="<?php echo count($lessons) + 1; ?>" required>
                                <?php echo form_error('order', '<small class="text-danger">', '</small>'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lesson_type">Lesson Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="lesson_type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="video">Video</option>
                                    <option value="text">Text</option>
                                    <option value="file">File</option>
                                </select>
                                <?php echo form_error('type', '<small class="text-danger">', '</small>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lesson_duration">Duration (optional)</label>
                                <input type="text" class="form-control" id="lesson_duration" name="duration" placeholder="e.g., 30 minutes, 2 hours">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lesson_content">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="lesson_content" name="content" rows="6" placeholder="Enter lesson content, description, or instructions" required></textarea>
                        <?php echo form_error('content', '<small class="text-danger">', '</small>'); ?>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="lesson_active" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="lesson_active">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Lesson</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Lesson Modal -->
<div class="modal fade" id="editLessonModal" tabindex="-1" role="dialog" aria-labelledby="editLessonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLessonModalLabel">Edit Lesson</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" id="editLessonForm">
                <div class="modal-body">
                    <input type="hidden" name="lesson_id" id="edit_lesson_id">

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="edit_lesson_title">Lesson Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_lesson_title" name="title" required>
                                <?php echo form_error('title', '<small class="text-danger">', '</small>'); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="edit_lesson_order">Order <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_lesson_order" name="order" min="1" required>
                                <?php echo form_error('order', '<small class="text-danger">', '</small>'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_lesson_type">Lesson Type <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_lesson_type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="video">Video</option>
                                    <option value="text">Text</option>
                                    <option value="file">File</option>
                                </select>
                                <?php echo form_error('type', '<small class="text-danger">', '</small>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_lesson_duration">Duration (optional)</label>
                                <input type="text" class="form-control" id="edit_lesson_duration" name="duration" placeholder="e.g., 30 minutes, 2 hours">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_lesson_content">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_lesson_content" name="content" rows="6" required></textarea>
                        <?php echo form_error('content', '<small class="text-danger">', '</small>'); ?>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="edit_lesson_active" name="is_active" value="1">
                            <label class="custom-control-label" for="edit_lesson_active">
                                Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Lesson</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Lesson Confirmation Modal -->
<div class="modal fade" id="deleteLessonModal" tabindex="-1" role="dialog" aria-labelledby="deleteLessonModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteLessonModalLabel">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the lesson "<span id="lessonName"></span>"? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a id="deleteLessonBtn" href="#" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<script>
function editLesson(id, title, type, content, duration, order, isActive) {
    document.getElementById('edit_lesson_id').value = id;
    document.getElementById('edit_lesson_title').value = title;
    document.getElementById('edit_lesson_type').value = type;
    document.getElementById('edit_lesson_content').value = content;
    document.getElementById('edit_lesson_duration').value = duration;
    document.getElementById('edit_lesson_order').value = order;
    document.getElementById('edit_lesson_active').checked = isActive == 1;
    document.getElementById('editLessonForm').action = '<?php echo base_url($url.'/courses/edit_lesson/'.$course_id.'/'.$module_id.'/'); ?>' + id;
    $('#editLessonModal').modal('show');
}

function confirmDeleteLesson(lessonId, lessonName) {
    document.getElementById('lessonName').textContent = lessonName;
    document.getElementById('deleteLessonBtn').href = '<?php echo base_url($url.'/courses/delete_lesson/'.$course_id.'/'.$module_id.'/'); ?>' + lessonId;
    $('#deleteLessonModal').modal('show');
}
</script>