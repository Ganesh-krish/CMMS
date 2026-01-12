<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Certificate Generator Library
 * Generates PDF certificates for course completion
 */
class Certificate_generator {

    private $ci;

    public function __construct() {
        $this->ci =& get_instance();
        $this->ci->load->helper('file');
    }

    /**
     * Generate PDF certificate
     * 
     * @param array $data Certificate data (student, course, certificate_number, etc.)
     * @return string|false Path to generated PDF file or false on failure
     */
    public function generate_pdf($data) {
        // Create certificates directory if it doesn't exist
        $cert_dir = FCPATH . 'uploads/certificates/';
        if (!is_dir($cert_dir)) {
            mkdir($cert_dir, 0755, true);
        }

        // Generate certificate using HTML to PDF approach
        // We'll use a simple HTML template and convert to PDF
        $html = $this->get_certificate_html($data);
        
        // For now, we'll save as HTML and use a simple approach
        // In production, you might want to use TCPDF, FPDF, or DomPDF
        $filename = 'certificate_' . $data['certificate_number'] . '_' . time() . '.html';
        $filepath = $cert_dir . $filename;
        
        if (write_file($filepath, $html)) {
            return 'uploads/certificates/' . $filename;
        }
        
        return false;
    }

    /**
     * Generate certificate HTML template
     */
    private function get_certificate_html($data) {
        // Get college name from data if provided, otherwise try to get it
        $college_name = $data['college_name'] ?? 'Educational Institution';
        
        if ($college_name === 'Educational Institution') {
            try {
                if (isset($this->ci->faculty_common)) {
                    $college = $this->ci->faculty_common->get_default_college();
                    $college_name = $college['name'] ?? 'Educational Institution';
                } elseif (isset($this->ci->college) && !empty($this->ci->college)) {
                    $college_name = $this->ci->college['name'] ?? 'Educational Institution';
                }
            } catch (Exception $e) {
                // Use default if college info not available
                log_message('error', 'Certificate generator: Could not get college name - ' . $e->getMessage());
            }
        }
        
        $student_name = htmlspecialchars($data['student_name'] ?? 'Student');
        $course_name = htmlspecialchars($data['course_name'] ?? 'Course');
        $certificate_number = htmlspecialchars($data['certificate_number'] ?? '');
        $issue_date = isset($data['issued_at']) ? date('F d, Y', strtotime($data['issued_at'])) : date('F d, Y');
        
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificate of Completion</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: "Georgia", "Times New Roman", serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #7e22ce 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .certificate-container {
            width: 11.69in;
            height: 8.27in;
            background: #ffffff;
            padding: 0;
            box-shadow: 0 20px 60px rgba(0,0,0,0.4);
            position: relative;
            overflow: hidden;
        }
        
        /* Outer Gold Border */
        .outer-border {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border: 25px solid #d4af37;
            z-index: 1;
        }
        
        /* Inner Decorative Border */
        .inner-border {
            position: absolute;
            top: 30px;
            left: 30px;
            right: 30px;
            bottom: 30px;
            border: 8px double #d4af37;
            z-index: 2;
        }
        
        /* Corner Decorations */
        .corner-decoration {
            position: absolute;
            width: 80px;
            height: 80px;
            border: 4px solid #d4af37;
            z-index: 3;
        }
        .corner-top-left {
            top: 50px;
            left: 50px;
            border-right: none;
            border-bottom: none;
        }
        .corner-top-right {
            top: 50px;
            right: 50px;
            border-left: none;
            border-bottom: none;
        }
        .corner-bottom-left {
            bottom: 50px;
            left: 50px;
            border-right: none;
            border-top: none;
        }
        .corner-bottom-right {
            bottom: 50px;
            right: 50px;
            border-left: none;
            border-top: none;
        }
        
