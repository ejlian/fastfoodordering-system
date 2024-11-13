<?php
class OrderManager {
    private $db;
    private $userId;

    public function __construct($userId) {
        $this->db = Database::getInstance()->getConnection();
        $this->userId = $userId;
    }

    public function placeOrder($data) {
        $response = ['success' => false];
        
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("
                INSERT INTO orders (
                    user_id, 
                    customer_name,
                    customer_phone,
                    customer_address,
                    payment_method,
                    delivery_option,
                    status
                ) VALUES (?, ?, ?, ?, ?, ?, 'pending')
            ");
            
            $stmt->execute([
                $this->userId,
                $data['address']['name'],
                $data['address']['phone'],
                $data['address']['address'],
                $data['payment'],
                $data['delivery']
            ]);
            
            $orderId = $this->db->lastInsertId();
            
            $stmt = $this->db->prepare("
                INSERT INTO order_items (order_id, menu_item_id, quantity, price)
                SELECT ?, menu_item_id, quantity, mi.price
                FROM cart_items ci
                JOIN menu_items mi ON ci.menu_item_id = mi.id
                WHERE ci.user_id = ?
            ");
            $stmt->execute([$orderId, $this->userId]);
            
            $stmt = $this->db->prepare("DELETE FROM cart_items WHERE user_id = ?");
            $stmt->execute([$this->userId]);
            
            $this->db->commit();
            
            $response['success'] = true;
            $response['order_id'] = $orderId;
        } catch (PDOException $e) {
            $this->db->rollBack();
            $response['error'] = $e->getMessage();
        }
        
        return $response;
    }
}