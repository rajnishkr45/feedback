<?php
include '../endpoint/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $points = intval($_POST['points']);

    if ($points < 0 || $points > 5) {
        echo json_encode(['success' => false, 'message' => 'Points must be between 0 and 5.']);
        exit;
    }

    $stmt = $conn->prepare("UPDATE departmental_activities SET points=? WHERE id=?");
    $stmt->bind_param("ii", $points, $id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Points updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed.']);
    }
    $stmt->close();
}
?>
