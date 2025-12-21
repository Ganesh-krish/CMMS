es <?php
echo "<h1>Student Portal Setup Test</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .status{padding:10px;margin:10px 0;border-radius:4px;} .success{background:#d4edda;color:#155724;border:1px solid #c3e6cb;} .error{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;} .warning{background:#fff3cd;color:#856404;border:1px solid #ffeaa7;}</style>";

// Check StudentPortal controller
$controller_path = __DIR__ . '/application/controllers/StudentPortal.php';
if (file_exists($controller_path)) {
    echo "<div class='status success'>✅ StudentPortal controller exists</div>";
} else {
    echo "<div class='status error'>❌ StudentPortal controller missing</div>";
}

// Check student views
$views = [
    'student/login.php',
    'student/dashboard.php',
    'student/common/sidebar.php',
    'student/common/footer.php',
    'student/courses/index.php',
    'student/courses/modules.php',
    'student/courses/lessons.php',
    'student/courses/view_lesson.php',
    'student/inventory/index.php',
    'student/announcements/index.php'
];

echo "<h3>Student Views Status:</h3>";
$all_views_exist = true;
foreach ($views as $view) {
    $path = __DIR__ . '/application/views/' . $view;
    if (file_exists($path)) {
        echo "<div class='status success'>✅ $view exists</div>";
    } else {
        echo "<div class='status error'>❌ $view missing</div>";
        $all_views_exist = false;
    }
}

// Check routes
$routes_file = __DIR__ . '/application/config/routes.php';
$routes_content = file_get_contents($routes_file);
$student_routes = [
    'student-portal/login',
    'student-portal/authenticate',
    'student-portal/dashboard',
    'student-portal/courses',
    'student-portal/course-modules',
    'student-portal/module-lessons',
    'student-portal/view-lesson',
    'student-portal/inventory',
    'student-portal/announcements',
    'student-portal/logout'
];

echo "<h3>Routes Status:</h3>";
$routes_exist = true;
foreach ($student_routes as $route) {
    if (strpos($routes_content, $route) !== false) {
        echo "<div class='status success'>✅ Route '$route' configured</div>";
    } else {
        echo "<div class='status error'>❌ Route '$route' missing</div>";
        $routes_exist = false;
    }
}

// Summary
echo "<h2>Setup Summary</h2>";
if ($all_views_exist && $routes_exist && file_exists($controller_path)) {
    echo "<div class='status success'>";
    echo "<h3>🎉 Student Portal Setup Complete!</h3>";
    echo "<p>All components are properly configured. You can now:</p>";
    echo "<ul>";
    echo "<li>Access student login: <code>http://localhost/CMMS/student-portal/login</code></li>";
    echo "<li>Use student credentials to login</li>";
    echo "<li>Navigate through courses (cards), inventory (cards), and announcements</li>";
    echo "</ul>";
    echo "</div>";
} else {
    echo "<div class='status error'>";
    echo "<h3>❌ Setup Incomplete</h3>";
    echo "<p>Please check the errors above and fix any missing components.</p>";
    echo "</div>";
}

echo "<p><strong>Note:</strong> Remember to create student accounts in the faculty student management section for testing.</p>";
?>
