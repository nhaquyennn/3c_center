<div id="main">
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>

    <div class="page-heading">

        <!-- TITLE START-->
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 mb-3">
                    <h3>Danh sách lớp học</h3>
                </div>

                <div class="col-12 col-md-6 text-end">
                    <a href="?module=class&action=create" class="btn btn-success">
                        <i class="bi bi-plus"></i> Thêm lớp học
                    </a>
                </div>
            </div>

            <!-- BREADCRUMB -->
            <div class="row">
                <div class="col-12">
                    <nav class="breadcrumb-header">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="<?= BASE_URL ?>?module=dashboard&action=index">Trang chủ</a>
                            </li>
                            <li class="breadcrumb-item active">Lớp học</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <!-- TITLE END-->

        <!-- MAIN START-->
        <section class="section">
            <div class="row">
                <div class="col-12">

                    <!-- TABLE START-->
                    <div class="card">

                        <!-- FILTER START-->

                        <form method="GET" class="mb-3">
                            <input type="hidden" name="module" value="class">

                            <div class="row">

                                <!-- KEYWORD -->
                                <div class="col-md-3">
                                    <input type="text" name="keyword" class="form-control" placeholder="Tìm khóa học..."
                                        value="<?= htmlspecialchars($filters['keyword'] ?? '') ?>">
                                </div>

                                <!-- COURSE -->
                                <div class="col-md-2">
                                    <select name="course_id" class="form-control">
                                        <option value="">Khóa học</option>
                                        <?php foreach ($courses as $c): ?>
                                            <option value="<?= $c['course_id'] ?>"
                                                <?= ($filters['course_id'] == $c['course_id']) ? 'selected' : '' ?>>
                                                <?= $c['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- PACKAGE -->
                                <div class="col-md-2">
                                    <select name="package_id" class="form-control">
                                        <option value="">Gói học</option>
                                        <?php foreach ($packages as $p): ?>
                                            <option value="<?= $p['package_id'] ?>"
                                                <?= ($filters['package_id'] == $p['package_id']) ? 'selected' : '' ?>>
                                                <?= $p['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- SCHEDULE -->
                                <div class="col-md-2">
                                    <select name="schedule_id" class="form-control">
                                        <option value="">Lịch học</option>
                                        <?php foreach ($schedules as $s): ?>
                                            <option value="<?= $s['schedule_id'] ?>"
                                                <?= ($filters['schedule_id'] == $s['schedule_id']) ? 'selected' : '' ?>>
                                                <?= $s['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- SHIFT -->
                                <div class="col-md-2">
                                    <select name="shift_id" class="form-control">
                                        <option value="">Ca học</option>
                                        <?php foreach ($shifts as $sh): ?>
                                            <option value="<?= $sh['shift_id'] ?>"
                                                <?= ($filters['shift_id'] == $sh['shift_id']) ? 'selected' : '' ?>>
                                                <?= $sh['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- STATUS -->
                                <div class="col-md-2 mt-2">
                                    <select name="status" class="form-control">
                                        <option value="">-- Trạng thái --</option>
                                        <option value="upcoming">Sắp học</option>
                                        <option value="studying">Đang học</option>
                                        <option value="done">Đã xong</option>
                                        <option value="inactive">Đã ngưng</option> 
                                    </select>
                                </div>

                                <!-- BUTTON -->
                                <div class="col-md-3 mt-2 d-flex gap-2">
                                    <button class="btn btn-primary">
                                        Lọc
                                    </button>

                                    <a href="?module=class" class="btn btn-secondary">
                                        Reset
                                    </a>
                                </div>

                            </div>
                        </form>
                        <!-- FILTER END -->
            
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">

                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>MÃ LỚP</th>
                                        <th>LỚP HỌC</th>
                                        <th>KHÓA</th>
                                        <th>GÓI</th>
                                        <th>HỌC VIÊN</th>
                                        <th>LỊCH</th>
                                        <th>NGÀY BẮT ĐẦU</th>
                                        <th>TRẠNG THÁI</th>
                                        <th>TIẾN ĐỘ</th>
                                        <th class="text-center">ACTION</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php if (!empty($classes)): ?>
                                        <?php foreach ($classes as $index => $c): ?>
                                            <tr>
                                                <!-- STT -->
                                                <td><?= $offset + $index + 1 ?></td>

                                                <!-- CODE -->
                                                <td>
                                                    <span class="badge bg-dark">
                                                        <?= $c['class_code'] ?>
                                                    </span>
                                                </td>

                                                <!-- CLASS NAME (AUTO COMPOSE) -->
                                                <td>
                                                    <?php
                                                    $code = $c['class_code'] ?? '';
                                                    $suffix = '';

                                                    if ($code && strpos($code, '-') !== false) {
                                                        $parts = explode('-', $code);
                                                        $suffix = end($parts); // lấy 001
                                                    }
                                                    ?>

                                                    <?= htmlspecialchars($c['course_name']) ?> -
                                                    <?= htmlspecialchars($c['package_name']) ?> -
                                                    <?= htmlspecialchars($suffix) ?>
                                                </td>

                                                <!-- COURSE -->
                                                <td><?= $c['course_name'] ?></td>

                                                <!-- PACKAGE -->
                                                <td><?= $c['package_name'] ?></td>

                                                <!-- STUDENTS -->
                                                <td>
                                                    <span class="badge bg-primary">
                                                        <?= $c['student_count'] ?>
                                                    </span>
                                                </td>

                                                <!-- SCHEDULE -->
                                                <td><?= $c['schedule_name'] ?></td>

                                                <!-- DATE -->
                                                <td><?= $c['start_date'] ?></td>

                                                <td>
                                                    <?php if ($c['status'] == 'unscheduled'): ?>
                                                        <span class="badge bg-secondary">Chưa xếp lịch</span>

                                                    <?php elseif ($c['status'] == 'upcoming'): ?>
                                                        <span class="badge bg-info">Sắp học</span>

                                                    <?php elseif ($c['status'] == 'studying'): ?>
                                                        <span class="badge bg-primary">Đang học</span>

                                                    <?php elseif ($c['status'] == 'done'): ?>
                                                        <span class="badge bg-success">Hoàn thành</span>

                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Ngưng</span>
                                                    <?php endif; ?>
                                                </td>

                                                <td style="width:200px;">
                                                    <?php
                                                    $percent = $c['total_sessions'] > 0
                                                        ? ($c['learned'] / $c['total_sessions']) * 100
                                                        : 0;
                                                    ?>

                                                    <div class="progress">
                                                        <div class="progress-bar bg-success" style="width: <?= $percent ?>%">
                                                            <?= $c['learned'] ?>/
                                                            <?= $c['total_sessions'] ?>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- ACTION -->
                                                <td class="text-center">
                                                    <a href="?module=class&action=edit&id=<?= $c['class_id'] ?>"
                                                        class="btn btn-sm btn-warning">
                                                        Edit
                                                    </a>

                                                    <?php if ($c['status'] == 'inactive'): ?>

                                                        <a href="?module=class&action=activate&id=<?= $c['class_id'] ?>"
                                                            class="btn btn-sm btn-success">
                                                            Kích hoạt
                                                        </a>

                                                    <?php else: ?>

                                                        <a href="?module=class&action=deactivate&id=<?= $c['class_id'] ?>"
                                                            class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Ngưng lớp này?')">
                                                            Ngưng
                                                        </a>

                                                    <?php endif; ?>

                                                    <a href="?module=session&action=index&class_id=<?= $c['class_id'] ?>"
                                                        class="btn btn-sm btn-primary">
                                                        Buổi học
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center">Không có dữ liệu</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>

                            </table>
                        </div>
                    </div>
                    <!-- TABLE END-->

                    <!-- INFO -->
                    <div class="text-center mt-2">
                        Hiển thị
                        <?= $total > 0 ? $offset + 1 : 0 ?>
                        -
                        <?= min($offset + $limit, $total) ?>
                        /
                        <?= $total ?> lớp học
                    </div>

                    <!-- PAGINATION -->
                    <?php
                    $query = http_build_query([
                        'module' => 'class',
                        'keyword' => $filters['keyword'] ?? '',
                        'course_id' => $filters['course_id'] ?? '',
                        'package_id' => $filters['package_id'] ?? '',
                        'schedule_id' => $filters['schedule_id'] ?? '',
                        'shift_id' => $filters['shift_id'] ?? '',
                        'status' => $filters['status'] ?? ''
                    ]);
                    ?>
                    <?php if ($totalPages > 1): ?>
                        <nav class="mt-3">
                            <ul class="pagination justify-content-center">

                                <!-- PREV -->
                                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="?module=class&page=<?= $page - 1 ?>
                                        &keyword=<?= urlencode($filters['keyword'] ?? '') ?>
                                        &course_id=<?= $filters['course_id'] ?? '' ?>">
                                        «
                                    </a>
                                </li>

                                <?php
                                $start = max(1, $page - 2);
                                $end = min($totalPages, $page + 2);
                                ?>

                                <!-- PAGE -->
                                <?php for ($i = $start; $i <= $end; $i++): ?>
                                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                        <a class="page-link"
                                            href="?module=class&page=<?= $i ?>&keyword=<?= urlencode(trim($filters['keyword'] ?? '')) ?>&course_id=<?= $filters['course_id'] ?? '' ?>">
                                            <?= $i ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>

                                <!-- NEXT -->
                                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                    <a class="page-link"
                                        href="?module=class&page=<?= $page + 1 ?>&keyword=<?= urlencode(trim($filters['keyword'] ?? '')) ?>&course_id=<?= $filters['course_id'] ?? '' ?>">
                                        »
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    <?php endif; ?>

                </div>
            </div>
        </section>
        <!-- MAIN END-->

    </div>
</div>