<?php
class ParentModel {
    private $db;

    public function __construct() {
        // Giả sử file Database trả về một kết nối PDO
        $database = new Database();
        $this->db = $database->connect();
    }

    /**
     * Lấy danh sách phụ huynh và các con tương ứng
     * Hiện tại đang fix cứng ID = 9 để giả lập dữ liệu chưa đăng nhập
     */
    public function getParentAndStudents() {
        // Tạm thời fix ID phụ huynh để test dữ liệu từ file ccc.sql
        $current_parent_user_id = 9; 

        $sql = "SELECT 
                    u_p.name AS parent_name, 
                    u_s.name AS student_name, 
                    s.student_id 
                FROM users u_p 
                JOIN parents p ON u_p.user_id = p.user_id 
                JOIN parent_student ps ON p.parent_id = ps.parent_id 
                JOIN students s ON ps.student_id = s.student_id 
                JOIN users u_s ON s.user_id = u_s.user_id 
                WHERE u_p.user_id = :parent_id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['parent_id' => $current_parent_user_id]);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Tổ chức lại dữ liệu trả về để View dễ sử dụng
            return [
                'parent_name' => !empty($data) ? $data[0]['parent_name'] : "Chưa đăng nhập",
                'students' => $data // Danh sách các con
            ];
        } catch (PDOException $e) {
            // Xử lý lỗi nếu truy vấn thất bại
            return [
                'parent_name' => "Lỗi kết nối",
                'students' => []
            ];
        }
    }

    // Hàm lấy chữ cái đại diện (có thể đặt ở đây hoặc file helper)
    public function getInitials($name) {
        if (empty($name)) return "??";
        $words = explode(" ", trim($name));
        $initials = "";
        if (count($words) >= 2) {
            $initials = mb_substr($words[0], 0, 1) . mb_substr(end($words), 0, 1);
        } else {
            $initials = mb_substr($words[0], 0, 1);
        }
        return mb_strtoupper($initials);
    }
}