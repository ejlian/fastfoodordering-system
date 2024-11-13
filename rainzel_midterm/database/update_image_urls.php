<?php
require_once('../classes/Database.php');

$db = Database::getInstance()->getConnection();

$updates = [
    "UPDATE menu_items SET image_url = 'jolly1.jpg' WHERE name = 'Chickenjoy Solo'",
    "UPDATE menu_items SET image_url = 'jolly2.jpg' WHERE name = 'Spicy Chickenjoy'",
    "UPDATE menu_items SET image_url = 'jolly3.jpg' WHERE name = 'Yumburger'",
    "UPDATE menu_items SET image_url = 'jolly4.jpg' WHERE name = 'Champ'",
    "UPDATE menu_items SET image_url = 'jolly5.jpg' WHERE name = 'Chicken Joy with Rice'",
    "UPDATE menu_items SET image_url = 'jolly6.jpg' WHERE name = 'Palabok Fiesta'",
    "UPDATE menu_items SET image_url = 'jolly7.jpg' WHERE name = 'Burger Steak'"
];

try {
    foreach ($updates as $sql) {
        $db->exec($sql);
    }
    echo "Image paths updated successfully!";
} catch(PDOException $e) {
    echo "Error updating image paths: " . $e->getMessage();
} 
