<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">
            <?php echo $title ?>
        </h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item">
                    <?php echo $title ?>
                </li>
            </ol>
        </div>
        <!-- Flash Message -->
        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>
            
        <div class="card p-4">
            <form method="post" action="<?= base_url($url . "/allspecialcourses/assign_students/" . $course['id']) ?>">
                
                <!-- Explanation Section -->
              

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="department">Departments</label>
                            <select class="form-control select2" id="department" name="department[]" multiple="multiple">
                                <?php foreach ($departments as $department) : ?>
                                    <option value="<?= $department['id'] ?>" <?= isset($course) && in_array($department['id'], $departmentIds) ? 'selected' : '' ?>>
                                        <?= $department['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="student_groups">Student Groups</label>
                            <select class="form-control select2" id="student_groups" name="student_groups[]" multiple="multiple">
                                <?php foreach ($student_groups as $group) : ?>
                                    <option value="<?= $group['id'] ?>" <?= in_array($group['id'], $groupIds) ? 'selected' : '' ?>>
                                        <?= $group['group_name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                                   
                <!-- Student Selection Section -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Student Selection</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter_department">Filter by Department</label>
                                    <select class="form-control" id="filter_department">
                                        <option value="">All Departments</option>
                                        <?php 
                                        
                                        foreach ($departments as $dept) : ?>
                                            <option value="<?= $dept['name'] ?>"><?= $dept['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="filter_batch">Filter by Batch</label>
                                    <select class="form-control" id="filter_batch">
                                        <option value="">All Batches</option>
                                        <?php 
                                        $unique_batches = array_unique(array_column($students, 'batch'));
                                        foreach ($unique_batches as $batch) : ?>
                                            <option value="<?= $batch ?>"><?= $batch ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="search_student">Search Student</label>
                                    <input type="text" class="form-control" id="search_student" placeholder="Search by name or ID">
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table id="students_table" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">
                                            <input type="checkbox" id="select_all_students">
                                        </th>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Registration No</th>
                                        <th>Department</th>
                                        <th>Batch</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student) : ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="student-checkbox" name="students[]" 
                                                    value="<?= $student['id'] ?>" 
                                                    <?= in_array($student['id'], $studentIds) ? 'checked' : '' ?>>
                                            </td>
                                            <td><?= $student['id'] ?></td>
                                            <td><?= $student['name'] ?></td>
                                            <td><?= $student['registration_number'] ?></td>
                                            <td><?= $student['department'] ?></td>
                                            <td><?= $student['batch'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary"><?= 'Assign Students'?></button>
                    <a href="<?= base_url($url . '/allcourses') ?>" class="btn btn-secondary">Close</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize select2 elements
        $('.select2').select2({
            placeholder: "Select options",
            allowClear: true
        });

        // Initialize select2-tags
        $('.select2-tags').select2({
            tags: true,
            tokenSeparators: [',', ' '],
            placeholder: "Type to add tags",
            allowClear: true
        });

        // Initialize DataTable
        var studentsTable = $('#students_table').DataTable({
            dom: '<"top"f>rt<"bottom"lip><"clear">',
            responsive: true,
            columnDefs: [
                { orderable: false, targets: 0 }
            ],
            order: [[1, 'asc']]
        });

        // Filter by department
        $('#filter_department').change(function() {
            var dept = $(this).val();
            studentsTable.column(4).search(dept).draw();
        });

        // Filter by batch
        $('#filter_batch').change(function() {
            var batch = $(this).val();
            studentsTable.column(5).search(batch).draw();
        });

        // Search student
        $('#search_student').keyup(function() {
            studentsTable.search($(this).val()).draw();
        });

        // Select all students
        $('#select_all_students').click(function() {
            $('.student-checkbox').prop('checked', $(this).prop('checked'));
        });

        // End date validation
        $('#end_date').change(function() {
            let startDate = new Date($('#start_date').val());
            let endDate = new Date($(this).val());
            
            if(endDate < startDate) {
                alert('End date should be greater than start date');
                $(this).val('');
            }
        });

        // Handle explanation toggle
        $('#show_explanation').change(function() {
            if($(this).is(':checked')) {
                $('.explanation-dates').slideDown();
                $('#explanation_start, #explanation_end').prop('required', true);
            } else {
                $('.explanation-dates').slideUp();
                $('#explanation_start, #explanation_end').prop('required', false);
            }
        });

        // Initialize explanation dates visibility
        if($('#show_explanation').is(':checked')) {
            $('.explanation-dates').show();
            $('#explanation_start, #explanation_end').prop('required', true);
        }

        // Validate explanation dates
        $('#explanation_end').change(function() {
            let startDate = new Date($('#explanation_start').val());
            let endDate = new Date($(this).val());
            
            if(endDate < startDate) {
                alert('Explanation end date should be greater than start date');
                $(this).val('');
            }
        });
    });
</script>

<!-- Include necessary JS files -->
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
<script src="<?= base_url("/") ?>assets/packages/select2.min.js"></script>