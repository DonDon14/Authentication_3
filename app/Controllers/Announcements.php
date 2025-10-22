<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;
use CodeIgniter\Controller;

class Announcements extends BaseController
{
    protected $announcementModel;

    public function __construct()
    {
        $this->announcementModel = new AnnouncementModel();
    }

    /**
     * Admin announcements management page
     */
    public function index()
    {
        // Check if admin is logged in
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to('/');
        }

        try {
            // Get all announcements for admin view
            $announcements = $this->announcementModel->orderBy('created_at', 'DESC')->findAll();
            
            // Get status counts
            $statusCounts = $this->announcementModel->getStatusCounts();

            // Add profile picture for sidebar and header
            $userId = $session->get('user_id');
            $usersModel = new \App\Models\UsersModel();
            $user = $usersModel->find($userId);
            
            $profilePictureUrl = '';
            if (!empty($user['profile_picture'])) {
                $filename = basename($user['profile_picture']);
                $profilePictureUrl = base_url('test-profile-picture/' . $filename);
            }

            $data = [
                'announcements' => $announcements,
                'status_counts' => $statusCounts,
                'name' => $session->get('name'),
                'email' => $session->get('email'),
                'profilePictureUrl' => $profilePictureUrl
            ];

            return view('announcements/index', $data);

        } catch (\Exception $e) {
            log_message('error', 'Announcements index error: ' . $e->getMessage());
            return redirect()->to('/dashboard')->with('error', 'Error loading announcements');
        }
    }

    /**
     * Student announcements view
     */
    public function studentView()
    {
        // Get session data (works for both admin and student sessions)
        $session = session();
        $isStudent = $session->get('user_type') === 'student';
        $isAdmin = $session->get('logged_in') === true;

        if (!$isStudent && !$isAdmin) {
            return redirect()->to('/user/login');
        }

        try {
            // Get audience type
            $audience = $isStudent ? 'students' : 'all';
            
            // Get active announcements
            $announcements = $this->announcementModel->getActiveAnnouncements($audience);
            
            // Separate by priority
            $criticalAnnouncements = array_filter($announcements, function($a) {
                return $a['priority'] === 'critical';
            });
            
            $highAnnouncements = array_filter($announcements, function($a) {
                return $a['priority'] === 'high';
            });
            
            $normalAnnouncements = array_filter($announcements, function($a) {
                return in_array($a['priority'], ['medium', 'low']);
            });

            $data = [
                'all_announcements' => $announcements,
                'critical_announcements' => $criticalAnnouncements,
                'high_announcements' => $highAnnouncements,
                'normal_announcements' => $normalAnnouncements,
                'is_student' => $isStudent,
                'is_admin' => $isAdmin
            ];

            // Add student or admin specific data
            if ($isStudent) {
                $data['student_name'] = $session->get('user_student_name');
                $data['student_id'] = $session->get('user_student_id');
            } else {
                $data['name'] = $session->get('name');
                $data['email'] = $session->get('email');
            }

            return view('announcements/student_view', $data);

        } catch (\Exception $e) {
            log_message('error', 'Student announcements view error: ' . $e->getMessage());
            $redirectUrl = $isStudent ? '/user/dashboard' : '/dashboard';
            return redirect()->to($redirectUrl)->with('error', 'Error loading announcements');
        }
    }

    /**
     * Create new announcement
     */
    public function create()
    {
        // Add error handling and debugging
        try {
            $session = session();
            
            // Debug session information
            log_message('debug', 'Create announcement - Session data: ' . json_encode([
                'logged_in' => $session->get('logged_in'),
                'user_id' => $session->get('user_id'),
                'session_id' => session_id()
            ]));
            
            if (!$session->get('logged_in')) {
                log_message('warning', 'Create announcement - User not logged in');
                return $this->response->setJSON(['success' => false, 'message' => 'Not authorized - please log in']);
            }

            // Debug POST data
            $postData = $this->request->getPost();
            log_message('debug', 'Create announcement - POST data: ' . json_encode($postData));
            
            $data = [
                'title' => $this->request->getPost('title'),
                'content' => $this->request->getPost('content'),
                'type' => $this->request->getPost('type'),
                'priority' => $this->request->getPost('priority'),
                'target_audience' => $this->request->getPost('target_audience'),
                'status' => $this->request->getPost('status'),
                'created_by' => $session->get('user_id') ?: 1, // Fallback to user 1 if no user_id
                'expires_at' => $this->request->getPost('expires_at') ?: null
            ];

            log_message('debug', 'Create announcement - Processed data: ' . json_encode($data));

            // Check if model exists
            if (!$this->announcementModel) {
                log_message('error', 'AnnouncementModel is null');
                return $this->response->setJSON(['success' => false, 'message' => 'Model initialization error']);
            }

            $id = $this->announcementModel->createAnnouncement($data);

            if ($id) {
                // Log activity
                try {
                    $activityModel = new \App\Models\ActivityModel();
                    $activityModel->logActivity(
                        $session->get('user_id'),
                        \App\Models\ActivityModel::ACTIVITY_CREATE,
                        'Created announcement: ' . $data['title'],
                        'announcement',
                        $id,
                        $data
                    );
                } catch (\Exception $activityError) {
                    log_message('warning', 'Failed to log announcement creation activity: ' . $activityError->getMessage());
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Announcement created successfully',
                    'id' => $id
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to create announcement',
                    'errors' => $this->announcementModel->errors()
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Create announcement error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error creating announcement: ' . $e->getMessage(),
                'debug' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Update announcement
     */
    public function update($id)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authorized']);
        }

        try {
            $data = [
                'title' => $this->request->getPost('title'),
                'content' => $this->request->getPost('content'),
                'type' => $this->request->getPost('type'),
                'priority' => $this->request->getPost('priority'),
                'target_audience' => $this->request->getPost('target_audience'),
                'status' => $this->request->getPost('status'),
                'expires_at' => $this->request->getPost('expires_at') ?: null
            ];

            $result = $this->announcementModel->updateAnnouncement($id, $data);

            if ($result) {
                // Log activity
                try {
                    $activityModel = new \App\Models\ActivityModel();
                    $activityModel->logActivity(
                        $session->get('user_id'),
                        \App\Models\ActivityModel::ACTIVITY_UPDATE,
                        'Updated announcement: ' . $data['title'],
                        'announcement',
                        $id,
                        $data
                    );
                } catch (\Exception $activityError) {
                    log_message('warning', 'Failed to log announcement update activity: ' . $activityError->getMessage());
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Announcement updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update announcement',
                    'errors' => $this->announcementModel->errors()
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Update announcement error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error updating announcement: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete announcement
     */
    public function delete($id)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authorized']);
        }

        try {
            $announcement = $this->announcementModel->find($id);
            if (!$announcement) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Announcement not found'
                ]);
            }

            $result = $this->announcementModel->delete($id);

            if ($result) {
                // Log activity
                try {
                    $activityModel = new \App\Models\ActivityModel();
                    $activityModel->logActivity(
                        $session->get('user_id'),
                        \App\Models\ActivityModel::ACTIVITY_DELETE,
                        'Deleted announcement: ' . $announcement['title'],
                        'announcement',
                        $id,
                        $announcement
                    );
                } catch (\Exception $activityError) {
                    log_message('warning', 'Failed to log announcement deletion activity: ' . $activityError->getMessage());
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Announcement deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete announcement'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Delete announcement error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error deleting announcement: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get announcement details
     */
    public function get($id)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authorized']);
        }

        try {
            $announcement = $this->announcementModel->find($id);
            
            if ($announcement) {
                return $this->response->setJSON([
                    'success' => true,
                    'announcement' => $announcement
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Announcement not found'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Get announcement error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving announcement'
            ]);
        }
    }

    /**
     * Archive announcement
     */
    public function archive($id)
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authorized']);
        }

        try {
            $announcement = $this->announcementModel->find($id);
            if (!$announcement) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Announcement not found'
                ]);
            }

            $result = $this->announcementModel->archiveAnnouncement($id);

            if ($result) {
                // Log activity
                try {
                    $activityModel = new \App\Models\ActivityModel();
                    $activityModel->logActivity(
                        $session->get('user_id'),
                        \App\Models\ActivityModel::ACTIVITY_UPDATE,
                        'Archived announcement: ' . $announcement['title'],
                        'announcement',
                        $id,
                        ['status' => 'archived']
                    );
                } catch (\Exception $activityError) {
                    log_message('warning', 'Failed to log announcement archive activity: ' . $activityError->getMessage());
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Announcement archived successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to archive announcement'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Archive announcement error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error archiving announcement: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Search announcements
     */
    public function search()
    {
        $session = session();
        $isStudent = $session->get('user_type') === 'student';
        $isAdmin = $session->get('logged_in') === true;

        if (!$isStudent && !$isAdmin) {
            return $this->response->setJSON(['success' => false, 'message' => 'Not authorized']);
        }

        try {
            $query = $this->request->getGet('q');
            if (empty($query)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Search query is required'
                ]);
            }

            $audience = $isStudent ? 'students' : 'all';
            $results = $this->announcementModel->searchAnnouncements($query, $audience);

            return $this->response->setJSON([
                'success' => true,
                'results' => $results,
                'count' => count($results)
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Search announcements error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error searching announcements'
            ]);
        }
    }
}