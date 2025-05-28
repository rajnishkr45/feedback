<?php
session_start();
if (isset($_SESSION['prof_email'])) {
    include '../endpoint/config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Adjusted to capture custom event name if 'Other' is selected
        $eventName = trim($_POST['event_name'] ?? '');
        $customEventName = trim($_POST['custom_event_name'] ?? '');
        $date = trim($_POST['date'] ?? '');
        $role = trim($_POST['role'] ?? '');
        $contribution = trim($_POST['contribution'] ?? '');
        $professor_id = trim($_POST['professor_id'] ?? '');

        // Determine the final event name
        if ($eventName === 'other' && !empty($customEventName)) {
            $eventName = $customEventName; // Use custom event name if 'Other' was selected
        }

        // Validate inputs
        if (empty($eventName) || empty($date) || empty($role) || empty($contribution) || empty($professor_id)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required.']);
            exit();
        }

        // Check if a file was uploaded
        if (isset($_FILES['proof_image']) && $_FILES['proof_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['proof_image']['tmp_name'];
            $fileName = $_FILES['proof_image']['name'];
            $fileSize = $_FILES['proof_image']['size'];
            $fileType = $_FILES['proof_image']['type'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // Allowed file types
            $allowedFileTypes = ['jpg', 'jpeg', 'png', 'gif'];

            // Validate file type and size
            if (!in_array($fileExtension, $allowedFileTypes)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.']);
                exit();
            }

            if ($fileSize > 250 * 1024) { // 250 KB
                echo json_encode(['success' => false, 'message' => 'File size exceeds 250KB!']);
                exit();
            }

            // Move the uploaded file to the desired directory
            $uploadFileDir = '../uploads/';
            $destPath = $uploadFileDir . basename($fileName);

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                // File uploaded successfully, now proceed with the database insertion
                // Use prepared statements to prevent SQL injection
                $stmt = $conn->prepare("INSERT INTO eventFeedback (event_name, event_date, role, contribution, professor_id, proof_image) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $eventName, $date, $role, $contribution, $professor_id, $fileName);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Database insertion failed!']);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'File upload failed!']);
            }
        } else {
            // No file was uploaded
            echo json_encode(['success' => false, 'message' => 'No file uploaded or file upload error!']);
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