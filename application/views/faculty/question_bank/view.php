<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3 justify-content-between">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Question Bank</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Question Bank</h6>
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
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">×</button>
                            <span class="alert-text" style="color:white">
                                <?= $this->session->flashdata('message')[1] ?>
                            </span>
                        </div>
                    <?php } ?>
                    
                    <div class="d-flex justify-content-between align-items-center"> 
                        <div class="card-header">
                            <h5>Question Bank</h5>
                        </div> 
                        <div class="card-header"> 
                            <button class="btn btn-primary" type="button" onclick="window.location.href='<?= base_url($url.'/question/add') ?>'"><i class="fas fa-plus"></i> Add Question</button>
                            <a href="<?= base_url($url.'/'.$samplePdfurl) ?>" class="btn btn-primary">Sample CSV</a>
                            <button class="btn btn-primary" onclick="document.getElementById('csvFileInput').click();">UPLOAD CSV (MCQ ONLY)</button>
                            <button class="btn btn-primary" onclick="downloadMCQ()"><i class="fas fa-download"></i> Download MCQ</button>
                            <input type="file" id="csvFileInput" style="display: none;" accept=".csv" onchange="sendCsv(this, '<?= base_url($url.'/question/bulk_add_questions/') ?>')">
                        </div> 
                    </div>
                    
                    <div class="card p-2">
                        <div class="top-filter mb-3 d-flex justify-content-center align-items-center gap-3">
                            <!-- Difficulty Level Filter -->
                            <label for="difficulty-filter" class="form-label mb-0 me-2">Filter by Difficulty:</label>
                            <select id="difficulty-filter" class="form-select form-control" style="width: 200px;">
                                <option value="">All Difficulty Levels</option>
                                <?php foreach ($difficulty_levels as $level) : ?>
                                    <?php if(!empty($level)) : ?>
                                        <option value="<?= $level['id']; ?>"><?= $level['level']; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>

                            <!-- Tags Filter -->
                            <label for="tags-filter" class="form-label mb-0 me-2">Filter by Topic:</label>
                            <select id="tags-filter" class="form-select form-control" style="width: 200px;">
                                <option value="">All Topics</option>
                                <?php foreach ($tags as $tag) : ?>
                                    <?php if(!empty($tag)) : ?>
                                        <option value="<?= $tag; ?>"><?= $tag; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>


                                <!-- Type Filter -->
                            <label for="type-filter" class="form-label mb-0 me-2">Filter by Type:</label>
                            <select id="types-filter" class="form-select form-control" style="width: 200px;">
                                <option value="">All Types</option>
                                <?php foreach ($question_types as $question_type) : ?>
                                    <?php if(!empty($question_type)) : ?>
                                        <option value="<?= $question_type['id']; ?>"><?= $question_type['type']; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>

                        </div>
                        
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0 " id="data-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center text-uppercase  text-sm font-weight-bolder ">S.No</th>
                                        <th class="text-uppercase  text-sm font-weight-bolder ">Problem Title</th>
                                        <th class="text-uppercase  text-sm font-weight-bolder ">Problem Type</th>
                                        <th class="text-uppercase  text-sm font-weight-bolder ">Sub Type</th>
                                        <th class="text-uppercase  text-sm font-weight-bolder ">Score</th>
                                        <th class="text-uppercase  text-sm font-weight-bolder ">Difficulty</th>
                                        <th class="text-uppercase  text-sm font-weight-bolder ">Topic</th>
                                        <th class="text-center text-uppercase  text-sm font-weight-bolder ">Created At</th>
                                        <th class="text-center text-uppercase  text-sm font-weight-bolder ">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($questions as $question) : ?>
                                        <tr>
                                            <td class="align-middle text-center text-sm">
                                                <span class="text-secondary text-sm font-weight-bold"><?= $no++; ?></span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="text-secondary text-sm font-weight-bold"><?= isset($question['question_title']) ? $question['question_title'] : 'Problem ' . $no; ?></span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="text-secondary text-sm font-weight-bold">
                                                    <?php 
                                                    $questionType = 'Unknown';
                                                    foreach ($question_types as $type) :
                                                        if($question['type'] == $type['id']) :
                                                            $questionType = $type['type'];
                                                        endif;
                                                    endforeach;
                                                    echo $questionType;
                                                    ?>
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="text-secondary text-sm font-weight-bold">
                                                    <?php 
                                                    $subType = '';
                                                    if (isset($question['sub_type'])) {
                                                        foreach ($question_sub_types as $subTypeItem) :
                                                            if($question['sub_type'] == $subTypeItem['id']) :
                                                                $subType = $subTypeItem['sub_type'];
                                                            endif;
                                                        endforeach;
                                                    }
                                                    echo $subType;
                                                    ?>
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="text-secondary text-sm font-weight-bold"><?= isset($question['score']) ? $question['score'] : ''; ?></span>
                                            </td>
                                            <td class="align-middle">
                                                <span class="text-secondary text-sm font-weight-bold">
                                                    <?php 
                                                    // Get the actual difficulty level name
                                                    $difficultyName = $question['difficulty_level'];
                                                    echo $difficultyName;
                                                    ?>
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                <?php if (!empty($question['tags'])) : ?>
                                                    <div class="tag-chips">
                                                        <?php 
                                                        $tagsList = explode(',', $question['tags']);
                                                        foreach ($tagsList as $tag) :
                                                            if (!empty(trim($tag))) :
                                                        ?>
                                                            <span class="tag-chip"><?= trim($tag) ?></span>
                                                        <?php 
                                                            endif;
                                                        endforeach; 
                                                        ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            
                                            <td class="align-middle text-center">
                                                <span class="text-secondary text-sm font-weight-bold"><?= date('d-m-Y', strtotime($question['created_at'])); ?></span>
                                            </td>
                                            <td class="align-middle text-center">
                                            <button type="button" data-id="<?= $question['id']; ?>" class="btn btn-info btn-sm view-question">View</button>
                                                <button type="button" data-id="<?= $question['id']; ?>" onclick="editQuestion('<?= $question['id']; ?>')" class="btn btn-warning  btn-sm edit-question">Edit</button>
                                                <button data-id="<?= $question['id']; ?>" class="btn btn-danger btn-sm deleteQuestion">Delete</button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div id="confirmDeleteModal" class="modal fade" tabindex="-1" role="dialog">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Confirm Delete</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p>Do you really want to delete this question?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="hideDeleteModal">Cancel</button>
                                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Yes, Delete</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Question Modal -->
                <div class="modal fade" id="viewQuestionModal" tabindex="-1" aria-labelledby="viewQuestionModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewQuestionModalLabel">Question Details</h5>
                                <button type="button" id="closeViewQuestionModal"  class="btn-close" data-bs-dismiss="modal" aria-label="Close"  ></button>
                            </div>
                            <div class="modal-body">
                                <!-- Content will be loaded dynamically -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="closeViewQuestionModal" class="btn btn-secondary" data-bs-dismiss="modal"  >Close</button>
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

