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
                                <a href="<?php echo base_url($url.'/courses/modules/'.$course_id); ?>" class="btn btn-secondary btn-sm">
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
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLessonModal">
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
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLessonModal">
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
                                                        <!-- View Lesson Content -->
                                                        <a href="<?php echo base_url($url.'/courses/view_lesson/'.$course_id.'/'.$module_id.'/'.$lesson['id']); ?>" class="btn btn-primary btn-sm" title="View Lesson Content">
                                                            <i class="feather icon-eye"></i>
                                                        </a>

                                                        <!-- Edit Lesson -->
                                                        <button type="button" class="btn btn-warning btn-sm" onclick="editLesson(<?php echo $lesson['id']; ?>, '<?php echo htmlspecialchars(addslashes($lesson['title'])); ?>', '<?php echo htmlspecialchars(addslashes($lesson['type'])); ?>', '<?php echo htmlspecialchars(addslashes($lesson['content'])); ?>', '<?php echo htmlspecialchars(addslashes($lesson['course_text'] ?? '')); ?>', '<?php echo htmlspecialchars(addslashes($lesson['course_url'] ?? '')); ?>', '<?php echo htmlspecialchars(addslashes($lesson['course_file'] ?? '')); ?>', '<?php echo htmlspecialchars(addslashes($lesson['duration'] ?? '')); ?>', <?php echo $lesson['order']; ?>, <?php echo $lesson['is_active'] ? 1 : 0; ?>)" title="Edit Lesson">
                                                            <i class="feather icon-edit"></i>
                                                        </button>

                                                        <!-- Delete Lesson -->
                                                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteLesson(<?php echo $lesson['id']; ?>, '<?php echo htmlspecialchars(addslashes($lesson['title'])); ?>')" title="Delete Lesson">
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?php echo base_url($url.'/courses/add_lesson'); ?>" method="post" enctype="multipart/form-data" id="addLessonForm">
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
                                    <option value="<?php echo LESSON_TYPE_TEXT; ?>"><?php echo ucfirst(LESSON_TYPE_TEXT); ?></option>
                                    <option value="<?php echo LESSON_TYPE_VIDEO; ?>"><?php echo ucfirst(LESSON_TYPE_VIDEO); ?></option>
                                    <option value="<?php echo LESSON_TYPE_FILE; ?>"><?php echo ucfirst(LESSON_TYPE_FILE); ?></option>
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

                    <!-- Dynamic Content Fields -->
                    <div id="lesson_text_field" class="form-group" style="display: none;">
                        <label for="lesson_text">Lesson Text <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="lesson_text" name="lesson_text" rows="8" placeholder="Enter the lesson text content here..."></textarea>
                        <small class="form-text text-muted">This text will be displayed to students when they view this lesson.</small>
                    </div>

                    <div id="lesson_video_field" class="form-group" style="display: none;">
                        <label for="lesson_video">Video URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="lesson_video" name="lesson_video" placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/...">
                        <small class="form-text text-muted">Enter the full URL of the video (YouTube, Vimeo, etc.).</small>
                    </div>

                    <div id="lesson_file_field" class="form-group" style="display: none;">
                        <label for="lesson_file">File Upload <span class="text-danger">*</span></label>
                        <input type="file" class="form-control-file" id="lesson_file" name="lesson_file" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt">
                        <small class="form-text text-muted">Upload a file for this lesson (PDF, DOC, PPT, TXT, etc.).</small>
                    </div>

                    <div class="form-group">
                        <label for="lesson_content">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="lesson_content" name="content" rows="4" placeholder="Enter lesson description or instructions" required></textarea>
                        <?php echo form_error('content', '<small class="text-danger">', '</small>'); ?>
                        <small class="form-text text-muted">Brief description of what students will learn in this lesson.</small>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="post" id="editLessonForm" enctype="multipart/form-data">
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
                                    <option value="<?php echo LESSON_TYPE_TEXT; ?>"><?php echo ucfirst(LESSON_TYPE_TEXT); ?></option>
                                    <option value="<?php echo LESSON_TYPE_VIDEO; ?>"><?php echo ucfirst(LESSON_TYPE_VIDEO); ?></option>
                                    <option value="<?php echo LESSON_TYPE_FILE; ?>"><?php echo ucfirst(LESSON_TYPE_FILE); ?></option>
                                </select>
                                <?php echo form_error('type', '<small class="text-danger">*</small>'); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_lesson_duration">Duration (optional)</label>
                                <input type="text" class="form-control" id="edit_lesson_duration" name="duration" placeholder="e.g., 30 minutes, 2 hours">
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Content Fields for Edit -->
                    <div id="edit_lesson_text_field" class="form-group" style="display: none;">
                        <label for="edit_lesson_text">Lesson Text <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_lesson_text" name="lesson_text" rows="8" placeholder="Enter the lesson text content here..."></textarea>
                        <small class="form-text text-muted">This text will be displayed to students when they view this lesson.</small>
                    </div>

                    <div id="edit_lesson_video_field" class="form-group" style="display: none;">
                        <label for="edit_lesson_video">Video URL <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="edit_lesson_video" name="lesson_video" placeholder="https://www.youtube.com/watch?v=... or https://vimeo.com/...">
                        <small class="form-text text-muted">Enter the full URL of the video (YouTube, Vimeo, etc.).</small>
                    </div>

                    <div id="edit_lesson_file_field" class="form-group" style="display: none;">
                        <label for="edit_lesson_file">File Upload</label>
                        <input type="file" class="form-control-file" id="edit_lesson_file" name="lesson_file" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt">
                        <small class="form-text text-muted">Upload a replacement file for this lesson (PDF, DOC, PPT, TXT, etc.).</small>
                    </div>

                    <div class="form-group">
                        <label for="edit_lesson_content">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_lesson_content" name="content" rows="4" required></textarea>
                        <?php echo form_error('content', '<small class="text-danger">', '</small>'); ?>
                        <small class="form-text text-muted">Brief description of what students will learn in this lesson.</small>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the lesson "<span id="lessonName"></span>"? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a id="deleteLessonBtn" href="#" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>

