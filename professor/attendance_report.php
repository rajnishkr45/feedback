<?php
// Include database connection
include '../endpoint/config.php'; // Replace with your actual DB connection file

header('Content-Type: application/json');

// Get input parameters
$semester = $_GET['semester'] ?? null;
$branch = $_GET['branch'] ?? null;

if (!$semester || !$branch) {
    echo json_encode(['message' => 'Invalid request. Semester and branch are required.']);
    exit;
}

// Array to hold the final response
$response = [];

try {
    // Fetch subjects for the given semester and branch
    $subjectQuery = "
        SELECT subject_id, subject_name 
        FROM subjects 
        WHERE semester = '$semester' AND branch = '$branch'
    ";
    $subjectResult = $conn->query($subjectQuery);

    if ($subjectResult->num_rows === 0) {
        echo json_encode(['message' => 'No subjects found for the selected semester and branch.']);
        exit;
    }

    $subjects = [];
    while ($row = $subjectResult->fetch_assoc()) {
        $subjects[] = $row;
    }

    // Fetch all students for the given semester and branch
    $studentQuery = "
        SELECT id, reg_no, name 
        FROM students 
        WHERE semester = '$semester' AND branch = '$branch'
    ";
    $studentResult = $conn->query($studentQuery);

    if ($studentResult->num_rows === 0) {
        echo json_encode(['message' => 'No students found for the selected semester and branch.']);
        exit;
    }

    $students = [];
    while ($row = $studentResult->fetch_assoc()) {
        $students[] = $row;
    }

    // Prepare attendance data for all students and subjects
    $attendanceData = [];
    foreach ($students as $student) {
        $studentAttendance = [];
        $totalPresent = 0;
        $totalClasses = 0;

        foreach ($subjects as $subject) {
            // Fetch attendance for each subject and student
            $attendanceQuery = "
                SELECT 
                    COUNT(CASE WHEN status = 'Present' THEN 1 END) AS present,
                    COUNT(*) AS total
                FROM attendance
                WHERE student_id = '{$student['id']}' AND subject_id = '{$subject['subject_id']}'
            ";
            $attendanceResult = $conn->query($attendanceQuery);
            $attendance = $attendanceResult->fetch_assoc();

            $present = $attendance['present'] ?? 0;
            $total = $attendance['total'] ?? 0;

            // Append subject-wise attendance
            $studentAttendance[] = [
                'subject_id' => $subject['subject_id'],
                'subject_name' => $subject['subject_name'],
                'present' => $present,
                'total' => $total
            ];

            // Update overall totals
            $totalPresent += $present;
            $totalClasses += $total;
        }

        // Append student attendance data
        $attendanceData[] = [
            'reg_no' => $student['reg_no'],
            'name' => $student['name'],
            'attendance' => $studentAttendance,
            'total_present' => $totalPresent,
            'total_classes' => $totalClasses
        ];
    }

    // Final response
    echo json_encode([
        'subjects' => $subjects,
        'students' => $attendanceData
    ]);
} catch (Exception $e) {
    echo json_encode(['message' => 'Error fetching attendance data.', 'error' => $e->getMessage()]);
}
?>
