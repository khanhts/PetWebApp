<?php
require_once(__DIR__ . "/../../config/database.php");
require_once(__DIR__ . "/../../models/Appointment.php");

$database = new Database();
$db = $database->getConnection();
$appointment = new Appointment($db);

// Lấy dữ liệu từ POST request
$data = json_decode(file_get_contents("php://input"));

// Kiểm tra dữ liệu hợp lệ
if (isset($data->id) && isset($data->status)) {
    $id = $data->id;
    $status = $data->status;

    // Cập nhật trạng thái lịch hẹn
    if ($appointment->updateStatus($id, $status)) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Cập nhật thất bại."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Dữ liệu không hợp lệ."]);
}
?>
