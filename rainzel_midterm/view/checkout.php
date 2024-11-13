<?php
require_once('../includes/session_check.php');
require_once('../classes/Database.php');

$db = Database::getInstance()->getConnection();
$userId = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $db->prepare("
    SELECT ci.*, mi.name, mi.price, mi.image_url 
    FROM cart_items ci 
    JOIN menu_items mi ON ci.menu_item_id = mi.id 
    WHERE ci.user_id = ?
");
$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$subtotal = 0;
foreach ($cartItems as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Jollibee Delivery</title>
    <link rel="stylesheet" href="../css/menu.css">
    <link rel="stylesheet" href="../css/checkout.css">
</head>
<body>
  
    <nav class="nav-bar">
        <div class="nav-container">
            <div class="logo">
                <a href="view_menu.php">
                    <img src="../resources/jollibee-logo.jpg" alt="Jollibee Logo">
                </a>
            </div>
        </div>
    </nav>

    <div class="checkout-container">
        <div class="checkout-steps">
            <div class="step active" id="step-1">1. Delivery Address</div>
            <div class="step" id="step-2">2. Payment Method</div>
            <div class="step" id="step-3">3. Delivery Option</div>
        </div>

        <div class="checkout-section active" id="address-section">
            <h2>Delivery Address</h2>
            <form id="address-form" class="delivery-form">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" value="<?php echo $user['username']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="address">Complete Address</label>
                    <textarea id="address" name="address" required><?php echo $user['address']; ?></textarea>
                </div>
                <button type="submit" class="next-btn">Continue to Payment</button>
            </form>
        </div>

        <div class="checkout-section" id="payment-section">
            <h2>Payment Method</h2>
            <form id="payment-form">
                <div class="payment-options">
                    <label class="payment-option">
                        <input type="radio" name="payment" value="cod" required>
                        <div class="option-content">
                            <div class="option-details">
                                <h3>Cash on Delivery</h3>
                                <p>Pay when you receive your order</p>
                            </div>
                        </div>
                    </label>
                    
                    <label class="payment-option">
                        <input type="radio" name="payment" value="card" required>
                        <div class="option-content">
                            <div class="option-details">
                                <h3>Credit/Debit Card</h3>
                                <p>Pay securely with your card</p>
                            </div>
                        </div>
                    </label>
                </div>

                <button type="submit" class="next-btn">Continue to Delivery Options</button>
            </form>
        </div>

        <div class="checkout-section" id="delivery-section">
            <h2>Delivery Options</h2>
            <form id="delivery-form">
                <div class="delivery-options">
                    <label class="delivery-option">
                        <input type="radio" name="delivery" value="standard" required>
                        <div class="option-content">
                            <div class="option-details">
                                <h3>Standard Delivery</h3>
                                <p>Estimated delivery: 30-45 mins</p>
                                <span class="delivery-fee">₱49.00</span>
                            </div>
                        </div>
                    </label>
                    
                    <label class="delivery-option">
                        <input type="radio" name="delivery" value="express" required>
                        <div class="option-content">
                            <div class="option-details">
                                <h3>Express Delivery</h3>
                                <p>Estimated delivery: 15-25 mins</p>
                                <span class="delivery-fee">₱99.00</span>
                            </div>
                        </div>
                    </label>
                </div>

                <button type="submit" class="place-order-btn">Place Order</button>
            </form>
        </div>

        <div class="order-summary">
            <h2>Order Summary</h2>
            <div class="summary-items">
                <?php foreach ($cartItems as $item): ?>
                    <div class="summary-item">
                        <span><?php echo $item['quantity']; ?>x <?php echo $item['name']; ?></span>
                        <span>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="summary-totals">
                <div class="subtotal">
                    <span>Subtotal</span>
                    <span>₱<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="delivery-fee">
                    <span>Delivery Fee</span>
                    <span id="delivery-fee">₱49.00</span>
                </div>
                <div class="total">
                    <span>Total</span>
                    <span id="total-amount">₱<?php echo number_format($subtotal + 49, 2); ?></span>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/checkout.js"></script>
</body>
</html> 
