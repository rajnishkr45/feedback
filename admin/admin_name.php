<?php
session_start();
if (isset($_SESSION['admin_email'])) {
    include '../endpoint/config.php'; // Ensure this file has your DB connection details

    // Fetch admin information based on logged-in email
    $admin_email = $_SESSION['admin_email'];
    $query = $conn->prepare("SELECT admin_id, name, dp FROM admins WHERE email = ?");
    $query->bind_param('s', $admin_email);
    $query->execute();
    $result = $query->get_result();
    $admin = $result->fetch_assoc();
    $admin_id = $admin['admin_id'];
    $profName = $admin['name'];
    $profilePicture = $admin['dp'];
} else {
    header('Location: ../login');
    exit;
}
?>