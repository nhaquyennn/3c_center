<?php
class ClassController extends Controller
{
    public function index()
    {
        $model = new ClassModel();

        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $filters = [
            'keyword' => $_GET['keyword'] ?? null,
            'course_id' => $_GET['course_id'] ?? null,
            'package_id' => $_GET['package_id'] ?? null,
            'status' => $_GET['status'] ?? null,
        ];

        //  Lấy danh sách lớp (đã JOIN thêm package)
        $classes = $model->getAll($filters, $limit, $offset);

        //  Chuẩn hóa dữ liệu để view dùng dễ
        foreach ($classes as &$c) {

            if (empty($c['class_name'])) {
                $c['class_name'] = $c['course_name'] . ' - Lớp #' . $c['class_id'];
            }

            if (empty($c['name'])) {
                $c['name'] = 'Chưa phân loại';
            }

            $c['total'] = $c['total'] ?? 0;
            $c['learned'] = $c['learned'] ?? 0;
        }

        unset($c);

        $total = $model->countAll($filters);
        $totalPages = ceil($total / $limit);

        require_once ROOT_PATH . "/modules/course/models/CourseModel.php";
        $courses = (new CourseModel())->getAll();

        $header = ROOT_PATH . "/modules/layouts/header_teacher.php";
        $view = ROOT_PATH . "/modules/class/views/index.php";
        require_once ROOT_PATH . "/modules/layouts/main.php";
    }

    public function create()
    {
        require_once ROOT_PATH . "/modules/course/models/CourseModel.php";
        $courses = (new CourseModel())->getAll();

        require_once ROOT_PATH . "/modules/package/models/PackageModel.php";
        $packages = (new PackageModel())->getAll();

        $header = ROOT_PATH . "/modules/layouts/header_teacher.php";
        $view = ROOT_PATH . "/modules/class/views/create.php";
        require_once ROOT_PATH . "/modules/layouts/main.php";
    }

    public function store()
    {
        $model = new ClassModel();

        $data = [
            'course_id' => $_POST['course_id'],
            'package_id' => $_POST['package_id'],
            'class_name' => $_POST['class_name'] ?? null,
            'start_date' => $_POST['start_date']
        ];

        $model->create($data);

        header("Location: ?module=class");
    }

    public function edit()
    {
        $id = $_GET['id'];

        $model = new ClassModel();
        $class = $model->findById($id);

        require_once ROOT_PATH . "/modules/course/models/CourseModel.php";
        $courses = (new CourseModel())->getAll();

        require_once ROOT_PATH . "/modules/package/models/PackageModel.php";
        $packages = (new PackageModel())->getAll();

        $header = ROOT_PATH . "/modules/layouts/header_teacher.php";
        $view = ROOT_PATH . "/modules/class/views/edit.php";
        require_once ROOT_PATH . "/modules/layouts/main.php";
    }

    public function update()
    {
        $model = new ClassModel();

        $data = [
            'class_id' => $_POST['class_id'],
            'course_id' => $_POST['course_id'],
            'package_id' => $_POST['package_id'],
            'class_name' => $_POST['class_name'] ?? null,
            'start_date' => $_POST['start_date']
        ];

        $model->update($data);

        header("Location: ?module=class");
    }

    public function detail()
    {
        $id = $_GET['id'];

        $model = new ClassModel();
        $class = $model->getDetail($id);

        if (!$class) {
            die("Class not found");
        }

        $header = ROOT_PATH . "/modules/layouts/header_teacher.php";
        $view = ROOT_PATH . "/modules/class/views/detail.php";
        require_once ROOT_PATH . "/modules/layouts/main.php";
    }
}