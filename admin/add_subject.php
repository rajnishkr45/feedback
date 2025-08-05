<?php
include 'admin_name.php'; // Include database connection

$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject_name = trim($_POST["subject_name"]);
    $semester = intval($_POST["semester"]);
    $branch = trim($_POST["branch"]);

    if (empty($subject_name) || empty($semester) || empty($branch)) {
        $message = '<div class="alert alert-danger">All fields are required!</div>';
    } else {
        $stmt = $conn->prepare("INSERT INTO subjects (subject_name, semester, branch) VALUES (?, ?, ?)");
        $stmt->bind_param("sis", $subject_name, $semester, $branch);

        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Subject added successfully!</div>';
        } else {
            $message = '<div class="alert alert-danger">Error: ' . $stmt->error . '</div>';
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<?php include './dependencies.php'; ?>

<body>
    <?php include 'navbar.php'; ?>

    <section id="content">
        <nav>
            <i class="bx bx-menu"></i>
            <form>
                <div class="form-input">
                    <input type="search" placeholder="Search...">
                    <button type="submit" class="search-btn"><i class="bx bx-search"></i></button>
                </div>
            </form>
        </nav>

        <main>
            <div class="head-title">
                <div class="left">
                    <ul class="breadcrumb">
                        <li><a href="dashboard.php">Dashboard</a></li>
                        <li><i class="bx bx-chevron-right"></i></li>
                        <li><a class="active" href="#">Add Subject</a></li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm" style="max-width: 600px; margin: auto;">
                <div class="card-header bg-primary text-white">
                    <h4 class="text-center">Add Subject</h4>
                </div>
                <div class="card-body">
                    <?= $message; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Subject Name</label>
                            <input type="text" name="subject_name" class="form-control" placeholder="Enter Subject Name"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Semester</label>
                            <select name="semester" class="form-select" required>
                                <option value="">Select Semester</option>
                                <?php for ($i = 1; $i <= 8; $i++) { ?>
                                    <option value="<?= $i ?>">Semester <?= $i ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Branch</label>
                            <select name="branch" class="form-select" required>
                                <option value="">Select Branch</option>
                                <option value="CSE">CSE</option>
                                <option value="EEE">EEE</option>
                                <option value="ME">ME</option>
                                <option value="CE">CE</option>
                                <option value="FTS">FTS</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Add Subject</button>
                    </form>
                </div>
            </div>
        </main>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>