<?php
require_once(__DIR__ . "/../../config/database.php");
require_once(__DIR__ . "/../../models/Appointment.php");

$database = new Database();
$db = $database->getConnection();
$appointment = new Appointment($db);

// Lấy ID từ URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Nếu không có ID, điều hướng về trang danh sách
if ($id == 0) {
    header("Location: index.php"); // Điều hướng về danh sách nếu không có ID
    exit();
}

// Lấy thông tin lịch hẹn
$appointment->AppointmentID = $id;
$detail = $appointment->getOne()->fetch(PDO::FETCH_ASSOC);

// Nếu không tìm thấy lịch hẹn, điều hướng về trang danh sách
if (!$detail) {
    header("Location: index.php"); // Điều hướng về danh sách nếu không tìm thấy lịch hẹn
    exit();
}

// Xử lý xóa lịch hẹn
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($appointment->delete()) {
        // Nếu xóa thành công, điều hướng về trang danh sách
        header("Location: index.php");
        exit();
    } else {
        $error_message = "Xóa lịch hẹn thất bại. Vui lòng thử lại.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xóa lịch hẹn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-danger">❌ Xóa lịch hẹn</h2>

        <p>Bạn có chắc chắn muốn xóa lịch hẹn này không?</p>
        <p><strong>Tên khách hàng:</strong> <?php echo htmlspecialchars($detail['CustomerName']); ?></p>
        <p><strong>Ngày hẹn:</strong> <?php echo date("d/m/Y H:i", strtotime($detail['AppointmentDate'])); ?></p>

        <form action="delete.php?id=<?php echo $id; ?>" method="POST">
            <button type="submit" class="btn btn-danger">Xóa</button>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </form>

        <?php if (isset($error_message)) { echo '<div class="alert alert-danger mt-3">' . $error_message . '</div>'; } ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
