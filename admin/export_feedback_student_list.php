<?php
include 'admin_name.php';

$semester = isset($_GET['semester']) ? $_GET['semester'] : '';
$passing_year = isset($_GET['passing_year']) ? $_GET['passing_year'] : '';

$sql = "SELECT DISTINCT s.id, s.name, s.reg_no, s.branch, f.semester, f.passing_year 
        FROM feedback_ratings f
        JOIN students s ON f.student_id = s.id
        WHERE 1";

if (!empty($semester)) {
    $sql .= " AND f.semester = '$semester'";
}
if (!empty($passing_year)) {
    $sql .= " AND f.passing_year = '$passing_year'";
}

$sql .= " ORDER BY s.reg_no ASC"; // Sorting by Registration Number

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $filename = "student_feedback_list.csv";

    // Set headers to download file as CSV
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    // Open output stream
    $output = fopen('php://output', 'w');

    // Set column headers
    fputcsv($output, array('Student Name', 'Registration No.', 'Branch', 'Semester', 'Passing Year'));

    // Fetch and write data
    $serial = 1;
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, array(
            $serial,
            $row['name'],
            $row['reg_no'],
            $row['branch'],
            $row['semester'],
            $row['passing_year']
        ));
        $serial++;
    }

    fclose($output);
    exit();
} else {
    echo "No data found for the selected criteria.";
}

$conn->close();
?>