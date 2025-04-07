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
            <h2 class="text-center">📅 Make an appointment</h2>
            <a href="/">Return</a>
            <div id="calendar"></div>
        </div>

        <!-- ✅ Modal Form Đặt Lịch -->
        <div id="appointmentModal" class="modal fade" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Appointment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="appointmentForm">
                            <input type="hidden" id="appointment_date">
                            <div class="mb-3">
                                <label>Pet info:</label>
                                <input type="text" name="pet" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <input type="text" name="user_id" class="form-control" hidden value="<?php echo $_SESSION['user_id']; ?>">
                            </div>
                            <div class="mb-3">
                                <label>Appointment time:</label>
                                <select name="appointment_time" class="form-control" required>
                                    <option value="">-- Choose Time--</option>
                                    <option value="08:00">08:00</option>
                                    <option value="08:30">08:30</option>
                                    <option value="09:00">09:00</option>
                                    <option value="09:30">09:30</option>
                                    <option value="10:00">10:00</option>
                                    <option value="10:30">10:30</option>
                                    <option value="11:00">11:00</option>
                                    <option value="13:00">13:00</option>
                                    <option value="13:30">13:30</option>
                                    <option value="14:00">14:00</option>
                                    <option value="14:30">14:30</option>
                                    <option value="15:00">15:00</option>
                                    <option value="15:30">15:30</option>
                                    <option value="16:00">16:00</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Context:</label>
                                <textarea name="reason" class="form-control"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Apply</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
document.addEventListener("DOMContentLoaded", async function () {
    var calendarEl = document.getElementById("calendar");
    var disabledDates = await fetchDisabledDates();
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
        events: "load_appointments.php",

        // ✅ Khi click chọn ngày
        dateClick: async function (info) {
            const selectedDate = info.dateStr;
            const jsDate = new Date(selectedDate);

            if (selectedDate < today) {
                alert("❌ Can't choose days in the past!");
                return;
            }

            const dayOfWeek = jsDate.getDay(); // 0 (CN) đến 6 (T7)
            if (dayOfWeek === 0 || dayOfWeek === 6) {
                alert("❌ Invalid date!");
                return;
            }

            if (disabledDates.includes(selectedDate)) {
                alert("❌ Holiday!");
                return;
            }

            const count = await getAppointmentCountForDate(selectedDate);
                if (count >= 5) {
                    alert("❌ Maximum appointment reach for the day!");
                    return;
                }


            $("#appointment_date").val(selectedDate);
            $("#appointmentModal").modal("show");
        }
    });

    calendar.render();


    // ✅ Gửi form đặt lịch
    $("#appointmentForm").submit(function (e) {
        e.preventDefault();

        const date = $("#appointment_date").val();
        const time = $("select[name='appointment_time']").val();

        if (!time) {
            alert("❌ Please choose an appointment time!");
            return;
        }
        
        const appointmentDateTime = `${date} ${time}:00`;

        const formData = $(this).serializeArray();
        formData.push({ name: "appointment_date", value: appointmentDateTime });

        $.ajax({
            url: "/appointment/create",
            type: "POST",
            data: $.param(formData),
            success: function () {
                alert("✅ Successfully apply!");
                $("#appointmentModal").modal("hide");
                $("#appointmentForm")[0].reset();
                calendar.refetchEvents();
            },
            error: function (xhr) {
            let msg = "❌ Can't make an appointment!";
            try {
                const res = JSON.parse(xhr.responseText);
                if (res.message) msg = res.message;
            } catch (e) {
                console.warn("❌ Error:", e);
            }
            alert(msg);
            }
        });
    });
});

/**
 * 🛑 Hàm lấy danh sách ngày bị chặn (ngày lễ + ngày đã đầy)
 */
async function fetchDisabledDates() {
    const holidays = ["2025-01-01", "2025-04-30", "2025-05-01", "2025-09-02"];

    try {
        const response = await fetch("/appointment/getDisabledDates");
        const serverDisabledDates = await response.json();
        return [...new Set([...serverDisabledDates, ...holidays])];
    } catch (error) {
        console.error("❌ Fetch dates fail:", error);
        return holidays;
    }
}

/**
 * 🔍 Hàm đếm số lịch hẹn trong 1 ngày (AJAX)
 */
async function getAppointmentCountForDate(date) {
    try {
        const response = await fetch(`index.php?controller=appointment&action=countByDate&date=yyyy-mm-dd`);
        const data = await response.json();
        return data.count || 0;
    } catch (error) {
        console.error("❌ Fail checking number of day:", error);
        return 0;
    }
}
</script>

    </body>
    </html>