/* View Question Modal Styles */
.question-view .markdown-content {
    line-height: 1.6;
}

.question-view .markdown-content pre {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 4px;
    overflow-x: auto;
}

.question-view .markdown-content code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 3px;
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
}

.question-view .options-list .option-item {
    padding: 0.5rem;
    border-radius: 4px;
    margin-bottom: 0.5rem;
    background-color: #f8f9fa;
}

.question-view .options-list .correct-option {
    background-color: #e8f5e9;
    border-left: 4px solid #4caf50;
}

.question-view .option-number {
    font-weight: bold;
    margin-right: 0.5rem;
}

.question-view .test-cases-list pre {
    white-space: pre-wrap;
    word-wrap: break-word;
    margin-bottom: 0;
}

/* Syntax highlighting */
.hljs {
    background: transparent !important;
    padding: 0 !important;
}



.tag-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
}

.tag-chip {
    background-color: #4a6cf7;
    color: white;
    border-radius: 50px;
    padding: 2px 10px;
    font-size: 0.75rem;
    display: inline-block;
    white-space: nowrap;
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
    
    .top-filter select {
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

/* Add these styles for fixed action column */
.table-responsive {
    overflow-x: auto;
}
.fixed-action-column {
    position: sticky !important;
    right: 0;
    background: white;
    z-index: 1;
}
.fixed-action-column::after {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 1px;
    background-color: #dee2e6;
}
.table-striped tbody tr:nth-of-type(odd) .fixed-action-column {
    background-color: #f2f2f2;
}
.table-striped tbody tr:nth-of-type(even) .fixed-action-column {
    background-color: #fff;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.loading-overlay .spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>

<script src="<?= base_url(); ?>assets/js/plugins/datatables.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="<?= base_url("/") ?>assets/packages/marked.min.js"></script>


<script>
$(document).ready(function () {


    // View Question Modal
const viewQuestionModal = new bootstrap.Modal(document.getElementById('viewQuestionModal'));


$(document).on('click','#closeViewQuestionModal',function(){
    viewQuestionModal.hide()
})



// Handle view button click
$(document).on('click', '.view-question', function() {
    const questionId = $(this).data('id');
    const baseUrl = "<?= base_url($url) ?>";
    
    // Show loading state
    $('#viewQuestionModal .modal-body').html('<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');
    viewQuestionModal.show();
    
    // Fetch question details
    $.ajax({
        url: baseUrl + "/question/view/" + questionId,
        type: "GET",
        success: function(response) {
            const data = JSON.parse(response)
            if (data.status === 'success') {
                renderQuestionDetails(data.question);
            } else {
                $('#viewQuestionModal .modal-body').html('<div class="alert alert-danger">' + (data.message || 'Error loading question') + '</div>');
            }
        },
        error: function(xhr) {
            console.error("Error:", xhr.responseText);
            $('#viewQuestionModal .modal-body').html('<div class="alert alert-danger">Error loading question details</div>');
        }
    });
});

// Render question details in modal
    function renderQuestionDetails(question) {
        let html = `
            <div class="question-view">
                <div class="mb-4">
                    <h5 class="text-primary">${question.question_title || 'Question'}</h5>
                    <div class="markdown-content">
                    <pre>${marked.parse(question.question_content)}</pre>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Type:</strong> ${question.type_name}
                    </div>
                    <div class="col-md-4">
                        <strong>Sub Type:</strong> ${question.sub_type_name || 'N/A'}
                    </div>
                    <div class="col-md-4">
                        <strong>Score:</strong> ${question.score}
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <strong>Difficulty:</strong> ${question.difficulty_name}
                    </div>
                    <div class="col-md-4">
                        <strong>Created At:</strong> ${new Date(question.created_at).toLocaleDateString()}
                    </div>
                </div>
        `;
        
        // Display tags if available
        if (question.tags_array.length > 0) {
            html += `
                <div class="mb-3">
                    <strong>Topics:</strong>
                    <div class="tag-chips mt-2">
                        ${question.tags_array.map(tag => `<span class="tag-chip">${tag}</span>`).join('')}
                    </div>
                </div>
            `;
        }
        
        // Display options if MCQ
        if (question.type == 1 && question.options && question.options.length > 0) {
            html += `
                <div class="mb-3">
                    <strong>Options:</strong>
                    <div class="options-list mt-2">
                        ${question.options.map((option, index) => `
                            <div class="option-item ${option.is_correct == "1" ? 'correct-option' : ''}">
                                <span class="option-number">${index + 1}.</span>
                                <span class="option-text">${option.option_text}</span>
                                ${option.is_correct == "1" ? '<span class="badge bg-success ms-2">Correct</span>' : ''}
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }
        
        // Display test cases if CODE
        if (question.type == 2 && question.test_cases && question.test_cases.length > 0) {
            html += `
                <div class="mb-3">
                    <strong>Test Cases:</strong>
                    <div class="test-cases-list mt-2">
                        ${question.test_cases.map((testCase, index) => `
                            <div class="test-case-item mb-3 p-3 bg-light rounded">
                                <div class="row">
                                    <div class="col-md-5">
                                        <strong>Input:</strong>
                                        <pre class="bg-white p-2 mt-1">${testCase.input}</pre>
                                    </div>
                                    <div class="col-md-5">
                                        <strong>Output:</strong>
                                        <pre class="bg-white p-2 mt-1">${testCase.output}</pre>
                                    </div>
                                    <div class="col-md-2">
                                        <span class="badge ${testCase.visibility == "1" ? 'bg-info' : 'bg-secondary'}">
                                            ${testCase.visibility == "1" ? 'Visible' : 'Hidden'}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }
        
        // Display explanation if available
        if (question.explanation) {
            html += `
                <div class="mb-3">
                    <strong>Explanation:</strong>
                    <div class="markdown-content mt-2">${marked.parse(question.explanation)}</div>
                </div>
            `;
        }
        
        html += `</div>`;
        
        $('#viewQuestionModal .modal-body').html(html);
        
        // Apply syntax highlighting to code blocks
        document.querySelectorAll('#viewQuestionModal pre code').forEach((block) => {
            hljs.highlightElement(block);
        });
 }




    let deleteQuestionId = null;

    var table = $('#data-table').DataTable({
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
                className: 'btn btn-primary',
                bom: true
            },
            {
                extend: 'pdfHtml5',
                className: 'btn btn-primary'
            }
        ],
        "lengthChange": true,
        "searching": true,
        "scrollX": true,
        "columnDefs": [
            {
                "targets": -1,
                "className": "fixed-action-column"
            }
        ],
        "language": {
            "paginate": {
                next: "Next →",
                previous: "← Previous"
            }
        },
        "drawCallback": function(settings) {
            if (settings.fnRecordsDisplay() <= settings._iDisplayLength) {
                $('#data-table_paginate').hide();
            } else {
                $('#data-table_paginate').show();
            }
        },
        "initComplete": function(settings, json) {
            $('#data-table_paginate').addClass('me-3 text-uppercase text-secondary text-xs font-weight-bolder opacity-7');
            $('.paginate_button').addClass("ms-2 text-uppercase text-secondary text-xs font-weight-bolder opacity-7")
            $('#data-table_info').addClass('ms-3 text-uppercase text-secondary text-xs font-weight-bolder opacity-7');
            $('#data-table_filter').addClass('dataTable-search text-uppercase text-secondary text-xs font-weight-bolder opacity-7 me-3');
            $('#data-table_length').addClass('dataTable-dropdown text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ms-3');
        },
    });

    function reloadTable() {
        const difficulty = $("#difficulty-filter").val();
        const tags = $("#tags-filter").val();
        const types = $("#types-filter").val();

        let url = window.location.href.split('?')[0]; 
        const params = new URLSearchParams();

        if (difficulty) {
            params.append('difficulty_level', difficulty);
        }
        if (tags) {
            params.append('tags', tags);
        }

        if(types){
            params.append('types',types)
        }

        if (params.toString()) {
            url += '?' + params.toString();
        }

        window.location.href = url;
    }

    $("#difficulty-filter, #tags-filter,#types-filter").on("change", function () {
        reloadTable();
    });

    const urlParams = new URLSearchParams(window.location.search);
    $("#difficulty-filter").val(urlParams.get('difficulty_level') || '');
    $("#tags-filter").val(urlParams.get('tags') || '');
    $("#types-filter").val(urlParams.get('types') || '');

    $(document).on("click", ".deleteQuestion", function () {
        deleteQuestionId = $(this).data("id");
        $("#confirmDeleteModal").modal("show");
    });
    
    $("#confirmDeleteBtn").on("click", function () {
        if (deleteQuestionId) {
            const base_url = "<?= base_url($url) ?>";
            $.ajax({
                url: base_url + "/question/delete/" + deleteQuestionId,
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
                    alert("An error occurred while deleting the question.");
                }
            });
        }
    });

    $("#hideDeleteModal").on("click", function () {
        deleteQuestionId = null;
        $("#confirmDeleteModal").modal("hide");
    });
});

