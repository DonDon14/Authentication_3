-- Create announcements table
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    type ENUM('general', 'urgent', 'maintenance', 'event', 'deadline') NOT NULL DEFAULT 'general',
    priority ENUM('low', 'medium', 'high', 'critical') NOT NULL DEFAULT 'medium',
    target_audience ENUM('all', 'students', 'admins', 'staff') NOT NULL DEFAULT 'all',
    status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft',
    created_by INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    published_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_target_audience (target_audience),
    INDEX idx_published_at (published_at),
    INDEX idx_expires_at (expires_at),
    INDEX idx_created_by (created_by)
);

-- Insert sample announcements
INSERT INTO announcements (title, content, type, priority, target_audience, status, published_at) VALUES
('Welcome to ClearPay System', 'We are excited to announce the launch of our new ClearPay payment system. This platform will make it easier for students to manage their payments and for administrators to track transactions.', 'general', 'high', 'all', 'published', NOW()),
('Payment Deadline Reminder', 'This is a reminder that the deadline for semester fee payments is approaching. Please ensure all outstanding payments are completed by the end of this month to avoid late fees.', 'deadline', 'high', 'students', 'published', NOW()),
('System Maintenance Scheduled', 'The ClearPay system will undergo routine maintenance this weekend from 2:00 AM to 6:00 AM. During this time, the system may be temporarily unavailable.', 'maintenance', 'medium', 'all', 'published', NOW()),
('New QR Code Feature', 'We have added a new QR code feature for payment receipts. Students can now download QR codes for their payment confirmations, making verification easier.', 'general', 'medium', 'students', 'published', NOW()),
('Holiday Schedule', 'Please note that our offices will be closed during the upcoming holiday period. Online payments will still be available, but manual processing may be delayed.', 'event', 'low', 'all', 'published', NOW());