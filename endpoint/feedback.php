<?php
// Database connection settings
include './config.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitize and validate inputs
    $name = trim(mysqli_real_escape_string($conn, $_POST['name']));
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $feedbackType = trim(mysqli_real_escape_string($conn, $_POST['feedbackType']));
    $message = trim(mysqli_real_escape_string($conn, $_POST['message']));

    // Basic validation
    if (empty($name) || empty($email) || empty($feedbackType) || empty($message)) {
        echo "error";
        exit;
    }

    // Insert data into the database
    $sql = "INSERT INTO feedback (name, email, feedback_type, message, submitted_at) 
            VALUES ('$name', '$email', '$feedbackType', '$message', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "success";
    } else {
        echo "error";
    }
}

// Close the connection
$conn->close();
?>