function sendCsv(event, url) {
    const file = event.files[0];

    if (file) {
        // Show loading state
        const loadingOverlay = $('<div class="loading-overlay"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><div class="mt-2">Uploading CSV file...</div></div>');
        $('body').append(loadingOverlay);

        const formData = new FormData();
        formData.append("csvFile", file);
        console.log(url);
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                console.log("Raw response:", response);
                try {
                    const result = JSON.parse(response);
                    console.log(result);
                    if (result.status === 'success') {
                        // Redirect to the report page
                        window.location.href = '<?= base_url($url) ?>/question/upload_report';
                    } else {
                        alert(result.message || 'Upload failed');
                        loadingOverlay.remove();
                    }
                } catch (e) {
                    console.error("Error parsing response:", e);
                    alert("An error occurred while processing the upload");
                    loadingOverlay.remove();
                }
            },
            error: function (xhr, status, error) {
                console.error("Upload failed:", error);
                alert("CSV upload failed: " + error);
                loadingOverlay.remove();
            }
        });
    }
}

function editQuestion(id) {
    const base_url = "<?= base_url($url) ?>";
    location.href = base_url + "/question/edit/" + id;
}

function downloadMCQ() {
    const tags = $("#tags-filter").val();
    const difficulty = $("#difficulty-filter").val();
    const types = $("#types-filter").val();
    
    let url = '<?= base_url($url) ?>/question/download_mcq';
    const params = new URLSearchParams();
    
    if (tags) {
        params.append('tags', tags);
    }
    if (difficulty) {
        params.append('difficulty_level', difficulty);
    }
    if (types) {
        params.append('types', types);
    }
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    window.location.href = url;
}
</script>