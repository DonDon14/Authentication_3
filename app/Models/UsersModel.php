<?php
namespace App\Models;
use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 
                                'username', 
                                'email', 
                                'email_verified', 
                                'verification_token', 
                                'verification_expires', 
                                'reset_token', 
                                'reset_expires', 
                                'password', 
                                'phone',
                                'profile_picture',
                                'created_at'];
    
    protected $useTimestamps = false;

    /**
     * Find user by username
     */
    public function findByUsername($username)
    {
        return $this->where('username', $username)->first();
    }

    /**
     * Find user by email
     */
    public function findByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    /**
     * Find user by verification token
     */
    public function findByVerificationToken($token)
    {
        return $this->where('verification_token', $token)
                   ->where('verification_expires >', date('Y-m-d H:i:s'))
                   ->first();
    }

    /**
     * Find user by reset token
     */
    public function findByResetToken($token)
    {
        return $this->where('reset_token', $token)
                   ->where('reset_expires >', date('Y-m-d H:i:s'))
                   ->first();
    }

    /**
     * Create a new user with hashed password
     */
    public function createUser($userData)
    {
        // Hash password before saving
        if (isset($userData['password'])) {
            $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        }
        
        return $this->insert($userData);
    }

    /**
     * Verify user email
     */
    public function verifyUser($userId)
    {
        return $this->update($userId, [
            'email_verified' => 1,
            'verification_token' => null,
            'verification_expires' => null
        ]);
    }

    /**
     * Set password reset token
     */
    public function setResetToken($userId, $token, $expires)
    {
        return $this->update($userId, [
            'reset_token' => $token,
            'reset_expires' => $expires
        ]);
    }

    /**
     * Update password and clear reset token
     */
    public function updatePassword($userId, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        return $this->update($userId, [
            'password' => $hashedPassword,
            'reset_token' => null,
            'reset_expires' => null
        ]);
    }

    /**
     * Delete expired unverified users
     */
    public function deleteExpiredUnverified()
    {
        return $this->where('email_verified', 0)
                   ->where('verification_expires <', date('Y-m-d H:i:s'))
                   ->delete();
    }

    /**
     * Check if username is available
     */
    public function isUsernameAvailable($username, $excludeUserId = null)
    {
        $query = $this->where('username', $username);
        
        if ($excludeUserId) {
            $query = $query->where('id !=', $excludeUserId);
        }
        
        return $query->first() === null;
    }

    /**
     * Check if email is available
     */
    public function isEmailAvailable($email, $excludeUserId = null)
    {
        $query = $this->where('email', $email);
        
        if ($excludeUserId) {
            $query = $query->where('id !=', $excludeUserId);
        }
        
        return $query->first() === null;
    }
}