        /* Content Wrapper */
        .content-wrapper {
            position: relative;
            z-index: 10;
            padding: 80px 100px;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        /* Header Section */
        .certificate-header {
            text-align: center;
            margin-bottom: 50px;
            padding-bottom: 20px;
            border-bottom: 3px solid #d4af37;
        }
        .certificate-header h1 {
            font-size: 52px;
            color: #1a1a1a;
            margin: 0;
            font-weight: 700;
            letter-spacing: 8px;
            text-transform: uppercase;
            font-family: "Georgia", serif;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        .certificate-header .subtitle {
            font-size: 18px;
            color: #666;
            margin-top: 10px;
            letter-spacing: 3px;
            font-style: italic;
        }
        
        /* Body Section */
        .certificate-body {
            text-align: center;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 0;
        }
        .certificate-text {
            font-size: 22px;
            color: #333;
            line-height: 2;
            margin: 15px 0;
            font-weight: 400;
        }
        .student-name {
            font-size: 42px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 25px 0;
            padding: 15px 40px;
            border-bottom: 4px solid #d4af37;
            border-top: 4px solid #d4af37;
            display: inline-block;
            letter-spacing: 2px;
            font-family: "Georgia", serif;
        }
        .course-name {
            font-size: 32px;
            color: #2a5298;
            font-weight: 600;
            margin: 25px 0;
            font-style: italic;
            letter-spacing: 1px;
        }
        .college-name {
            font-size: 24px;
            color: #555;
            margin-top: 30px;
            font-weight: 500;
            letter-spacing: 2px;
        }
        
        /* Footer Section */
        .certificate-footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding-top: 30px;
            border-top: 2px solid #e0e0e0;
        }
        .signature-block {
            text-align: center;
            width: 280px;
            flex: 1;
        }
        .signature-line {
            border-top: 3px solid #1a1a1a;
            margin-top: 70px;
            padding-top: 12px;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }
        .signature-name {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        .signature-title {
            font-size: 13px;
            color: #666;
            font-style: italic;
        }
        
        /* Bottom Info */
        .certificate-info {
            position: absolute;
            bottom: 40px;
            left: 100px;
            right: 100px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: #666;
            z-index: 10;
        }
        .issue-date {
            font-weight: 500;
        }
        .certificate-number {
            font-weight: 600;
            color: #2a5298;
            letter-spacing: 1px;
        }
        
        /* Background Seal */
        .seal {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200px;
            height: 200px;
            border: 8px solid #d4af37;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
            color: #d4af37;
            opacity: 0.25;
            z-index: 5;
        }
        .seal::before {
            content: "✓";
            font-weight: bold;
        }
        
        /* Decorative Lines */
        .decorative-line {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 400px;
            height: 2px;
            background: linear-gradient(to right, transparent, #d4af37, transparent);
            z-index: 6;
        }
        .decorative-line-top {
            top: 180px;
        }
        .decorative-line-bottom {
            bottom: 200px;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .certificate-container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Outer Gold Border -->
        <div class="outer-border"></div>
        
        <!-- Inner Decorative Border -->
        <div class="inner-border"></div>
        
        <!-- Corner Decorations -->
        <div class="corner-decoration corner-top-left"></div>
        <div class="corner-decoration corner-top-right"></div>
        <div class="corner-decoration corner-bottom-left"></div>
        <div class="corner-decoration corner-bottom-right"></div>
        
        <!-- Background Seal -->
        <div class="seal"></div>
        
        <!-- Decorative Lines -->
        <div class="decorative-line decorative-line-top"></div>
        <div class="decorative-line decorative-line-bottom"></div>
        
        <!-- Main Content -->
        <div class="content-wrapper">
            <!-- Header -->
            <div class="certificate-header">
                <h1>CERTIFICATE OF COMPLETION</h1>
                <div class="subtitle">This is to Certify</div>
            </div>
            
            <!-- Body -->
            <div class="certificate-body">
                <div class="certificate-text">
                    That
                </div>
                
                <div class="student-name">
                    ' . $student_name . '
                </div>
                
                <div class="certificate-text">
                    has successfully completed the course
                </div>
                
                <div class="course-name">
                    "' . $course_name . '"
                </div>
                
                <div class="college-name">
                    ' . $college_name . '
                </div>
            </div>
            
            <!-- Footer with Signatures -->
            <div class="certificate-footer">
                <div class="signature-block">
                    <div class="signature-line">
                        <div class="signature-name">Principal</div>
                        <div class="signature-title">' . $college_name . '</div>
                    </div>
                </div>
                
                <div class="signature-block">
                    <div class="signature-line">
                        <div class="signature-name">Course Coordinator</div>
                        <div class="signature-title">' . $college_name . '</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom Info -->
        <div class="certificate-info">
            <div class="issue-date">
                <strong>Date of Issue:</strong> ' . $issue_date . '
            </div>
            <div class="certificate-number">
                <strong>Certificate No:</strong> ' . $certificate_number . '
            </div>
        </div>
    </div>
</body>
</html>';
        
        return $html;
    }

    /**
     * Generate PDF using TCPDF (if available) or return HTML
     * This is a placeholder - you can integrate TCPDF here
     */
    public function generate_pdf_tcpdf($data) {
        // Check if TCPDF is available
        if (class_exists('TCPDF')) {
            // TCPDF implementation would go here
            // For now, we'll use HTML approach
        }
        
        return $this->generate_pdf($data);
    }
}
