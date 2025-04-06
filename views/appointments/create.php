<?php
require_once __DIR__ . '/../../models/Appointment.php';
require_once __DIR__ . '/../../config/Database.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt Lịch Khám Thú Cưng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">📅 Đặt Lịch Khám Thú Cưng</h2>

    <form id="appointmentForm" method="POST" action="index.php?controller=appointment&action=add">
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
            <input type="text" name="phone" class="form-control" required pattern="\d{10}" title="Số điện thoại phải có 10 chữ số">
        </div>
        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Ngày Hẹn:</label>
            <input type="date" id="appointment_date" name="appointment_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Giờ Hẹn:</label>
            <select name="appointment_time" class="form-control" required>
                <option value="">-- Chọn giờ --</option>
                <option value="08:00">08:00</option>
                <option value="09:00">09:00</option>
                <option value="10:00">10:00</option>
                <option value="11:00">11:00</option>
                <option value="13:00">13:00</option>
                <option value="14:00">14:00</option>
                <option value="15:00">15:00</option>
                <option value="16:00">16:00</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Lý Do Khám:</label>
            <textarea name="reason" class="form-control"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Đặt Lịch</button>
        <a href="index.php" class="btn btn-secondary">🔙 Quay Lại</a>
    </form>
</div>

<script>
const holidays = ["2025-01-01", "2025-04-30", "2025-05-01", "2025-09-02"];

$(document).ready(function () {
    $("#appointmentForm").submit(async function (e) {
        const selectedDate = $("#appointment_date").val();
        const dateObj = new Date(selectedDate);
        const day = dateObj.getDay();
        const today = new Date().toISOString().split("T")[0];

        if (selectedDate < today) {
            alert("❌ Không thể chọn ngày trong quá khứ.");
            e.preventDefault();
            return;
        }

        if (day === 0 || day === 6) {
            alert("❌ Không nhận lịch vào Thứ 7 hoặc Chủ Nhật.");
            e.preventDefault();
            return;
        }

        if (holidays.includes(selectedDate)) {
            alert("❌ Đây là ngày lễ, không thể đặt lịch.");
            e.preventDefault();
            return;
        }

        // Gọi AJAX để kiểm tra số lượng lịch trong ngày
        const count = await getAppointmentCountForDate(selectedDate);
        if (count >= 5) {
            alert("❌ Ngày này đã đủ 5 lịch hẹn.");
            e.preventDefault();
        }
    });
});

async function getAppointmentCountForDate(date) {
    try {
        const response = await fetch(`index.php?controller=appointment&action=countByDate&date=${date}`);
        const data = await response.json();
        return data.count || 0;
    } catch (err) {
        console.error("Lỗi khi kiểm tra lịch:", err);
        return 0;
    }
}
</script>

</body>
</html>
