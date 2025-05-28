<?php
session_start();
include '../endpoint/config.php';
if (isset($_SESSION['admin_email'])) {
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security

    // Check if the email already exists
    $sql = "SELECT `admin_id` FROM `admins` WHERE `email` = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Email already exists
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Email already exists!',
            });
        </script>";
    } else {
        // Insert new admin
        $sql = "INSERT INTO `admins` (`name`, `email`, `phone`, `password`) VALUES ('$name', '$email', '$phone', '$password')";

        if ($conn->query($sql) === TRUE) {
            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Admin added successfully!',
                }).then(function() {
                    window.location = 'admin_list.php'; // Redirect after success
                });
            </script>";
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error adding admin: " . $conn->error . "',
                });
            </script>";
        }
    }

    $conn->close();
}}else{
    header('Location: ../login');
    exit;
}
?>