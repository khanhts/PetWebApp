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
    // public function getAll() {
    //     $stmt = $this->conn->prepare("SELECT * FROM $this->table ORDER BY appointment_date DESC");
    //     $stmt->execute();
    //     return $stmt;
    // }
    public function getAll() {
        $query = "SELECT * FROM appointments";
        return $this->conn->query($query);
    }
    
    

    // Đếm số lịch theo ngày
    public function countByDate($date) {
        $query = "SELECT COUNT(*) as total FROM appointments WHERE DATE(appointment_date) = :date";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
    

    public function isTimeSlotTaken($date, $time) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM appointments WHERE DATE(appointment_date) = ? AND appointment_time = ?");
        $stmt->execute([$date, $time]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

// ✅ Trả về danh sách ngày đã đủ 5 lịch hẹn
public function getDisabledDates() {
    $query = "
        SELECT DATE(appointment_date) AS date
        FROM appointments
        GROUP BY DATE(appointment_date)
        HAVING COUNT(*) >= 5
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->execute();

    $dates = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $dates[] = $row['date'];
    }

    return $dates;
}

    
    
}
