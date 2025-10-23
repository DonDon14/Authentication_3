<?php

namespace App\Controllers;

use App\Models\UsersModel;

class Auth extends BaseController
{
    protected $usersModel;

    public function __construct()
    {
        $this->usersModel = new UsersModel();
    }
    public function showRegisterForm()
    {
        return view('auth/register'); // app/Views/auth/register.php
    }

    public function processRegister()
    {
        try {
            // Debug: Log all POST data
            log_message('debug', 'POST data: ' . print_r($this->request->getPost(), true));

            $name = $this->request->getPost('fullname');
            $email = $this->request->getPost('email');
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            $confirmPassword = $this->request->getPost('confirmPassword');

            // Debug: Log individual values
            log_message('debug', "Fullname: '$name' (" . strlen($name ?? '') . " chars)");
            log_message('debug', "Username: '$username' (" . strlen($username ?? '') . " chars)");
            log_message('debug', "Email: '$email' (" . strlen($email ?? '') . " chars)");
            log_message('debug', "Password: '$password' (" . strlen($password ?? '') . " chars)");
            log_message('debug', "ConfirmPassword: '$confirmPassword' (" . strlen($confirmPassword ?? '') . " chars)");

            // Validate input
            if (empty($name) || empty($username) || empty($email) || empty($password) || empty($confirmPassword)) {
                log_message('debug', 'Validation failed - one or more fields are empty');
                return $this->response->setJSON(['message' => 'All fields are required']);
            }

            if ($password !== $confirmPassword) {
                return $this->response->setJSON(['message' => 'Passwords do not match']);
            }

            // Password complexity validation
            if (strlen($password) < 8) {
                return $this->response->setJSON(['message' => 'Password must be at least 8 characters']);
            }

            if (!preg_match('/[A-Z]/', $password)) {
                return $this->response->setJSON(['message' => 'Password must contain at least one uppercase letter']);
            }

            if (!preg_match('/[a-z]/', $password)) {
                return $this->response->setJSON(['message' => 'Password must contain at least one lowercase letter']);
            }

            if (!preg_match('/[0-9]/', $password)) {
                return $this->response->setJSON(['message' => 'Password must contain at least one number']);
            }

            if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\?]/', $password)) {
                return $this->response->setJSON(['message' => 'Password must contain at least one special character']);
            }

            // Check for uniqueness in database using UsersModel methods
            
            // Check if email already exists
            $emailCheck = $this->usersModel->findByEmail($email);
            if ($emailCheck) {
                if ($emailCheck['email_verified'] == 1) {
                    // Verified account exists
                    return $this->response->setJSON(['message' => 'Email address is already registered']);
                } else {
                    // Unverified account exists - check if expired
                    $isExpired = strtotime($emailCheck['verification_expires']) < time();
                    
                    if ($isExpired) {
                        // Expired - delete old account and allow new registration
                        $this->usersModel->delete($emailCheck['id']);
                        log_message('info', 'Deleted expired unverified account: ' . $email);
                    } else {
                        // Still valid - offer to resend
                        return $this->response->setJSON([
                            'message' => 'An unverified account with this email already exists. Please check your email or try again after ' . date('M j, Y g:i A', strtotime($emailCheck['verification_expires'])),
                            'action' => 'resend_available'
                        ]);
                    }
                }
            }

            // Check if username already exists
            if (!$this->usersModel->isUsernameAvailable($username)) {
                return $this->response->setJSON(['message' => 'Username is already taken']);
            }

            // Check if fullname already exists
            $nameCheck = $this->usersModel->where('name', $name)->first();
            if ($nameCheck) {
                return $this->response->setJSON(['message' => 'Full name is already registered']);
            }

            // Generate verification token
            $verificationToken = bin2hex(random_bytes(32));
            $verificationExpires = date('Y-m-d H:i:s', strtotime('+24 hours'));

            // Save to database with verification data using UsersModel
            $this->usersModel->createUser([
                'name' => $name,
                'email' => $email,
                'username' => $username,
                'password' => $password, // Will be hashed in createUser method
                'email_verified' => 0,
                'verification_token' => $verificationToken,
                'verification_expires' => $verificationExpires
            ]);

