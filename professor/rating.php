<?php
include 'pro_name.php';
$professor_id = $professor['prof_id'] ?? 0;

// Utility function to count rows for a table and professor
function getActivityCount($conn, $table, $professor_id)
{
    $stmt = $conn->prepare("SELECT COUNT(*) FROM $table WHERE professor_id = ?");
    $stmt->bind_param("i", $professor_id);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

// Fetch activity counts
$feedCount = getActivityCount($conn, 'eventfeedback', $professor_id);
$teachingCount = getActivityCount($conn, 'teaching_process', $professor_id);
$instituteCount = getActivityCount($conn, 'institute_activity', $professor_id);
$deptCount = getActivityCount($conn, 'departmental_activities', $professor_id);
?>


<!DOCTYPE html>
<html lang="en">
<?php include 'dependencies.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="../css/form.css?v=1">

<body>
    <style>
        .box-info li {
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .box-info li:hover {
            transform: translateY(-10px);
        }
    </style>

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
                        <li><a class="active" href="#">Feedbacks</a></li>
                    </ul>
                </div>
            </div>

            <ul class="box-info">
                <li id="cts">
                    <i class='bx bx-world'></i>
                    <span class="text">
                        <h3><?= $feedCount ?></h3>
                        <p>Contribution to Society</p>
                    </span>
                </li>
                <li id="teaching_process">
                    <i class='bx bx-chalkboard'></i>
                    <span class="text">
                        <h3><?= $teachingCount ?></h3>
                        <p>Teaching Process</p>
                    </span>
                </li>
                <li id="institute_activity">
                    <i class='bx bx-buildings'></i>
                    <span class="text">
                        <h3><?= $instituteCount ?></h3>
                        <p>Institute Activities</p>
                    </span>
                </li>
                <li id="dept_activity">
                    <i class='bx bxs-school'></i>
                    <span class="text">
                        <h3><?= $deptCount ?></h3>
                        <p>Departmental Activities</p>
                    </span>
                </li>
            </ul>
        </main>
    </section>

    <script>
        const pageLinks = {
            cts: "cts",
            teaching_process: "teaching_process",
            institute_activity: "institute_activity",
            dept_activity: "departmental_activity"
        };

        Object.keys(pageLinks).forEach(id => {
            document.getElementById(id)?.addEventListener("click", () => {
                window.location.href = pageLinks[id];
            });
        });
    </script>

</body>

</html>