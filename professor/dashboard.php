<?php
include 'pro_name.php';

$classSql = "SELECT * FROM assigned_class WHERE professor_id = '$professor_id'";
$classResult = mysqli_query($conn, $classSql);
if (mysqli_num_rows($classResult) > 0) {
    $classCount = mysqli_num_rows($classResult);
} else {
    $classCount = "N/A";
}

$feedSql = "SELECT * FROM eventFeedback WHERE professor_id = '$professor_id'";
$feedResult = mysqli_query($conn, $feedSql);
if (mysqli_num_rows($feedResult) > 0) {
    $feedCount = mysqli_num_rows($feedResult);
} else {
    $feedCount = "N/A";
}


// Counting students
$stdSql = "SELECT * FROM students";
$stdResult = mysqli_query($conn, $stdSql);
$studentCount = mysqli_num_rows($stdResult) > 0 ? mysqli_num_rows($stdResult) : "N/A";

// Counting subjects
$subjSql = "SELECT * FROM subjects";
$subjResult = mysqli_query($conn, $subjSql);
$subjCount = mysqli_num_rows($subjResult) > 0 ? mysqli_num_rows($subjResult) : "N/A";


?>


<!DOCTYPE html>
<html lang="en">
<?php
include 'dependencies.php';
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
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Dashboard</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a href="#">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Home</a>
                        </li>
                    </ul>
                </div>
                <a href="#" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Download PDF</span>
                </a>
            </div>

            <ul class="box-info">
                <li>
                    <i class='bx bxs-group'></i>
                    <span class="text">
                        <h3><?php echo $studentCount; ?></h3>
                        <p>Students</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-book'></i>
                    <span class="text">
                        <h3><?php echo $subjCount; ?></h3>
                        <p>Total Subjects</p>
                    </span>
                </li>
                <li>
                    <i class='bx bx-chalkboard'></i>
                    <span class="text">
                        <h3><?php echo $classCount; ?></h3>
                        <p>Assigned Classes</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-message-check'></i>
                    <span class="text">
                        <h3><?php echo $feedCount; ?></h3>
                        <p>Professor's Feedback</p>
                    </span>
                </li>
            </ul>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->
</body>
</html>