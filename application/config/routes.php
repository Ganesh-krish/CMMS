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

// Convenience routes for admin dashboard
// Legacy routes now point to unified faculty Dashboard
$route['Dashboard'] = 'faculty/Dashboard/index';
$route['Dashboard/view'] = 'faculty/Dashboard/view';
$route['Dashboard/students'] = 'faculty/Dashboard/students';

// Student portal (CI views)
$route['student-portal/(:any)/login'] = 'StudentPortal/login/$1';
$route['student-portal/(:any)/authenticate'] = 'StudentPortal/authenticate/$1';
$route['student-portal/(:any)/dashboard'] = 'StudentPortal/dashboard/$1';
$route['student-portal/(:any)/logout'] = 'StudentPortal/logout/$1';

// Faculty/Student portals (migrated from college app)
// $route['(:any)/login/faculty'] = 'faculty/Login/faculty/$1'; // Removed as requested 
$route['(:any)/college'] = 'faculty/College'; 
// $route['(:any)/logout'] = 'faculty/Login/logout/$1'; // Faculty logout
// $route['logout'] = 'OAuth/logout'; // OAuth controller removed 
// Unified Dashboard Routes - Clean URLs, permission-based access
$route['(:any)/dashboard'] = 'faculty/Dashboard/index';  // Main dashboard (role-adaptive)
$route['(:any)/principal'] = 'faculty/Dashboard/index';  // Backward compatibility
$route['(:any)/view'] = 'faculty/Dashboard/view';        // Administrative view

// Unified Course Routes - Single controller for all course functionality
$route['(:any)/courses'] = 'faculty/Course/index';        // Main course management
$route['(:any)/course'] = 'faculty/Course/index';         // Backward compatibility
$route['(:any)/courses/add'] = 'faculty/Course/add';
$route['(:any)/course/add'] = 'faculty/Course/add';       // Backward compatibility
$route['(:any)/courses/edit/(:num)'] = 'faculty/Course/edit/$2';
$route['(:any)/course/edit/(:any)/(:any)'] = 'faculty/Course/edit/$2'; // Backward compatibility
$route['(:any)/course/edit/(:any)'] = 'faculty/Course/edit/$2';        // Backward compatibility
$route['(:any)/courses/delete/(:num)'] = 'faculty/Course/delete/$2';
$route['(:any)/course/delete/(:any)/(:any)'] = 'faculty/Course/delete/$2'; // Backward compatibility
$route['(:any)/courses/modules/(:num)'] = 'faculty/Course/modules/$2';
$route['(:any)/courses/add_module'] = 'faculty/Course/add_module';
$route['(:any)/courses/edit_module/(:num)/(:num)'] = 'faculty/Course/edit_module/$2/$3';
$route['(:any)/courses/delete_module/(:num)/(:num)'] = 'faculty/Course/delete_module/$2/$3';
$route['(:any)/courses/lessons/(:num)/(:num)'] = 'faculty/Course/lessons/$2/$3';
$route['(:any)/courses/add_lesson'] = 'faculty/Course/add_lesson';
$route['(:any)/courses/edit_lesson/(:num)/(:num)/(:num)'] = 'faculty/Course/edit_lesson/$2/$3/$4';
$route['(:any)/courses/delete_lesson/(:num)/(:num)/(:num)'] = 'faculty/Course/delete_lesson/$2/$3/$4';
$route['(:any)/courses/enrollments/(:num)'] = 'faculty/Course/enrollments/$2';
$route['(:any)/courses/enroll_student'] = 'faculty/Course/enroll_student';
$route['(:any)/courses/update_enrollment_status/(:num)/(:any)'] = 'faculty/Course/update_enrollment_status/$2/$3';
$route['(:any)/courses/unenroll_student/(:num)'] = 'faculty/Course/unenroll_student/$2';
$route['(:any)/courses/students'] = 'faculty/Course/students';
$route['(:any)/system_courses'] = 'faculty/Course/system_courses'; // System-level course management

