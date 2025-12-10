
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <!-- Navbar -->
    <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
        <div class="container-fluid py-1 px-3 justify-content-between">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="<?= base_url($url.'/test') ?>">Tests</a></li>
                    <li class="breadcrumb-item text-sm text-dark active" aria-current="page">Test Questions</li>
                </ol>
                <h6 class="font-weight-bolder mb-0">Manage Test Questions</h6>
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
        <div class="row mb-4" id="testInfoRow">
            <div class="col-12">
                <div class="card">
                    <?php if ($this->session->flashdata('message')) { ?>
                        <div class="alert alert-<?= $this->session->flashdata('message')[0] ?> alert-dismissible" id="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">×</button>
                            <span class="alert-text" style="color:black">
                                <?= $this->session->flashdata('message')[1] ?>
                            </span>
                        </div>
                    <?php } ?>
                    
                    <div class="card-header d-flex justify-content-between align-items-center pb-0">
                        <div>
                            <h5 class="mb-0">Test: <?= $test->title ?></h5>
                            <p class="text-sm mb-0">Duration: <?= floor($test->duration / 60) ?>:<?= str_pad($test->duration % 60, 2, '0', STR_PAD_LEFT) ?></p>
                        </div>
                        <div class="d-flex">
                            <a href="<?= base_url($url.'/test') ?>" class="btn btn-outline-secondary btn-sm me-2">
                                Back to Tests
                            </a>
                            <?php if ($sections_enabled): ?>
                                <button type="button" class="btn btn-outline-success btn-sm me-2" id="manageSectionsBtn" data-test-id="<?= $test->id ?>">
                                    <i class="fas fa-layer-group"></i> Manage Sections
                                </button>
                            <?php endif; ?>
                            <a href="<?= base_url($url.'/test/edit/'.$test->id) ?>" class="btn btn-outline-primary btn-sm">
                                Edit Test
                            </a>
                        </div>
                    </div>
                </div>
        </div>
        </div>

        <?php if ($sections_enabled): ?>
        <!-- Sectional tests: Save Order warning banner (shown only when unsaved questions exist) -->
        <div class="row mb-2 d-none" id="saveRequiredWarningRow">
            <div class="col-12">
                <div class="save-order-banner" id="saveRequiredWarning" role="alert">
                    <div class="save-order-banner-text">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span>
                            <strong>Action Required:</strong>
                            You have <span id="warningUnsavedCount">0</span> unsaved question(s). Please click "Save Order" to save them.
                        </span>
                    </div>
                    <div class="save-order-banner-actions">
                        <button type="button" class="btn btn-sm btn-warning save-order-pulse" id="saveOrderBanner">
                            <i class="fas fa-save me-1"></i> Save Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Question Management Section -->
        <?php
        // Find default section ID at the start
        $default_section_id = DEFAULT_SECTION_ID; // Default section always has id = 1
        ?>
        <div class="row">
            <!-- Selected Questions Column -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0 me-3">Selected Questions</h6>
                            </div>
                            <div class="d-flex">
                                <button type="button" class="btn btn-sm btn-danger me-2" id="removeAllQuestions">
                                    Remove All
                                </button>
                                <!-- Primary Save Order button (default location for all tests) -->
                                <button type="button" class="btn btn-sm save-order-header-btn save-order-btn" id="saveOrderHeader">
                                    <i class="fas fa-save me-1"></i> Save Order
                                </button>
                            </div>
                        </div>
                        <?php if ($sections_enabled && !empty($sections)): ?>
                            <div class="d-flex align-items-center mt-2">
                                <label class="form-label mb-0 me-2" style="min-width: 100px;"><i class="fas fa-filter text-primary"></i> Filter by Section:</label>
                                <select class="form-select form-select-sm section-filter-dropdown" id="sectionFilter" style="max-width: 250px;">
                                    <option value="">All Sections</option>
                                    <?php foreach ($sections as $section): ?>
                                        <?php if ($section['id'] != $default_section_id): ?>
                                        <?php 
                                        $section_display_name = ($section['id'] == $default_section_id) ? 'Unassigned Questions' : $section['section_name'];
                                        ?>
                                        <option value="<?= $section['id'] ?>"><?= htmlspecialchars($section_display_name) ?></option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body pt-2 pb-2">
                        <?php if (empty($test_questions)) : ?>
                            <div class="text-center py-4">
                                <p class="mb-0">No questions have been added to this test yet.</p>
                                <p class="text-sm text-muted">Select questions from the list on the right to add them.</p>
                            </div>
                        <?php else : ?>
                            <?php if ($sections_enabled): ?>
                                <!-- Grouped by Sections View -->
                                <?php
                                // Group questions by section
                                // Note: With default section, NULL section_id shouldn't exist anymore
                                // but we handle it as fallback
                                $grouped_questions = [];
                                foreach ($test_questions as $question) {
                                    $section_id = $question['section_id'];
                                    // If section_id is null and we have default section, use it
                                    if ($section_id === null && $default_section_id) {
                                        $section_id = $default_section_id;
                                    }
                                    if (!isset($grouped_questions[$section_id])) {
                                        $grouped_questions[$section_id] = [];
                                    }
                                    $grouped_questions[$section_id][] = $question;
                                }
                                
                                // Sort: sections in order
                                $sorted_groups = [];
                                foreach ($sections as $section) {
                                    if (isset($grouped_questions[$section['id']])) {
                                        $sorted_groups[$section['id']] = [
                                            'name' => (isset($section['id']) && $section['id'] == DEFAULT_SECTION_ID) ? 'Unassigned Questions' : $section['section_name'],
                                            'questions' => $grouped_questions[$section['id']]
                                        ];
                                    }
                                }
                                
                                // Fallback: Handle any remaining NULL questions (shouldn't happen with default section)
                                if (isset($grouped_questions[null])) {
                                    $sorted_groups[null] = [
                                        'name' => 'Unassigned Questions',
                                        'questions' => $grouped_questions[null]
                                    ];
                                }
                                
                                // Color palette for sections - different colors for each section
                                $section_colors = [
                                    '#674eeb', // Purple (default)
                                    '#f59e0b', // Amber
                                    '#10b981', // Emerald
                                    '#3b82f6', // Blue
                                    '#ef4444', // Red
                                    '#8b5cf6', // Violet
                                    '#06b6d4', // Cyan
                                    '#f97316', // Orange
                                    '#14b8a6', // Teal
                                    '#ec4899', // Pink
                                ];
                                
                                $global_index = 0;
                                $section_index = 0;
                                $total_sections = count($sorted_groups);
                                ?>
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">#</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Question</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Score</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Type</th>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="sortable-questions">
                                            <?php foreach ($sorted_groups as $section_id => $group): ?>
                                                <?php 
                                                // Get color for this section (cycle through colors)
                                                $section_color = $section_colors[$section_index % count($section_colors)];
                                                $section_index++;
                                                $is_last_section = ($section_index >= $total_sections);
                                                ?>
                                                <!-- Section Header Row -->
                                                <tr class="section-header-row bg-gradient-light" data-section-id="<?= is_numeric($section_id) ? $section_id : '' ?>" style="--section-color: <?= $section_color ?>;">
                                                    <td colspan="5" class="py-2" style="border-left-color: <?= $section_color ?> !important;">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-layer-group me-2" style="color: <?= $section_color ?>;"></i>
                                                            <strong class="section-title" style="color: <?= $section_color ?>;"><?= htmlspecialchars($group['name']) ?></strong>
                                                            <span class="badge bg-secondary ms-2 section-badge" style="background: linear-gradient(135deg, <?= $section_color ?> 0%, <?= $section_color ?>dd 100%) !important;"><?= count($group['questions']) ?> question(s)</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <!-- Questions in this section -->
                                                <?php foreach ($group['questions'] as $question): ?>
                                                    <?php $global_index++; ?>
                                                    <tr class="question-row" data-question-id="<?= $question['id'] ?>" data-test-question-id="<?= $question['test_question_id'] ?>" data-section-id="<?= $question['section_id'] ?? '' ?>" style="--section-color: <?= $section_color ?>; border-left-color: <?= $section_color ?> !important;">
                                                        <td>
                                                            <div class="d-flex px-2 py-1">
                                                                <div class="handle cursor-move me-3">
                                                                    <i class="fas fa-grip-vertical text-secondary"></i>
                                                                </div>
                                                                <span class="text-secondary text-xs font-weight-bold question-order"><?= $global_index ?></span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <p class="text-xs font-weight-bold mb-0"><?= $question['title'] ?></p>
                                                                <!-- Unsaved indicator badge (hidden by default, shown via JS) -->
                                                                <span class="badge bg-warning text-dark ms-2 unsaved-badge" style="display: none;">
                                                                    <i class="fas fa-exclamation-circle me-1"></i> Unsaved
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="text-secondary text-xs font-weight-bold"><?= $question['score'] ?></span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-<?= $question['type'] == 'MCQ' ? 'info' : ($question['type'] == 'CODE' ? 'warning' : 'success') ?>"><?= $question['type'] ?></span>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <button type="button" data-id="<?= $question['id']; ?>" class="btn btn-info btn-sm view-question">View</button>
                                                            <button type="button" class="btn btn-danger btn-sm remove-question" data-test-question-id="<?= $question['test_question_id'] ?>">
                                                                Delete
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                <!-- Spacer row between sections (not after last section) -->
                                                <?php if (!$is_last_section): ?>
                                                    <tr class="section-spacer-row">
                                                        <td colspan="5" style="height:2px; border: none; background: transparent;"></td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <!-- Original Non-Sectioned View -->
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">#</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Question</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Score</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Type</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="sortable-questions">
                                        <?php foreach ($test_questions as $index => $question) : ?>
                                                <tr class="question-row" data-question-id="<?= $question['id'] ?>" data-test-question-id="<?= $question['test_question_id'] ?>" data-section-id="<?= $question['section_id'] ?? '' ?>">
                                                <td>
                                                    <div class="d-flex px-2 py-1">
                                                        <div class="handle cursor-move me-3">
                                                            <i class="fas fa-grip-vertical text-secondary"></i>
                                                        </div>
                                                        <span class="text-secondary text-xs font-weight-bold question-order"><?= $index + 1 ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <p class="text-xs font-weight-bold mb-0"><?= $question['title'] ?></p>
                                                        <!-- Unsaved indicator badge (hidden by default, shown via JS) -->
                                                        <span class="badge bg-warning text-dark ms-2 unsaved-badge" style="display: none;">
                                                            <i class="fas fa-exclamation-circle me-1"></i> Unsaved
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-secondary text-xs font-weight-bold"><?= $question['score'] ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $question['type'] == 'MCQ' ? 'info' : ($question['type'] == 'CODE' ? 'warning' : 'success') ?>"><?= $question['type'] ?></span>
                                                </td>
                                                <td class="align-middle text-center">
                                                <button type="button" data-id="<?= $question['id']; ?>" class="btn btn-info btn-sm view-question">View</button>
                                                    <button type="button" class="btn btn-danger btn-sm remove-question" data-test-question-id="<?= $question['test_question_id'] ?>">
                                                    Delete
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Available Questions Column -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Available Questions</h6>
                            <div class="d-flex align-items-center">
                                <?php if ($sections_enabled): ?>
                                <!-- Add Selected Dropdown with Sections -->
                                <div class="btn-group me-2">
                                    <button type="button" id="addSelectedQuestions" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-plus"></i> Add Selected
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" id="sectionDropdownForAdd">
                                        <?php 
                                        $non_default_sections = array_filter($sections, function($section) use ($default_section_id) {
                                            return $section['id'] != $default_section_id;
                                        });
                                        if (!empty($non_default_sections)): ?>
                                            <?php foreach ($non_default_sections as $section): ?>
                                                <?php 
                                                $section_display_name = ($section['id'] == $default_section_id) ? 'Unassigned Questions' : $section['section_name'];
                                                ?>
                                                <li><a class="dropdown-item section-option" href="javascript:void(0);" data-section-id="<?= $section['id'] ?>" data-section-name="<?= htmlspecialchars($section_display_name) ?>">
                                                    <i class="fas fa-layer-group me-2"></i><?= htmlspecialchars($section_display_name) ?>
                                                </a></li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <li><a class="dropdown-item disabled" href="#">No sections available</a></li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                                <?php else: ?>
                                <button id="addSelectedQuestions" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Add Selected
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if ($sections_enabled): ?>
                        <div class="alert alert-info alert-sm mt-2 mb-0 py-2">
                            <small><i class="fas fa-info-circle"></i> <strong>How to add questions:</strong> Use the dropdown arrows to select a section, then check questions and click "Add Selected" OR click the "+" icon on individual questions to choose their section.</small>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body pt-2 pb-2">
                        <?php if (empty($available_questions)) : ?>
                            <div class="text-center py-4">
                                <p class="mb-0">No available questions found.</p>
                                <a href="<?= base_url($url.'/question/add') ?>" class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-plus"></i> Create New Question
                                </a>
                            </div>
                        <?php else : ?>
                            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                                <table class="table align-items-center mb-0" id="availableQuestionsTable">
                                    <thead class="position-sticky top-0 bg-white">
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2" style="width: 30px;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="selectAllQuestions">
                                                </div>
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Question</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Score</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Type</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Topic</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Difficulty</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($available_questions as $question) : ?>
                                            <tr class="available-question-row" data-question-id="<?= $question['id'] ?>" data-question-type="<?= $question['type'] ?>">
                                                <td>
                                                    <div class="form-check ms-2">
                                                        <input class="form-check-input question-checkbox" type="checkbox" value="<?= $question['id'] ?>">
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0"><?= $question['title'] ?></p>
                                                </td>
                                                <td>
                                                    <span class="text-secondary text-xs font-weight-bold"><?= $question['score'] ?></span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $question['type'] == 'MCQ' ? 'info' : ($question['type'] == 'CODE' ? 'warning' : 'success') ?>"><?= $question['type'] ?></span>
                                                </td>
                                                <td>
                                                    <span class="text-secondary text-xs font-weight-bold"><?= $question['topics'] ?></span>
                                                </td>
                                                <td>
                                                    <span class="text-secondary text-xs font-weight-bold"><?= $question['level'] ?></span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <button type="button" data-id="<?= $question['id']; ?>" class="btn btn-info btn-sm view-question">View</button>
                                                    <?php if ($sections_enabled): ?>
                                                    <div class="dropdown d-inline-block dropup">
                                                        <button type="button" class="btn btn-sm btn-primary question-add-dropdown" data-bs-toggle="dropdown" aria-expanded="false" data-question-id="<?= $question['id'] ?>">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <?php
                                                            $non_default_sections = array_filter($sections, function($section) use ($default_section_id) {
                                                                return $section['id'] != $default_section_id;
                                                            });
                                                            if (!empty($non_default_sections)): ?>
                                                                <?php foreach ($non_default_sections as $section): ?>
                                                                    <?php 
                                                                    $section_display_name = ($section['id'] == $default_section_id) ? 'Unassigned Questions' : $section['section_name'];
                                                                    ?>
                                                                    <li><a class="dropdown-item add-single-question" href="javascript:void(0);" data-question-id="<?= $question['id'] ?>" data-section-id="<?= $section['id'] ?>" data-section-name="<?= htmlspecialchars($section_display_name) ?>">
                                                                        <i class="fas fa-layer-group me-2"></i><?= htmlspecialchars($section_display_name) ?>
                                                                    </a></li>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <li><a class="dropdown-item disabled" href="#">No sections available</a></li>
                                                            <?php endif; ?>
                                                        </ul>
                                                    </div>
                                                    <?php else: ?>
                                                    <button type="button" class="btn btn-sm btn-primary add-question" data-question-id="<?= $question['id'] ?>">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                    <?php endif; ?>
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

    <!-- Remove Question Confirmation Modal -->
    <div class="modal fade" id="removeQuestionModal" tabindex="-1" aria-labelledby="removeQuestionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="removeQuestionModalLabel">Confirm Removal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to remove this question from the test?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmRemoveBtn">Remove</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Manage Sections Modal -->
    <div class="modal fade" id="manageSectionsModal" tabindex="-1" aria-labelledby="manageSectionsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageSectionsModalLabel">Manage Test Sections</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Add New Section -->
                    <div class="mb-4">
                        <label for="newSectionName" class="form-label">Add New Section</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="newSectionName" placeholder="Enter section name (e.g., Quantitative, Verbal)">
                            <button class="btn btn-primary" type="button" id="addSectionBtn">
                                <i class="fas fa-plus"></i> Add Section
                            </button>
                        </div>
                    </div>

                    <!-- Sections List -->
                    <div id="sectionsList">
                        <h6 class="text-muted">Loading sections...</h6>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Question to Section Modal -->
    <div class="modal fade" id="assignToSectionModal" tabindex="-1" aria-labelledby="assignToSectionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignToSectionModalLabel">Assign to Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Select a section to assign this question to:</p>
                    <select class="form-control" id="sectionSelectDropdown">
                        <option value="">-- Select Section --</option>
                    </select>
                    <input type="hidden" id="assignQuestionId" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmAssignBtn">Assign</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Section Confirmation Modal -->
    <div class="modal fade" id="deleteSectionModal" tabindex="-1" aria-labelledby="deleteSectionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteSectionModalLabel">Confirm Section Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> Deleting this section will unassign all questions linked to it. They will be moved to the Unassigned section
                    </div>
                    <p>Are you sure you want to delete the section "<strong id="sectionNameToDelete"></strong>"?</p>
                    <!-- <p class="text-muted mb-0"><strong>Note:</strong> Deleting this section will assign all questions in it to the unassigned section.</p> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteSectionBtn">
                        <i class="fas fa-trash me-1"></i> Delete Section
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Loader Component -->
<div id="globalLoader" class="global-loader">
    <div class="loader-content">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 mb-0">Loading...</p>
    </div>
