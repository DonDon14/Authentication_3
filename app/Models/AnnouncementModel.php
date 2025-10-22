<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table = 'announcements';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'title',
        'content',
        'type',
        'priority',
        'target_audience',
        'status',
        'created_by',
        'created_at',
        'updated_at',
        'published_at',
        'expires_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'content' => 'required|min_length[10]',
        'type' => 'required|in_list[general,urgent,maintenance,event,deadline]',
        'priority' => 'required|in_list[low,medium,high,critical]',
        'target_audience' => 'required|in_list[all,students,admins,staff]',
        'status' => 'required|in_list[draft,published,archived]'
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'Announcement title is required',
            'min_length' => 'Title must be at least 3 characters long',
            'max_length' => 'Title cannot exceed 255 characters'
        ],
        'content' => [
            'required' => 'Announcement content is required',
            'min_length' => 'Content must be at least 10 characters long'
        ],
        'type' => [
            'required' => 'Announcement type is required',
            'in_list' => 'Invalid announcement type'
        ],
        'priority' => [
            'required' => 'Priority level is required',
            'in_list' => 'Invalid priority level'
        ],
        'target_audience' => [
            'required' => 'Target audience is required',
            'in_list' => 'Invalid target audience'
        ],
        'status' => [
            'required' => 'Status is required',
            'in_list' => 'Invalid status'
        ]
    ];

    /**
     * Get active announcements for a specific audience
     */
    public function getActiveAnnouncements($audience = 'all', $limit = null)
    {
        $builder = $this->where('status', 'published')
                       ->where('(expires_at IS NULL OR expires_at > NOW())')
                       ->where('published_at <=', date('Y-m-d H:i:s'));

        if ($audience !== 'all') {
            $builder->groupStart()
                   ->where('target_audience', 'all')
                   ->orWhere('target_audience', $audience)
                   ->groupEnd();
        }

        $builder->orderBy('priority', 'DESC')
               ->orderBy('published_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Get announcements by priority
     */
    public function getAnnouncementsByPriority($priority, $audience = 'all')
    {
        $builder = $this->where('priority', $priority)
                       ->where('status', 'published')
                       ->where('(expires_at IS NULL OR expires_at > NOW())')
                       ->where('published_at <=', date('Y-m-d H:i:s'));

        if ($audience !== 'all') {
            $builder->groupStart()
                   ->where('target_audience', 'all')
                   ->orWhere('target_audience', $audience)
                   ->groupEnd();
        }

        return $builder->orderBy('published_at', 'DESC')->findAll();
    }

    /**
     * Get announcements count by status
     */
    public function getStatusCounts()
    {
        return [
            'total' => $this->countAll(),
            'published' => $this->where('status', 'published')->countAllResults(false),
            'draft' => $this->where('status', 'draft')->countAllResults(false),
            'archived' => $this->where('status', 'archived')->countAllResults(false),
            'expired' => $this->where('status', 'published')
                             ->where('expires_at IS NOT NULL')
                             ->where('expires_at <', date('Y-m-d H:i:s'))
                             ->countAllResults(false)
        ];
    }

    /**
     * Create new announcement
     */
    public function createAnnouncement($data)
    {
        // Set defaults
        $data['created_by'] = $data['created_by'] ?? session()->get('user_id') ?? 1;
        $data['status'] = $data['status'] ?? 'draft';
        
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        return $this->insert($data);
    }

    /**
     * Update announcement
     */
    public function updateAnnouncement($id, $data)
    {
        $existing = $this->find($id);
        if (!$existing) {
            return false;
        }

        // If changing status to published, set published_at if not already set
        if ($data['status'] === 'published' && empty($existing['published_at'])) {
            $data['published_at'] = date('Y-m-d H:i:s');
        }

        return $this->update($id, $data);
    }

    /**
     * Archive announcement
     */
    public function archiveAnnouncement($id)
    {
        return $this->update($id, ['status' => 'archived']);
    }

    /**
     * Get recent announcements for dashboard
     */
    public function getRecentAnnouncements($audience = 'all', $limit = 5)
    {
        return $this->getActiveAnnouncements($audience, $limit);
    }

    /**
     * Search announcements
     */
    public function searchAnnouncements($query, $audience = 'all')
    {
        $builder = $this->like('title', $query)
                       ->orLike('content', $query)
                       ->where('status', 'published')
                       ->where('(expires_at IS NULL OR expires_at > NOW())');

        if ($audience !== 'all') {
            $builder->groupStart()
                   ->where('target_audience', 'all')
                   ->orWhere('target_audience', $audience)
                   ->groupEnd();
        }

        return $builder->orderBy('published_at', 'DESC')->findAll();
    }
}