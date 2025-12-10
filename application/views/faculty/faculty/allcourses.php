<?php
$typeMap = unserialize(COURSE_TYPES);
$modeMap = unserialize(COURSE_MODES);

echo '<style>';
foreach ($typeMap as $label => $details) {
    if (!empty($details['color'])) {
        $class = strtolower(preg_replace('/[^a-z0-9]+/', '-', $label));
        echo ".label-chip.{$class} { background-color: {$details['color']}; }";
    }
}
foreach ($modeMap as $label => $details) {
    if (!empty($details['color'])) {
        $class = strtolower(preg_replace('/[^a-z0-9]+/', '-', $label));
        echo ".label-chip.{$class} { background-color: {$details['color']}; }";
    }
}
echo '</style>';
?>
<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Courses</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <!-- <li class="breadcrumb-item">Principal</li> -->
                <li class="breadcrumb-item">All Courses</li>
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
                <h6 class="card-header" style="border:none">Our College Course Details</h6>
            </div>
            <div class="card-datatable">
                <div class="row mb-3">
                    <div class="col-md-4 mx-auto">
                        <div class="form-group">
                            <label for="specialtagFilter" class="d-block text-center mb-2">Filter by Tags</label>
                            <select id="specialtagFilter" class="form-control select2">
                                <option value="">All Tags</option>
                                <?php
                                foreach ($tags as $tag) {
                                    $selected = (isset($_GET['tag']) && $_GET['tag'] === $tag) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($tag) . '" ' . $selected . '>' . htmlspecialchars($tag) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="mytable" class="datatables-demo table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Description</th>
                                <th>Tags</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                            if (!empty($cources)) { $no=0;
                                foreach ($cources as $row) {  $no++?>
                                    <tr>
                                        <td> <?= $no;?></td>   
                                        <td><?php if (isset($row['course_code'])) {
                                                echo $row['course_code'];
                                            } else {
                                                echo "-";
                                            } ?>
                                        </td>   
     <td>
<?php 
    echo isset($row['name']) ? htmlspecialchars($row['name']) : "-";

    $typeMap = unserialize(COURSE_TYPES);
    $modeMap = unserialize(COURSE_MODES);

    $typeTag = '';
    $modeTag = '';

                                        $courseTypeId = isset($row['course_type']) ? (int)$row['course_type'] : null;
                                            $courseModeId = isset($row['course_mode']) ? (int)$row['course_mode'] : null;

                                            // Type chip based on ID (skip id=1 for 'Courses')
                                            foreach ($typeMap as $label => $info) {
                                                if (isset($info['id']) && $info['id'] === $courseTypeId && $info['id'] !== 1) {
                                                    $class = strtolower(preg_replace('/[^a-z0-9]+/', '-', $label));
                                                    $short = ($info['id'] === 2) ? 'Comp...' : 'Labs...';
                                                    $typeTag = '<span class="label-chip ' . $class . '" title="' . htmlspecialchars($label) . '">' . $short . '</span>';
                                                    break;
                                                }
                                            }

                                            // Mode chip based on ID (only show for Gamification, id = 2)
                                            foreach ($modeMap as $label => $info) {
                                                if (isset($info['id']) && $info['id'] === $courseModeId && $info['id'] === 2) {
                                                    $class = strtolower(preg_replace('/[^a-z0-9]+/', '-', $label));
                                                    $modeTag = '<span class="label-chip ' . $class . '" title="' . htmlspecialchars($label) . '">Game</span>';
                                                    break;
                                                }
                                            }
                                                echo ' ' . $typeTag . ' ' . $modeTag;
                                            ?>
                                            </td>

  
                                        <td><?php if (isset($row['description'])) {
                                                echo $row['description'];
                                            } else {
                                                echo "-";
                                            } ?>
                                        </td>   

                                        <td>
                                        <?php if (isset($row['tag']) && !empty($row['tag'])) {
                                                $tags = explode(',', $row['tag']);
                                                foreach($tags as $tag) {
                                                    $tag = trim($tag);
                                                    if(!empty($tag)) {
                                                        echo '<span class="tag-chip">' . htmlspecialchars($tag) . '</span>';
                                                    }
                                                }
                                            } else {
                                                echo "-";
                                            } ?>
                                        </td>
                                    
                                        
                                        <td><?php if (isset($row['created_at'])) {
                                                echo $this->common->display_date($row['created_at']);
                                            } else {
                                                echo "-";
                                            } 
                                            // print_r($cources);
                                            // exit
                                            ?>
                                        </td>  
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                                <a href="<?= base_url($url.'/allcourses/modules/'.$row['id']); ?>" class="btn btn-primary btn-sm" ><i class="feather icon-layers"></i>&nbsp;Modules </a>
                                                <!-- <a href="<?= base_url($url.'/course/test/add/'.$row['id']); ?>" class="btn btn-info btn-sm" ><i class="feather icon-plus"></i>&nbsp;Add Test </a> -->
                                                <a href="<?= base_url($url.'/course/view_students/'.$row['id']); ?>" class="btn btn-info btn-sm" ><i class="feather icon-users"></i>&nbsp;View Students </a>
                                                <!-- <a href="<?= base_url($url.'/course/edit/'.$college_id.'/'.$row['id']) ?>" class="btn btn-warning btn-sm" ><i class="feather icon-edit"></i>&nbsp;Edit </a>
                                                <a href="<?= base_url($url.'/course/delete/'.$college_id.'/'.$row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?');"><i class="feather icon-trash"></i>&nbsp;Delete </a> -->
                                            </div>
                                        </td>
                                    </tr>
                            <?php }
                            } 
                            //  print_r($special_courses);
                            // exit;
                            ?>
                            
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
                           
        <!-- SPECIAL COURSE CARD STARTS -->
        <!-- <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <li class="breadcrumb-item">Principal</li>
                <li class="breadcrumb-item">Special Courses</li>
            </ol>
        </div> -->
        <?php if ($this->session->flashdata('s_message')) { ?>
            <div class="alert alert-dark-<?= $this->session->flashdata('s_message')[0] ?> alert-dismissible fade show" id="alert">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <span><?= $this->session->flashdata('s_message')[1] ?></span>
            </div>
        <?php   } ?>
        <div class="card p-2">
            <div style="display: flex; justify-content:space-between; align-items: center;
            border-bottom: 0 solid rgba(24, 28, 33, 0.13);
            border-color: rgba(24, 28, 33, 0.13);
            border-radius: 0.125rem 0.125rem 0 0; 
            border-bottom-width: 1px;">
                <h6 class="card-header" style="border:none">Special Course Details</h6>
            </div>
            <div class="card-datatable">
                <div class="row mb-3">
                    <div class="col-md-4 mx-auto">
                        <div class="form-group">
                            <label for="tagFilter" class="d-block text-center mb-2">Filter by Tags</label>
                            <select id="tagFilter" class="form-control select2">
                                <option value="">All Tags</option>
                                <?php
                                foreach ($sp_tags as $tag) {
                                    $selected = (isset($_GET['tag']) && $_GET['tag'] === $tag) ? 'selected' : '';
                                    echo '<option value="' . htmlspecialchars($tag) . '" ' . $selected . '>' . htmlspecialchars($tag) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table id="myspecialtable" class="datatables-demo table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Description</th>
                                <th>Tags</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        // print_r($special_courses);
                        // exit;
                            if (!empty($special_courses)) { $no=0;
                                foreach ($special_courses as $row) {  $no++?>
                                    <tr>
                                        <td> <?= $no;?></td>   
                                        <td><?php if (isset($row['course_code'])) {
                                                echo $row['course_code'];
                                            } else {
                                                echo "-";
                                            } ?>
                                        </td>   
     <td>
<?php 
    echo isset($row['name']) ? htmlspecialchars($row['name']) : "-";

    $typeMap = unserialize(COURSE_TYPES);
    $modeMap = unserialize(COURSE_MODES);

    $typeTag = '';
    $modeTag = '';

                                        $courseTypeId = isset($row['course_type']) ? (int)$row['course_type'] : null;
                                            $courseModeId = isset($row['course_mode']) ? (int)$row['course_mode'] : null;

                                            // Type chip based on ID (skip id=1 for 'Courses')
                                            foreach ($typeMap as $label => $info) {
                                                if (isset($info['id']) && $info['id'] === $courseTypeId && $info['id'] !== 1) {
                                                    $class = strtolower(preg_replace('/[^a-z0-9]+/', '-', $label));
                                                    $short = ($info['id'] === 2) ? 'Comp...' : 'Labs...';
                                                    $typeTag = '<span class="label-chip ' . $class . '" title="' . htmlspecialchars($label) . '">' . $short . '</span>';
                                                    break;
                                                }
                                            }

                                            // Mode chip based on ID (only show for Gamification, id = 2)
                                            foreach ($modeMap as $label => $info) {
                                                if (isset($info['id']) && $info['id'] === $courseModeId && $info['id'] === 2) {
                                                    $class = strtolower(preg_replace('/[^a-z0-9]+/', '-', $label));
                                                    $modeTag = '<span class="label-chip ' . $class . '" title="' . htmlspecialchars($label) . '">Game</span>';
                                                    break;
                                                }
                                            }
                                                echo ' ' . $typeTag . ' ' . $modeTag;
                                            ?>
                                            </td>

  
                                        <td><?php if (isset($row['description'])) {
                                                echo $row['description'];
                                            } else {
                                                echo "-";
                                            } ?>
                                        </td>   

                                        <td>
                                        <?php if (isset($row['tag']) && !empty($row['tag'])) {
                                                $tags = explode(',', $row['tag']);
                                                foreach($tags as $tag) {
                                                    $tag = trim($tag);
                                                    if(!empty($tag)) {
                                                        echo '<span class="tag-chip">' . htmlspecialchars($tag) . '</span>';
                                                    }
                                                }
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
                                            <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                                <a href="<?= base_url($url.'/allcourses/modules/'.$row['course_id']); ?>" class="btn btn-primary btn-sm" ><i class="feather icon-layers"></i>&nbsp;Modules </a>
                                                <a href="<?= base_url($url.'/course/view_students/'.$row['course_id']); ?>" class="btn btn-info btn-sm" ><i class="feather icon-users"></i>&nbsp;View Students </a>
                                                <a href="<?= base_url($url.'/allspecialcourses/assign_students/'.$row['course_id']); ?>" class="btn btn-warning btn-sm" ><i class="feather icon-users"></i>&nbsp;Assign Students </a>
                                                <!-- <a href="<?= base_url($url.'/course/test/add/'.$row['id']); ?>" class="btn btn-info btn-sm" ><i class="feather icon-plus"></i>&nbsp;Add Test </a> -->
                                                <!-- <a href="<?= base_url($url.'/course/edit/'.$college_id.'/'.$row['id']) ?>" class="btn btn-warning btn-sm" ><i class="feather icon-edit"></i>&nbsp;Edit </a>
                                                <a href="<?= base_url($url.'/course/delete/'.$college_id.'/'.$row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?');"><i class="feather icon-trash"></i>&nbsp;Delete </a> -->
                                            </div>
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
</div>

<style>
    .tag-chip {
        display: inline-block;
        padding: 4px 8px;
        margin: 2px;
        background-color: #e9ecef;
        border-radius: 16px;
        font-size: 0.875rem;
        color: #495057;
        border: 1px solid #dee2e6;
    }
    .tag-chip:hover {
        background-color: #dee2e6;
    }
    /* Sticky column styles */
    .sticky-column {
        position: sticky !important;
        right: 0;
        background-color: #fff;
        z-index: 1;
    }
    .sticky-column::after {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 1px;
        background-color: #dee2e6;
    }
    /* Ensure the sticky column has proper background */
    .table-striped tbody tr:nth-of-type(odd) .sticky-column {
        background-color: #f2f2f2;
    }
    .table-striped tbody tr:nth-of-type(even) .sticky-column {
        background-color: #fff;
    }
    /* Remove horizontal scrollbar */
    .dataTables_wrapper {
        overflow-x: auto !important;
    }
    .table-responsive {
        overflow-x: auto !important;
    }
    /* Fix table layout */
    .card-datatable {
        padding: 1rem;
    }
    .dataTables_wrapper {
        margin-bottom: 0 !important;
    }
    .dt-buttons {
        margin-bottom: 1rem;
    }
    .dataTables_filter {
        margin-bottom: 1rem;
    }
    .dataTables_info, .dataTables_paginate {
        margin-top: 1rem;
    }
.label-chip {
    display: inline-block;
    padding: 3px 10px;
    margin-left: 5px;
    font-size: 0.75rem;
    color: white;
    clip-path: polygon(
        0% 0%, 90% 0%, 100% 50%, 90% 100%, 0% 100%, 10% 50%
    );
}

 /* .label-chip {
    display: inline-block;
    padding: 3px 10px;
    margin-left: 5px;
    font-size: 0.75rem;
    border-radius: 12px;
    color: white;
    cursor: default;
    vertical-align: middle;
}  */

  /* .label-chip {
    display: inline-block;
    padding: 4px 10px;
    margin-left: 5px;
    font-size: 0.75rem;
    border-radius: 2px;
    color: white;
} */

</style>

<script>
$(document).ready(function() {
    // Initialize select2 for tag filter
    $('#tagFilter').select2({
        placeholder: "Select a tag",
        allowClear: true,
        width: '100%'
    });

    // Tag filter change handler
    $('#tagFilter').change(function() {
        const selectedTag = $(this).val();
        const currentUrl = new URL(window.location.href);
        
        if (selectedTag) {
            currentUrl.searchParams.set('tag', selectedTag);
        } else {
            currentUrl.searchParams.delete('tag');
        }
        
        window.location.href = currentUrl.toString();
    });

     // Initialize select2 for tag filter
    $('#specialtagFilter').select2({
        placeholder: "Select a tag",
        allowClear: true,
        width: '100%'
    });

    // Tag filter change handler
    $('#specialtagFilter').change(function() {
        const selectedTag = $(this).val();
        const currentUrl = new URL(window.location.href);
        
        if (selectedTag) {
            currentUrl.searchParams.set('tag', selectedTag);
        } else {
            currentUrl.searchParams.delete('tag');
        }
        
        window.location.href = currentUrl.toString();
    });

    // Wait for the initial DataTable initialization to complete
    setTimeout(function() {
        // Destroy existing DataTable instance
        if ($.fn.DataTable.isDataTable('#mytable')) {
            $('#mytable').DataTable().destroy();
        }
        
        // Initialize DataTable with our custom configuration
        $('#mytable').DataTable({
            autoWidth: false,
            scrollX: false,
            fixedColumns: {
                right: 1
            },
            columnDefs: [
                {
                    targets: -1, // Last column (Action column)
                    className: 'sticky-column'
                }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            responsive: true,
            drawCallback: function() {
                // Adjust table container height after drawing
                $('.dataTables_wrapper').css('min-height', 'auto');
            }
        });
    }, 100);

    setTimeout(function() {
        // Destroy existing DataTable instance
        if ($.fn.DataTable.isDataTable('#myspecialtable')) {
            $('#myspecialtable').DataTable().destroy();
        }
        
        // Initialize DataTable with our custom configuration
        $('#myspecialtable').DataTable({
            autoWidth: false,
            scrollX: false,
            fixedColumns: {
                right: 1
            },
            columnDefs: [
                {
                    targets: -1, // Last column (Action column)
                    className: 'sticky-column'
                }
            ],
            dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            responsive: true,
            drawCallback: function() {
                // Adjust table container height after drawing
                $('.dataTables_wrapper').css('min-height', 'auto');
            }
        });
    }, 100);
});
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