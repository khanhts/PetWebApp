<?php
require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/models/Appointment.php';

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
        'title' => $row['pet'], // Tên thú cưng hiển thị trên lịch
        'start' => $row['appointment_date'], // Ngày hẹn
    ];
}

// Trả về dữ liệu JSON
header('Content-Type: application/json');
echo json_encode($appointments);
?>
