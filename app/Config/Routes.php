<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index'); // Default to login page
$routes->post('/login', 'Auth::processLogin');
$routes->get('/register', 'Auth::showRegisterForm');
$routes->post('/register', 'Auth::processRegister');
$routes->get('/dashboard', 'Auth::dashboard');
$routes->get('/logout', 'Auth::logout');
$routes->get('/verify-email/(:any)', 'Auth::verifyEmail/$1');
$routes->get('/forgot-password', 'Auth::forgotPassword');
$routes->post('/forgot-password', 'Auth::processForgotPassword');
$routes->get('/reset-password/(:any)', 'Auth::showResetPasswordForm/$1');
$routes->post('/reset-password', 'Auth::processResetPassword');
$routes->get('/payments', 'Payments::index');

// Contributions routes
$routes->get('/contributions', 'Contributions::index');
$routes->post('/contributions/add', 'Contributions::add');
$routes->post('/contributions/update/(:num)', 'Contributions::update/$1');
$routes->post('/contributions/toggle/(:num)', 'Contributions::toggle/$1');
$routes->post('/contributions/delete/(:num)', 'Contributions::delete/$1');
$routes->get('/contributions/get/(:num)', 'Contributions::get/$1');

// Payment routes
$routes->get('/payments/history', 'Payments::history');
$routes->get('/payments', 'Payments::index');
$routes->post('/payments/save', 'Payments::save');
$routes->post('/payments/searchByQR', 'Payments::searchByQR');
$routes->post('/payments/generateReceipt/(:num)', 'Payments::generateReceipt/$1');
$routes->get('/payments/downloadReceipt/(:num)', 'Payments::downloadReceipt/$1');
$routes->post('/payments/verifyPayment', 'Payments::verifyPayment');
$routes->get('/payments/getPayments/(:num)', 'Payments::getPayments/$1');
$routes->get('/payments/getPaymentDetails/(:num)', 'Payments::getPaymentDetails/$1');
$routes->get('/payments/viewContribution/(:num)', 'Contributions::viewContribution/$1');
$routes->get('/writable/uploads/(:any)', 'Payments::serveUpload/$1');
$routes->get('/payments/test/(:num)', 'Payments::test/$1'); // Test route
$routes->get('/payments/testQR/(:num)', 'Payments::testQR/$1');
$routes->get('/payments/testQRSimple', 'Payments::testQRSimple');
$routes->get('/payments/serveUpload/(:any)', 'Payments::serveUpload/$1');
$routes->post('/payments/getPaymentStatus', 'Payments::getPaymentStatus');
$routes->get('/payments/partial', 'Payments::partialPayments');
$routes->get('/payments/addPartial', 'Payments::addPartialPayment');
$routes->get('/payments/cleanup', 'Payments::cleanupPaymentStatuses');
$routes->get('/payments/fixPartialPayments', 'Payments::fixPartialPayments');
$routes->get('/contributions/getStudentPaymentHistory/(:num)/(:any)', 'Contributions::getStudentPaymentHistory/$1/$2');
$routes->get('/payments/studentPaymentHistory/(:num)/(:any)', 'Contributions::getStudentPaymentHistory/$1/$2');

// Alternative routes to handle routing issues
$routes->get('payments/viewContribution/(:num)', 'Payments::viewContribution/$1');
$routes->get('Authentication_3/payments/viewContribution/(:num)', 'Payments::viewContribution/$1');

// Profile and Settings routes
$routes->get('/profile', 'Auth::profile');
$routes->post('/profile/update', 'Auth::updateProfile');
$routes->get('/settings', 'Auth::settings');
$routes->get('payments/getQRCode/(:any)/(:num)', 'Payments::getQRCode/$1/$2');
//$routes->get('/public/js/contribution_details.js', 'Home::serveJs');

// Student/User Authentication Routes
$routes->get('/user/login', 'UserAuth::login');
$routes->post('/user/login', 'UserAuth::processLogin');
$routes->get('/user/dashboard', 'UserAuth::dashboard');
$routes->get('/user/payment-history', 'UserAuth::paymentHistory');
$routes->get('/user/payment-details/(:num)', 'UserAuth::getPaymentDetails/$1');
$routes->get('/user/logout', 'UserAuth::logout');
$routes->post('/user/check-payment-status/(:num)', 'UserAuth::checkPaymentStatus/$1');
$routes->get('/user/profile', 'UserAuth::profile');
$routes->get('/user/help', 'UserAuth::help');