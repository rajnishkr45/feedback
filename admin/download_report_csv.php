<?php
include 'admin_name.php'; // Include your database connection

header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=professors_report.csv');

// Open output stream
$output = fopen('php://output', 'w');

// Output the column headings for the CSV
fputcsv($output, ['Rank', 'Name', 'Phone', 'Department', 'Role', 'Final Points']);

// Get department filter if available
$dept_search = isset($_GET['dept']) ? $_GET['dept'] : '';

// Fetch professors
$sql = "SELECT p.prof_id, p.name, p.phone, p.dept, p.role FROM professors p";
if (!empty($dept_search)) {
    $sql .= " WHERE p.dept = ?";
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
    fclose($output);
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

    // Decode JSON and calculate average
    $ratings_array = json_decode($ratings_json, true);
    if (is_array($ratings_array) && count($ratings_array) > 0) {
        $average_rating = array_sum($ratings_array) / count($ratings_array);
    } else {
        $average_rating = 0; // No ratings available
    }

    // Convert scale (assuming original ratings are out of 10)
    $rating_data[$prof_id] = round($average_rating * 2.5, 2);
}

// Fetch the top 2 statuses for each professor from eventFeedback
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

// Fetch teaching process data and calculate points
$teaching_sql = "
    SELECT professor_id, scheduled_classes, actual_classes 
    FROM teaching_process 
    WHERE professor_id IN ($prof_ids_string)
";

$teaching_result = $conn->query($teaching_sql);
$teaching_points_data = [];
while ($teaching_row = $teaching_result->fetch_assoc()) {
    if ($teaching_row['scheduled_classes'] > 0) {
        $teaching_points = ($teaching_row['actual_classes'] / $teaching_row['scheduled_classes']) * 25;
    } else {
        $teaching_points = 0;
    }
    $teaching_points_data[$teaching_row['professor_id']] = round($teaching_points, 2);
}

// Prepare an array to store professors with their final points
$professors_with_points = [];

foreach ($professors_data as $prof_id => $prof) {
    // Get the average rating
    $average_rating = isset($rating_data[$prof_id]) ? $rating_data[$prof_id] : 0;

    // Get the average status for this professor (if available)
    $avg_status = isset($status_data[$prof_id]) ? $status_data[$prof_id] : 0;

    // Get the teaching points for this professor (if available)
    $teaching_points = isset($teaching_points_data[$prof_id]) ? $teaching_points_data[$prof_id] : 0;

    // Calculate final points
    $final_points = $average_rating + $avg_status + $teaching_points;

    // Store professor data with final points
    $professors_with_points[] = [
        'prof_id' => $prof['prof_id'],
        'name' => $prof['name'],
        'phone' => $prof['phone'],
        'dept' => $prof['dept'],
        'role' => $prof['role'],
        'final_points' => $final_points
    ];
}

// Sort the professors array by final points in descending order to determine ranking
usort($professors_with_points, function ($a, $b) {
    return $b['final_points'] <=> $a['final_points']; // Descending order
});

// Output each row of data as CSV
$rank = 1;
foreach ($professors_with_points as $prof) {
    fputcsv($output, [
        $rank,
        $prof['name'],
        $prof['phone'],
        $prof['dept'],
        $prof['role'],
        $prof['final_points'] == 0 ? 'N/A' : round($prof['final_points'], 2)
    ]);
    $rank++;
}

// Close the output stream
fclose($output);
$conn->close();
exit();
