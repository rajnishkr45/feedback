<?php
// Include the database configuration
include '../endpoint/config.php';

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize the input data
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0; // Ensure the ID is an integer
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $reg_no = isset($_POST['reg_no']) ? trim($_POST['reg_no']) : '';
    $semester = isset($_POST['semester']) ? intval($_POST['semester']) : 0; // Ensure it's an integer
    $branch = isset($_POST['branch']) ? trim($_POST['branch']) : ''; // Department/branch
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';

    // Debugging output: Check the value of branch
    error_log("Branch: " . $branch); // Log branch value to the server's error log

    // Validate the data (you can add more validation as needed)
    if ($id > 0 && !empty($name) && !empty($reg_no) && !empty($branch) && filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/^[0-9]{10}$/', $phone)) {
        // Prepare the SQL statement
        $stmt = $conn->prepare("UPDATE students SET name = ?, reg_no = ?, semester = ?, branch = ?, email = ?, phone = ? WHERE id = ?");
        $stmt->bind_param("ssisssi", $name, $reg_no, $semester, $branch, $email, $phone, $id);

        // Execute the statement
        if ($stmt->execute()) {
            // Respond with success
            echo json_encode(['status' => 'success', 'message' => 'Student updated successfully.']);
        } else {
            // Respond with error
            echo json_encode(['status' => 'error', 'message' => 'Failed to update student.']);
        }

        // Close the statement
        $stmt->close();
    } else {
        // Invalid input
        echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
    }
} else {
    // If not a POST request, return an error
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

// Close the database connection
$conn->close();
?>
