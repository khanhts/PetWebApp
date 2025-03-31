<?php
class Appointment {
    private $conn;
    private $table = "appointments";

    public $id;
    public $pet;
    public $owner_name;
    public $phone;
    public $email;
    public $appointment_date;
    public $reason;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Tạo lịch hẹn mới
    public function create() {
        $query = "INSERT INTO " . $this->table . " (pet, owner_name, phone, email, appointment_date, reason) 
                  VALUES (:pet, :owner_name, :phone, :email, :appointment_date, :reason)";
        $stmt = $this->conn->prepare($query);

        // Gán giá trị
        $stmt->bindParam(":pet", $this->pet);
        $stmt->bindParam(":owner_name", $this->owner_name);
        $stmt->bindParam(":phone", $this->phone);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":appointment_date", $this->appointment_date);
        $stmt->bindParam(":reason", $this->reason);

        return $stmt->execute();
    }

    // Lấy tất cả lịch hẹn
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY appointment_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
?>
