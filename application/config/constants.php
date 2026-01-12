<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

 
defined('CLIENT_ID')      or define('CLIENT_ID', '436348384707-emm1a4aumq4i3j3kge93nf63gtabiv3u.apps.googleusercontent.com');
defined('ClientSecret')      or define('ClientSecret', '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');
defined('RedirectURL')      or define('RedirectURL', "OAuth/verify");

defined('DB_INSTALL_KEY')      or define('DB_INSTALL_KEY', "YInTXhwPxaMwSGGZ9DQtVkuTk4OoZguJ");
defined('STUDENT_PORTAL_BASE_URL')      or define('STUDENT_PORTAL_BASE_URL','');
defined('SINGLE_COLLEGE_ID') or define('SINGLE_COLLEGE_ID', 1);



defined('TABLE_COLLEGE')      or define('TABLE_COLLEGE', "college");
defined('TABLE_DEPARTMENT')      or define('TABLE_DEPARTMENT', "departments");
defined('TABLE_FACULTY')    or define('TABLE_FACULTY', "faculty");
defined('TABLE_STUDENT')      or define('TABLE_STUDENT', "students");
defined('TABLE_COURCES')      or define('TABLE_COURCES', "courses");
defined('TABLE_COURSE_ENROLLMENTS')      or define('TABLE_COURSE_ENROLLMENTS', "course_enrollments");
defined('TABLE_COURSE_STUDENTS')      or define('TABLE_COURSE_STUDENTS', "course_enrollments"); // Alias for enrollments
defined('TABLE_COURSE_MODULES')      or define('TABLE_COURSE_MODULES', "course_modules");
defined('TABLE_COURSE_MODULE_LESSONS') or define('TABLE_COURSE_MODULE_LESSONS', "course_module_lessons");
defined('TABLE_COURSE_CERTIFICATES') or define('TABLE_COURSE_CERTIFICATES', "course_certificates");
defined('TABLE_STUDENT_LESSON_PROGRESS') or define('TABLE_STUDENT_LESSON_PROGRESS', "student_lesson_progress");
defined('TABLE_CERTIFICATE_REQUESTS') or define('TABLE_CERTIFICATE_REQUESTS', "certificate_requests");

// Musical Instrument Inventory Tables
defined('TABLE_INSTRUMENTS')               or define('TABLE_INSTRUMENTS', "instruments");
defined('TABLE_INSTRUMENT_ISSUES')         or define('TABLE_INSTRUMENT_ISSUES', "instrument_issues");
defined('TABLE_INSTRUMENT_CATEGORIES')     or define('TABLE_INSTRUMENT_CATEGORIES', "instrument_categories");

// Groups Tables
defined('TABLE_GROUPS')                    or define('TABLE_GROUPS', "groups");
defined('TABLE_MEMGROUPS')                 or define('TABLE_MEMGROUPS', "memgroups");

// Announcement Tables
defined('TABLE_ANNOUNCEMENTS')             or define('TABLE_ANNOUNCEMENTS', "announcements");



define('COURSE_TYPES', json_encode([
    1 => ['name' => 'Courses', 'color' => ''],
    2 => ['name' => 'Company Specific Test', 'color' => '#55A3F4'],
    3 => ['name' => 'Exams & Labs', 'color' => '#F4AB55']
]));

define('COURSE_MODES', json_encode([
    1 => ['name' => 'Normal', 'color' => ''],
    2 => ['name' => 'Gamification', 'color' => '#62D493']
]));

// New role hierarchy for educational institution
defined('ROLE_PRINCIPAL')      or define('ROLE_PRINCIPAL', 1);      // Principal
defined('ROLE_VICE_PRINCIPAL')  or define('ROLE_VICE_PRINCIPAL', 2);  // Vice-Principal
defined('ROLE_HOD')             or define('ROLE_HOD', 3);             // HOD (Department Admin)
defined('ROLE_STAFF')           or define('ROLE_STAFF', 4);           // Staff (Instructor)
defined('ROLE_CUSTODIAN')       or define('ROLE_CUSTODIAN', 5);         // Custodian (Inventory)
defined('ROLE_STUDENT')         or define('ROLE_STUDENT',6);

// Designation aliases for the new hierarchy
defined('DESIGNATION_PRINCIPAL')     or define('DESIGNATION_PRINCIPAL', ROLE_PRINCIPAL);
defined('DESIGNATION_VICE_PRINCIPAL') or define('DESIGNATION_VICE_PRINCIPAL', ROLE_VICE_PRINCIPAL);
defined('DESIGNATION_HOD')           or define('DESIGNATION_HOD', ROLE_HOD);
defined('DESIGNATION_STAFF')         or define('DESIGNATION_STAFF', ROLE_STAFF);
defined('DESIGNATION_CUSTODIAN')     or define('DESIGNATION_CUSTODIAN', ROLE_CUSTODIAN);


// Musical Instrument Condition Status (String Constants)
defined('INSTRUMENT_CONDITION_EXCELLENT') or define('INSTRUMENT_CONDITION_EXCELLENT', 'excellent');
defined('INSTRUMENT_CONDITION_GOOD')      or define('INSTRUMENT_CONDITION_GOOD', 'good');
defined('INSTRUMENT_CONDITION_FAIR')      or define('INSTRUMENT_CONDITION_FAIR', 'fair');
defined('INSTRUMENT_CONDITION_POOR')      or define('INSTRUMENT_CONDITION_POOR', 'poor');
defined('INSTRUMENT_CONDITION_DAMAGED')   or define('INSTRUMENT_CONDITION_DAMAGED', 'damaged');

// Musical Instrument Availability Status (String Constants)
defined('INSTRUMENT_STATUS_AVAILABLE')    or define('INSTRUMENT_STATUS_AVAILABLE', 'available');
defined('INSTRUMENT_STATUS_ISSUED')       or define('INSTRUMENT_STATUS_ISSUED', 'issued');
defined('INSTRUMENT_STATUS_MAINTENANCE')  or define('INSTRUMENT_STATUS_MAINTENANCE', 'maintenance');
defined('INSTRUMENT_STATUS_DAMAGED')      or define('INSTRUMENT_STATUS_DAMAGED', 'damaged');

// Instrument Issue Status (String Constants)
defined('INSTRUMENT_ISSUE_STATUS_ISSUED')    or define('INSTRUMENT_ISSUE_STATUS_ISSUED', 'issued');
defined('INSTRUMENT_ISSUE_STATUS_RETURNED')  or define('INSTRUMENT_ISSUE_STATUS_RETURNED', 'returned');
defined('INSTRUMENT_ISSUE_STATUS_OVERDUE')   or define('INSTRUMENT_ISSUE_STATUS_OVERDUE', 'overdue');

// Lesson Type Constants (String Constants)
defined('LESSON_TYPE_TEXT')  or define('LESSON_TYPE_TEXT', 'text');
defined('LESSON_TYPE_VIDEO') or define('LESSON_TYPE_VIDEO', 'video');
defined('LESSON_TYPE_FILE')  or define('LESSON_TYPE_FILE', 'file');
