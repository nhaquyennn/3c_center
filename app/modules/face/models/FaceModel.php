
<?php

class FaceModel
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->connect();
    }

    // =========================
    // USERS
    // =========================
    public function getUsers()
    {
        $sql = "
            SELECT
                user_id,
                name,
                role
            FROM users
            WHERE status = '1'
            ORDER BY name ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================
    // CHECK IN
    // =========================
    public function checkIn($user_id, $imageName = null)
    {
        try {

            // =====================================
            // USER -> TEACHER
            // =====================================

            $sql = "
                SELECT
                    teacher_id
                FROM teachers
                WHERE user_id = ?
                LIMIT 1
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$user_id]);

            $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$teacher) {

                return [
                    'success' => false,
                    'message' => 'Không tìm thấy giảng viên',
                    'debug_user_id' => $user_id
                ];
            }

            $teacher_id = $teacher['teacher_id'];

            // =====================================
            // GET SESSION TODAY
            // =====================================

            $sql = "
                SELECT
                    st.teacher_id,
                    s.session_id,
                    s.class_id,
                    s.session_date,
                    s.shift_id,
                    sh.start_time,
                    sh.end_time

                FROM session_teachers st

                INNER JOIN sessions s
                    ON s.session_id = st.session_id

                LEFT JOIN shifts sh
                    ON sh.shift_id = s.shift_id

                WHERE st.teacher_id = ?
                AND DATE(s.session_date) = CURDATE()

                ORDER BY sh.start_time ASC

                LIMIT 1
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$teacher_id]);

            $session = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$session) {

                return [
                    'success' => false,
                    'message' => 'Không có lịch dạy hôm nay',
                    'debug_teacher_id' => $teacher_id
                ];
            }

            // =====================================
            // CHECK EXIST
            // =====================================

            $sql = "
                SELECT attendance_id
                FROM teacher_attendance
                WHERE teacher_id = ?
                AND session_id = ?
                AND DATE(session_date) = CURDATE()
                LIMIT 1
            ";

            $stmt = $this->db->prepare($sql);

            $stmt->execute([
                $teacher_id,
                $session['session_id']
            ]);

            $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

            // =====================================
            // STATUS
            // =====================================

            $now = time();

            $startTime = strtotime(
                $session['session_date']
                . ' ' .
                $session['start_time']
            );

            $status = ($now > $startTime)
                ? 'late'
                : 'present';

            // =====================================
            // UPDATE
            // =====================================

            if ($attendance) {

                $sql = "
                    UPDATE teacher_attendance
                    SET
                        check_in_time = NOW(),
                        face_image = ?,
                        status = ?
                    WHERE attendance_id = ?
                ";

                $stmt = $this->db->prepare($sql);

                $stmt->execute([
                    $imageName,
                    $status,
                    $attendance['attendance_id']
                ]);

                return [
                    'success' => true,
                    'message' => 'Updated check-in',
                    'teacher_id' => $teacher_id,
                    'session_id' => $session['session_id'],
                    'status' => $status
                ];
            }

            // =====================================
            // INSERT
            // =====================================

            $sql = "
                INSERT INTO teacher_attendance
                (
                    teacher_id,
                    session_id,
                    class_id,
                    session_date,
                    check_in_time,
                    face_image,
                    status
                )
                VALUES
                (
                    ?, ?, ?, ?, NOW(), ?, ?
                )
            ";

            $stmt = $this->db->prepare($sql);

            $ok = $stmt->execute([
                $teacher_id,
                $session['session_id'],
                $session['class_id'],
                $session['session_date'],
                $imageName,
                $status
            ]);

            if (!$ok) {

                return [
                    'success' => false,
                    'message' => 'Insert thất bại',
                    'sql_error' => $stmt->errorInfo()
                ];
            }

            return [
                'success' => true,
                'message' => 'Check-in success',
                'teacher_id' => $teacher_id,
                'session_id' => $session['session_id'],
                'status' => $status
            ];

        } catch (Exception $e) {

            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // =========================
    // CHECK OUT
    // =========================
    public function checkOut($teacher_id)
    {
        $sql = "
            SELECT attendance_id
            FROM teacher_attendance
            WHERE teacher_id = ?
            AND DATE(session_date) = CURDATE()
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([$teacher_id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {

            return [
                'success' => false,
                'message' => 'Chưa check-in'
            ];
        }

        $sql = "
            UPDATE teacher_attendance
            SET check_out_time = NOW()
            WHERE attendance_id = ?
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            $row['attendance_id']
        ]);

        return [
            'success' => true,
            'message' => 'Check-out success'
        ];
    }

    // =========================
    // LATE REPORT
    // =========================
    public function getLateTeachers()
    {
        $sql = "
            SELECT *
            FROM vw_teacher_attendance_report
            WHERE status_calculated = 'late'
            ORDER BY check_in_time DESC
        ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