// Legacy advanced routes (redirect to main system)
$route['(:any)/allcourses'] = 'faculty/Course/index'; // Redirect legacy to main
$route['(:any)/allcourses/modules/(:any)'] = 'faculty/Course/modules/$2';
$route['(:any)/vice_principal'] = 'faculty/Principal/vice_principal';
$route['(:any)/add_vice_principal'] = 'faculty/Principal/add_vice_principal';
$route['(:any)/add_hod'] = 'faculty/Principal/add_hod';
$route['(:any)/add_department_admin'] = 'faculty/Principal/add_department_admin';
$route['(:any)/edit_hod/(:num)'] = 'faculty/Principal/edit_hod/$1';
$route['(:any)/delete_hod/(:num)'] = 'faculty/Principal/delete_hod/$1';
$route['(:any)/add_staff'] = 'faculty/Principal/add_staff';
$route['(:any)/edit_vice_principal/(:num)'] = 'faculty/Principal/edit_vice_principal/$2';
$route['(:any)/delete_vice_principal/(:num)'] = 'faculty/Principal/delete_vice_principal/$2';
$route['(:any)/manage_hod'] = 'faculty/Principal/hod';   // Renamed for clarity
$route['(:any)/manage_staff'] = 'faculty/Principal/staff'; // Renamed for clarity

// Clean unified routes handle all access - no role prefixes needed
$route['(:any)/courses'] = 'faculty/Course';
$route['(:any)/courses/add'] = 'faculty/Course/add';
$route['(:any)/courses/edit/(:num)'] = 'faculty/Course/edit/$2';
$route['(:any)/courses/delete/(:num)'] = 'faculty/Course/delete/$2';
$route['(:any)/courses/modules/(:num)'] = 'faculty/Course/modules/$2';
$route['(:any)/courses/add_module'] = 'faculty/Course/add_module';
$route['(:any)/courses/edit_module/(:num)/(:num)'] = 'faculty/Course/edit_module/$2/$3';
$route['(:any)/courses/delete_module/(:num)/(:num)'] = 'faculty/Course/delete_module/$2/$3';
$route['(:any)/courses/lessons/(:num)/(:num)'] = 'faculty/Course/lessons/$2/$3';
$route['(:any)/courses/add_lesson'] = 'faculty/Course/add_lesson';
$route['(:any)/courses/edit_lesson/(:num)/(:num)/(:num)'] = 'faculty/Course/edit_lesson/$2/$3/$4';
$route['(:any)/courses/delete_lesson/(:num)/(:num)/(:num)'] = 'faculty/Course/delete_lesson/$2/$3/$4';
$route['(:any)/courses/enrollments/(:num)'] = 'faculty/Course/enrollments/$2';
$route['(:any)/courses/enroll_student'] = 'faculty/Course/enroll_student';
$route['(:any)/courses/update_enrollment_status/(:num)/(:any)'] = 'faculty/Course/update_enrollment_status/$2/$3';
$route['(:any)/courses/unenroll_student/(:num)'] = 'faculty/Course/unenroll_student/$2';

// Course students/enrollments overview
$route['(:any)/courses/students'] = 'faculty/Course/students';

$route['(:any)/students'] = 'faculty/Dashboard/students';  // Clean URL
$route['(:any)/profile'] = 'faculty/Dashboard/profile';  // Clean URL

// College management routes (SuperAdmin only)
$route['(:any)/college/view'] = 'faculty/College/view';  // College management
$route['(:any)/college/edit/(:num)'] = 'faculty/College/edit/$2';  // Edit college
$route['(:any)/college/add'] = 'faculty/College/add';  // Add college (disabled in single-college mode)
$route['(:any)/reset_password'] = 'faculty/Principal/reset_password';
$route['(:any)/reset_password_student'] = 'faculty/Principal/reset_password_student';
$route['(:any)/departments'] = 'faculty/Department/view';  // Department management
$route['(:any)/departments/add/(:num)'] = 'faculty/Department/add/$2';  // Add department to college
$route['(:any)/departments/edit/(:num)'] = 'faculty/Department/edit/$2';  // Edit department
$route['(:any)/departments/delete/(:num)'] = 'faculty/Department/delete/$2';  // Delete department
$route['(:any)/add_department'] = 'faculty/Principal/add_department';
$route['(:any)/edit_department/(:num)'] = 'faculty/Principal/edit_department/$2';
$route['(:any)/delete_department/(:num)'] = 'faculty/Principal/delete_department/$2';
// Clean URL routes for HOD and Staff
$route['(:any)/hod_dashboard'] = 'faculty/Hod/index';  // HOD main dashboard
$route['(:any)/hod_management'] = 'faculty/Hod/hod';  // HOD management
$route['(:any)/hod_view'] = 'faculty/Hod/hod';        // HOD view (alias)
$route['(:any)/hod_staff'] = 'faculty/Hod/staff';     // HOD staff management
$route['(:any)/hod_students'] = 'faculty/Hod/students'; // HOD student view
$route['(:any)/hod_reset_password'] = 'faculty/Hod/reset_password';
$route['(:any)/hod_reset_password_student'] = 'faculty/Hod/reset_password_student';

