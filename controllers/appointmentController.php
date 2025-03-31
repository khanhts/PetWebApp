<?php
require_once "../config/database.php";
require_once "../models/Appointment.php";

class AppointmentController
{
    private $appointment;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->appointment = new Appointment($db);
    }

    public function index()
    {
        $stmt = $this->appointment->getAll();
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include "../views/appointment-list.php";
    }

    public function updateStatus()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!isset($data['AppointmentID'], $data['Status'])) {
                echo json_encode(["success" => false, "message" => "Dữ liệu không hợp lệ"]);
                return;
            }
            
            $appointmentID = filter_var($data['AppointmentID'], FILTER_VALIDATE_INT);
            $status = filter_var($data['Status'], FILTER_VALIDATE_INT);
            
            if ($appointmentID === false || $status === false || $status < 0 || $status > 2) {
                echo json_encode(["success" => false, "message" => "Dữ liệu không hợp lệ"]);
                return;
            }

            if ($this->appointment->updateStatus($appointmentID, $status)) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "message" => "Cập nhật thất bại"]);
            }
        }
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'updateStatus') {
    $controller = new AppointmentController();
    $controller->updateStatus();
} else {
    $controller = new AppointmentController();
    $controller->index();
}
