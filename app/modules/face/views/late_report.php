<div id="main">

<div class="page-heading">

    <h3>
        Báo cáo chấm công giảng viên
    </h3>

</div>

<div class="page-content">

<div class="card">

<div class="card-body">

<!-- FILTER -->

<div class="mb-3">

    <a
        href="?module=face&action=lateReport&filter=all"
        class="btn btn-primary"
    >
        Tất cả
    </a>

    <a
        href="?module=face&action=lateReport&filter=present"
        class="btn btn-success"
    >
        Đúng giờ
    </a>

    <a
        href="?module=face&action=lateReport&filter=late"
        class="btn btn-warning"
    >
        Đi trễ
    </a>

    <a
        href="?module=face&action=lateReport&filter=absent"
        class="btn btn-danger"
    >
        Vắng
    </a>

</div>

<table class="table table-bordered">

<thead>

<tr>

    <th>#</th>
    <th>Giảng viên</th>
    <th>Lớp</th>
    <th>Ngày</th>
    <th>Ca</th>
    <th>Checkin</th>
    <th>Trễ</th>
    <th>Ảnh</th>
    <th>Action</th>

</tr>

</thead>

<tbody>

<?php if(!empty($reports)): ?>

<?php foreach($reports as $index => $row): ?>

<?php

$late =
    max(
        0,
        $row['late_minutes']
    );

?>

<tr>

<td>
    <?= $index + 1 ?>
</td>

<td>
    <?= htmlspecialchars($row['teacher_name']) ?>
</td>

<td>
    <?= htmlspecialchars($row['class_name']) ?>
</td>

<td>
    <?= $row['session_date'] ?>
</td>

<td>
    <?= $row['start_time'] ?>
</td>

<td>
    <?= $row['check_in_time'] ?>
</td>

<td>

<?php if($late <= 0): ?>

    <span class="badge bg-success">
        Đúng giờ
    </span>

<?php elseif($late < 30): ?>

    <span class="badge bg-warning">
        <?= $late ?> phút
    </span>

<?php else: ?>

    <span class="badge bg-danger">
        Vắng
    </span>

<?php endif; ?>

</td>

<td>

<?php if(!empty($row['face_image'])): ?>

<img
    src="uploads/attendance/<?= htmlspecialchars($row['face_image']) ?>"
    width="120"
    class="img-thumbnail"
>

<?php endif; ?>

</td>

<td>

<?php if($late > 0 && $late < 30): ?>

<button
    class="btn btn-warning btn-sm"
    onclick="
        confirmPenalty(
            <?= $row['attendance_id'] ?>,
            'late'
        )
    "
>
    Phạt đi trễ
</button>

<?php endif; ?>

<?php if($late >= 30): ?>

<button
    class="btn btn-danger btn-sm"
    onclick="
        confirmPenalty(
            <?= $row['attendance_id'] ?>,
            'absent'
        )
    "
>
    Xác nhận vắng
</button>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>

<td
    colspan="9"
    class="text-center"
>
    Không có dữ liệu
</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<script>

async function confirmPenalty(
    attendance_id,
    type
)
{
    if(!confirm("Xác nhận xử lý?"))
    {
        return;
    }

    const formData =
        new FormData();

    formData.append(
        "attendance_id",
        attendance_id
    );

    formData.append(
        "type",
        type
    );

    const res = await fetch(
        "?module=face&action=confirmPenalty",
        {
            method: "POST",
            body: formData
        }
    );

    const data =
        await res.json();

    if(data.success)
    {
        alert("Đã xử lý");

        location.reload();
    }
}

</script>