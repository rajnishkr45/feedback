<?php
include 'pro_name.php'; // contains DB connection + $professor_id + $professor_email

// Fetch professor name
$stmt = $conn->prepare("SELECT name FROM professors WHERE prof_id = ?");
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$resultProf = $stmt->get_result();
$professorName = "";
if ($resultProf->num_rows > 0) {
    $row = $resultProf->fetch_assoc();
    $professorName = $row['name'];
}

// Fetch assigned subjects for the logged-in professor
$sql = "SELECT s.subject_name, ac.assigned_sem, ac.assigned_branch
        FROM assigned_class ac
        JOIN subjects s ON ac.subject_id = s.subject_id
        WHERE ac.professor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$result = $stmt->get_result();

$assignedSubjects = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $assignedSubjects[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'dependencies.php'; ?>
<body>

<?php include 'navbar.php'; ?>

<section id="content">
    <nav>
        <i class='bx bx-menu'></i>
        <form action="#">
            <div class="form-input">
                <input type="search" placeholder="Search...">
                <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
            </div>
        </form>
        <input type="checkbox" id="switch-mode" hidden>
        <label for="switch-mode" class="switch-mode"></label>
        <a href="#" class="notification">
            <i class='bx bxs-bell'></i>
            <span class="num">9</span>
        </a>
        <a href="#" class="profile">
            <img src="../dp/<?php echo $profilePicture ?? 'default.png'; ?>" alt="Profile Pic">
        </a>
    </nav>

    <main>
        <div class="head-title">
            <div class="left">
                <ul class="breadcrumb">
                    <li><a href="#">Dashboard</a></li>
                    <li><i class='bx bx-chevron-right'></i></li>
                    <li><a class="active" href="#">My Subjects</a></li>
                </ul>
            </div>
            <a href="#" class="btn-download">
                <i class='bx bxs-cloud-download'></i>
                <span class="text">Download PDF</span>
            </a>
        </div>

        <div class="table-data" style="margin-top: 20px;">
            <div class="order">
                <div class="head">
                    <h3>Assigned Classes - <?php echo htmlspecialchars($professorName); ?></h3>
                </div>

                <?php if (!empty($assignedSubjects)) { ?>
                    <table class="professor-table" style="font-size:14px;">
                        <thead>
                            <tr>
                                <th>SN.</th>
                                <th>Subject Name</th>
                                <th>Semester</th>
                                <th>Branch</th>
                            </tr>
                        </thead>
                        <tbody style="text-align:center">
                            <?php 
                            $sn = 1;
                            foreach ($assignedSubjects as $subject) { ?>
                                <tr>
                                    <td><?= $sn++ ?></td>
                                    <td><?= htmlspecialchars($subject['subject_name']) ?></td>
                                    <td><?= htmlspecialchars($subject['assigned_sem']) ?></td>
                                    <td><?= htmlspecialchars($subject['assigned_branch']) ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else {
                    echo "<p>No subjects assigned yet.</p>";
                } ?>
            </div>
        </div>
    </main>
</section>

</body>
</html>
