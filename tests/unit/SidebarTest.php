<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

class SidebarTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testSidebarLinksExist()
    {
        $result = $this->get('dashboard');
        
        // Test that the sidebar container exists
        $result->assertSee('class="sidebar"');
        
        // Test that the logo exists
        $result->assertSee('ClearPay');
        
        // Test that all main navigation links exist
        $expectedLinks = [
            'Dashboard',
            'Payments',
            'Contributions',
            'Partial Payments',
            'Payment History',
            'Analytics',
            'Students',
            'Announcements',
            'Profile',
            'Settings'
        ];

        foreach ($expectedLinks as $link) {
            $result->assertSee($link);
        }
    }

    public function testSidebarActiveStateOnDashboard()
    {
        $result = $this->get('dashboard');
        $result->assertSee('nav-item active');
        $result->assertSee('fas fa-home');
    }

    public function testSidebarActiveStateOnPayments()
    {
        $result = $this->get('payments');
        $result->assertSee('nav-item active');
        $result->assertSee('fas fa-credit-card');
    }

    public function testSidebarActiveStateOnContributions()
    {
        $result = $this->get('contributions');
        $result->assertSee('nav-item active');
        $result->assertSee('fas fa-hand-holding-usd');
    }

    public function testSidebarActiveStateOnPartialPayments()
    {
        $result = $this->get('payments/partial');
        $result->assertSee('nav-item active');
        $result->assertSee('fas fa-clock');
    }

    public function testSidebarActiveStateOnPaymentHistory()
    {
        $result = $this->get('payments/history');
        $result->assertSee('nav-item active');
        $result->assertSee('fas fa-history');
    }

    public function testHelpSectionIncluded()
    {
        $result = $this->get('dashboard');
        $result->assertSee('sidebar-footer');
    }

    public function testSidebarToggleExists()
    {
        $result = $this->get('dashboard');
        $result->assertSee('sidebar-toggle');
        $result->assertSee('sidebarToggle');
    }

    public function testNavigationStructure()
    {
        $result = $this->get('dashboard');
        
        // Test basic structure
        $result->assertSee('sidebar-header');
        $result->assertSee('sidebar-nav');
        $result->assertSee('nav-list');
        
        // Test that navigation items have correct structure
        $result->assertSee('class="nav-item');
        $result->assertSee('class="nav-link');
    }

    public function testSidebarNewBadgeOnPayments()
    {
        $result = $this->get('payments');
        $result->assertSee('nav-badge">New<');
    }
}