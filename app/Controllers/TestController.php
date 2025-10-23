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

    public function receiptTest()
    {
        // Generate a test QR code first
        $qrCode = new \Endroid\QrCode\QrCode('Test Receipt QR Code - ' . time());
        $writer = new \Endroid\QrCode\Writer\PngWriter();
        $result = $writer->write($qrCode);
        
        // Create QR file
        $qrFilename = 'test_receipt_' . date('Ymd_His') . '.png';
        $qrPath = WRITEPATH . 'uploads/' . $qrFilename;
        file_put_contents($qrPath, $result->getString());
        
        // Create test payment data
        $data = [
            'payment' => [
                'student_name' => 'John Doe',
                'student_id' => '2023001',
                'payment_date' => date('Y-m-d H:i:s'),
                'amount_paid' => 1000.00,
                'remaining' => 500.00,
                'payment_type' => 'Tuition Fee',
                'payment_method' => 'Cash',
                'transaction_id' => 'TXN'.time(),
                'verification_status' => 'Verified',
                'qr_code' => $qrFilename,  // Set the QR code filename
                'receipt_number' => 'RCP'.time()
            ]
        ];

        // Set content type
        $this->response->setHeader('Content-Type', 'text/html');
        
        return view('tests/receipt_test', $data);
    }
}