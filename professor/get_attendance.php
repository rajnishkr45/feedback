<?php
// Include database configuration
include 'pro_name.php';

// Fetch attendance data
if (isset($_GET['semester']) && isset($_GET['branch']) && isset($_GET['subject'])) {
    $semester = $_GET['semester'];
    $branch = $_GET['branch'];
    $subject_id = $_GET['subject'];

    // SQL query to fetch total present classes and total classes
    $query = "
        SELECT 
            s.reg_no, 
            s.name, 
            COUNT(a.status) AS total_classes,
            SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) AS present_classes,
            ROUND(
                CASE 
                    WHEN COUNT(a.status) > 0 
                    THEN (SUM(CASE WHEN a.status = 'Present' THEN 1 ELSE 0 END) / COUNT(a.status)) * 100
                    ELSE 0 
                END, 2
            ) AS percentage
        FROM students s
        LEFT JOIN attendance a ON a.student_id = s.id
        WHERE s.semester = ? AND s.branch = ? AND a.subject_id = ?
        GROUP BY s.id, s.reg_no, s.name
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('iss', $semester, $branch, $subject_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $attendanceData = [];
    while ($row = $result->fetch_assoc()) {
        $attendanceData[] = $row;
    }

    if (empty($attendanceData)) {
        echo json_encode(['message' => 'No attendance data found for the selected filters.']);
    } else {
        echo json_encode($attendanceData);
    }
}
?>
