ds<?php
/**
 * File Preview Libraries Installation Check
 *
 * This script checks if the required libraries for file content preview are installed.
 * Run this from your browser to check library status.
 */

// Check if Composer autoload exists
$autoloadPath = __DIR__ . '/vendor/autoload.php';
$composerAvailable = file_exists($autoloadPath);

echo "<h1>File Preview Libraries Status</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .status{padding:10px;margin:10px 0;border-radius:4px;} .success{background:#d4edda;color:#155724;border:1px solid #c3e6cb;} .error{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;} .warning{background:#fff3cd;color:#856404;border:1px solid #ffeaa7;}</style>";

if (!$composerAvailable) {
    echo "<div class='status error'>";
    echo "<h3>❌ Composer Not Found</h3>";
    echo "<p>Composer autoload file not found. Please install Composer and run the following commands in your project root:</p>";
    echo "<pre>curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer</pre>";
    echo "</div>";
    exit;
}

echo "<div class='status success'>✅ Composer autoload found</div>";

// Check PDF Parser library
try {
    require_once $autoloadPath;
    $pdfParserAvailable = class_exists('\Smalot\PdfParser\Parser');
} catch (Exception $e) {
    $pdfParserAvailable = false;
}

if ($pdfParserAvailable) {
    echo "<div class='status success'>✅ PDF Parser library (smalot/pdfparser) is installed</div>";
} else {
    echo "<div class='status error'>";
    echo "<h3>❌ PDF Parser Library Missing</h3>";
    echo "<p>To enable PDF content preview, install the PDF parser library:</p>";
    echo "<pre>composer require smalot/pdfparser</pre>";
    echo "<p>After installation, uncomment the PDF parsing code in <code>view_lesson.php</code></p>";
    echo "</div>";
}

// Check PHPWord library
try {
    $phpWordAvailable = class_exists('\PhpOffice\PhpWord\IOFactory');
} catch (Exception $e) {
    $phpWordAvailable = false;
}

if ($phpWordAvailable) {
    echo "<div class='status success'>✅ PHPWord library (phpoffice/phpword) is installed</div>";
} else {
    echo "<div class='status error'>";
    echo "<h3>❌ PHPWord Library Missing</h3>";
    echo "<p>To enable DOC/DOCX content preview, install the PHPWord library:</p>";
    echo "<pre>composer require phpoffice/phpword</pre>";
    echo "<p>After installation, uncomment the DOC parsing code in <code>view_lesson.php</code></p>";
    echo "</div>";
}

// Summary
echo "<h2>Installation Summary</h2>";
echo "<div class='status " . ($pdfParserAvailable && $phpWordAvailable ? "success" : "warning") . "'>";
if ($pdfParserAvailable && $phpWordAvailable) {
    echo "<h3>🎉 All Libraries Installed!</h3>";
    echo "<p>You can now preview content from TXT, PDF, and DOC files.</p>";
} else {
    echo "<h3>⚠️  Some Libraries Missing</h3>";
    echo "<p>TXT files will work immediately. PDF and DOC preview require library installation.</p>";
}
echo "</div>";

echo "<h3>Current File Preview Capabilities:</h3>";
echo "<ul>";
echo "<li><strong>TXT files:</strong> ✅ Full content preview (no library needed)</li>";
echo "<li><strong>PDF files:</strong> " . ($pdfParserAvailable ? "✅ Text extraction available" : "⚠️ Requires smalot/pdfparser library") . "</li>";
echo "<li><strong>DOC/DOCX files:</strong> " . ($phpWordAvailable ? "✅ Text extraction available" : "⚠️ Requires phpoffice/phpword library") . "</li>";
echo "<li><strong>Images:</strong> ✅ Preview available</li>";
echo "<li><strong>Other files:</strong> ✅ Download available</li>";
echo "</ul>";

echo "<p><strong>Note:</strong> Remember to uncomment the PDF and DOC parsing code in <code>application/views/faculty/courses/view_lesson.php</code> after installing the required libraries.</p>";
?>