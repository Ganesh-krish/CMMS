<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Module Lessons - <?php echo $module['name']; ?></h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/courses') ?>">Courses</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($url.'/courses/modules/'.$course_id) ?>"><?php echo $course['name']; ?></a></li>
                <li class="breadcrumb-item">Lessons</li>
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
                <div>
                    <h6 class="mb-0">Lessons for: <?php echo $course['name']; ?> > <?php echo $module['name']; ?></h6>
                    <small class="text-muted"><?php echo $course['code']; ?> - Module <?php echo $module['order']; ?></small>
                </div>
                <div>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLessonModal">
                        <i class="feather icon-plus"></i> Add Lesson
                    </button>
                    <a href="<?= base_url($url.'/courses/modules/'.$course_id) ?>" class="btn btn-secondary btn-sm">
                        <i class="feather icon-arrow-left"></i> Back to Modules
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table id="lessonsTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Lesson Title</th>
                            <th>Type</th>
                            <th>Duration</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($lessons)) {
                            $i = 1;
                            foreach ($lessons as $lesson) { ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $lesson['title']; ?></td>
                                    <td><?php echo $lesson['type']; ?></td>
                                    <td><?php echo $lesson['duration'] ? $lesson['duration'] . ' mins' : '-'; ?></td>
                                    <td><?php echo $lesson['order']; ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $lesson['is_active'] ? 'success' : 'danger'; ?>">
                                            <?php echo $lesson['is_active'] ? 'Active' : 'Inactive'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $this->common->display_date($lesson['created_at']); ?></td>
                                    <td class="d-flex gap-1" style="flex-wrap: wrap;">
                                        <button class="btn btn-sm btn-info" onclick="viewLesson(<?php echo $lesson['id']; ?>)">
                                            <i class="feather icon-eye"></i> View
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editLesson(<?php echo $lesson['id']; ?>, '<?php echo addslashes($lesson['title']); ?>', '<?php echo addslashes($lesson['type']); ?>', '<?php echo $lesson['duration']; ?>', '<?php echo $lesson['order']; ?>', '<?php echo addslashes($lesson['content']); ?>')">
                                            <i class="feather icon-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteLesson(<?php echo $lesson['id']; ?>)">
                                            <i class="feather icon-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                        <?php }
                        } else { ?>
                            <tr>
                                <td colspan="8" class="text-center">No lessons found for this module</td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Lesson Modal -->
<div class="modal fade" id="addLessonModal" tabindex="-1" aria-labelledby="addLessonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addLessonModalLabel">Add Lesson</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($url . '/courses/add_lesson') ?>" method="POST">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <input type="hidden" name="module_id" value="<?php echo $module_id; ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="title" class="form-label">Lesson Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="order" class="form-label">Order *</label>
                            <input type="number" class="form-control" id="order" name="order" min="1" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="type" class="form-label">Lesson Type *</label>
                            <select class="form-control" id="type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="video">Video</option>
                                <option value="text">Text</option>
                                <option value="quiz">Quiz</option>
                                <option value="assignment">Assignment</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="duration" class="form-label">Duration (minutes)</label>
                            <input type="number" class="form-control" id="duration" name="duration" min="1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="is_active" class="form-label">Status</label>
                            <select class="form-control" id="is_active" name="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content *</label>
                        <textarea class="form-control" id="content" name="content" rows="5" required placeholder="Enter lesson content, video URL, or instructions"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Lesson</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Lesson Modal -->
<div class="modal fade" id="editLessonModal" tabindex="-1" aria-labelledby="editLessonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editLessonModalLabel">Edit Lesson</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url($url . '/courses/edit_lesson') ?>" method="POST">
                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                <input type="hidden" name="module_id" value="<?php echo $module_id; ?>">
                <input type="hidden" id="edit_lesson_id" name="lesson_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="edit_title" class="form-label">Lesson Title *</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_order" class="form-label">Order *</label>
                            <input type="number" class="form-control" id="edit_order" name="order" min="1" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_type" class="form-label">Lesson Type *</label>
                            <select class="form-control" id="edit_type" name="type" required>
                                <option value="">Select Type</option>
                                <option value="video">Video</option>
                                <option value="text">Text</option>
                                <option value="quiz">Quiz</option>
                                <option value="assignment">Assignment</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_duration" class="form-label">Duration (minutes)</label>
                            <input type="number" class="form-control" id="edit_duration" name="duration" min="1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_is_active" class="form-label">Status</label>
                            <select class="form-control" id="edit_is_active" name="is_active">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_content" class="form-label">Content *</label>
                        <textarea class="form-control" id="edit_content" name="content" rows="5" required placeholder="Enter lesson content, video URL, or instructions"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Lesson</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editLesson(id, title, type, duration, order, content) {
    document.getElementById('edit_lesson_id').value = id;
    document.getElementById('edit_title').value = title;
    document.getElementById('edit_type').value = type;
    document.getElementById('edit_duration').value = duration;
    document.getElementById('edit_order').value = order;
    document.getElementById('edit_content').value = content;
    new bootstrap.Modal(document.getElementById('editLessonModal')).show();
}

function deleteLesson(id) {
    if (confirm('Are you sure you want to delete this lesson?')) {
        window.location.href = '<?= base_url($url . "/courses/delete_lesson/") ?><?php echo $course_id; ?>/<?php echo $module_id; ?>/' + id;
    }
}

function viewLesson(id) {
    // For now, just show an alert. In a real implementation, this could open a modal or redirect to a lesson viewer
    alert('Lesson viewing functionality would be implemented here.');
}

// Initialize DataTable
$(document).ready(function() {
    $('#lessonsTable').DataTable({
        "pageLength": 25,
        "order": [[ 4, "asc" ]] // Order by lesson order
    });
});
</script>