$route['(:any)/staff_dashboard'] = 'faculty/Staff/index'; // Staff main dashboard
$route['(:any)/staff_management'] = 'faculty/Staff/staff'; // Staff management
$route['(:any)/staff_view'] = 'faculty/Staff/staff';       // Staff view (alias)
$route['(:any)/staff_students'] = 'faculty/Staff/students'; // Staff student view

// Backward compatibility routes for HOD and Staff
$route['(:any)/hod'] = 'faculty/Hod/index'; // HOD backward compatibility
$route['(:any)/hod/hod'] = 'faculty/Hod/hod';
$route['(:any)/hod/view'] = 'faculty/Hod/hod';
$route['(:any)/hod/staff'] = 'faculty/Hod/staff';
$route['(:any)/hod/students'] = 'faculty/Hod/students';

$route['(:any)/staff'] = 'faculty/Staff/index'; // Staff backward compatibility
$route['(:any)/staff/staff'] = 'faculty/Staff/staff';
$route['(:any)/staff/view'] = 'faculty/Staff/staff';
$route['(:any)/staff/students'] = 'faculty/Staff/students';
$route['(:any)/hod/groups'] = 'faculty/Hod/groups';

// Course functionality - redirect basic operations to Courses controller
$route['(:any)/course'] = 'faculty/Course/index';  // Redirect to Courses index
$route['(:any)/allcourses'] = 'faculty/Course/allcourses';  // Keep advanced view
$route['(:any)/course/new'] = 'faculty/Course/add';  // Redirect to Courses add
$route['(:any)/course/add'] = 'faculty/Course/add';  // Redirect to Courses add
$route['(:any)/course/edit/(:any)/(:any)'] = 'faculty/Course/edit/$2';  // Redirect to Courses edit
$route['(:any)/course/edit/(:any)'] = 'faculty/Course/edit/$2';  // Redirect to Courses edit
$route['(:any)/course/delete/(:any)/(:any)'] = 'faculty/Course/delete/$2';  // Redirect to Courses delete
$route['(:any)/course/view_students/(:any)'] = 'faculty/Course/enrollments/$2';  // Redirect to Courses enrollments
$route['(:any)/course/modules/(:any)'] = 'faculty/Course/modules/$2';  // Redirect to Courses modules
$route['(:any)/allcourses/modules/(:any)'] = 'faculty/Course/allcourses_modules/$2';  // Keep advanced
$route['(:any)/allspecialcourses/assign_students/(:any)'] = 'faculty/Course/assign_students/$2';  // Keep advanced
$route['(:any)/course/add_module'] = 'faculty/Course/add_module';  // Redirect to Courses
$route['(:any)/course/edit_module/(:any)/(:any)'] = 'faculty/Course/edit_module/$2/$3';  // Redirect to Courses
$route['(:any)/course/delete_module/(:any)/(:any)'] = 'faculty/Course/delete_module/$2/$3';  // Redirect to Courses

// Batches & schedules
$route['(:any)/batches/(:num)'] = 'faculty/Batches/index/$2';
$route['(:any)/batches/create'] = 'faculty/Batches/create';
$route['(:any)/batches/update/(:num)'] = 'faculty/Batches/update/$2';
$route['(:any)/batches/schedules/(:num)'] = 'faculty/Batches/schedules/$2';
$route['(:any)/batches/schedules/add'] = 'faculty/Batches/add_schedule';

