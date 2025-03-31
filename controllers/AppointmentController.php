<?php
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../config/Database.php';

class AppointmentController {
    private $model;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->model = new Appointment($db);
    }

    // ✅ Hiển thị danh sách lịch hẹn
    public function index() {
        $stmt = $this->model->getAll();
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../views/appointments/appointments.php';
    }

    // ✅ Hiển thị form thêm lịch hẹn
    public function create() {  // 👉 Đổi tên từ createForm() thành create()
        include __DIR__ . '/../views/appointments/create.php';
    }

    public function store() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (empty($_POST["pet"]) || empty($_POST["owner_name"]) || empty($_POST["phone"]) || empty($_POST["email"]) || empty($_POST["appointment_date"])) {
                die("Vui lòng nhập đầy đủ thông tin.");
            }
    
            $this->model->pet = $_POST["pet"];
            $this->model->owner_name = $_POST["owner_name"];
            $this->model->phone = $_POST["phone"];
            $this->model->email = $_POST["email"];
            $this->model->appointment_date = $_POST["appointment_date"];
            $this->model->reason = $_POST["reason"] ?? '';
    
            if ($this->model->create()) {
                header("Location: index.php?controller=appointment&action=success");
                exit;
            } else {
                echo "Lỗi khi thêm lịch hẹn.";
            }
        }
    }
    
 public function success() {
    include "views/appointments/success.php";
}

}
?>