<?php
session_start();
if (isset($_SESSION['prof_email'])) {
    include '../endpoint/config.php'; // Ensure this file has your DB connection details

    // Fetch professor information based on logged-in email
    $professor_email = $_SESSION['prof_email'];
    $query = $conn->prepare("SELECT prof_id, name,role, dp FROM professors WHERE email = ?");
    $query->bind_param('s', $professor_email);
    $query->execute();
    $result = $query->get_result();
    $professor = $result->fetch_assoc();
    $professor_id = $professor['prof_id'];
    $profName = $professor['name'];
    $profRole = $professor['role'];
    $profilePicture = $professor['dp'];
} else {
    header('Location: ../login');
    exit;
}
?>