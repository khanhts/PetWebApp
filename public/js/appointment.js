document.addEventListener("DOMContentLoaded", async function () {
    const calendarEl = document.getElementById("calendar");
    const dateInput = document.querySelector('input[name="appointment_date"]');
    const timeSelect = document.getElementById("appointmentTime");

    if (calendarEl) {
        await setupCalendar(dateInput, timeSelect);
    }

    if (dateInput) {
        await setupDatePicker(dateInput, timeSelect);
    }
});

/**
 * 📅 Khởi tạo FullCalendar với sự kiện chọn ngày
 */
async function setupCalendar(dateInput, timeSelect) {
    const calendarEl = document.getElementById("calendar");
    const disabledDates = await fetchDisabledDates();
    const today = new Date().toISOString().split("T")[0];

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: "dayGridMonth",
        locale: "vi",
        selectable: true, // ✅ Cho phép chọn ngày
        editable: false, // Không cho phép kéo thả
        eventLimit: true,
        events: async function (fetchInfo, successCallback, failureCallback) {
            try {
                const response = await fetch("index.php?controller=appointment&action=getAppointments");
                const data = await response.json();
                const events = data.map(appt => ({
                    title: `${appt.pet} - ${appt.ownerName}`,
                    start: appt.appointmentDate,
                    extendedProps: { id: appt._id }
                }));
                successCallback(events);
            } catch (error) {
                failureCallback(error);
            }
        },
        dateClick: async function (info) {
            const selectedDate = info.dateStr;

            // ❌ Chặn ngày trong quá khứ
            if (selectedDate < today) {
                alert("❌ Không thể đặt lịch vào ngày trong quá khứ!");
                return;
            }

            // ❌ Kiểm tra nếu ngày này bị chặn
            if (disabledDates.includes(selectedDate)) {
                alert("❌ Ngày này đã đầy lịch hoặc là ngày lễ, vui lòng chọn ngày khác!");
                return;
            }

            // ✅ Cập nhật input ngày đặt lịch và tải danh sách giờ
            if (dateInput) {
                dateInput.value = selectedDate;
                await loadAvailableTimes(selectedDate, timeSelect);
            }
        }
    });

    calendar.render();
}

/**
 * 📅 Xử lý chọn ngày & tải giờ trống từ input
 */
async function setupDatePicker(dateInput, timeSelect) {
    const disabledDates = await fetchDisabledDates();
    const today = new Date().toISOString().split("T")[0];

    dateInput.setAttribute("min", today);

    dateInput.addEventListener("change", async function () {
        if (!dateInput.value) return;

        if (disabledDates.includes(dateInput.value)) {
            alert("❌ Ngày này là ngày lễ hoặc đã đầy lịch, vui lòng chọn ngày khác!");
            dateInput.value = "";
            return;
        }

        await loadAvailableTimes(dateInput.value, timeSelect);
    });
}

/**
 * 🛑 Lấy danh sách ngày bị khóa (ngày lễ + ngày đã đầy lịch)
 */
async function fetchDisabledDates() {
    const holidays = ["2025-01-01", "2025-04-30", "2025-05-01", "2025-09-02"]; // Ngày lễ cố định

    try {
        const response = await fetch("index.php?controller=appointment&action=getDisabledDates");
        const serverDisabledDates = await response.json();
        return [...new Set([...serverDisabledDates, ...holidays])]; // Gộp danh sách
    } catch (error) {
        console.error("❌ Lỗi khi tải danh sách ngày bị chặn:", error);
        return holidays;
    }
}

/**
 * 🕒 Lấy danh sách giờ trống của ngày đã chọn
 */
async function loadAvailableTimes(date, timeSelect) {
    try {
        const response = await fetch(`index.php?controller=appointment&action=getAvailableTimes&date=${date}`);
        const bookedTimes = await response.json();

        timeSelect.innerHTML = `<option value="">Chọn giờ</option>`;
        const availableHours = ["08:00", "09:00", "10:00", "14:00", "15:00"];

        availableHours.forEach(time => {
            if (!bookedTimes.includes(time)) {
                const option = document.createElement("option");
                option.value = time;
                option.textContent = time;
                timeSelect.appendChild(option);
            }
        });
    } catch (error) {
        console.error("❌ Lỗi khi lấy dữ liệu giờ đặt:", error);
    }
}
