<?php

class FaceController extends Controller
{
    // dashboard
    // public function index()
    // {
    //     $view = ROOT_PATH . "/modules/face/views/index.php";
    //     $header = ROOT_PATH . "/modules/layouts/header_teacher.php";

    //     require_once ROOT_PATH . "/modules/layouts/main.php";
    // }

    // giao diện thêm khuôn mặt
    public function enroll()
    {
        $model = new FaceModel();

        $users = $model->getUsers();

        $view = ROOT_PATH . "/modules/face/views/enroll.php";
        $header = ROOT_PATH . "/modules/layouts/header_teacher.php";

        require_once ROOT_PATH . "/modules/layouts/main.php";
    }

    // giao diện chấm công realtime
    public function attendance()
    {
        $view = ROOT_PATH . "/modules/face/views/teacher_attendance.php";
        $header = ROOT_PATH . "/modules/layouts/header_teacher.php";

        require_once ROOT_PATH . "/modules/layouts/main.php";
    }

public function checkIn()
{
    // header('Content-Type: application/json');

    // $teacher_id = $_POST['teacher_id'] ?? 0;

    // $debug = [
    //     'received_teacher_id' => $teacher_id,
    //     'server_time' => date('Y-m-d H:i:s'),
    //     'today' => date('Y-m-d')
    // ];

    // $model = new FaceModel();

    // $result = $model->checkIn($teacher_id, $_FILES['image']['name'] ?? null);

    // // gắn debug vào response
    // $result['debug'] = $debug;

    // echo json_encode($result);

    header('Content-Type: application/json');

    try {

        $user_id = $_POST['teacher_id'] ?? 0;

        if (!$user_id) {

            echo json_encode([
                'success' => false,
                'message' => 'Missing user_id'
            ]);

            return;
        }

        // =========================================
        // SAVE IMAGE
        // =========================================

        $imageName = null;

        if (
            isset($_FILES['image'])
            &&
            $_FILES['image']['error'] == 0
        ) {

            $uploadDir =
                ROOT_PATH .
                "/public/uploads/attendance/";

            // create folder
            if (!is_dir($uploadDir)) {

                mkdir(
                    $uploadDir,
                    0777,
                    true
                );
            }

            $ext = pathinfo(
                $_FILES['image']['name'],
                PATHINFO_EXTENSION
            );

            if (!$ext) {
                $ext = 'jpg';
            }

            $imageName =
                'attendance_' .
                time() .
                '_' .
                rand(1000,9999)
                . '.' .
                $ext;

            $uploadPath =
                $uploadDir .
                $imageName;

            move_uploaded_file(
                $_FILES['image']['tmp_name'],
                $uploadPath
            );
        }

        // =========================================
        // CHECK IN
        // =========================================

        $model = new FaceModel();

        $result = $model->checkIn(
            $user_id,
            $imageName
        );

        echo json_encode($result);

    } catch (Exception $e) {

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
public function checkOut()
{
    header('Content-Type: application/json');

    $teacher_id = $_POST['teacher_id'] ?? 0;

    $model = new FaceModel();
    echo json_encode($model->checkOut($teacher_id));
}
public function lateReport()
{
    $model = new FaceModel();

    $reports = $model->getLateTeachers();

    $view =
        ROOT_PATH .
        "/modules/face/views/late_report.php";

    $header = ROOT_PATH . "/modules/layouts/header_teacher.php";

   require_once ROOT_PATH . "/modules/layouts/main.php";
}

}