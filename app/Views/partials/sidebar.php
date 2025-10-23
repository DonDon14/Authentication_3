<?php
/**
 * Sidebar navigation partial
 * This file contains the sidebar that will be used across all pages
 */
?>
<aside class="sidebar">
  <div class="sidebar-header">
    <div class="app-logo">
      <div class="logo-icon">
        <i class="fas fa-credit-card"></i>
      </div>
      <a href="<?= base_url('dashboard') ?>" class="app-name-link">
        <h2 class="app-name">ClearPay</h2>
      </a>
    </div>
    <button class="sidebar-toggle" id="sidebarToggle">
      <i class="fas fa-bars"></i>
    </button>
  </div>
  
  <nav class="sidebar-nav">
    <ul class="nav-list">
      <li class="nav-item <?= str_contains(current_url(true)->getPath(), '/dashboard') ? 'active' : '' ?>">
        <a href="<?= base_url('dashboard') ?>" class="nav-link">
          <i class="fas fa-home"></i>
          <span>Dashboard</span>
        </a>
      </li>
      <?php 
      $currentPath = current_url(true)->getPath();
      $isContributionView = str_contains($currentPath, '/payments/viewContribution/');
      ?>
      <li class="nav-item <?= (str_contains($currentPath, '/payments') && !str_contains($currentPath, '/partial') && !str_contains($currentPath, '/history') && !$isContributionView) ? 'active' : '' ?>">
        <a href="<?= base_url('payments') ?>" class="nav-link">
          <i class="fas fa-credit-card"></i>
          <span>Payments</span>
          <span class="nav-badge">New</span>
        </a>
      </li>
      <li class="nav-item <?= str_contains($currentPath, '/contributions') || $isContributionView ? 'active' : '' ?>">
        <a href="<?= base_url('contributions') ?>" class="nav-link">
          <i class="fas fa-hand-holding-usd"></i>
          <span>Contributions</span>
        </a>
      </li>
      <li class="nav-item <?= str_contains(current_url(true)->getPath(), '/payments/partial') ? 'active' : '' ?>">
        <a href="<?= base_url('payments/partial') ?>" class="nav-link">
          <i class="fas fa-clock"></i>
          <span>Partial Payments</span>
        </a>
      </li>
      <li class="nav-item <?= str_contains(current_url(true)->getPath(), '/payments/history') ? 'active' : '' ?>">
        <a href="<?= base_url('payments/history') ?>" class="nav-link">
          <i class="fas fa-history"></i>
          <span>Payment History</span>
        </a>
      </li>
      <li class="nav-divider"></li>
      <li class="nav-item <?= str_contains(current_url(true)->getPath(), '/analytics') ? 'active' : '' ?>">
        <a href="<?= base_url('analytics') ?>" class="nav-link">
          <i class="fas fa-chart-bar"></i>
          <span>Analytics</span>
        </a>
      </li>
      <li class="nav-item <?= str_contains(current_url(true)->getPath(), '/students') ? 'active' : '' ?>">
        <a href="<?= base_url('students') ?>" class="nav-link">
          <i class="fas fa-users"></i>
          <span>Students</span>
        </a>
      </li>
      <li class="nav-item <?= str_contains(current_url(true)->getPath(), '/announcements') ? 'active' : '' ?>">
        <a href="<?= base_url('announcements') ?>" class="nav-link">
          <i class="fas fa-bullhorn"></i>
          <span>Announcements</span>
        </a>
      </li>
      <li class="nav-item <?= str_contains(current_url(true)->getPath(), '/profile') ? 'active' : '' ?>">
        <a href="<?= base_url('profile') ?>" class="nav-link">
          <i class="fas fa-user"></i>
          <span>Profile</span>
        </a>
      </li>
      <li class="nav-item <?= str_contains(current_url(true)->getPath(), '/settings') ? 'active' : '' ?>">
        <a href="<?= base_url('settings') ?>" class="nav-link">
          <i class="fas fa-cog"></i>
          <span>Settings</span>
        </a>
      </li>
    </ul>
  </nav>
  
  <div class="sidebar-footer">
    <?= $this->include('partials/help_section') ?>
  </div>
</aside>