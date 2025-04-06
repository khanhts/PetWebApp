<?php
namespace App\Models;
use PDO;
use PDOException;

class AppointmentModel {
    private $conn;
    private $table_name = 'appointment';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Tạo lịch hẹn mới
    public function create($data) {
        echo "User ID: " . $data['user_id'];
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table_name} (pet_info, user_id,appointment_date, appointment_time, context)
            VALUES (:pet_info, :user_id, :appointment_date, :appointment_time, :context)
        ");
        $stmt->bindParam(':pet_info', $data['pet_info']);
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':appointment_date', $data['appointment_date']);
        $stmt->bindParam(':appointment_time', $data['appointment_time']);
        $stmt->bindParam(':context', $data['context']);
        return $stmt->execute();
    }

    public function getAll() {
        $query = "SELECT * FROM {$this->table_name}";
        return $this->conn->query($query);
    }
    
    public function countByDate($date) {
        $query = "SELECT COUNT(*) as total FROM {$this->table_name} WHERE DATE(appointment_date) = :date";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
    

    public function isTimeSlotTaken($date, $time) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) as count FROM {$this->table_name} WHERE DATE(appointment_date) = ? AND appointment_time = ?");
        $stmt->execute([$date, $time]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

public function getDisabledDates() {
    $query = "
        SELECT DATE(appointment_date) AS date
        FROM {$this->table_name}
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