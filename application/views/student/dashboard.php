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
            <div class="col-md-6">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $course['name']; ?></h5>
                        <?php if (!empty($course['description'])): ?>
                            <p class="card-text text-muted"><?php echo $course['description']; ?></p>
                        <?php endif; ?>
                        <?php if (!empty($course['level'])): ?>
                            <span class="badge bg-info text-dark">Level: <?php echo $course['level']; ?></span>
                        <?php endif; ?>
                        <?php if (!empty($course['instrument_focus'])): ?>
                            <span class="badge bg-secondary">Instrument: <?php echo $course['instrument_focus']; ?></span>
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
</body>
</html>

