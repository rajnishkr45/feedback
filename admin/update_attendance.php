<?php
// update_attendance.php

// Include the database connection
include '../endpoint/config.php';

// Check if required POST data exists
if (isset($_POST['id']) && isset($_POST['status'])) {
    $id = $_POST['id'];
    $status = $_POST['status'];

    // Validate input (optional, depending on your data)
    if (empty($id) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        exit;
    }

    // Prepare the update query
    $query = "UPDATE attendance SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database query error: ' . $conn->error]);
        exit;
    }

    // Bind parameters and execute the query
    $stmt->bind_param('si', $status, $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Attendance status updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating attendance: ' . $stmt->error]);
    }

    // Close the prepared statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Missing parameters.']);
}
