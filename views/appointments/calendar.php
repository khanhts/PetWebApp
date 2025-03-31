<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch hẹn thú cưng</title>
    
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

    <!-- JQuery, FullCalendar, Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">📅 Quản Lý Lịch Hẹn</h2>
        <div id="calendar"></div>
    </div>

    <!-- ✅ Modal Form Đặt Lịch -->
    <div id="appointmentModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Đặt Lịch Khám</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="appointmentForm">
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
                            <!-- <label>Ngày Hẹn:</label>
                            <input type="text" id="appointment_date" name="appointment_date" class="form-control" readonly>
                        </div> -->
                        <div class="mb-3">
                            <label>Lý Do Khám:</label>
                            <textarea name="reason" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Đặt Lịch</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
        <script src="public/js/appointment.js"></script>
</body>
</html>
