<?php

include '../endpoint/config.php';

// Get the student registration number from the form
$regNo = isset($_POST['reg_no']) ? $_POST['reg_no'] : '';

$studentDetails = [];
$ratings = [];

if ($regNo) {
    // First, get student details
    $studentQuery = "SELECT name, reg_no, branch FROM students WHERE reg_no = ?";
    $stmt = $conn->prepare($studentQuery);
    $stmt->bind_param("s", $regNo);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the student details
    if ($result->num_rows > 0) {
        $studentDetails = $result->fetch_assoc();
    }
    $stmt->close();

    // Get ratings given by the student to all professors along with question text
    $ratingsQuery = "SELECT 
                        professors.name AS professor_name, 
                        feedback_ratings.question_id, 
                        feedback_ratings.rating, 
                        feedback_questions.question_text, 
                        feedback_ratings.semester, 
                        feedback_ratings.subject_id
                    FROM 
                        feedback_ratings
                    JOIN 
                        professors ON feedback_ratings.professor_id = professors.prof_id
                    JOIN 
                        feedback_questions ON feedback_ratings.question_id = feedback_questions.question_id
                    WHERE 
                        feedback_ratings.student_id = (SELECT id FROM students WHERE reg_no = ?)";
    
    // Prepare and execute the ratings query
    $stmt = $conn->prepare($ratingsQuery);
    $stmt->bind_param("s", $regNo);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch all the ratings
    while ($row = $result->fetch_assoc()) {
        $ratings[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Ratings for Professors</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            margin-bottom: 20px;
            text-align: center;
        }
        input[type="text"] {
            padding: 10px;
            width: 60%;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        .student-details, .ratings-section {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
        }
        td {
            background-color: #fdfdfd;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Student Ratings for Professors</h1>
    
    <form action="particular.php" method="post">
        <input type="text" name="reg_no" placeholder="Enter student registration number" required>
        <input type="submit" value="Search">
    </form>

    <?php if ($studentDetails): ?>
        <!-- Display Student Details -->
        <div class="student-details">
            <h2>Student Details</h2>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($studentDetails['name']); ?></p>
            <p><strong>Registration Number:</strong> <?php echo htmlspecialchars($studentDetails['reg_no']); ?></p>
            <p><strong>Branch:</strong> <?php echo htmlspecialchars($studentDetails['branch']); ?></p>
        </div>
    <?php endif; ?>

    <?php if ($ratings): ?>
        <!-- Display Ratings for Professors -->
        <div class="ratings-section">
            <h2>Ratings Given to Professors</h2>
            <table>
                <thead>
                    <tr>
                        <th>Professor Name</th>
                        <th>Question ID</th>
                        <th>Question Text</th>
                        <th>Rating</th>
                        <th>Semester</th>
                        <th>Subject ID</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ratings as $rating): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($rating['professor_name']); ?></td>
                            <td><?php echo htmlspecialchars($rating['question_id']); ?></td>
                            <td><?php echo htmlspecialchars($rating['question_text']); ?></td>
                            <td><?php echo htmlspecialchars($rating['rating']); ?></td>
                            <td><?php echo htmlspecialchars($rating['semester']); ?></td>
                            <td><?php echo htmlspecialchars($rating['subject_id']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($regNo): ?>
        <p>No ratings found for registration number <?php echo htmlspecialchars($regNo); ?></p>
    <?php endif; ?>
</div>

</body>
</html>
