<?php
include 'admin_name.php';

$session_id = intval($_POST['session_id']);
$output = [];

$profStmt = $conn->prepare("SELECT prof_id, name FROM professors");
$profStmt->execute();
$profResult = $profStmt->get_result();

while ($prof = $profResult->fetch_assoc()) {
    $pid = $prof['prof_id'];

    // 1️⃣ Student Feedback (25)
    $feedbackStmt = $conn->prepare("SELECT ratings FROM feedback_ratings WHERE professor_id=? AND session_id=?");
    $feedbackStmt->bind_param("ii", $pid, $session_id);
    $feedbackStmt->execute();
    $feedbackResult = $feedbackStmt->get_result();

    $studentAverages = [];
    while ($row = $feedbackResult->fetch_assoc()) {
        $ratings = json_decode($row['ratings'], true);
        if ($ratings && is_array($ratings)) {
            $avg = array_sum($ratings) / count($ratings);
            $studentAverages[] = $avg;
        }
    }
    $feedbackScore = 0;
    if (!empty($studentAverages)) {
        $finalAvg = array_sum($studentAverages) / count($studentAverages);
        $feedbackScore = ($finalAvg / 10) * 25;
    }

    // 2️⃣ Contribution to Society (Max 10 - Only 2 contributions allowed)
    $contribStmt = $conn->prepare("
    SELECT COALESCE(SUM(points), 0) AS total_points 
    FROM eventfeedback 
    WHERE professor_id = ? AND session_id = ?
    LIMIT 2
");
    $contribStmt->bind_param("ii", $pid, $session_id);
    $contribStmt->execute();
    $contribPoints = $contribStmt->get_result()->fetch_assoc()['total_points'] ?? 0;

    // Ensure maximum 10 points
    $contribScore = min($contribPoints, 10);


    // 3️⃣ Teaching Process (25)
    $tpStmt = $conn->prepare("SELECT scheduled_classes, actual_classes FROM teaching_process WHERE professor_id=? AND session_id=?");
    $tpStmt->bind_param("ii", $pid, $session_id);
    $tpStmt->execute();
    $tp = $tpStmt->get_result()->fetch_assoc();

    $tpScore = 0;
    if ($tp && $tp['scheduled_classes'] > 0) {
        $tpScore = ($tp['actual_classes'] / $tp['scheduled_classes']) * 25;
        if ($tpScore > 25)
            $tpScore = 25;
    }

    // 4️⃣ Institute Activity (10)
    $instStmt = $conn->prepare("SELECT COALESCE(SUM(points), 0) as pts FROM institute_activity WHERE professor_id=? AND session_id=?");
    $instStmt->bind_param("ii", $pid, $session_id);
    $instStmt->execute();
    $instScore = min($instStmt->get_result()->fetch_assoc()['pts'], 10);

    // 5️⃣ Department Activity (20)
    $deptStmt = $conn->prepare("SELECT COALESCE(SUM(points), 0) as pts FROM departmental_activities WHERE professor_id=? AND session_id=?");
    $deptStmt->bind_param("ii", $pid, $session_id);
    $deptStmt->execute();
    $deptScore = min($deptStmt->get_result()->fetch_assoc()['pts'], 20);

    // 🔹 Total out of 90
    $total90 = $feedbackScore + $contribScore + $tpScore + $instScore + $deptScore;

    // 🔹 ACR out of 10
    $acrScore = round(($total90 / 90) * 10, 2);
    if ($acrScore > 10)
        $acrScore = 10;

    // 🔹 Final Total out of 100
    $total = round($total90 + $acrScore, 2);
    if ($total > 100)
        $total = 100;

    $output[] = [
        'name' => $prof['name'],
        'feedback' => round($feedbackScore, 2),
        'contribution' => round($contribScore, 2),
        'teaching' => round($tpScore, 2),
        'institute' => round($instScore, 2),
        'department' => round($deptScore, 2),
        'acr' => $acrScore,
        'total' => $total
    ];
}

// 🔹 Sort by total score (highest first)
usort($output, function ($a, $b) {
    return $b['total'] <=> $a['total'];
});

echo json_encode(['success' => true, 'professors' => $output]);
?>