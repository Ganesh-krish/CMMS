<!-- View file: application/views/course/student_test_report.php -->
<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<div class="layout-content">
    <div class="container-fluid flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="font-weight-bold mb-5">Student Overall Test Report</h4>
                <div class="text-muted small mt-0 d-block breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="#"><i class="feather icon-home"></i></a></li>
                        <!-- <li class="breadcrumb-item"><a href="<?= base_url($url.'/'.$route) ?>">Courses</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url($url.'/'.$route.'/'.'modules/'.$course['id']) ?>">Modules</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url($url.'/'.$route.'/'.'module_tests/'.$course['id'].'/'.$module['id']) ?>">Module Tests</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url($url.'/'.$route.'/'.'test_results/'.$course['id'].'/'.$module['id'].'/'.$test['id']) ?>">Test Results</a></li> -->
                        <li class="breadcrumb-item active">Student Overall Report</li>
                    </ol>
                </div>
            </div>
            <div class="export-buttons">
                <!-- <a href="<?= base_url($url.'/course/export_test_report/'.$course['id'].'/'.$module['id'].'/'.$test['id'].'/'.$student['id']) ?>" class="btn btn-primary mr-2">
                    <i class="fas fa-file-export"></i> Export HTML Report
                </a> -->
                <!-- <a href="<?= base_url($url.'/course/export_test_report_csv/'.$course['id'].'/'.$module['id'].'/'.$test['id'].'/'.$student['id']) ?>" class="btn btn-success">
                    <i class="fas fa-file-csv"></i> Export CSV Report
                </a> -->
            </div>
        </div>

        <!-- Student Overview Card -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-9">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary text-white rounded p-3 mr-4">
                                <i class="feather icon-user" style="font-size: 24px;"></i>
                            </div>
                            <div>
                                <h5 class="mb-1"><?= $student['name'] ?></h5>
                                <div class="text-muted ">
                                    <?= $student['email'] ?> | Department: <?= $student['department_name'] ?>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                </div>
            </div>
        </div>
<!-- Student Overview Card ends-->
 <!-- Submission Overview Card Starts -->
<div class="card">
    <div class="card-header">
        <h5 class="card-header-title mb-0">Submission Details</h5>
    </div>
    <div class="card-body ">
        <?php if (!empty($submission)): ?>
            <?php foreach ($submission as $index => $sub): ?>
                <div class="submission-card mb-4 p-3 submission-details">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <!-- <strong>Status:</strong> -->
                            <h6 class="badge badge-secondary">#<?= $index + 1 ?></h6>
                            <h6 class="badge badge-danger"><?= date('M d, Y h:i A', strtotime($sub['submission_time'])) ?></h6>
                        </div>
                        <div class="col-md-6 text-right">
                            <!-- <h6 class="badge badge-info"> <?= htmlspecialchars($sub['course_name']) ?></h6> -->
                             <!-- <h6 class="badge badge-info"> <?= htmlspecialchars($sub['module_name']) ?></h6> -->
                            <a href="<?= htmlspecialchars($sub['detail_url']) ?>" 
                               class="btn btn-primary btn-sm">
                                View Full Summary
                            </a>
                        </div>
                    </div>    
                    
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <strong>Course:</strong> <?= htmlspecialchars($sub['course_name']) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Module:</strong> <?= htmlspecialchars($sub['module_name']) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Test:</strong> <?= htmlspecialchars($sub['test_title']) ?>
                        </div>
                    </div>

                    <div class="row mb-2">
                        <div class="col-md-4">
                            <strong>Total Score:</strong> <?= htmlspecialchars($sub['total_score']) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Earned Score:</strong> <?= htmlspecialchars($sub['earned_score']) ?>
                        </div>
                        <div class="col-md-4">
                            <strong>Percentage:</strong> <?= htmlspecialchars($sub['percentage']) ?>%
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">No submissions found.</div>
        <?php endif; ?>
    </div>
