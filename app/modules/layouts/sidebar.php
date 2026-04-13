<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header">
            <div class="d-flex justify-content-between">
                <div class="logo">
                    <a href="index.html"><img src="assets/images/logo/logo.png" alt="Logo" srcset=""></a>
                </div>
                <div class="toggler">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Menu</li>

                <!-- DASHBOARD CHÍNH -->
                <li class="sidebar-item active ">
                    <a href="index.html" class='sidebar-link'>
                        <i class="bi bi-grid-fill"></i>
                        <span>Trang chủ</span>
                    </a>
                </li>

                <!-- LỚP HỌC -->
                <li class="sidebar-item has-sub">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-collection-fill"></i>
                        <span>Quản lý lớp học</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="?module=class&action=index">Danh sách lớp học</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item has-sub">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-collection-fill"></i>
                        <span>Quản lý giảng viên</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="?module=teacher&action=index">Danh sách giảng viên</a>
                        </li>
                        <li class="submenu-item">
                            <a href="?module=teacher&action=create">Thêm giảng viên</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item has-sub">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-collection-fill"></i>
                        <span>Quản lý khóa học</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="?module=course&action=index">Danh sách khóa học</a>
                        </li>
                        <li class="submenu-item">
                            <a href="?module=course&action=create">Thêm khóa học</a>
                        </li>
                    </ul>
                </li>

                <li class="sidebar-item has-sub">
                    <a href="#" class="sidebar-link">
                        <i class="bi bi-collection-fill"></i>
                        <span>Quản lý gói học</span>
                    </a>
                    <ul class="submenu">
                        <li class="submenu-item">
                            <a href="?module=package&action=index">Danh sách gói học</a>
                        </li>
                        <li class="submenu-item">
                            <a href="?module=package&action=create">Thêm gói học</a>
                        </li>
                    </ul>
                </li>


            </ul>
        </div>
        <button class="sidebar-toggler btn x"><i data-feather="x"></i></button>
    </div>
</div>