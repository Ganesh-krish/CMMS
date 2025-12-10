<!-- Main view file content -->
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <!-- Existing navbar code - keep as is -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3 justify-content-between">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= base_url($url.'/question') ?>">Question Bank</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page"><?= isset($question) ? 'Edit Question' : 'Add Question' ?></li>
                </ol>
                <h6 class="font-weight-bolder mb-0"><?= isset($question) ? 'Edit Question' : 'Add Question' ?></h6>
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
                    <div class="container mt-3">
                        <div class="card-body">
                            <form id="questionForm" action="<?= isset($question) ? base_url($url."/question/update/".$question['id']) : base_url($url.'/question/create') ?>" method="post">
                                <!-- Problem Title Field -->
                                <div class="mb-4">
                                    <label for="problem_title" class="form-label">Problem Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="problem_title" name="question_title" 
                                        value="<?= isset($question) ? htmlspecialchars($question['question_title'] ?? '') : '' ?>" required>
                                </div>

                                <!-- Markdown Editor -->
                                <div class="mb-4">
                                <label for="problem_title" class="form-label">Problem Description/Instructions <span class="text-danger">*</span></label>
                                    <div class="markdown-editor-wrapper">
                                        <div class="markdown-tabs">
                                            <div class="tab-buttons">
                                                <button type="button" class="tab-btn active" data-mode="write">WRITE</button>
                                                <button type="button" class="tab-btn" data-mode="preview">PREVIEW</button>
                                            </div>
                                            <div class="formatting-toolbar">
                                                <button type="button" class="tool-btn" data-command="heading" title="Heading"><i class="fas fa-heading"></i></button>
                                                <button type="button" class="tool-btn" data-command="bold" title="Bold"><i class="fas fa-bold"></i></button>
                                                <button type="button" class="tool-btn" data-command="italic" title="Italic"><i class="fas fa-italic"></i></button>
                                                <button type="button" class="tool-btn" data-command="table" title="Table"><i class="fas fa-table"></i></button>
                                                <button type="button" class="tool-btn" data-command="list-ul" title="Unordered List"><i class="fas fa-list-ul"></i></button>
                                                <button type="button" class="tool-btn" data-command="list-ol" title="Ordered List"><i class="fas fa-list-ol"></i></button>
                                                <button type="button" class="tool-btn" data-command="quote" title="Quote"><i class="fas fa-quote-right"></i></button>
                                                <button type="button" class="tool-btn" data-command="strikethrough" title="Strikethrough"><i class="fas fa-strikethrough"></i></button>
                                                <button type="button" class="tool-btn" data-command="code" title="Code"><i class="fas fa-code"></i></button>
                                                <button type="button" class="tool-btn" data-command="link" title="Link"><i class="fas fa-link"></i></button>
                                                <button type="button" class="tool-btn" data-command="image" title="Image"><i class="fas fa-image"></i></button>
                                            </div>
                                        </div>
                                        <div class="editor-content-area">
                                            <textarea class="form-control" id="markdown-editor" rows="8" placeholder="Enter the problem Description/Instructions"><?= isset($question) ? htmlspecialchars($question['question_content']) : '' ?></textarea>
                                            <div class="preview-content" style="display: none;"></div>
                                        </div>
                                        <!-- Hidden input to store markdown content for form submission -->
                                        <input type="hidden" id="question" name="question_content" value="<?= isset($question) ? htmlspecialchars($question['question_content']) : '' ?>" required>
                                    </div>
                                </div>

                                <!-- Score, Difficulty Level, and Tags in a row -->
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label for="score" class="form-label">Score (1-100) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="score" name="score" 
                                            value="<?= isset($question) ? htmlspecialchars($question['score'] ?? '1') : '1' ?>" 
                                            min="1" max="100" required>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label for="difficulty_level" class="form-label">Difficulty Level</label>
                                        <select class="form-select" id="difficulty_level" name="difficulty_level">
                                            <option value="1" <?= isset($question) && $question['difficulty_level'] == '1' ? 'selected' : '' ?>>Easy</option>
                                            <option value="2" <?= isset($question) && $question['difficulty_level'] == '2' ? 'selected' : '' ?>>Medium</option>
                                            <option value="3" <?= isset($question) && $question['difficulty_level'] == '3' ? 'selected' : '' ?>>Hard</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <label for="tags" class="form-label">Problem Topic</label>
                                        <div class="tags-container">
                                            <select class="form-select select2-tags" id="tags-select" multiple>
                                                <?php foreach ($tags as $tag) : ?>
                                                <option value="<?= $tag ?>" <?= (isset($question) && strpos($question['tags'] ?? '', $tag) !== false) ? 'selected' : '' ?>>
                                                    <?= $tag ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <input type="hidden" id="tags" name="tags" value="<?= isset($question) ? htmlspecialchars($question['tags'] ?? '') : '' ?>">
                                        </div>
                                    </div>
                                </div>

                                <!-- Question Type Cards -->
                                <div class="mb-4">
                                    <label class="form-label">Select Problem Type:</label>
                                    <div class="row question-type-cards">
                                        <?php foreach ($question_types as $q_type) : ?>
                                        <div class="col-md-3 mb-3">
                                            <div class="card question-type-card" data-type="<?= $q_type['id'] ?>">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?= $q_type['type'] ?></h5>
                                                    <p class="card-text">
                                                        <?php if ($q_type['type'] == 'MCQ') : ?>
                                                            MCQ style problems with single/multiple correct answers.
                                                        <?php elseif ($q_type['type'] == 'CODE') : ?>
                                                            Coding problems with test cases Ex. Python, MySQL etc.
                                                        <?php elseif ($q_type['type'] == 'FILL IN THE BLANK') : ?>
                                                            Complete partial statements with missing words/phrases
                                                        <?php else : ?>
                                                            <?= $q_type['type'] ?> type questions
                                                        <?php endif; ?>
                                                    </p>
                                                    <input type="radio" name="type" value="<?= $q_type['id'] ?>" class="visually-hidden question-type-radio"
                                                        <?= (isset($question) && $question['type'] == $q_type['id']) ? 'checked' : 
                                                            (!isset($question) && $q_type['type'] == 'MCQ' ? 'checked' : '') ?>>
                                                </div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <!-- Sub Type Selection - Dynamic based on question type -->
                                <div id="subtype-container" class="mb-4">
                                    <!-- MCQ Sub Type - Single/Multi Select -->
                                    <div id="mcq-subtype" class="subtype-section">
                                        <label class="form-label">Select Type</label>
                                        <div class="d-flex">
                                            <?php 
                                            $mcq_subtypes = array_filter($question_sub_types, function($st) { return $st['type_id'] == 1; });
                                            foreach ($mcq_subtypes as $subtype) : 
                                            ?>
                                            <div class="form-check me-4">
                                                <input class="form-check-input" type="radio" name="sub_type" 
                                                    id="subtype_<?= $subtype['id'] ?>" value="<?= $subtype['id'] ?>"
                                                    <?= (isset($question) && $question['sub_type'] == $subtype['id']) ? 'checked' : 
                                                        (!isset($question) && $subtype['sub_type'] == 'MCQ-SINGLE ANSWER' ? 'checked' : '') ?>>
                                                <label class="form-check-label" for="subtype_<?= $subtype['id'] ?>">
                                                    <?= str_replace('MCQ-', '', $subtype['sub_type']) ?>
                                                </label>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <!-- CODE Sub Type - Programming/Web/Database -->
                                    <div id="code-subtype" class="subtype-section">
                                        <label class="form-label">Select Language Type:</label>
                                        <div class="row">
                                            <?php 
                                            $code_subtypes = array_filter($question_sub_types, function($st) { return $st['type_id'] == 2; });
                                            foreach ($code_subtypes as $subtype) : 
                                                $subtype_class = '';
                                                $subtype_examples = '';
                                                
                                                switch($subtype['sub_type']) {
                                                    case 'PROGRAMMING LANGUAGE':
                                                        $subtype_class = 'programming-languages';
                                                        $subtype_examples = 'ex. Java, Python, C & more...';
                                                        break;
                                                    case 'WEB LANGUAGE':
                                                        $subtype_class = 'web-languages';
                                                        $subtype_examples = 'ex. HTML, JS, JQuery & more...';
                                                        break;
                                                    case 'DATABASE':
                                                        $subtype_class = 'databases';
                                                        $subtype_examples = 'ex. MySQL, MongoDB & more...';
                                                        break;
                                                }
                                            ?>
                                            <div class="col-md-4 mb-3">
                                                <div class="card language-type-card <?= $subtype_class ?>" data-subtype="<?= $subtype['id'] ?>">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?= ucwords(strtolower(str_replace('_', ' ', $subtype['sub_type']))) ?></h5>
                                                        <p class="card-text"><?= $subtype_examples ?></p>
                                                        <input type="radio" name="sub_type" value="<?= $subtype['id'] ?>" class="visually-hidden language-type-radio"
                                                            <?= (isset($question) && $question['sub_type'] == $subtype['id']) ? 'checked' : '' ?>>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Language Selection - Dynamic based on code subtype -->
                                <div id="language-selection-container" class="mb-4">
                                    <!-- Programming Languages -->
                                    <div id="programming-languages-section" class="language-section">
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="select_all_programming" value="1">
                                                <label class="form-check-label" for="select_all_programming">
                                                    Select All Programming Languages
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <?php 
                                            $programming_languages = ['Java', 'Python', 'C', 'C++', 'NodeJS', 'JavaScript', 'Groovy', 'JShell', 'Haskell', 'Tcl', 
                                                                    'Lua', 'Ada', 'CommonLisp', 'D', 'Elixir', 'Erlang', 'F#', 'Fortran', 'Assembly', 'Scala', 
                                                                    'PHP', 'Python2', 'C#', 'Perl', 'Ruby', 'Go', 'R', 'Racket', 'OCaml', 'Visual Basic', 
                                                                    'Basic', 'Bash', 'Clojure', 'TypeScript', 'Cobol', 'Kotlin', 'Pascal', 'Prolog', 'Rust', 
                                                                    'Swift', 'Objective-C', 'Octave', 'Text', 'BrainFK', 'CoffeeScript', 'EJS', 'Dart', 'Deno', 'Bun'];


                                                
                                            
                                            $selected_languages = isset($question) && isset($question['selected_languages']) ? 
                                                                explode(',', $question['selected_languages']) : [];
                                            
                                            foreach ($programming_languages as $lang) :
                                            ?>
                                            <div class="col-md-3 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input programming-language" type="checkbox" 
                                                        id="lang_<?= strtolower(preg_replace('/[^a-z0-9]/i', '_', $lang)) ?>" 
                                                        value="<?= $lang ?>"
                                                        <?= in_array(strtolower($lang), $selected_languages) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="lang_<?= strtolower(preg_replace('/[^a-z0-9]/i', '_', $lang)) ?>">
                                                        <?= $lang ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <input type="hidden" id="selected_languages" name="selected_languages" value="<?= isset($question) ? ($question['selected_languages'] ?? '') : '' ?>">
                                    </div>
                                    
                                    <!-- Web Languages -->
                                    <div id="web-languages-section" class="language-section">
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="select_all_web" value="1">
                                                <label class="form-check-label" for="select_all_web">
                                                    Select All Web Languages
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <?php 
                                            $web_languages = ['HTML', 'Materialize', 'Bootstrap', 'JQuery', 'Foundation', 'Bulma', 'UIkit', 'Semantic UI', 
                                                           'Skeleton', 'Milligram', 'PaperCSS', 'BackboneJS', 'React', 'Vue', 'Angular'];
                                            
                                            foreach ($web_languages as $lang) :
                                            ?>
                                            <div class="col-md-3 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input web-language" type="checkbox" 
                                                        id="lang_<?= strtolower(preg_replace('/[^a-z0-9]/i', '_', $lang)) ?>" 
                                                        value="<?= $lang ?>"
                                                        <?= in_array(strtolower($lang), $selected_languages) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="lang_<?= strtolower(preg_replace('/[^a-z0-9]/i', '_', $lang)) ?>">
                                                        <?= $lang ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Databases -->
                                    <div id="databases-section" class="language-section">
                                        <div class="mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="select_all_databases" value="1">
                                                <label class="form-check-label" for="select_all_databases">
                                                    Select All Databases
                                                </label>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <?php 
                                            $databases = ['MySQL', 'Oracle Database', 'PostgreSQL', 'MongoDB', 'SQLite', 'Redis', 'MariaDB', 
                                                        'Cassandra', 'Oracle PL/SQL', 'Microsoft SQL Server'];
                                            
                                            foreach ($databases as $db) :
                                            ?>
                                            <div class="col-md-3 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input database" type="checkbox" 
                                                        id="lang_<?= strtolower(preg_replace('/[^a-z0-9]/i', '_', $db)) ?>" 
                                                        value="<?= $db ?>"
                                                        <?= in_array(strtolower($db), $selected_languages) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="lang_<?= strtolower(preg_replace('/[^a-z0-9]/i', '_', $db)) ?>">
                                                        <?= $db ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Test Cases Container - For CODE questions -->
                                <div id="test-cases-container" class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Add Test Cases:</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="ignore_case" name="ignore_case" value="1"
                                                <?= isset($question) && isset($question['ignore_case']) && $question['ignore_case'] ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="ignore_case">
                                                Ignore case while validating output
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div id="test-cases-list">
                                        <?php 
                                        if (isset($question) && isset($question['test_cases']) && !empty($question['test_cases'])) {
                                            foreach ($question['test_cases'] as $index => $test_case) {
                                        ?>
                                        <div class="test-case-row mb-3">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label class="form-label">Input</label>
                                                    <textarea class="form-control test-input" rows="3"><?= htmlspecialchars($test_case['input']) ?></textarea>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label">Output</label>
                                                    <textarea class="form-control test-output" rows="3"><?= htmlspecialchars($test_case['output']) ?></textarea>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <button type="button" class="btn btn-danger remove-test-case"><i class="fas fa-trash"></i></button>
                                                    <button type="button" class="btn btn-secondary ms-2 toggle-test-case"><i class="fas fa-eye"></i></button>
                                                </div>
                                            </div>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input test-visibility" type="checkbox" value="1" 
                                                    <?= $test_case['visibility'] ? 'checked' : '' ?>>
                                                <label class="form-check-label">
                                                    Visible to students
                                                </label>
                                            </div>
                                        </div>
                                        <?php 
                                            }
                                        } else {
                                        ?>
                                        <div class="test-case-row mb-3">
                                            <div class="row">
                                                <div class="col-md-5">
                                                    <label class="form-label">Input</label>
                                                    <textarea class="form-control test-input" rows="3" placeholder="Peter"></textarea>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="form-label">Output</label>
                                                    <textarea class="form-control test-output" rows="3" placeholder="Hello Peter"></textarea>
                                                </div>
                                                <div class="col-md-2 d-flex align-items-center">
                                                    <button type="button" class="btn btn-danger remove-test-case"><i class="fas fa-trash"></i></button>
                                                    <button type="button" class="btn btn-secondary ms-2 toggle-test-case"><i class="fas fa-eye"></i></button>
                                                </div>
                                            </div>
                                            <div class="form-check mt-2">
                                                <input class="form-check-input test-visibility" type="checkbox" value="1" checked>
                                                <label class="form-check-label">
                                                    Visible to students
                                                </label>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    
                                    <div class="info-box mb-3 p-3 bg-light rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-info-circle me-2 text-primary"></i>
                                            <div>
                                                <span class="info-text programming-info">Please write valid code, return value on the success condition.</span>
                                                <span class="info-text web-info">Please write valid Javascript code, return true on the success condition.</span>
                                                <span class="info-text database-info">The queries from the Test Cases will be executed on a database after executing user's code.</span>
                                                <a href="#" class="ms-2 small">More Information</a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="button" class="btn btn-primary btn-sm" id="add-test-case">
                                        <i class="fas fa-plus"></i> ADD TEST CASE
                                    </button>
                                </div>
                                
                                <!-- MCQ Options Container -->
                                <div id="options-container" class="mb-4">
                                    <label class="form-label">Options</label>
                                    <div id="options-list">
                                        <?php if (isset($question) && !empty($question['options'])) : ?>
                                            <?php foreach ($question['options'] as $index => $option) : ?>
                                                <div class="option-row mb-3">
                                                    <div class="input-group">
                                                        <span class="input-group-text"><?= $index + 1 ?>. Answer</span>
                                                        <input type="text" class="form-control option-text" name="option_text[]" 
                                                            value="<?= htmlspecialchars($option['option_text']) ?>" placeholder="Answer" required>
                                                        <button type="button" class="btn btn-danger remove-option"><i class="fas fa-trash"></i></button>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="option-row mb-3">
                                                <div class="input-group">
                                                    <span class="input-group-text">1. Answer</span>
                                                    <input type="text" class="form-control option-text" name="option_text[]" placeholder="Answer" required>
                                                    <button type="button" class="btn btn-danger remove-option"><i class="fas fa-trash"></i></button>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-sm" id="add-option">
                                        <i class="fas fa-plus"></i> Add Option
                                    </button>
                                </div>

                                <!-- Answer selection for MCQ -->
                                <div id="answers-container" class="mb-4">
                                    <label class="form-label">Answer(s)</label>
                                    <div id="answers-list">
                                        <?php if (isset($question) && !empty($question['options'])) : ?>
                                            <?php foreach ($question['options'] as $index => $option) : ?>
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" class="form-check-input answer-checkbox" 
                                                        id="answer_<?= $index ?>" name="is_correct[]" value="<?= $index ?>"
                                                        <?= $option['is_correct'] ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="answer_<?= $index ?>">
                                                        <?= $index + 1 ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="form-check mb-2">
                                                <input type="checkbox" class="form-check-input answer-checkbox" 
                                                    id="answer_0" name="is_correct[]" value="0">
                                                <label class="form-check-label" for="answer_0">
                                                    1
                                                </label>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Fill in the Blank Answer -->
                                <div id="fill-blank-container" class="mb-4">
                                    <label class="form-label">Answer</label>
                                    <textarea class="form-control" id="fill-blank-answer" name="fill_blank_answer" rows="4" placeholder="Enter the answer to be filled in the blank"><?= isset($question) && isset($question['fill_blank_answer']) ? htmlspecialchars($question['fill_blank_answer']) : '' ?></textarea>
                                </div>

                                <!-- Explanation Field (Optional) -->
                                <div class="mb-4">
                                    <label for="explanation" class="form-label">Explanation (Optional)</label>
                                    <div class="markdown-editor-wrapper">
                                        <div class="markdown-tabs">
                                            <div class="tab-buttons">
                                                <button type="button" class="tab-btn active" data-mode="write">WRITE</button>
                                                <button type="button" class="tab-btn" data-mode="preview">PREVIEW</button>
                                            </div>
                                            <div class="formatting-toolbar">
                                                <button type="button" class="tool-btn" data-command="heading" title="Heading"><i class="fas fa-heading"></i></button>
                                                <button type="button" class="tool-btn" data-command="bold" title="Bold"><i class="fas fa-bold"></i></button>
                                                <button type="button" class="tool-btn" data-command="italic" title="Italic"><i class="fas fa-italic"></i></button>
                                                <button type="button" class="tool-btn" data-command="table" title="Table"><i class="fas fa-table"></i></button>
                                                <button type="button" class="tool-btn" data-command="list-ul" title="Unordered List"><i class="fas fa-list-ul"></i></button>
                                                <button type="button" class="tool-btn" data-command="list-ol" title="Ordered List"><i class="fas fa-list-ol"></i></button>
                                                <button type="button" class="tool-btn" data-command="quote" title="Quote"><i class="fas fa-quote-right"></i></button>
                                                <button type="button" class="tool-btn" data-command="strikethrough" title="Strikethrough"><i class="fas fa-strikethrough"></i></button>
                                                <button type="button" class="tool-btn" data-command="code" title="Code"><i class="fas fa-code"></i></button>
                                                <button type="button" class="tool-btn" data-command="link" title="Link"><i class="fas fa-link"></i></button>
                                                <button type="button" class="tool-btn" data-command="image" title="Image"><i class="fas fa-image"></i></button>
                                            </div>
                                        </div>
                                        <div class="editor-content-area">
                                            <textarea class="form-control" id="explanation-editor" rows="8" placeholder="Enter the explanation for the answer (optional)"><?= isset($question) && isset($question['explanation']) ? htmlspecialchars($question['explanation']) : '' ?></textarea>
                                            <div class="preview-content" style="display: none;"></div>
                                        </div>
                                        <!-- Hidden input to store markdown content for form submission -->
                                        <input type="hidden" id="explanation" name="explanation" value="<?= isset($question) && isset($question['explanation']) ? htmlspecialchars($question['explanation']) : '' ?>">
                                    </div>
                                </div>
                                
                                <!-- Submit buttons -->
                                <div class="mt-4 d-flex justify-content-between">
                                    <a href="<?= base_url($url.'/question') ?>" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back to Questions
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> <?= isset($question) ? 'Update Question' : 'Save Question' ?>
                                    </button>
                                </div>
                            </form>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Upload Modal -->
    <div class="modal fade" id="imageUploadModal" tabindex="-1" aria-labelledby="imageUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageUploadModalLabel">Insert Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="imageTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="url-tab" data-bs-toggle="tab" data-bs-target="#url-pane" type="button" role="tab" aria-controls="url-pane" aria-selected="true">Image URL</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload-pane" type="button" role="tab" aria-controls="upload-pane" aria-selected="false">Upload Image</button>
                        </li>
                    </ul>
                    <div class="tab-content mt-3" id="imageTabContent">
                        <div class="tab-pane fade show active" id="url-pane" role="tabpanel" aria-labelledby="url-tab">
                            <div class="mb-3">
                                <label for="imageUrl" class="form-label">Image URL</label>
                                <input type="text" class="form-control" id="imageUrl" placeholder="https://example.com/image.jpg">
                            </div>
                            <div class="mb-3">
                                <label for="imageAltText" class="form-label">Alt Text (for accessibility)</label>
                                <input type="text" class="form-control" id="imageAltText" placeholder="Description of the image">
                            </div>
                        </div>
                        <div class="tab-pane fade" id="upload-pane" role="tabpanel" aria-labelledby="upload-tab">
                            <form id="imageUploadForm" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="imageFile" class="form-label">Select Image</label>
                                    <input class="form-control" type="file" id="imageFile" accept="image/*">
                                </div>
                                <div class="mb-3">
                                    <label for="uploadAltText" class="form-label">Alt Text (for accessibility)</label>
                                    <input type="text" class="form-control" id="uploadAltText" placeholder="Description of the image">
                                </div>
                                <div class="image-preview-container" style="display: none;">
                                    <p>Preview:</p>
                                    <img id="imagePreview" src="#" alt="Preview" style="max-width: 100%; max-height: 200px;">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="insertImageBtn">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true" id="uploadSpinner"></span>
                        <span id="insertImageText">Insert Image</span>
                    </button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="moreInfoModal" tabindex="-1" aria-labelledby="moreInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="moreInfoModalLabel">Test Case Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Content will be dynamically populated -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</main>

<!-- CSS Styles -->
<style>
/* Base styling */
body {
    background-color: #f8f9fa;
}

/* Select2 container sizing and alignment */
.tags-container {
    position: relative;
}

.select2-container {
    width: 100% !important;
}

/* Selection box styling */
.select2-container--default .select2-selection--multiple {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    min-height: 38px;
    padding: 0.25rem 0.5rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    background-color: #fff;
}

/* Focus state */
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Tag/chip styling */
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background-color:rgb(179, 0, 178);
    border: none;
    border-radius: 50px;
    color: white;
    padding: 0.25rem 0.75rem;
    margin: 0.125rem 0.25rem;
    font-size: 0.875rem;
    display: flex;
    align-items: center;
}

