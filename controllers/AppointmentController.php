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
    public function create() {
        include __DIR__ . '/../views/appointments/create.php';
    }

    // ✅ Lưu lịch hẹn mới với ràng buộc
    public function store() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $requiredFields = ['pet', 'owner_name', 'phone', 'email', 'appointment_date'];

            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    http_response_code(400);
                    echo "Vui lòng nhập đầy đủ thông tin.";
                    return;
                }
            }

            $appointmentDateTime = $_POST["appointment_date"]; // Format: yyyy-mm-dd hh:mm:ss
            $dateOnly = substr($appointmentDateTime, 0, 10);
            $dayOfWeek = date('w', strtotime($dateOnly)); // 0 = Chủ Nhật, 6 = Thứ 7

            if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                http_response_code(400);
                echo "❌ Không thể đặt lịch vào Thứ 7 và Chủ Nhật!";
                return;
            }

            // $existingCount = $this->model->countByDate($dateOnly);
            // if ($existingCount >= 5) {
            //     http_response_code(400);
            //     echo "❌ Ngày này đã đủ 5 lịch hẹn!";
            //     return;
            // }

            $data = [
                'pet' => $_POST["pet"],
                'owner_name' => $_POST["owner_name"],
                'phone' => $_POST["phone"],
                'email' => $_POST["email"],
                'appointment_date' => $appointmentDateTime,
                'appointment_time' => $_POST["appointment_time"] ?? null,
                'reason' => $_POST["reason"] ?? ''
            ];

            if ($this->model->create($data)) {
                header("Location: index.php?controller=appointment&action=success");
                exit;
            } else {
                http_response_code(500);
                echo "❌ Lỗi khi lưu lịch hẹn.";
            }
        }
    }

    // ✅ API: Đếm số lịch theo ngày
    public function countByDate() {
        $date = $_GET['date'] ?? null;
        if (!$date) {
            echo json_encode(['count' => 0]);
            return;
        }
    
        $count = $this->model->countByDate($date);
        echo json_encode(['count' => $count]);
    }
    

    // ✅ Trang thông báo đặt lịch thành công
    public function success() {
        include "views/appointments/success.php";
    }

    // ✅ Nhận dữ liệu từ form modal (AJAX)
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $database = new Database();
            $db = $database->getConnection();
            $appointment = new Appointment($db);
    
            $data = [
                'pet' => $_POST['pet'],
                'owner_name' => $_POST['owner_name'],
                'phone' => $_POST['phone'],
                'email' => $_POST['email'],
                'appointment_date' => $_POST['appointment_date'], // yyyy-mm-dd hh:mm:ss
                'appointment_time' => $_POST['appointment_time'], // hh:mm
                'reason' => $_POST['reason']
            ];
    
            $dateOnly = substr($data['appointment_date'], 0, 10);
            $timeOnly = $data['appointment_time'];
    
            // ❌ Kiểm tra trùng giờ
            if ($appointment->isTimeSlotTaken($dateOnly, $timeOnly)) {
                http_response_code(400);
                echo json_encode(['message' => '❌ Giờ hẹn này đã có người đặt!']);
                return;
            }
    
            // ✅ Tạo lịch nếu không trùng
            $result = $appointment->create($data);
    
            if ($result) {
                echo json_encode(['message' => '✅ Đặt lịch thành công']);
            } else {
                http_response_code(500);
                echo json_encode(['message' => '❌ Đặt lịch thất bại']);
            }
        }
    }

// ✅ API: Lấy danh sách ngày bị chặn (đã đủ 5 lịch hẹn)
public function getDisabledDates() {
    $stmt = $this->model->getAll();
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $dateCounts = [];

    foreach ($appointments as $appointment) {
        $date = substr($appointment['appointment_date'], 0, 10);
        if (!isset($dateCounts[$date])) {
            $dateCounts[$date] = 0;
        }
        $dateCounts[$date]++;
    }

    $disabledDates = [];
    foreach ($dateCounts as $date => $count) {
        if ($count >= 5) {
            $disabledDates[] = $date;
        }
    }

    header('Content-Type: application/json');
    echo json_encode($disabledDates);
}


    
    
    }

?>
