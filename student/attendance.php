<?php
// Include database connection
include 'std_name.php';

// Query to get attendance data for the logged-in student
$attendanceQuery = "
    SELECT a.subject_id, 
           COUNT(*) AS total_classes,
           SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS attended_classes
    FROM attendance a
    WHERE a.student_id = '$student_id'
    GROUP BY a.subject_id
";
$result = mysqli_query($conn, $attendanceQuery);

// Fetch attendance data
$attendanceData = [];
$totalAttended = 0;
$totalClasses = 0;
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $attendanceData[] = $row;
        $totalAttended += $row['attended_classes'];
        $totalClasses += $row['total_classes'];
    }
}

// Query to get subject names based on subject_id
$subjectQuery = "SELECT subject_id, subject_name FROM subjects";
$subjectResult = mysqli_query($conn, $subjectQuery);
$subjects = [];
if ($subjectResult && mysqli_num_rows($subjectResult) > 0) {
    while ($row = mysqli_fetch_assoc($subjectResult)) {
        $subjects[$row['subject_id']] = $row['subject_name'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php
include './dependencies.php';
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script> <!-- For displaying data labels -->

<style>
    .card-container {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .attendance-card {
        width: 250px;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 20px;
        text-align: center;
    }

    .chart-container {
        position: relative;
        height: 150px;
        width: 150px;
        margin: 0 auto;
    }

    .attendance-details {
        margin-top: 10px;
    }

    .chart-container h3,
    .chart-container p {
        position: relative;
        top: -100px;
        right: 0px;
        margin: 5px 0;
    }


    .subject-name {
        font-weight: bold;
    }
</style>

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

            <div class="card-container">
                <?php
                // Loop through attendance data
                foreach ($attendanceData as $data):
                    $percentage = round(($data['attended_classes'] / $data['total_classes']),4 )* 100;
                    $subject_name = $subjects[$data['subject_id']] ?? 'Unknown Subject';
                    ?>
                    <div class="attendance-card">
                        <div class="chart-container">
                            <canvas id="chart-<?php echo $data['subject_id']; ?>"></canvas>
                            <h3><?php echo $percentage; ?>%</h3>
                            <p><?php echo "{$data['attended_classes']} / {$data['total_classes']}"; ?></p>

                        </div>
                        <div class="attendance-details">
                            <p class="subject-name"><?php echo $subject_name; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <br>

            <!-- Overall Attendance -->
            <div class="card-container">
                <div class="attendance-card">
                    <h3>Overall Attendance</h3>
                    <div class="chart-container">
                        <canvas id="overall-chart"></canvas>
                        <h3><?php echo round(($totalAttended / $totalClasses),4) * 100; ?>%</h3>
                        <p><?php echo $totalAttended; ?> / <?php echo $totalClasses; ?></p>

                    </div>
                </div>
            </div>

        </main>
    </section>
    <script>
        // Attendance Data in JS
        const attendanceData = <?php echo json_encode($attendanceData); ?>;
        const totalAttended = <?php echo $totalAttended; ?>;
        const totalClasses = <?php echo $totalClasses; ?>;

        // Define different colors for each subject
        const colors = [
            "#2ECC71", "#3498DB", "#E74C3C", "#F39C12", "#9B59B6", "#1ABC9C", "#16A085", "#F1C40F"
        ];

        // Overall Attendance Chart (combined chart for all subjects)
        const overallCtx = document.getElementById("overall-chart").getContext("2d");
        const overallPercentage = Math.round((totalAttended / totalClasses) * 100);

        new Chart(overallCtx, {
            type: "doughnut",
            data: {
                datasets: [
                    {
                        data: [totalAttended, totalClasses - totalAttended],
                        backgroundColor: ["#2ECC71", "#E5E5E5"], // Green for attended and grey for the remaining part
                        borderWidth: 0,
                    },
                ],
            },
            options: {
                cutout: "70%",
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: false },
                    datalabels: {
                        display: true,
                        color: "#fff", // White color for the text
                        font: {
                            weight: 'bold',
                            size: 16
                        },
                        formatter: function (value, ctx) {
                            if (ctx.dataIndex === 0) {
                                return `${overallPercentage}%`;
                            }
                        },
                        align: 'center',
                        anchor: 'center',
                    },
                },
            },
        });

        // Loop through the attendance data for individual subjects
        attendanceData.forEach((data, index) => {
            const ctx = document.getElementById(`chart-${data.subject_id}`).getContext("2d");
            const attended = data.attended_classes;
            const total = data.total_classes;
            const percentage = Math.round((attended / total) * 100);

            const color = colors[index % colors.length];

            new Chart(ctx, {
                type: "doughnut",
                data: {
                    datasets: [
                        {
                            data: [attended, total - attended],
                            backgroundColor: [color, "#E5E5E5"],
                            borderWidth: 0,
                        },
                    ],
                },
                options: {
                    cutout: "70%",
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { enabled: false },
                        datalabels: {
                            display: true,
                            color: "#fff",
                            font: {
                                weight: 'bold',
                                size: 16
                            },
                            formatter: function (value, ctx) {
                                if (ctx.dataIndex === 0) {
                                    return `${attended} / ${total}\n${percentage}%`;
                                }
                            },
                            align: 'center',
                            anchor: 'center',
                        },
                    },
                },
            });
        });
    </script>
</body>
</html>