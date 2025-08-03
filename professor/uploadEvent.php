<?php
session_start();
if (isset($_SESSION['prof_email'])) {
    include '../endpoint/config.php';
    include '../endpoint/academic_session.php'; // ✅ Fetch active session

    // ✅ Get active session
    $activeSession = getActiveSession($conn);
    if (!$activeSession) {
        echo json_encode(['success' => false, 'message' => '❌ No active academic session. Contact admin.']);
        exit();
    }
    $session_id = $activeSession['session_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $eventName = trim($_POST['event_name'] ?? '');
        $customEventName = trim($_POST['custom_event_name'] ?? '');
        $date = trim($_POST['date'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $contribution = trim($_POST['contribution'] ?? '');
        $professor_id = trim($_POST['professor_id'] ?? '');

        if ($eventName === 'other' && !empty($customEventName)) {
            $eventName = $customEventName;
        }

        if (empty($eventName) || empty($date) || empty($role) || empty($contribution) || empty($professor_id)) {
            echo json_encode(['success' => false, 'message' => '⚠️ All fields are required.']);
            exit();
        }

        // ✅ Check if professor already uploaded 2 contributions in this session
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM eventfeedback WHERE professor_id=? AND session_id=?");
        $checkStmt->bind_param("ii", $professor_id, $session_id);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($count >= 2) {
            echo json_encode(['success' => false, 'message' => '⚠️ You already upload 2 contributions in this session. No more submission allowed ']);
            exit();
        }

        // ✅ File upload validation
        if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['proof_image']['tmp_name'];
            $fileName = $_FILES['proof_image']['name'];
            $fileSize = $_FILES['proof_image']['size'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedFileTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($fileExtension, $allowedFileTypes)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.']);
                exit();
            }

            if ($fileSize > 250 * 1024) {
                echo json_encode(['success' => false, 'message' => '⚠️ File size exceeds 250KB!']);
                exit();
            }

            $uploadFileDir = '../uploads/';
            $destPath = $uploadFileDir . basename($fileName);

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // ✅ Insert with session_id
                $stmt = $conn->prepare("INSERT INTO eventfeedback (event_name, event_date, role, contribution, professor_id, proof_image, session_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssi", $eventName, $date, $role, $contribution, $professor_id, $fileName, $session_id);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => '❌ Database insertion failed!']);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => '❌ File upload failed!']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => '⚠️ No file uploaded or file upload error!']);
        }

        mysqli_close($conn);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method!']);
    }
} else {
    header('Location: ../login');
    exit;
}
?>