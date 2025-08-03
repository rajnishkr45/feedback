<?php
include '../endpoint/config.php';
include '../endpoint/academic_session.php'; // To get active session

$response = ['success' => false];

// ✅ Get Active Session
$activeSession = getActiveSession($conn);
if (!$activeSession) {
    $response['message'] = '❌ No active academic session. Contact admin.';
    echo json_encode($response);
    exit;
}
$session_id = $activeSession['session_id'];

$professor_id = $_POST['professor_id'] ?? null;
$scheduled_classes = $_POST['scheduled_classes'] ?? null;
$actual_classes = $_POST['actual_classes'] ?? null;

if ($professor_id && $scheduled_classes && $actual_classes) {

    // ✅ Check if professor already submitted for this session
    $check_query = "SELECT id FROM teaching_process WHERE professor_id = ? AND session_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $professor_id, $session_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // ❌ Already submitted → Block further submission
        $response['message'] = '⚠️ You have already submitted for this session. Only 1 submission is allowed.';
    } else {
        // ✅ Insert new contribution
        $insert_query = "INSERT INTO teaching_process (professor_id, session_id, scheduled_classes, actual_classes, contribution_date) 
                         VALUES (?, ?, ?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("iiii", $professor_id, $session_id, $scheduled_classes, $actual_classes);

        if ($insert_stmt->execute()) {
            $response['success'] = true;
            $response['message'] = '✅ Record saved successfully for this session.';
        } else {
            $response['message'] = '❌ Failed to save record.';
        }
        $insert_stmt->close();
    }
    $check_stmt->close();
} else {
    $response['message'] = '⚠️ Please fill in all fields.';
}

echo json_encode($response);
?>
