<div id="main">
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>

    <div class="page-heading">
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Chỉnh sửa lớp học</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="?module=class&action=update">

                        <!-- hidden id -->
                        <input type="hidden" name="class_id" value="<?= $class['class_id'] ?>">

                        <div class="row">

                            <!-- TÊN LỚP -->
                            <div class="col-md-6">
                                <label>Tên lớp</label>
                                <input type="text" name="class_name"
                                    value="<?= htmlspecialchars($class['class_name']) ?>" class="form-control" required>
                            </div>

                            <!-- KHÓA HỌC -->
                            <div class="col-md-6">
                                <label>Khóa học</label>
                                <select name="course_id" class="form-control" required>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?= $course['course_id'] ?>"
                                            <?= ($course['course_id'] == $class['course_id']) ? 'selected' : '' ?>>
                                            <?= $course['course_name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- GÓI HỌC -->
                            <div class="col-md-6">
                                <label>Gói học</label>
                                <select name="package_id" class="form-control" required>
                                    <?php foreach ($packages as $p): ?>
                                        <option value="<?= $p['package_id'] ?>" <?= ($p['package_id'] == $class['package_id']) ? 'selected' : '' ?>>
                                            <?= $p['name'] ?> (
                                            <?= $p['session_total'] ?> buổi)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- NGÀY BẮT ĐẦU -->
                            <div class="col-md-6">
                                <label>Ngày bắt đầu</label>
                                <input type="date" name="start_date" value="<?= $class['start_date'] ?>"
                                    class="form-control" required>
                            </div>

                        </div>

                        <div class="mt-3 text-end">
                            <button class="btn btn-warning">
                                <i class="bi bi-save"></i> Cập nhật
                            </button>
                            <a href="?module=class" class="btn btn-secondary">Hủy</a>
                        </div>

                    </form>
                </div>
            </div>
        </section>
    </div>
</div>