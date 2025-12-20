# File Content Preview Setup

This document explains how to enable file content preview for TXT, PDF, and DOC files in the lesson viewer.

## Current Status

- ✅ **TXT files**: Full content preview (works immediately)
- ✅ **Images**: Preview thumbnails (works immediately)
- ⚠️ **PDF files**: Requires library installation
- ⚠️ **DOC/DOCX files**: Requires library installation

## Installation Steps

### 1. Install Required Libraries

Run these commands in your project root directory:

```bash
# Install PDF parser for PDF content extraction
composer require smalot/pdfparser

# Install PHPWord for DOC/DOCX content extraction
composer require phpoffice/phpword
```

### 2. Verify Installation

Visit this URL to check if libraries are installed:
```
http://localhost/CMMS/install_file_preview_libraries.php
```

### 3. Enable Code (Important!)

After installing libraries, you MUST uncomment the PDF and DOC parsing code in:
```
application/views/faculty/courses/view_lesson.php
```

Look for these commented sections and uncomment them:

#### PDF Parsing Code (around line 205):
```php
/*
require_once APPPATH . '../vendor/autoload.php';
try {
    $parser = new \Smalot\PdfParser\Parser();
    $pdf = $parser->parseFile($full_file_path);
    $text = $pdf->getText();

    if (!empty(trim($text))) {
        echo '<div class="pdf-preview" style="font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6;">';
        echo nl2br(htmlspecialchars(substr($text, 0, 2000)));
        if (strlen($text) > 2000) {
            echo '<br><em>... (content truncated)</em>';
        }
        echo '</div>';
    } else {
        echo '<div class="alert alert-info">PDF appears to be image-based or empty.</div>';
    }
} catch (Exception $pdfError) {
    echo '<div class="alert alert-warning">Unable to extract PDF text: ' . htmlspecialchars($pdfError->getMessage()) . '</div>';
}
*/
```

#### DOC Parsing Code (around line 230):
```php
/*
require_once APPPATH . '../vendor/autoload.php';
try {
    $phpWord = \PhpOffice\PhpWord\IOFactory::load($full_file_path);
    $text = '';

    foreach ($phpWord->getSections() as $section) {
        foreach ($section->getElements() as $element) {
            if ($element instanceof \PhpOffice\PhpWord\Element\TextRun) {
                foreach ($element->getElements() as $textElement) {
                    if ($textElement instanceof \PhpOffice\PhpWord\Element\Text) {
                        $text .= $textElement->getText() . ' ';
                    }
                }
            } elseif ($element instanceof \PhpOffice\PhpWord\Element\Text) {
                $text .= $element->getText() . ' ';
            }
        }
    }

    if (!empty(trim($text))) {
        echo '<div class="doc-preview" style="font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6;">';
        echo nl2br(htmlspecialchars(substr($text, 0, 2000)));
        if (strlen($text) > 2000) {
            echo '<br><em>... (content truncated)</em>';
        }
        echo '</div>';
    } else {
        echo '<div class="alert alert-info">Document appears to be image-based or empty.</div>';
    }
} catch (Exception $docError) {
    echo '<div class="alert alert-warning">Unable to extract DOC text: ' . htmlspecialchars($docError->getMessage()) . '</div>';
}
*/
```

## File Size Limits

- **Preview Limit**: 5MB maximum file size for content preview
- **Text Limit**: 2000 characters displayed (with "truncated" message)
- **Security**: Files are checked for readability before processing

## Supported File Types

| File Type | Preview Status | Requirements |
|-----------|---------------|-------------|
| `.txt` | ✅ Full preview | None |
| `.pdf` | ✅ Text extraction | `smalot/pdfparser` library |
| `.doc` | ✅ Text extraction | `phpoffice/phpword` library |
| `.docx` | ✅ Text extraction | `phpoffice/phpword` library |
| Images | ✅ Thumbnail preview | None |
| Other | ✅ Download only | None |

## Troubleshooting

### Libraries Not Loading
- Ensure Composer is installed and `vendor/autoload.php` exists
- Run `composer install` to install dependencies
- Check file permissions on `vendor/` directory

### PDF Shows No Text
- PDF might be image-based (scanned document)
- Try a different PDF file with selectable text
- Check PDF parser error messages

### DOC Shows No Text
- Document might be image-based or corrupted
- Try a different DOC file
- Check PHPWord error messages

### Files Not Readable
- Check file permissions on `uploads/course_files/` directory
- Ensure files were uploaded successfully
- Verify file paths are correct

## Testing

1. Upload a TXT file to a lesson
2. Click the eye icon to view the lesson
3. TXT content should display immediately

4. Upload a PDF file (with selectable text)
5. After installing PDF library and uncommenting code, content should display

6. Upload a DOC file
7. After installing PHPWord library and uncommenting code, content should display

## Security Notes

- File content is displayed as plain text (HTML entities escaped)
- File size limits prevent memory exhaustion
- File readability is verified before processing
- External libraries are used safely with try-catch blocks