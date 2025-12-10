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
defined('STUDENT_PORTAL_BASE_URL')      or define('STUDENT_PORTAL_BASE_URL','https://drillu.in');
defined('ONE_COMPILER_API_KEY') or define('ONE_COMPILER_API_KEY', getenv('ONE_COMPILER_API_KEY') ?: '');

defined('DESIGNATION_PRINCIPAL')      or define('DESIGNATION_PRINCIPAL', 1);
defined('DESIGNATION_HOD')      or define('DESIGNATION_HOD', 2);
defined('DESIGNATION_STAFF')      or define('DESIGNATION_STAFF', 3);


defined('TABLE_COLLEGE')      or define('TABLE_COLLEGE', "colleges");
defined('TABLE_DEPARTMENT')      or define('TABLE_DEPARTMENT', "departments");
defined('TABLE_REQUEST')      or define('TABLE_REQUEST', "login_requests");
defined('TABLE_OWNER')      or define('TABLE_OWNER', "owner");
defined('TABLE_STAFF')      or define('TABLE_STAFF', "staffs");
defined('TABLE_STUDENT')      or define('TABLE_STUDENT', "students");
defined('TABLE_COURCES')      or define('TABLE_COURCES', "courses");
defined('TABLE_SPECIAL_COURCES')      or define('TABLE_SPECIAL_COURCES', "special_courses");
defined('TABLE_STUDENT_TEST_SUBMISSION') or define('TABLE_STUDENT_TEST_SUBMISSION', "student_test_submission");
defined('TABLE_BATCHES') or define('TABLE_BATCHES', "batches");
defined('TABLE_MODULE_SCHEDULES') or define('TABLE_MODULE_SCHEDULES', "module_schedules");
defined('TABLE_ENROLLMENTS') or define('TABLE_ENROLLMENTS', "enrollments");
defined('TABLE_PROGRESS_LOGS') or define('TABLE_PROGRESS_LOGS', "progress_logs");
defined('TABLE_INSTRUMENTS') or define('TABLE_INSTRUMENTS', "instruments");
defined('TABLE_INSTRUMENT_TRANSACTIONS') or define('TABLE_INSTRUMENT_TRANSACTIONS', "instrument_transactions");
defined('TABLE_INSTRUMENT_MAINTENANCE') or define('TABLE_INSTRUMENT_MAINTENANCE', "instrument_maintenance");
defined('TABLE_NOTIFICATIONS') or define('TABLE_NOTIFICATIONS', "notifications");

defined("TABLE_QUESTION_TYPE") or define("TABLE_QUESTION_TYPE", "question_types");
defined("TABLE_QUESTION_BANK") or define("TABLE_QUESTION_BANK","private_question_bank");
defined('TABLE_ANSWER_OPTIONS')      or define('TABLE_ANSWER_OPTIONS', "private_answer_options");

defined('TABLE_QUESTION_SUB_TYPES') or define('TABLE_QUESTION_SUB_TYPES', "question_sub_types");
defined('TABLE_QUESTION_DIFFICULTY_LEVEL') or define('TABLE_QUESTION_DIFFICULTY_LEVEL', "question_difficulty_level");
defined('TABLE_QUESTION_TEST_CASES') or define('TABLE_QUESTION_TEST_CASES', "private_question_test_cases");


defined("TABLE_TEST_MODULES") or define("TABLE_TEST_MODULES","test_modules");
defined("TABLE_PRIVATE_TESTS") or define("TABLE_PRIVATE_TESTS","private_tests");

defined("TABLE_PRIVATE_TEST_SETTINGS_NAVIGATION") or define("TABLE_PRIVATE_TEST_SETTINGS_NAVIGATION", "private_test_settings_navigation");
defined("TABLE_PRIVATE_TEST_SETTINGS_SECURITY") or define("TABLE_PRIVATE_TEST_SETTINGS_SECURITY", "private_test_settings_security");
defined("TABLE_PRIVATE_TEST_SETTINGS_MONITORING") or define("TABLE_PRIVATE_TEST_SETTINGS_MONITORING", "private_test_settings_monitoring");
defined("TABLE_PRIVATE_TEST_SETTINGS_UI") or define("TABLE_PRIVATE_TEST_SETTINGS_UI", "private_test_settings_ui");
defined("TABLE_PRIVATE_TEST_QUESTIONS") or define("TABLE_PRIVATE_TEST_QUESTIONS","private_test_questions");

define('COURSE_TYPES', json_encode([
    1 => ['name' => 'Courses', 'color' => ''],
    2 => ['name' => 'Company Specific Test', 'color' => '#55A3F4'],
    3 => ['name' => 'Exams & Labs', 'color' => '#F4AB55']
]));

define('COURSE_MODES', json_encode([
    1 => ['name' => 'Normal', 'color' => ''],
    2 => ['name' => 'Gamification', 'color' => '#62D493']
]));


