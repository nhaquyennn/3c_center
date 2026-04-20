<div id="main">
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>

    <div class="page-heading">
        <div class="page-title">
            <div class="row align-items-center mb-2">
                <div class="col-6">
                    <h3 class="mb-0">Danh sách học viên</h3>
                </div>
                <div class="col-6 text-end">
                    <a href="?module=student&action=create" class="btn btn-primary">
                        <i class="bi bi-person-plus-fill"></i> Thêm học viên
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <nav aria-label="breadcrumb" class="breadcrumb-header">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Học viên</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="row" id="table-hover-row">
                <div class="col-12">
                    
                    <div class="card mb-3">
                        <div class="card-body">
                            <form method="GET">
                                <input type="hidden" name="module" value="student">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <input type="text" name="keyword" class="form-control" 
                                               placeholder="Tìm tên học viên / email / SĐT..." 
                                               value="<?= $_GET['keyword'] ?? '' ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="status" class="form-select">
                                            <option value="">-- Tất cả trạng thái --</option>
                                            <option value="1" <?= ($_GET['status'] ?? '') == '1' ? 'selected' : '' ?>>Đang học</option>
                                            <option value="0" <?= ($_GET['status'] ?? '') == '0' ? 'selected' : '' ?>>Đã nghỉ</option>
                                        </select>
                                    </div>
                                    <div class="col-md-5">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-search"></i> Lọc
                                        </button>
                                        <a href="?module=student" class="btn btn-light-secondary">Reset</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>HỌC VIÊN</th>
                                            <th>SĐT / EMAIL</th>
                                            <th>NGÀY SINH</th>
                                            <th>PHỤ HUYNH</th>
                                            <th>TRẠNG THÁI</th>
                                            <th class="text-center">THAO TÁC</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($students)): ?>
                                            <?php 
                                            $i = (isset($page) && $page > 0) ? ($page - 1) * 10 + 1 : 1; 
                                            foreach ($students as $s): 
                                            ?>
                                            <tr>
                                                <td><?= $i++ ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar bg-primary me-3">
                                                            <span class="avatar-content" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center; color: white; border-radius: 50%; font-weight: bold;">
                                                                <?= $this->getInitials($s['name']) ?>
                                                            </span>
                                                        </div>
                                                        <span class="text-bold-500"><?= htmlspecialchars($s['name']) ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <small><i class="bi bi-phone"></i> <?= $s['phone'] ?></small><br>
                                                    <small class="text-muted"><i class="bi bi-envelope"></i> <?= $s['email'] ?></small>
                                                </td>
                                                <td><?= date('d/m/Y', strtotime($s['date_of_birth'])) ?></td>
                                                <td><?= htmlspecialchars($s['parent_name'] ?? 'N/A') ?></td>
                                                <td>
                                                    <?php if ($s['status'] == 1): ?>
                                                        <span class="badge bg-light-success">Đang học</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-light-danger">Đã nghỉ</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group">
                                                        <a href="?module=student&action=enroll&id=<?= $s['student_id'] ?>" 
                                                           class="btn btn-sm btn-outline-info" title="Đăng ký gói học">
                                                            <i class="bi bi-cart-plus"></i>
                                                        </a>
                                                        <a href="?module=student&action=edit&id=<?= $s['student_id'] ?>" 
                                                           class="btn btn-sm btn-outline-warning">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="?module=student&action=delete&id=<?= $s['student_id'] ?>" 
                                                           class="btn btn-sm btn-outline-danger" 
                                                           onclick="return confirm('Bạn có chắc chắn muốn lưu trữ học sinh này?')">
                                                            <i class="bi bi-archive"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center p-4">Không tìm thấy học viên nào</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if (isset($totalPages) && $totalPages > 1): ?>
                            <nav class="p-4">
                                <ul class="pagination pagination-primary justify-content-end">
                                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?module=student&page=<?= $page - 1 ?>&keyword=<?= $_GET['keyword'] ?? '' ?>">«</a>
                                    </li>
                                    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                        <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                                            <a class="page-link" href="?module=student&page=<?= $p ?>&keyword=<?= $_GET['keyword'] ?? '' ?>"><?= $p ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?module=student&page=<?= $page + 1 ?>&keyword=<?= $_GET['keyword'] ?? '' ?>">»</a>
                                    </li>
                                </ul>
                            </nav>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>