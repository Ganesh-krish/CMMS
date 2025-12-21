<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Welcome';
$route['logout'] = 'Welcome/logout';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Student Portal Routes
$route['student-portal/login'] = 'StudentPortal/login';
$route['student-portal/authenticate'] = 'StudentPortal/authenticate';
$route['student-portal/logout'] = 'StudentPortal/logout';
$route['student-portal/dashboard'] = 'StudentPortal/dashboard';
$route['student-portal/courses'] = 'StudentPortal/courses';
$route['student-portal/course-modules/(:num)'] = 'StudentPortal/course_modules/$1';
$route['student-portal/module-lessons/(:num)/(:num)'] = 'StudentPortal/module_lessons/$1/$2';
$route['student-portal/view-lesson/(:num)/(:num)/(:num)'] = 'StudentPortal/view_lesson/$1/$2/$3';
$route['student-portal/inventory'] = 'StudentPortal/inventory';
$route['student-portal/announcements'] = 'StudentPortal/announcements';

//Dashboard routes
$route['(:any)/dashboard'] = 'Dashboard/index';  // Dashboard route

// Management routes (Administrator dropdown)
$route['(:any)/management/principal'] = 'Management/principal';
$route['(:any)/management/principal/add'] = 'Management/add_principal';
$route['(:any)/management/principal/edit/(:num)'] = 'Management/edit_principal/$2';
$route['(:any)/management/principal/delete/(:num)'] = 'Management/delete_principal/$2';

$route['(:any)/management/vice_principal'] = 'Management/vice_principal';
$route['(:any)/management/vice_principal/add'] = 'Management/add_vice_principal';
$route['(:any)/management/vice_principal/edit/(:num)'] = 'Management/edit_vice_principal/$2';
$route['(:any)/management/vice_principal/delete/(:num)'] = 'Management/delete_vice_principal/$2';

$route['(:any)/management/hod'] = 'Management/hod';
$route['(:any)/management/hod/add'] = 'Management/add_hod';
$route['(:any)/management/hod/edit/(:num)'] = 'Management/edit_hod/$2';
$route['(:any)/management/hod/delete/(:num)'] = 'Management/delete_hod/$2';

// Faculty Management routes (Instructor & Custodian)
$route['(:any)/faculty/instructor'] = 'Faculty/instructor';
$route['(:any)/faculty/instructor/add'] = 'Faculty/add_instructor';
$route['(:any)/faculty/instructor/edit/(:num)'] = 'Faculty/edit_instructor/$2';
$route['(:any)/faculty/instructor/delete/(:num)'] = 'Faculty/delete_instructor/$2';

$route['(:any)/faculty/custodian'] = 'Faculty/custodian';
$route['(:any)/faculty/custodian/add'] = 'Faculty/add_custodian';
$route['(:any)/faculty/custodian/edit/(:num)'] = 'Faculty/edit_custodian/$2';
$route['(:any)/faculty/custodian/delete/(:num)'] = 'Faculty/delete_custodian/$2';
$route['(:any)/faculty/reset_password_instructor'] = 'Faculty/reset_password_instructor';
$route['(:any)/faculty/reset_password_custodian'] = 'Faculty/reset_password_custodian';

// Department Management routes
$route['(:any)/departments'] = 'Department/view';
$route['(:any)/departments/add'] = 'Department/add';
$route['(:any)/departments/add/(:num)'] = 'Department/add/$2';
$route['(:any)/departments/edit/(:num)'] = 'Department/edit/$2';
$route['(:any)/departments/delete/(:num)'] = 'Department/delete/$2';

// Student Management routes
$route['(:any)/students'] = 'Student/students'; // Admin student management
$route['(:any)/students/add'] = 'Student/add_student';
$route['(:any)/students/edit/(:num)'] = 'Student/edit_student/$2';
$route['(:any)/students/delete/(:num)'] = 'Student/delete_student/$2';
$route['(:any)/students/reset_password'] = 'Student/reset_password_student';

// Role-based Student Management (existing)
$route['(:any)/principal/students'] = 'Principal/students';
$route['(:any)/staff/students'] = 'Staff/students';
$route['(:any)/hod/students'] = 'Hod/students';

// Student password reset routes
$route['(:any)/principal/reset_password_student'] = 'Principal/reset_password_student';
$route['(:any)/staff/reset_password_student'] = 'Staff/reset_password_student';
$route['(:any)/hod/reset_password_student'] = 'Hod/reset_password_student';

// Music Groups Management routes
$route['(:any)/groups'] = 'Groups/groups';
$route['(:any)/groups/add'] = 'Groups/add_group';
$route['(:any)/groups/edit/(:num)'] = 'Groups/edit_group/$2';
$route['(:any)/groups/delete/(:num)'] = 'Groups/delete_group/$2';
$route['(:any)/groups/group_students/(:num)'] = 'Groups/group_students/$2';
$route['(:any)/groups/add_students_to_group/(:num)'] = 'Groups/add_students_to_group/$2';
$route['(:any)/groups/remove_student/(:num)/(:num)'] = 'Groups/remove_student_from_group/$2/$3';

// Course Management routes
$route['(:any)/courses'] = 'Course/index';
$route['(:any)/courses/add'] = 'Course/add';
$route['(:any)/courses/edit/(:num)'] = 'Course/edit/$2';
$route['(:any)/courses/delete/(:num)'] = 'Course/delete/$2';

