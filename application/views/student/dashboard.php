<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $college['name']; ?> | Student Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand"><?php echo $college['name']; ?> Student Panel</span>
        <div class="d-flex text-white">
            <span class="me-3"><?php echo $student->name ?? ''; ?></span>
            <a class="btn btn-outline-light btn-sm" href="<?php echo base_url("student-portal/{$college_slug}/logout"); ?>">Logout</a>
        </div>
    </div>
</nav>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title mb-0">Courses</h5>
                    <small class="text-muted">Active courses available to you</small>
                </div>
            </div>
        </div>
    </div>
    <div class="row g-3">
        <?php if (!empty($courses)): foreach ($courses as $course): ?>
            <div class="col-12">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="card-title mb-1"><?php echo $course['name']; ?></h5>
                        <?php if (!empty($course['description'])): ?>
                            <p class="card-text text-muted"><?php echo $course['description']; ?></p>
                        <?php endif; ?>
                        <div class="mb-2">
                            <?php if (!empty($course['level'])): ?>
                                <span class="badge bg-info text-dark me-2">Level: <?php echo $course['level']; ?></span>
                            <?php endif; ?>
                            <?php if (!empty($course['instrument_focus'])): ?>
                                <span class="badge bg-secondary">Instrument: <?php echo $course['instrument_focus']; ?></span>
                            <?php endif; ?>
                        </div>
                        <?php $modules = $modules_by_course[$course['id']] ?? []; ?>
                        <?php if (!empty($modules)): ?>
                            <div class="accordion" id="accordion-<?php echo $course['id']; ?>">
                                <?php foreach ($modules as $idx => $module): ?>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="heading-<?php echo $course['id'] . '-' . $idx; ?>">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $course['id'] . '-' . $idx; ?>">
                                                <?php echo $module['name']; ?>
                                            </button>
                                        </h2>
                                        <div id="collapse-<?php echo $course['id'] . '-' . $idx; ?>" class="accordion-collapse collapse" data-bs-parent="#accordion-<?php echo $course['id']; ?>">
                                            <div class="accordion-body">
                                                <?php if (!empty($module['lessons'])): ?>
                                                    <ul class="list-group list-group-flush">
                                                        <?php foreach ($module['lessons'] as $lesson): ?>
                                                            <li class="list-group-item">
                                                                <div class="fw-semibold"><?php echo $lesson['title']; ?></div>
                                                                <?php if (!empty($lesson['video_url'])): ?>
                                                                    <div><small class="text-muted">Video: <a href="<?php echo $lesson['video_url']; ?>" target="_blank">Open</a></small></div>
                                                                <?php endif; ?>
                                                                <?php if (!empty($lesson['attachment_url'])): ?>
                                                                    <div><small class="text-muted">Attachment: <a href="<?php echo $lesson['attachment_url']; ?>" target="_blank">Download</a></small></div>
                                                                <?php endif; ?>
                                                                <?php if (!empty($lesson['body_text'])): ?>
                                                                    <div class="mt-1"><small class="text-muted">Text lesson available</small></div>
                                                                <?php endif; ?>
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php else: ?>
                                                    <div class="text-muted">No lessons available.</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-muted">No modules available.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; else: ?>
            <div class="col">
                <div class="alert alert-info">No courses assigned yet.</div>
            </div>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


