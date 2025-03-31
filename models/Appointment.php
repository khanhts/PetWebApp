<?php
require_once 'config/database.php';

class Appointment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAppointmentsByCustomer($customerId) {
        $stmt = $this->pdo->prepare("SELECT * FROM appointments WHERE customer_id = ?");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAppointmentById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM appointments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function cancelAppointment($id) {
        $stmt = $this->pdo->prepare("UPDATE appointments SET status = 'Đã hủy' WHERE id = ?");
        return $stmt->execute([$id]);
    }
   
    
}
?>
