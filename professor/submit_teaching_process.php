<?php
include 'db_connection.php';

$professor_id = $_POST['professor_id'];
$subject_id = $_POST['subject_id'];
$scheduled_classes = $_POST['scheduled_classes'];
$actual_classes = $_POST['actual_classes'];
$response = ['success' => false];

if ($professor_id && $subject_id && $scheduled_classes && $actual_classes) {
    // Check if the entry already exists for this professor and subject
    $check_query = "SELECT id FROM teaching_process WHERE professor_id = ? AND subject_id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ii", $professor_id, $subject_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        // If entry exists, update the contribution data
        $update_query = "UPDATE teaching_process SET scheduled_classes = ?, actual_classes = ?, contribution_date = NOW() 
                         WHERE professor_id = ? AND subject_id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("iiii", $scheduled_classes, $actual_classes, $professor_id, $subject_id);

        if ($update_stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Contribution updated successfully.';
        } else {
            $response['message'] = 'Failed to update contribution.';
        }
        $update_stmt->close();
    } else {
        // If entry doesn't exist, insert a new contribution record
        $insert_query = "INSERT INTO teaching_process (professor_id, subject_id, scheduled_classes, actual_classes, contribution_date) 
                         VALUES (?, ?, ?, ?, NOW())";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("iiii", $professor_id, $subject_id, $scheduled_classes, $actual_classes);

        if ($insert_stmt->execute()) {
            $response['success'] = true;
            $response['message'] = 'Contribution recorded successfully.';
        } else {
            $response['message'] = 'Failed to save contribution.';
        }
        $insert_stmt->close();
    }
    $check_stmt->close();
} else {
    $response['message'] = 'Please fill in all fields.';
}

echo json_encode($response);
?>