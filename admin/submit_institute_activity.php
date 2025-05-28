<?php
include '../endpoint/config.php';

// Check if the form data is received
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $professor_id = $_POST['professor_id'];
    $role = $_POST['role'];
    $extra_info = $_POST['extra'];

    // Validate input
    if (empty($professor_id) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
        exit;
    }

    // Insert task into the database
    $stmt = $conn->prepare("INSERT INTO institute_activity (professor_id, role, extra_info) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $professor_id, $role, $extra_info);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Task assigned successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to assign task. Please try again.']);
    }

    $stmt->close();
}
?>