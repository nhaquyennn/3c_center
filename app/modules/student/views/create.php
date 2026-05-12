<div id="app">
    <div id="main">
        <header class="mb-3">
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </header>
        <div class="card">
            <div class="card-content">
                <div class="card-body">

                    <h5>Thêm học viên</h5>

                    <form method="POST" action="?module=student&action=store">

                        <!-- NAME -->
                        <div class="mb-3">
                            <label>Tên học viên</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <!-- PHONE -->
                        <div class="mb-3">
                            <label>SĐT</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                        <!-- PARENT -->
                        <div class="mb-3">
                            <label>Phụ huynh</label>
                            <input type="text" name="parent_name" class="form-control">
                        </div>

                        <!-- DOB -->
                        <div class="mb-3">
                            <label>Ngày sinh</label>
                            <input type="date" name="date_of_birth" class="form-control">
                        </div>

                        <!-- STATUS -->
                        <div class="mb-3">
                            <label>Trạng thái</label>
                            <select name="status" class="form-control">
                                <option value="1">Đang học</option>
                                <option value="0">Ngừng</option>
                            </select>
                        </div>

                        <button class="btn btn-primary">Lưu</button>
                        <a href="?module=student" class="btn btn-secondary">Quay lại</a>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>