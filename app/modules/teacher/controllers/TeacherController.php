<?php
class TeacherController extends Controller
{
    // ===== DANH SÁCH =====
    public function index()
    {
        $model = new TeacherModel();

        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $keyword = $_GET['keyword'] ?? null;

        $filters = [
            'keyword' => $_GET['keyword'] ?? null,
            'specialization' => $_GET['specialization'] ?? null,
            'salary_type' => $_GET['salary_type'] ?? null,
            'status' => $_GET['status'] ?? null,
        ];

        $teachers = $model->getAll($filters, $limit, $offset);
        $total = $model->countAll($filters);

        $totalPages = ceil($total / $limit);

        $view = ROOT_PATH . "/modules/teacher/views/index.php";
        $header = ROOT_PATH . "/modules/layouts/header_teacher.php";

        require_once ROOT_PATH . "/modules/layouts/main.php";
    }

    // ===== FORM CREATE =====
    public function create()
    {
        $view = ROOT_PATH . "/modules/teacher/views/create.php";
        $header = ROOT_PATH . "/modules/layouts/header_teacher.php";

        require_once ROOT_PATH . "/modules/layouts/main.php";
    }

    // ===== STORE =====
    public function store()
    {
        $model = new TeacherModel();

        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'specialization' => $_POST['specialization'],
            'hire_date' => $_POST['hire_date'],
            'salary_type' => $_POST['salary_type'],
            'salary_value' => $_POST['salary_value'],
            'status' => $_POST['status']
        ];

        $model->create($data);

        header("Location: ?module=teacher");
    }

    // ===== FORM EDIT =====
    public function edit()
    {
        $id = $_GET['id'] ?? 0;

        $model = new TeacherModel();
        $teacher = $model->findById($id);

        $view = ROOT_PATH . "/modules/teacher/views/edit.php";
        $header = ROOT_PATH . "/modules/layouts/header_teacher.php";

        require_once ROOT_PATH . "/modules/layouts/main.php";
    }

    // ===== UPDATE =====
    public function update()
    {
        $model = new TeacherModel();

        $data = [
            'teacher_id' => $_POST['teacher_id'],
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'specialization' => $_POST['specialization'],
            'hire_date' => $_POST['hire_date'],
            'salary_type' => $_POST['salary_type'],
            'salary_value' => $_POST['salary_value']
        ];

        $model->update($data);

        header("Location: ?module=teacher&action=index");
        exit;
    }

    // ===== DELETE =====
    public function delete()
    {
        $id = $_GET['id'] ?? 0;

        if ($id) {
            $model = new TeacherModel();
            $model->delete($id);
        }

        header("Location: ?module=teacher&action=index");
        exit;
    }

    public function restore()
    {
        $id = $_GET['id'];

        $model = new TeacherModel();
        $model->restore($id);

        header("Location: ?module=teacher");
    }
    // ===== CẤU HÌNH BẬC LƯƠNG =====
public function salary_config()
{
    $model = new TeacherModel();
    // Lấy cấu hình các bậc lương hiện có
    $salary_levels = $model->getSalaryLevels(); 

    $view = ROOT_PATH . "/modules/teacher/views/salary_config.php";
    $header = ROOT_PATH . "/modules/layouts/header_teacher.php";
    require_once ROOT_PATH . "/modules/layouts/main.php";
}
// ===== Update bậc lương =====
public function saveSalaryLevels()
{
    header('Content-Type: application/json');

    $data = json_decode(file_get_contents("php://input"), true);

    $model = new TeacherModel();

    $success = true;

    foreach ($data['levels'] as $level) {

        // bậc 1 luôn = 0
        if ($level['id'] == 1 || $level['requirement_sessions'] == '') {
            $level['requirement_sessions'] = 0;
        }

        $ok = $model->updateSalaryLevel($level);

        if (!$ok) {
            $success = false;
        }
    }

    echo json_encode([
        'success' => $success
    ]);

    exit;
    foreach ($data['levels'] as $level) {

    // level 1 luôn = 0
    if ($level['level'] == 1) {
        $level['requirement_sessions'] = 0;
    }

    $ok = $model->updateSalaryLevel($level);

    if (!$ok) {
        $success = false;
    }
}
}
// ===== BẢNG LƯƠNG THÁNG (PAYROLL) =====
public function payroll()
{
    $model = new TeacherModel();
    
    // Nếu có yêu cầu chạy tính lương (từ nút bấm)
    if (isset($_POST['calculate'])) {
        $model->calculateAllSalaries(date('m'), date('Y'));
        header("Location: ?module=teacher&action=payroll");
        exit;
    }

    // Lấy dữ liệu từ VIEW v_payroll_current_month
    $payroll_data = $model->getCurrentMonthPayroll();

    $view = ROOT_PATH . "/modules/teacher/views/payroll.php";
    $header = ROOT_PATH . "/modules/layouts/header_teacher.php";
    require_once ROOT_PATH . "/modules/layouts/main.php";
}
// thưởng phạt
public function bonus_penalties() {
    $model = new TeacherModel();

    $month = $_GET['month'] ?? date('m');
    $year  = $_GET['year'] ?? date('Y');

    $teachers = $model->getTeachers();
    $stats = $model->getStats($month, $year);
    $history = $model->getHistory($month, $year);

    
    $view = ROOT_PATH . "/modules/teacher/views/bonus_penalties.php";
    $header = ROOT_PATH . "/modules/layouts/header_teacher.php";
    require_once ROOT_PATH . "/modules/layouts/main.php";
}

public function saveTransaction() {
    header('Content-Type: application/json');

    try {
        $data = json_decode(file_get_contents("php://input"), true);

        $model = new TeacherModel();

        if ($data['type'] == 'penalty') {
            $ok = $model->addPenalty($data);
        } else {
            $ok = $model->addBonus($data);
        }

        echo json_encode(['success' => $ok]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }

    exit;
}
}