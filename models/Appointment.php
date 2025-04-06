<?php
require_once __DIR__ . '/../config/Database.php';

class Appointment {
    private $conn;
    private $table = 'appointments';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Tạo lịch hẹn mới
    public function create($data) {
        $stmt = $this->conn->prepare("INSERT INTO $this->table (pet, owner_name, phone, email, appointment_date, appointment_time, reason) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['pet'],
            $data['owner_name'],
            $data['phone'],
            $data['email'],
            $data['appointment_date'],
            $data['appointment_time'],
            $data['reason']
        ]);
    }

    // Lấy tất cả lịch hẹn
    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM $this->table ORDER BY appointment_date DESC");
        $stmt->execute();
        return $stmt;
    }

    // Đếm số lịch theo ngày
    public function countByDate($date) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM $this->table WHERE appointment_date = ?");
        $stmt->execute([$date]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
}
