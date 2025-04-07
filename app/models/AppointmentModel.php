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

    public function getAll()
    {
        $query = "
            SELECT 
                a.*, 
                u.fullname AS owner_name, 
                u.email,
                u.phone
            FROM {$this->table_name} a
            LEFT JOIN users u ON a.user_id = u.id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function searchAppointments(string $search): array
    {
        $query = "
            SELECT 
                a.*, 
                u.fullname AS owner_name, 
                u.phone, 
                u.email, 
                u.address
            FROM {$this->table_name} a
            LEFT JOIN users u ON a.user_id = u.id
            WHERE u.fullname LIKE :search 
               OR u.phone LIKE :search 
               OR u.email LIKE :search
        ";
        $stmt = $this->conn->prepare($query);
        $searchTerm = '%' . $search . '%';
        $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAppointmentsByUserId(int $userId): array
    {
        $query = "
            SELECT 
                a.*, 
                u.fullname AS owner_name, 
                u.phone, 
                u.email
            FROM {$this->table_name} a
            LEFT JOIN users u ON a.user_id = u.id
            WHERE a.user_id = :user_id
            ORDER BY a.appointment_date DESC
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}