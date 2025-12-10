<style>
    .top-filter{
        display: flex; justify-content: flex-end; align-items: center; gap: 10px;
    }
</style>
<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <h4 class="font-weight-bold py-3 mb-0">Question Bank</h4>
        <div class="text-muted small mt-0 mb-4 d-block breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                <!-- <li class="breadcrumb-item">Principal</li> -->
                <li class="breadcrumb-item">View Questions</li>
            </ol>
        </div>
        <div id="flashMessagepage"></div> <!-- Flash message will appear here -->
        <div class="row">
            <div class="col-12">
                <div class="card p-3">
                    <div style="display: flex; justify-content:space-between; align-items: center;
                    border-bottom: 0 solid rgba(24, 28, 33, 0.13);
                    border-color: rgba(24, 28, 33, 0.13);
                    border-radius: 0.125rem 0.125rem 0 0; 
                    border-bottom-width: 1px;">
                        <h6 class="card-header" style="border:none">Question Bank</h6>
                        <div>
                            <a href="#" class="btn btn-primary mr-3" data-toggle="modal" data-target="#questionModal" onclick="resetForm()">Add Question</a>
                            <a href="<?= base_url($url."/SampleCsv/download_sample_csv_questions") ?>" class="btn btn-primary mr-3">Sample CSV</a>
                            <label class="btn btn-primary mr-3" onclick="document.getElementById('csvFileInput').click();">UPLOAD CSV</label>
                            <input type="file" id="csvFileInput" style="display: none;" accept=".csv" onchange="sendCsv(this, '<?= base_url($url.'/question/bulk_add_questions') ?>')">
                        </div>

                    </div>
                    <div class="card-datatable container table-responsive mt-3">
                    <div class="top-filter">
                            <!-- Difficulty Level Filter -->
                            <label for="difficulty-filter" class="form-label mb-0">Filter by Difficulty:</label>
                            <select id="difficulty-filter" class="form-select form-control" style="width: 200px;">
                                <option value="">All Difficulty Levels</option>
                                <?php foreach ($difficulty_levels as $level) : ?>
                                    <?php if(!empty($level)) : ?>
                                        <option value="<?= $level; ?>"><?= $level; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>

                            <!-- Topic Filter -->
                            <label for="topic-filter" class="form-label mb-0">Filter by Topic:</label>
                            <select id="topic-filter" class="form-select form-control" style="width: 200px;">
                                <option value="">All Topics</option>
                                <?php foreach ($topics as $topic) : ?>

                                    <?php if(!empty($topic)) : ?>
                                        <option value="<?= $topic; ?>"><?= $topic; ?></option>
                                    <?php endif; ?>
                                    
                                <?php endforeach; ?>
                            </select>

                            <!-- Tags Filter -->
                            <label for="tags-filter" class="form-label mb-0">Filter by Tags:</label>
                            <select id="tags-filter" class="form-select form-control" style="width: 200px;">
                                <option value="">All Tags</option>
                                <?php foreach ($tags as $tag) : ?>
                                    <?php if(!empty($tag)) : ?>
                                        <option value="<?= $tag; ?>"><?= $tag; ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <table id="mytable" class="datatables-demo table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-center">S.NO</th>
                                    <th>Question</th>
                                    <th>Options</th>
                                    <th>Question Type</th>
                                    <th>Difficulty Level</th>
                                    <th>Topic</th>
                                    <th>Tags</th>
                                    <!-- <th>Public Access</th> -->
                                    <th>Created At</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($questions as $question) : ?>
                                    <tr>
                                        <td class="text-center"><?= $no++; ?></td>
                                        <td><?= $question['question_content']; ?></td>
                                        <td>
                                            <ol type="A">
                                                <?php foreach ($question['options'] as $option) : ?>
                                                    <li <?= $option['is_correct'] ? 'style="font-weight:bold; color:green;"' : ''; ?>>
                                                        <?= $option['option_text']; ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ol>
                                        </td>
                                        <td>
                                        <?php foreach ($question_types as $type)  : ?>

                                            <?php if($question['type'] == $type['id']) : ?>
                                                <?= $type['type']; ?>
                                            <?php endif; ?>
                                            

                                        <?php endforeach; ?>
                                        </td>
                                        <td><?= $question['difficulty_level']; ?></td>
                                        <td><?= $question['topic']; ?></td>
                                        <td><?= $question['tags']; ?></td>
                                        <!-- <td><?= $question['is_public'] == '1' ? 'Yes' : 'No'; ?></td> -->
                                        <td><?= date('d-m-Y', strtotime($question['created_at'])); ?></td>
                                        <td class="text-center">
                                        <?php if (isset($question['is_public']) && $question['is_public'] == '0') : ?>
                                            <button type="button" data-id="<?= $question['id']; ?>" class="btn btn-info edit-question">Edit</button>
                                            <button data-id="<?= $question['id']; ?>" class="btn btn-danger deleteQuestion">Delete</button>
                                        <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <!-- Add/Edit Question Modal -->
                        <!-- Modal for Add/Edit Question -->
                        <div class="modal fade" id="questionModal" tabindex="-1" role="dialog" aria-labelledby="questionModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title" id="questionModalLabel">Add Question</h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" id="closeIconModal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div id="flashMessage"></div>
                                        <form id="questionForm">
                                            <div class="form-group">
                                                <label for="question">Question</label>
                                                <textarea class="form-control" id="question" name="question_content" rows="3" required></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label for="type">Question Type</label>
                                                <select class="form-control" id="type" name="type" required>

                                                <?php foreach ($question_types as $type) : ?>
                                                    <option value="<?= $type['id']; ?>"><?= $type['type']; ?></option>
                                                <?php endforeach; ?>
                                                    <!-- <option value="">Select Type</option>
                                                    <option value="1">Multiple Choice - Single Answer</option>
                                                    <option value="2">Multiple Choice - Multiple Answers</option> -->
                                                </select>
                                            </div>
                                            <div class="form-group">
                                               
                                                <label for="difficulty_level">Difficulty Level</label>
                                                    <input type="text" class="form-control" id="difficulty_level" name="difficulty_level" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="topic">Topic</label>
                                                    <input type="text" class="form-control" id="topic" name="topic" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="tags">Tags</label>
                                                    <input type="text" class="form-control" id="tags" name="tags" placeholder="Comma-separated tags">
                                            </div>
                                            <!-- <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" id="is_public" name="is_public">
                                                    <label class="custom-control-label" for="is_public">Make Public</label>
                                                </div>
                                            </div> -->
                                            <div class="form-group">
                                                <label>Options</label>
                                                <div id="options-container">
                                                    <div class="option-row mb-2">
                                                        <div class="row">
                                                            <div class="col-md-1">
                                                                <div class="custom-control custom-checkbox mt-2">
                                                                    <input type="checkbox" class="custom-control-input correct-option" id="correct_0" name="is_correct[]" value="0">
                                                                    <label class="custom-control-label" for="correct_0"></label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <input type="text" class="form-control" name="option_text[]" placeholder="Option text" required>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <button type="button" class="btn btn-danger btn-sm remove-option">Remove</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-primary btn-sm mt-2" id="add-option">Add Option</button>
                                            </div>
                                            <input type="hidden" id="question_id" name="question_id">
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal" id="hideQuestionModel">Cancel</button>
                                        <button type="submit" class="btn btn-primary" form="questionForm">Save Question</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Add/Edit Question Modal END-->   
                        <!-- Delete Confirmation Modal -->
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
                        <!-- Delete Confirmation Modal END-->                                
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>       
<script>

