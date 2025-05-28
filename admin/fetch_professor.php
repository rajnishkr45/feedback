<?php
// Database connection
session_start();
if (isset($_SESSION['admin_email'])) {

    include '../endpoint/config.php';
    // Fetch professors from the `professors` table
    $query = "SELECT prof_id, name FROM professors";
    $result = $conn->query($query);

    $professors = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $professors[] = $row;
        }
    }

    // Return the data as JSON
    header('Content-Type: application/json');
    echo json_encode($professors);
    $conn->close();
} else {
    header('Location: ../login');
    exit;
}
?>