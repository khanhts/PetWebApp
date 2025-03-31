<?php
class Appointment
{
    private $conn;
    private $table_name = "appointment";

    public $AppointmentID;
    public $customer_name;
    public $customer_email;
    public $customer_phone;
    public $appointment_date;
    public $doctor_id;
    public $work_hour_id;
    public $notes;
    public $Status; // Trạng thái

    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Lấy danh sách lịch hẹn
    public function getAll()
    {
        try {
            $query = "SELECT * FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (Exception $e) {
            die("Lỗi lấy danh sách lịch hẹn: " . $e->getMessage());
        }
    }

    // Thêm lịch hẹn mới
    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    customer_name = :customer_name,
                    customer_email = :customer_email,
                    customer_phone = :customer_phone,
                    appointment_date = :appointment_date,
                    doctor_id = :doctor_id,
                    work_hour_id = :work_hour_id,
                    notes = :notes,
                    Status = :Status"; // Mặc định trạng thái

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":customer_name", $this->customer_name);
        $stmt->bindParam(":customer_email", $this->customer_email);
        $stmt->bindParam(":customer_phone", $this->customer_phone);
        $stmt->bindParam(":appointment_date", $this->appointment_date);
        $stmt->bindParam(":doctor_id", $this->doctor_id);
        $stmt->bindParam(":work_hour_id", $this->work_hour_id);
        $stmt->bindParam(":notes", $this->notes);
        $stmt->bindParam(":Status", $this->Status); // Mặc định là 0 (Chờ xác nhận)

        return $stmt->execute();
    }

    public function update($id, $customerName, $customerEmail, $customerPhone, $appointmentDate, $status, $notes)
{
    $query = "UPDATE " . $this->table_name . " 
              SET CustomerName = :CustomerName, CustomerEmail = :CustomerEmail, CustomerPhone = :CustomerPhone, 
                  AppointmentDate = :AppointmentDate, Status = :Status, Notes = :Notes 
              WHERE AppointmentID = :AppointmentID";

    $stmt = $this->conn->prepare($query);

    // Bind parameters
    $stmt->bindParam(':CustomerName', $customerName);
    $stmt->bindParam(':CustomerEmail', $customerEmail);
    $stmt->bindParam(':CustomerPhone', $customerPhone);
    $stmt->bindParam(':AppointmentDate', $appointmentDate);
    $stmt->bindParam(':Status', $status);
    $stmt->bindParam(':Notes', $notes);
    $stmt->bindParam(':AppointmentID', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        return true;
    }
    return false;
}

    // Cập nhật trạng thái lịch hẹn
    public function updateStatus($id, $status)
    {
        $query = "UPDATE " . $this->table_name . " SET Status = :status WHERE AppointmentID = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status, PDO::PARAM_INT);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    
        return $stmt->execute();
    }
    
    

    // Xóa lịch hẹn
    public function delete()
{
    $query = "DELETE FROM " . $this->table_name . " WHERE AppointmentID = :AppointmentID";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(":AppointmentID", $this->AppointmentID);

    if ($stmt->execute()) {
        return true;
    }
    return false;
}   

    // Lấy chi tiết lịch hẹn
    public function getOne()
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE AppointmentID = :AppointmentID";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":AppointmentID", $this->AppointmentID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt;
    }





    
}