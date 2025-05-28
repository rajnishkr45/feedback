<?php
include '../endpoint/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignment_id = $_POST['assignment_id'];
    $targetDir = "../uploads/proofs/";

    // Check if file is uploaded
    if (!empty($_FILES['image_proof']['name'])) {
        $fileName = basename($_FILES['image_proof']['name']);
        $targetFilePath = $targetDir . $assignment_id . "_" . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Allow certain file formats
        $allowedTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array($fileType, $allowedTypes)) {
            // Upload file to server
            if (move_uploaded_file($_FILES['image_proof']['tmp_name'], $targetFilePath)) {
                // Update database with the proof image path and change status to "Completed"
                $query = "UPDATE institute_activity SET image_proof = ?, status = 'Completed' WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("si", $targetFilePath, $assignment_id);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Proof image submitted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Database update failed']);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'File upload failed']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid file format']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    }
}
$conn->close();
?>