/* Remove button (x) styling */
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: white;
    margin-right: 0.5rem;
    border: none;
    background: transparent;
    opacity: 0.8;
    font-weight: 700;
    transition: all 0.2s;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    background: rgb(145, 145, 145);
    color: red;
    opacity: 1;
    border-radius: 50%;
}

/* Search box alignment */
.select2-container--default .select2-selection--multiple .select2-search--inline {
    align-self: stretch;
    margin: 0.125rem 0;
}

.select2-container--default .select2-selection--multiple .select2-search--inline .select2-search__field {
    margin-top: 0;
    height: 26px;
    font-size: 0.95rem;
}

/* Dropdown styling */
.select2-dropdown {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.select2-results__option {
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
    color:black;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background-color: #4a6cf7;
    color:black;
}

/* New tag styling */
.new-tag-badge {
    background-color: #28a745;
    color: black;
    border-radius: 4px;
    padding: 0.15rem 0.5rem;
    font-size: 0.7rem;
    margin-left: 0.5rem;
}

/* Placeholder styling */
.select2-container--default .select2-selection--multiple .select2-selection__placeholder {
    color: #6c757d;
}

.card {
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: none;
}

/* Markdown editor styles */
.markdown-editor-wrapper {
    border: 1px solid #ddd;
    border-radius: 8px;
    overflow: hidden;
    background-color: #fff;
}

.markdown-tabs {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #f8f9fa;
    border-bottom: 1px solid #ddd;
    padding: 0;
}

.tab-buttons {
    display: flex;
}

.tab-btn {
    padding: 10px 20px;
    border: none;
    background: none;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    border-right: 1px solid #ddd;
}

.tab-btn.active {
    background-color:rgb(103, 78, 231);
    color: white;
}

.formatting-toolbar {
    display: flex;
    padding: 4px 8px;
}

.tool-btn {
    background: none;
    border: none;
    padding: 6px 10px;
    margin: 0 2px;
    cursor: pointer;
    color: #555;
}

.tool-btn:hover {
    background-color: #e9ecef;
    border-radius: 3px;
}

.editor-content-area {
    position: relative;
}

#markdown-editor {
    border: none;
    border-radius: 0;
    resize: vertical;
    min-height: 200px;
    padding: 15px;
    width: 100%;
    outline: none;
}

