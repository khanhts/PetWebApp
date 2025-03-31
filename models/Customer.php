<?php
require_once 'config/database.php';

class Customer {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllCustomerIds() {
        $stmt = $this->pdo->prepare("SELECT id FROM customers");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>
