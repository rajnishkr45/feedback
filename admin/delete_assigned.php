<?php
// Include your database connection file
session_start();
if (isset($_SESSION['admin_email'])) {
    include '../endpoint/config.php';
    if (isset($_POST['id'])) {
        $assigned_class_id = $_POST['id'];

        // Prepare SQL delete query
        $sql = "DELETE FROM assigned_class WHERE id = ?";

        // Prepare the statement
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            echo "Error preparing statement: " . $conn->error;
            exit;
        }

        // Bind the assigned class ID
        $stmt->bind_param('i', $assigned_class_id);

        // Execute the statement
        if ($stmt->execute()) {
            echo "Record deleted successfully.";
        } else {
            echo "Error deleting record: " . $stmt->error; // Provide more detailed error message
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "No ID provided for deletion.";
    }

    // Close the database connection
    $conn->close();

    // Exit the script
    exit;
} else {
    header('Location: ../login');
    exit;
}
?>