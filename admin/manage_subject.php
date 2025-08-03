<?php
require_once "../endpoint/config.php"; // Database connection

$message = "";

// DELETE SUBJECT
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM subjects WHERE subject_id = ?");
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        $message = '<div class="alert alert-success">Subject deleted successfully!</div>';
    } else {
        $message = '<div class="alert alert-danger">Failed to delete subject.</div>';
    }
    $stmt->close();
}

// UPDATE SUBJECT
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit_subject_id"])) {
    $subject_id = intval($_POST["edit_subject_id"]);
    $subject_name = trim($_POST["edit_subject_name"]);
    $semester = intval($_POST["edit_semester"]);
    $branch = trim($_POST["edit_branch"]);

    if (empty($subject_name) || empty($semester) || empty($branch)) {
        $message = '<div class="alert alert-danger">All fields are required!</div>';
    } else {
        $stmt = $conn->prepare("UPDATE subjects SET subject_name=?, semester=?, branch=? WHERE subject_id=?");
        $stmt->bind_param("sisi", $subject_name, $semester, $branch, $subject_id);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Subject updated successfully!</div>';
        } else {
            $message = '<div class="alert alert-danger">Error updating subject.</div>';
        }
        $stmt->close();
    }
}

// FETCH SUBJECTS BASED ON FILTER
$semester_filter = isset($_GET['semester']) ? intval($_GET['semester']) : "";
$branch_filter = isset($_GET['branch']) ? trim($_GET['branch']) : "";

$query = "SELECT * FROM subjects WHERE 1";
$params = [];
$types = "";

if (!empty($semester_filter)) {
    $query .= " AND semester = ?";
    $params[] = $semester_filter;
    $types .= "i";
}
if (!empty($branch_filter)) {
    $query .= " AND branch = ?";
    $params[] = $branch_filter;
    $types .= "s";
}

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Subjects</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="text-center">Manage Subjects</h4>
            </div>
            <div class="card-body">
                <?php echo $message; ?>
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Filter by Semester</label>
                        <select name="semester" class="form-select">
                            <option value="">All Semesters</option>
                            <?php for ($i = 1; $i <= 8; $i++) { ?>
                                <option value="<?= $i ?>" <?= ($semester_filter == $i) ? "selected" : "" ?>>
                                    Semester <?= $i ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Filter by Branch</label>
                        <select name="branch" class="form-select">
                            <option value="">All Branches</option>
                            <option value="CSE" <?= ($branch_filter == "CSE") ? "selected" : "" ?>>CSE</option>
                            <option value="EEE" <?= ($branch_filter == "EEE") ? "selected" : "" ?>>EEE</option>
                            <option value="ME" <?= ($branch_filter == "ME") ? "selected" : "" ?>>ME</option>
                            <option value="CE" <?= ($branch_filter == "CE") ? "selected" : "" ?>>CE</option>
                            <option value="FTS" <?= ($branch_filter == "FTS") ? "selected" : "" ?>>FTS</option>
                        </select>
                    </div>

                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">Filter</button>
                        <a href="add_subject.php" class="btn btn-success flex-fill">Add New Subject</a>
                    </div>
                </form>

                <table class="table table-bordered table-striped mt-3">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Subject Name</th>
                            <th>Semester</th>
                            <th>Branch</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                            <tr>
                                <td><?= $row['subject_id'] ?></td>
                                <td><?= $row['subject_name'] ?></td>
                                <td><?= $row['semester'] ?></td>
                                <td><?= $row['branch'] ?></td>
                                <td>
                                    <a href="?edit_id=<?= $row['subject_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="?delete_id=<?= $row['subject_id'] ?>" class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                            <?php if (isset($_GET['edit_id']) && $_GET['edit_id'] == $row['subject_id']) { ?>
                                <tr>
                                    <td colspan="5">
                                        <form method="POST">
                                            <input type="hidden" name="edit_subject_id" value="<?= $row['subject_id'] ?>">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <input type="text" name="edit_subject_name" class="form-control"
                                                        value="<?= $row['subject_name'] ?>" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <select name="edit_semester" class="form-select" required>
                                                        <?php for ($i = 1; $i <= 8; $i++) { ?>
                                                            <option value="<?= $i ?>" <?= ($row['semester'] == $i) ? "selected" : "" ?>>Semester <?= $i ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <select name="edit_branch" class="form-select" required>
                                                        <option value="CSE" <?= ($row['branch'] == "CSE") ? "selected" : "" ?>>CSE
                                                        </option>
                                                        <option value="EEE" <?= ($row['branch'] == "EEE") ? "selected" : "" ?>>EEE
                                                        </option>
                                                        <option value="ME" <?= ($row['branch'] == "ME") ? "selected" : "" ?>>ME
                                                        </option>
                                                        <option value="CE" <?= ($row['branch'] == "CE") ? "selected" : "" ?>>CE
                                                        </option>
                                                        <option value="FTS" <?= ($row['branch'] == "FTS") ? "selected" : "" ?>>FTS
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="submit" class="btn btn-success w-100">Update</button>
                                                </div>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>