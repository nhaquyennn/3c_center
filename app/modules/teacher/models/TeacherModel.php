<?php
class TeacherModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }
// Lấy danh sách tất cả giảng viên (dành cho dropdown)
 public function getTeachers() {
    return $this->db->query("
        SELECT t.teacher_id, u.name
        FROM teachers t
        JOIN users u ON t.user_id = u.user_id
    ")->fetchAll();
}

    public function getAll($filters, $limit, $offset)
    {
        // FIX: đảm bảo không bị undefined key
        $filters = array_merge([
            'keyword' => null,
            'specialization' => null,
            'salary_type' => null,
            'status' => null
        ], $filters ?? []);

        $sql = "SELECT t.*, u.name, u.email 
            FROM teachers t
            JOIN users u ON t.user_id = u.user_id
            WHERE 1";

        if (!empty($filters['keyword'])) {
            $sql .= " AND (u.name LIKE :kw OR u.email LIKE :kw)";
        }

        if (!empty($filters['specialization'])) {
            $sql .= " AND t.specialization = :specialization";
        }

        if ($filters['salary_type'] !== null && $filters['salary_type'] !== '') {
            $sql .= " AND t.salary_type = :salary_type";
        }

        if ($filters['status'] !== null && $filters['status'] !== '') {
            $sql .= " AND t.status = :status";
        }

        $sql .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        if (!empty($filters['keyword'])) {
            $stmt->bindValue(':kw', "%" . $filters['keyword'] . "%");
        }

        if (!empty($filters['specialization'])) {
            $stmt->bindValue(':specialization', $filters['specialization']);
        }

        if ($filters['salary_type'] !== null && $filters['salary_type'] !== '') {
            $stmt->bindValue(':salary_type', $filters['salary_type']);
        }

        if ($filters['status'] !== null && $filters['status'] !== '') {
            $stmt->bindValue(':status', $filters['status']);
        }

        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ===== COUNT =====
    public function countAll($filters)
    {
        $sql = "SELECT COUNT(*) 
            FROM teachers t
            JOIN users u ON t.user_id = u.user_id
            WHERE 1";

        if (!empty($filters['keyword'])) {
            $sql .= " AND (u.name LIKE :kw OR u.email LIKE :kw)";
        }

        if (!empty($filters['specialization'])) {
            $sql .= " AND t.specialization = :specialization";
        }

        if ($filters['salary_type'] !== null && $filters['salary_type'] !== '') {
            $sql .= " AND t.salary_type = :salary_type";
        }

        if ($filters['status'] !== null && $filters['status'] !== '') {
            $sql .= " AND t.status = :status";
        }

        $stmt = $this->db->prepare($sql);

        if (!empty($filters['keyword'])) {
            $stmt->bindValue(':kw', "%" . $filters['keyword'] . "%");
        }

        if (!empty($filters['specialization'])) {
            $stmt->bindValue(':specialization', $filters['specialization']);
        }

        if ($filters['salary_type'] !== null && $filters['salary_type'] !== '') {
            $stmt->bindValue(':salary_type', $filters['salary_type']);
        }

        if ($filters['status'] !== null && $filters['status'] !== '') {
            $stmt->bindValue(':status', $filters['status']);
        }

        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // ===== FIND =====
    public function findById($id)
    {
        $sql = "SELECT t.*, u.name, u.email 
                FROM teachers t
                JOIN users u ON t.user_id = u.user_id
                WHERE t.teacher_id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    // ===== CREATE =====
    public function create($data)
    {
        try {
            $this->db->beginTransaction();

            // 1. insert user
            $sqlUser = "INSERT INTO users (name, email, role)
                    VALUES (:name, :email, 'teacher')";
            $stmtUser = $this->db->prepare($sqlUser);
            $stmtUser->execute([
                'name' => $data['name'],
                'email' => $data['email']
            ]);

            $user_id = $this->db->lastInsertId();

            // 2. insert teacher
            $sqlTeacher = "INSERT INTO teachers 
            (user_id, specialization, hire_date, salary_type, salary_value, status)
            VALUES 
            (:user_id, :specialization, :hire_date, :salary_type, :salary_value, :status)";

            $stmtTeacher = $this->db->prepare($sqlTeacher);
            $stmtTeacher->execute([
                'user_id' => $user_id,
                'specialization' => $data['specialization'],
                'hire_date' => $data['hire_date'],
                'salary_type' => $data['salary_type'],
                'salary_value' => $data['salary_value'],
                'status' => $data['status']
            ]);

            $this->db->commit();

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // ===== UPDATE =====
    public function update($data)
    {
        // update users
        $sql = "UPDATE users u
                JOIN teachers t ON u.user_id = t.user_id
                SET u.name = :name, u.email = :email
                WHERE t.teacher_id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'name' => $data['name'],
            'email' => $data['email'],
            'id' => $data['teacher_id']
        ]);

        // update teacher
        $sql2 = "UPDATE teachers
                SET specialization=:specialization,
                    hire_date=:hire_date,
                    salary_type=:salary_type,
                    salary_value=:salary_value
                WHERE teacher_id=:id";

        $stmt2 = $this->db->prepare($sql2);

        return $stmt2->execute([
            'specialization' => $data['specialization'],
            'hire_date' => $data['hire_date'],
            'salary_type' => $data['salary_type'],
            'salary_value' => $data['salary_value'],
            'id' => $data['teacher_id']
        ]);
    }

    // ===== DELETE =====
    public function delete($id)
    {
        $sql = "UPDATE teachers 
            SET status = 0 
            WHERE teacher_id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function restore($id)
    {
        $sql = "UPDATE teachers SET status = 1 WHERE teacher_id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
// Update bậc lương
public function updateSalaryLevel($data)
{
    $sql = "UPDATE salary_levels
            SET
                level_name = :level_name,
                requirement_sessions = :requirement_sessions,
                amount = :amount
            WHERE id = :id";

    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        'level_name' => $data['level_name'],
        'requirement_sessions' => $data['requirement_sessions'],
        'amount' => $data['amount'],
        'id' => $data['id']
    ]);
}
    // Lấy danh sách bậc lương
    public function getSalaryLevels()
{
    // Sử dụng Subquery để đếm số GV đang ở mỗi bậc lương
    $sql = "SELECT sl.*, 
            (SELECT COUNT(*) FROM teachers t WHERE t.current_level_id = sl.id) as teacher_count 
            FROM salary_levels sl 
            ORDER BY sl.type, sl.level";
    return $this->db->query($sql)->fetchAll();
}

    // Gọi Procedure tính toán lương toàn bộ GV
    public function calculateAllSalaries($month, $year)
    {
        $sql = "CALL sp_calculate_all_salaries(?, ?)";
        return $this->db->prepare($sql)->execute([$month, $year]);
    }

    // Lấy dữ liệu từ VIEW bảng lương
    public function getCurrentMonthPayroll()
    {
        $sql = "SELECT * FROM v_payroll_current_month";
        return $this->db->query($sql)->fetchAll();
    }

    // lưu thưởng phạt
public function saveAdjustment($data) {
    $stmt = $this->db->prepare("
        INSERT INTO allowance_penalties 
        (teacher_id, type, amount, reason, month, year, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    return $stmt->execute([
        $data['teacher_id'],
        $data['type'], // bonus | penalty
        $data['amount'],
        $data['reason'],
        $data['month'],
        $data['year'],
        $_SESSION['user_id'] ?? 1
    ]);
}

// Lấy thống kê tháng về thưởng phạt
public function getStats($month, $year) {
    $sql = "
        SELECT 
            SUM(CASE WHEN type='bonus' THEN amount ELSE 0 END) as total_bonus,
            SUM(CASE WHEN type='penalty' THEN amount ELSE 0 END) as total_penalty,
            COUNT(CASE WHEN type='bonus' THEN 1 END) as bonus_count,
            COUNT(CASE WHEN type='penalty' THEN 1 END) as penalty_count
        FROM allowance_penalties
        WHERE month = ? AND year = ?
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$month, $year]);
    return $stmt->fetch();
}

// Lịch sủ thưởng phạt
public function getHistory($month, $year) {
    $sql = "
        SELECT ap.*, u.name
        FROM allowance_penalties ap
        JOIN teachers t ON t.teacher_id = ap.teacher_id
        JOIN users u ON u.user_id = t.user_id
        WHERE ap.month = ? AND ap.year = ?
        ORDER BY ap.created_at DESC
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([$month, $year]);
    return $stmt->fetchAll();
}

// Thêm phạt
public function addPenalty($data) {
    $sql = "INSERT INTO allowance_penalties 
            (teacher_id, type, amount, reason, month, year, created_by)
            VALUES (?, 'penalty', ?, ?, ?, ?, ?)";

    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        $data['teacher_id'],
        $data['amount'],
        $data['reason'],
        $data['month'],
        $data['year'],
        $_SESSION['user_id'] ?? 1
    ]);
}

// Thêm thưởng
public function addBonus($data) {
    $sql = "INSERT INTO allowance_penalties 
            (teacher_id, type, amount, reason, month, year, created_by)
            VALUES (?, 'bonus', ?, ?, ?, ?, ?)";

    $stmt = $this->db->prepare($sql);

    return $stmt->execute([
        $data['teacher_id'],
        $data['amount'],
        $data['reason'],
        $data['month'],
        $data['year'],
        $_SESSION['user_id'] ?? 1
    ]);
}
}