function sendCsv(event, url) {
    console.log(url);
    const file = event.files[0];

    if (file) {
        console.log("File selected:", file.name);

        const formData = new FormData();
        formData.append("csvFile", file);

       $.ajax({
            url: url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response?.message == "success") {
                    location.reload();
                }
            },
            error: function (xhr, status, error) {
                console.error("Upload failed:", error);
            }
        });
    }
}


$(document).ready(function () {
    let optionCount = 1;
    let deleteQuestionId = null;


    function reloadTable() {
        const difficulty = $("#difficulty-filter").val();
        const topic = $("#topic-filter").val();
        const tags = $("#tags-filter").val();

        let url = window.location.href.split('?')[0]; 
        const params = new URLSearchParams();

        if (difficulty) {
            params.append('difficulty_level', difficulty);
        }
        if (topic) {
            params.append('topic', topic);
        }
        if (tags) {
            params.append('tags', tags);
        }

        if (params.toString()) {
            url += '?' + params.toString();
        }

        window.location.href = url;
    }

    $("#difficulty-filter, #topic-filter, #tags-filter").on("change", function () {
        reloadTable();
    });

    const urlParams = new URLSearchParams(window.location.search);
    $("#difficulty-filter").val(urlParams.get('difficulty_level') || '');
    $("#topic-filter").val(urlParams.get('topic') || '');
    $("#tags-filter").val(urlParams.get('tags') || '');



    $(document).on("click", ".btn-primary[data-target='#questionModal']", function () {
        resetForm();
        $("#questionModalLabel").text("Add Question");
    });

    $("#hideQuestionModel").on("click", function () {
        resetForm();
        $("#questionModal").modal("hide");
    });

    $("#closeIconModal").on("click", function () {
        resetForm();
        $("#questionModal").modal("hide");
    });

    $("#add-option").off("click").on("click", function () {
        const newOptionRow = `
            <div class="option-row mb-2">
                <div class="row">
                    <div class="col-md-1">
                        <div class="custom-control custom-checkbox mt-2">
                            <input type="checkbox" class="custom-control-input correct-option" 
                                id="correct_${optionCount}" name="is_correct[]" value="${optionCount}">
                            <label class="custom-control-label" for="correct_${optionCount}"></label>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="option_text[]" placeholder="Option text" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm remove-option">Remove</button>
                    </div>
                </div>
                <div class="row mt-2 explanation-row" style="display: none;">
                    <div class="col-md-1"></div>
                    <div class="col-md-11">
                        <textarea class="form-control" name="explanation[]" placeholder="Explanation (optional)"></textarea>
                    </div>
                </div>
            </div>
        `;
        $("#options-container").append(newOptionRow);
        optionCount++;
    });

    $(document).on("click", ".remove-option", function () {
        if ($("#options-container .option-row").length > 1) {
            $(this).closest(".option-row").remove();
        } else {
            alert("You must have at least one option.");
        }
    });

    $(document).on("change", ".correct-option", function () {
        const explanationRow = $(this).closest('.option-row').find('.explanation-row');
        if ($(this).is(':checked')) {
            explanationRow.slideDown();
        } else {
            explanationRow.slideUp();
        }

        if ($("#type").val() == "1" && $(this).is(':checked')) {
            $(".correct-option").not(this).prop('checked', false);
            $(".explanation-row").not(explanationRow).slideUp();
        }
    });

    $("#type").on("change", function () {
        $(".correct-option").prop('checked', false);
        $(".explanation-row").slideUp();
    });

    $(document).on("click", ".edit-question", function () {
        const questionId = $(this).data("id");
        const base_url = "<?= base_url($url) ?>";

        $.ajax({
            url: base_url + "/question/get_question/" + questionId,
            type: "GET",
            success: function (response) {
                const questionData = JSON.parse(response);

                if (questionData.error) {
                    alert(questionData.error);
                    return;
                }

                $("#question_id").val(questionData.question.id);
                $("#question").val(questionData.question.question_content);
                $("#type").val(questionData.question.type);
                $("#difficulty_level").val(questionData.question.difficulty_level);
                $("#topic").val(questionData.question.topic);
                $("#tags").val(questionData.question.tags);
                $("#is_public").prop('checked',false);


                $("#options-container").empty();

                questionData.options.forEach(function (option, index) {
                    const optionRow = `
                        <div class="option-row mb-2">
                            <div class="row">
                                <div class="col-md-1">
                                    <div class="custom-control custom-checkbox mt-2">
                                        <input type="checkbox" class="custom-control-input correct-option" 
                                            id="correct_${index}" name="is_correct[]" value="${index}" 
                                            ${option.is_correct == '1' ? 'checked' : ''}>
                                        <label class="custom-control-label" for="correct_${index}"></label>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="option_text[]" 
                                        value="${option.option_text}" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-option">Remove</button>
                                </div>
                            </div>
                            <div class="row mt-2 explanation-row" style="${option.is_correct ? '' : 'display: none;'}">
                                <div class="col-md-1"></div>
                                <div class="col-md-11">
                                    <textarea class="form-control" name="explanation[]" 
                                        placeholder="Explanation (optional)">${option.explanation || ''}</textarea>
                                </div>
                            </div>
                        </div>
                    `;
                    $("#options-container").append(optionRow);
                });

                // Show the modal
                $("#questionModalLabel").text("Edit Question");
                $("#questionModal").modal("show");

            },
            error: function () {
                alert("Error fetching question data!");
            }
        });
    });

    $('#questionForm').off("submit").on('submit', function (e) {
        e.preventDefault();

        if ($('.correct-option:checked').length === 0) {
            $('#flashMessage').html('<div class="alert alert-danger">Please select at least one correct answer.</div>');
            return false;
        }

        const questionData = {
            question_content: $('#question').val(),
            type: $('#type').val(),
            is_public: $('#is_public').is(':checked') ? 1 : 0,
            difficulty_level:$('#difficulty_level').val(),
            topic:$('#topic').val(),
            tags:$('#tags').val()
        };

        const options = [];
        $('.option-row').each(function (index) {
            const optionText = $(this).find('input[name="option_text[]"]').val();
            const isCorrect = $(this).find('.correct-option').is(':checked') ? 1 : 0;
            const explanation = $(this).find('textarea[name="explanation[]"]').val() || '';

            if (optionText) {
                options.push({
                    option_text: optionText,
                    is_correct: isCorrect,
                    explanation: explanation
                });
            }
        });

        const payload = {
            question: questionData,
            options: options
        };

        const questionId = $('#question_id').val();
        const actionUrl = questionId
            ? "<?= base_url($url . '/question/edit_question/') ?>" + questionId
            : "<?= base_url($url . '/question/add_question') ?>";

        $.ajax({
            url: actionUrl,
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(payload),
            dataType: "json",
            success: function (response) {
                if (response.status === 'error') {
                    $('#flashMessage').html('<div class="alert alert-danger">' + response.message + '</div>');
                } else if (response.status === 'success') {
                    $('#questionModal').modal('hide');
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');

                    $('#flashMessagepage').html('<div class="alert alert-success">' + response.message + '</div>');

                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                }
            },
            error: function (xhr) {
                console.error("Error response:", xhr.responseText);
                let errorMessage = "Error saving question!";
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) { }

                $('#flashMessage').html('<div class="alert alert-danger">' + errorMessage + '</div>');
            }
        });
    });

    $(document).on("click", ".deleteQuestion", function () {
            deleteQuestionId = $(this).data("id");
            $("#confirmDeleteModal").modal("show");
    });
    $("#confirmDeleteBtn").on("click", function () {
            if (deleteQuestionId) {
                const base_url = "<?= base_url($url) ?>";
                $.ajax({
                    url: base_url + "/question/delete_question/" + deleteQuestionId,
                    type: "POST",
                    success: function (response) {

                        const result = JSON.parse(response);
                        if (result.status === 'success') {
                            // Close the delete confirmation modal
                            $("#confirmDeleteModal").modal("hide");
                            // Show success message
                            $('#flashMessagepage').html('<div class="alert alert-success">' + result.message + '</div>');
                            // Refresh the page after a short delay
                            deleteQuestionId = null;
                            setTimeout(function () {
                                location.reload();
                            }, 1500);
                        }else if (result.status === 'error') {
                            // Show error message
                            $('#flashMessagepage').html('<div class="alert alert-danger">' + result.message + '</div>');
                                setTimeout(function () {
                                location.reload();
                            }, 1500);
                        }
                    },
                    error: function (response) {
                        // console.error("Error:", response.responseText);
                        // alert("An error occurred while deleting the question.");
                    }
                });
            }
    });

    $(hideDeleteModal).on("click", function () {
        deleteQuestionId = null;
        $("#confirmDeleteModal").modal("hide");
    })


});

// Reset form function
function resetForm() {
    $('#questionForm')[0].reset();
    $('#question_id').val(''); 
    $("#options-container").html(`
        <div class="option-row mb-2">
            <div class="row">
                <div class="col-md-1">
                    <div class="custom-control custom-checkbox mt-2">
                        <input type="checkbox" class="custom-control-input correct-option" id="correct_0" name="is_correct[]" value="0">
                        <label class="custom-control-label" for="correct_0"></label>
                    </div>
                </div>
                <div class="col-md-9">
                    <input type="text" class="form-control" name="option_text[]" placeholder="Option text" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm remove-option">Remove</button>
                </div>
            </div>
            <div class="row mt-2 explanation-row" style="display: none;">
                <div class="col-md-1"></div>
                <div class="col-md-11">
                    <textarea class="form-control" name="explanation[]" placeholder="Explanation (optional)"></textarea>
                </div>
            </div>
        </div>
    `);
}
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