.preview-content {
    min-height: 200px;
    padding: 15px;
    background-color: #f8f9fa;
    border: none;
    overflow: auto;
    width: 100%;
}

/* Question type cards */
.question-type-card, .language-type-card {
    cursor: pointer;
    height: 100%;
    transition: all 0.3s ease;
    border: 1px solid #dee2e6;
}

.question-type-card:hover, .language-type-card:hover {
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.question-type-card.selected, .language-type-card.selected {
    border: 2px solidrgb(247, 74, 126);
    background-color: #f0f4ff;
}

.question-type-card .card-title, .language-type-card .card-title {
    font-size: 1.1rem;
    font-weight: 600;
}

.question-type-card .card-text, .language-type-card .card-text {
    font-size: 0.85rem;
    color: #6c757d;
}

/* Hide radio buttons but keep them accessible */
.visually-hidden {
    position: absolute;
    width: 1px;
    height: 1px;
    margin: -1px;
    padding: 0;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
}

/* Options styling */
.option-row {
    position: relative;
}

.input-group-text {
    min-width: 100px;
}

.remove-option, .remove-test-case {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

/* Test cases styling */
.test-case-row {
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #fff;
}

.info-box {
    background-color: #e9ecef;
    border-left: 4px solid #4a6cf7;
}

/* Dropdown styling */
.difficulty-dropdown .dropdown-toggle,
.tags-dropdown .dropdown-toggle {
    text-align: left;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.difficulty-dropdown .dropdown-toggle::after,
.tags-dropdown .dropdown-toggle::after {
    margin-left: auto;
}

.dropdown-menu {
    max-height: 250px;
    overflow-y: auto;
}

/* Hide/show sections based on question type */
.subtype-section, .language-section {
    display: none;
}

/* Image upload styling */
.image-preview-container {
    margin-top: 15px;
    padding: 10px;
    border: 1px dashed #ddd;
    border-radius: 4px;
    text-align: center;
}

#imagePreview {
    display: block;
    margin: 0 auto;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .question-type-cards .col-md-3 {
        margin-bottom: 15px;
    }
}

/* Ensure language sections are visible when selected */
#language-selection-container .language-section {
    display: none;
}

#language-selection-container .language-section.visible {
    display: block;
}

