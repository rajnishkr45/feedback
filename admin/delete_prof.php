<?php
session_start();
if (isset($_SESSION['admin_email'])) {
    include '../endpoint/config.php';
    if (isset($_POST['prof_id'])) {
        $prof_id = $_POST['prof_id'];

        // Prepare and execute the deletion query
        $stmt = $conn->prepare("DELETE FROM professors WHERE prof_id = ?");
        $stmt->bind_param("i", $prof_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete professor.']);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid professor ID.']);
    }
} else {
    header('Location: ../login');
    exit;
}
?>