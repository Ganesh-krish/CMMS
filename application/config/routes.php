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
