<?php
$role = $_SESSION['user']['role'] ?? null;

$currentModule = $_GET['module'] ?? 'dashboard';
$currentAction = $_GET['action'] ?? 'index';
?>

<!-- ===== SIDEBAR ===== -->
<div id="sidebar" class="active">

    <div class="sidebar-wrapper active">

        <!-- HEADER -->
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">

                <div class="logo">
                    <a href="index.php">
                        <img src="assets/images/logo/logo3c.png" alt="Logo">
                    </a>
                </div>

            </div>
        </div>

        <!-- MENU -->
        <div class="sidebar-menu">

            <ul class="menu">

                <!-- ========================= -->
                <!-- TEACHER VIEW (CHỈ HIỂN THỊ 2-3 MENU) -->
                <!-- ========================= -->
                <?php if ($role === 'teacher'): ?>
                    
                    

                    <!-- TRANG CHỦ -->
                    <li class="sidebar-item <?= $currentModule == 'teacher' ? 'active' : '' ?>">
                        <a href="?module=teacher&action=dashboard" class="sidebar-link">
                            <i class="bi bi-grid-fill"></i>
                            <span>Trang chủ</span>
                        </a>
                    </li>

                    <!-- LỚP HỌC (THÊM MỚI) -->
                    <li class="sidebar-item <?= $currentModule == 'class' ? 'active' : '' ?>">
                        <a href="?module=class&action=index" class="sidebar-link">
                            <i class="bi bi-collection-fill"></i>
                            <span>Lớp học</span>
                        </a>
                    </li>

                    <!-- LỊCH SỬ DẠY -->
                    <li
                        class="sidebar-item <?= ($currentModule == 'teacher' && $currentAction == 'history') ? 'active' : '' ?>">
                        <a href="?module=teacher&action=history" class="sidebar-link">
                            <i class="bi bi-clock-history"></i>
                            <span>Lịch sử dạy</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="?module=auth&action=logout" class="sidebar-link text-danger">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Đăng xuất</span>
                        </a>
                    </li>

                <?php endif; ?>


                <!-- ========================= -->
                <!-- ADMIN FULL MENU (GIỮ NGUYÊN 100%) -->
                <!-- ========================= -->
                <?php if ($role !== 'teacher'): ?>

                    <!-- DASHBOARD -->
                    <li class="sidebar-item <?= $currentModule == 'dashboard' ? 'active' : '' ?>">

                        <a href="index.php" class="sidebar-link">
                            <i class="bi bi-grid-fill"></i>
                            <span>Trang chủ</span>
                        </a>

                    </li>

                    <!-- LỚP HỌC -->
                    <li class="sidebar-item has-sub <?= $currentModule == 'class' ? 'active open' : '' ?>">

                        <a href="javascript:void(0)" class="sidebar-link">
                            <i class="bi bi-collection-fill"></i>
                            <span>Quản lý lớp học</span>
                        </a>

                        <ul class="submenu" style="<?= $currentModule == 'class' ? 'display:block;' : '' ?>">

                            <li class="submenu-item <?= $currentModule == 'class' ? 'active' : '' ?>">
                                <a href="?module=class&action=index">
                                    <i class="bi bi-caret-right"></i>
                                    <span>Danh sách lớp học</span>
                                </a>
                            </li>

                        </ul>
                    </li>

                    <!-- HỌC VIÊN -->
                    <li
                        class="sidebar-item has-sub <?= ($currentModule == 'student' || $currentModule == 'enrollment') ? 'active open' : '' ?>">

                        <a href="javascript:void(0)" class="sidebar-link">
                            <i class="bi bi-person-lines-fill"></i>
                            <span>Quản lý học viên</span>
                        </a>

                        <ul class="submenu"
                            style="<?= ($currentModule == 'student' || $currentModule == 'enrollment') ? 'display:block;' : '' ?>">

                            <li class="submenu-item <?= $currentModule == 'student' ? 'active' : '' ?>">
                                <a href="?module=student&action=index">
                                    <i class="bi bi-caret-right"></i>
                                    <span>Danh sách học viên</span>
                                </a>
                            </li>

                            <li class="submenu-item <?= $currentModule == 'enrollment' ? 'active' : '' ?>">
                                <a href="?module=enrollment&action=index">
                                    <i class="bi bi-caret-right"></i>
                                    <span>Ghi danh</span>
                                </a>
                            </li>

                        </ul>
                    </li>

                    <!-- CA HỌC -->
                    <li class="sidebar-item has-sub <?= $currentModule == 'shift' ? 'active open' : '' ?>">

                        <a href="javascript:void(0)" class="sidebar-link">
                            <i class="bi bi-clock-fill"></i>
                            <span>Quản lý ca học</span>
                        </a>

                        <ul class="submenu" style="<?= $currentModule == 'shift' ? 'display:block;' : '' ?>">

                            <li class="submenu-item <?= $currentModule == 'shift' ? 'active' : '' ?>">
                                <a href="?module=shift&action=index">
                                    <i class="bi bi-caret-right"></i>
                                    <span>Danh sách ca học</span>
                                </a>
                            </li>

                        </ul>
                    </li>

                    <!-- GIẢNG VIÊN -->
                    <li
                        class="sidebar-item has-sub <?= in_array($currentModule, ['teacher', 'specialization', 'salary']) ? 'active open' : '' ?>">

                        <a href="javascript:void(0)" class="sidebar-link">
                            <i class="bi bi-people-fill"></i>
                            <span>Quản lý giảng viên</span>
                        </a>

                        <ul class="submenu"
                            style="<?= in_array($currentModule, ['teacher', 'specialization', 'salary']) ? 'display:block;' : '' ?>">

                            <li class="submenu-item <?= $currentModule == 'teacher' ? 'active' : '' ?>">
                                <a href="?module=teacher&action=index">
                                    <i class="bi bi-caret-right"></i>
                                    <span>Danh sách giảng viên</span>
                                </a>
                            </li>

                            <li class="submenu-item">
                                <a href="?module=teacher&action=create">
                                    <i class="bi bi-caret-right"></i>
                                    <span>Thêm giảng viên</span>
                                </a>
                            </li>

                            <li class="submenu-item <?= $currentModule == 'specialization' ? 'active' : '' ?>">
                                <a href="?module=specialization&action=index">
                                    <i class="bi bi-caret-right"></i>
                                    <span>Chuyên môn</span>
                                </a>
                            </li>

                            <li class="submenu-item <?= $currentModule == 'salary' ? 'active' : '' ?>">
                                <a href="?module=salary&action=index">
                                    <i class="bi bi-caret-right"></i>
                                    <span>Quản lý lương</span>
                                </a>
                            </li>

                        </ul>
                    </li>

                    <!-- KHÓA HỌC -->
                    <li class="sidebar-item has-sub <?= $currentModule == 'course' ? 'active open' : '' ?>">

                        <a href="javascript:void(0)" class="sidebar-link">
                            <i class="bi bi-book-fill"></i>
                            <span>Quản lý khóa học</span>
                        </a>

                        <ul class="submenu" style="<?= $currentModule == 'course' ? 'display:block;' : '' ?>">

                            <li class="submenu-item">
                                <a href="?module=course&action=index">
                                    <i class="bi bi-caret-right"></i>
                                    <span>Danh sách khóa học</span>
                                </a>
                            </li>

                            <li class="submenu-item">
                                <a href="?module=course&action=create">
                                    <i class="bi bi-caret-right"></i>
                                    <span>Thêm khóa học</span>
                                </a>
                            </li>

                        </ul>
                    </li>

                    <!-- GÓI HỌC -->
                    <li class="sidebar-item <?= $currentModule == 'package' ? 'active' : '' ?>">
                        <a href="?module=package&action=index" class="sidebar-link">
                            <i class="bi bi-border-width"></i>
                            <span>Quản lý gói học</span>
                        </a>
                    </li>

                    <!-- LỊCH HỌC -->
                    <li class="sidebar-item <?= $currentModule == 'schedule' ? 'active' : '' ?>">
                        <a href="?module=schedule&action=index" class="sidebar-link">
                            <i class="bi bi-calendar-fill"></i>
                            <span>Lịch học</span>
                        </a>
                    </li>

                    <!-- PHÒNG HỌC -->
                    <li class="sidebar-item <?= $currentModule == 'room' ? 'active' : '' ?>">
                        <a href="?module=room&action=index" class="sidebar-link">
                            <i class="bi bi-building"></i>
                            <span>Phòng học</span>
                        </a>
                    </li>

                    <li class="sidebar-item">
                        <a href="?module=auth&action=logout" class="sidebar-link text-danger">
                            <i class="bi bi-box-arrow-right"></i>
                            <span>Đăng xuất</span>
                        </a>
                    </li>
                <?php endif; ?>

            </ul>

        </div>

    </div>
</div>