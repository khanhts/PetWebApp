<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Lấy tất cả người dùng
    public function getAllUsers() {
        $stmt = $this->pdo->query("SELECT id, name, email, role FROM users");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy danh sách khách hàng
    public function getCustomers() {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE role = 'customer'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVetById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE role = 'vet' AND id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
