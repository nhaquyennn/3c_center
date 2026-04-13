<?php
class ClassModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function getAll($filters, $limit, $offset)
    {
        $sql = "SELECT 
                c.class_id,
                c.course_id,
                c.package_id,
                c.class_name,
                c.start_date,

                co.course_name,

                p.name,
                p.session_total AS total,

                COUNT(DISTINCT cs.session_id) AS learned

            FROM classes c

            JOIN courses co 
                ON c.course_id = co.course_id

            JOIN packages p 
                ON c.package_id = p.package_id

            LEFT JOIN class_sessions cs 
                ON cs.class_id = c.class_id

            WHERE 1";

        if (!empty($filters['keyword'])) {
            $sql .= " AND c.class_name LIKE :keyword";
        }

        if (!empty($filters['course_id'])) {
            $sql .= " AND c.course_id = :course_id";
        }

        if (!empty($filters['package_id'])) {
            $sql .= " AND c.package_id = :package_id";
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] == 'upcoming') {
                $sql .= " AND c.start_date > CURDATE()";
            } elseif ($filters['status'] == 'studying') {
                $sql .= " AND c.start_date <= CURDATE()";
            } elseif ($filters['status'] == 'done') {
                $sql .= " HAVING learned >= total";
            }
        }

        $sql .= " GROUP BY 
                c.class_id,
                c.course_id,
                c.package_id,
                c.class_name,
                c.start_date,
                co.course_name,
                p.name,
                p.session_total";

        $sql .= " ORDER BY c.class_id DESC";

        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        // 🔗 bind param
        if (!empty($filters['keyword'])) {
            $stmt->bindValue(':keyword', '%' . $filters['keyword'] . '%');
        }

        if (!empty($filters['course_id'])) {
            $stmt->bindValue(':course_id', $filters['course_id']);
        }

        if (!empty($filters['package_id'])) {
            $stmt->bindValue(':package_id', $filters['package_id']);
        }

        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Đếm tổng số lớp (phục vụ phân trang)
    public function countAll($filters)
    {
        $sql = "SELECT COUNT(*) FROM classes WHERE 1";

        if (!empty($filters['course_id'])) {
            $sql .= " AND course_id = :course_id";
        }

        $stmt = $this->db->prepare($sql);

        if (!empty($filters['course_id'])) {
            $stmt->bindValue(':course_id', $filters['course_id']);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    //  Tạo lớp mới
    public function create($data)
    {
        $sql = "INSERT INTO classes (course_id, package_id, class_name, start_date)
            VALUES (:course_id, :package_id, :class_name, :start_date)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
    }

    //  Lấy chi tiết 1 lớp
    public function findById($id)
    {
        $sql = "SELECT 
                c.*,
                co.course_name,
                p.name,
                p.session_total AS total

            FROM classes c

            JOIN courses co ON c.course_id = co.course_id
            JOIN packages p ON c.package_id = p.package_id

            WHERE c.class_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //  Cập nhật lớp
    public function update($data)
    {
        $sql = "UPDATE classes 
            SET course_id = :course_id,
                package_id = :package_id,
                class_name = :class_name,
                start_date = :start_date
            WHERE class_id = :class_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
    }

    //  Xóa lớp (bonus thêm cho đủ CRUD)
    public function delete($id)
    {
        $sql = "DELETE FROM classes WHERE class_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
    }

    public function getDetail($id)
    {
        $sql = "SELECT 
                c.*,
                co.course_name,
                p.name,
                p.session_total AS total,
                COUNT(DISTINCT cs.session_id) AS learned

            FROM classes c

            JOIN courses co 
                ON c.course_id = co.course_id

            JOIN packages p 
                ON c.package_id = p.package_id

            LEFT JOIN class_sessions cs 
                ON cs.class_id = c.class_id

            WHERE c.class_id = ?

            GROUP BY 
                c.class_id,
                co.course_name,
                p.name,
                p.session_total";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("
        SELECT c.*, 
               co.name AS course_name,
               p.name AS package_name
        FROM classes c
        LEFT JOIN courses co ON c.course_id = co.course_id
        LEFT JOIN packages p ON c.package_id = p.package_id
        WHERE c.class_id = ?
    ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}