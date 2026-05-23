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

$route['(:any)/authentication'] = 'authentication/index/$1';
$route['(:any)/forgot'] = 'authentication/forgot/$1';
$route['(:any)/teachers'] = 'home/teachers';
$route['(:any)/sovapoti'] = 'home/sovapoti';
$route['(:any)/principal'] = 'home/principal';




$route['(:any)/privacy'] = 'home/privacy';
$route['(:any)/terms'] = 'home/terms';
$route['(:any)/video'] = 'home/video';





$route['(:any)/events'] = 'home/events';
$route['(:any)/news'] = 'home/news/';
$route['(:any)/about'] = 'home/about';
$route['(:any)/faq'] = 'home/faq';
$route['(:any)/admission'] = 'home/admission';
$route['(:any)/gallery'] = 'home/gallery';
$route['(:any)/contact'] = 'home/contact';
$route['(:any)/admit_card'] = 'home/admit_card';
$route['(:any)/exam_results'] = 'home/exam_results';
$route['(:any)/certificates'] = 'home/certificates';
$route['(:any)/page/(:any)'] = 'home/page/$2';
$route['(:any)/gallery_view/(:any)'] = 'home/gallery_view/$2';
$route['(:any)/event_view/(:num)'] = 'home/event_view/$2';
$route['(:any)/news_view/(:any)'] = 'home/news_view/$2';

$route['dashboard'] = 'dashboard/index';
$route['branch'] = 'branch/index';
$route['attachments'] = 'attachments/index';
$route['homework'] = 'homework/index';
$route['onlineexam'] = 'onlineexam/index';
$route['hostels'] = 'hostels/index';
$route['event'] = 'event/index';
$route['accounting'] = 'accounting/index';
$route['school_settings'] = 'school_settings/index';
$route['role'] = 'role/index';
$route['sessions'] = 'sessions/index';
$route['translations'] = 'translations/index';
$route['cron_api'] = 'cron_api/index';
$route['modules'] = 'modules/index';
$route['system_student_field'] = 'system_student_field/index';
$route['custom_field'] = 'custom_field/index';
$route['backup'] = 'backup/index';
$route['advance_salary'] = 'advance_salary/index';
$route['system_update'] = 'system_update/index';
$route['certificate'] = 'certificate/index';
$route['payroll'] = 'payroll/index';
$route['leave'] = 'leave/index';
$route['award'] = 'award/index';
$route['classes'] = 'classes/index';
$route['student_promotion'] = 'student_promotion/index';
$route['live_class'] = 'live_class/index';
$route['exam'] = 'exam/index';
$route['profile'] = 'profile/index';
$route['sections'] = 'sections/index';

$route['authentication'] = 'authentication/index';
// SS-PHASE1-PATCH: routes - tenant resolution smoke-test endpoint
// REMOVE before production deploy. Keeps the catch-all (:any) route
// from swallowing /ss_probe and routing it to home/index.
//$route['ss_probe'] = 'ss_probe/index';

// SS-PHASE-P9-v3: super-admin Landing-page editor.  MUST appear before
// the catch-all `saas/(:any) => saas/$1` rule below, otherwise CI3
// would route `/saas/landing` to `Saas::landing()` (which doesn't exist).
$route['saas/landing']                   = 'landing_admin/index';
$route['saas/landing/save']              = 'landing_admin/save';
$route['saas/landing/set_variant/(:any)'] = 'landing_admin/set_variant/$1';
$route['saas/landing/preview/(:any)']    = 'landing_admin/preview/$1';

// SS-PHASE-P5P8-v3: explicit SaaS routes MUST appear before the catch-all
// `(:any) => home/index/$1` below, otherwise single-segment URLs like
// `/signup` are swallowed and rendered as the apex tenant's home page.
$route['signup']                       = 'signup/index';
$route['signup/(:any)']                = 'signup/$1';
$route['signup/(:any)/(:any)']         = 'signup/$1/$2';
$route['saas']                         = 'saas/index';
$route['saas/(:any)']                  = 'saas/$1';
$route['saas/(:any)/(:any)']           = 'saas/$1/$2';
$route['custom_domain']                = 'custom_domain/list';
$route['custom_domain/(:any)']         = 'custom_domain/$1';
$route['custom_domain/(:any)/(:any)']  = 'custom_domain/$1/$2';
$route['subscription']                 = 'subscription/index';
$route['subscription/(:any)']          = 'subscription/$1';
$route['sslcommerz']                   = 'sslcommerz/index';
$route['sslcommerz/(:any)']            = 'sslcommerz/$1';

// SS-PHASE-P5.2: SaaS billing — provider-agnostic pay flow.
// pay/<invoice_id>             — provider selector
// start/<invoice_id>/<code>    — POST: kick off charge
// success|fail|cancel/<code>   — gateway redirect-back
// ipn/<code>                   — gateway server-to-server notification
$route['billing/pay/(:num)']                = 'saas_billing/pay/$1';
$route['billing/start/(:num)/(:any)']       = 'saas_billing/start/$1/$2';
$route['billing/success/(:any)']            = 'saas_billing/success/$1';
$route['billing/fail/(:any)']               = 'saas_billing/fail/$1';
$route['billing/cancel/(:any)']             = 'saas_billing/cancel/$1';
$route['billing/ipn/(:any)']                = 'saas_billing/ipn/$1';
// Manual / bank-transfer flow (tenant submits proof of payment for super-admin approval).
$route['billing/manual/(:num)']             = 'saas_billing/manual/$1';
$route['billing/submit_manual/(:num)']      = 'saas_billing/submit_manual/$1';

$route['home'] = 'home/index';
$route['landing'] = 'landing/index';
$route['404_override'] = 'errors';

// SS-PHASE-P9-v1: apex domain (`smartschool.bd` / `www.smartschool.bd`) serves
// the marketing landing page; every tenant subdomain (`<sub>.smartschool.bd`)
// and custom domain keeps hitting Home::index() unchanged.
$ss_host = preg_replace('/^www\./', '', strtolower((string)($_SERVER['HTTP_HOST'] ?? '')));
$ss_host = preg_replace('/:\d+$/', '', $ss_host);
$route['default_controller'] = ($ss_host === 'smartschool.bd') ? 'landing' : 'home';
unset($ss_host);

$route['(:any)'] = 'home/index/$1';
$route['translate_uri_dashes'] = FALSE;