</div>

       <!--  -->

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
    
    .badge {
        padding: 0.5em 0.85em;
        font-size: 0.85rem;
        font-weight: 500;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 80px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .badge-success {
        background-color: #f0fff4;
        color: #38a169;
        border: 1px solid #c6f6d5;
    }
    
    .badge-danger {
        background-color: #fff5f5;
        color: #e53e3e;
        border: 1px solid #fed7d7;
    }
    
    .badge-secondary {
        background-color: #f7fafc;
        color: #4a5568;
        border: 1px solid #e2e8f0;
    }
    
    .badge-info {
        background-color: #f0f7ff;
        color: #4a6cf7;
        border: 1px solid #e2e8f0;
    }
    
    .badge-warning {
        background-color: #fff8e6;
        color: #ffa800;
        border: 1px solid #feebc8;
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

    /* Submission Details Styling */
    .submission-details {
        background-color: #f8fafc;
        border-radius: 8px;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
    }

    .detail-item {
        display: flex;
        align-items: center;
        font-size: 0.95rem;
    }

    .detail-item .text-muted {
        min-width: 120px;
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

    /* Code Block Styling */
    pre {
        /* background-color: #1e1e1e !important; */
        /* border-radius: 6px !important;
        padding: 1rem !important;
        margin: 1rem 0 !important;
        border: 0.2px solid #2d2d2d !important; */
    }

    pre code {
        color:rgb(0, 0, 0) !important;
        font-family: 'Fira Code', 'Consolas', monospace !important;
        font-size: 0.9rem !important;
        line-height: 1.5 !important;
    }

    /* Card Styling */
    .card {
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        border-radius: 12px;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid #f0f0f0;
        padding: 1.25rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Alert Styling */
    .alert {
        border: none;
        border-radius: 8px;
        padding: 1rem 1.25rem;
    }

    .alert-warning {
        background-color: #fff8e6;
        color: #ffa800;
    }

    .alert-info {
        background-color: #f0f7ff;
        color: #4a6cf7;
    }

    /* Test Cases Styling */
    .test-cases-container {
        margin-top: 1rem;
    }

    .test-case-card {
        background: #fff;
        border: 1px solid #edf2f7;
        border-radius: 8px;
        overflow: hidden;
        transition: all 0.2s ease;
        margin-bottom: 1.5rem;
    }

    .test-case-card:hover {
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }

    .test-case-header {
        background-color: #f8fafc;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .test-case-number {
        font-weight: 500;
        color: #4a5568;
    }

    .test-case-content {
        padding: 1rem;
    }

    .test-case-section {
        background: #f8fafc;
        border-radius: 6px;
        padding: 1rem;
        height: 100%;
    }

    .section-header {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
        color: #4a5568;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .section-header i {
        margin-right: 0.5rem;
        font-size: 0.9rem;
    }

    .test-case-code {
        background-color: #1e1e1e;
        color: rgb(255, 255, 255);
        padding: 0.75rem;
        border-radius: 4px;
        margin: 0;
        font-family: 'Fira Code', 'Consolas', monospace;
        font-size: 0.9rem;
        line-height: 1.5;
        overflow-x: auto;
        border: 1px solid #2d2d2d;
    }

    .error-section {
        background-color: #2d1e1e;
        border: 1px solid #3d2e2e;
    }

    .error-message {
        color: #ff6b6b;
        background-color: #2d1e1e;
        border-color: #3d2e2e;
    }

    /* MCQ Options Styling */
    .mcq-options {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .mcq-option {
        position: relative;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        transition: all 0.2s ease;
        margin-bottom: 0.5rem;
    }

    .mcq-option:last-child {
        margin-bottom: 0;
    }

    .mcq-option:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .correct-option {
        background: #f0fff4;
        border-color: #c6f6d5;
    }

    .correct-option:hover {
        background: #e6ffed;
        border-color: #9ae6b4;
    }

    .option-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .option-content .badge {
        flex-shrink: 0;
    }

    /* Add styles for selected options */
    .selected-option {
        background: #f0f7ff;
        border-color: #c6f6d5;
    }

    .selected-option:hover {
        background: #e6ffed;
        border-color: #9ae6b4;
    }

    .selected-option .badge-info {
        background-color: #f0f7ff;
        color: #4a6cf7;
        border: 1px solid #e2e8f0;
    }

    /* Text muted style */
    .text-muted {
        color: #718096;
        /* font-style: italic; */
    }

    /* Update test case badges */
    .test-case-header .badge {
        min-width: 90px;
        font-size: 0.8rem;
        padding: 0.4em 0.8em;
    }

    /* Update result badges */
    .badge-pill {
        padding: 0.6em 1.2em;
        font-size: 0.9rem;
        border-radius: 50px;
    }

    /* Responsive adjustments for badges */
    @media (max-width: 768px) {
        .badge {
            padding: 0.4em 0.7em;
            font-size: 0.8rem;
            min-width: 70px;
        }
        
        .option-content {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
        
        .option-content .badge {
            align-self: flex-start;
        }
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .test-case-content {
            padding: 0.75rem;
        }
        
        .test-case-section {
            margin-bottom: 1rem;
        }
        
        .test-case-section:last-child {
            margin-bottom: 0;
        }
    }

    /* Code File Styling */
    .code-file {
        background: #1e1e1e;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .code-file-header {
        background: #2d2d2d;
        padding: 0.75rem 1rem;
        color: #d4d4d4;
        font-size: 0.9rem;
        border-bottom: 1px solid #3d3d3d;
        display: flex;
        align-items: center;
    }

    .code-file-header i {
        color: #4a6cf7;
        margin-right: 0.5rem;
    }

    .code-content {
        margin: 0;
        padding: 1rem;
        background: #1e1e1e;
        border: none;
        border-radius: 0;
    }

    .code-content code {
        font-family: 'Fira Code', 'Consolas', monospace;
        font-size: 0.9rem;
        line-height: 1.5;
        color: #d4d4d4;
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