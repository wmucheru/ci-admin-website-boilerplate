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
|	https://codeigniter.com/user_guide/general/routing.html
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
$route['default_controller'] = 'site';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

/**
 * 
 * SITE
 * 
*/

# Site
$route['home'] = 'site/index';
$route['about'] = 'site/about';
$route['contact'] = 'site/contact';
$route['contact/message'] = 'site/sendMessage';

/**
 * 
 * BACKEND
 * 
*/

# Admin
$route['admin'] = 'auth';
$route['accounts/signup'] = 'auth/signup';
$route['accounts/login'] = 'auth/index';
$route['logout'] = 'auth/logout';

# Users
$route['admin/users'] = 'admin/users/index';
$route['admin/users/(:any)'] = 'admin/users/index/$1';
$route['admin/user/save'] = 'admin/users/saveUser';
$route['admin/user/perm'] = 'admin/users/set_perms';
$route['admin/user/suspend/(:any)'] = 'admin/users/suspend_user/$1';
$route['admin/suspended-users'] = 'admin/users/suspended';

$route['admin/permissions'] = 'admin/users/permissions';
$route['admin/permissions/(:any)'] = 'admin/users/permissions/$1';
$route['admin/permissions/(:any)/(:any)'] = 'admin/users/permissions/$1/$2';
