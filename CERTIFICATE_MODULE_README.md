# Course Certificate Generation Module

## Overview
This module implements automatic certificate generation for students upon course completion. The system generates professional certificates that students can view, download, and print.

## Features Implemented

### 1. Database Structure
- **Table**: `course_certificates`
  - Stores certificate information
  - Links to course enrollments, courses, and students
  - Includes unique certificate numbers
  - Tracks certificate files and issue dates

### 2. Certificate Model (`Certificate_model.php`)
- `get_certificate($certificate_id)` - Get certificate by ID
- `get_certificate_by_enrollment($enrollment_id)` - Get certificate for an enrollment
- `get_certificate_by_number($certificate_number)` - Get certificate by certificate number
- `get_student_certificates($student_id)` - Get all certificates for a student
- `create_certificate($data)` - Create or update certificate
- `generate_certificate_number($course_id, $student_id)` - Generate unique certificate number
- `certificate_exists($enrollment_id)` - Check if certificate exists

### 3. Certificate Generator Library (`Certificate_generator.php`)
- Generates professional HTML-based certificates
- Creates certificate files in `uploads/certificates/` directory
- Beautiful certificate design with:
  - College branding
  - Student name
  - Course name
  - Certificate number
  - Issue date
  - Signature blocks
  - Decorative borders and seal

### 4. Auto-Generation on Course Completion
- When a course enrollment status is changed to "completed":
  - Automatically generates a unique certificate number
  - Creates certificate HTML file
  - Saves certificate record to database
  - Sets `completed_at` timestamp
  - Updates progress to 100%

### 5. Student Portal Integration
- **Certificates Page** (`/student-portal/certificates`)
  - Lists all certificates earned by the student
  - Shows certificate details (number, issue date, course)
  - Provides view and download options

- **Certificate View** (`/student-portal/certificate/{id}`)
  - Displays full certificate in printable format
  - Includes print button
  - Professional certificate layout

- **Course List Enhancement**
  - Shows "View Certificate" button for completed courses
  - Only displays if certificate exists

### 6. Menu Integration
- Added "Certificates" menu item to student sidebar
- Easy access to all certificates

## File Structure

```
application/
├── config/
│   └── constants.php (added TABLE_COURSE_CERTIFICATES)
├── controllers/
│   ├── Course.php (updated - auto-generates certificates)
│   └── StudentPortal.php (added certificate methods)
├── models/
│   └── Certificate_model.php (new)
├── libraries/
│   └── Certificate_generator.php (new)
└── views/
    └── student/
        ├── common/
        │   └── sidebar.php (added Certificates menu)
        ├── certificates/
        │   ├── index.php (new - certificate list)
        │   └── view.php (new - certificate display)
        └── courses/
            └── index.php (updated - shows certificate button)

database/
└── course_certificates.sql (new - database schema)
```

## Database Setup

Run the SQL file to create the certificate table:
```sql
-- Run: database/course_certificates.sql
```

## How It Works

### For Faculty/Admin:
1. Navigate to course enrollments
2. Change student enrollment status to "completed"
3. System automatically:
   - Generates certificate number (format: CERT-{COURSE}-{DATE}-{RANDOM})
   - Creates certificate HTML file
   - Saves certificate record
   - Shows success message

### For Students:
1. Complete a course (status changed to "completed" by faculty)
2. Certificate is automatically generated
3. Access certificates via:
   - "Certificates" menu in sidebar
   - "View Certificate" button on completed courses
4. View, print, or download certificates

## Certificate Number Format
- Format: `CERT-{COURSE_CODE}-{YYYYMMDD}-{RANDOM}`
- Example: `CERT-MUSI-20260115-A3F2B1`
- Ensures uniqueness across all certificates

## Certificate Design Features
- Professional layout with decorative borders
- College branding support
- Student and course information
- Unique certificate number
- Issue date
- Signature blocks for Principal and Course Coordinator
- Print-friendly design
- Responsive layout

## Future Enhancements (Optional)
1. **PDF Generation**: Integrate TCPDF or DomPDF for true PDF certificates
2. **Email Notifications**: Send certificate via email when generated
3. **Digital Signatures**: Add digital signatures to certificates
4. **QR Codes**: Add QR codes for certificate verification
5. **Certificate Templates**: Multiple certificate design templates
6. **Bulk Generation**: Generate certificates for multiple students at once

## Notes
- Certificates are stored as HTML files in `uploads/certificates/`
- Certificate files can be printed directly from browser
- For production, consider implementing true PDF generation
- Certificate numbers are unique and cannot be duplicated
- Only one certificate per enrollment (prevents duplicates)

## Access Control
- Students can only view their own certificates
- Faculty can mark courses as completed (triggers certificate generation)
- Certificate viewing requires authentication
