<?php
include 'admin_name.php'; // Include your database connection and other necessary files

// Counting professors
$proSql = "SELECT * FROM professors";
$proResult = mysqli_query($conn, $proSql);
$proCount = mysqli_num_rows($proResult) > 0 ? mysqli_num_rows($proResult) : "N/A";

// Counting students
$stdSql = "SELECT * FROM students";
$stdResult = mysqli_query($conn, $stdSql);
$studentCount = mysqli_num_rows($stdResult) > 0 ? mysqli_num_rows($stdResult) : "N/A";

// Counting classes
$classSql = "SELECT * FROM assigned_class";
$classResult = mysqli_query($conn, $classSql);
$classCount = mysqli_num_rows($classResult) > 0 ? mysqli_num_rows($classResult) : "N/A";

// Counting subjects
$subjSql = "SELECT * FROM subjects";
$subjResult = mysqli_query($conn, $subjSql);
$subjCount = mysqli_num_rows($subjResult) > 0 ? mysqli_num_rows($subjResult) : "N/A";

// Counting feedback
$feedSql = "SELECT * FROM feedback_ratings";
$feedResult = mysqli_query($conn, $feedSql);
$feedCount = mysqli_num_rows($feedResult) > 0 ? mysqli_num_rows($feedResult) : "N/A";

// Counting professor feedback
$proFeedSql = "SELECT * FROM eventFeedback";
$proFeedResult = mysqli_query($conn, $proFeedSql);
$proFeedCount = mysqli_num_rows($proFeedResult) > 0 ? mysqli_num_rows($proFeedResult) : "N/A";
?>

<!DOCTYPE html>
<html lang="en">

<?php
include './dependencies.php';
?>

<body>
    <!-- SIDEBAR -->
    <?php
    include 'navbar.php';
    ?>
    <!-- SIDEBAR -->

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
                <img src="../dp/<?php echo $profilePicture ?? 'default.png'; ?>">
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
                    <i class='bx bxs-user-voice'></i>
                    <span class="text">
                        <h3><?php echo $proCount; ?></h3>
                        <p>Professors</p>
                    </span>
                </li>
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
                        <p>Total Classes</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-message-dots'></i>
                    <span class="text">
                        <h3>
                            <?php
                            // Make sure $feedCount is an integer
                            $feedCount = (int) $feedCount; // Ensure it's an integer
                            
                            if ($feedCount > 0) {
                                echo $feedCount / 10; // Division if greater than 0
                            } else {
                                echo "N/A"; // Display "N/A" if $feedCount is 0
                            }
                            ?>
                        </h3>
                        <p>Student's Feedback</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-message-check'></i>
                    <span class="text">
                        <h3><?php echo $proFeedCount; ?></h3>
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