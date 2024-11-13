<?php
require_once('../includes/session_check.php');
require_once('../classes/Database.php');
require_once('../classes/MenuItem.php');

$db = Database::getInstance()->getConnection();

$stmt = $db->query("SELECT m.*, c.name as category_name 
                    FROM menu_items m 
                    JOIN categories c ON m.category_id = c.id 
                    WHERE m.is_available = 1 
                    ORDER BY c.id, m.name");
$menuItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$cartCountStmt = $db->prepare("
    SELECT COUNT(*) as count 
    FROM cart_items 
    WHERE user_id = ?
");
$cartCountStmt->execute([$_SESSION['user_id']]);
$cartCount = $cartCountStmt->fetch(PDO::FETCH_ASSOC)['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menu - Food Delivery</title>
    <link rel="stylesheet" href="../css/menu.css">
</head>
<body>
 
    <nav class="nav-bar">
        <div class="nav-container">
            <div class="logo">
                <img src="../resources/jollibee-logo.jpg" alt="Jollibee Logo">
            </div>
            <div class="nav-links">
                <a href="#">Menu</a>
                <a href="cart.php" class="cart-btn">
                    <span class="cart-icon">
                        🛒
                        <span class="cart-count"><?php echo $cartCount; ?></span>
                    </span>
                    Cart
                </a>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </nav>

    <div class="banner">
        <div class="banner-content">
            <div class="banner-text">
                <h1>Bringing joy to you</h1>
                <p>Have your Jollibee favorites delivered right to your doorstep!</p>
                <a href="#" class="order-now-btn">Order Now</a>
            </div>
            <div class="banner-image">
                <img src="../resources/jolly1.jpg" alt="Chickenjoy">
            </div>
        </div>
    </div>

    <div class="menu-container">
        <?php
        $currentCategory = '';
        foreach ($menuItems as $item): 
            if ($currentCategory != $item['category_name']):
                if ($currentCategory != '') echo '</div>'; 
                $currentCategory = $item['category_name'];
        ?>
                <h2 class="category-title"><?php echo $currentCategory; ?></h2>
                <div class="category-items">
        <?php endif; ?>
        
        <div class="menu-item">
            <img src="../resources/<?php echo $item['image_url']; ?>" alt="<?php echo $item['name']; ?>">
            <div class="item-details">
                <h3><?php echo $item['name']; ?></h3>
                <p class="price">₱<?php echo number_format($item['price'], 2); ?></p>
                <a href="cart.php" class="add-to-cart-btn" onclick="event.preventDefault(); addToCart(<?php echo $item['id']; ?>)">
                    Add to Cart
                </a>
            </div>
        </div>
    <?php endforeach; ?>
    <?php if ($currentCategory != '') echo '</div>'; ?>
</div>
    <script src="../js/menu.js"></script>
</body>
</html>
