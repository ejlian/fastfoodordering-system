<?php
require_once('../includes/session_check.php');
require_once('../classes/Database.php');

$db = Database::getInstance()->getConnection();
$userId = $_SESSION['user_id'];

$stmt = $db->prepare("
    SELECT ci.*, mi.name, mi.price, mi.image_url 
    FROM cart_items ci 
    JOIN menu_items mi ON ci.menu_item_id = mi.id 
    WHERE ci.user_id = ?
");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart - Jollibee Delivery</title>
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/cart.css">
</head>
<body>

    <nav class="nav-bar">
        <div class="nav-container">
            <div class="logo">
                <a href="view_menu.php">
                    <img src="../resources/jollibee-logo.jpg" alt="Jollibee Logo">
                </a>
            </div>
            <div class="nav-links">
                <a href="view_menu.php">Menu</a>
                <a href="cart.php" class="cart-btn active">
                    <span class="cart-icon">🛒</span>
                    Cart
                </a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </nav>

    <div class="cart-container">
        <h1 class="cart-title">My Cart</h1>
        
        <?php if (empty($cartItems)): ?>
            <div class="empty-cart">
                <div class="empty-cart-message">
                    <h2>Your cart is empty</h2>
                    <p>Looks like you haven't added any items yet.</p>
                    <a href="view_menu.php" class="continue-shopping">Start Ordering</a>
                </div>
            </div>
        <?php else: ?>
            <div class="cart-content">
                <div class="cart-items">
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item" id="cart-item-<?php echo $item['menu_item_id']; ?>">
                            <div class="item-details">
                                <img src="../resources/<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>" class="item-image">
                                <div class="item-info">
                                    <h3><?php echo $item['name']; ?></h3>
                                    <p class="price" id="price-<?php echo $item['menu_item_id']; ?>" data-price="<?php echo $item['price']; ?>">
                                        ₱<?php echo number_format($item['price'], 2); ?>
                                    </p>
                                    <div class="quantity-controls">
                                        <button type="button" class="quantity-btn minus" onclick="updateQuantity(<?php echo $item['menu_item_id']; ?>, -1)">-</button>
                                        <input type="text" id="quantity-<?php echo $item['menu_item_id']; ?>" 
                                               value="<?php echo $item['quantity']; ?>" readonly>
                                        <button type="button" class="quantity-btn plus" onclick="updateQuantity(<?php echo $item['menu_item_id']; ?>, 1)">+</button>
                                    </div>
                                    <p class="total-price" id="total-<?php echo $item['menu_item_id']; ?>">
                                        ₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                    </p>
                                    <button type="button" class="remove-btn" onclick="removeItem(<?php echo $item['menu_item_id']; ?>)">Remove</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="order-summary">
                    <h2>Order Summary</h2>
                    <div class="summary-content">
                        <div class="summary-item">
                            <span>Subtotal</span>
                            <span>₱<?php echo number_format($total, 2); ?></span>
                        </div>
                        <div class="summary-item">
                            <span>Delivery Fee</span>
                            <span>₱49.00</span>
                        </div>
                        <div class="summary-total">
                            <span>Total</span>
                            <span>₱<?php echo number_format($total + 49, 2); ?></span>
                        </div>
                    </div>
                    <div class="checkout-button-container">
                        <a href="checkout.php" class="proceed-to-checkout">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <script src="../js/cart.js"></script>
</body>
</html> 
