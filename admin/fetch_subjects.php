<?php
session_start();
if (isset($_SESSION['admin_email'])) {

    // Database connection
    include '../endpoint/config.php';

    $semester = $_GET['semester'];
    $branch = $_GET['branch'];

    // Fetch subjects for the selected semester and branch
    $query = $conn->prepare("SELECT subject_id, subject_name FROM subjects WHERE semester = ? AND branch = ?");
    $query->bind_param("is", $semester, $branch);
    $query->execute();
    $result = $query->get_result();

    $subjects = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $subjects[] = $row;
        }
    }

    // Return the data as JSON
    header('Content-Type: application/json');
    echo json_encode($subjects);

    $conn->close();
} else {
    header('Location: ../login');
    exit;
}
?>