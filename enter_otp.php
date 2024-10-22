<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter OTP</title>
    <link rel="stylesheet" href="style_user_regist.css">
</head>

<body>
    <?php
    session_start();

    $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
    ?>
    <div class="enter-otp-container">
        <h2>Enter OTP</h2>
        <form action="reset_password.php" method="POST">
            <input type="" name="email" value="<?php echo htmlspecialchars($email); ?>">
            <input type="number" name="otp" required>
            <button type="submit">Submit OTP</button>
        </form>
    </div>
</body>

</html>