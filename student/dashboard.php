<?php
include 'std_name.php';

// Counting subjects
$subjSql = "SELECT * FROM subjects WHERE semester = '$stdSem' AND branch = '$stdBranch'";
$subjResult = mysqli_query($conn, $subjSql);
$subjCount = mysqli_num_rows($subjResult) > 0 ? mysqli_num_rows($subjResult) : "N/A";

$feedSql = "SELECT * FROM feedback_ratings WHERE student_id = '$student_id'";
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
                    <i class='bx bxs-message-check'></i>
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
                        <p>Your Feedback</p>
                    </span>
                </li>
            </ul>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>
                            <?php
                            $todaydate = date("F j, Y"); // Format: MonthName Day, Year
                            echo $todaydate;
                            ?>
                        </h3>
                    </div>
                    <table class="professor-table">
                        <thead>
                            <tr>
                                <th>SN.</th>
                                <th>Subject</th>
                                <th>Professor Name</th>
                                <th>Class Time</th>
                                <th>Attendance Status</th>
                            </tr>
                        </thead>
                        <tbody id="student-list">
                            <?php
                            // Ensure the student is logged in
                            if (isset($_SESSION['email'])) {
                                $today = date("Y-m-d");
                                // Query to fetch today's attendance subject-wise
                                $attendanceQuery = "
            SELECT a.subject_id, s.subject_name, p.name AS professor_name, a.timestamp, a.status
            FROM attendance a
            JOIN subjects s ON a.subject_id = s.subject_id
            JOIN professors p ON a.professor_id = p.prof_id
            WHERE a.student_id = '$student_id' 
              AND DATE(a.timestamp) = '$today'
        ";
                                $result = mysqli_query($conn, $attendanceQuery);

                                // Check if there are results
                                if (mysqli_num_rows($result) > 0) {
                                    $sn = 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        echo "<tr>";
                                        echo "<td style='text-align:center;'>" . $sn++ . "</td>";
                                        echo "<td>" . htmlspecialchars($row['subject_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['professor_name']) . "</td>";

                                        $startTime = strtotime($row['timestamp']); // Get the timestamp
                                        $endTime = strtotime('+1 hour', $startTime); // Add 1 hour to the timestamp
                            
                                        echo "<td style='text-align:center;'>"
                                            . date("h:00 A", $startTime)
                                            . " - "
                                            . date("h:00 A", $endTime)
                                            . "</td>";


                                        echo "<td style='text-align:center;'>" . $row['status'] . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' style='text-align: center;'>No attendance records found for today.</td></tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' style='text-align: center;'>Student not logged in.</td></tr>";
                            }
                            ?>
                        </tbody>

                    </table>
                </div>
            </div>
        </main>
    </section>
</body>

</html>