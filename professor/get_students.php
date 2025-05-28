<?php
include '../endpoint/config.php';

// Get parameters from the request
$semester = $_GET['semester'];
$branch = $_GET['branch'];
$subject = $_GET['subject'];

// Check if all required parameters are provided
if (!$semester || !$branch || !$subject) {
    echo json_encode(["error" => "Missing parameters"]);
    exit;
}

// Fetch students based on semester, branch, and subject
$query = "SELECT id, name, reg_no 
          FROM students 
          WHERE semester = ? AND branch = ?
          ORDER BY reg_no ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $semester, $branch);
$stmt->execute();
$result = $stmt->get_result();

$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// If no students are found, return a message
if (empty($students)) {
    echo json_encode(["message" => "No students found for the selected criteria."]);
    exit;
}

echo json_encode($students);
?>