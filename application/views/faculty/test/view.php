<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3 justify-content-between">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Tests</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Tests</h6>
            </nav>
            <div class="collapse navbar-collapse flex-grow-0 mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <ul class="navbar-nav justify-content-end">
                    <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                        <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                            <div class="sidenav-toggler-inner">
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                                <i class="sidenav-toggler-line"></i>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <?php if ($this->session->flashdata('message')) { ?>
                        <div class="alert alert-<?= $this->session->flashdata('message')[0] ?> alert-dismissible" id="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            <span class="alert-text" style="color:black">
                                <?= $this->session->flashdata('message')[1] ?>
                            </span>
                        </div>
                    <?php } ?>
                    
                    <div class="d-flex justify-content-between align-items-center"> 
                        <div class="card-header">
                            <h5>Manage Tests</h5>
                        </div> 
                        <div class="card-header"> 
                            <button class="btn btn-primary" type="button" onclick="window.location.href='<?= base_url($url.'/test/create') ?>'">
                                <i class="fas fa-plus"></i> Create New Test
                            </button>
                        </div> 
                    </div>
                    
                    <div class="card p-2">
                     
                        
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0" id="tests-table">
                                <thead>
                                    <tr>
                                        <th class="text-center text-uppercase  text-sm font-weight-bolder opacity-7">S.No</th>
                                        <th class="text-uppercase  text-sm font-weight-bolder opacity-7">Test Title</th>
                                        <!-- <th class="text-uppercase  text-sm font-weight-bolder opacity-7">Module</th>
                                        <th class="text-uppercase  text-sm font-weight-bolder opacity-7">Start Date</th>
                                        <th class="text-uppercase  text-sm font-weight-bolder opacity-7">End Date</th> -->
                                        <th class="text-uppercase  text-sm font-weight-bolder opacity-7">Duration (hr:min)</th>
                                        <th class="text-uppercase  text-sm font-weight-bolder opacity-7">Status</th>
                                        <th class="text-center text-uppercase  text-sm font-weight-bolder opacity-7">Created At</th>
                                        <th class="text-center text-uppercase  text-sm font-weight-bolder opacity-7">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; if(isset($tests) && !empty($tests)) : foreach ($tests as $test) : ?>
                                        <tr>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-secondary text-sm font-weight-bold"><?= $no++; ?></span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="text-secondary text-sm font-weight-bold"><?= $test->title; ?></span>
                                            </td>
                                        
                                            <td class="align-middle">
                                                    <span class="text-secondary text-sm font-weight-bold">
                                                        <?= isset($test->duration) ? floor($test->duration / 60).':'.str_pad($test->duration % 60, 2, '0', STR_PAD_LEFT) : '0:00' ?>
                                                    </span>
                                            </td>
                                            <td class="align-middle">
                                                <?php if ($test->is_active) : ?>
                                                    <span style="color: green; ">Active</span>
                                                <?php else : ?>
                                                    <span style="color: red; ">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-sm font-weight-bold"><?= date('d-m-Y', strtotime($test->created_at)); ?></span>
                                            </td>
                                            <td class="align-middle text-center">
                                                <button type="button" onclick="window.location.href='<?= base_url($url.'/test/questions/' . $test->id); ?>'" class="btn btn-info btn-sm">
                                                    <i class="fas fa-add"></i> Questions
                                                </button>
                                                <button type="button" onclick="window.location.href='<?= base_url($url.'/test/edit/' . $test->id); ?>'" class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit"></i> Edit
                                                </button>
                                                <button data-id="<?= $test->id; ?>" class="btn btn-danger btn-sm deleteTest">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center">No tests found</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Delete Confirmation Modal -->
                <div id="confirmDeleteModal" class="modal fade" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirm Delete</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this test? This action cannot be undone.</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="hideDeleteModal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Tags styling -->
<style>
.top-filter {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}

.top-filter select,
.top-filter input {
    border-radius: 0.5rem;
    border: 1px solid #d2d6da;
    padding: 0.5rem;
}

.top-filter label {
    font-weight: 600;
    color: #344767;
    font-size: 0.875rem;
}

/* Make sure the filter form elements are styled nicely */
.top-filter select {
    min-width: 150px;
}

/* Mobile responsive fixes */
@media (max-width: 767px) {
    .top-filter {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .top-filter select, 
    .top-filter input {
        width: 100% !important;
        margin-bottom: 10px;
    }
    
    .card-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .card-header .btn {
        margin-bottom: 5px;
    }
}

/* Datatable styling improvements */
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
    margin-bottom: 15px;
}

