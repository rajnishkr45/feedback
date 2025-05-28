<?php
include 'admin_name.php';
header('Content-Type: application/json');

// Decode the JSON request
$data = json_decode(file_get_contents("php://input"), true);
$dept = $data['dept'] ?? '';
$semester = $data['semester'] ?? '';

// Check if the connection is established
if (!$conn) {
    echo json_encode(['error' => 'Database connection failed.']);
    exit();
}

// SQL query with optional filters
$sql = "SELECT id, name, email, branch, phone, reg_no, semester FROM students WHERE 1=1";
if ($dept !== '') {
    $sql .= " AND branch = ?";
}
if ($semester !== '') {
    $sql .= " AND semester = ?";
}

$stmt = $conn->prepare($sql);

// Check if the statement preparation was successful
if ($stmt === false) {
    echo json_encode(['error' => 'SQL statement preparation failed: ' . $conn->error]);
    exit();
}

// Bind parameters based on available filters
$params = [];
$types = ''; // Initialize a string for the types of parameters
if ($dept !== '') {
    $params[] = $dept;
    $types .= 's'; // Assuming branch is a string
}
if ($semester !== '') {
    $params[] = $semester;
    $types .= 's'; // Assuming semester is a string
}

// Bind parameters if any
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Fetch the results
$students = [];
while ($row = $result->fetch_assoc()) {
    $students[] = $row;
}

// Send the response as JSON
if (!empty($students)) {
    echo json_encode($students);
} else {
    echo json_encode(['message' => 'No students found.']);
}

// Close connection
$stmt->close();
$conn->close();
?>
