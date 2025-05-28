<?php
session_start();
if (isset($_SESSION['admin_email'])) {
    include '../endpoint/config.php';
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $professor_id = $_POST['professor_id'];
        $subject_id = $_POST['subject_id'];
        $branch = $_POST['branch'];
        $semester = $_POST['semester'];

        // Check if the subject is already assigned to another professor in the given branch and semester
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM assigned_class WHERE subject_id = ? AND assigned_branch = ? AND assigned_sem = ?");
        $checkStmt->bind_param("iss", $subject_id, $branch, $semester);
        $checkStmt->execute();
        $checkStmt->bind_result($count);
        $checkStmt->fetch();
        $checkStmt->close();

        if ($count > 0) {
            // If there's already a professor assigned to this subject, prevent the assignment
            echo json_encode(['success' => false, 'message' => 'This subject is already assigned to another professor in the specified branch and semester.']);
        } else {
            // If not, insert the new assignment
            $stmt = $conn->prepare("INSERT INTO assigned_class (professor_id, subject_id, assigned_branch, assigned_sem) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiss", $professor_id, $subject_id, $branch, $semester);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Class assigned successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to assign class!']);
            }

            $stmt->close();
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request!']);
    }

    $conn->close();
} else {
    header('Location: ../login');
    exit;
}
?>