/* Make info text more visible */
.info-box {
    background-color: #f0f4ff;
    border-left: 4px solid #4a6cf7;
    padding: 15px;
    margin-bottom: 20px;
}

.info-text {
    font-weight: 500;
}

/* Add animation to draw attention to sections when they appear */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.language-section, .subtype-section {
    animation: fadeIn 0.3s ease-in-out;
}
</style>

<!-- Required scripts -->
<script src="<?= base_url("/") ?>assets/js/font-awesome-all-min.js"></script>
<script src="<?= base_url("/") ?>assets/packages/marked.min.js"></script>
<script src="<?= base_url("/") ?>assets/packages/highlight.min.js"></script>
<script src="<?= base_url("/") ?>assets/packages/bootstrap.bundle.min.js"></script>
<script src="<?= base_url("/") ?>assets/packages/select2.min.js"></script>


<script>
$(document).ready(function() {

    $('.select2-tags').select2({
        placeholder: 'Select or add Topics',
        tags: true,
        tokenSeparators: [',', ' '],
        closeOnSelect: false,
        templateResult: formatTag,
        templateSelection: formatTagSelection
    }).on("select2:open", function() {
        // Set focus to the search field when dropdown opens
        setTimeout(function() {
            $(".select2-search__field").focus();
        }, 0);
    });
    
    // Update hidden input when selection changes
    $('.select2-tags').on('change', function() {
        const selectedTags = $(this).val() || [];
        $('#tags').val(selectedTags.join(','));
    });
    
    // Update selected tags on initialization
    const initialTags = $('#tags').val();
    if (initialTags) {
        const tagArray = initialTags.split(',');
        
        // Add any tags that might not be in the existing options
        tagArray.forEach(tag => {
            if (tag.trim() !== '') {
                // Check if this tag already exists as an option
                if ($('#tags-select option[value="' + tag + '"]').length === 0) {
                    $('#tags-select').append(new Option(tag, tag, true, true));
                }
            }
        });
        
        // Update the select2 instance
        $('#tags-select').val(tagArray).trigger('change');
    }
    
    // Format the tag in dropdown
    function formatTag(tag) {
        if (tag.loading) {
            return tag.text;
        }
        
        if (tag.id === tag.text && !tag.selected) { // This is a new tag
            return $(`
                <div class="d-flex align-items-center justify-content-between">
                    <span>${tag.text}</span>
                    <span class="new-tag-badge">New</span>
                </div>
            `);
        }
        
        return tag.text;
    }
    
    // Format the selected tag
    function formatTagSelection(tag) {
        return tag.text;
    }
    // Configure marked for markdown processing
    marked.setOptions({
        highlight: function(code, lang) {
            if (lang && hljs.getLanguage(lang)) {
                return hljs.highlight(code, { language: lang }).value;
            }
            return hljs.highlightAuto(code).value;
        },
        breaks: true,
        gfm: true
    });


    function updateRequiredAttributes() {
        const selectedType = $('input[name="type"]:checked').val();
        
        // Only make options required for MCQ questions
        if (selectedType == '1') { // MCQ
            $('.option-text').attr('required', 'required');
        } else {
            $('.option-text').removeAttr('required');
        }
    }

    // Initialize UI based on selected question type
    function initializeUI() {
        const selectedType = $('input[name="type"]:checked').val();
        
        console.log("Initializing UI for type:", selectedType);
        
        // Hide all dynamic containers first
        $('#mcq-subtype, #code-subtype, #options-container, #answers-container, #test-cases-container, #language-selection-container, #fill-blank-container').hide();
        $('.language-section').hide();
        $('.info-text').hide();
        
        // Show relevant sections based on question type
        if (selectedType == '1') { // MCQ
            $('#mcq-subtype').show();
            $('#options-container, #answers-container').show();
            
            // Set select type behavior
            updateSelectTypeBehavior();
        } 
        else if (selectedType == '2') { // CODE
            console.log("Showing CODE sections");
            $('#code-subtype').show();
            $('#test-cases-container').show();
            $('#language-selection-container').show(); // Show the container first
            
            // Show language selection based on selected subtype
            const selectedSubType = $('input[name="sub_type"]:checked').val();
            console.log("Selected subtype:", selectedSubType);
            showLanguageSelection(selectedSubType);
        }
        else if (selectedType == '3') { // FILL IN THE BLANK
            $('#fill-blank-container').show();
        }
        
        // Initialize card selection
        $('.question-type-card').removeClass('selected');
        $(`.question-type-card[data-type="${selectedType}"]`).addClass('selected');
        
        // Initialize language type card selection
        if (selectedType == '2') {
            const selectedLangType = $('input[name="sub_type"]:checked').val();
            console.log("Selecting language type card:", selectedLangType);
            $('.language-type-card').removeClass('selected');
            $(`.language-type-card[data-subtype="${selectedLangType}"]`).addClass('selected');
        }
        updateRequiredAttributes();
    }

    $('input[name="type"]').on('change', function() {
        console.log("Question type changed to:", $(this).val());
        initializeUI();
    });

    updateRequiredAttributes();
    

    // Show language selection based on selected code subtype
    function showLanguageSelection(subTypeId) {
        console.log("Showing language selection for subtype:", subTypeId);
        
        // Default if no subtype is selected
        if (!subTypeId && $('input[name="type"]:checked').val() == '2') {
            // Select first subtype by default
            const firstSubtypeId = $('.language-type-card').first().data('subtype');
            $(`input[name="sub_type"][value="${firstSubtypeId}"]`).prop('checked', true);
            subTypeId = firstSubtypeId.toString();
            console.log("No subtype selected, defaulting to:", subTypeId);
        }
        
        // Hide all language sections first
        $('.language-section').hide();
        $('.info-text').hide();
        
        // Show the appropriate language section based on the subtype
        if (subTypeId == '3') { // PROGRAMMING LANGUAGE
            console.log("Showing programming languages section");
            $('#programming-languages-section').show();
            $('.programming-info').show();
        } 
        else if (subTypeId == '4') { // WEB LANGUAGE
            console.log("Showing web languages section");
            $('#web-languages-section').show();
            $('.web-info').show();
        } 
        else if (subTypeId == '5') { // DATABASE
            console.log("Showing databases section");
            $('#databases-section').show();
            $('.database-info').show();
        }
    }

    // Update select type behavior (single vs multi)
    function updateSelectTypeBehavior() {
        const selectType = $('input[name="sub_type"]:checked').val();
        
        if (selectType == '1') { // Single select
            $('.answer-checkbox').on('change', function() {
                if ($(this).is(':checked')) {
                    $('.answer-checkbox').not(this).prop('checked', false);
                }
            });
            
            // Make sure only one is checked when initializing
            if ($('.answer-checkbox:checked').length > 1) {
                $('.answer-checkbox:checked').not(':first').prop('checked', false);
            }
        } else {
            $('.answer-checkbox').off('change');
        }
    }

    // Update options numbering
    function updateOptionsNumbering() {
        $('.option-row').each(function(index) {
            $(this).find('.input-group-text').text(`${index + 1}. Answer`);
        });
    }

    // Update answers checkboxes
    function updateAnswersCheckboxes() {
        $('#answers-list').empty();
        
        $('.option-row').each(function(index) {
            $('#answers-list').append(`
                <div class="form-check mb-2">
                    <input type="checkbox" class="form-check-input answer-checkbox" 
                        id="answer_${index}" name="is_correct[]" value="${index}">
                    <label class="form-check-label" for="answer_${index}">
                        ${index + 1}
                    </label>
                </div>
            `);
        });
        
        updateSelectTypeBehavior();
    }

    // Update tags hidden input and dropdown text
    function updateTags() {
        const selectedTags = [];
        $('.tag-checkbox:checked').each(function() {
            selectedTags.push($(this).val());
        });
        
        $('#tags').val(selectedTags.join(','));
        
        if (selectedTags.length > 0) {
            $('#tagsDropdown').text(selectedTags.join(', '));
        } else {
            $('#tagsDropdown').text('Select Tags');
        }
    }

    // Function to insert markdown code into the editor
    function insertIntoEditor(code) {
        const editorWrapper = $('#imageUploadModal').data('editorWrapper');
        const textarea = editorWrapper.find('textarea')[0];
        const hiddenInput = editorWrapper.find('input[type="hidden"]')[0];
        
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        
        // Insert the code
        textarea.value = text.substring(0, start) + code + text.substring(end);
        
        // Update the cursor position
        const newPosition = start + code.length;
        textarea.selectionStart = newPosition;
        textarea.selectionEnd = newPosition;
        
        // Update hidden input with markdown content
        if (hiddenInput) {
            hiddenInput.value = textarea.value;
        }
        
        // Focus back on the textarea
        textarea.focus();
    }
    
    // Function to upload image to the server
    function uploadImage(file, altText) {
        // Show loading state
        $('#uploadSpinner').removeClass('d-none');
        $('#insertImageText').text('Uploading...');
        $('#insertImageBtn').prop('disabled', true);

        const formData = new FormData();
        formData.append('image', file);
        
        $.ajax({
            url: '<?= base_url($url."/question/uploadQuestionImage") ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    // Parse response if it's a string
                    const parsedResponse = typeof response === 'string' ? JSON.parse(response) : response;
                    
                    if (parsedResponse.success) {
                        // Insert the image markdown with the uploaded file URL
                        const imageCode = `![${altText}](${parsedResponse.file_url})`;
                        insertIntoEditor(imageCode);

                        // Reset form fields
                        $('#imageFile').val('');
                        $('#uploadAltText').val('');
                        $('#imageUrl').val('');
                        $('#imageAltText').val('');
                        $('.image-preview-container').hide();
                        $('#imagePreview').attr('src', '#');

                        // Hide the modal after inserting
                        try {
                            // Try jQuery method first
                            $('#imageUploadModal').modal('hide');
                            
                            // If that doesn't work, try Bootstrap's native method
                            const modalElement = document.getElementById('imageUploadModal');
                            if (modalElement) {
                                const modal = new bootstrap.Modal(modalElement);
                                modal.hide();
                            }
                            
                            // As a last resort, manually hide the modal
                            $('#imageUploadModal').removeClass('show');
                            $('.modal-backdrop').remove();
                            $('body').removeClass('modal-open');
                            $('body').css('overflow', '');
                            $('body').css('padding-right', '');
                        } catch (error) {
                            console.error('Error hiding modal:', error);
                        }
                    } else {
                        alert(parsedResponse.message || 'Error uploading image');
                    }
                } catch (e) {
                    console.error("Error processing upload response:", e);
                    alert('Error processing upload response');
                }
            },
            error: function(xhr) {
                console.error("Upload error:", xhr.responseText);
                alert('Error uploading image. Please try again.');
            },
            complete: function() {
                // Reset loading state
                $('#uploadSpinner').addClass('d-none');
                $('#insertImageText').text('Insert Image');
                $('#insertImageBtn').prop('disabled', false);
            }
        });
    }

    // Event Handlers
    
    // Switch between write and preview modes
    $('.tab-btn').on('click', function() {
        const mode = $(this).data('mode');
        const editorWrapper = $(this).closest('.markdown-editor-wrapper');
        
        // Update active button
        editorWrapper.find('.tab-btn').removeClass('active');
        $(this).addClass('active');
        
        const textarea = editorWrapper.find('textarea');
        const previewContent = editorWrapper.find('.preview-content');
        
        if (mode === 'write') {
            textarea.show();
            previewContent.hide();
        } else {
            // Render markdown content
            const markdownContent = textarea.val();
            previewContent.html(marked.parse(markdownContent));
            previewContent.show();
            textarea.hide();
        }
    });

    // Handle question type selection
    $('.question-type-card').on('click', function() {
        const type = $(this).data('type');
        $('input[name="type"][value="' + type + '"]').prop('checked', true);
        
        $('.question-type-card').removeClass('selected');
        $(this).addClass('selected');
        
        initializeUI();
    });
    
    // Handle language type card selection
    $('.language-type-card').on('click', function() {
        const subtype = $(this).data('subtype');
        $('input[name="sub_type"][value="' + subtype + '"]').prop('checked', true);
        
        $('.language-type-card').removeClass('selected');
        $(this).addClass('selected');
        
        showLanguageSelection(subtype);
    });

    // Handle markdown commands
    $('.tool-btn').on('click', function() {
        const command = $(this).data('command');
        const editorWrapper = $(this).closest('.markdown-editor-wrapper');
        const textarea = editorWrapper.find('textarea')[0];
        const hiddenInput = editorWrapper.find('input[type="hidden"]')[0];
        
        // If it's the image button, show modal instead of direct insertion
        if (command === 'image') {
            // Store the current editor wrapper in the modal's data
            $('#imageUploadModal').data('editorWrapper', editorWrapper);
            $('#imageUploadModal').modal('show');
            return;
        }
        
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        let selectedText = text.substring(start, end);
        let replacement = '';
        
        switch(command) {
            case 'heading':
                replacement = `## ${selectedText || 'Heading'}`;
                break;
            case 'bold':
                replacement = `**${selectedText || 'bold text'}**`;
                break;
            case 'italic':
                replacement = `*${selectedText || 'italic text'}*`;
                break;
            case 'table':
                replacement = `| Header | Header |\n| ------ | ------ |\n| Cell   | Cell   |\n| Cell   | Cell   |`;
                break;
            case 'list-ul':
                replacement = selectedText ? selectedText.split('\n').map(line => `- ${line}`).join('\n') : '- List item\n- List item\n- List item';
                break;
            case 'list-ol':
                replacement = selectedText ? selectedText.split('\n').map((line, i) => `${i+1}. ${line}`).join('\n') : '1. List item\n2. List item\n3. List item';
                break;
            case 'quote':
                replacement = selectedText ? selectedText.split('\n').map(line => `> ${line}`).join('\n') : '> Blockquote';
                break;
            case 'strikethrough':
                replacement = `~~${selectedText || 'strikethrough text'}~~`;
                break;
            case 'code':
                replacement = selectedText ? '```\n' + selectedText + '\n```' : '```\ncode block\n```';
                break;
            case 'link':
                replacement = `[${selectedText || 'link text'}](https://example.com)`;
                break;
        }
        
        // Insert the replacement text
        textarea.value = text.substring(0, start) + replacement + text.substring(end);
        
        // Update the cursor position
        const newPosition = start + replacement.length;
        textarea.selectionStart = newPosition;
        textarea.selectionEnd = newPosition;
        
        // Update hidden input with markdown content
        if (hiddenInput) {
            hiddenInput.value = textarea.value;
        }
        
        // Focus back on the textarea
        textarea.focus();
    });
    
    // Fix the "More Information" link
    $(document).on('click', '.info-box a', function(e) {
        e.preventDefault();
        console.log("More Information link clicked");
        
        // Determine which type of info to show based on visible section
        let infoTitle = "Test Case Information";
        let infoContent = "";
        
        if ($('.programming-info').is(':visible')) {
            infoTitle = "Programming Language Test Cases";
            infoContent = `
                <p>Programming language test cases are used to verify the correctness of code submissions.</p>
                <ul>
                    <li><strong>Input:</strong> The data provided to the program.</li>
                    <li><strong>Output:</strong> The expected result that should be returned.</li>
                    <li><strong>Visibility:</strong> When checked, students can see this test case.</li>
                </ul>
                <p>For programming languages, the test cases will be executed against the student's code. The student's code should return the expected output when given the input.</p>
                <p>Example: If the input is "John" and the expected output is "Hello, John!", the student's code should include logic to format and return this greeting.</p>
            `;
        } else if ($('.web-info').is(':visible')) {
            infoTitle = "Web Language Test Cases";
            infoContent = `
                <p>For web languages (HTML, CSS, JavaScript, etc.), test cases validate the functionality and structure of web components.</p>
                <ul>
                    <li><strong>Input:</strong> The test condition or scenario.</li>
                    <li><strong>Output:</strong> The expected result or behavior.</li>
                </ul>
                <p>JavaScript test cases often check for specific DOM manipulations, event handling, or data transformations.</p>
                <p>Example: A test might check if clicking a button changes the text of a paragraph element.</p>
            `;
        } else if ($('.database-info').is(':visible')) {
            infoTitle = "Database Test Cases";
            infoContent = `
                <p>Database test cases verify SQL queries and database operations.</p>
                <ul>
                    <li><strong>Input:</strong> The SQL query or database command to execute.</li>
                    <li><strong>Output:</strong> The expected result set or affected rows.</li>
                </ul>
                <p>The student's query will be executed against a test database, and the results will be compared with the expected output.</p>
                <p>Example: If the input is "SELECT COUNT(*) FROM users WHERE name = 'John'", the expected output might be "2".</p>
            `;
        }
        
        // Update modal content
        $('#moreInfoModal .modal-title').text(infoTitle);
        $('#moreInfoModal .modal-body').html(infoContent);
        
        // Show the modal
        console.log("Opening modal");
        const modal = new bootstrap.Modal(document.getElementById('moreInfoModal'));
        modal.show();
    });

    // Other event handlers
    
    // Listen for changes to question type radio buttons
    $('input[name="type"]').on('change', function() {
        console.log("Question type changed to:", $(this).val());
        initializeUI();
    });
    
    // Listen for changes to subtype radio buttons
    $('input[name="sub_type"]').on('change', function() {
        const subTypeId = $(this).val();
        console.log("Subtype changed to:", subTypeId);
        
        if ($('input[name="type"]:checked').val() == '1') { // MCQ
            updateSelectTypeBehavior();
        } else if ($('input[name="type"]:checked').val() == '2') { // CODE
            showLanguageSelection(subTypeId);
        }
    });

    // Update hidden input with markdown content on change
    $('#markdown-editor, #explanation-editor').on('input', function() {
        const editorWrapper = $(this).closest('.markdown-editor-wrapper');
        const hiddenInput = editorWrapper.find('input[type="hidden"]');
        if (hiddenInput.length) {
            hiddenInput.val($(this).val());
        }
    });

    // Handle difficulty dropdown
    $('.difficulty-item').on('click', function(e) {
        e.preventDefault();
        const value = $(this).data('value');
        $('#difficultyDropdown').text(value);
        $('#difficulty_level').val(value);
    });

    // Handle tag checkboxes
    $('.tag-checkbox').on('change', function() {
        updateTags();
    });
    
    // Add new tag
    $('#add-new-tag').on('click', function() {
        const newTag = $('#new-tag').val().trim();
        if (newTag) {
            // Create new checkbox
            const tagId = 'tag_' + newTag.toLowerCase().replace(/[^a-z0-9]/g, '_');
            
            // Only add if tag doesn't already exist
            if ($('#' + tagId).length === 0) {
                const newCheckbox = `
                    <div class="form-check">
                        <input class="form-check-input tag-checkbox" type="checkbox" value="${newTag}" id="${tagId}" checked>
                        <label class="form-check-label" for="${tagId}">
                            ${newTag}
                        </label>
                    </div>
                `;
                
                $(newCheckbox).insertBefore('.dropdown-menu .mt-2');
                updateTags();
            } else {
                // If tag already exists, just check it
                $('#' + tagId).prop('checked', true).trigger('change');
            }
            
            $('#new-tag').val('');
        }
    });
    
    // Handle "select all" checkboxes
    $('#select_all_programming').on('change', function() {
        $('.programming-language').prop('checked', $(this).is(':checked')).trigger('change');
    });
    
    $('#select_all_web').on('change', function() {
        $('.web-language').prop('checked', $(this).is(':checked')).trigger('change');
    });
    
    $('#select_all_databases').on('change', function() {
        $('.database').prop('checked', $(this).is(':checked')).trigger('change');
    });
    
    // Update selected languages
    $('.programming-language, .web-language, .database').on('change', function() {
        let selectedLanguages = [];
        $('.programming-language:checked').each(function() {
            selectedLanguages.push($(this).val());
        });

        $('.web-language:checked').each(function() {
            selectedLanguages.push($(this).val());
        });

        $('.database:checked').each(function() {
            selectedLanguages.push($(this).val());
        });
        
        $('#selected_languages').val(selectedLanguages.join(','));
    });

    // Add new option
    $('#add-option').on('click', function() {
        const optionCount = $('.option-row').length + 1;
        
        const newOption = `
            <div class="option-row mb-3">
                <div class="input-group">
                    <span class="input-group-text">${optionCount}. Answer</span>
                    <input type="text" class="form-control option-text" name="option_text[]" placeholder="Answer" required>
                    <button type="button" class="btn btn-danger remove-option"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `;
        
        $('#options-list').append(newOption);
        updateAnswersCheckboxes();
    });

    // Remove option
    $(document).on('click', '.remove-option', function() {
        if ($('.option-row').length > 1) {
            $(this).closest('.option-row').remove();
            updateOptionsNumbering();
            updateAnswersCheckboxes();
        } else {
            alert("You must have at least one option.");
        }
    });
    
    // Add test case
    $('#add-test-case').on('click', function() {
        const newTestCase = `
            <div class="test-case-row mb-3">
                <div class="row">
                    <div class="col-md-5">
                        <label class="form-label">Input</label>
                        <textarea class="form-control test-input" rows="3"></textarea>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Output</label>
                        <textarea class="form-control test-output" rows="3"></textarea>
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <button type="button" class="btn btn-danger remove-test-case"><i class="fas fa-trash"></i></button>
                        <button type="button" class="btn btn-secondary ms-2 toggle-test-case"><i class="fas fa-eye"></i></button>
                    </div>
                </div>
                <div class="form-check mt-2">
                    <input class="form-check-input test-visibility" type="checkbox" value="1" checked>
                    <label class="form-check-label">
                        Visible to students
                    </label>
                </div>
            </div>
        `;
        
        $('#test-cases-list').append(newTestCase);
    });
    
    // Remove test case
    $(document).on('click', '.remove-test-case', function() {
        $(this).closest('.test-case-row').remove();
    });
    
    // Toggle test case visibility
    $(document).on('click', '.toggle-test-case', function() {
        const checkbox = $(this).closest('.test-case-row').find('.test-visibility');
        checkbox.prop('checked', !checkbox.is(':checked'));
    });
    
    // Image handling
    // Handle file selection and show preview
    $('#imageFile').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                $('#imagePreview').attr('src', e.target.result);
                $('.image-preview-container').show();
            };
            
            reader.readAsDataURL(file);
        } else {
            $('.image-preview-container').hide();
        }
    });
    
    // Handle insert image button click
    $('#insertImageBtn').on('click', function() {
        const activeTab = $('#imageTabs .nav-link.active').attr('id');
        let imageCode = '';
        
        if (activeTab === 'url-tab') {
            const url = $('#imageUrl').val().trim();
            const alt = $('#imageAltText').val().trim() || 'Image';
            
            if (url) {
                imageCode = `![${alt}](${url})`;
                insertIntoEditor(imageCode);
                try {
                    // Try jQuery method first
                    $('#imageUploadModal').modal('hide');
                    
                    // If that doesn't work, try Bootstrap's native method
                    const modalElement = document.getElementById('imageUploadModal');
                    if (modalElement) {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.hide();
                    }
                    
                    // As a last resort, manually hide the modal
                    $('#imageUploadModal').removeClass('show');
                    $('.modal-backdrop').remove();
                    $('body').removeClass('modal-open');
                    $('body').css('overflow', '');
                    $('body').css('padding-right', '');
                } catch (error) {
                    console.error('Error hiding modal:', error);
                }
            } else {
                alert('Please enter an image URL');
            }
        } else {
            const file = $('#imageFile')[0].files[0];
            const alt = $('#uploadAltText').val().trim() || 'Image';
            
            if (file) {
                // Upload the file to the server
                uploadImage(file, alt);
            } else {
                alert('Please select an image file');
            }
        }
    });

    // Form submission
    $('#questionForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        let isValid = true;
        
        // Check if question content is provided
        if ($('#question').val().trim() === '') {
            alert('Please provide question content.');
            isValid = false;
        }
        
        // Check if at least one answer is selected for MCQ questions
        const questionType = $('input[name="type"]:checked').val();
        if (questionType == '1' && $('.answer-checkbox:checked').length === 0) {
            alert('Please select at least one correct answer.');
            isValid = false;
        }
        
        if (!isValid) {
            return false;
        }

        if (questionType == '1') {
            // Check if options are provided
            let allOptionsValid = true;
            $('.option-text').each(function() {
                if ($(this).val().trim() === '') {
                    allOptionsValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            
            if (!allOptionsValid) {
                alert('Please fill in all option texts.');
                isValid = false;
            }
            
            // Check if at least one answer is selected
            if ($('.answer-checkbox:checked').length === 0) {
                alert('Please select at least one correct answer.');
                isValid = false;
            }
        }

        if (questionType == '2' && $('.test-case-row').length > 0) {
            let testCasesValid = true;
            $('.test-case-row').each(function() {
                const input = $(this).find('.test-input').val().trim();
                const output = $(this).find('.test-output').val().trim();
                
                if (input === '' || output === '') {
                    testCasesValid = false;
                    if (input === '') $(this).find('.test-input').addClass('is-invalid');
                    if (output === '') $(this).find('.test-output').addClass('is-invalid');
                } else {
                    $(this).find('.test-input, .test-output').removeClass('is-invalid');
                }
            });
            
            if (!testCasesValid) {
                alert('Please fill in both input and output for all test cases.');
                isValid = false;
            }
        }

        if (questionType == '3' && $('#fill-blank-answer').val().trim() === '') {
            $('#fill-blank-answer').addClass('is-invalid');
            alert('Please provide an answer for the fill in the blank question.');
            isValid = false;
        }
        
        if (!isValid) {
            return false;
        }
        
        // Collect test cases data if it's a CODE question
        const testCases = [];
        if (questionType == '2') {
            $('.test-case-row').each(function() {
                const input = $(this).find('.test-input').val();
                const output = $(this).find('.test-output').val();
                const visibility = $(this).find('.test-visibility').is(':checked') ? 1 : 0;
                
                testCases.push({
                    input: input,
                    output: output,
                    visibility: visibility
                });
            });
        }
        
        // Prepare form data
        const formData = {
            question: {
                question_title: $('#problem_title').val(),
                question_content: $('#question').val(),
                type: $('input[name="type"]:checked').val(),
                sub_type: $('input[name="sub_type"]:checked').val(),
                difficulty_level: $('#difficulty_level').val(),
                score: $('#score').val(),
                tags: $('#tags').val(),
                selected_languages: $('#selected_languages').val(),
                ignore_case: $('#ignore_case').is(':checked') ? 1 : 0,
                fill_blank_answer: $('#fill-blank-answer').val(),
                explanation : $('#explanation').val()
            },
            options: [],
            test_cases: testCases
        };
        
        // Only include options for MCQ questions
        if (questionType == '1') {
            $('.option-row').each(function(index) {
                const optionText = $(this).find('.option-text').val();
                const isCorrect = $('.answer-checkbox[value="' + index + '"]').is(':checked');
                
                formData.options.push({
                    option_text: optionText,
                    is_correct: isCorrect ? 1 : 0
                });
            });
        }
        
        // Submit via AJAX
        $.ajax({
            url: $(this).attr('action'),
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify(formData),
            dataType: "json",
            success: function(response) {
                if (response.status === 'success') {
                    window.location.href = "<?= base_url($url.'/question') ?>";
                } else {
                    alert(response.message || "Error saving question!");
                }
            },
            error: function(xhr) {
                console.error("Error response:", xhr.responseText);
                let errorMessage = "An error occurred while saving the question.";
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {}
                alert(errorMessage);
            }
        });
    });
    
    // Check if we need to select a default subtype for CODE questions
    if ($('input[name="type"]:checked').val() == '2' && !$('input[name="sub_type"]:checked').length) {
        // Select the first available subtype by default
        const firstSubtype = $('.language-type-card').first().data('subtype');
        if (firstSubtype) {
            $(`input[name="sub_type"][value="${firstSubtype}"]`).prop('checked', true);
        }
    }
    
    // Initialize the interface on page load
    console.log("Initializing interface on page load");
    initializeUI();
    
    // Initialize dropdowns
    const dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    dropdownElementList.map(function(dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });
});
</script>