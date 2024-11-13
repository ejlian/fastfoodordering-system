<?php
session_start();
require_once('../classes/Database.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: view_menu.php');
        exit();
    } else {
        $_SESSION['error'] = 'Invalid email or password';
    }
}
?>

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
            <h2>Welcome to Jollibee<br>Delivery</h2>
            <form method="POST">
                <div class="input-group">
                    <input type="email" name="email" required placeholder="Email">
                </div>
                <div class="input-group">
                    <input type="password" name="password" required placeholder="Password">
                </div>
                <button type="submit" class="login-btn">Login</button>
            </form>
            <?php if(isset($_SESSION['error'])): ?>
                <p class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 
