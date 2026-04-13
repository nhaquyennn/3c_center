<?php
class SessionController extends Controller
{
    public function index()
    {
        $class_id = $_GET['class_id'] ?? null;

        if (!$class_id) {
            die("Thiếu class_id");
        }

        $model = new SessionModel();
        $sessions = $model->getByClass($class_id);

        require_once ROOT_PATH . "/modules/teacher/models/TeacherModel.php";
        $teachers = (new TeacherModel())->getAll([], 1000, 0);

        $header = ROOT_PATH . "/modules/layouts/header_session.php";
        $view = ROOT_PATH . "/modules/session/views/index.php";
        require_once ROOT_PATH . "/modules/layouts/main.php";
    }

    public function assign()
    {
        $model = new SessionModel();

        $data = [
            'session_id' => $_POST['session_id'],
            'teacher_id' => $_POST['teacher_id']
        ];

        // check trùng lịch
        if ($model->isTeacherBusy($data['teacher_id'], $data['session_id'])) {
            die("Giáo viên đã có lịch trùng!");
        }

        $model->assignTeacher($data);

        header("Location: " . $_SERVER['HTTP_REFERER']);
    }

    public function createEvent()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $model = new SessionModel();
        $model->createEvent($data);
    }

    public function getEvents()
    {
        $model = new SessionModel();
        $events = $model->getEvents();

        echo json_encode($events);
    }

    public function calendar()
    {
        $class_id = $_GET['class_id'] ?? null;

        if (!$class_id) {
            header("Location: ?module=class");
            exit;
        }

        require_once ROOT_PATH . "/modules/teacher/models/TeacherModel.php";
        $teachers = (new TeacherModel())->getAll([], 1000, 0);

        $header = ROOT_PATH . "/modules/layouts/header_session.php";
        $view = ROOT_PATH . "/modules/session/views/calendar.php";
        require_once ROOT_PATH . "/modules/layouts/main.php";
    }
}