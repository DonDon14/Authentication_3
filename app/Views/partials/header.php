<?php
/**
 * Header partial for the application
 * This file contains the header that will be used across all pages
 */
?>
<!-- Header -->
<header class="header">
    <div class="header-left">
        <h1 class="page-title"><?= $pageTitle ?? 'Dashboard' ?></h1>
        <p class="page-subtitle"><?= $pageSubtitle ?? 'Welcome back, ' . esc($name) . '! Here\'s your overview.' ?></p>
    </div>
    
    <div class="header-right">
        <!-- Search Bar -->
        <div class="search-container">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search payments, students..." class="search-input">
        </div>
        
        <!-- Notifications -->
        <div class="notification-center">
            <button class="notification-btn" onclick="toggleNotifications()">
                <i class="fas fa-bell"></i>
                <span class="notification-count">3</span>
            </button>
            
            <!-- Notification Dropdown -->
            <div class="notification-dropdown" id="notificationDropdown">
                <div class="notification-header">
                    <h3>Notifications</h3>
                    <button class="mark-read-btn">Mark all read</button>
                </div>
                <div class="notification-list">
                    <div class="notification-item unread">
                        <div class="notification-icon success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="notification-content">
                            <h4>Payment Verified</h4>
                            <p>John Doe's payment of ₱1,000.00 has been verified</p>
                            <span class="notification-time">2 minutes ago</span>
                        </div>
                    </div>
                    <div class="notification-item unread">
                        <div class="notification-icon primary">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="notification-content">
                            <h4>New Payment Received</h4>
                            <p>Jane Smith submitted uniform payment</p>
                            <span class="notification-time">1 hour ago</span>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notification-icon info">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="notification-content">
                            <h4>System Update</h4>
                            <p>QR receipt system is now active</p>
                            <span class="notification-time">3 hours ago</span>
                        </div>
                    </div>
                </div>
                <div class="notification-footer">
                    <a href="#" class="view-all-notifications">View all notifications</a>
                </div>
            </div>
        </div>
        
        <!-- User Menu -->
        <div class="user-menu">
            <button class="user-menu-btn" onclick="toggleUserMenu()">
                <div class="user-avatar">
                    <?php if (!empty($profilePictureUrl)): ?>
                        <img src="<?= esc($profilePictureUrl) ?>" alt="Profile Picture">
                    <?php else: ?>
                        <i class="fas fa-user"></i>
                    <?php endif; ?>
                </div>
                <span class="user-name"><?= esc(explode(' ', $name)[0]) ?></span>
                <i class="fas fa-chevron-down"></i>
            </button>
            
            <!-- User Dropdown -->
            <div class="user-dropdown" id="userDropdown">
                <div class="dropdown-header">
                    <div class="user-info">
                        <h4><?= esc($name) ?></h4>
                        <p><?= esc($email ?? 'admin@clearpay.com') ?></p>
                    </div>
                </div>
                <div class="dropdown-menu">
                    <a href="<?= base_url('profile') ?>" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                    <a href="<?= base_url('settings') ?>" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <a href="<?= base_url('help/index.html') ?>" class="dropdown-item">
                        <i class="fas fa-question-circle"></i>
                        <span>Help</span>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="<?= base_url('logout') ?>" class="dropdown-item logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>