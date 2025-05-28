<?php
session_start();
if (isset($_SESSION['admin_email'])) {

    include '../endpoint/config.php';

    // Get the selected department
    $dept_search = isset($_GET['dept']) ? $_GET['dept'] : '';

    // Fetch professors
    $sql = "
        SELECT p.prof_id, p.name, p.email, p.phone, p.password, p.role, p.dept
        FROM professors p
    ";

    if (!empty($dept_search)) {
        $sql .= " WHERE p.dept = ? ";
    }

    $stmt = $conn->prepare($sql);
    if (!empty($dept_search)) {
        $stmt->bind_param("s", $dept_search);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $professors_data = [];
    while ($row = $result->fetch_assoc()) {
        $professors_data[$row['prof_id']] = $row;
    }

    if (empty($professors_data)) {
        echo "<tr><td colspan='6'>No professors found</td></tr>";
        exit;
    }

    $professor_ids = array_keys($professors_data);
    $prof_ids_string = implode(',', array_map('intval', $professor_ids));

    // Fetch feedback ratings stored in JSON format
    $rating_sql = "SELECT professor_id, ratings FROM feedback_ratings WHERE professor_id IN ($prof_ids_string)";
    $rating_result = $conn->query($rating_sql);

    $rating_data = [];
    while ($rating_row = $rating_result->fetch_assoc()) {
        $prof_id = $rating_row['professor_id'];
        $ratings_json = $rating_row['ratings'];

        // Decode JSON ratings and calculate average
        $ratings_array = json_decode($ratings_json, true);
        if (is_array($ratings_array) && count($ratings_array) > 0) {
            $average_rating = array_sum($ratings_array) / count($ratings_array);
        } else {
            $average_rating = 0; // No ratings available
        }

        // Convert scale (assuming original ratings are out of 10)
        $rating_data[$prof_id] = round($average_rating * 2.5, 2);
    }

    // Fetch event feedback data
    $event_sql = "
        SELECT professor_id, AVG(status) AS avg_status
        FROM (
            SELECT professor_id, status, 
                   ROW_NUMBER() OVER (PARTITION BY professor_id ORDER BY status DESC) AS rn
            FROM eventFeedback
            WHERE professor_id IN ($prof_ids_string)
        ) AS ranked_statuses
        WHERE rn <= 2
        GROUP BY professor_id
    ";
    $event_result = $conn->query($event_sql);

    $status_data = [];
    while ($event_row = $event_result->fetch_assoc()) {
        $status_data[$event_row['professor_id']] = $event_row['avg_status'];
    }

    // Fetch teaching process data
    $teaching_sql = "
        SELECT professor_id, scheduled_classes, actual_classes 
        FROM teaching_process 
        WHERE professor_id IN ($prof_ids_string)
    ";
    $teaching_result = $conn->query($teaching_sql);

    $teaching_points_data = [];
    while ($teaching_row = $teaching_result->fetch_assoc()) {
        $teaching_points = ($teaching_row['actual_classes'] / max($teaching_row['scheduled_classes'], 1)) * 25;
        $teaching_points_data[$teaching_row['professor_id']] = round($teaching_points, 2);
    }

    // Calculate final scores
    $professors_with_points = [];
    foreach ($professors_data as $prof_id => $prof) {
        $average_rating = isset($rating_data[$prof_id]) ? $rating_data[$prof_id] : 0;
        $avg_status = isset($status_data[$prof_id]) ? $status_data[$prof_id] : 0;
        $teaching_points = isset($teaching_points_data[$prof_id]) ? $teaching_points_data[$prof_id] : 0;

        $final_points = $average_rating + $avg_status + $teaching_points;

        $professors_with_points[] = [
            'prof_id' => $prof['prof_id'],
            'name' => $prof['name'],
            'phone' => $prof['phone'],
            'dept' => $prof['dept'],
            'role' => $prof['role'],
            'final_points' => $final_points
        ];
    }

    // Sort by final points
    usort($professors_with_points, function ($a, $b) {
        return $b['final_points'] <=> $a['final_points'];
    });

    // Output results
    $serial = 1;
    foreach ($professors_with_points as $serial => $prof) {
        echo "<tr>
            <td>$serial</td>
            <td><span class='prof-name'>" . htmlspecialchars($prof['name']) . "</span></td>
            <td><span class='prof-dept'>" . htmlspecialchars($prof['dept']) . "</span></td>
            <td><span class='prof-role'>" . htmlspecialchars($prof['role']) . "</span></td>
            <td><span class='prof-rating'>" . ($prof['final_points'] == 0 ? 0 : round($prof['final_points'], 2)) . "</span></td>
            <td><a href='individual_report.php?prof_id=" . htmlspecialchars($prof['prof_id']) . "' class='btn btn-info'>View Details</a></td>
        </tr>";

        $serial++;
    }

    $conn->close();
} else {
    header('Location: ../login');
    exit;
}
?>