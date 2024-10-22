<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="style_user_regist.css">
</head>

<body>
    <div class="user-regist-container">
        <div class="user-regist-welcome">
            <div class="user-regist-welcome-ticket">
                <img src="ticket1.png" alt="ticket" class="ticket">
            </div>
            <div class="user-regist-welcome-text">
                <h2>Regist to your favourite event now!</h2>
            </div>
        </div>
        <div class="user-regist-page">
            <div class="user-regist-page-buttons">
                <div class="user-regist-page-button active" id="regist_button">
                    <button id="register-button">Register</button>
                </div>
                <div class="user-regist-page-button" id="login_button">
                    <button id="login-button">Login</button>
                </div>
            </div>
        </div>
        <div class="user-rl-section active" id="register_section">
            <form action="sign_up_process.php" method="POST">
                <label>Name</label>
                <input type="text" name="name" value="<?php echo isset($_COOKIE['name']) ? htmlspecialchars($_COOKIE['name']) : ''; ?>" required>
                <label>Username</label>
                <input type="text" name="username" required>
                <label>Email</label>
                <input type="email" name="email" required>
                <label>Password</label>
                <input type="password" name="password" required>
                <button type="submit">Register</button>
            </form>
            <div id="warning2" style="color: #393E46; display: none;">
                <h5>Email or username already used. Try to use different email or username.</h5>
            </div>
        </div>
        <div class="user-rl-section" id="login_section">
            <form action="login_process.php" method="POST">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo isset($_COOKIE['email']) ? htmlspecialchars($_COOKIE['email']) : ''; ?>" required>
                <label>Username</label>
                <input type="text" name="username" value="<?php echo isset($_COOKIE['username']) ? htmlspecialchars($_COOKIE['username']) : ''; ?>" required>
                <label>Password</label>
                <input type="password" name="password" required>
                <button type="submit">Login</button>
            </form>
            <!-- warning kalau password salah -->
            <div id="warning" style="color: #393E46; display: none;">
                <h5>Wrong password. Please try again or try to reset your password.</h5>
            </div>
            <!-- warning kalau email atau username -->
            <div id="warning1" style="color: #393E46; display: none;">
                <h5>Wrong email or username. Please try again.</h5>
            </div>
            <div class="user-rl-addition">
                <a href="forgot_password.php">Forgot Password</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function(){
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');

            if (error === 'wrong_password') {
                document.getElementById("login_section").classList.add("active");
                document.getElementById("register_section").classList.remove("active");
                document.getElementById("login_button").classList.add("active");
                document.getElementById("regist_button").classList.remove("active");
                document.getElementById("warning").style.display = "block";
            }

            if (error === 'not_found') {
                document.getElementById("login_section").classList.add("active");
                document.getElementById("register_section").classList.remove("active");
                document.getElementById("login_button").classList.add("active");
                document.getElementById("regist_button").classList.remove("active");
                document.getElementById("warning1").style.display = "block";
            }

            if (error === 'used'){
                document.getElementById("register_section").classList.add("active");
                document.getElementById("login_section").classList.remove("active");
                document.getElementById("regist_button").classList.add("active");
                document.getElementById("login_button").classList.remove("active");
                document.getElementById("warning2").style.display = "block";
            }

            window.onbeforeunload = function() {
                document.cookie = 'email=; expires=Mon, 21 Oct 2024 00:00:00 GMT; path=/;';
                document.cookie = 'username=; expires=Mon, 21 Oct 2024 00:00:00 GMT; path=/;';
            };

            document.getElementById("register-button").addEventListener("click", function() {
                document.getElementById("register_section").classList.add("active");
                document.getElementById("login_section").classList.remove("active");
                document.getElementById("regist_button").classList.add("active");
                document.getElementById("login_button").classList.remove("active");
            });

            document.getElementById("login-button").addEventListener("click", function() {
                document.getElementById("login_section").classList.add("active");
                document.getElementById("register_section").classList.remove("active");
                document.getElementById("login_button").classList.add("active");
                document.getElementById("regist_button").classList.remove("active");
            });
        });
    </script>
</body>

</html>