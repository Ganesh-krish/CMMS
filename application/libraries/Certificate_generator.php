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
        body {
            font-family: "Times New Roman", serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .certificate-container {
            width: 11in;
            height: 8.5in;
            background: white;
            padding: 60px;
            box-shadow: 0 10px 50px rgba(0,0,0,0.3);
            position: relative;
            border: 20px solid #d4af37;
        }
        .certificate-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .certificate-header h1 {
            font-size: 48px;
            color: #2c3e50;
            margin: 0;
            font-weight: bold;
            letter-spacing: 3px;
        }
        .certificate-body {
            text-align: center;
            margin: 60px 0;
        }
        .certificate-text {
            font-size: 24px;
            color: #34495e;
            line-height: 1.8;
            margin: 20px 0;
        }
        .student-name {
            font-size: 36px;
            font-weight: bold;
            color: #2c3e50;
            margin: 30px 0;
            text-decoration: underline;
            text-decoration-color: #d4af37;
            text-decoration-thickness: 3px;
        }
        .course-name {
            font-size: 28px;
            color: #667eea;
            font-weight: bold;
            margin: 20px 0;
        }
        .certificate-footer {
            margin-top: 80px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }
        .signature-block {
            text-align: center;
            width: 250px;
        }
        .signature-line {
            border-top: 2px solid #2c3e50;
            margin-top: 60px;
            padding-top: 10px;
        }
        .signature-name {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
        }
        .signature-title {
            font-size: 14px;
            color: #7f8c8d;
        }
        .certificate-number {
            position: absolute;
            bottom: 30px;
            right: 60px;
            font-size: 12px;
            color: #95a5a6;
        }
        .issue-date {
            position: absolute;
            bottom: 30px;
            left: 60px;
            font-size: 14px;
            color: #34495e;
        }
        .decorative-border {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 3px solid #d4af37;
            pointer-events: none;
        }
        .seal {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 150px;
            height: 150px;
            border: 5px solid #d4af37;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 60px;
            color: #d4af37;
            opacity: 0.3;
            z-index: 0;
        }
        .content-wrapper {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="decorative-border"></div>
        <div class="seal">✓</div>
        
        <div class="content-wrapper">
            <div class="certificate-header">
                <h1>CERTIFICATE OF COMPLETION</h1>
            </div>
            
            <div class="certificate-body">
                <div class="certificate-text">
                    This is to certify that
                </div>
                
                <div class="student-name">
                    ' . $student_name . '
                </div>
                
                <div class="certificate-text">
                    has successfully completed the course
                </div>
                
                <div class="course-name">
                    ' . $course_name . '
                </div>
                
                <div class="certificate-text" style="margin-top: 40px;">
                    ' . $college_name . '
                </div>
            </div>
            
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
            
            <div class="issue-date">
                Date: ' . $issue_date . '
            </div>
            
            <div class="certificate-number">
                Certificate No: ' . $certificate_number . '
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
