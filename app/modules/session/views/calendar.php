<div id="main">
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>
    <div id="calendar"></div>
    <div id="popup" style="
        display:none;
        position:fixed;
        top:30%;
        left:40%;
        background:#fff;
        padding:20px;
        border-radius:10px;
        z-index:9999;  /* QUAN TRỌNG */
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        ">

        <h5>Chọn giảng viên</h5>

        <select id="teacherSelect" class="form-select">
            <?php foreach ($teachers as $t): ?>
                <option value="<?= $t['teacher_id'] ?>">
                    <?= $t['name'] ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button onclick="saveEvent()" class="btn btn-primary mt-2">Lưu</button>
        <button onclick="closePopup()" class="btn btn-secondary mt-2">Hủy</button>
    </div>

    <script>
        let selectedInfo = null;

        document.addEventListener('DOMContentLoaded', function () {
            let calendarEl = document.getElementById('calendar');

            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                selectable: true,
                selectMirror: true,
                timeZone: 'local',

                events: '?module=session&action=getEvents&class_id=<?= $_GET['class_id'] ?>',

                select: function (info) {
                    selectedInfo = info;
                    document.getElementById('popup').style.display = 'block';
                }
            });

            calendar.render();
        });

        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }

        function saveEvent() {
            let teacher_id = document.getElementById('teacherSelect').value;
            let class_id = <?= $_GET['class_id'] ?>;

            let formData = new FormData();
            formData.append('class_id', class_id);
            formData.append('teacher_id', teacher_id);
            formData.append('start', selectedInfo.startStr);
            formData.append('end', selectedInfo.endStr);

            fetch('?module=session&action=createEvent', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    class_id: class_id,
                    teacher_id: teacher_id,
                    start: selectedInfo.startStr,
                    end: selectedInfo.endStr
                })
            })
                .then(res => res.text())
                .then(data => {
                    console.log("SERVER:", data);
                    selectedInfo = null;
                    location.reload();
                });

            closePopup();
        }
    </script>
</div>