<?php
// fetch_attendance.php

include '../endpoint/config.php';

// Get the registration number from POST data
$reg_no = $_POST['reg_no'] ?? '';

if (empty($reg_no)) {
    echo "<p>No registration number provided.</p>";
    exit;
}

// Prepare a statement to fetch the student_id from students table
$student_query = "SELECT id, name FROM students WHERE reg_no = ?";
$stmt_student = $conn->prepare($student_query);
if (!$stmt_student) {
    echo "<p>Database query error: " . htmlspecialchars($conn->error) . "</p>";
    exit;
}
$stmt_student->bind_param("s", $reg_no);
$stmt_student->execute();
$result_student = $stmt_student->get_result();

if ($result_student->num_rows === 0) {
    echo "<p>No student found with registration number: " . htmlspecialchars($reg_no) . "</p>";
    $stmt_student->close();
    $conn->close();
    exit;
}

// Assuming registration_number is unique, fetch the first row
$student = $result_student->fetch_assoc();
$student_id = $student['id'];
$student_name = htmlspecialchars($student['name']);
$stmt_student->close();

// Prepare a statement to fetch attendance records for the student
$attendance_query = "
    SELECT 
        a.id, 
        p.name AS professor_name, 
        s.subject_name, 
        a.semester, 
        a.status, 
        a.timestamp 
    FROM 
        attendance a
    JOIN 
        professors p ON a.professor_id = p.prof_id
    JOIN 
        subjects s ON a.subject_id = s.subject_id
    WHERE 
        a.student_id = ?";
$stmt_attendance = $conn->prepare($attendance_query);
if (!$stmt_attendance) {
    echo "<p>Database query error: " . htmlspecialchars($conn->error) . "</p>";
    exit;
}
$stmt_attendance->bind_param("i", $student_id);
$stmt_attendance->execute();
$result_attendance = $stmt_attendance->get_result();

if ($result_attendance->num_rows > 0) {
    echo "<h3>Attendance Records for " . $student_name . " (Registration Number: " . htmlspecialchars($reg_no) . ")</h3>";
    echo "<table>
            <tr>
                <th>ID</th>
                <th>Professor</th>
                <th>Subject</th>
                <th>Semester</th>
                <th>Status</th>
                <th>Timestamp</th>
            </tr>";
    while ($row = $result_attendance->fetch_assoc()) {
        $id = htmlspecialchars($row['id']);
        $professor_name = htmlspecialchars($row['professor_name']);
        $subject_name = htmlspecialchars($row['subject_name']);
        $semester = htmlspecialchars($row['semester']);
        $status = htmlspecialchars($row['status']);
        $timestamp = htmlspecialchars($row['timestamp']);

        // Render status dropdown
        $status_dropdown = "
            <select class='status-dropdown' data-id='{$id}'>
                <option value='Present' " . ($status === 'Present' ? 'selected' : '') . ">Present</option>
                <option value='Absent' " . ($status === 'Absent' ? 'selected' : '') . ">Absent</option>
            </select>";

        echo "<tr>
                <td>{$id}</td>
                <td>{$professor_name}</td>
                <td>{$subject_name}</td>
                <td>{$semester}</td>
                <td>{$status_dropdown}</td>"?>
              <td><?php echo date("Y-m-d", strtotime($timestamp)); ?></td>
              <?php echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No attendance records found for registration number: " . htmlspecialchars($reg_no) . "</p>";
}

$stmt_attendance->close();
$conn->close();
?>
