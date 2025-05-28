<?php
session_start();
if (isset($_SESSION['prof_email'])) {
    include('../endpoint/config.php'); // Include the database connection config

    // Check if the required data is sent via POST
    if (isset($_POST['feedback_id']) && isset($_POST['marks'])) {
        $feedback_id = $_POST['feedback_id'];
        $marks = $_POST['marks'];

        // Update the marks in the database
        $stmt = $conn->prepare("UPDATE eventFeedback SET status = ? WHERE feedback_id = ?");
        $stmt->bind_param("di", $marks, $feedback_id); // 'd' for decimal, 'i' for integer

        if ($stmt->execute()) {
            // If update is successful, return success response
            echo json_encode(['success' => true, 'message' => 'Marks updated successfully.']);
        } else {
            // If update fails, return error response
            echo json_encode(['success' => false, 'message' => 'Failed to update marks.']);
        }

        $stmt->close();
        $conn->close();
    } else {
        // Invalid request
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    }
} else {
    header('Location: ../login');
    exit;
}
?>