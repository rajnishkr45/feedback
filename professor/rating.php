<?php
include 'pro_name.php';
$professor_id = $professor['prof_id']; // Assuming you want to store the professor ID.

$feedSql = "SELECT * FROM eventFeedback WHERE professor_id = '$professor_id'";

$feedResult = mysqli_query($conn, $feedSql);
if (mysqli_num_rows($feedResult) > 0) {
    $feedCount = mysqli_num_rows($feedResult);
} else {
    $feedCount = "0";
}


$teachingSql = "SELECT * FROM teaching_process WHERE professor_id = '$professor_id'";
$teachingResult = mysqli_query($conn, $teachingSql);
if (mysqli_num_rows($teachingResult) > 0) {
    $teachingCount = mysqli_num_rows($teachingResult);
} else {
    $teachingCount = "0";
}

$instituteSql = "SELECT * FROM institute_activity WHERE professor_id = '$professor_id'";
$instituteResult = mysqli_query($conn, $instituteSql);
if (mysqli_num_rows($instituteResult) > 0) {
    $instituteCount = mysqli_num_rows($instituteResult);
} else {
    $instituteCount = "0";
}


?>



<!DOCTYPE html>
<html lang="en">

<?php
include 'dependencies.php';
?>
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
                <img src="../dp/<?php echo $profilePicture ?? 'default.png'; ?>" alt="Profile Pic">
            </a>
        </nav>


        <main>
            <div class="head-title">
                <div class="left">
                    <ul class="breadcrumb">
                        <li>
                            <a href="#">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Feedbacks</a>
                        </li>
                    </ul>
                </div>
            </div>

            <ul class="box-info">
                <li id="cts">
                    <i class='bx bx-world'></i>
                    <span class="text">
                        <h3><?php echo $feedCount; ?></h3>
                        <p>Contribution to society</p>
                    </span>
                </li>
                <li id="teaching_process">
                    <i class='bx bx-chalkboard'></i>
                    <span class="text">
                        <h3><?php echo $teachingCount; ?></h3>
                        <p>Teaching Process</p>
                    </span>
                </li>
                <li id="institute_activity">
                    <i class='bx bx-buildings'></i>
                    <span class="text">
                        <h3><?php echo $instituteCount; ?></h3>
                        <p>Institute Activities</p>
                    </span>
                </li>

            </ul>
        </main>
    </section>


    <script>
        document.getElementById("cts").addEventListener("click", () => {
            window.location.href = "cts";
        })

        document.getElementById("teaching_process").addEventListener("click", () => {
            window.location.href = "teaching_process";
        })
      
        document.getElementById("institute_activity").addEventListener("click", () => {
            window.location.href = "institute_activity";
        })
    </script>
</body>

</html>