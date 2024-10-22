<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="style_user_regist.css">
</head>

<body>
    <div class="forgot-password-container">
        <div class="forgot-password-section">
            <div class="forgot-password-notes">
                <h1>Forgot Password</h1>
                <h3>Don't worry! We got your back!</h3>
            </div>
            <div class="forgot-password-form">
                <form action="send_email.php" method="POST">
                    <label>Enter your email</label>
                    
                    <input type="email" name="email">
                    <button>Send email</button>
                </form>
                <div id="warning3" style="color: #393E46; display: none;">
                    <h5>Email not found. Please use registered email.</h5>
                </div>
            </div>
        </div>
    </div>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        if (error === 'no_account') {
            document.getElementById("warning3").style.display = "block";
        }
    </script>

</body>

</html>