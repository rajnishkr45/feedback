<?php
include 'admin_name.php'; // Include your database connection and other necessary files


// Variables to hold search results
$student_data = null;
$feedback_data = [];

// Check if a search request is made
if (isset($_GET['reg_no'])) {
    $reg_no = $_GET['reg_no'];

    // Fetch student data by registration number
    $student_query = $conn->prepare("SELECT * FROM `students` WHERE `reg_no` = ?");
    $student_query->bind_param("s", $reg_no);
    $student_query->execute();
    $student_result = $student_query->get_result();

    if ($student_result->num_rows > 0) {
        $student_data = $student_result->fetch_assoc();

        // Fetch unique feedback data for the student
        $feedback_query = $conn->prepare("
            SELECT DISTINCT fr.semester, p.name AS professor_name, p.dept, s.subject_name 
            FROM `feedback_ratings` fr
            JOIN `professors` p ON fr.professor_id = p.prof_id
            JOIN `subjects` s ON fr.subject_id = s.subject_id
            WHERE fr.student_id = ?
        ");
        $feedback_query->bind_param("i", $student_data['id']);
        $feedback_query->execute();
        $feedback_result = $feedback_query->get_result();

        while ($row = $feedback_result->fetch_assoc()) {
            $feedback_data[] = $row;
        }
    } else {
        $error_message = "No student found with registration number $reg_no.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCE + AICTE || Student Status</title>
    <style>
        .search-form {
            margin: 20px auto;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .search-form input,
        .search-form button {
            padding: 7px 5px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .search-form input {
            flex: 1;
            min-width: 150px;
            max-width: 175px;
        }

        .search-form button {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .search-form button:hover {
            background-color: #0056b3;
        }

        /* Results Section */
        .results {
            width: 100%;
            max-width: 800px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .results h2 {
            margin-top: 0;
        }

        /* Table Styles */
        .feedback-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .feedback-table th,
        .feedback-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .feedback-table th {
            background-color: #f4f4f9;
        }

        /* Responsive Design */
        @media (max-width: 768px) {

            .feedback-table,
            .feedback-table thead,
            .feedback-table tbody,
            .feedback-table th,
            .feedback-table td,
            .feedback-table tr {
                display: block;
            }

            .feedback-table tr {
                margin-bottom: 15px;
            }

            .feedback-table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }

            .feedback-table td::before {
                content: attr(data-label);
                position: absolute;
                left: 0;
                width: 50%;
                padding-left: 10px;
                text-align: left;
                font-weight: bold;
            }

            .feedback-table th {
                display: none;
            }
        }
    </style>

    <?php
    include './dependencies.php';
    ?>

<body>
    <!-- SIDEBAR -->
    <?php
    include 'navbar.php';
    ?>

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu'></i>
            <form class="search-form" method="GET" action="">
                <input type="text" name="reg_no" placeholder="Enter Reg. No"
                    value="<?= htmlspecialchars($_GET['reg_no'] ?? '') ?>" required>
                <button type="submit">Search</button>
            </form>
        </nav>

        <main>

            <!-- Results -->
            <?php if (isset($error_message)): ?>
                <p><?= htmlspecialchars($error_message) ?></p>
            <?php elseif ($student_data): ?>
                <div class="results">
                    <h2>Student Details</h2>
                    <p><strong>Name:</strong> <?= htmlspecialchars($student_data['name']) ?></p>
                    <p><strong>Registration Number:</strong> <?= htmlspecialchars($student_data['reg_no']) ?></p>
                    <p><strong>Branch:</strong> <?= htmlspecialchars($student_data['branch']) ?></p>
                    <p><strong>Semester:</strong> <?= htmlspecialchars($student_data['semester']) ?></p>

                    <h2>Feedback Details</h2>
                    <?php if (!empty($feedback_data)): ?>
                        <table class="feedback-table">
                            <thead>
                                <tr>
                                    <th>Semester</th>
                                    <th>Professor</th>
                                    <th>Department</th>
                                    <th>Subject</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($feedback_data as $feedback): ?>
                                    <tr>
                                        <td data-label="Semester"><?= htmlspecialchars($feedback['semester']) ?></td>
                                        <td data-label="Professor"><?= htmlspecialchars($feedback['professor_name']) ?></td>
                                        <td data-label="Department"><?= htmlspecialchars($feedback['dept']) ?></td>
                                        <td data-label="Subject"><?= htmlspecialchars($feedback['subject_name']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No feedback found for this student.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php $conn->close(); ?>

        </main>
    </section>

</body>

</html>