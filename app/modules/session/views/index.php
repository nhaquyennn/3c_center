<div id="main">
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>

    <div class="page-heading">

        <!-- PAGE HEADER START-->
        <div class="page-title mb-3">
            <div class="row align-items-center">

                <!-- LEFT: TITLE + BREADCRUMB START-->
                <div class="col-md-6">
                    <h3 class="mb-1">Danh sách buổi học</h3>

                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="?module=class">Lớp học</a>
                            </li>

                            <?php
                            $code = $class['class_code'] ?? '';
                            $codeSuffix = '';

                            if ($code && strpos($code, '-') !== false) {
                                $parts = explode('-', $code);
                                $codeSuffix = end($parts); // lấy 001
                            }
                            ?>

                            <li class="breadcrumb-item">
                                <a href="?module=class&action=edit&id=<?= $class_id ?>">
                                    <?= htmlspecialchars($class['course_name']) ?> -
                                    <?= htmlspecialchars($class['package_name']) ?> -
                                    <?= htmlspecialchars($codeSuffix) ?>
                                </a>
                            </li>

                            <li class="breadcrumb-item active">
                                Buổi học
                            </li>
                        </ol>
                    </nav>
                </div>
                <!-- LEFT: TITLE + BREADCRUMB END-->

                <!-- RIGHT: ACTION START-->
                <div class="col-md-6 text-end">

                    <a href="?module=class" class="btn btn-secondary me-2">
                        ← Quay lại
                    </a>

                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateModal">
                        <i class="bi bi-calendar-plus"></i> Thêm buổi học
                    </button>

                </div>
                <!-- RIGHT: ACTION END-->

            </div>
        </div>
        <!-- PAGE HEADER END-->

        <section class="section">

            <?php if (empty($sessions)): ?>

            <!-- FORM TẠO LỊCH START-->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Tạo buổi học tự động</h4>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="?module=session&action=generate">

                            <input type="hidden" name="class_id" value="<?= htmlspecialchars($class_id) ?>">

                            <div class="row">

                                <!-- START DATE -->
                                <div class="col-md-6">
                                    <label>Ngày bắt đầu</label>
                                    <input type="date" name="start_date" class="form-control"
                                        value="<?= date('Y-m-d') ?>" required>
                                </div>

                                <!-- TOTAL -->
                                <div class="col-md-6">
                                    <label>Số buổi</label>
                                    <input type="number" name="total_sessions" class="form-control"
                                        placeholder="VD: 10" required>
                                </div>

                            </div>

                            <div class="mt-3 text-end">
                                <button class="btn btn-success">
                                    <i class="bi bi-calendar-plus"></i> Sinh lịch
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
                <!-- FORM TẠO LỊCH END-->


            <?php else: ?>

                <!-- TABLE SESSIONS START-->
                <div class="card">
                    <div class="card-content">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">

                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Ngày học</th>
                                        <th>Ca học</th>
                                        <th>Giờ học</th>
                                        <th>Phòng</th>
                                        <th>Giảng viên</th>
                                        <th>Trợ giảng</th>
                                        <th class="text-center">Điểm danh</th>
                                        <th>Trạng thái</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($sessions as $index => $s): ?>
                                        <tr>
                                            <!-- STT START-->
                                            <td><?= $index + 1 ?></td>
                                            <!-- STT END-->

                                            <!-- DATE START-->
                                            <td><?= $s['session_date'] ?></td>
                                            <!-- DATE END-->

                                            <!-- SHIFT START-->
                                            <td>
                                                <?= $s['shift_name'] ?? 'N/A' ?>

                                                <?php if ($s['status'] == 'conflict'): ?>
                                                    <br>
                                                    <span class="badge bg-danger">⚠ Trùng ca</span>
                                                <?php endif; ?>
                                            </td>
                                            <!-- SHIFT END-->

                                            <!-- TIME START-->
                                            <td>
                                                <?php if (!empty($s['start_time'])): ?>
                                                    <a href="#" class="badge bg-primary text-decoration-none" data-bs-toggle="modal"
                                                        data-bs-target="#timeModal" onclick="setSessionId(<?= $s['session_id'] ?>)">
                                                        🕒 <?= substr($s['start_time'], 0, 5) ?> -
                                                        <?= substr($s['end_time'], 0, 5) ?>
                                                    </a>

                                                <?php elseif (!empty($s['shift_name'])): ?>
                                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                                        data-bs-target="#timeModal" onclick="setSessionId(<?= $s['session_id'] ?>)">
                                                        <?= $s['shift_name'] ?> (mặc định)
                                                    </button>

                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                        data-bs-target="#timeModal" onclick="setSessionId(<?= $s['session_id'] ?>)">
                                                        + Chọn giờ
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                            <!-- TIME END-->

                                            <!-- ROOM START-->
                                            <td>
                                                <?php if ($s['room_name']): ?>
                                                    <a href="#" class="badge bg-success text-decoration-none" data-bs-toggle="modal"
                                                        data-bs-target="#roomModal" onclick="setSessionId(<?= $s['session_id'] ?>)">
                                                        <?= $s['room_name'] ?>
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                        data-bs-target="#roomModal" onclick="setSessionId(<?= $s['session_id'] ?>)">
                                                        + Chọn phòng
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                            <!-- ROOM END-->

                                            <!-- TEACHER START-->
                                            <td>
                                                <?php if (!empty($s['teacher_main'])): ?>
                                                    <a href="#" class="badge bg-info text-decoration-none" data-bs-toggle="modal"
                                                        data-bs-target="#teacherModal"
                                                        onclick="setSessionId(<?= $s['session_id'] ?>)">
                                                        <?= $s['teacher_main'] ?>
                                                    </a>
                                                <?php else: ?>
                                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                                        data-bs-target="#teacherModal"
                                                        onclick="setSessionId(<?= $s['session_id'] ?>)">
                                                        + Chọn GV
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                            <!-- TEACHER END-->

                                            <!-- ASSISTANT START-->
                                            <td>
                                                <?php if (!empty($s['teacher_assistant'])): ?>
                                                    <a href="#" class="badge bg-secondary text-decoration-none"
                                                        data-bs-toggle="modal" data-bs-target="#teacherModal"
                                                        onclick="setSessionId(<?= $s['session_id'] ?>)">
                                                        <?= $s['teacher_assistant'] ?>
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted">Không có</span>
                                                <?php endif; ?>
                                            </td>
                                            <!-- ASSISTANT END-->

                                            <!-- ACTION START-->
                                            <td class="text-center">

                                                <?php if ($s['status'] == 'conflict'): ?>

                                                    <button class="btn btn-sm btn-warning"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#timeModal"
                                                        onclick="setSessionId(<?= $s['session_id'] ?>)">
                                                        ⚠ Chọn lại ca
                                                    </button>

                                                <?php elseif ($s['status'] == 'done'): ?>

                                                    <!-- vẫn cho mở lại để xem/sửa -->
                                                    <button class="btn btn-sm btn-success"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#attendanceModal"
                                                        onclick="openAttendanceModal(<?= $s['session_id'] ?>)">
                                                        ✔ Đã điểm danh
                                                    </button>

                                                <?php else: ?>

                                                    <button class="btn btn-sm btn-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#attendanceModal"
                                                        onclick="openAttendanceModal(<?= $s['session_id'] ?>)">
                                                        Điểm danh
                                                    </button>

                                                    <a href="?module=session&action=cancel&id=<?= $s['session_id'] ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Xác nhận hủy buổi học?')">
                                                        Hủy
                                                    </a>

                                                <?php endif; ?>

                                            </td>
                                            <!-- ACTION END-->

                                            <td>
                                                <?php if ($s['status'] == 'done'): ?>
                                                    <span class="badge bg-success">Hoàn thành</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Chưa hoàn thành</span>
                                                <?php endif; ?>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
                <!-- TABLE SESSIONS END-->
            <?php endif; ?>

        </section>
    </div>
