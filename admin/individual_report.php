<?php
session_start();
include '../endpoint/config.php';

if (!isset($_GET['prof_id'])) {
    die("Invalid Request: Professor ID is required.");
}

$prof_id = $_GET['prof_id'];

// Fetch professor details
$sql = "SELECT * FROM professors WHERE prof_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $prof_id);
$stmt->execute();
$result = $stmt->get_result();
$professor = $result->fetch_assoc();

if (!$professor) {
    die("Professor not found.");
}

// Fetch all feedback questions
$question_sql = "SELECT question_id, question_text FROM feedback_questions";
$question_result = $conn->query($question_sql);
$questions = [];
while ($row = $question_result->fetch_assoc()) {
    $questions[$row['question_id']] = $row['question_text'];
}

// Fetch all ratings for the professor
$ratings_sql = "SELECT ratings FROM feedback_ratings WHERE professor_id = ?";
$stmt = $conn->prepare($ratings_sql);
$stmt->bind_param("i", $prof_id);
$stmt->execute();
$ratings_result = $stmt->get_result();

$ratings_data = [];
while ($row = $ratings_result->fetch_assoc()) {
    $ratings_json = json_decode($row['ratings'], true);
    foreach ($ratings_json as $question_id => $rating) {
        if (!isset($ratings_data[$question_id])) {
            $ratings_data[$question_id] = ['total' => 0, 'count' => 0];
        }
        $ratings_data[$question_id]['total'] += $rating;
        $ratings_data[$question_id]['count']++;
    }
}

// Calculate average rating per question
$avg_ratings = [];
foreach ($ratings_data as $question_id => $data) {
    $avg_ratings[$question_id] = round($data['total'] / $data['count'], 2);
}

// Contribution to society
$event_sql = "SELECT event_name, event_date, role, contribution, proof_image, status FROM eventfeedback WHERE professor_id = ?";
$stmt = $conn->prepare($event_sql);
$stmt->bind_param("i", $prof_id);
$stmt->execute();
$event_result = $stmt->get_result();


// Fetch Departmental Activities
$dept_sql = "SELECT activity_name, semester, points, max_points FROM departmental_activities WHERE professor_id = ?";
$stmt = $conn->prepare($dept_sql);
$stmt->bind_param("i", $prof_id);
$stmt->execute();
$dept_result = $stmt->get_result();


// Fetch Institute Activities
$inst_sql = "SELECT role, extra_info, status, points, image_proof FROM institute_activity WHERE professor_id = ?";
$stmt = $conn->prepare($inst_sql);
$stmt->bind_param("i", $prof_id);
$stmt->execute();
$inst_result = $stmt->get_result();


// Fetch Teaching Process Data
$teaching_sql = "SELECT tp.subject_id, s.subject_name, tp.scheduled_classes, tp.actual_classes, tp.contribution_date 
                 FROM teaching_process tp 
                 JOIN subjects s ON tp.subject_id = s.subject_id 
                 WHERE tp.professor_id = ?";

$stmt = $conn->prepare($teaching_sql);
$stmt->bind_param("i", $prof_id);
$stmt->execute();
$teaching_result = $stmt->get_result();


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professor Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        th{
            text-align: center;
        }
    </style>
</head>

