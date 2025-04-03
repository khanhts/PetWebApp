<?php
require_once "config/database.php";

class CartModel {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function addToCart($product_id) {
        $stmt = $this->conn->prepare("INSERT INTO cart (product_id, quantity) 
                                      VALUES (:product_id, 1)
                                      ON DUPLICATE KEY UPDATE quantity = quantity + 1");
        return $stmt->execute(['product_id' => $product_id]);
    }

    public function getCartItems() {
        $stmt = $this->conn->query("SELECT c.id, p.name, p.price, c.quantity 
                                    FROM cart c 
                                    JOIN products p ON c.product_id = p.id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function removeFromCart($id) {
        $stmt = $this->conn->prepare("DELETE FROM cart WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function clearCart() {
        $stmt = $this->conn->query("DELETE FROM cart");
        return $stmt->execute();
    }
}
?>
