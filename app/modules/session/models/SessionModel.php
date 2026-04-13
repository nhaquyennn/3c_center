<?php
class SessionModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function getByClass($class_id)
    {
        $sql = "SELECT cs.*, u.name AS teacher_name
                FROM class_sessions cs
                LEFT JOIN teachers t ON cs.teacher_id = t.teacher_id
                LEFT JOIN users u ON t.user_id = u.user_id
                WHERE cs.class_id = ?
                ORDER BY cs.session_no";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$class_id]);
        return $stmt->fetchAll();
    }

    public function assignTeacher($data)
    {
        $sql = "UPDATE class_sessions 
                SET teacher_id = :teacher_id
                WHERE session_id = :session_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
    }

    public function isTeacherBusy($teacher_id, $session_id)
    {
        // lấy info buổi học
        $sql = "SELECT * FROM class_sessions WHERE session_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$session_id]);
        $session = $stmt->fetch();

        // check trùng
        $sql = "SELECT * FROM class_sessions
                WHERE teacher_id = ?
                AND date = ?
                AND session_id != ?
                AND (start_time < ? AND end_time > ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $teacher_id,
            $session['date'],
            $session_id,
            $session['end_time'],
            $session['start_time']
        ]);

        return $stmt->rowCount() > 0;
    }

    public function getEvents()
    {
        $sql = "SELECT 
                cs.session_id,
                COALESCE(CONCAT('GV: ', u.name), 'Chưa có GV') AS title,
                CONCAT(cs.date, 'T', cs.start_time) AS start,
                CONCAT(cs.date, 'T', cs.end_time) AS end
            FROM class_sessions cs
            LEFT JOIN teachers t ON cs.teacher_id = t.teacher_id
            LEFT JOIN users u ON t.user_id = u.user_id";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createEvent($data)
    {
        // lấy session_no tiếp theo
        $sql = "SELECT COALESCE(MAX(session_no), 0) + 1 
            FROM class_sessions 
            WHERE class_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$data['class_id']]);
        $session_no = $stmt->fetchColumn();

        $start = new DateTime($data['start']);
        $end = new DateTime($data['end']);

        $sql = "INSERT INTO class_sessions 
        (class_id, session_no, teacher_id, date, start_time, end_time)
        VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['class_id'],
            $session_no,
            $data['teacher_id'],
            $start->format('Y-m-d'),
            $start->format('H:i:s'),
            $end->format('H:i:s')
        ]);
    }
}