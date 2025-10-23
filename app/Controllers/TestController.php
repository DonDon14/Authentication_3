<?php

namespace App\Controllers;

class TestController extends BaseController
{
    public function headerTest()
    {
        // Mock user data for testing header components
        $data = [
            'name' => 'John Doe',
            'email' => 'john.doe@example.com',
            'profilePictureUrl' => null, // Set to null to test default avatar icon
        ];

        // Disable layout if you're using one
        $this->response->setHeader('Content-Type', 'text/html');
        
        return view('tests/header_test', $data);
    }
}