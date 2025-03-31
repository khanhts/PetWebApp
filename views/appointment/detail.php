<?php
require_once(__DIR__ . "/../../config/database.php");
require_once(__DIR__ . "/../../models/Appointment.php");

// Kết nối CSDL
$database = new Database();
$db = $database->getConnection();
$appointment = new Appointment($db);

// Lấy ID từ URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$appointment->AppointmentID = $id;
$stmt = $appointment->getOne();
$detail = $stmt->fetch(PDO::FETCH_ASSOC); // Chuyển từ PDOStatement sang mảng

if (!$detail) {
    die("Không tìm thấy lịch hẹn!");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết lịch hẹn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-primary">📅 Chi tiết lịch hẹn</h2>
        <table class="table table-bordered">
            <tr>
                <th>Mã lịch hẹn</th>
                <td><?php echo htmlspecialchars($detail['AppointmentID']); ?></td>
            </tr>
            <tr>
                <th>Khách hàng</th>
                <td><?php echo htmlspecialchars($detail['CustomerName']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($detail['CustomerEmail']); ?></td>
            </tr>
            <tr>
                <th>Số điện thoại</th>
                <td><?php echo htmlspecialchars($detail['CustomerPhone']); ?></td>
            </tr>
            <tr>
                <th>Ngày hẹn</th>
                <td><?php echo date("d/m/Y H:i", strtotime($detail['AppointmentDate'])); ?></td>
            </tr>
            <tr>
                <th>Trạng thái</th>
                <td>
                    <?php
                    $statusText = ["⏳ Chờ xác nhận", "✅ Đã xác nhận", "❌ Đã hủy"];
                    echo $statusText[$detail['Status']];
                    ?>
                </td>
            </tr>
            <tr>
                <th>Ghi chú</th>
                <td><?php echo htmlspecialchars($detail['Notes'] ?? 'Không có'); ?></td>
            </tr>
        </table>
        <a href="index.php" class="btn btn-secondary">⬅ Quay lại</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