</div>

<style>

/* Section Header Styles - Tab-like appearance */
.section-header-row {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
    border: 2px solid #dee2e6 !important;
    border-bottom: none !important;
    font-weight: 600;
    position: relative;
    margin-bottom: 0;
    transition: all 0.3s ease;
}

.section-header-row:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);

}


.section-header-row td {
    padding: 15px 12px !important;
    border-radius: 12px 12px 0 0 !important;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%) !important;
    /* box-shadow: 0 2px 6px rgba(0,0,0,0.1); */
    border: 2px solid #dee2e6;  
    border-left: 4px solid var(--section-color, #674eeb) !important;
    position: relative;
    /* Smooth transition for all properties */
    /* transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); */
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

/* Style section title and badge */
.section-title {
    font-size: 1.0em;
    color: #495057;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.section-badge {
    background: linear-gradient(135deg, var(--section-color, #674eeb) 0%, var(--section-color, #5a3fd8)dd 100%) !important;
    color: white;
    font-weight: 600;
    padding: 4px 8px;
    border-radius: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

/* Add spacing between sections */
.section-header-row + .question-row {
    border-top: none;
    margin-top: 0;
}

/* Group questions under sections with contained styling */
.question-row {
    background: linear-gradient(135deg, #fafbfc 0%, #f8f9fa 100%) !important;
    border-left: 4px solid var(--section-color, #dee2e6) !important;
    border-right: 2px solid #dee2e6;
    border-bottom: 1px solid #e9ecef;
    transition: all 0.2s ease;
}

/* Ensure question rows use the same color as section header */
.question-row[style*="--section-color"] {
    border-left-color: var(--section-color) !important;
}

.question-row:hover {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    transform: translateX(2px);
}

.question-row:last-child {
    border-bottom: 2px solid #dee2e6;
    border-radius: 0 0 12px 12px;
}

/* Remove default table borders for sectioned view */
.table tbody tr.section-header-row + tr.question-row {
    border-top: none;
}

/* Add visual separation between section groups */
.section-header-row:not(:first-child) {
    margin-top: 0;
}

/* Spacer row between sections */
.section-spacer-row {
    height: 2px;
    border: none !important;
    background: transparent !important;
}

.section-spacer-row td {
    border: none !important;
    padding: 0 !important;
    height: 2px;
    background: transparent !important;
}

/* Enhanced drag and drop visual cues */
.sortable-ghost {
    opacity: 0.6;
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%) !important;
    border: 2px dashed #2196f3 !important;
    border-radius: 8px;
}

.sortable-drag {
    opacity: 0.9;
    background: linear-gradient(135deg, #fff3e0 0%, #ffcc02 100%) !important;
    box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    transform: rotate(1deg) scale(1.02);
    border-radius: 8px;
}

/* Question rows in sections */
.question-row {
    transition: all 0.2s;
}

.question-row:hover {
    background-color: #f8f9fa !important;
}

/* Dragging styles */
.sortable-ghost {
    opacity: 0.4;
    background-color: #e3e8ff !important;
    border: 2px dashed #674eeb !important;
}

.sortable-drag {
    opacity: 0.8;
    background-color: #fff !important;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
    transform: rotate(2deg);
}

/* Section headers should not be affected by drag */
.section-header-row {
    cursor: default !important;
}

/* Section Dropdown Styling */
.section-select-dropdown,
.section-filter-dropdown {
    border: 2px solid #dee2e6;
    transition: all 0.3s ease;
    font-weight: 500;
}

.section-select-dropdown:hover,
.section-filter-dropdown:hover {
    border-color: #674eeb;
    background-color: #f8f9ff;
}

.section-select-dropdown:focus,
.section-filter-dropdown:focus {
    border-color: #674eeb;
    box-shadow: 0 0 0 0.25rem rgba(103, 78, 235, 0.15);
    background-color: #fff;
}

.section-select-dropdown option,
.section-filter-dropdown option {
    padding: 8px 12px;
}


/* Gradient for section select icon background */
.bg-gradient-primary {
    background: linear-gradient(135deg, #674eeb 0%, #5a3fd8 100%) !important;
}

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

.cursor-move {
    cursor: move;
}

.top-0 {
    top: 0;
}

.handle {
    padding: 0.25rem;
    border-radius: 0.25rem;
}

.handle:hover {
    background-color: #f8f9fa;
}

/* Fixed Action Column Styles */
.table-responsive {
    position: relative;
}

.table-responsive table {
    width: 100%;
}

.table-responsive th:last-child,
.table-responsive td:last-child {
    position: sticky;
    right: 0;
    background-color: #fff;
    z-index: 9;
    box-shadow: -2px 0 5px -2px rgba(0,0,0,0.1);
}

/* Ensure fixed header stays on top of fixed column */
.table-responsive thead th:last-child {
    z-index: 11;
    background-color: #fff;
}

/* Hover effect for rows */
.table-responsive tbody tr:hover td {
    background-color: #f8f9fa;
}

/* Ensure fixed column maintains hover effect */
.table-responsive tbody tr:hover td:last-child {
    background-color: #f8f9fa;
}

/* Ensure proper alignment of action buttons */
.table-responsive .btn-group {
    white-space: nowrap;
    display: inline-flex;
}

/* Ensure proper alignment of badges */
.table-responsive .badge {
    white-space: nowrap;
}

/* Ensure proper alignment of checkboxes */
.table-responsive .form-check {
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
}

/* Add shadow to indicate scrollable content */
.table-responsive::after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    width: 5px;
    background: linear-gradient(to right, rgba(0,0,0,0.05), rgba(0,0,0,0));
    pointer-events: none;
}

/* Ensure proper spacing in the table */
.table-responsive td,
.table-responsive th {
    padding: 0.75rem;
    vertical-align: middle;
}

/* Handle long content in question column */
.table-responsive td:nth-child(2) p {
    white-space: normal;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 300px;
}

/* Loader Styles */
.global-loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

.global-loader.active {
    display: flex;
}

.loader-content {
    text-align: center;
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

.loader-content p {
    color: #344767;
    font-size: 0.875rem;
}

/* Section Editing Styles */
.editing-section {
    background-color: #f8f9ff !important;
    border-left: 3px solid #674eeb !important;
    transition: all 0.2s ease;
}

.section-edit-input {
    max-width: 300px;
    font-weight: 600;
    border: none !important;
    border-bottom: 2px solid #674eeb !important;
    border-radius: 0 !important;
    box-shadow: none !important;
}

.section-edit-input:focus {
    outline: none !important;
    border-bottom-color: #674eeb !important;
    box-shadow: none !important;
}

/* Unsaved question styling - higher specificity to override inline styles and section gradients */
tr.question-row[data-unsaved="true"] {
    /* background: #f5f5f5 !important; Light grey - commented out */
    /* background-color: #f5f5f5 !important; */
    background: #eeeeee !important; /* grey lighten-3 */
    background-color: #eeeeee !important;
    border-left-width: 4px !important;
    border-left-color: #9e9e9e !important; /* Grey accent */
    animation: pulse-unsaved 2s infinite;
}

tr.question-row[data-unsaved="true"]:hover {
    /* background: #eeeeee !important; Slightly darker grey on hover - commented out */
    /* background-color: #eeeeee !important; */
    background: #e0e0e0 !important; /* grey lighten-2 */
    background-color: #e0e0e0 !important;
}

tr.question-row[data-unsaved="true"] td {
    /* background: #f5f5f5 !important; - commented out */
    /* background-color: #f5f5f5 !important; */
    background: #eeeeee !important; /* grey lighten-3 */
    background-color: #eeeeee !important;
}

tr.question-row[data-unsaved="true"]:hover td {
    /* background: #eeeeee !important; - commented out */
    /* background-color: #eeeeee !important; */
    background: #e0e0e0 !important; /* grey lighten-2 */
    background-color: #e0e0e0 !important;
}

/* Override any gradient backgrounds for unsaved questions */
tr.question-row[data-unsaved="true"][style*="background"] {
    /* background: #f5f5f5 !important; - commented out */
    /* background-color: #f5f5f5 !important; */
    background: #eeeeee !important; /* grey lighten-3 */
    background-color: #eeeeee !important;
}

@keyframes pulse-unsaved {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(158, 158, 158, 0.4); /* Grey accent color */
    }
    50% {
        box-shadow: 0 0 0 4px rgba(158, 158, 158, 0);
    }
}

/* Save Order button pulse when unsaved questions exist */
.save-order-pulse {
    position: relative;
    animation: pulse-save-order 1.8s ease-in-out infinite;
}

@keyframes pulse-save-order {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.5);
        transform: translateY(0);
    }
    50% {
        box-shadow: 0 0 0 8px rgba(255, 193, 7, 0);
        transform: translateY(-1px);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
        transform: translateY(0);
    }
}

/* Save Order header button default color */
.save-order-header-btn {
    background-color: #674ECF;
    border-color: #674ECF;
    color: #ffffff;
}

.save-order-header-btn:hover {
    background-color: #5b43c2;
    border-color: #5b43c2;
    color: #ffffff;
}

/* Sectional Save Order banner (yellow warning strip) */
.save-order-banner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;
    background-color: #fff8e6;
    border: 1px solid #ffe0b2;
    color: #8a6d3b;
    font-size: 0.9rem;
}

.save-order-banner-text {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.save-order-banner-text i {
    color: #ffa000;
}

.save-order-banner-actions .btn {
    white-space: nowrap;
}


</style>

<!-- Include Sortable.js library -->
<script src="<?= base_url("/") ?>assets/packages/sortable.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
<script src="<?= base_url("/") ?>assets/packages/marked.min.js"></script>
<script>
$(document).ready(function() {
    let testId = <?= $test->id ?>;
    let questionToRemove = null;
    const sectionsEnabled = <?= $sections_enabled ? 'true' : 'false' ?>;
    
    // Track unsaved questions (questions added after page load or after last save)
    let unsavedQuestionIds = new Set();
    
    // Function to update unsaved indicator (background colors only, no alerts)
    function updateUnsavedIndicator() {
        // Mark questions as unsaved (only those in the set) - background color only
        $('.question-row').each(function() {
            const testQuestionId = $(this).data('test-question-id');
            if (unsavedQuestionIds.has(testQuestionId)) {
                $(this).attr('data-unsaved', 'true');
            } else {
                $(this).attr('data-unsaved', 'false');
            }
        });
        
        // After marking unsaved rows, manage the Save Order banner/button for all tests
        checkSaveRequired();
    }
    
    // Function to check if save is required and highlight Save Order controls
    // - NORMAL tests: Save Order is optional, always shown in purple (#674ECF), never pulses, no banner
    // - SECTIONAL tests:
    //     * No unsaved questions: header button purple, banner hidden
    //     * Unsaved questions: header button hidden, yellow banner with pulsing Save Order on the right
    function checkSaveRequired() {
        const $headerBtn = $('#saveOrderHeader');
        const $bannerRow = $('#saveRequiredWarningRow');
        const $bannerBtn = $('#saveOrderBanner');
        const $warningCount = $('#warningUnsavedCount');

        // Normal tests (sections disabled): Save Order is optional
        if (!sectionsEnabled) {
            // Header button always visible, calm purple style
            $headerBtn
                .show()
                .removeClass('btn-warning save-order-pulse')
                .addClass('save-order-header-btn')
                .html('<i class="fas fa-save me-1"></i> Save Order');

            // No banner for normal tests
            $bannerRow.addClass('d-none');

            // Never treat as "required" for normal tests
            return false;
        }

        // Sectional tests: Save Order is required when there are unsaved questions
        if (unsavedQuestionIds.size > 0) {
            // Hide header button while changes are pending
            $headerBtn
                .hide()
                .removeClass('btn-warning save-order-pulse')
                .addClass('save-order-header-btn')
                .html('<i class="fas fa-save me-1"></i> Save Order');

            // Show yellow banner with pulsing Save Order button
            $bannerRow.removeClass('d-none');
            if ($warningCount.length) {
                $warningCount.text(unsavedQuestionIds.size);
            }
            $bannerBtn
                .removeClass('btn-outline-secondary')
                .addClass('btn-warning save-order-pulse')
                .html('<i class="fas fa-exclamation-triangle me-1"></i> Save Order');

            return true; // Save is required (sectional)
        } else {
            // Saved state for sectional tests: calm purple Save Order button
            $headerBtn
                .show()
                .removeClass('btn-warning save-order-pulse')
                .addClass('save-order-header-btn')
                .html('<i class="fas fa-save me-1"></i> Save Order');

            // Hide banner when everything is saved
            $bannerRow.addClass('d-none');
            $bannerBtn
                .removeClass('btn-warning save-order-pulse')
                .addClass('btn-outline-secondary')
                .html('<i class="fas fa-save me-1"></i> Save Order');

            return false; // No required save pending
        }
    }
    
    // Mark all existing questions as saved on page load
    $('.question-row').each(function() {
        $(this).attr('data-unsaved', 'false');
    });
    
    // After page reload, check which specific questions were just added
    // DON'T clear localStorage here - keep it until user clicks "SAVE ORDER"
    const newlyAddedQuestionIds = JSON.parse(localStorage.getItem('newlyAddedQuestionIds_' + testId) || '[]');
    if (newlyAddedQuestionIds.length > 0 && sectionsEnabled) {
        console.log('Found newly added question IDs:', newlyAddedQuestionIds);
        
        // Convert all IDs to numbers for consistent matching
        const newlyAddedIdsNum = newlyAddedQuestionIds.map(function(id) { return parseInt(id); });
        
        // Mark only the newly added questions as unsaved
        $('.question-row').each(function() {
            const $row = $(this);
            const questionId = parseInt($row.attr('data-question-id'));
            
            if (newlyAddedIdsNum.indexOf(questionId) !== -1) {
                const testQuestionId = $row.data('test-question-id');
                unsavedQuestionIds.add(testQuestionId);
                $row.attr('data-unsaved', 'true');
                console.log('Marked question as unsaved:', questionId, 'test_question_id:', testQuestionId);
            }
        });
        
        updateUnsavedIndicator();
        
        // DO NOT clear localStorage here - it should persist until "SAVE ORDER" is clicked
        // This allows multiple questions to accumulate as unsaved
    } else if (sectionsEnabled) {
        // Initialize counts even if no unsaved questions
        updateUnsavedIndicator();
    }
    
    // Flag to track if we're intentionally reloading (to prevent beforeunload warning)
    let isIntentionalReload = false;
    
    // Prevent navigation if there are unsaved questions (but not for intentional reloads)
    $(window).on('beforeunload', function(e) {
        if (isIntentionalReload) {
            // Don't show warning for intentional reloads (after adding questions)
            return;
        }
        if (sectionsEnabled && unsavedQuestionIds.size > 0) {
            // Show browser warning only for accidental navigation
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
            return e.returnValue;
        }
    });

    // Loader functions
    function showLoader() {
        $('#globalLoader').addClass('active');
    }

    function hideLoader() {
        $('#globalLoader').removeClass('active');
    }

    // Modify AJAX setup to show/hide loader
    $.ajaxSetup({
        beforeSend: function() {
            showLoader();
        },
        complete: function() {
            hideLoader();
        }
    });

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
                    <div class="markdown-content"><pre>${marked.parse(question.question_content)}</pre></div>
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





    const availableQuestionsTable = $('#availableQuestionsTable').DataTable({
        pageLength: 10,
        lengthMenu: [[5, 10, 25, 50, -1], [5, 10, 25, 50, "All"]],
        order: [[1, 'asc']], // Order by question title by default
        columnDefs: [
            { orderable: false, targets: [0, 6] }, // Disable ordering for checkbox and action columns
            { searchable: false, targets: [0, 6] } // Disable searching for checkbox, type, and action columns
        ],
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l<"ms-2">><"d-flex"f>>rtip',
        language: {
            search: "",
            searchPlaceholder: "Search questions...",
            lengthMenu: "_MENU_"
        },
        initComplete: function() {
            // Add type filter dropdown next to the length menu
            const filterHtml = `
                <div class="d-flex align-items-center ms-2">
                    <select class="form-select form-select-sm me-2" id="topicFilterDT" style="width: 120px;">
                        <option value="">All Topics</option>
                        <?php foreach ($topics as $topic) : ?>
                            <option value="<?= $topic ?>"><?= $topic ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="form-select form-select-sm me-2" id="difficultyFilterDT" style="width: 120px;">
                        <option value="">All Levels</option>
                        <?php foreach ($difficulty_levels as $level) : ?>
                            <option value="<?= $level['level'] ?>"><?=$level['level'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select class="form-select form-select-sm me-2" id="typeFilterDT" style="width: 120px;">
                        <option value="">All Types</option>
                        <?php foreach ($question_types as $type) : ?>
                            <option value="<?= $type ?>"><?= $type ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            `;
            $(filterHtml).insertAfter('.dataTables_length select');
            
            // Apply custom styling to search input
            $('.dataTables_filter input').addClass('form-control form-control-sm');
            $('.dataTables_filter input').attr('placeholder', 'Search questions...');
            $('.dataTables_filter label').addClass('mb-0');
            $('.dataTables_filter label').contents().filter(function() {
                return this.nodeType === 3;
            }).remove();
            
            $('.dataTables_length select').addClass('form-select form-select-sm');

             // CUSTOM SEARCH FUNCTION FOR ALL FILTERS
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    // Only apply to our specific table
                    if (settings.nTable.id !== 'availableQuestionsTable') {
                        return true;
                    }

                    // Get filter values
                    const typeFilter = $('#typeFilterDT').val();
                    const topicFilter = $('#topicFilterDT').val();
                    const difficultyFilter = $('#difficultyFilterDT').val();

                    // Get data from the row (adjust column indices as needed)
                    const rowType = data[3] || ''; // Type column (remove HTML tags)
                    const rowTopic = data[4] || ''; // Topic column
                    const rowDifficulty = data[5] || ''; // Difficulty column

                        if (topicFilter) {
                            console.log(data)
                            console.log('=== Topic Filter Debug ===');
                            console.log('Topic Filter:', topicFilter);
                            console.log('Row Topic:', rowTopic);
                            console.log('Match Result:', rowTopic.toLowerCase().includes(topicFilter.toLowerCase()));
                            console.log('========================');
                        }

                    // Clean the type data (remove HTML badge tags)
                    const cleanType = rowType.replace(/<[^>]*>/g, '');
                    
                    // Apply filters
                    const typeMatch = !typeFilter || cleanType === typeFilter;
                    const topicMatch = !topicFilter || rowTopic.includes(topicFilter);
                    const difficultyMatch = !difficultyFilter || rowDifficulty.includes(difficultyFilter);

                    return typeMatch && topicMatch && difficultyMatch;
                }
            );

            $('#typeFilterDT, #topicFilterDT, #difficultyFilterDT').on('change', function() {
                availableQuestionsTable.draw();
            });
                

            
        
        }
    });
    
    // Custom filtering function for question type
    $('#typeFilterDT').on('change', function() {
        const typeFilter = $(this).val();
        
        $.fn.dataTable.ext.search.pop(); // Remove previous filter
        
        if (typeFilter) {
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const type = $(availableQuestionsTable.row(dataIndex).node()).data('question-type');
                return type === typeFilter;
            });
        }
        
        availableQuestionsTable.draw();
    });
    
    // Handle "Select All" checkbox with DataTables pagination
    $('#selectAllQuestions').on('change', function() {
        const isChecked = $(this).prop('checked');
        
        // Select/deselect checkboxes on the current page only
        availableQuestionsTable.page.info().recordsDisplay;
        $('.question-checkbox').prop('checked', false);
        
        availableQuestionsTable.rows({page: 'current'}).nodes().each(function(row) {
            $(row).find('.question-checkbox').prop('checked', isChecked);
        });
    });
    
    // Reset "Select All" checkbox when changing page
    availableQuestionsTable.on('page.dt', function() {
        $('#selectAllQuestions').prop('checked', false);
    });
    
    // Initialize Sortable.js for the selected questions
    const sortableQuestions = document.querySelector('.sortable-questions');
    if (sortableQuestions) {
        new Sortable(sortableQuestions, {
            handle: '.handle',
            animation: 150,
            filter: '.section-header-row',  // Prevent section headers from being dragged
            preventOnFilter: false,
            onEnd: function(evt) {
                updateQuestionNumbers();
                
                <?php if ($sections_enabled): ?>
                // Check if question was moved to a different section
                const movedRow = $(evt.item);
                const testQuestionId = movedRow.data('test-question-id');
                const oldSectionId = movedRow.data('section-id') || '';
                
                // Find the section header above this question
                const sectionHeader = movedRow.prevAll('.section-header-row').first();
                const newSectionId = sectionHeader.length > 0 ? (sectionHeader.data('section-id') || '') : '';
                
                // Update section assignment if it changed
                if (String(oldSectionId) !== String(newSectionId)) {
                    console.log('Section changed from', oldSectionId, 'to', newSectionId);
                    
                    // IMMEDIATELY update the visual color before AJAX call
                    if (sectionHeader.length > 0) {
                        // Get the section color from the header
                        // Try multiple methods to extract the color
                        let sectionColor = null;
                        
                        // Method 1: Get from CSS variable
                        const computedStyle = window.getComputedStyle(sectionHeader[0]);
                        sectionColor = computedStyle.getPropertyValue('--section-color').trim();
                        
                        // Method 2: Get from border-left-color style
                        if (!sectionColor) {
                            sectionColor = sectionHeader.css('border-left-color');
                        }
                        
                        // Method 3: Parse from inline style attribute
                        if (!sectionColor) {
                            const styleAttr = sectionHeader.attr('style') || '';
                            const match = styleAttr.match(/--section-color:\s*([^;]+)/);
                            if (match) {
                                sectionColor = match[1].trim();
                            }
                        }
                        
                        // Method 4: Parse border-left-color from style attribute
                        if (!sectionColor) {
                            const styleAttr = sectionHeader.attr('style') || '';
                            const match = styleAttr.match(/border-left-color:\s*([^!;]+)/);
                            if (match) {
                                sectionColor = match[1].trim();
                            }
                        }
                        
                        if (sectionColor) {
                            // Update the row's border color immediately
                            const currentStyle = movedRow.attr('style') || '';
                            // Remove any existing border-left-color and --section-color from style
                            const cleanedStyle = currentStyle
                                .replace(/border-left-color:[^;]*/gi, '')
                                .replace(/--section-color:[^;]*/gi, '')
                                .replace(/;+/g, ';')
                                .replace(/^;|;$/g, '');
                            // Add new color styles
                            movedRow.attr('style', cleanedStyle + '; border-left-color: ' + sectionColor + ' !important; --section-color: ' + sectionColor);
                        }
                    }
                    
                    updateQuestionSection(testQuestionId, newSectionId, movedRow);
                }
                <?php endif; ?>
            }
        });
    }
    
    // Function to update question section via AJAX
    function updateQuestionSection(testQuestionId, newSectionId, $row) {
        $.ajax({
            url: '<?= base_url($url.'/test/assign_question_to_section') ?>',
            type: 'POST',
            data: {
                test_question_id: testQuestionId,
                section_id: newSectionId || null
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Update the row's data attribute
                    $row.attr('data-section-id', newSectionId);
                    
                    // Ensure color is set (in case it wasn't set in onEnd)
                    const sectionHeader = $row.prevAll('.section-header-row').first();
                    if (sectionHeader.length > 0) {
                        const sectionColor = sectionHeader.css('border-left-color') || 
                                           window.getComputedStyle(sectionHeader[0]).getPropertyValue('--section-color').trim();
                        if (sectionColor) {
                            $row.css('border-left-color', sectionColor + ' !important');
                            $row.css('--section-color', sectionColor);
                        }
                    }
                    
                    // Update section counts
                    updateSectionCounts();
                    
                    showAlert('success', '<i class="fas fa-check-circle"></i> Question moved to new section successfully!');
                } else {
                    showAlert('danger', '<i class="fas fa-times-circle"></i> ' + (response.message || 'Error updating section'));
                    // Reload page to restore original state
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                }
            },
            error: function() {
                showAlert('danger', '<i class="fas fa-times-circle"></i> Error updating section. Page will reload.');
                setTimeout(function() {
                    location.reload();
                }, 2000);
            }
        });
    }
    
    // Update section counts in headers
    function updateSectionCounts() {
        $('.section-header-row').each(function() {
            const $headerRow = $(this);
            
            // Count all questions physically under this section header
            // (until the next section header or end of list)
            const count = $headerRow.nextUntil('.section-header-row', '.question-row').length;
            
            // Update the badge
            $headerRow.find('.badge').text(count + ' question(s)');
        });
    }
    
    // Update question numbers when order changes
    function updateQuestionNumbers() {
        $('.question-row').each(function(index) {
            $(this).find('.question-order').text(index + 1);
        });
    }
    
    // Filter available questions
    $('#typeFilter, #searchQuestions').on('change keyup', function() {
        const typeFilter = $('#typeFilter').val();
        const searchText = $('#searchQuestions').val().toLowerCase();
        
        $('.available-question-row').each(function() {
            const questionType = $(this).data('question-type');
            const questionTitle = $(this).find('p').text().toLowerCase();
            
            const typeMatch = !typeFilter || questionType === typeFilter;
            const searchMatch = !searchText || questionTitle.includes(searchText);
            
            $(this).toggle(typeMatch && searchMatch);
        });
    });
    
    // Select all questions
    $('#selectAllQuestions').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('.question-checkbox:visible').prop('checked', isChecked);
    });
    
    // Variable to store selected section for "Add Selected" button
    let selectedSectionForAdd = null;
    let selectedSectionNameForAdd = '';
    
    <?php if ($sections_enabled): ?>
    // Handle section selection from dropdown for "Add Selected" button
    $(document).on('click', '.section-option', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Store selected section
        selectedSectionForAdd = $(this).data('section-id');
        selectedSectionNameForAdd = $(this).data('section-name');
        
        // Check if any questions are already selected via checkbox
        const selectedQuestions = [];
        $('.question-checkbox:checked').each(function() {
            selectedQuestions.push($(this).val());
        });
        
        if (selectedQuestions.length > 0) {
            // Questions are selected, add them immediately
            addMultipleQuestionsWithSection(selectedQuestions, selectedSectionForAdd);
        } else {
            // No questions selected, show error
            showAlert('warning', '<strong><i class="fas fa-info-circle"></i> Please select at least one question first, then choose a section.</strong>');
        }
        
        // Bootstrap 4 dropdown closes automatically on click
    });
    
    // Handle individual question dropdown selection
    $(document).on('click', '.add-single-question', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const questionId = $(this).data('question-id');
        const sectionId = $(this).data('section-id');
        const sectionName = $(this).data('section-name');
        
        // Add single question with section
        addSingleQuestionWithSection(questionId, sectionId);
        
        // Bootstrap 5 dropdown closes automatically on click
    });
    <?php else: ?>
    // Add a single question to the test (no sections)
    $(document).on('click', '.add-question', function () {
        const questionId = $(this).data('question-id');
        addQuestionToTest(questionId);
    });
    <?php endif; ?>
    
    // Add selected questions to the test
    $('#addSelectedQuestions').on('click', function(e) {
        <?php if ($sections_enabled): ?>
        const dropdownElement = this;
        const dropdown = bootstrap.Dropdown.getInstance(dropdownElement) || new bootstrap.Dropdown(dropdownElement);
        const isOpen = $(this).next('.dropdown-menu').hasClass('show');
        
        // Check if we have both questions and section selected
        const selectedQuestions = [];
        $('.question-checkbox:checked').each(function() {
            selectedQuestions.push($(this).val());
        });
        
        // If dropdown is already open, let Bootstrap handle it (close it)
        if (isOpen) {
            // If we have both section and questions selected, add them
            if (selectedSectionForAdd && selectedQuestions.length > 0) {
                e.preventDefault();
                dropdown.hide();
                addMultipleQuestionsWithSection(selectedQuestions, selectedSectionForAdd);
                return;
            }
            // Otherwise, just let Bootstrap close the dropdown normally
            return;
        }
        
        // Dropdown is closed - open it
        // The button click will open the dropdown via Bootstrap's data-bs-toggle
        // But we can check if we should add immediately
        if (selectedSectionForAdd && selectedQuestions.length > 0) {
            // Both are ready, add them instead of opening dropdown
            e.preventDefault();
            addMultipleQuestionsWithSection(selectedQuestions, selectedSectionForAdd);
        } else {
            // Open dropdown to let user select section
            // The data-bs-toggle will handle this automatically
            if (selectedQuestions.length === 0) {
                showAlert('warning', '<i class="fas fa-info-circle"></i> Please select at least one question to add.');
            }
        }
        <?php else: ?>
        const selectedQuestions = [];
        $('.question-checkbox:checked').each(function() {
            selectedQuestions.push($(this).val());
        });
        
        if (selectedQuestions.length === 0) {
            showAlert('warning', '<i class="fas fa-info-circle"></i> Please select at least one question to add.');
            return;
        }
        
        addQuestionsToTest(selectedQuestions);
        <?php endif; ?>
    });
    
    // Remove question from test
    $('.remove-question').on('click', function() {
        questionToRemove = $(this).data('test-question-id');
        $('#removeQuestionModal').modal('show');
    });
    
    // Confirm question removal
    $('#confirmRemoveBtn').on('click', function() {
        if (questionToRemove) {
            removeQuestionFromTest(questionToRemove);
        }
    });
    
    // Save question order (both header and banner buttons, banner only exists for sectional tests)
    $('#saveOrderHeader').on('click', function() {
        saveQuestionOrder();
    });
    $('#saveOrderBanner').on('click', function() {
        saveQuestionOrder();
    });
    
    // Add single question with section (new function)
    function addSingleQuestionWithSection(questionId, sectionId) {
        $.ajax({
            url: '<?= base_url($url.'/test/add_question') ?>',
            type: 'POST',
            data: {
                test_id: testId,
                question_id: questionId,
                section_id: sectionId || null
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Store the specific question_id that was added (ensure it's a number)
                    // Accumulate with existing unsaved questions - don't replace them
                    if (sectionsEnabled) {
                        const existing = JSON.parse(localStorage.getItem('newlyAddedQuestionIds_' + testId) || '[]');
                        const qId = parseInt(questionId);
                        // Only add if not already in the list
                        if (existing.indexOf(qId) === -1) {
                            existing.push(qId);
                            localStorage.setItem('newlyAddedQuestionIds_' + testId, JSON.stringify(existing));
                            console.log('Added question ID:', qId, 'All unsaved questions:', existing);
                        } else {
                            console.log('Question ID already in unsaved list:', qId);
                        }
                    }
                    // Set flag to prevent beforeunload warning
                    isIntentionalReload = true;
                    location.reload();
                } else {
                    showAlert('danger', '<i class="fas fa-times-circle"></i> ' + (response.message || 'Error adding question to test'));
                }
            },
            error: function() {
                showAlert('danger', '<i class="fas fa-times-circle"></i> An error occurred while adding the question.');
            }
        });
    }
    
    // Add multiple questions with section (new function)
    function addMultipleQuestionsWithSection(questionIds, sectionId) {
        $.ajax({
            url: '<?= base_url($url.'/test/add_questions') ?>',
            type: 'POST',
            data: {
                test_id: testId,
                question_ids: questionIds,
                section_id: sectionId || null
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Store the specific question_ids that were added (ensure they're numbers)
                    // Accumulate with existing unsaved questions - don't replace them
                    if (sectionsEnabled) {
                        const existing = JSON.parse(localStorage.getItem('newlyAddedQuestionIds_' + testId) || '[]');
                        let addedCount = 0;
                        questionIds.forEach(function(qId) {
                            const id = parseInt(qId);
                            if (existing.indexOf(id) === -1) {
                                existing.push(id);
                                addedCount++;
                            }
                        });
                        if (addedCount > 0) {
                            localStorage.setItem('newlyAddedQuestionIds_' + testId, JSON.stringify(existing));
                            console.log('Added question IDs:', questionIds, 'All unsaved questions:', existing);
                        } else {
                            console.log('All question IDs already in unsaved list');
                        }
                    }
                    // Set flag to prevent beforeunload warning
                    isIntentionalReload = true;
                    location.reload();
                } else {
                    showAlert('danger', '<i class="fas fa-times-circle"></i> ' + (response.message || 'Error adding questions to test'));
                }
            },
            error: function() {
                showAlert('danger', '<i class="fas fa-times-circle"></i> An error occurred while adding questions.');
            }
        });
    }
    
    // Modify addQuestionToTest function (for non-section tests)
    function addQuestionToTest(questionId) {
        $.ajax({
            url: '<?= base_url($url.'/test/add_question') ?>',
            type: 'POST',
            data: {
                test_id: testId,
                question_id: questionId
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Set flag to prevent beforeunload warning
                    isIntentionalReload = true;
                    location.reload();
                } else {
                    showAlert('danger', '<i class="fas fa-times-circle"></i> ' + (response.message || 'Error adding question to test'));
                }
            },
            error: function() {
                showAlert('danger', '<i class="fas fa-times-circle"></i> An error occurred while adding the question.');
            }
        });
    }
    
    // Modify addQuestionsToTest function (for non-section tests)
    function addQuestionsToTest(questionIds) {
        $.ajax({
            url: '<?= base_url($url.'/test/add_questions') ?>',
            type: 'POST',
            data: {
                test_id: testId,
                question_ids: questionIds
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Set flag to prevent beforeunload warning
                    isIntentionalReload = true;
                    location.reload();
                } else {
                    showAlert('danger', '<i class="fas fa-times-circle"></i> ' + (response.message || 'Error adding questions to test'));
                }
            },
            error: function() {
                showAlert('danger', '<i class="fas fa-times-circle"></i> An error occurred while adding questions.');
            }
        });
    }
    
    // Modify removeQuestionFromTest function
    function removeQuestionFromTest(testQuestionId) {
        $.ajax({
            url: '<?= base_url($url.'/test/remove_question') ?>',
            type: 'POST',
            data: {
                test_question_id: testQuestionId
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#removeQuestionModal').modal('hide');
                    // Set flag to prevent beforeunload warning
                    isIntentionalReload = true;
                    location.reload();
                } else {
                    showAlert('danger', '<i class="fas fa-times-circle"></i> ' + (response.message || 'Error removing question from test'));
                }
            },
            error: function() {
                showAlert('danger', '<i class="fas fa-times-circle"></i> An error occurred while removing the question.');
            }
        });
    }
    
    // Modify saveQuestionOrder function
    function saveQuestionOrder() {
        const questionOrder = [];
        $('.question-row').each(function(index) {
            questionOrder.push({
                test_question_id: $(this).data('test-question-id'),
                order: index + 1
            });
        });
        
        // Show loading state
        $('#saveOrder').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');
        
        $.ajax({
            url: '<?= base_url($url.'/test/save_question_order') ?>',
            type: 'POST',
            data: {
                test_id: testId,
                question_order: JSON.stringify(questionOrder)
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Clear all unsaved markers
                    unsavedQuestionIds.clear();
                    updateUnsavedIndicator();
                    
                    // Clear localStorage flags
                    localStorage.removeItem('newlyAddedQuestionIds_' + testId);
                    localStorage.removeItem('questionsAddedAt_' + testId);
                    
                    const alertHtml = `
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <span class="alert-text"><i class="fas fa-check-circle me-2"></i>Question order saved successfully.</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    `;
                    
                    $('.alert').remove();
                    $('.card').first().prepend(alertHtml);
                    
                    setTimeout(function() {
                        $('.alert').alert('close');
                    }, 5000);
                    
                    // After saving order, sync with OneCompiler
                    // First call create_challenge to update the challenge
                    $.get('<?= base_url($url.'/test/create_challenge/'.($test->id ?? '')) ?>', function() {
                        console.log('Challenge synced with OneCompiler');
                        
                        // Then update sections if enabled
                        <?php if ($sections_enabled): ?>
                        updateChallengeSections();
                        <?php endif; ?>
                    });
                } else {
                    showAlert('danger', '<i class="fas fa-times-circle"></i> ' + (response.message || 'Error saving question order'));
                }
                
                // Re-enable button
                $('#saveOrder').prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Order');
            },
            error: function() {
                showAlert('danger', '<i class="fas fa-times-circle"></i> An error occurred while saving the question order.');
            }
        });
    }

    // Modify removeAllQuestions click handler
    $('#removeAllQuestions').on('click', function() {
        if (confirm('Are you sure you want to remove all questions from this test?')) {
            $.ajax({
                url: '<?= base_url($url.'/test/remove_all_questions') ?>',
                type: 'POST',
                data: {
                    test_id: testId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        location.reload();
                    } else {
                        showAlert('danger', '<i class="fas fa-times-circle"></i> ' + (response.message || 'Error removing questions'));
                    }
                },
                error: function() {
                    showAlert('danger', '<i class="fas fa-times-circle"></i> An error occurred while removing questions.');
                }
            });
        }
    });

    /**
     * Section Management Functions
     */
    
    // Handle section dropdown change for question assignment
    $(document).on('change', '.section-dropdown', function() {
        const testQuestionId = $(this).data('test-question-id');
        const sectionId = $(this).val();
        const $dropdown = $(this);
        const $row = $dropdown.closest('tr');

        if (sectionId) {
            // Assign to section
            $.ajax({
                url: '<?= base_url($url.'/test/assign_question_to_section') ?>',
                type: 'POST',
                data: {
                    test_question_id: testQuestionId,
                    section_id: sectionId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Update row data attribute
                        $row.attr('data-section-id', sectionId);
                        
                        // Show success message
                        showAlert('success', 'Question assigned to section successfully');
                    } else {
                        showAlert('danger', response.message || 'Error assigning question to section');
                        // Revert dropdown
                        $dropdown.val($row.attr('data-section-id') || '');
                    }
                },
                error: function() {
                    showAlert('danger', 'An error occurred while assigning the question.');
                    // Revert dropdown
                    $dropdown.val($row.attr('data-section-id') || '');
                }
            });
        } else {
            // Remove from section
            $.ajax({
                url: '<?= base_url($url.'/test/remove_question_from_section') ?>',
                type: 'POST',
                data: {
                    test_question_id: testQuestionId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Update row data attribute
                        $row.attr('data-section-id', '');
                        
                        // Show success message
                        showAlert('success', 'Question removed from section successfully');
                    } else {
                        showAlert('danger', response.message || 'Error removing question from section');
                        // Revert dropdown
                        $dropdown.val($row.attr('data-section-id') || '');
                    }
                },
                error: function() {
                    showAlert('danger', 'An error occurred while removing the question.');
                    // Revert dropdown
                    $dropdown.val($row.attr('data-section-id') || '');
                }
            });
        }
    });

    // Handle section filter
    $('#sectionFilter').on('change', function() {
        const filterValue = $(this).val();

        <?php if ($sections_enabled): ?>
        // For grouped view: Show/hide section groups
        $('.section-header-row').each(function() {
            const $headerRow = $(this);
            // Get all questions in this section (until next section header or end)
            const $sectionQuestions = $headerRow.nextUntil('.section-header-row', '.question-row');

            if (filterValue === '') {
                // Show all sections
                $headerRow.show();
                $sectionQuestions.show();
            } else {
                // Show specific section - check by section-id
                const firstQuestion = $sectionQuestions.first();
                if (firstQuestion.length && String(firstQuestion.attr('data-section-id')) === String(filterValue)) {
                    $headerRow.show();
                    $sectionQuestions.show();
                } else {
                    $headerRow.hide();
                    $sectionQuestions.hide();
                }
            }
        });
        <?php else: ?>
        // For non-grouped view: Show/hide individual question rows
        $('.question-row').each(function() {
            const $row = $(this);
            const sectionId = $row.attr('data-section-id');

            if (filterValue === '') {
                // Show all
                $row.show();
            } else {
                // Show specific section
                if (String(sectionId) === String(filterValue)) {
                    $row.show();
                } else {
                    $row.hide();
                }
            }
        });
        <?php endif; ?>

        // Update question numbers
        updateQuestionNumbers();
    });

    // Helper function to show alerts
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                <span class="alert-text">${message}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        $('.alert').remove();
        $('.card').first().prepend(alertHtml);
        
        setTimeout(function() {
            $('.alert').alert('close');
        }, 3000);
    }

    // Helper function to update challenge sections using UPDATE API
    function updateChallengeSections() {
        // Call the new update_challenge_with_sections method
        $.ajax({
            url: '<?= base_url($url.'/test/update_challenge_with_sections/'.($test->id ?? '')) ?>',
            type: 'GET',
            success: function() {
                console.log('Challenge sections updated in OneCompiler');
            },
            error: function() {
                console.log('Note: Challenge section update may need manual sync');
            }
        });
    }

    // Helper function to update left panel sections without page refresh
    function updateLeftPanelSections() {
        // For proper real-time updates, we need to reload the entire left panel
        // since sections are grouped server-side and questions need to be re-organized
        location.reload();
    }
    
    // Open Manage Sections Modal
    $('#manageSectionsBtn').on('click', function() {
        const testId = $(this).data('test-id');
        loadSections(testId);
        $('#manageSectionsModal').modal('show');
    });

    // Open Manage Sections Modal from "Create a section first" link
    $('#createSectionLink').on('click', function(e) {
        e.preventDefault();
        const testId = $('#manageSectionsBtn').data('test-id');
        loadSections(testId);
        $('#manageSectionsModal').modal('show');
    });

    // Load sections from server
    // Helper function to get display name for a section
    // Default section (id = 1) shows as "Unassigned Questions", others use section_name
    function getSectionDisplayName(section) {
        if (section.id == <?= DEFAULT_SECTION_ID ?>) {
            return 'Unassigned Questions';
        }
        return section.section_name || section.display_name || '';
    }

    function loadSections(testId) {
        $.ajax({
            url: '<?= base_url($url.'/test/get_sections') ?>/' + testId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    displaySections(response.sections);
                } else {
                    $('#sectionsList').html('<p class="text-danger">Error loading sections</p>');
                }
            },
            error: function() {
                $('#sectionsList').html('<p class="text-danger">Error loading sections</p>');
            }
        });
    }

    // Update all section dropdowns on the page (filter and assignment dropdowns)
    function updateSectionDropdowns(testId) {
        $.ajax({
            url: '<?= base_url($url.'/test/get_sections') ?>/' + testId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    const sections = response.sections;
                    // Filter out default section from dropdowns (id = 1)
                    const nonDefaultSections = sections.filter(section => section.id != <?= DEFAULT_SECTION_ID ?>);
                    
                    // 1. Update "Add Selected" dropdown menu
                    const addSelectedDropdown = $('#sectionDropdownForAdd');
                    if (addSelectedDropdown.length) {
                        addSelectedDropdown.empty();
                        if (nonDefaultSections.length === 0) {
                            addSelectedDropdown.append('<li><a class="dropdown-item disabled" href="#">No sections available</a></li>');
                        } else {
                            nonDefaultSections.forEach(function(section) {
                                const displayName = getSectionDisplayName(section);
                                addSelectedDropdown.append(`<li><a class="dropdown-item section-option" href="javascript:void(0);" data-section-id="${section.id}" data-section-name="${displayName}">
                                    <i class="fas fa-layer-group me-2"></i>${displayName}
                                </a></li>`);
                            });
                        }
                    }
                    
                    // 2. Update all individual question "+" dropdown menus
                    $('#availableQuestionsTable tbody .dropdown').each(function() {
                        const questionId = $(this).find('.question-add-dropdown').data('question-id');
                        const dropdownMenu = $(this).find('.dropdown-menu');
                        if (dropdownMenu.length && questionId) {
                            dropdownMenu.empty();
                            if (nonDefaultSections.length === 0) {
                                dropdownMenu.append('<li><a class="dropdown-item disabled" href="#">No sections available</a></li>');
                            } else {
                                nonDefaultSections.forEach(function(section) {
                                    const displayName = getSectionDisplayName(section);
                                    dropdownMenu.append(`<li><a class="dropdown-item add-single-question" href="javascript:void(0);" data-question-id="${questionId}" data-section-id="${section.id}" data-section-name="${displayName}">
                                        <i class="fas fa-layer-group me-2"></i>${displayName}
                                    </a></li>`);
                                });
                            }
                        }
                    });
                    
                    // 3. Update filter dropdown in Selected Questions panel
                    const filterDropdown = $('#sectionFilter');
                    if (filterDropdown.length) {
                        const currentFilterValue = filterDropdown.val();
                        filterDropdown.empty();
                        filterDropdown.append('<option value="">All Sections</option>');
                        nonDefaultSections.forEach(function(section) {
                            const displayName = getSectionDisplayName(section);
                            filterDropdown.append(`<option value="${section.id}">${displayName}</option>`);
                        });
                        filterDropdown.val(currentFilterValue); // Restore selection
                    }
                }
            },
            error: function() {
                console.log('Error updating section dropdowns');
            }
        });
    }

    // Display sections in the modal
    function displaySections(sections) {
        // Filter out default "Unassigned Questions" section (id = 1)
        const nonDefaultSections = sections.filter(function(section) {
            return section.id != <?= DEFAULT_SECTION_ID ?>;
        });

        if (nonDefaultSections.length === 0) {
            $('#sectionsList').html('<p class="text-muted">No sections created yet. Add one above!</p>');
            return;
        }

        let html = '<div class="list-group">';
        nonDefaultSections.forEach(function(section) {
            const displayName = getSectionDisplayName(section);
            html += `
                <div class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${displayName}</h6>
                            <small class="text-muted">${section.question_count} question(s)</small>
                        </div>
                        <div>
                            <button class="btn btn-sm btn-outline-primary edit-section" data-section-id="${section.id}" data-section-name="${section.section_name}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-section" data-section-id="${section.id}">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        
        $('#sectionsList').html(html);
    }

    // Add new section
    $('#addSectionBtn').on('click', function() {
        const sectionName = $('#newSectionName').val().trim();
        const testId = $('#manageSectionsBtn').data('test-id');

        if (!sectionName) {
            showAlert('warning', '<i class="fas fa-info-circle"></i> Please enter a section name.');
            return;
        }

        $.ajax({
            url: '<?= base_url($url.'/test/create_section') ?>',
            type: 'POST',
            data: {
                test_id: testId,
                section_name: sectionName
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#newSectionName').val('');
                    // Server won't return the new section until it's assigned to a question.
                    // Inject it into UI immediately (UI-only until refresh).
                    const newSection = {
                        id: response.section_id,
                        section_name: response.section_name || sectionName,
                        question_count: 0
                    };
                    const newSectionDisplayName = getSectionDisplayName(newSection);

                    // 1) Manage Sections modal - prepend item
                    if ($('#sectionsList').length) {
                        const item = `
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">${newSectionDisplayName}</h6>
                                        <small class="text-muted">0 question(s)</small>
                                    </div>
                                    <div>
                                        <button class="btn btn-sm btn-outline-primary edit-section" data-section-id="${newSection.id}" data-section-name="${newSection.section_name}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-section" data-section-id="${newSection.id}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>`;
                        const container = $('#sectionsList .list-group');
                        if (container.length) {
                            container.prepend(item);
                        } else {
                            $('#sectionsList').html('<div class="list-group">' + item + '</div>');
                        }
                    }

                    // 2) Add Selected dropdown - append option
                    const addSelectedDropdown = $('#sectionDropdownForAdd');
                    if (addSelectedDropdown.length) {
                        // Remove "No sections available" message if it exists
                        addSelectedDropdown.find('.dropdown-item.disabled').remove();
                        addSelectedDropdown.append(`
                            <li><a class="dropdown-item section-option" href="javascript:void(0);" data-section-id="${newSection.id}" data-section-name="${newSectionDisplayName}">
                                <i class="fas fa-layer-group me-2"></i>${newSectionDisplayName}
                            </a></li>`);
                    }

                    // 3) Individual "+" dropdowns - append to each
                    $('#availableQuestionsTable tbody .dropdown').each(function() {
                        const questionId = $(this).find('.question-add-dropdown').data('question-id');
                        const dropdownMenu = $(this).find('.dropdown-menu');
                        if (dropdownMenu.length && questionId) {
                            // Remove "No sections available" message if it exists
                            dropdownMenu.find('.dropdown-item.disabled').remove();
                            dropdownMenu.append(`
                                <li><a class="dropdown-item add-single-question" href="javascript:void(0);" data-section-id="${newSection.id}" data-section-name="${newSectionDisplayName}" data-question-id="${questionId}">
                                    <i class="fas fa-layer-group me-2"></i>${newSectionDisplayName}
                                </a></li>`);
                        }
                    });

                    // 4) Filter dropdown - append option (default is excluded elsewhere)
                    const filterDropdown = $('#sectionFilter');
                    if (filterDropdown.length) {
                        filterDropdown.append(`<option value="${newSection.id}">${newSectionDisplayName}</option>`);
                    }

                    showAlert('success', 'Section created successfully!');
                    // Modal stays open - user can continue adding more sections
                } else {
                    showAlert('danger', '<i class="fas fa-times-circle"></i> ' + (response.message || 'Error creating section'));
                }
            },
            error: function() {
                showAlert('danger', '<i class="fas fa-times-circle"></i> An error occurred while creating the section.');
            }
        });
    });

    // Edit section - Convert to inline editing
    $(document).on('click', '.edit-section', function() {
        const $listItem = $(this).closest('.list-group-item');
        const $nameElement = $listItem.find('h6');
        const $editBtn = $(this);
        const $deleteBtn = $listItem.find('.delete-section');
        
        // Check if already in edit mode
        if ($listItem.hasClass('editing-section')) {
            return; // Already editing, ignore
        }
        
        // Get current values
        const sectionId = $editBtn.data('section-id');
        const currentName = $nameElement.text().trim();
        const originalName = currentName;
        
        // Mark as editing
        $listItem.addClass('editing-section');
        
        // Replace h6 with input field
        const $input = $('<input>', {
            type: 'text',
            class: 'form-control form-control-sm section-edit-input',
            value: currentName,
            'data-original-name': originalName
        });
        $nameElement.replaceWith($input);
        
        // Change EDIT button to SAVE
        $editBtn.removeClass('edit-section').addClass('save-section');
        $editBtn.html('<i class="fas fa-save"></i> Save');
        $editBtn.data('section-id', sectionId);
        $editBtn.removeClass('btn-outline-primary').addClass('btn-outline-success');
        
        // Change DELETE button to CANCEL
        $deleteBtn.removeClass('delete-section').addClass('cancel-edit-section');
        $deleteBtn.html('<i class="fas fa-times"></i> Cancel');
        $deleteBtn.removeClass('btn-outline-danger').addClass('btn-outline-secondary');
        
        // Focus on input
        $input.focus().select();
    });
    
    // Save section - Update section name
    $(document).on('click', '.save-section', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $listItem = $(this).closest('.list-group-item');
        const $input = $listItem.find('.section-edit-input');
        const sectionId = $(this).data('section-id');
        const newName = $input.val().trim();
        const originalName = $input.data('original-name');
        
        // Validate
        if (!newName) {
            showAlert('warning', '<i class="fas fa-info-circle"></i> Section name cannot be empty.');
            $input.focus();
            return;
        }
        
        if (newName === originalName) {
            // No change, just cancel edit
            cancelSectionEdit($listItem, originalName);
            return;
        }
        
        // Disable buttons while saving
        const $saveBtn = $(this);
        const $cancelBtn = $listItem.find('.cancel-edit-section');
        $saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        $cancelBtn.prop('disabled', true);
        
        $.ajax({
            url: '<?= base_url($url.'/test/update_section') ?>',
            type: 'POST',
            data: {
                section_id: sectionId,
                section_name: newName
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Revert to normal view with new name
                    revertSectionEdit($listItem, newName, sectionId);
                    
                    const testId = $('#manageSectionsBtn').data('test-id');
                    
                    // Reload sections list (this should not close the modal)
                    loadSections(testId);
                    updateSectionDropdowns(testId); // Update all section dropdowns
                    // Update the left panel without closing modal
                    updateLeftPanelSections();
                    showAlert('success', 'Section updated successfully!');
                    
                    // The modal should remain open - no need to force show
                } else {
                    $saveBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Save');
                    $cancelBtn.prop('disabled', false);
                    showAlert('danger', '<i class="fas fa-times-circle"></i> ' + (response.message || 'Error updating section'));
                }
            },
            error: function() {
                $saveBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Save');
                $cancelBtn.prop('disabled', false);
                showAlert('danger', '<i class="fas fa-times-circle"></i> An error occurred while updating the section.');
            }
        });
    });
    
    // Cancel edit - Revert changes
    $(document).on('click', '.cancel-edit-section', function() {
        const $listItem = $(this).closest('.list-group-item');
        const $input = $listItem.find('.section-edit-input');
        const originalName = $input.data('original-name');
        const sectionId = $listItem.find('.save-section').data('section-id') || $listItem.find('.edit-section').data('section-id');
        
        cancelSectionEdit($listItem, originalName, sectionId);
    });
    
    // Helper function to cancel edit and revert to normal view
    function cancelSectionEdit($listItem, sectionName, sectionId) {
        const $input = $listItem.find('.section-edit-input');
        const $saveBtn = $listItem.find('.save-section');
        const $cancelBtn = $listItem.find('.cancel-edit-section');
        
        // If elements don't exist, section might have been reloaded, just remove class
        if ($input.length === 0 || $saveBtn.length === 0 || $cancelBtn.length === 0) {
            $listItem.removeClass('editing-section');
            return;
        }
        
        // Replace input with h6
        const $h6 = $('<h6>', {
            class: 'mb-1',
            text: sectionName
        });
        $input.replaceWith($h6);
        
        // Revert SAVE button to EDIT
        $saveBtn.removeClass('save-section').addClass('edit-section');
        $saveBtn.html('<i class="fas fa-edit"></i> Edit');
        $saveBtn.removeClass('btn-outline-success').addClass('btn-outline-primary');
        $saveBtn.prop('disabled', false);
        if (sectionId) {
            $saveBtn.data('section-id', sectionId);
            $saveBtn.data('section-name', sectionName);
        }
        
        // Revert CANCEL button to DELETE
        $cancelBtn.removeClass('cancel-edit-section').addClass('delete-section');
        $cancelBtn.html('<i class="fas fa-trash"></i> Delete');
        $cancelBtn.removeClass('btn-outline-secondary').addClass('btn-outline-danger');
        $cancelBtn.prop('disabled', false);
        if (sectionId) {
            $cancelBtn.data('section-id', sectionId);
        }
        
        // Remove editing class
        $listItem.removeClass('editing-section');
    }
    
    // Helper function to revert after successful save
    function revertSectionEdit($listItem, sectionName, sectionId) {
        const $input = $listItem.find('.section-edit-input');
        const $saveBtn = $listItem.find('.save-section');
        const $cancelBtn = $listItem.find('.cancel-edit-section');
        
        // Replace input with h6
        const $h6 = $('<h6>', {
            class: 'mb-1',
            text: sectionName
        });
        $input.replaceWith($h6);
        
        // Revert SAVE button to EDIT
        $saveBtn.removeClass('save-section').addClass('edit-section');
        $saveBtn.html('<i class="fas fa-edit"></i> Edit');
        $saveBtn.removeClass('btn-outline-success').addClass('btn-outline-primary');
        $saveBtn.data('section-id', sectionId);
        $saveBtn.data('section-name', sectionName);
        $saveBtn.prop('disabled', false);
        
        // Revert CANCEL button to DELETE
        $cancelBtn.removeClass('cancel-edit-section').addClass('delete-section');
        $cancelBtn.html('<i class="fas fa-trash"></i> Delete');
        $cancelBtn.removeClass('btn-outline-secondary').addClass('btn-outline-danger');
        $cancelBtn.data('section-id', sectionId);
        $cancelBtn.prop('disabled', false);
        
        // Remove editing class
        $listItem.removeClass('editing-section');
    }
    
    // Allow Enter key to save and Escape key to cancel
    $(document).on('keydown', '.section-edit-input', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $(this).closest('.list-group-item').find('.save-section').click();
        } else if (e.key === 'Escape') {
            e.preventDefault();
            $(this).closest('.list-group-item').find('.cancel-edit-section').click();
        }
    });

    // Delete section - Show confirmation modal
    $(document).on('click', '.delete-section', function() {
        // Don't allow delete if in edit mode
        const $listItem = $(this).closest('.list-group-item');
        if ($listItem.hasClass('editing-section')) {
            return;
        }
        
        const sectionId = $(this).data('section-id');
        const sectionName = $listItem.find('h6').text().trim();

        // Set section name in modal
        $('#sectionNameToDelete').text(sectionName);
        $('#confirmDeleteSectionBtn').data('section-id', sectionId);

        // Show confirmation modal
        $('#deleteSectionModal').modal('show');
    });

    // Confirm section deletion
    $('#confirmDeleteSectionBtn').on('click', function() {
        const sectionId = $(this).data('section-id');

        $.ajax({
            url: '<?= base_url($url.'/test/delete_section') ?>',
            type: 'POST',
            data: {
                section_id: sectionId
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#deleteSectionModal').modal('hide');
                    const testId = $('#manageSectionsBtn').data('test-id');
                    loadSections(testId);
                    updateSectionDropdowns(testId); // Update all section dropdowns
                    // Update the left panel without closing modal
                    updateLeftPanelSections();
                    showAlert('success', 'Section deleted successfully!');
                    // Modal stays open - user can continue managing sections
                } else {
                    showAlert('danger', '<i class="fas fa-times-circle"></i> ' + (response.message || 'Error deleting section'));
                }
            },
            error: function() {
                showAlert('danger', '<i class="fas fa-times-circle"></i> An error occurred while deleting the section.');
            }
        });
    });
});
</script>

