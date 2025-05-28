<?php
session_start();
if (isset($_SESSION['prof_email'])) {
    include('../endpoint/config.php');

    // Check if form data is sent
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $currentPassword = $_POST['currentPassword'];
        $newPassword = $_POST['newPassword'];

        // Assuming you have a function to check the current password and update it
        $professorEmail = $_SESSION['prof_email'];

        // Check if current password is correct
        $sql = "SELECT password FROM professors WHERE email = '$professorEmail'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($currentPassword, $row['password'])) {
                // Update the password
                $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateSql = "UPDATE professors SET password = '$hashedNewPassword' WHERE email = '$professorEmail'";
                if ($conn->query($updateSql) === TRUE) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false]);
                }
            } else {
                echo json_encode(['success' => false]);
            }
        } else {
            echo json_encode(['success' => false]);
        }
    }
} else {
    header('Location: ../login');
    exit;
}
?>