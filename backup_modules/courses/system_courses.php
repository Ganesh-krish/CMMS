<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">System Course Management</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo base_url($url.'/dashboard'); ?>"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item active">System Courses</li>
            </ol>
        </div>

        <?php if ($this->session->flashdata('message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('message')[1] ?></span>
            </div>
        <?php } ?>

        <!-- College Filter -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Filter by College</h6>
                                <select id="collegeFilter" class="form-control select2" multiple>
                                    <?php foreach ($colleges as $college): ?>
                                        <option value="<?php echo $college['id']; ?>"><?php echo htmlspecialchars($college['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 text-right">
                                <h6>System Administration</h6>
                                <p class="mb-0">Manage courses across all colleges</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="coursesTable" class="table table-striped table-bordered" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>College</th>
                                        <th>Mode</th>
                                        <th>Type</th>
                                        <th>Created Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var table = $('#coursesTable').DataTable({
        serverSide: true,
        ajax: {
            url: '<?php echo base_url($url."/system_courses"); ?>',
            type: 'GET',
            data: function(d) {
                d.college_id = $('#collegeFilter').val() ? $('#collegeFilter').val().join(',') : '';
            }
        },
        columns: [
            { data: 0 }, // ID
            { data: 1 }, // Course Code
            { data: 2 }, // Course Name
            { data: 3 }, // College
            { data: 4 }, // Mode
            { data: 5 }, // Type
            { data: 6 }, // Created Date
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<button class="btn btn-sm btn-info" onclick="manageColleges(' + row[0] + ')">Manage Colleges</button>';
                }
            }
        ]
    });

    // Filter change event
    $('#collegeFilter').on('change', function() {
        table.ajax.reload();
    });
});

function manageColleges(courseId) {
    // Open modal to manage college assignments for this course
    window.location.href = '<?php echo base_url($url."/course_college_management/"); ?>' + courseId;
}
</script>