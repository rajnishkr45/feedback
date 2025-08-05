<?php
include 'admin_name.php';
include '../endpoint/academic_session.php'; // ✅ Get $current_session_id


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $professor_id = intval($_POST['professor_id'] ?? 0);
    $role = trim($_POST['role'] ?? '');
    $extra_info = trim($_POST['extra'] ?? '');

    if ($professor_id <= 0 || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
        exit;
    }

    // ✅ Check how many tasks are already assigned in this session
    $checkStmt = $conn->prepare("SELECT COUNT(*) AS total FROM institute_activity 
                                WHERE professor_id = ? AND session_id = ?");
    $checkStmt->bind_param("ii", $professor_id, $current_session_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result()->fetch_assoc();
    $checkStmt->close();

    if ($result['total'] >= 2) {
        echo json_encode(['success' => false, 'message' => '⚠️ This professor already has 2 institute activities assigned for this session.']);
        exit;
    }

    // ✅ Assign the new task
    $stmt = $conn->prepare("INSERT INTO institute_activity (professor_id, role, extra_info, session_id) 
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $professor_id, $role, $extra_info, $current_session_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => '✅ Task assigned successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => '❌ Failed to assign task.']);
    }

    $stmt->close();
}
?>
