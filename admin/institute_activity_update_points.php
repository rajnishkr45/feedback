<?php
include 'admin_name.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activityId = intval($_POST['activity_id']);
    $points = intval($_POST['points']);

    if ($activityId && $points >= 0) {
        // Validate activity ID exists
        $checkQuery = "SELECT id FROM institute_activity WHERE id = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("i", $activityId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid activity ID.']);
            exit;
        }

        // Update points and status
        $query = "UPDATE institute_activity SET points = ?, status = 'Approved', updated_at = NOW() WHERE id = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            echo json_encode(['success' => false, 'message' => 'Database error: Unable to prepare statement.']);
            exit;
        }
        $stmt->bind_param("ii", $points, $activityId);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Points assigned successfully and status updated to Completed.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update points.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
