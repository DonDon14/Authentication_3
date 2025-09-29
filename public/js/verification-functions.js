// QR Verification Functions for Dashboard

/**
 * Show verification loading state
 */
function showVerificationLoading(message) {
    const resultDiv = document.getElementById('verificationResult');
    if (resultDiv) {
        resultDiv.innerHTML = `
            <div style="
                background: #e3f2fd;
                border: 1px solid #2196f3;
                border-radius: 8px;
                padding: 20px;
                text-align: center;
            ">
                <i class="fas fa-spinner fa-spin" style="color: #2196f3; font-size: 24px; margin-bottom: 10px;"></i>
                <p style="color: #1976d2; margin: 0;">${message}</p>
            </div>
        `;
        resultDiv.style.display = 'block';
    }
}

/**
 * Show verification success result
 */
function showVerificationSuccess(paymentData, message) {
    const resultDiv = document.getElementById('verificationResult');
    if (resultDiv) {
        resultDiv.innerHTML = `
            <div style="
                background: #e8f5e8;
                border: 1px solid #4caf50;
                border-radius: 8px;
                padding: 20px;
                text-align: left;
            ">
                <div style="text-align: center; margin-bottom: 15px;">
                    <i class="fas fa-check-circle" style="color: #4caf50; font-size: 32px; margin-bottom: 10px;"></i>
                    <h3 style="color: #2e7d32; margin: 0;">${message}</h3>
                </div>
                
                <div class="verified-payment-details">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <strong>Student:</strong> <span>${paymentData.student_name}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <strong>Student ID:</strong> <span>${paymentData.student_id}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <strong>Contribution:</strong> <span>${paymentData.contribution_title}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <strong>Amount:</strong> <span>$${parseFloat(paymentData.amount).toFixed(2)}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <strong>Payment Date:</strong> <span>${new Date(paymentData.payment_date).toLocaleDateString()}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <strong>Status:</strong> <span style="
                            background: #4caf50;
                            color: white;
                            padding: 2px 8px;
                            border-radius: 12px;
                            font-size: 12px;
                        ">${paymentData.payment_status}</span>
                    </div>
                </div>
            </div>
        `;
        resultDiv.style.display = 'block';
    }
}

/**
 * Show verification error
 */
function showVerificationError(message) {
    const resultDiv = document.getElementById('verificationResult');
    if (resultDiv) {
        resultDiv.innerHTML = `
            <div style="
                background: #ffebee;
                border: 1px solid #f44336;
                border-radius: 8px;
                padding: 20px;
                text-align: center;
            ">
                <i class="fas fa-times-circle" style="color: #f44336; font-size: 24px; margin-bottom: 10px;"></i>
                <p style="color: #c62828; margin: 0; font-weight: 600;">${message}</p>
            </div>
        `;
        resultDiv.style.display = 'block';
    }
}