$route['(:any)/staff/reset_password'] = 'faculty/Staff/reset_password'; 
$route['(:any)/staff/reset_password_student'] = 'faculty/Staff/reset_password_student'; 

// Groups
$route['(:any)/staff/groups'] = 'faculty/Staff/groups';
$route['(:any)/staff/addMemberstoGroup'] = 'faculty/Staff/addMemberstoGroup';
$route['(:any)/groups'] = 'faculty/Groups';
$route['(:any)/groups/add'] = 'faculty/Groups/add';
$route['(:any)/groups/edit/(:num)'] = 'faculty/Groups/edit/$2';
$route['(:any)/groups/delete_group'] = 'faculty/Groups/deleteGroup';
$route['(:any)/groups/group_students/(:num)'] = 'faculty/Groups/group_students/$2';
$route['(:any)/groups/addMemberstoGroup'] = 'faculty/Groups/addMemberstoGroup';

// Reports
$route['report'] = 'faculty/Report';
$route['admin/report'] = 'faculty/Report';
$route['staff/report'] = 'faculty/Report';
$route['hod/report'] = 'faculty/Report';
$route['principal/report'] = 'faculty/Report';
$route['(:any)/report/kpis'] = 'faculty/Report/kpis';
$route['(:any)/report/(:any)'] = 'faculty/Report/$2';
$route['(:any)/report/dashboard'] = 'faculty/Report/dashboard';

// Instrument Inventory
$route['(:any)/inventory'] = 'faculty/Inventory/index';
$route['(:any)/inventory/create'] = 'faculty/Inventory/create';
$route['(:any)/inventory/update'] = 'faculty/Inventory/update';
$route['(:any)/inventory/get_instrument/(:num)'] = 'faculty/Inventory/get_instrument/$2';
$route['(:any)/inventory/issue'] = 'faculty/Inventory/issue';
$route['(:any)/inventory/return_item'] = 'faculty/Inventory/return_item';
$route['(:any)/inventory/maintenance'] = 'faculty/Inventory/maintenance';
$route['(:any)/inventory/issues'] = 'faculty/Inventory/issues';
$route['(:any)/inventory/maintenance_logs'] = 'faculty/Inventory/maintenance_logs';
$route['(:any)/inventory/reports'] = 'faculty/Inventory/reports';

// Announcement routes
$route['(:any)/announcements'] = 'faculty/Announcement/index';
$route['(:any)/announcements/create'] = 'faculty/Announcement/create';
$route['(:any)/announcements/edit/(:num)'] = 'faculty/Announcement/edit/$2';
$route['(:any)/announcements/delete/(:num)'] = 'faculty/Announcement/delete/$2';
$route['(:any)/announcements/view/(:num)'] = 'faculty/Announcement/view/$2';
$route['(:any)/announcements/get_user_announcements'] = 'faculty/Announcement/get_user_announcements';

// Student routes
$route['(:any)/student/login'] = 'faculty/Student/login';
$route['(:any)/student/check_auth'] = 'faculty/Student/check_auth';
$route['(:any)/student/auth_logout'] = 'faculty/Student/auth_logout';
$route['(:any)/student/courses'] = 'faculty/Student/courses';
$route['(:any)/student/dashboard'] = 'faculty/Student/dashboard';
$route['(:any)/student/course_modules/(:any)'] = 'faculty/Student/course_modules/$2';
$route['(:any)/student/logo'] = 'faculty/Student/logo';
$route['(:any)/student/banner'] = 'faculty/Student/banner';
$route['(:any)/student/lessons/(:num)'] = 'faculty/Student/lessons/$2';
$route['(:any)/lessons'] = 'faculty/Lessons/index';
$route['(:any)/lessons/module/(:num)'] = 'faculty/Lessons/by_module/$2';
$route['(:any)/lessons/create'] = 'faculty/Lessons/create';
$route['(:any)/lessons/update/(:num)'] = 'faculty/Lessons/update/$2';
$route['(:any)/lessons/delete/(:num)'] = 'faculty/Lessons/delete/$2';
$route['(:any)/lessons/upload_attachment'] = 'faculty/Lessons/upload_attachment';