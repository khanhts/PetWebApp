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
                        <input type="hidden" id="appointment_date" name="appointment_date">
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
                            <label>Lý Do Khám:</label>
                            <textarea name="reason" class="form-control"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Đặt Lịch</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
document.addEventListener("DOMContentLoaded", async function () {
    var calendarEl = document.getElementById("calendar");
    var disabledDates = await fetchDisabledDates(); // Lấy danh sách ngày bị chặn
    var today = new Date().toISOString().split("T")[0];

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        headerToolbar: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay"
        },
        locale: "vi",
        selectable: true,
        editable: false,
        eventLimit: true,
        events: "load_appointments.php",

        // ✅ Xử lý khi chọn ngày
        dateClick: function (info) {
            const selectedDate = info.dateStr;

            if (selectedDate < today) {
                alert("❌ Không thể đặt lịch vào ngày trong quá khứ!");
                return;
            }

            if (disabledDates.includes(selectedDate)) {
                alert("❌ Ngày này đã đầy lịch hoặc là ngày lễ, vui lòng chọn ngày khác!");
                return;
            }

            $("#appointment_date").val(selectedDate);
            $("#appointmentModal").modal("show");
        }
    });

    calendar.render();

    // ✅ Xử lý gửi form đặt lịch bằng AJAX
    $("#appointmentForm").submit(function (e) {
        e.preventDefault();

        $.ajax({
            url: "load_appointments.php", // ✅ Đồng bộ API
            type: "POST",
            data: $(this).serialize(),
            success: function () {
                alert("✅ Đặt lịch thành công!");
                $("#appointmentModal").modal("hide");
                $("#appointmentForm")[0].reset();
                calendar.refetchEvents(); // ✅ Cập nhật lại lịch sau khi đặt
            },
            error: function () {
                alert("❌ Lỗi khi đặt lịch, vui lòng thử lại!");
            }
        });
    });
});

/**
 * 🛑 Lấy danh sách ngày bị khóa (ngày lễ + ngày đã đầy lịch)
 */
async function fetchDisabledDates() {
    const holidays = ["2025-01-01", "2025-04-30", "2025-05-01", "2025-09-02"];

    try {
        const response = await fetch("index.php?controller=appointment&action=getDisabledDates");
        const serverDisabledDates = await response.json();
        return [...new Set([...serverDisabledDates, ...holidays])];
    } catch (error) {
        console.error("❌ Lỗi khi tải danh sách ngày bị chặn:", error);
        return holidays;
    }
}
</script>
</body>
</html>
