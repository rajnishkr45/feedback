<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: ../login');
    exit;
}

include '../endpoint/config.php';
$response = ['success' => false, 'message' => ''];

$student_email = $_SESSION['email'];
$student_data = $conn->query("SELECT id, passing_year, semester FROM students WHERE email = '$student_email'")->fetch_assoc();

if ($student_data) {
    $student_id = $student_data['id'];
    $student_semester = $student_data['semester'];
    $passing_year = $student_data['passing_year'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Get JSON input from frontend
        $input_data = json_decode(file_get_contents("php://input"), true);

        if (!empty($input_data['professor_id']) && !empty($input_data['semester']) && !empty($input_data['subject']) && !empty($input_data['ratings'])) {
            $professor_id = $input_data['professor_id'];
            $semester = $input_data['semester'];
            $subject = $input_data['subject'];
            $passing_year = $student_data['passing_year'];
            $ratings = json_encode($input_data['ratings']); // Convert array to JSON string

            // Check if feedback already exists
            $check_stmt = $conn->prepare("SELECT COUNT(*) FROM feedback_ratings WHERE student_id = ? AND professor_id = ? AND semester = ? AND subject_id = ?");
            $check_stmt->bind_param('iiii', $student_id, $professor_id, $semester, $subject);
            $check_stmt->execute();
            $check_stmt->bind_result($count);
            $check_stmt->fetch();
            $check_stmt->close();

            if ($count > 0) {
                $response['message'] = "You have already given feedback to this professor in this semester.";
            } else {
                // Insert feedback as JSON
                $stmt = $conn->prepare("INSERT INTO feedback_ratings (student_id, professor_id, semester, passing_year, subject_id, ratings) VALUES (?, ?, ?,?, ?, ?)");
                $stmt->bind_param('iiiiss', $student_id, $professor_id, $semester, $passing_year, $subject, $ratings);

                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Your feedback has been submitted successfully.';
                } else {
                    $response['message'] = 'Error submitting feedback.';
                }
                $stmt->close();
            }
        } else {
            $response['message'] = "All required fields are missing.";
        }
    }
} else {
    $response['message'] = "Student not found.";
}

echo json_encode($response);
?>