<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <a 
                            href="<?php echo $back_url; ?>"
                                class="btn btn-outline-primary back-btn">
                                <i class="fas fa-arrow-left"></i> Back to Modules
                            </a>
                        </div>
                        <h4 class="card-title mb-0">
                            Test Questions - <?php echo $test['title']; ?>
                        </h4>
                    </div>
                    <?php if (!empty($test['instructions'])): ?>
                        <div class="test-instructions mt-3">
                            <p class="text-muted"><?php echo $test['instructions']; ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php
                        $sections_enabled = $sections_enabled ?? false;
                        $questions_by_section = $questions_by_section ?? [];
                        $section_map = $section_map ?? [];
                        $section_order = $section_order ?? [];

                        $render_question_card = function($question, $section_color = null) {
                            $question_number = isset($question['question_order']) ? $question['question_order'] : ($question['display_index'] ?? '');
                            $difficulty_label = isset($question['difficulty_level']) ? $question['difficulty_level'] : 'N/A';
                            $question_type = isset($question['question_type']) ? $question['question_type'] : 'N/A';
                            $question_sub_type = isset($question['question_sub_type']) ? $question['question_sub_type'] : '';
                            $score_value = isset($question['score']) ? $question['score'] : '0';
                            $card_style = $section_color ? ' style="border-left: 4px solid ' . htmlspecialchars($section_color, ENT_QUOTES, 'UTF-8') . '; border-top-left-radius: 0; border-bottom-left-radius: 0;"' : '';
                            ?>
                            <div class="question-card"<?php echo $card_style; ?>>
                                <div class="question-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5>Question <?php echo $question_number; ?></h5>
                                        <div>
                                            <span class="badge badge-difficulty">
                                                <i class="fas fa-signal-alt"></i> 
                                                <?php echo htmlspecialchars($difficulty_label); ?>
                                            </span>
                                            <span class="badge badge-type">
                                                <i class="fas fa-puzzle-piece"></i> 
                                                <?php echo htmlspecialchars($question_type); ?>
                                                <?php echo !empty($question_sub_type) ? ' - ' . htmlspecialchars($question_sub_type) : ''; ?>
                                            </span>
                                            <span class="badge badge-marks">
                                                <i class="fas fa-star-half-alt"></i> 
                                                <?php echo htmlspecialchars($score_value); ?> Score
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="question-content">
                                    <div class="mb-4">
                                        <h5 class="text-primary"><?php echo !empty($question['question_title']) ? $question['question_title'] : 'Question ' . $question_number; ?></h5>
                                        <div class="markdown-content">
                                            <?php echo $question['question_content']; ?>
                                        </div>
                                    </div>
                                    
                                    <?php if (isset($question['type']) && $question['type'] == 1 && !empty($question['options'])): ?>
                                        <div class="options-list mt-3">
                                            <div class="section-header">
                                                <i class="fas fa-list-ul text-primary"></i>
                                                <strong class="ms-2">Options:</strong>
                                            </div>
                                            <div class="mt-2">
                                                <?php foreach ($question['options'] as $option): ?>
                                                    <div class="option-item <?php echo !empty($option['is_correct']) ? 'correct-option' : ''; ?>">
                                                        <span class="option-text"><?php echo $option['option_text']; ?></span>
                                                        <?php if (!empty($option['is_correct'])): ?>
                                                            <span class="badge badge-correct">
                                                                <i class="fas fa-check-circle"></i> Correct
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (isset($question['type']) && $question['type'] == 2 && !empty($question['test_cases'])): ?>
                                        <div class="mb-3">
                                            <div class="section-header">
                                                <i class="fas fa-vial text-primary"></i>
                                                <strong class="ms-2">Test Cases:</strong>
                                            </div>
                                            <div class="test-cases-list mt-2">
                                                <?php foreach ($question['test_cases'] as $testCase): ?>
                                                    <div class="test-case-item">
                                                        <div class="row">
                                                            <div class="col-md-5">
                                                                <div class="input-label">
                                                                    <i class="fas fa-arrow-right text-success"></i>
                                                                    <strong class="ms-2">Input:</strong>
                                                                </div>
                                                                <pre class="bg-white p-2 mt-1"><?php echo $testCase['input']; ?></pre>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div class="output-label">
                                                                    <i class="fas fa-arrow-left text-primary"></i>
                                                                    <strong class="ms-2">Output:</strong>
                                                                </div>
                                                                <pre class="bg-white p-2 mt-1"><?php echo $testCase['output']; ?></pre>
                                                            </div>
                                                            <div class="col-md-2">
                                                                <span class="badge <?php echo isset($testCase['visibility']) && $testCase['visibility'] == "1" ? 'badge-visible' : 'badge-hidden'; ?>">
                                                                    <i class="fas <?php echo isset($testCase['visibility']) && $testCase['visibility'] == "1" ? 'fa-eye' : 'fa-eye-slash'; ?>"></i>
                                                                    <?php echo isset($testCase['visibility']) && $testCase['visibility'] == "1" ? 'Visible' : 'Hidden'; ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($question['explanation'])): ?>
                                        <div class="explanation-section">
                                            <div class="explanation-header">
                                                <i class="fas fa-lightbulb text-warning"></i>
                                                <strong class="ms-2">Explanation:</strong>
                                            </div>
                                            <div class="explanation-content markdown-content mt-2">
                                                <?php echo $question['explanation']; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($question['tags'])): ?>
                                        <div class="topics-section">
                                            <div class="topics-header">
                                                <i class="fas fa-hashtag text-primary"></i>
                                                <strong class="ms-2">Topics:</strong>
                                            </div>
                                            <div class="tag-chips mt-2">
                                                <?php 
                                                $tagsList = explode(',', $question['tags']);
                                                foreach ($tagsList as $tag):
                                                    if (!empty(trim($tag))):
                                                ?>
                                                    <span class="tag-chip">
                                                        <i class="fas fa-tag"></i>
                                                        <?php echo trim($tag); ?>
                                                    </span>
                                                <?php 
                                                    endif;
                                                endforeach; 
                                                ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php
                        };
                    ?>

                    <?php if (empty($questions)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-info-circle fa-2x mb-3"></i>
                            <h5>No questions found for this test</h5>
                            <p>Questions will appear here once they are added to the test.</p>
                        </div>
                    <?php else: ?>
                        <?php if (!empty($sections_enabled) && !empty($questions_by_section)): ?>
                            <?php
                                $section_colors = [
                                    '#674eeb',
                                    '#28a745',
                                    '#dc3545',
                                    '#ffc107',
                                    '#17a2b8',
                                    '#6f42c1',
                                    '#e83e8c',
                                    '#fd7e14',
                                ];
                                $ordered_sections = !empty($section_order) ? $section_order : array_keys($questions_by_section);
                                $section_index = 0;
                            ?>
                            <?php foreach ($ordered_sections as $section_key): ?>
                                <?php
                                    if (!isset($questions_by_section[$section_key])) {
                                        continue;
                                    }
                                    $section_questions = $questions_by_section[$section_key];
                                    if ($section_key === 'unassigned' || (is_numeric($section_key) && (int)$section_key === (defined('DEFAULT_SECTION_ID') ? (int)DEFAULT_SECTION_ID : 1))) {
                                        foreach ($section_questions as $section_question) {
                                            $render_question_card($section_question);
                                        }
                                        continue;
                                    }
                                    $section_color = $section_colors[$section_index % count($section_colors)];
                                    $section_index++;
                                    $section_name = isset($section_map[$section_key])
                                        ? $section_map[$section_key]
                                        : (is_numeric($section_key) ? 'Section ' . $section_key : 'Unassigned Questions');
                                ?>
                                <div class="section-header-row" style="--section-color: <?php echo htmlspecialchars($section_color, ENT_QUOTES, 'UTF-8'); ?>;">
                                    <div class="d-flex align-items-center py-3 px-3">
                                        <i class="fas fa-layer-group me-2" style="color: <?php echo htmlspecialchars($section_color, ENT_QUOTES, 'UTF-8'); ?>;"></i>
                                        <strong class="section-title">
                                            <?php echo htmlspecialchars($section_name); ?>
                                        </strong>
                                        <span class="badge bg-secondary section-badge">
                                            <?php echo count($section_questions); ?> question(s)
                                        </span>
                                    </div>
                                </div>
                                <?php foreach ($section_questions as $section_question): ?>
                                    <?php $render_question_card($section_question, $section_color); ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php foreach ($questions as $question): ?>
                                <?php $render_question_card($question); ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .question-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        margin-bottom: 24px;
        padding: 32px;
        transition: all 0.2s ease;
        border: 1px solid #f0f0f0;
    }
    .question-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .question-header {
        border-bottom: 1px solid #f0f0f0;
        margin-bottom: 20px;
        padding-bottom: 16px;
    }

    .section-header-row {
        background: #f8f9ff;
        border-radius: 12px;
        margin-bottom: 16px;
        border-left: 4px solid var(--section-color, #674eeb);
        border-right: 1px solid #e0e5ff;
        border-top: 1px solid #e0e5ff;
        border-bottom: 1px solid #e0e5ff;
    }

    .section-header-row .section-title {
        color: var(--section-color, #674eeb);
        font-size: 1.1em;
    }

    .section-header-row .section-badge {
        background: linear-gradient(135deg, var(--section-color, #674eeb) 0%, rgba(103, 78, 235, 0.85) 100%) !important;
        color: #fff;
        margin-left: 12px;
    }
    
    .badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.8em;
        margin-right: 8px;
        font-weight: 400;
    }
    
    .badge-difficulty {
        background-color: #fafafa;
        color: #666;
        border: 1px solid #f0f0f0;
    }
    
    .badge-type {
        background-color: #f0f7ff;
        color: #4a6cf7;
    }
    
    .badge-marks {
        background-color: #f0fff4;
        color: #28a745;
    }
    
    .badge-correct {
        background-color: #f0fff4;
        color: #28a745;
    }
    
    .badge-visible {
        background-color: #f0f7ff;
        color: #4a6cf7;
    }
    
    .badge-hidden {
        background-color: #fafafa;
        color: #666;
    }
    
    .tag-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .tag-chip {
        background-color: #f0f7ff;
        color: #4a6cf7;
        border-radius: 4px;
        padding: 4px 12px;
        font-size: 0.8rem;
        display: inline-block;
        white-space: nowrap;
        transition: all 0.2s ease;
    }
    
    .tag-chip:hover {
        background-color: #e6f0ff;
    }
    
    .test-cases-list pre {
        white-space: pre-wrap;
        word-wrap: break-word;
        margin-bottom: 0;
        background-color: #fafafa;
        border: 1px solid #f0f0f0;
        border-radius: 4px;
    }
    
    .test-case-item {
        background-color: #fafafa;
        border-radius: 4px;
        padding: 1rem;
        margin-bottom: 1rem;
        border: 1px solid #f0f0f0;
    }
    
    .test-case-item pre {
        background-color: white;
        padding: 0.8rem;
        border-radius: 4px;
        margin-top: 0.5rem;
        border: 1px solid #f0f0f0;
    }
    
    .test-instructions {
        font-size: 0.9rem;
        color: #666;
        margin-top: 1rem;
    }
    
    .explanation-section {
        margin-top: 1.2rem;
        padding-top: 1rem;
        border-top: 1px solid #f0f0f0;
    }
    
    .explanation-header {
        color: #4a6cf7;
        margin-bottom: 0.5rem;
        font-size: 1rem;
    }
    
    .explanation-content {
        color: #333;
        line-height: 1.7;
    }
    
    .topics-section {
        margin-top: 1.2rem;
        padding-top: 1rem;
        border-top: 1px solid #f0f0f0;
    }
    
    .topics-header {
        color: #4a6cf7;
        margin-bottom: 0.5rem;
        font-size: 1rem;
    }
    
    .card {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border-radius: 12px;
    }
    
    .card-header {
        background-color: white;
        border-bottom: 1px solid #f0f0f0;
        padding: 1.5rem 2rem;
    }
    
    .card-title {
        color: #222;
        font-weight: 500;
        margin-bottom: 0;
    }
    
    .card-body {
        padding: 2rem;
    }
    
    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 0.8rem;
        border-radius: 4px;
        font-weight: 400;
        transition: all 0.2s ease;
        color: #4a6cf7;
        border: 1px solid #4a6cf7;
    }
    
    .back-btn:hover {
        background-color: #4a6cf7;
        color: white;
        transform: translateX(-2px);
    }
    
    .back-btn i {
        font-size: 0.9em;
    }
    
    .question-text {
        margin-bottom: 2rem;
        padding: 1.5rem;
        background-color: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    
    .options-list .option-item {
        padding: 0.8rem;
        border-radius: 4px;
        margin-bottom: 0.5rem;
        background-color: #fafafa;
        transition: all 0.2s ease;
        border: 1px solid #f0f0f0;
    }
    
    .options-list .option-item:hover {
        background-color: #f5f5f5;
    }
    
    .options-list .correct-option {
        background-color: #f0fff4;
        border-left: 3px solid #28a745;
    }
    
    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
        color: #4a6cf7;
    }
    
    .input-label, .output-label {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    
    .badge i {
        margin-right: 4px;
    }
    
    .tag-chip i {
        font-size: 0.8em;
        margin-right: 4px;
        opacity: 0.8;
    }
    
    .explanation-header i,
    .topics-header i {
        font-size: 1.1em;
    }
    
    .badge-difficulty i {
        color: #666;
    }
    
    .badge-type i {
        color: #4a6cf7;
    }
    
    .badge-marks i {
        color: #28a745;
    }
    
    .badge-correct i {
        color: #28a745;
    }
    
    .badge-visible i {
        color: #4a6cf7;
    }
    
    .badge-hidden i {
        color: #666;
    }

    /* Markdown Content Styling */
    .markdown-content {
        line-height: 1.6;
        color: #333;
        font-size: 1.1rem;
        padding: 1.5rem;
        background-color: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        margin-top: 1rem;
        word-wrap: break-word;
        overflow-wrap: break-word;
        max-width: 100%;
    }

    .markdown-content h1 { font-size: 2rem; }
    .markdown-content h2 { font-size: 1.75rem; }
    .markdown-content h3 { font-size: 1.5rem; }
    .markdown-content h4 { font-size: 1.25rem; }
    .markdown-content h5 { font-size: 1.15rem; }
    .markdown-content h6 { font-size: 1.1rem; }

    .markdown-content p {
        margin-bottom: 1em;
        font-size: 1.1rem;
    }

    .markdown-content ul,
    .markdown-content ol {
        margin-bottom: 1em;
        padding-left: 1.5em;
        font-size: 1.1rem;
    }

    .markdown-content li {
        margin-bottom: 0.5em;
    }

    .markdown-content blockquote {
        margin: 1em 0;
        padding: 1em 1.25em;
        border-left: 4px solid #4a6cf7;
        background-color: #f8f9fa;
        color: #666;
        font-size: 1.1rem;
    }

    .markdown-content pre {
        background-color: #f8f9fa;
        padding: 1.25rem;
        border-radius: 4px;
        margin: 1em 0;
        border: 1px solid #e2e8f0;
        font-size: 1rem;
        white-space: pre-wrap;
        word-wrap: break-word;
        overflow-x: auto;
    }

    .markdown-content code {
        background-color: #f8f9fa;
        padding: 0.2rem 0.4rem;
        border-radius: 3px;
        font-family: 'SFMono-Regular', Consolas, 'Liberation Mono', Menlo, monospace;
        font-size: 1rem;
    }

    .markdown-content table {
        width: 100%;
        margin: 1em 0;
        border-collapse: collapse;
        font-size: 1.1rem;
        overflow-x: auto;
        display: block;
    }

    .markdown-content table th,
    .markdown-content table td {
        padding: 0.75em;
        border: 1px solid #e2e8f0;
        min-width: 100px;
    }

    .markdown-content table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .markdown-content img {
        max-width: 100%;
        height: auto;
        border-radius: 4px;
        margin: 1em auto;
        display: block;
    }

    .markdown-content p img {
        margin: 1em auto;
    }

    .markdown-content hr {
        margin: 1.5em 0;
        border: none;
        border-top: 1px solid #e2e8f0;
    }
</style>

<!-- Include marked.js for markdown parsing -->
<script src="<?= base_url("/") ?>assets/packages/highlight.min.js"></script>
<script src="<?= base_url("/") ?>assets/packages/marked.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configure marked.js
        marked.setOptions({
            highlight: function(code, lang) {
                if (lang && hljs.getLanguage(lang)) {
                    return hljs.highlight(code, { language: lang }).value;
                }
                return hljs.highlightAuto(code).value;
            },
            breaks: true,
            gfm: true,
            headerIds: true,
            mangle: false,
            pedantic: false,
            smartLists: true,
            smartypants: true
        });

        // Parse all markdown content
        document.querySelectorAll('.markdown-content').forEach(function(element) {
            let content = element.innerHTML.trim();
            
            // Check if content contains markdown elements
            const hasMarkdown = content.includes('```') || 
                              content.includes('~~~') || 
                              content.includes('![') || 
                              content.includes('#') ||
                              content.includes('*') ||
                              content.includes('_') ||
                              content.includes('>') ||
                              content.includes('- ') ||
                              content.includes('1. ');

            if (hasMarkdown) {
                // For content with markdown elements, use marked
                element.innerHTML = marked.parse(content);
            } else {
                // For simple text, preserve line breaks and escape HTML
                element.innerHTML = content
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/\n/g, '<br>');
            }
        });

        // Apply syntax highlighting to code blocks
        if (typeof hljs !== 'undefined') {
            document.querySelectorAll('pre code').forEach((block) => {
                hljs.highlightElement(block);
            });
        }

        // Ensure images are properly sized and responsive
        document.querySelectorAll('.markdown-content img').forEach(img => {
            img.style.maxWidth = '100%';
            img.style.height = 'auto';
            img.style.display = 'block';
            img.style.margin = '1em auto';
        });
    });
</script>

