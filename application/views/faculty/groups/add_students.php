<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Add Students to <?php echo htmlspecialchars($group['name']); ?> Group</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/groups'); ?>">Music Groups</a></li>
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/groups/group_students/'.$group['id']); ?>"><?php echo htmlspecialchars($group['name']); ?> Students</a></li>
                <li class="breadcrumb-item active">Add Students</li>
            </ol>
        </div>
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- Group Info -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6><i class="feather icon-users mr-2"></i><?php echo htmlspecialchars($group['name']); ?> Music Group</h6>
                                <p class="mb-0"><?php echo htmlspecialchars($group['description'] ?? 'No description available'); ?></p>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="<?php echo base_url($url.'/groups/group_students/'.$group['id']); ?>" class="btn btn-secondary">
                                    <i class="feather icon-arrow-left"></i> Back to Group Students
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Students Form -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Select Students to Add</h6>
                    </div>
                    <div class="card-body">
                        <?php if (empty($available_students)): ?>
                            <div class="text-center py-5">
                                <i class="feather icon-user-x" style="font-size: 4rem; color: #ccc;"></i>
                                <h4 class="mt-3">No Students Available</h4>
                                <p class="text-muted">All students are already in this music group or no students are available in your department.</p>
                            </div>
                        <?php else: ?>
                            <form method="post" action="<?php echo base_url($url.'/groups/add_students_to_group/'.$group['id']); ?>">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                            <label class="form-check-label font-weight-bold" for="selectAll">
                                                Select All Students
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th width="50">Select</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Roll No</th>
                                                <th>Department</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($available_students as $student): ?>
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input student-checkbox" type="checkbox"
                                                                   name="student_ids[]" value="<?php echo $student['id']; ?>"
                                                                   id="student_<?php echo $student['id']; ?>">
                                                            <label class="form-check-label" for="student_<?php echo $student['id']; ?>"></label>
                                                        </div>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($student['roll_no'] ?? '-'); ?></td>
                                                    <td>
                                                        <?php
                                                        if (!empty($student['department'])) {
                                                            $dept = $this->db_model->get_row('departments', ['id' => $student['department']]);
                                                            echo $dept ? htmlspecialchars($dept['name']) : 'N/A';
                                                        } else {
                                                            echo 'N/A';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12 text-right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="feather icon-user-plus"></i> Add Selected Students
                                        </button>
                                        <a href="<?php echo base_url($url.'/groups/group_students/'.$group['id']); ?>" class="btn btn-secondary ml-2">
                                            Cancel
                                        </a>
                                    </div>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleSelectAll() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');

    studentCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
}

// Update "Select All" checkbox when individual checkboxes change
document.addEventListener('DOMContentLoaded', function() {
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    const selectAllCheckbox = document.getElementById('selectAll');

    studentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(studentCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(studentCheckboxes).some(cb => cb.checked);

            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        });
    });
});
</script>
