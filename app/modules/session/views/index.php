<div id="main">
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>
    <table class="table table-hover">
        <thead>
            <tr>
                <th>Buổi</th>
                <th>Ngày</th>
                <th>Giờ</th>
                <th>Giảng viên</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($sessions as $s): ?>
                <tr>
                    <td><?= $s['session_no'] ?></td>
                    <td><?= $s['date'] ?></td>
                    <td><?= $s['start_time'] ?> - <?= $s['end_time'] ?></td>

                    <td>
                        <form method="POST" action="?module=session&action=assign">
                            <input type="hidden" name="session_id" value="<?= $s['session_id'] ?>">

                            <select name="teacher_id" onchange="this.form.submit()" class="form-control">
                                <option value="">-- Chọn GV --</option>

                                <?php foreach ($teachers as $t): ?>
                                    <option value="<?= $t['teacher_id'] ?>" <?= $t['teacher_id'] == $s['teacher_id'] ? 'selected' : '' ?>>
                                        <?= $t['name'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
