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

// Xử lý cập nhật nếu form được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Cập nhật thông tin lịch hẹn từ form
    $appointment->CustomerName = $_POST['CustomerName'];
    $appointment->CustomerEmail = $_POST['CustomerEmail'];
    $appointment->CustomerPhone = $_POST['CustomerPhone'];
    $appointment->AppointmentDate = $_POST['AppointmentDate'];
    $appointment->Status = $_POST['Status'];

    if ($appointment->update()) {
        // Nếu cập nhật thành công, điều hướng về trang danh sách
        header("Location: index.php");
        exit();
    } else {
        $error_message = "Cập nhật thất bại. Vui lòng thử lại.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa lịch hẹn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-primary">✏️ Chỉnh sửa lịch hẹn</h2>

        <?php if (isset($error_message)) { echo '<div class="alert alert-danger">' . $error_message . '</div>'; } ?>

        <form action="edit.php?id=<?php echo $id; ?>" method="POST">
            <div class="mb-3">
                <label for="CustomerName" class="form-label">Tên khách hàng</label>
                <input type="text" class="form-control" id="CustomerName" name="CustomerName" value="<?php echo htmlspecialchars($detail['CustomerName']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="CustomerEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="CustomerEmail" name="CustomerEmail" value="<?php echo htmlspecialchars($detail['CustomerEmail']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="CustomerPhone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="CustomerPhone" name="CustomerPhone" value="<?php echo htmlspecialchars($detail['CustomerPhone']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="AppointmentDate" class="form-label">Ngày hẹn</label>
                <input type="datetime-local" class="form-control" id="AppointmentDate" name="AppointmentDate" value="<?php echo date("Y-m-d\TH:i", strtotime($detail['AppointmentDate'])); ?>" required>
            </div>

            <div class="mb-3">
                <label for="Status" class="form-label">Trạng thái</label>
                <select class="form-select" id="Status" name="Status" required>
                    <option value="0" <?php echo $detail['Status'] == 0 ? 'selected' : ''; ?>>⏳ Chờ xác nhận</option>
                    <option value="1" <?php echo $detail['Status'] == 1 ? 'selected' : ''; ?>>✅ Đã xác nhận</option>
                    <option value="2" <?php echo $detail['Status'] == 2 ? 'selected' : ''; ?>>❌ Đã hủy</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="index.php" class="btn btn-secondary">Quay lại</a>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
