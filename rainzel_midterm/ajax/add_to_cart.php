<?php
session_start();
require_once('../classes/Database.php');
require_once('../classes/CartManager.php');

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        $response['error'] = 'User not logged in';
    } else {
        $data = json_decode(file_get_contents('php://input'), true);
        $cartManager = new CartManager($_SESSION['user_id']);
        $response = $cartManager->addToCart($data['itemId'], $data['quantity']);
    }
}

header('Content-Type: application/json');
echo json_encode($response); 