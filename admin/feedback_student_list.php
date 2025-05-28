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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Feedback List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5>Student Feedback List</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="mb-3">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="semester" class="form-label">Select Semester</label>
                            <select name="semester" id="semester" class="form-control">
                                <option value="">All</option>
                                <?php for ($i = 1; $i <= 8; $i++): ?>
                                    <option value="<?= $i ?>" <?= ($semester == $i) ? "selected" : "" ?>><?= $i ?>th Semester
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="passing_year" class="form-label">Select Passing Year</label>
                            <select name="passing_year" id="passing_year" class="form-control">
                                <option value="">All</option>
                                <?php for ($year = date("Y"); $year <= 2028; $year++): ?>
                                    <option value="<?= $year ?>" <?= ($passing_year == $year) ? "selected" : "" ?>><?= $year ?>
                                    </option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">Filter</button>
                        </div>
                    </div>
                </form>

                <a href="export_feedback_student_list.php?semester=<?= $semester ?>&passing_year=<?= $passing_year ?>"
                    class="btn btn-primary mb-3 text-white">Download CSV</a>

                <a href="./dashboard" class="btn btn-danger mb-3 text-white">Back to Dashboard</a>


                <?php if ($result->num_rows > 0): ?>
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>SN.</th>
                                <th>Student Name</th>
                                <th>Registration No.</th>
                                <th>Branch</th>
                                <th>Semester</th>
                                <th>Passing Year</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $serial = 1;
                            while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $serial; ?></td>
                                    <td><?= htmlspecialchars($row['name']); ?></td>
                                    <td><?= htmlspecialchars($row['reg_no']); ?></td>
                                    <td><?= htmlspecialchars($row['branch']); ?></td>
                                    <td><?= htmlspecialchars($row['semester']); ?></td>
                                    <td><?= htmlspecialchars($row['passing_year']); ?></td>
                                </tr>
                                <?php
                                $serial++;
                            endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-danger">No students found for the selected criteria.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>