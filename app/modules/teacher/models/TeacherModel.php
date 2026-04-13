<?php
class TeacherModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
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
}