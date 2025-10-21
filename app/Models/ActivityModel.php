<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityModel extends Model
{
    protected $table = 'user_activities';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'activity_type',
        'description',
        'entity_type',
        'entity_id',
        'metadata',
        'ip_address',
        'user_agent',
        'created_at'
    ];
    
    protected $useTimestamps = false;
    
    // Activity types constants
    const ACTIVITY_LOGIN = 'login';
    const ACTIVITY_LOGOUT = 'logout';
    const ACTIVITY_PAYMENT_CREATED = 'payment_created';
    const ACTIVITY_PAYMENT_VERIFIED = 'payment_verified';
    const ACTIVITY_PAYMENT_UPDATED = 'payment_updated';
    const ACTIVITY_QR_GENERATED = 'qr_generated';
    const ACTIVITY_CONTRIBUTION_CREATED = 'contribution_created';
    const ACTIVITY_CONTRIBUTION_UPDATED = 'contribution_updated';
    const ACTIVITY_CONTRIBUTION_DELETED = 'contribution_deleted';
    const ACTIVITY_PROFILE_UPDATED = 'profile_updated';
    const ACTIVITY_EXPORT_DATA = 'export_data';
    
    /**
     * Log an activity
     */
    public function logActivity($userId, $activityType, $description, $entityType = null, $entityId = null, $metadata = null)
    {
        $request = service('request');
        
        $data = [
            'user_id' => $userId,
            'activity_type' => $activityType,
            'description' => $description,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'metadata' => $metadata ? json_encode($metadata) : null,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $this->insert($data);
    }
    
    /**
     * Get recent activities with user information
     */
    public function getRecentActivities($limit = 10)
    {
        return $this->select('
            user_activities.*,
            users.name as user_name,
            users.email as user_email
        ')
        ->join('users', 'users.id = user_activities.user_id', 'left')
        ->orderBy('user_activities.created_at', 'DESC')
        ->limit($limit)
        ->findAll();
    }
    
    /**
     * Get activities by user
     */
    public function getActivitiesByUser($userId, $limit = 20)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
    
    /**
     * Get activities by type
     */
    public function getActivitiesByType($activityType, $limit = 20)
    {
        return $this->select('
            user_activities.*,
            users.name as user_name,
            users.email as user_email
        ')
        ->join('users', 'users.id = user_activities.user_id', 'left')
        ->where('activity_type', $activityType)
        ->orderBy('user_activities.created_at', 'DESC')
        ->limit($limit)
        ->findAll();
    }
    
    /**
     * Get activity statistics
     */
    public function getActivityStats($days = 7)
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-$days days"));
        
        // Get activity counts by type
        $stats = $this->select('activity_type, COUNT(*) as count')
                      ->where('created_at >=', $startDate)
                      ->groupBy('activity_type')
                      ->orderBy('count', 'DESC')
                      ->findAll();
        
        // Get total activities today
        $todayStart = date('Y-m-d 00:00:00');
        $todayCount = $this->where('created_at >=', $todayStart)->countAllResults();
        
        return [
            'by_type' => $stats,
            'today_count' => $todayCount,
            'period_days' => $days
        ];
    }
    
    /**
     * Format activity for display
     */
    public function formatActivityForDisplay($activity)
    {
        $timeAgo = $this->timeAgo($activity['created_at']);
        $icon = $this->getActivityIcon($activity['activity_type']);
        $color = $this->getActivityColor($activity['activity_type']);
        
        return [
            'id' => $activity['id'],
            'title' => $this->getActivityTitle($activity),
            'description' => $activity['description'],
            'time_ago' => $timeAgo,
            'icon' => $icon,
            'color' => $color,
            'user_name' => $activity['user_name'] ?? 'System',
            'metadata' => $activity['metadata'] ? json_decode($activity['metadata'], true) : null
        ];
    }
    
    /**
     * Get activity icon based on type
     */
    private function getActivityIcon($activityType)
    {
        $icons = [
            self::ACTIVITY_LOGIN => 'fas fa-sign-in-alt',
            self::ACTIVITY_LOGOUT => 'fas fa-sign-out-alt',
            self::ACTIVITY_PAYMENT_CREATED => 'fas fa-plus',
            self::ACTIVITY_PAYMENT_VERIFIED => 'fas fa-check',
            self::ACTIVITY_PAYMENT_UPDATED => 'fas fa-edit',
            self::ACTIVITY_QR_GENERATED => 'fas fa-qrcode',
            self::ACTIVITY_CONTRIBUTION_CREATED => 'fas fa-hand-holding-usd',
            self::ACTIVITY_CONTRIBUTION_UPDATED => 'fas fa-edit',
            self::ACTIVITY_PROFILE_UPDATED => 'fas fa-user-edit',
            self::ACTIVITY_EXPORT_DATA => 'fas fa-download'
        ];
        
        return $icons[$activityType] ?? 'fas fa-circle';
    }
    
    /**
     * Get activity color based on type
     */
    private function getActivityColor($activityType)
    {
        $colors = [
            self::ACTIVITY_LOGIN => 'success',
            self::ACTIVITY_LOGOUT => 'secondary',
            self::ACTIVITY_PAYMENT_CREATED => 'primary',
            self::ACTIVITY_PAYMENT_VERIFIED => 'success',
            self::ACTIVITY_PAYMENT_UPDATED => 'warning',
            self::ACTIVITY_QR_GENERATED => 'info',
            self::ACTIVITY_CONTRIBUTION_CREATED => 'primary',
            self::ACTIVITY_CONTRIBUTION_UPDATED => 'warning',
            self::ACTIVITY_PROFILE_UPDATED => 'info',
            self::ACTIVITY_EXPORT_DATA => 'secondary'
        ];
        
        return $colors[$activityType] ?? 'primary';
    }
    
    /**
     * Get activity title based on activity data
     */
    private function getActivityTitle($activity)
    {
        $titles = [
            self::ACTIVITY_LOGIN => 'User Login',
            self::ACTIVITY_LOGOUT => 'User Logout',
            self::ACTIVITY_PAYMENT_CREATED => 'New Payment Recorded',
            self::ACTIVITY_PAYMENT_VERIFIED => 'Payment Verified',
            self::ACTIVITY_PAYMENT_UPDATED => 'Payment Updated',
            self::ACTIVITY_QR_GENERATED => 'QR Receipt Generated',
            self::ACTIVITY_CONTRIBUTION_CREATED => 'Contribution Created',
            self::ACTIVITY_CONTRIBUTION_UPDATED => 'Contribution Updated',
            self::ACTIVITY_PROFILE_UPDATED => 'Profile Updated',
            self::ACTIVITY_EXPORT_DATA => 'Data Exported'
        ];
        
        return $titles[$activity['activity_type']] ?? 'System Activity';
    }
    
    /**
     * Calculate time ago from timestamp
     */
    private function timeAgo($datetime)
    {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'just now';
        if ($time < 3600) return floor($time/60) . ' minutes ago';
        if ($time < 86400) return floor($time/3600) . ' hours ago';
        if ($time < 2592000) return floor($time/86400) . ' days ago';
        if ($time < 31536000) return floor($time/2592000) . ' months ago';
        
        return floor($time/31536000) . ' years ago';
    }
}