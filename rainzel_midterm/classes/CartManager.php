<?php
class CartManager {
    private $db;
    private $userId;

    public function __construct($userId) {
        $this->db = Database::getInstance()->getConnection();
        $this->userId = $userId;
    }

    public function addToCart($itemId, $quantity) {
        $response = ['success' => false];
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM cart_items WHERE user_id = ? AND menu_item_id = ?");
            $stmt->execute([$this->userId, $itemId]);
            $existingItem = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existingItem) {
                $stmt = $this->db->prepare("UPDATE cart_items SET quantity = quantity + ? WHERE user_id = ? AND menu_item_id = ?");
                $stmt->execute([$quantity, $this->userId, $itemId]);
            } else {
                $stmt = $this->db->prepare("INSERT INTO cart_items (user_id, menu_item_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$this->userId, $itemId, $quantity]);
            }
            
            $response['success'] = true;
            $response['cartCount'] = $this->getCartCount();
        } catch (PDOException $e) {
            $response['error'] = $e->getMessage();
        }
        
        return $response;
    }

    public function removeFromCart($itemId) {
        $response = ['success' => false];
        
        try {
            $stmt = $this->db->prepare("DELETE FROM cart_items WHERE user_id = ? AND menu_item_id = ?");
            $stmt->execute([$this->userId, $itemId]);
            
            $response['success'] = true;
            $response['cartCount'] = $this->getCartCount();
            $response['subtotal'] = $this->getCartSubtotal();
        } catch (PDOException $e) {
            $response['error'] = $e->getMessage();
        }
        
        return $response;
    }

    public function updateQuantity($itemId, $quantity) {
        $response = ['success' => false];
        
        try {
            $stmt = $this->db->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND menu_item_id = ?");
            $stmt->execute([$quantity, $this->userId, $itemId]);
            
            $response['success'] = true;
            $response['subtotal'] = $this->getCartSubtotal();
            $response['cartCount'] = $this->getCartCount();
        } catch (PDOException $e) {
            $response['error'] = $e->getMessage();
        }
        
        return $response;
    }

    private function getCartCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM cart_items WHERE user_id = ?");
        $stmt->execute([$this->userId]);
        return $stmt->fetchColumn();
    }

    private function getCartSubtotal() {
        $stmt = $this->db->prepare("
            SELECT SUM(ci.quantity * mi.price) 
            FROM cart_items ci
            JOIN menu_items mi ON ci.menu_item_id = mi.id
            WHERE ci.user_id = ?
        ");
        $stmt->execute([$this->userId]);
        return (float)$stmt->fetchColumn() ?? 0;
    }

    public function getCartItems() {
        $stmt = $this->db->prepare("
            SELECT ci.*, mi.name, mi.price, mi.image_url
            FROM cart_items ci
            JOIN menu_items mi ON ci.menu_item_id = mi.id
            WHERE ci.user_id = ?
        ");
        $stmt->execute([$this->userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} 