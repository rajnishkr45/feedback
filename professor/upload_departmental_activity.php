<?php
include '../endpoint/config.php';
include '../endpoint/academic_session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $professor_id = intval($_POST['professor_id']);
    $activity_name = trim($_POST['activity_name']);
    $details = trim($_POST['details']);

    // Get active session
    $session = getActiveSession($conn);
    if (!$session) {
        echo json_encode(['success' => false, 'message' => 'No active session found.']);
        exit;
    }
    $session_id = $session['session_id'];

    // Validate inputs
    if (empty($professor_id) || empty($activity_name) || empty($details)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    // Check if professor already uploaded 4 activities in this session
    $check = $conn->prepare("SELECT COUNT(*) as total FROM departmental_activities WHERE professor_id=? AND session_id=?");
    $check->bind_param("ii", $professor_id, $session_id);
    $check->execute();
    $uploaded = $check->get_result()->fetch_assoc()['total'];
    if ($uploaded >= 4) {
        echo json_encode(['success' => false, 'message' => '⚠️ You can upload only 4 activities per session.']);
        exit;
    }

    // Handle file upload
    $targetDir = "../uploads/departmental_activities/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $proofPath = NULL;
    if (!empty($_FILES['proof']['name'])) {
        $fileName = time() . "_" . basename($_FILES['proof']['name']);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileType, $allowedTypes)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file format. Allowed: JPG, PNG, GIF']);
            exit;
        }

        if ($_FILES['proof']['size'] > 250 * 1024) {
            echo json_encode(['success' => false, 'message' => 'File size must be under 250KB']);
            exit;
        }

        if (move_uploaded_file($_FILES['proof']['tmp_name'], $targetFile)) {
            $proofPath = "uploads/departmental_activities/" . $fileName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload proof.']);
            exit;
        }
    }

    // Insert into DB
    $stmt = $conn->prepare("INSERT INTO departmental_activities (professor_id, activity_name, details, proof, session_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("isssi", $professor_id, $activity_name, $details, $proofPath, $session_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => '✅ Activity uploaded successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error. Try again.']);
    }

    $stmt->close();
}
$conn->close();
?>
