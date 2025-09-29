<!DOCTYPE html>
<html>
<head>
    <title>Modal Test</title>
    <script>
        function showModal() {
            console.log('showModal called');
            const modal = document.getElementById('testModal');
            console.log('Modal element:', modal);
            if (modal) {
                modal.style.display = 'block';
            } else {
                alert('Modal not found');
            }
        }
        
        function closeModal() {
            document.getElementById('testModal').style.display = 'none';
        }
        
        window.onload = function() {
            console.log('Page loaded');
            const modal = document.getElementById('testModal');
            console.log('Modal found on load:', !!modal);
        }
    </script>
</head>
<body>
    <h1>Modal Test</h1>
    <button onclick="showModal()">Test Modal</button>
    
    <div id="testModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
        <div style="background: white; margin: 10% auto; padding: 20px; width: 300px; border-radius: 8px;">
            <h3>Test Modal</h3>
            <p>This is a test modal</p>
            <button onclick="closeModal()">Close</button>
        </div>
    </div>
</body>
</html>