<body>

    <div class="container mt-4">
        <h2 class="text-center">Professor Report</h2>
        <!-- Professor details -->
        <div class="card mt-3">
            <div class="card-header bg-primary text-white">
                <h4><?php echo htmlspecialchars($professor['name']); ?></h4>
            </div>
            <div class="card-body">
                <p><strong>Department:</strong> <?php echo htmlspecialchars($professor['dept']); ?></p>
                <p><strong>Role:</strong> <?php echo htmlspecialchars($professor['role']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($professor['email']); ?></p>
            </div>
        </div>

        <!-- Student Feedback -->
        <div class="card mt-3">
            <div class="card-header bg-info text-white">
                <h5>Student Feedback</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($avg_ratings)): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>SN.</th>
                                <th>Question</th>
                                <th>Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $serialNo = 0;
                            $total_rating = 0;
                            $num_questions = count($avg_ratings);
                            foreach ($avg_ratings as $question_id => $avg):
                                $total_rating += $avg;
                                $serialNo++;
                                ?>
                                <tr>
                                    <td><?php echo $serialNo ?></td>
                                    <td><?php echo htmlspecialchars($questions[$question_id] ?? 'Unknown Question'); ?></td>
                                    <td><?php echo $avg; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total point out of 25 points based on student feedback</th>
                                <th>
                                    <?php
                                    $overall_rating = ($num_questions > 0) ? round(($total_rating / $num_questions) * 2.5, 2) : 0;
                                    echo $overall_rating;
                                    ?>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                <?php else: ?>
                    <p>No Student feedback ratings available.</p>
                <?php endif; ?>
            </div>
        </div>

        <br><br><br>

        <!-- Teaching Process -->
        <div class="card mt-3">
            <div class="card-header bg-warning text-white">
                <h5>Teaching Process</h5>
            </div>
            <div class="card-body">
                <?php if ($teaching_result->num_rows > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Subject Name</th>
                                <th>Scheduled Classes</th>
                                <th>Actual Classes</th>
                                <th>Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $teaching_result->fetch_assoc()): ?>
                                <?php
                                $efficiency = ($row['scheduled_classes'] > 0)
                                    ? round(($row['actual_classes'] / $row['scheduled_classes']) * 25, 2)
                                    : 0;
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['scheduled_classes']); ?></td>
                                    <td><?php echo htmlspecialchars($row['actual_classes']); ?></td>
                                    <td><?php echo $efficiency; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No teaching process data available.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contribution to Sociebty Feedback -->
        <div class="card mt-3">
            <div class="card-header bg-success text-white">
                <h5>Contribution to Society</h5>
            </div>
            <div class="card-body">
                <?php if ($event_result->num_rows > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Event Name</th>
                                <th>Date</th>
                                <th>Role</th>
                                <th>Contribution</th>
                                <th>Status</th>
                                <th>Proof</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $event_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['event_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['event_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                                    <td><?php echo htmlspecialchars($row['contribution']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td>
                                        <?php if ($row['proof_image']): ?>
                                            <a href="../uploads/<?php echo $row['proof_image']; ?>" target="_blank">View</a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No event feedback available.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Departmental Activities -->
        <div class="card mt-3">
            <div class="card-header bg-secondary text-white">
                <h5>Departmental Activities</h5>
            </div>
            <div class="card-body">
                <?php if ($dept_result->num_rows > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Activity Name</th>
                                <th>Semester</th>
                                <th>Points</th>
                                <th>Max Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $dept_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['activity_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['semester']); ?></td>
                                    <td><?php echo htmlspecialchars($row['points']); ?></td>
                                    <td><?php echo htmlspecialchars($row['max_points']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No departmental activities available.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Institute Activities -->
        <div class="card mt-3">
            <div class="card-header bg-danger text-white">
                <h5>Institute Activities</h5>
            </div>
            <div class="card-body">
                <?php if ($inst_result->num_rows > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Extra Info</th>
                                <th>Status</th>
                                <th>Points</th>
                                <th>Proof</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $inst_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                                    <td><?php echo htmlspecialchars($row['extra_info']); ?></td>
                                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                                    <td><?php echo htmlspecialchars($row['points']); ?></td>
                                    <td>
                                        <?php if ($row['image_proof']): ?>
                                            <a href="../uploads/<?php echo $row['image_proof']; ?>" target="_blank">View</a>
                                        <?php else: ?>
                                            N/A
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No institute activities available.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Download Button -->
        <div class="mt-3 text-center">
            <button onclick="downloadReport()" class="btn btn-dark">Download Report</button>
        </div>
    </div>
    <br><br>
    <script>
        function downloadReport() {
            window.print();
        }

    </script>
</body>

</html>