<!-- Load Bootstrap Select for this page -->
<link rel="stylesheet" href="<?= base_url('') ?>assets/libs/bootstrap-select/bootstrap-select.css">

<script>
// Ensure jQuery is loaded
if (typeof jQuery === 'undefined') {
    console.error('jQuery is not loaded!');
}

$(document).ready(function() {
    console.log('Document ready - initializing lesson form handlers');
    
    // Reset form when add modal opens (Bootstrap 5 event)
    var addLessonModal = document.getElementById('addLessonModal');
    if (addLessonModal) {
        addLessonModal.addEventListener('shown.bs.modal', function() {
            console.log('Add lesson modal opened');
            resetAddLessonForm();
            // Check if type is already selected (shouldn't be, but just in case)
            var currentType = $('#lesson_type').val();
            if (currentType) {
                toggleLessonFields(currentType);
            }
        });
    }

    // Handle lesson type change for add modal - use multiple event handlers to ensure it works
    $('#lesson_type').on('change', function() {
        var selectedType = $(this).val();
        console.log('Lesson type changed (direct handler) to:', selectedType);
        if (selectedType) {
            toggleLessonFields(selectedType);
        } else {
            hideAllLessonFields();
        }
    });
    
    // Also use event delegation as backup
    $(document).on('change', '#lesson_type', function() {
        var selectedType = $(this).val();
        console.log('Lesson type changed (delegated handler) to:', selectedType);
        if (selectedType) {
            toggleLessonFields(selectedType);
        } else {
            hideAllLessonFields();
        }
    });
    
    // Also handle input event in case change doesn't fire
    $('#lesson_type').on('input', function() {
        var selectedType = $(this).val();
        if (selectedType) {
            toggleLessonFields(selectedType);
        }
    });

    // Handle lesson type change for edit modal
    $(document).on('change', '#edit_lesson_type', function() {
        var selectedType = $(this).val();
        console.log('Edit lesson type changed to:', selectedType);
        if (selectedType) {
            toggleEditLessonFields(selectedType);
        } else {
            // Hide all fields if no type selected
            $('#edit_lesson_text_field').hide();
            $('#edit_lesson_video_field').hide();
            $('#edit_lesson_file_field').hide();
            $('#edit_lesson_text').removeAttr('required').val('');
            $('#edit_lesson_video').removeAttr('required').val('');
            $('#edit_lesson_file').removeAttr('required').val('');
        }
    });
});

function resetAddLessonForm() {
    // Reset the form
    document.getElementById('lesson_title').value = '';
    // Auto-fill order with next available number (don't clear it)
    var lessonCount = <?php echo count($lessons ?? []); ?>;
    document.getElementById('lesson_order').value = lessonCount + 1;
    document.getElementById('lesson_duration').value = '';
    document.getElementById('lesson_content').value = '';

    // Reset additional dynamic fields
    if (document.getElementById('lesson_text')) {
        document.getElementById('lesson_text').value = '';
    }
    if (document.getElementById('lesson_video')) {
        document.getElementById('lesson_video').value = '';
    }
    if (document.getElementById('lesson_file')) {
        document.getElementById('lesson_file').value = '';
    }

    // Reset dropdown
    $('#lesson_type').val('');

    // Hide all dynamic fields
    $('#lesson_text_field').hide();
    $('#lesson_video_field').hide();
    $('#lesson_file_field').hide();

    // Remove required attributes
    $('#lesson_text').removeAttr('required');
    $('#lesson_video').removeAttr('required');
    $('#lesson_file').removeAttr('required');

    // Reset checkbox
    document.getElementById('lesson_active').checked = true;
}

function hideAllLessonFields() {
    $('#lesson_text_field').hide();
    $('#lesson_video_field').hide();
    $('#lesson_file_field').hide();
    $('#lesson_text').removeAttr('required').val('');
    $('#lesson_video').removeAttr('required').val('');
    $('#lesson_file').removeAttr('required').val('');
}

