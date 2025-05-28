<?php
include '../endpoint/config.php';

// Get parameters from the request
$professor_id = $_GET['professor_id'];
$semester = $_GET['semester'];
$branch = $_GET['branch'];

// Check if all required parameters are provided
if (!$professor_id || !$semester || !$branch) {
    echo json_encode(["error" => "Missing parameters"]);
    exit;
}

// Fetch subjects based on professor ID, semester, and branch
$query = "SELECT s.subject_id, s.subject_name 
          FROM subjects s
          JOIN assigned_class ac ON s.subject_id = ac.subject_id
          WHERE ac.professor_id = ? AND s.semester = ? AND s.branch = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iis", $professor_id, $semester, $branch);
$stmt->execute();
$result = $stmt->get_result();

$subjects = [];
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}

// If no subjects are found, return a message
if (empty($subjects)) {
    echo json_encode(["message" => "No subjects found for the selected criteria."]);
    exit;
}

echo json_encode($subjects);
?>