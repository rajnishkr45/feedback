<?php

include 'std_name.php';
include '../endpoint/academic_session.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

// Get active academic session
$activeSession = getActiveSession($conn);
if (!$activeSession) {
    echo json_encode([
        'success' => false,
        'message' => '❌ No active academic session found. Please contact admin.'
    ]);
    exit;
}
$session_id = $activeSession['session_id'];

// Get student details
$student_email = $_SESSION['email'];
$student_query = $conn->prepare("SELECT id, semester, passing_year FROM students WHERE email = ?");
$student_query->bind_param("s", $student_email);
$student_query->execute();
$student_result = $student_query->get_result();

if ($student_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => '❌ Student not found.']);
    exit;
}

$student = $student_result->fetch_assoc();
$student_id = (int)$student['id'];
$student_semester = (int)$student['semester'];
$passing_year = $student['passing_year'];

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    if (
        empty($input['professor_id']) ||
        empty($input['semester']) ||
        empty($input['subject']) ||
        empty($input['ratings']) ||
        !is_array($input['ratings'])
    ) {
        echo json_encode(['success' => false, 'message' => '⚠️ Please fill all required fields.']);
        exit;
    }

    $professor_id = (int)$input['professor_id'];
    $semester = (int)$input['semester'];
    $subject_id = (int)$input['subject'];
    $ratings = $input['ratings'];

    // Validate each rating
    foreach ($ratings as $question_id => $value) {
        if (!is_numeric($question_id) || !is_numeric($value) || $value < 1 || $value > 10) {
            echo json_encode(['success' => false, 'message' => '❌ Invalid rating values detected.']);
            exit;
        }
    }

    $ratings_json = json_encode($ratings);

    // Check for duplicate submission
    $check = $conn->prepare("SELECT COUNT(*) FROM feedback_ratings WHERE student_id = ? AND professor_id = ? AND semester = ? AND subject_id = ? AND session_id = ?");
    $check->bind_param("iiiii", $student_id, $professor_id, $semester, $subject_id, $session_id);
    $check->execute();
    $check->bind_result($exists);
    $check->fetch();
    $check->close();

    if ($exists > 0) {
        echo json_encode(['success' => false, 'message' => '⚠️ Feedback already submitted for this professor and subject.']);
        exit;
    }

    // ✅ Insert feedback
    $insert = $conn->prepare("INSERT INTO feedback_ratings (student_id, professor_id, semester, passing_year, subject_id, ratings, session_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insert->bind_param("iiisisi", $student_id, $professor_id, $semester, $passing_year, $subject_id, $ratings_json, $session_id);

    if ($insert->execute()) {
        echo json_encode(['success' => true, 'message' => '✅ Feedback submitted successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => '❌ Failed to submit feedback. Please try again.']);
    }

    $insert->close();
} else {
    echo json_encode(['success' => false, 'message' => '❌ Invalid request method.']);
}
?>
