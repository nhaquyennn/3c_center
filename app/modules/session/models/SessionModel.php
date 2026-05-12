<?php
class SessionModel
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function getByClass($class_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                s.*,
                sh.name AS shift_name,
                sh.start_time,
                sh.end_time,
                r.name AS room_name,

                COUNT(a.attendance_id) AS attendance_count,

                GROUP_CONCAT(DISTINCT 
                    CASE WHEN st.role = 'main' THEN u.name END
                ) AS teacher_main,

                GROUP_CONCAT(DISTINCT 
                    CASE WHEN st.role = 'assistant' THEN u.name END
                ) AS teacher_assistant

            FROM sessions s
            LEFT JOIN shifts sh ON s.shift_id = sh.shift_id
            LEFT JOIN rooms r ON s.room_id = r.room_id
            LEFT JOIN attendances a ON a.session_id = s.session_id
            LEFT JOIN session_teachers st ON st.session_id = s.session_id
            LEFT JOIN teachers t ON st.teacher_id = t.teacher_id
            LEFT JOIN users u ON t.user_id = u.user_id
            WHERE s.class_id = ?
            GROUP BY s.session_id
            ORDER BY s.session_date ASC
        ");

        $stmt->execute([$class_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteSessions($class_id)
    {
        $this->db->prepare("DELETE FROM sessions WHERE class_id = ?")
            ->execute([$class_id]);
    }

    public function generateSessionsCustom($class_id, $start_date, $total_sessions)
    {
        $stmt = $this->db->prepare("SELECT * FROM classes WHERE class_id = ?");
        $stmt->execute([$class_id]);
        $class = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $this->db->prepare("
            SELECT day_of_week FROM schedule_days WHERE schedule_id = ?
        ");
        $stmt->execute([$class['schedule_id']]);
        $days = array_column($stmt->fetchAll(), 'day_of_week');

        sort($days);

        $date = new DateTime($start_date);
        $count = 0;

        while ($count < $total_sessions) {

            $phpDay = $date->format('N');
            $dbDay = ($phpDay == 7) ? 1 : $phpDay + 1;

            if (in_array($dbDay, $days)) {

                $shift_id = $class['shift_id'];

                $stmt = $this->db->prepare("
                    SELECT COUNT(*) FROM sessions 
                    WHERE session_date = ? AND shift_id = ?
                ");
                $stmt->execute([$date->format('Y-m-d'), $shift_id]);

                $isConflict = $stmt->fetchColumn() > 0 ? 1 : 0;

                $this->db->prepare("
                    INSERT INTO sessions 
                    (class_id, session_date, shift_id, status, note)
                    VALUES (?, ?, ?, 'scheduled', ?)
                ")->execute([
                            $class_id,
                            $date->format('Y-m-d'),
                            $shift_id,
                            $isConflict ? 'conflict' : null
                        ]);

                $count++;
            }

            $date->modify('+1 day');
        }
    }

    public function updateRoom($session_id, $room_id)
    {
        $this->db->prepare("UPDATE sessions SET room_id = ? WHERE session_id = ?")
            ->execute([$room_id, $session_id]);
    }

    public function updateShift($session_id, $shift_id)
    {
        $this->db->prepare("
            UPDATE sessions 
            SET shift_id = ?, status = 'scheduled', note = NULL
            WHERE session_id = ?
        ")->execute([$shift_id, $session_id]);
    }

    public function updateStatus($session_id, $status)
    {
        $this->db->prepare("UPDATE sessions SET status = ? WHERE session_id = ?")
            ->execute([$status, $session_id]);
    }

    public function takeAttendance($session_id)
    {
        $stmt = $this->db->prepare("SELECT class_id FROM sessions WHERE session_id = ?");
        $stmt->execute([$session_id]);
        $class_id = $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT student_id FROM enrollments WHERE class_id = ?");
        $stmt->execute([$class_id]);
        $students = $stmt->fetchAll(PDO::FETCH_COLUMN);

        foreach ($students as $student_id) {
            $this->db->prepare("
                INSERT IGNORE INTO attendances (session_id, student_id, status)
                VALUES (?, ?, 'present')
            ")->execute([$session_id, $student_id]);
        }
    }

    public function saveTeachers($session_id, $main, $assistants)
    {
        // CHECK MAIN
        if ($main && $this->isTeacherBusy($main, $session_id)) {
            header("Location: " . $_SERVER['HTTP_REFERER'] . "&error=main_conflict");
            exit;
        }

        // CHECK ASSISTANT
        if ($assistants) {
            foreach ($assistants as $t) {
                if ($this->isTeacherBusy($t, $session_id)) {
                    header("Location: " . $_SERVER['HTTP_REFERER'] . "&error=assistant_conflict");
                    exit;
                }
            }
        }

        // XÓA CŨ
        $this->db->prepare("DELETE FROM session_teachers WHERE session_id = ?")
            ->execute([$session_id]);

        // MAIN
        if ($main) {
            $this->db->prepare("
                INSERT INTO session_teachers (session_id, teacher_id, role)
                VALUES (?, ?, 'main')
            ")->execute([$session_id, $main]);
        }

        // ASSISTANT
        if ($assistants) {
            $stmt = $this->db->prepare("
            INSERT INTO session_teachers (session_id, teacher_id, role)
            VALUES (?, ?, 'assistant')
        ");

            foreach ($assistants as $t) {
                if ($t == $main)
                    continue;
                $stmt->execute([$session_id, $t]);
            }
        }
    }

    public function isTeacherBusy($teacher_id, $session_id)
    {
        $stmt = $this->db->prepare("
            SELECT 1
            FROM sessions s
            JOIN session_teachers st ON s.session_id = st.session_id
            JOIN shifts sh ON s.shift_id = sh.shift_id

            WHERE st.teacher_id = ?
            AND s.session_date = (
                SELECT session_date FROM sessions WHERE session_id = ?
            )
            AND s.session_id != ?

            AND (
                sh.start_time < (
                    SELECT sh2.end_time 
                    FROM sessions s2
                    JOIN shifts sh2 ON s2.shift_id = sh2.shift_id
                    WHERE s2.session_id = ?
                )
                AND
                sh.end_time > (
                    SELECT sh2.start_time 
                    FROM sessions s2
                    JOIN shifts sh2 ON s2.shift_id = sh2.shift_id
                    WHERE s2.session_id = ?
                )
            )

            LIMIT 1
        ");

        $stmt->execute([
            $teacher_id,
            $session_id,
            $session_id,
            $session_id,
            $session_id
        ]);

        return $stmt->fetch() ? true : false;
    }

    public function getTeachersWithStatus($session_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                t.teacher_id,
                u.name,

                CASE 
                    WHEN EXISTS (
                        SELECT 1
                        FROM sessions s
                        JOIN session_teachers st ON s.session_id = st.session_id
                        JOIN shifts sh ON s.shift_id = sh.shift_id

                        WHERE st.teacher_id = t.teacher_id
                        AND s.session_date = (
                            SELECT session_date FROM sessions WHERE session_id = ?
                        )
                        AND s.session_id != ?

                        AND (
                            sh.start_time < (
                                SELECT sh2.end_time 
                                FROM sessions s2
                                JOIN shifts sh2 ON s2.shift_id = sh2.shift_id
                                WHERE s2.session_id = ?
                            )
                            AND
                            sh.end_time > (
                                SELECT sh2.start_time 
                                FROM sessions s2
                                JOIN shifts sh2 ON s2.shift_id = sh2.shift_id
                                WHERE s2.session_id = ?
                            )
                        )
                    )
                    THEN 1 ELSE 0
                END AS is_busy

            FROM teachers t
            JOIN users u ON t.user_id = u.user_id
        ");

        $stmt->execute([
            $session_id,
            $session_id,
            $session_id,
            $session_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function isRoomBusy($room_id, $session_id)
    {
        $stmt = $this->db->prepare("
            SELECT 1
            FROM sessions s
            JOIN shifts sh ON s.shift_id = sh.shift_id

            WHERE s.room_id = ?
            AND s.session_date = (
                SELECT session_date FROM sessions WHERE session_id = ?
            )

            AND s.session_id != ?

            AND (
                sh.start_time < (
                    SELECT sh2.end_time 
                    FROM sessions s2
                    JOIN shifts sh2 ON s2.shift_id = sh2.shift_id
                    WHERE s2.session_id = ?
                )
                AND
                sh.end_time > (
                    SELECT sh2.start_time 
                    FROM sessions s2
                    JOIN shifts sh2 ON s2.shift_id = sh2.shift_id
                    WHERE s2.session_id = ?
                )
            )

            LIMIT 1
        ");

        $stmt->execute([
            $room_id,
            $session_id,
            $session_id,
            $session_id,
            $session_id
        ]);

        return $stmt->fetch() ? true : false;
    }

    public function getRoomsWithStatus($session_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                r.room_id,
                r.name,
                r.capacity,

                CASE 
                    WHEN EXISTS (
                        SELECT 1 
                        FROM sessions s
                        JOIN shifts sh ON s.shift_id = sh.shift_id

                        WHERE s.room_id = r.room_id
                        AND s.session_date = (
                            SELECT session_date 
                            FROM sessions 
                            WHERE session_id = ?
                        )
                        AND s.session_id != ?

                        AND (
                            sh.start_time < (
                                SELECT sh2.end_time 
                                FROM sessions s2
                                JOIN shifts sh2 
                                    ON s2.shift_id = sh2.shift_id
                                WHERE s2.session_id = ?
                            )
                            AND
                            sh.end_time > (
                                SELECT sh2.start_time 
                                FROM sessions s2
                                JOIN shifts sh2 
                                    ON s2.shift_id = sh2.shift_id
                                WHERE s2.session_id = ?
                            )
                        )
                    )
                    THEN 1 ELSE 0
                END AS is_busy

            FROM rooms r
            WHERE r.status = 'active'
        ");

        $stmt->execute([
            $session_id,
            $session_id,
            $session_id,
            $session_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentsForAttendance($session_id)
    {
        $stmt = $this->db->prepare("
            SELECT 
                st.student_id,
                u.name,
                a.status

            FROM sessions s
            JOIN enrollments e ON s.class_id = e.class_id
            JOIN students st ON e.student_id = st.student_id
            JOIN users u ON st.user_id = u.user_id

            LEFT JOIN attendances a 
                ON a.student_id = st.student_id 
                AND a.session_id = s.session_id

            WHERE s.session_id = ?
        ");

        $stmt->execute([$session_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveAttendance($session_id, $data)
    {
        foreach ($data as $student_id => $status) {

            // Lưu trạng thái điểm danh
            $this->db->prepare("
                INSERT INTO attendances (session_id, student_id, status)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE status = VALUES(status)
            ")->execute([$session_id, $student_id, $status]);

            // Nếu có mặt hoặc trễ → tính là đã học
            if ($status == 'present' || $status == 'late') {

                $this->db->prepare("
                    UPDATE enrollments 
                    SET attended_sessions = attended_sessions + 1
                    WHERE student_id = ? 
                    AND class_id = (SELECT class_id FROM sessions WHERE session_id = ?)
                ")->execute([$student_id, $session_id]);
            }
        }

        // cập nhật trạng thái buổi học
        $this->updateStatus($session_id, 'done');
    }
}