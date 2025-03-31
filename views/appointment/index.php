<?php
require_once(__DIR__ . "/../../config/database.php");
require_once(__DIR__ . "/../../models/Appointment.php");

$database = new Database();
$db = $database->getConnection();

$appointment = new Appointment($db);
$result = $appointment->getAll();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách lịch hẹn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 1000px; margin: auto; padding-top: 20px; }
        .table th, .table td { vertical-align: middle; }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center text-primary">📅 Danh sách lịch hẹn</h2>
        
        <!-- Thanh tìm kiếm -->
        <div class="d-flex justify-content-between my-3">
            <input type="text" id="search" class="form-control w-50" placeholder="🔍 Tìm kiếm theo tên khách hàng...">
        </div>

        <!-- Bảng hiển thị danh sách lịch hẹn -->
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Khách hàng</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Ngày hẹn</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody id="appointmentTable">
                <?php $stt = 1; ?>
                <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo $stt++; ?></td>
                    <td><?php echo htmlspecialchars($row['CustomerName']); ?></td>
                    <td><?php echo htmlspecialchars($row['CustomerEmail']); ?></td>
                    <td><?php echo htmlspecialchars($row['CustomerPhone']); ?></td>
                    <td><?php echo date("d/m/Y H:i", strtotime($row['AppointmentDate'])); ?></td>
                    <td>
                        <select class="form-select status-select" data-id="<?php echo $row['AppointmentID']; ?>">
                            <option value="0" <?php echo $row['Status'] == 0 ? 'selected' : ''; ?>>⏳ Chờ xác nhận</option>
                            <option value="1" <?php echo $row['Status'] == 1 ? 'selected' : ''; ?>>✅ Đã xác nhận</option>
                            <option value="2" <?php echo $row['Status'] == 2 ? 'selected' : ''; ?>>❌ Đã hủy</option>
                        </select>   
                    </td>
                    <td>
                    <a href="detail.php?id=<?php echo $row['AppointmentID']; ?>" class="btn btn-info btn-sm">📄</a>
                        <a href="edit.php?id=<?php echo $row['AppointmentID']; ?>" class="btn btn-warning btn-sm">✏️</a>
                        <a href="delete.php?id=<?php echo $row['AppointmentID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa lịch hẹn này?')">🗑️</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Tìm kiếm lịch hẹn theo tên khách hàng
        document.getElementById("search").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll("#appointmentTable tr");

            rows.forEach(row => {
                let name = row.cells[1].textContent.toLowerCase();
                row.style.display = name.includes(filter) ? "" : "none";
            });
        });

        document.querySelectorAll(".status-select").forEach(select => {
    select.addEventListener("change", async function () {
        let appointmentId = this.dataset.id;  // Dùng dataset.id thay vì dataset.Status
        let newStatus = this.value;

        try {
            let response = await fetch("update_status.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id: appointmentId, status: newStatus })
            });

            let data = await response.json();
            if (data.success) {
                alert("✅ Cập nhật thành công!");
            } else {
                alert("❌ " + data.message);
            }
        } catch (error) {
            console.error("Lỗi:", error);
        }
    });
});

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>