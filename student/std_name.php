<?php
session_start();
if (isset($_SESSION['email'])) {
    include '../endpoint/config.php'; // Ensure this file has your DB connection details

    // Fetch student information based on logged-in email
    $student_email = $_SESSION['email'];
    $query = $conn->prepare("SELECT id, name,branch,semester,dp FROM students WHERE email = ?");
    $query->bind_param('s', $student_email);
    $query->execute();
    $result = $query->get_result();
    $student = $result->fetch_assoc();
    $student_id = $student['id'];
    $stdName = $student['name'];
    $stdSem = $student['semester'];
    $stdBranch = $student['branch'];
    $profilePicture = $student['dp'];
} else {
    header('Location: ../login');
    exit;
}
?>