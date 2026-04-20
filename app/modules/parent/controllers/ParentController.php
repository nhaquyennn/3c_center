<?php
class ParentController extends Controller
{
    private $parentModel;

    public function __construct()
    {
        // Khởi tạo model để dùng chung cho các hàm bên dưới
        $this->parentModel = new ParentModel();
    }

    public function dashboard()
    {
        $this->role(['parent']);
        // 1. Lấy dữ liệu từ Model (Đang fix ID 9 trong Model)
        $data = $this->parentModel->getParentAndStudents();
        
        // 2. Trích xuất biến để View có thể sử dụng trực tiếp
        $parentName = $data['parent_name'];
        $students = $data['students'];
        $parentInitials = $this->getInitials($parentName);
        

        // 3. Thiết lập đường dẫn view
        $view = ROOT_PATH . "/modules/parent/views/parent_dashboard.php";
        $header = ROOT_PATH . "/modules/layouts/header_parent.php";

        require_once ROOT_PATH . "/modules/layouts/parent_main.php";
    }

    public function calendar()
    {
        $data = $this->parentModel->getParentAndStudents();
        $parentName = $data['parent_name'];
        $students = $data['students'];
        $parentInitials = $this->getInitials($parentName);

        $view = ROOT_PATH . "/modules/parent/views/calendar.php";
        $header = ROOT_PATH . "/modules/layouts/header_parent.php";

        require_once ROOT_PATH . "/modules/layouts/parent_main.php";
    }

    public function report()
    {
        $data = $this->parentModel->getParentAndStudents();
        $parentName = $data['parent_name'];
        $students = $data['students'];
        $parentInitials = $this->getInitials($parentName);

        $view = ROOT_PATH . "/modules/parent/views/daily_report.php";
        $header = ROOT_PATH . "/modules/layouts/header_parent.php";

        require_once ROOT_PATH . "/modules/layouts/parent_main.php";
    }

    // Hàm hỗ trợ tạo avatar chữ cái đầu
    public function getInitials($name) {
        $name = trim($name);
        if (empty($name)) return "??";
        $words = explode(" ", $name);
        $initials = "";
        if (count($words) >= 2) {
            $initials = mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1);
        } else {
            $initials = mb_substr($words[0], 0, 1);
        }
        return mb_strtoupper($initials);
    }
}