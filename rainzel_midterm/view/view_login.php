<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Jollibee Delivery</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <img src="../resources/jollibee-logo.jpg" alt="Jollibee Logo" class="logo">
            <h2>Welcome to Jollibee Delivery</h2>
            <form action="../controllers/login_controller.php" method="POST">
                <div class="input-group">
                    <input type="email" name="email" required placeholder="Email">
                </div>
                <div class="input-group">
                    <input type="password" name="password" required placeholder="Password">
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>
            <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
        </div>
    </div>
</body>
</html> 