</div>

<!-- MODAL CHỌN PHÒNG START-->
<div class="modal fade" id="roomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" action="?module=session&action=assignRoom">

                <div class="modal-header">
                    <h5 class="modal-title">Chọn phòng học</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="session_id" id="session_id">

                    <label><b>Phòng học</b></label>

                    <!-- LIST PHÒNG (dynamic) -->
                    <div id="roomList">
                        <div class="text-muted">Đang tải...</div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Lưu</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>

            </form>

        </div>
    </div>
</div>
<!-- MODAL CHỌN PHÒNG END-->

<div class="modal fade" id="timeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" action="?module=session&action=assignTime">

                <div class="modal-header">
                    <h5 class="modal-title">Chọn giờ học</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="session_id" id="session_id">

                    <label>Ca học</label>
                    <select name="shift_id" class="form-control" required>
                        <option value="">-- Chọn ca --</option>
                        <?php foreach ($shifts as $sh): ?>
                            <option value="<?= $sh['shift_id'] ?>">
                                <?= $sh['name'] ?> (
                                <?= $sh['start_time'] ?> -
                                <?= $sh['end_time'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Lưu</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- MODAL CHỌN GV START-->
<div class="modal fade" id="teacherModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <form method="POST" action="?module=session&action=assignTeacher">

                <div class="modal-header">
                    <h5 class="modal-title">Chọn giảng viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="session_id" id="session_id">

                    <!-- GIẢNG VIÊN CHÍNH -->
                    <label><b>Giảng viên chính</b></label>
                    <div id="mainTeachers">
                        <div class="text-muted">Đang tải...</div>
                    </div>

                    <hr>

                    <!-- TRỢ GIẢNG -->
                    <label><b>Trợ giảng</b></label>
                    <div id="assistantTeachers">
                        <div class="text-muted">Đang tải...</div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Lưu</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>

            </form>

        </div>
    </div>
</div>
<!-- MODAL CHỌN GV END-->

<!-- MODAL ĐIỂM DANH START-->
<div class="modal fade" id="attendanceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="POST" action="?module=session&action=saveAttendance">

                <div class="modal-header">
                    <h5 class="modal-title">Điểm danh học sinh</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" name="session_id" id="attendance_session_id">

                    <div id="attendanceList">
                        Đang tải...
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success">Lưu điểm danh</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>

            </form>

        </div>
    </div>
</div>
<!-- MODAL ĐIỂM DANH END -->

<script>
    function setSessionId(id) {
        // set session_id cho tất cả form
        document.querySelectorAll('[name="session_id"]').forEach(el => {
            el.value = id;
        });

        // ========================
        // LOAD GIẢNG VIÊN
        // ========================
        document.getElementById('mainTeachers').innerHTML = 'Đang tải...';
        document.getElementById('assistantTeachers').innerHTML = 'Đang tải...';

        fetch(`?module=session&action=getTeachersWithStatus&session_id=${id}`)
            .then(res => res.json())
            .then(data => {

                let htmlMain = '';
                let htmlAssist = '';

                data.forEach(t => {

                    let disabled = t.is_busy == 1 ? 'disabled' : '';
                    let textClass = t.is_busy == 1 ? 'text-muted' : '';
                    let note = r.is_busy == 1
                        ? ' <span class="badge bg-danger">Phòng đang được sử dụng</span>'
                        : '';

                    // GIẢNG VIÊN CHÍNH
                    htmlMain += `
                        <div class="form-check ${textClass}">
                            <input class="form-check-input" type="radio" 
                                name="main_teacher_id" 
                                value="${t.teacher_id}" ${disabled}>
                            <label class="form-check-label">
                                ${t.name} ${note}
                            </label>
                        </div>
                    `;

                    // TRỢ GIẢNG
                    htmlAssist += `
                        <div class="form-check ${textClass}">
                            <input class="form-check-input" type="checkbox" 
                                name="assistant_ids[]" 
                                value="${t.teacher_id}" ${disabled}>
                            <label class="form-check-label">
                                ${t.name} ${note}
                            </label>
                        </div>
                    `;
                });

                document.getElementById('mainTeachers').innerHTML = htmlMain;
                document.getElementById('assistantTeachers').innerHTML = htmlAssist;
            });


        // ========================
        // LOAD PHÒNG
        // ========================
        document.getElementById('roomList').innerHTML = 'Đang tải...';

        fetch(`?module=session&action=getRoomsWithStatus&session_id=${id}`)
            .then(res => res.json())
            .then(data => {

                let html = '';

                data.forEach(r => {

                    let disabled = r.is_busy == 1 ? 'disabled' : '';
                    let textClass = r.is_busy == 1 ? 'text-muted' : '';
                    let note = r.is_busy == 1
                        ? ' <span class="badge bg-danger">Phòng đang được sử dụng</span>'
                        : '';

                    html += `
                        <div class="form-check ${textClass}">
                            <input class="form-check-input" type="radio" 
                                name="room_id" 
                                value="${r.room_id}" ${disabled}>
                            <label class="form-check-label">
                                ${r.name} (Sức chứa: ${r.capacity}) ${note}
                            </label>
                        </div>
                    `;
                });

                document.getElementById('roomList').innerHTML = html;
            });
    }

    // =======================
    // LOAD HỌC SINH
    // ========================
    function openAttendanceModal(sessionId) {
        console.log("OPEN MODAL", sessionId);

        document.getElementById('attendance_session_id').value = sessionId;
        document.getElementById('attendanceList').innerHTML = 'Đang tải...';

        fetch(`?module=session&action=getStudentsForAttendance&session_id=${sessionId}`)
            .then(res => res.json())
            .then(data => {

                let html = '';

                if (data.length === 0) {
                    html = '<div class="text-muted text-center">Chưa có học sinh</div>';
                } else {
                    data.forEach(s => {

                        let status = s.status ?? 'present';

                        html += `
                            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                
                                <!-- TÊN -->
                                <div>
                                    <b>${s.name}</b>
                                </div>

                                <!-- CHỌN TRẠNG THÁI -->
                                <div style="width: 150px;">
                                    <select name="status[${s.student_id}]" class="form-select form-select-sm">
                                        <option value="present" ${status=='present'?'selected':''}>Có mặt</option>
                                        <option value="absent" ${status=='absent'?'selected':''}>Vắng</option>
                                        <option value="late" ${status=='late'?'selected':''}>Trễ</option>
                                    </select>
                                </div>

                            </div>
                        `;
                    });
                }

                document.getElementById('attendanceList').innerHTML = html;
            });
    }
</script>