            // Send verification email
            log_message('debug', 'About to send verification email to: ' . $email);
            $this->sendVerificationEmail($email, $name, $verificationToken);
            log_message('debug', 'Verification email sending process completed');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Registration successful! Please check your email to verify your account.',
                'redirect' => '/'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Registration error: ' . $e->getMessage());
            return $this->response->setJSON(['message' => 'Database error: ' . $e->getMessage()]);
        }
    }

    public function processLogin()
    {
        try {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            // Validate input
            if (empty($username) || empty($password)) {
                return $this->response->setJSON(['message' => 'Please fill in all fields']);
            }

            // Check user in database using UsersModel
            $user = $this->usersModel->findByUsername($username);

            if (!$user) {
                return $this->response->setJSON(['message' => 'Invalid username or password']);
            }

            // Verify password
            if (!password_verify($password, $user['password'])) {
                return $this->response->setJSON(['message' => 'Invalid username or password']);
            }

            // Check if email is verified
            if (!$user['email_verified']) {
                return $this->response->setJSON(['message' => 'Please verify your email address before logging in. Check your email for the verification link.']);
            }

            // Start session and store user data
            $session = session();
            $session->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'name' => $user['name'],
                'email' => $user['email'],
                'profile_picture' => $user['profile_picture'] ?? '',
                'logged_in' => true
            ]);

            // Log the login activity
            try {
                $activityModel = new \App\Models\ActivityModel();
                $activityModel->logActivity(
                    $user['id'],
                    \App\Models\ActivityModel::ACTIVITY_LOGIN,
                    $user['name'] . ' logged into the system',
                    'user',
                    $user['id'],
                    ['username' => $user['username'], 'login_time' => date('Y-m-d H:i:s')]
                );
            } catch (\Exception $activityError) {
                // Don't fail login if activity logging fails
                log_message('warning', 'Failed to log login activity: ' . $activityError->getMessage());
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Login successful! Redirecting to dashboard...',
                'redirect' => '/dashboard'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Login error: ' . $e->getMessage());
            return $this->response->setJSON(['message' => 'Login error: ' . $e->getMessage()]);
        }
    }

    public function dashboard()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/');
        }
        $session = session();

        try {
            // Load models
            $paymentModel = new \App\Models\PaymentModel();
            $contributionModel = new \App\Models\ContributionModel();
            $activityModel = new \App\Models\ActivityModel();

            // Get recent payments (last 10) - FIXED QUERY
            $recentPayments = $paymentModel->select('
                payments.*, 
                contributions.title as contribution_title,
                contributions.category
            ')
            ->join('contributions', 'contributions.id = payments.contribution_id', 'left')
            ->orderBy('payments.payment_date', 'DESC')
            ->limit(10)
            ->findAll();

            // Get dashboard stats - FIXED
            $totalCollectionsResult = $paymentModel->selectSum('amount_paid')->first();
            $totalCollections = $totalCollectionsResult['amount_paid'] ?? 0;

            $verifiedCount = $paymentModel->where('payment_status', 'completed')->countAllResults();
            $pendingCount = $paymentModel->where('payment_status', 'pending')->countAllResults();

            // Get today's payments count
            $todayCount = $paymentModel->where('DATE(payment_date)', date('Y-m-d'))->countAllResults();

            // Get recent activities for the activity timeline
            $recentActivities = $activityModel->getRecentActivities(15);
            
            // Format activities for display
            $formattedActivities = [];
            foreach ($recentActivities as $activity) {
                $formattedActivities[] = $activityModel->formatActivityForDisplay($activity);
            }

            // Get activity statistics
            $activityStats = $activityModel->getActivityStats(7);

            // Debug logging
            log_message('debug', 'Recent payments count: ' . count($recentPayments));
            log_message('debug', 'Total collections: ' . $totalCollections);
            log_message('debug', 'Recent activities count: ' . count($recentActivities));

        } catch (\Exception $e) {
            log_message('error', 'Dashboard data retrieval error: ' . $e->getMessage());
            $recentPayments = [];
            $totalCollections = 0;
            $verifiedCount = 0;
            $pendingCount = 0;
            $todayCount = 0;
            $formattedActivities = [];
            $activityStats = ['by_type' => [], 'today_count' => 0, 'period_days' => 7];
        }

        // Get user profile picture
        $userId = $session->get('user_id');
        $userModel = new \App\Models\UsersModel();
        $user = $userModel->find($userId);
        
        $profilePictureUrl = '';
        if (!empty($user['profile_picture'])) {
            $filename = basename($user['profile_picture']);
            $profilePictureUrl = base_url('test-profile-picture/' . $filename);
        }

        $data = [
            'name' => $session->get('name'),
            'email' => $session->get('email'),
            'profilePictureUrl' => $profilePictureUrl,
            'recentPayments' => $recentPayments,
            'recentActivities' => $formattedActivities,
            'activityStats' => $activityStats,
            'stats' => [
                'total_collections' => $totalCollections,
                'verified_count' => $verifiedCount,
                'pending_count' => $pendingCount,
                'today_count' => $todayCount
            ]
        ];

        return view('dashboard', $data);
    }

    private function isLoggedIn()
    {
        $session = session();
        return $session->get('logged_in') === true;
    }

    public function profile()
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to('/');
        }

        // Get user data from database
        $user = $this->usersModel->find($session->get('user_id'));
        
        // Debug the profile picture
        log_message('debug', 'Profile picture in DB: ' . ($user['profile_picture'] ?? 'NULL'));
        log_message('debug', 'User data: ' . json_encode($user));
        
        // Get profile picture URL
        $profilePictureUrl = '';
        if (!empty($user['profile_picture'])) {
            // Use the test route for now to bypass potential issues
            $profilePictureUrl = base_url('payments/serveUpload/' . basename($user['profile_picture']));
            log_message('debug', 'Profile picture URL: ' . $profilePictureUrl);
            
            // Check if file exists
            $filepath = WRITEPATH . 'uploads/' . $user['profile_picture'];
            $fileExists = file_exists($filepath);
            log_message('debug', 'Profile picture file exists: ' . ($fileExists ? 'YES' : 'NO') . ' at ' . $filepath);
        }
        
        return view('profile', [
            'username' => $user['username'],
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone'] ?? '',
            'profile_picture' => $profilePictureUrl
        ]);
    }

    public function updateProfile()
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not logged in']);
        }

        try {
            $userId = $session->get('user_id');
            
            $data = [
                'name' => $this->request->getPost('full_name'),
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'phone' => $this->request->getPost('phone')
            ];

            // Handle password update if provided
            $newPassword = $this->request->getPost('new_password');
            $currentPassword = $this->request->getPost('current_password');
            
            if (!empty($newPassword)) {
                // Verify current password
                $user = $this->usersModel->find($userId);
                if (!password_verify($currentPassword, $user['password'])) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Current password is incorrect']);
                }
                
                // Validate new password
                if (strlen($newPassword) < 8) {
                    return $this->response->setJSON(['success' => false, 'message' => 'New password must be at least 8 characters']);
                }
                
                $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            // Update user
            $this->usersModel->update($userId, $data);
            
            // Update session data
            $session->set([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email']
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Profile updated successfully']);

        } catch (\Exception $e) {
            log_message('error', 'Profile update error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error updating profile']);
        }
    }

    public function settings()
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to('/');
        }

        // Get user profile picture
        $userId = $session->get('user_id');
        $userModel = new \App\Models\UsersModel();
        $user = $userModel->find($userId);
        
        $profilePictureUrl = '';
        if (!empty($user['profile_picture'])) {
            $filename = basename($user['profile_picture']);
            $profilePictureUrl = base_url('test-profile-picture/' . $filename);
        }

        return view('settings', [
            'username' => $session->get('username'),
            'name' => $session->get('name'),
            'email' => $session->get('email'),
            'profilePictureUrl' => $profilePictureUrl
        ]);
    }

    public function uploadPicture()
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not logged in']);
        }

        try {
            $file = $this->request->getFile('profile_picture');
            
            if (!$file || !$file->isValid()) {
                return $this->response->setJSON(['success' => false, 'message' => 'No file uploaded or file is invalid']);
            }

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid file type. Please upload a JPEG, PNG, GIF, or WebP image.']);
            }

            // Validate file size (max 5MB)
            $maxSize = 5 * 1024 * 1024; // 5MB in bytes
            if ($file->getSize() > $maxSize) {
                return $this->response->setJSON(['success' => false, 'message' => 'File size too large. Maximum size is 5MB.']);
            }

            // Create uploads directory if it doesn't exist
            $uploadPath = WRITEPATH . 'uploads/profile_pictures/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generate unique filename
            $userId = $session->get('user_id');
            $extension = $file->getExtension();
            $fileName = 'profile_' . $userId . '_' . time() . '.' . $extension;

            // Move file to uploads directory
            if (!$file->move($uploadPath, $fileName)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to upload file']);
            }

            // Update database with profile picture path
            $profilePicturePath = 'profile_pictures/' . $fileName;
            $this->usersModel->update($userId, ['profile_picture' => $profilePicturePath]);

            // Update session if needed
            $session->set('profile_picture', $profilePicturePath);

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Profile picture updated successfully',
                'profile_picture' => base_url('payments/serveUpload/' . basename($profilePicturePath))
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Profile picture upload error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error uploading profile picture']);
        }
    }

    public function testProfilePicture($filename = null)
    {
        if (!$filename) {
            return 'No filename provided';
        }
        
        $filepath = WRITEPATH . 'uploads/profile_pictures/' . $filename;
        
        if (!file_exists($filepath)) {
            return 'File not found: ' . $filepath;
        }
        
        $fileinfo = pathinfo($filepath);
        $extension = strtolower($fileinfo['extension']);
        
        $contentTypes = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif'
        ];
        
        $contentType = $contentTypes[$extension] ?? 'application/octet-stream';
        
        return $this->response
            ->setHeader('Content-Type', $contentType)
            ->setHeader('Content-Length', filesize($filepath))
            ->setBody(file_get_contents($filepath));
    }

    public function forgotPassword()
    {
        return view('auth/forgotpassword'); // app/Views/auth/forgotpassword.php
    }
    public function processForgotPassword()
    {
        try {
            $email = $this->request->getPost('email');
            
            // Validate email
            if (empty($email)) {
                return $this->response->setJSON(['message' => 'Please enter your email address.']);
            }
            
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return $this->response->setJSON(['message' => 'Please enter a valid email address.']);
            }
            
            // Check if user exists using UsersModel
            $user = $this->usersModel->findByEmail($email);
            
            if (!$user) {
                // For security, don't reveal if email exists or not
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'If your email address exists in our system, you will receive a password reset link shortly.'
                ]);
            }
            
            // Generate reset token
            $resetToken = bin2hex(random_bytes(32));
            $resetExpires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Save reset token to database using UsersModel
            $this->usersModel->setResetToken($user['id'], $resetToken, $resetExpires);
            
            // Send password reset email
            $this->sendPasswordResetEmail($email, $user['name'], $resetToken);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Password reset link has been sent to your email address. Please check your inbox.'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Forgot password error: ' . $e->getMessage());
            return $this->response->setJSON(['message' => 'An error occurred. Please try again later.']);
        }
    }

    public function showResetPasswordForm($token)
    {
        // Validate token before showing form using UsersModel
        $user = $this->usersModel->findByResetToken($token);
        
        if (!$user) {
            return view('auth/verification_failed', [
                'message' => 'Invalid or expired password reset link. Please request a new password reset.'
            ]);
        }
        
        return view('auth/resetpassword', ['token' => $token]);
    }

    public function processResetPassword()
    {
        try {
            $token = $this->request->getPost('token');
            $password = $this->request->getPost('password');
            $confirmPassword = $this->request->getPost('confirmPassword');
            
            // Validate input
            if (empty($token) || empty($password) || empty($confirmPassword)) {
                return $this->response->setJSON(['message' => 'All fields are required.']);
            }
            
            // Password complexity validation
            if (strlen($password) < 8) {
                return $this->response->setJSON(['message' => 'Password must be at least 8 characters long.']);
            }

            if (!preg_match('/[A-Z]/', $password)) {
                return $this->response->setJSON(['message' => 'Password must contain at least one uppercase letter.']);
            }

            if (!preg_match('/[a-z]/', $password)) {
                return $this->response->setJSON(['message' => 'Password must contain at least one lowercase letter.']);
            }

            if (!preg_match('/[0-9]/', $password)) {
                return $this->response->setJSON(['message' => 'Password must contain at least one number.']);
            }

            if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\?]/', $password)) {
                return $this->response->setJSON(['message' => 'Password must contain at least one special character.']);
            }
            
            if ($password !== $confirmPassword) {
                return $this->response->setJSON(['message' => 'Passwords do not match.']);
            }
            
            // Check if token is valid and not expired using UsersModel
            $user = $this->usersModel->findByResetToken($token);
            
            if (!$user) {
                return $this->response->setJSON(['message' => 'Invalid or expired reset token. Please request a new password reset.']);
            }
            
            // Update password and clear reset token using UsersModel
            $this->usersModel->updatePassword($user['id'], $password);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Password has been reset successfully! Redirecting to login...'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Reset password error: ' . $e->getMessage());
            return $this->response->setJSON(['message' => 'An error occurred. Please try again later.']);
        }
    }


    public function logout()
    {
        $session = session();
        
        // Log logout activity before destroying session
        if ($session->get('logged_in')) {
            try {
                $activityModel = new \App\Models\ActivityModel();
                $activityModel->logActivity(
                    $session->get('user_id'),
                    \App\Models\ActivityModel::ACTIVITY_LOGOUT,
                    $session->get('name') . ' logged out of the system',
                    'user',
                    $session->get('user_id'),
                    ['logout_time' => date('Y-m-d H:i:s')]
                );
            } catch (\Exception $activityError) {
                // Don't fail logout if activity logging fails
                log_message('warning', 'Failed to log logout activity: ' . $activityError->getMessage());
            }
        }
        
        $session->destroy();
        return redirect()->to('/')->with('message', 'You have been logged out successfully');
    }

    // Test email functionality - Remove this after testing
    public function testEmail()
    {
        echo "<h1>Email Configuration Test</h1>";
        
        // Test 1: CodeIgniter Email Service
        echo "<h2>Test 1: CodeIgniter SMTP</h2>";
        try {
            $emailService = \Config\Services::email();
            
            $emailService->setTo('floroocero18@gmail.com');
            $emailService->setSubject('Test Email from CodeIgniter');
            $emailService->setMessage('<h1>Test Email</h1><p>This is a test email to verify SMTP configuration.</p>');
            
            if ($emailService->send()) {
                echo "<p style='color: green;'>✅ CodeIgniter email sent successfully!</p>";
            } else {
                echo "<p style='color: red;'>❌ CodeIgniter email failed.</p>";
                echo "<pre>" . $emailService->printDebugger() . "</pre>";
            }
            
        } catch (\Exception $e) {
            echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
        }
        
        // Test 2: Basic PHP mail
        echo "<h2>Test 2: Basic PHP Mail</h2>";
        $basicMailResult = mail(
            'floroocero18@gmail.com',
            'PHP Mail Test',
            'This is a test email using basic PHP mail() function.',
            'From: test@localhost'
        );
        
        if ($basicMailResult) {
            echo "<p style='color: green;'>✅ Basic PHP mail sent successfully!</p>";
        } else {
            echo "<p style='color: red;'>❌ Basic PHP mail failed.</p>";
        }
        
        // Display current email configuration
        echo "<h2>Current Email Configuration</h2>";
        $email = new \Config\Email();
        echo "<pre>";
        echo "Protocol: " . $email->protocol . "\n";
        echo "SMTP Host: " . $email->SMTPHost . "\n";
        echo "SMTP Port: " . $email->SMTPPort . "\n";
        echo "SMTP User: " . $email->SMTPUser . "\n";
        echo "SMTP Password: " . (strlen($email->SMTPPass) > 0 ? 'Set (' . strlen($email->SMTPPass) . ' characters)' : 'Not set') . "\n";
        echo "From Email: " . $email->fromEmail . "\n";
        echo "Mail Type: " . $email->mailType . "\n";
        echo "</pre>";
    }

    private function sendVerificationEmail($email, $name, $token)
    {
        $emailService = \Config\Services::email();
        
        $verificationLink = base_url("verify-email/{$token}");
        
        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Email Verification</title>
        </head>
        <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
            <table role='presentation' style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <td style='padding: 20px 0; text-align: center;'>
                        <table role='presentation' style='width: 600px; margin: 0 auto; background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
                            <!-- Header -->
                            <tr>
                                <td style='background-color: #007bff; padding: 30px 20px; text-align: center;'>
                                    <h1 style='margin: 0; color: white; font-size: 28px;'>Email Verification</h1>
                                </td>
                            </tr>
                            <!-- Content -->
                            <tr>
                                <td style='padding: 40px 30px;'>
                                    <h2 style='margin: 0 0 20px 0; color: #333; font-size: 24px;'>Hello {$name}!</h2>
                                    <p style='margin: 0 0 25px 0; color: #666; font-size: 16px; line-height: 1.5;'>
                                        Thank you for registering with us! To complete your registration, please verify your email address by clicking the button below:
                                    </p>
                                    <!-- Button -->
                                    <table role='presentation' style='margin: 30px 0;'>
                                        <tr>
                                            <td style='text-align: center;'>
                                                <a href='{$verificationLink}' style='background-color: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; font-size: 16px;'>Verify Email Address</a>
                                            </td>
                                        </tr>
                                    </table>
                                    <p style='margin: 25px 0 10px 0; color: #666; font-size: 14px;'>
                                        Or copy and paste this link into your browser:
                                    </p>
                                    <p style='margin: 0 0 25px 0; word-break: break-all;'>
                                        <a href='{$verificationLink}' style='color: #007bff; text-decoration: none;'>{$verificationLink}</a>
                                    </p>
                                    <p style='margin: 0; color: #999; font-size: 12px; font-style: italic;'>
                                        This link will expire in 24 hours.
                                    </p>
                                </td>
                            </tr>
                            <!-- Footer -->
                            <tr>
                                <td style='background-color: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #e9ecef;'>
                                    <p style='margin: 0; color: #666; font-size: 12px;'>
                                        If you didn't create an account, please ignore this email.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>";

        try {
            log_message('debug', 'Attempting to send verification email to: ' . $email);
            
            $emailService->setTo($email);
            $emailService->setSubject('Please verify your email address');
            $emailService->setMessage($message);
            
            $emailResult = $emailService->send();
            
            if ($emailResult) {
                log_message('debug', 'Verification email sent successfully to: ' . $email);
            } else {
                log_message('error', 'Failed to send verification email to: ' . $email);
                log_message('error', 'Email debug info: ' . $emailService->printDebugger(['headers', 'subject', 'body']));
                
                // Get more detailed error information
                $emailErrors = $emailService->printDebugger();
                log_message('error', 'Full email error details: ' . $emailErrors);
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Email sending exception: ' . $e->getMessage());
            log_message('error', 'Email exception trace: ' . $e->getTraceAsString());
        }
    }

    public function verifyEmail($token)
    {
        // Find user with verification token using UsersModel
        $user = $this->usersModel->findByVerificationToken($token);
        
        if (!$user) {
            return view('auth/verification_failed', ['message' => 'Invalid or expired verification link']);
        }
        
        // Update user as verified using UsersModel
        $this->usersModel->verifyUser($user['id']);
        
        return view('auth/verification_success', ['name' => $user['name']]);
    }



    private function sendPasswordResetEmail($email, $name, $token)
    {
        $emailService = \Config\Services::email();
        
        $resetLink = base_url("reset-password/{$token}");
        
        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Password Reset</title>
        </head>
        <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
            <table role='presentation' style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <td style='padding: 20px 0; text-align: center;'>
                        <table role='presentation' style='width: 600px; margin: 0 auto; background-color: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);'>
                            <!-- Header -->
                            <tr>
                                <td style='background-color: #dc3545; padding: 30px 20px; text-align: center;'>
                                    <h1 style='margin: 0; color: white; font-size: 28px;'>Password Reset</h1>
                                </td>
                            </tr>
                            <!-- Content -->
                            <tr>
                                <td style='padding: 40px 30px;'>
                                    <h2 style='margin: 0 0 20px 0; color: #333; font-size: 24px;'>Hello {$name}!</h2>
                                    <p style='margin: 0 0 25px 0; color: #666; font-size: 16px; line-height: 1.5;'>
                                        We received a request to reset your password. If you made this request, click the button below to reset your password:
                                    </p>
                                    <!-- Button -->
                                    <table role='presentation' style='margin: 30px 0;'>
                                        <tr>
                                            <td style='text-align: center;'>
                                                <a href='{$resetLink}' style='background-color: #dc3545; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; font-size: 16px;'>Reset Password</a>
                                            </td>
                                        </tr>
                                    </table>
                                    <p style='margin: 25px 0 10px 0; color: #666; font-size: 14px;'>
                                        Or copy and paste this link into your browser:
                                    </p>
                                    <p style='margin: 0 0 25px 0; word-break: break-all;'>
                                        <a href='{$resetLink}' style='color: #dc3545; text-decoration: none;'>{$resetLink}</a>
                                    </p>
                                    <p style='margin: 0; color: #999; font-size: 12px; font-style: italic;'>
                                        This link will expire in 1 hour. If you didn't request this reset, please ignore this email.
                                    </p>
                                </td>
                            </tr>
                            <!-- Footer -->
                            <tr>
                                <td style='background-color: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 1px solid #e9ecef;'>
                                    <p style='margin: 0; color: #666; font-size: 12px;'>
                                        If you didn't request a password reset, please ignore this email.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>";

        $emailService->setTo($email);
        $emailService->setSubject('Password Reset Request');
        $emailService->setMessage($message);
        
        if (!$emailService->send()) {
            log_message('error', 'Failed to send password reset email to: ' . $email);
            log_message('error', $emailService->printDebugger(['headers']));
        }
    }
}
