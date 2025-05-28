<?php
session_start();
if (isset($_SESSION['email'])) {
    include '../endpoint/config.php';
    // Retrieve parameters from GET request
    $subject = $_GET['subject_id'];
    $semester = $_GET['semester'];
    $branch = $_GET['branch']; // Add branch parameter

    // Fetch professors based on the subject, semester, and branch
    $professors = $conn->query("SELECT p.prof_id, p.name 
                             FROM professors p
                             JOIN assigned_class a ON p.prof_id = a.professor_id
                             WHERE a.subject_id = '$subject' 
                             AND a.assigned_sem = '$semester' 
                             AND a.assigned_branch = '$branch'");

    // Prepare options for the select dropdown
    $options = '';
    if ($professors->num_rows > 0) {
        while ($row = $professors->fetch_assoc()) {
            $options .= '<option value="' . $row['prof_id'] . '">' . $row['name'] . '</option>';
        }
    } else {
        $options .= '<option value="" selected disabled>No professors assigned</option>'; // Message for no available professors
    }

    // Output the options
    echo $options;
} else {
    header('Location: ../login');
    exit;
}
?>