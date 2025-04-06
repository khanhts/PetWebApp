<?php
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/Appointment.php';

// Thiết lập múi giờ chính xác
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Kết nối database
$database = new Database();
$db = $database->getConnection();
$appointment = new Appointment($db);

// Lấy tất cả lịch hẹn
$stmt = $appointment->getAll();
$appointments = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $appointments[] = [
        'id'    => $row['id'],
        'title' => $row['pet'] . ' - ' . $row['owner_name'], // Hiển thị thêm tên chủ
        'start' => $row['appointment_date'], // Định dạng chuẩn yyyy-mm-dd hh:mm:ss
        'allDay' => false // Rất quan trọng nếu bạn dùng timeGrid view
    ];
}

// Trả về JSON cho FullCalendar
header('Content-Type: application/json');
echo json_encode($appointments);
?>
