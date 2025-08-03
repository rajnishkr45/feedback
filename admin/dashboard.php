<?php
include 'admin_name.php'; // DB connection

// ✅ Function to count records efficiently
function getCount($conn, $table) {
    $sql = "SELECT COUNT(*) AS total FROM $table";
    $result = mysqli_query($conn, $sql);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        return $row['total'];
    }
    return "N/A";
}

// ✅ Getting all counts
$proCount       = getCount($conn, "professors");
$studentCount   = getCount($conn, "students");
$classCount     = getCount($conn, "assigned_class");
$subjCount      = getCount($conn, "subjects");
$feedCount      = getCount($conn, "feedback_ratings");
$proFeedCount   = getCount($conn, "eventFeedback");
$deptCount      = getCount($conn, "departmental_activities");
$instCount      = getCount($conn, "institute_activity");
$sessCount      = getCount($conn, "sessions");
?>
<!DOCTYPE html>
<html lang="en">
<?php include './dependencies.php'; ?>

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
                <img src="../dp/<?php echo $profilePicture ?? 'default.png'; ?>">
            </a>
        </nav>

        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Dashboard</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Home</a></li>
                    </ul>
                </div>
                <a href="./verify_student" class="btn-download">
                    <i class='bx bx-search-alt-2'></i>
                    <span class="text">Check Status</span>
                </a>
            </div>

            <ul class="box-info">
                <li>
                    <i class='bx bxs-user-voice'></i>
                    <a href="manage_pro">
                        <span class="text"><h3><?= $proCount ?></h3><p>Manage Prof.</p></span>
                    </a>
                </li>
                <li>
                    <i class='bx bxs-group'></i>
                    <a href="manage_std">
                        <span class="text"><h3><?= $studentCount ?></h3><p>Manage Students</p></span>
                    </a>
                </li>
                <li>
                    <i class='bx bxs-book-add'></i>
                    <a href="manage_subject">
                        <span class="text"><h3><?= $subjCount ?></h3><p>Manage Subjects</p></span>
                    </a>
                </li>
                <li>
                    <i class='bx bx-chalkboard'></i>
                    <a href="manage_class">
                        <span class="text"><h3><?= $classCount ?></h3><p>Manage Classes</p></span>
                    </a>
                </li>
                <li>
                    <i class='bx bxs-message-dots'></i>
                    <span class="text"><h3><?= $feedCount ?: "N/A" ?></h3><p>Student's Feedback</p></span>
                </li>
                <li>
                    <i class='bx bxs-message-check'></i>
                    <span class="text"><h3><?= $proFeedCount ?></h3><p>Contribution to society</p></span>
                </li>
                <li>
                    <i class='bx bxs-school'></i>
                    <a href="departmental_activity">
                        <span class="text"><h3><?= $deptCount ?></h3><p>Manage Departmental</p></span>
                    </a>
                </li>
                <li>
                    <i class='bx bxs-institution'></i>
                    <a href="institute_activity">
                        <span class="text"><h3><?= $instCount ?></h3><p>Manage Institutional</p></span>
                    </a>
                </li>
                <li>
                    <i class='bx bxs-calendar'></i>
                    <a href="manage_sessions" target="_blank">
                        <span class="text"><h3><?= $sessCount ?></h3><p>Manage Session</p></span>
                    </a>
                </li>
            </ul>
        </main>
    </section>
</body>
</html>
