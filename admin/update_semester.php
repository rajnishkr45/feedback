<?php
include 'admin_name.php';
header('Content-Type: application/json');

$student_ids = $_POST['student_ids'] ?? [];
$new_semester = $_POST['new_semester'] ?? '';

if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

if (empty($student_ids) || $new_semester === '') {
    echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    exit();
}

// Create placeholders for student IDs (?, ?, ?, ...)
$placeholders = implode(',', array_fill(0, count($student_ids), '?'));
$sql = "UPDATE students SET semester = ? WHERE id IN ($placeholders)";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'SQL error: ' . $conn->error]);
    exit();
}

// Bind parameters (first semester, then student IDs)
$types = "i" . str_repeat("i", count($student_ids));
$params = array_merge([$new_semester], $student_ids);
$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Semester updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update semester.']);
}

$stmt->close();
$conn->close();
?>