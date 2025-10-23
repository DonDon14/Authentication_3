<?php

namespace App\Controllers;

class TestController extends BaseController
{
    public function headerTest()
    {
        // Disable layout if you're using one
        $this->response->setHeader('Content-Type', 'text/html');
        
        return view('tests/header_test');
    }
}