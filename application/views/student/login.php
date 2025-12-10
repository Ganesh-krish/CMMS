<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $college['name']; ?> | Student Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width: 480px;">
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="text-center mb-4">
                <?php if (!empty($college['logo'])): ?>
                    <img src="<?php echo $college['logo']; ?>" alt="logo" height="64">
                <?php endif; ?>
                <h4 class="mt-2 mb-0"><?php echo $college['name']; ?></h4>
                <small class="text-muted">Student Portal</small>
            </div>
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message[0]; ?>">
                    <?php echo $message[1]; ?>
                </div>
            <?php endif; ?>
            <form method="post" action="<?php echo base_url("student-portal/{$college_slug}/authenticate"); ?>">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>


