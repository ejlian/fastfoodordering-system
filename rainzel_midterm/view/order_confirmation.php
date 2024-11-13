<?php
require_once('../includes/session_check.php');
require_once('../classes/Database.php');

$orderId = $_GET['order_id'] ?? null;
if (!$orderId) {
    header('Location: view_menu.php');
    exit;
}

$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("
    SELECT o.*, 
           GROUP_CONCAT(CONCAT(mi.name, ' x', oi.quantity) SEPARATOR ', ') as items
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN menu_items mi ON oi.menu_item_id = mi.id
    WHERE o.id = ? AND o.user_id = ?
    GROUP BY o.id
");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: view_menu.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation - Jollibee Delivery</title>
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/confirmation.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="nav-bar">
        <div class="nav-container">
            <div class="logo">
                <a href="view_menu.php">
                    <img src="../resources/jollibee-logo.png" alt="Jollibee Logo">
                </a>
            </div>
        </div>
    </nav>

    <div class="confirmation-container">
        <div class="confirmation-box">
            <div class="success-checkmark">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>Thank You for Your Order!</h1>
            <p class="order-number">Order #<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></p>
            
            <div class="order-details">
                <p>Your order has been successfully placed and will be delivered to:</p>
                <div class="delivery-info">
                    <p><strong><?php echo htmlspecialchars($order['customer_name']); ?></strong></p>
                    <p><?php echo htmlspecialchars($order['customer_address']); ?></p>
                    <p>Phone: <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                </div>
                
                <div class="delivery-time">
                    <p>Estimated Delivery Time:</p>
                    <p class="time"><?php echo $order['delivery_option'] === 'express' ? '15-25' : '30-45'; ?> minutes</p>
                </div>
            </div>
            
            <a href="view_menu.php" class="back-to-menu">Back to Menu</a>
        </div>
    </div>
</body>
</html>