function toggleLessonFields(selectedType) {
    console.log('toggleLessonFields called with:', selectedType);
    console.log('LESSON_TYPE_TEXT constant:', '<?php echo LESSON_TYPE_TEXT; ?>');
    
    if (!selectedType || selectedType === '') {
        hideAllLessonFields();
        return;
    }
    
    // Hide all dynamic fields first
    hideAllLessonFields();

    // Show the selected field and make it required
    var textType = '<?php echo LESSON_TYPE_TEXT; ?>';
    var videoType = '<?php echo LESSON_TYPE_VIDEO; ?>';
    var fileType = '<?php echo LESSON_TYPE_FILE; ?>';
    
    console.log('Comparing:', selectedType, '===', textType, '?', selectedType === textType);
    console.log('Text type value:', textType, 'Selected:', selectedType, 'Match:', selectedType === textType);
    
    if (selectedType === textType || selectedType.trim() === textType.trim()) {
        console.log('Showing text field');
        $('#lesson_text_field').show();
        $('#lesson_text').attr('required', 'required');
        console.log('Text field should now be visible');
    } else if (selectedType === videoType || selectedType.trim() === videoType.trim()) {
        console.log('Showing video field');
        $('#lesson_video_field').show();
        $('#lesson_video').attr('required', 'required');
    } else if (selectedType === fileType || selectedType.trim() === fileType.trim()) {
        console.log('Showing file field');
        $('#lesson_file_field').show();
        $('#lesson_file').attr('required', 'required');
    } else {
        console.log('Unknown lesson type:', selectedType, 'Expected one of:', textType, videoType, fileType);
    }
}

// Clear irrelevant fields before form submission
$('#addLessonForm').on('submit', function(e) {
    var lessonType = $('#lesson_type').val();
    
    // Clear fields that are not relevant to the selected type
    if (lessonType !== '<?php echo LESSON_TYPE_TEXT; ?>') {
        $('#lesson_text').val('').removeAttr('required');
    }
    if (lessonType !== '<?php echo LESSON_TYPE_VIDEO; ?>') {
        $('#lesson_video').val('').removeAttr('required');
    }
    if (lessonType !== '<?php echo LESSON_TYPE_FILE; ?>') {
        $('#lesson_file').val('').removeAttr('required');
    }
});

function toggleEditLessonFields(selectedType) {
    // Hide all dynamic fields first
    $('#edit_lesson_text_field').hide();
    $('#edit_lesson_video_field').hide();
    $('#edit_lesson_file_field').hide();

    // Remove required attributes
    $('#edit_lesson_text').removeAttr('required');
    $('#edit_lesson_video').removeAttr('required');
    $('#edit_lesson_file').removeAttr('required');

    // Show the selected field and make it required
    if (selectedType === '<?php echo LESSON_TYPE_TEXT; ?>') {
        $('#edit_lesson_text_field').show();
        $('#edit_lesson_text').attr('required', 'required');
    } else if (selectedType === '<?php echo LESSON_TYPE_VIDEO; ?>') {
        $('#edit_lesson_video_field').show();
        $('#edit_lesson_video').attr('required', 'required');
    } else if (selectedType === '<?php echo LESSON_TYPE_FILE; ?>') {
        $('#edit_lesson_file_field').show();
        $('#edit_lesson_file').attr('required', 'required');
    }
}


function editLesson(id, title, type, content, courseText, courseUrl, courseFile, duration, order, isActive) {
    document.getElementById('edit_lesson_id').value = id;
    document.getElementById('edit_lesson_title').value = title;

    // Set Bootstrap Select value and refresh
    $('#edit_lesson_type').val(type);
    $('#edit_lesson_type').selectpicker('refresh');

    document.getElementById('edit_lesson_content').value = content;

    // Set the new dynamic fields
    if (document.getElementById('edit_lesson_text')) {
        document.getElementById('edit_lesson_text').value = courseText || '';
    }
    if (document.getElementById('edit_lesson_video')) {
        document.getElementById('edit_lesson_video').value = courseUrl || '';
    }
    if (document.getElementById('edit_lesson_file')) {
        // File inputs can't be set programmatically for security reasons
        // Just leave it empty - user can upload a replacement if needed
    }

    document.getElementById('edit_lesson_duration').value = duration || '';
    document.getElementById('edit_lesson_order').value = order;
    document.getElementById('edit_lesson_active').checked = isActive == 1;
    document.getElementById('editLessonForm').action = '<?php echo base_url($url.'/courses/edit_lesson/'.$course_id.'/'.$module_id.'/'); ?>' + id;

    // Show/hide fields based on current type
    toggleEditLessonFields(type);

    // Use Bootstrap 5 modal API
    var editLessonModal = new bootstrap.Modal(document.getElementById('editLessonModal'));
    editLessonModal.show();
}

function confirmDeleteLesson(lessonId, lessonName) {
    document.getElementById('lessonName').textContent = lessonName;
    document.getElementById('deleteLessonBtn').href = '<?php echo base_url($url.'/courses/delete_lesson/'.$course_id.'/'.$module_id.'/'); ?>' + lessonId;
    // Use Bootstrap 5 modal API
    var deleteLessonModal = new bootstrap.Modal(document.getElementById('deleteLessonModal'));
    deleteLessonModal.show();
}
</script>
</script>