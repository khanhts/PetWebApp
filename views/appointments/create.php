<?php
require_once __DIR__ . '/../../models/Appointment.php';
require_once __DIR__ . '/../../config/Database.php';

// Lấy ngày từ URL
$appointment_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Lịch Hẹn</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center">📅 Đặt Lịch Khám Thú Cưng</h2>
        
        <!-- Form đặt lịch -->
        <form method="POST" action="index.php?controller=appointment&action=store">
            <div class="mb-3">
                <label>Thú Cưng:</label>
                <input type="text" name="pet" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Tên Chủ:</label>
                <input type="text" name="owner_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Số Điện Thoại:</label>
                <input type="text" name="phone" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email:</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Ngày Hẹn:</label>
                <input type="date" name="appointment_date" class="form-control" value="<?= $appointment_date ?>">

            </div>
            <div class="mb-3">
                <label>Lý Do Khám:</label>
                <textarea name="reason" class="form-control"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Đặt Lịch</button>
        </form>
        
        <a href="index.php" class="btn btn-secondary mt-3">🔙 Quay Lại</a>
    </div>
    <script src="public/js/appointment.js"></script>

</body>
</html>