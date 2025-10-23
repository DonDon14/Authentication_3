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
$routes->get('/contributions/analytics', 'Contributions::analytics');
$routes->post('/contributions/add', 'Contributions::add');
$routes->post('/contributions/update/(:num)', 'Contributions::update/$1');
$routes->post('/contributions/toggle/(:num)', 'Contributions::toggle/$1');
$routes->post('/contributions/delete/(:num)', 'Contributions::delete/$1');
$routes->get('/contributions/get/(:num)', 'Contributions::get/$1');

// Payment routes
$routes->get('/payments/history', 'Payments::history');
$routes->get('/payments/test-export', 'Payments::testExport');
$routes->get('/payments/export', 'Payments::exportPayments');
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
$routes->get('/payments/getStudentHistory/(:num)/(:any)', 'Payments::getStudentHistory/$1/$2');
$routes->get('/payments/getPaymentDetails/(:num)', 'Payments::getPaymentDetails/$1');
$routes->get('/payments/generateQR/(:num)/(:any)/(:num)', 'Payments::generateQR/$1/$2/$3');
$routes->get('/payments/verifyQR/(:num)', 'Payments::verifyQR/$1');

// Profile routes
$routes->get('/profile', 'Auth::profile');

// Alternative routes to handle routing issues
$routes->get('payments/viewContribution/(:num)', 'Payments::viewContribution/$1');
$routes->get('Authentication_3/payments/viewContribution/(:num)', 'Payments::viewContribution/$1');

// Analytics routes
$routes->get('/analytics', 'Analytics::index');
$routes->get('/analytics/export/(:any)', 'Analytics::export/$1');

// Students routes
$routes->get('/students', 'Students::index');
$routes->get('/students/details/(:any)', 'Students::details/$1');
$routes->get('/students/export/(:any)', 'Students::exportStudentData/$1');
$routes->post('/students/update', 'Students::update');
$routes->get('/students/getStudentMetadata/(:any)', 'Students::getStudentMetadata/$1');

// Profile and Settings routes
$routes->get('/profile', 'Auth::profile');
$routes->post('/profile/update', 'Auth::updateProfile');
$routes->post('/profile/upload-picture', 'Auth::uploadPicture');
$routes->post('/auth/uploadPicture', 'Auth::uploadPicture'); // Alternative route
$routes->post('/auth/updateProfile', 'Auth::updateProfile'); // Alternative route
$routes->get('/settings', 'Auth::settings');

// Test route for profile pictures
$routes->get('/test-profile-picture/(:any)', 'Auth::testProfilePicture/$1');

// Announcements routes
$routes->get('/announcements', 'Announcements::index');
$routes->get('/announcements/student-view', 'Announcements::studentView');
$routes->post('/announcements/create', 'Announcements::create');
$routes->post('/announcements/update/(:num)', 'Announcements::update/$1');
$routes->delete('/announcements/delete/(:num)', 'Announcements::delete/$1');
$routes->get('/announcements/get/(:num)', 'Announcements::get/$1');
$routes->post('/announcements/archive/(:num)', 'Announcements::archive/$1');
$routes->get('/announcements/search', 'Announcements::search');

// Test routes for debugging
$routes->get('/test-announcements', 'TestAnnouncements::testCreate');
$routes->get('/test/header', 'TestController::headerTest');
$routes->get('/payments/renderReceiptPartial/(:num)', 'Payments::renderReceiptPartial/$1');
$routes->get('/payments/getQRCode/(:any)/(:num)', 'Payments::getQRCode/$1/$2');
$routes->get('/test/receipt', 'TestController::receiptTest');
$routes->get('/test/receiptTest', 'TestController::receiptTest');
// Test routes for sidebar
$routes->get('/test/sidebar', 'TestController::sidebarTest');
$routes->get('test/sidebar', 'TestController::sidebarTest');
$routes->get('Authentication_3/test/sidebar', 'TestController::sidebarTest');
$routes->get('/Authentication_3/test/sidebar', 'TestController::sidebarTest');
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