// Course Modules routes
$route['(:any)/courses/modules/(:num)'] = 'Course/modules/$2';
$route['(:any)/courses/add_module'] = 'Course/add_module';
$route['(:any)/courses/edit_module/(:num)/(:num)'] = 'Course/edit_module/$2/$3';
$route['(:any)/courses/delete_module/(:num)/(:num)'] = 'Course/delete_module/$2/$3';

// Course Lessons routes
$route['(:any)/courses/lessons/(:num)/(:num)'] = 'Course/lessons/$2/$3';
$route['(:any)/courses/add_lesson'] = 'Course/add_lesson';
$route['(:any)/courses/edit_lesson/(:num)/(:num)/(:num)'] = 'Course/edit_lesson/$2/$3/$4';
$route['(:any)/courses/delete_lesson/(:num)/(:num)/(:num)'] = 'Course/delete_lesson/$2/$3/$4';

// Course Enrollments routes
$route['(:any)/courses/enrollments/(:num)'] = 'Course/enrollments/$2';
$route['(:any)/courses/view_lesson/(:num)/(:num)/(:num)'] = 'Course/view_lesson/$2/$3/$4';

$route['(:any)/courses/enroll_student'] = 'Course/enroll_student';
$route['(:any)/courses/update_enrollment_status/(:num)/(:any)'] = 'Course/update_enrollment_status/$2/$3';
$route['(:any)/courses/unenroll_student/(:num)'] = 'Course/unenroll_student/$2';

// Course Students overview route
$route['(:any)/courses/students'] = 'Course/students';

// System-level Course Management (SuperAdmin only)
$route['(:any)/system_courses'] = 'Course/system_courses';
$route['(:any)/courses/add_colleges/(:num)'] = 'Course/add_colleges/$2';
$route['(:any)/courses/get_colleges'] = 'Course/get_colleges';
$route['(:any)/courses/get_shared_colleges/(:num)'] = 'Course/get_shared_colleges/$2';
$route['(:any)/courses/assign_course'] = 'Course/assign_course';
$route['(:any)/courses/remove_course_assign/(:num)/(:num)'] = 'Course/remove_course_assign/$2/$3';
$route['(:any)/courses/get_exclude_colleges/(:num)/(:num)'] = 'Course/get_exclude_colleges/$2/$3';

// Musical Instruments/Inventory Management routes
$route['(:any)/inventory'] = 'Inventory/index';
$route['(:any)/inventory/create'] = 'Inventory/create';
$route['(:any)/inventory/update/(:num)'] = 'Inventory/update/$2';
$route['(:any)/inventory/view/(:num)'] = 'Inventory/get_instrument/$2';
$route['(:any)/inventory/delete/(:num)'] = 'Inventory/delete/$2';
$route['(:any)/inventory/issue/(:num)'] = 'Inventory/issue/$2';
$route['(:any)/inventory/issue'] = 'Inventory/issue';
$route['(:any)/inventory/return/(:num)'] = 'Inventory/return_item/$2';
$route['(:any)/inventory/issues'] = 'Inventory/issues';
$route['(:any)/inventory/reports'] = 'Inventory/reports';
$route['(:any)/inventory/categories'] = 'Inventory/categories';
$route['(:any)/inventory/add_category'] = 'Inventory/add_category';
$route['(:any)/inventory/update_category'] = 'Inventory/update_category';

// Musical Instruments API routes (for AJAX operations)
$route['(:any)/api/inventory/create'] = 'Inventory/create_api';
$route['(:any)/api/inventory/update/(:num)'] = 'Inventory/update_api/$2';
$route['(:any)/api/inventory/issue'] = 'Inventory/issue_api';
$route['(:any)/api/inventory/return'] = 'Inventory/return_item_api';
$route['(:any)/api/get_students'] = 'Inventory/get_students_api';
$route['(:any)/api/get_staff'] = 'Inventory/get_staff_api';
$route['(:any)/api/get_issue_details'] = 'Inventory/get_issue_details_api';
$route['(:any)/api/return_instrument'] = 'Inventory/return_instrument_api';

// Announcements Management routes
$route['(:any)/announcements'] = 'Announcement/index';
$route['(:any)/announcements/create'] = 'Announcement/create';
$route['(:any)/announcements/edit/(:num)'] = 'Announcement/edit/$2';
$route['(:any)/announcements/delete/(:num)'] = 'Announcement/delete/$2';
$route['(:any)/announcements/view/(:num)'] = 'Announcement/view/$2';
$route['(:any)/announcements/user'] = 'Announcement/get_user_announcements';

// Reports Management routes
$route['(:any)/reports'] = 'Report/index';
$route['(:any)/report'] = 'Report/index'; // Singular alias
$route['(:any)/reports/student/(:num)'] = 'Report/student_detail/$2';
$route['(:any)/reports/course/(:num)'] = 'Report/course_detail/$2';
$route['(:any)/reports/export/(:any)/(:num)'] = 'Report/export_csv/$2/$3';
$route['(:any)/reports/export/(:any)'] = 'Report/export_csv/$2';
$route['(:any)/reports/dashboard'] = 'Report/dashboard';
$route['(:any)/reports/dashboard/export/(:any)'] = 'Report/export_dashboard/$2';
$route['(:any)/reports/kpis'] = 'Report/kpis';

// Settings routes
$route['(:any)/settings'] = 'College/view';
$route['(:any)/college/view'] = 'College/view';
$route['(:any)/college/edit/(:num)'] = 'College/edit/$2';
