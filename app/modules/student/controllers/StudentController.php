<?php
class StudentController extends Controller {
    public function index() {
        $model = new StudentModel();
        $students = $model->getAll();
        $view = ROOT_PATH . "/modules/student/views/index.php";
        $header = ROOT_PATH . "/modules/layouts/header_course.php";
        require_once ROOT_PATH . "/modules/layouts/main.php";
    }
}