.dataTables_wrapper .dataTables_info {
    margin-top: 15px;
}

.dataTables_wrapper .dt-buttons {
    margin-bottom: 15px;
}

.dataTables_wrapper .dt-buttons .btn {
    margin-right: 5px;
}
</style>

<script src="<?= base_url(); ?>assets/js/plugins/datatables.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function () {
    let deleteTestId = null;
    
    // Initialize DataTable
    var table = $('#tests-table').DataTable({
        dom: 'Blfrtip',
        buttons: [{
                extend: 'copyHtml5',
                className: 'btn btn-primary'
            },
            {
                extend: 'excelHtml5',
                className: 'btn btn-primary'
            },
            {
                extend: 'csvHtml5',
                className: 'btn btn-primary'
            },
            {
                extend: 'pdfHtml5',
                className: 'btn btn-primary'
            }
        ],
        "lengthChange": true,
        "searching": true,
        "language": {
            "paginate": {
                next: "Next →",
                previous: "← Previous"
            }
        },
        "drawCallback": function(settings) {
            if (settings.fnRecordsDisplay() <= settings._iDisplayLength) {
                $('#tests-table_paginate').hide();
            } else {
                $('#tests-table_paginate').show();
            }
        },
        "initComplete": function(settings, json) {
            $('#tests-table_paginate').addClass('me-3 text-uppercase text-secondary text-xs font-weight-bolder opacity-7');
            $('.paginate_button').addClass("ms-2 text-uppercase text-secondary text-xs font-weight-bolder opacity-7")
            $('#tests-table_info').addClass('ms-3 text-uppercase text-secondary text-xs font-weight-bolder opacity-7');
            $('#tests-table_filter').addClass('dataTable-search text-uppercase text-secondary text-xs font-weight-bolder opacity-7 me-3');
            $('#tests-table_length').addClass('dataTable-dropdown text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ms-3');
        },
    });
    


    function reloadTable() {
        const moduleId = $("#module-filter").val();
        const startDate = $("#start-date").val();
        const endDate = $("#end-date").val();
        
        let url = "<?= base_url($url.'/test') ?>"; 
        const params = new URLSearchParams();

        if (moduleId) {
            params.append('module', moduleId);
        }
        if (startDate) {
            params.append('start_date', startDate);
        }
        if (endDate) {
            params.append('end_date', endDate);
        }

        if (params.toString()) {
            url += '?' + params.toString();
        }

        window.location.href = url;
    }
    

    // Apply filter button
    $('#apply-filter').on('click', function() {
        reloadTable();
    });
    
    // Reset filter button
    $('#reset-filter').on('click', function() {
        $('#module-filter').val('');
        $('#start-date').val('');
        $('#end-date').val('');
        console.log("Resetting filters...");    
        
        // Clear query parameters and reload the page
        const baseUrl = "<?= base_url($url.'/test') ?>";
        window.location.href = baseUrl;
    });
    // Pre-select values from URL if present
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('module')) {
        $('#module-filter').val(urlParams.get('module'));
    }
    if (urlParams.has('start_date')) {
        $('#start-date').val(urlParams.get('start_date'));
    }
    if (urlParams.has('end_date')) {
        $('#end-date').val(urlParams.get('end_date'));
    }

    // Handle delete button click
    $(document).on("click", ".deleteTest", function () {
        deleteTestId = $(this).data("id");
        $("#confirmDeleteModal").modal("show");
    });
    
    // Handle confirm delete button click
    $("#confirmDeleteBtn").on("click", function () {
        if (deleteTestId) {
            const base_url = "<?= base_url($url.'/test') ?>";
            $.ajax({
                url: base_url + "/delete/" + deleteTestId,
                type: "POST",
                success: function (response) {
                    let result;
                    try {
                        result = typeof response === 'string' ? JSON.parse(response) : response;
                    } catch(e) {
                        console.error("Error parsing response:", e);
                        result = { status: 'error', message: 'Invalid response format' };
                    }
                    
                    if (result.status === 'success') {
                        $("#confirmDeleteModal").modal("hide");
                        location.reload();
                    } else {
                        alert("Error: " + (result.message || "Unknown error"));
                    }
                },
                error: function (xhr) {
                    console.error("Error:", xhr.responseText);
                    alert("An error occurred while deleting the test.");
                }
            });
        }
    });

    // Handle cancel delete
    $("#hideDeleteModal").on("click", function () {
        deleteTestId = null;
        $("#confirmDeleteModal").modal("hide");
    });
});
</script>