<?php
include 'pro_name.php';

// Set the default time zone to Indian Standard Time (IST)
date_default_timezone_set('Asia/Kolkata');
$timedate = date("Y-m-d H:i:s");

$data = json_decode(file_get_contents('php://input'), true);
$subject_id = $data['subject_id'];
$attendance = $data['attendance'];
$semester = $data['semester'];

foreach ($attendance as $entry) {
    $student_id = $entry['student_id'];
    $status = $entry['status'];

    $query = "INSERT INTO attendance (professor_id, student_id, subject_id, semester, status, timestamp) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiiiss", $professor_id, $student_id, $subject_id, $semester, $status,$timedate);
    $stmt->execute();
}

echo json_encode(["success" => true]);
?>
