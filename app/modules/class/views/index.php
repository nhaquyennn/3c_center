<div id="main">
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>

    <div class="page-heading">

        <!-- Breadcum Start -->
        <div class="page-title">

            <!-- HÀNG 1 -->
            <div class="row align-items-center mb-2">
                <div class="col-6">
                    <h3 class="mb-0">Danh sách lớp học</h3>
                </div>

                <div class="col-6 text-end">
                    <a href="?module=class&action=create" class="btn btn-success mb-3">
                        + Thêm lớp
                    </a>
                </div>
            </div>

            <!-- HÀNG 2 -->
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb" class="breadcrumb-header">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="<?= BASE_URL ?>?module=dashboard&action=index">Trang chủ</a>
                            </li>
                            <li class="breadcrumb-item active">
                                Lớp học
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>

        </div>
        <!-- Breadcum End -->
        <section class="section">
            <div class="row" id="table-hover-row">
                <div class="col-12">

                    <!-- Filter Start-->
                    <div class="filter-box mb-3">
                        <form method="GET" class="mb-3">
                            <input type="hidden" name="module" value="class">

                            <div class="row">

                                <!-- TÊN LỚP -->
                                <div class="col-md-3">
                                    <input type="text" name="keyword" class="form-control"
                                        placeholder="Tìm tên lớp"
                                        value="<?= $_GET['keyword'] ?? '' ?>">
                                </div>

                                <!-- KHÓA HỌC -->
                                <div class="col-md-3">
                                    <select name="course_id" class="form-control">
                                        <option value="">-- Khóa học --</option>
                                        <?php foreach ($courses as $course): ?>
                                            <option value="<?= $course['course_id'] ?>"
                                                <?= ($_GET['course_id'] ?? '') == $course['course_id'] ? 'selected' : '' ?>>
                                                <?= $course['course_name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- GÓI HỌC -->
                                <div class="col-md-2">
                                    <select name="package_id" class="form-control">
                                        <option value="">-- Gói học --</option>
                                        <?php foreach ($packages as $p): ?>
                                            <option value="<?= $p['package_id'] ?>"
                                                <?= ($_GET['package_id'] ?? '') == $p['package_id'] ? 'selected' : '' ?>>
                                                <?= $p['name'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- TRẠNG THÁI -->
                                <div class="col-md-2">
                                    <select name="status" class="form-control">
                                        <option value="">-- Trạng thái --</option>
                                        <option value="upcoming" <?= ($_GET['status'] ?? '') == 'upcoming' ? 'selected' : '' ?>>
                                            Sắp học
                                        </option>
                                        <option value="studying" <?= ($_GET['status'] ?? '') == 'studying' ? 'selected' : '' ?>>
                                            Đang học
                                        </option>
                                        <option value="done" <?= ($_GET['status'] ?? '') == 'done' ? 'selected' : '' ?>>
                                            Đã kết thúc
                                        </option>
                                    </select>
                                </div>

                                <!-- ACTION -->
                                <div class="col-md-2">
                                    <button class="btn btn-primary">
                                        <i class="bi bi-search"></i> Lọc
                                    </button>
                                    <a href="?module=class" class="btn btn-secondary">
                                        Reset
                                    </a>
                                </div>

                            </div>
                        </form>
                    </div>
                    <!-- Filter End-->
                    <div class="card">

                        <div class="card-content">
                            <div class="table-responsive">

                                <!-- Table Start -->
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Lớp học</th>
                                            <th>Khóa học</th>
                                            <th>Gói học</th>
                                            <th>Ngày bắt đầu</th>
                                            <th>Tiến độ</th>
                                            <th>Trạng thái</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php $i = ($page - 1) * 10 + 1; // STT ?>
                                        <?php foreach ($classes as $c): ?>

                                            <?php
                                            // Xác định trạng thái
                                            $today = date('Y-m-d');
                                            $status = 'Sắp học';
                                            $badge = 'secondary';

                                            if ($c['start_date'] <= $today) {
                                                if ($c['learned'] >= $c['total']) {
                                                    $status = 'Đã kết thúc';
                                                    $badge = 'dark';
                                                } else {
                                                    $status = 'Đang học';
                                                    $badge = 'success';
                                                }
                                            }

                                            // % tiến độ
                                            $percent = ($c['total'] > 0) ? round(($c['learned'] / $c['total']) * 100) : 0;
                                            ?>

                                            <tr>
                                                <td class="text-center"><?= $i++ ?></td>

                                                <!-- Tên lớp -->
                                                <td>
                                                    <strong><?= $c['class_name'] ?? 'Chưa đặt tên' ?></strong>
                                                </td>

                                                <td><?= $c['course_name'] ?></td>

                                                <!-- Gói học -->
                                                <td>
                                                    <span class="badge bg-info">
                                                        <?= $c['name'] ?? 'N/A' ?>
                                                    </span>
                                                </td>

                                                <td><?= $c['start_date'] ?></td>

                                                <!-- Progress bar -->
                                                <td style="min-width:150px;">
                                                    <div class="progress" style="height: 8px;">
                                                        <div class="progress-bar" role="progressbar"
                                                            style="width: <?= $percent ?>%;">
                                                        </div>
                                                    </div>
                                                    <small><?= $c['learned'] ?> / <?= $c['total'] ?> buổi</small>
                                                </td>

                                                <!-- Trạng thái -->
                                                <td>
                                                    <span class="badge bg-<?= $badge ?>">
                                                        <?= $status ?>
                                                    </span>
                                                </td>

                                                <td>
                                                    <a href="?module=class&action=edit&id=<?= $c['class_id'] ?>"
                                                        class="btn btn-sm btn-warning">Edit</a>

                                                    <a href="?module=session&action=calendar&class_id=<?= $c['class_id'] ?>"
                                                        class="btn btn-sm btn-primary">
                                                        Xếp lịch
                                                    </a>

                                                    <a href="?module=class&action=detail&id=<?= $c['class_id'] ?>"
                                                        class="btn btn-sm btn-secondary">
                                                        View
                                                    </a>
                                                </td>
                                            </tr>

                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <!-- Table End -->

                                <!-- Pagination Start-->
                                <nav>
                                    <ul class="pagination justify-content-end mt-3">

                                        <!-- prev -->
                                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?module=class&page=<?= $page - 1 ?>
                                                &course_id=<?= $_GET['course_id'] ?? '' ?>">
                                                «
                                            </a>
                                        </li>

                                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                                <a class="page-link" href="?module=class&page=<?= $i ?>
                                                    &course_id=<?= $_GET['course_id'] ?? '' ?>">
                                                    <?= $i ?>
                                                </a>
                                            </li>
                                        <?php endfor; ?>

                                        <!-- next -->
                                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                            <a class="page-link" href="?module=class&page=<?= $page + 1 ?>
                                                &course_id=<?= $_GET['course_id'] ?? '' ?>">
                                                »
                                            </a>
                                        </li>

                                    </ul>
                                </nav>
                                <!-- Pagination End-->

                            </div>
                        </div>
                    </div>
                </div>
        </section>
    </div>
</div>