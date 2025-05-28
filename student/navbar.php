<?php
// Get the current file name
$current_page = basename($_SERVER['PHP_SELF']);
?>

<section id="sidebar">
    <a href="#" class="brand">
        <i class='bx bxs-user'></i>
        <span class="text">AICTE | DCE</span>
    </a>
    <ul class="side-menu top">
        <li class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <a href="dashboard.php">
                <i class='bx bxs-dashboard'></i>
                <span class="text">Dashboard</span>
            </a>
        </li>
        <li class="<?php echo ($current_page == 'rating.php') ? 'active' : ''; ?>">
            <a href="rating.php">
                <i class='bx bxs-message-dots'></i>
                <span class="text">Feedback</span>
            </a>
        </li>
        <li class="<?php echo ($current_page == 'report.php') ? 'active' : ''; ?>">
            <a href="#">
                <i class='bx bxs-doughnut-chart'></i>
                <span class="text">Analytics</span>
            </a>
        </li>
        <li class="<?php echo ($current_page == 'attendance.php') ? 'active' : ''; ?>">
            <a href="attendance">
                <i class='bx bx-calendar'></i>
                <span class="text">Attendance</span>
            </a>
        </li>
    </ul>
    <ul class="side-menu">
        <li class="<?php echo ($current_page == 'account.php') ? 'active' : ''; ?>">
            <a href="account.php">
                <i class='bx bxs-cog'></i>
                <span class="text">Settings</span>
            </a>
        </li>
        <li class="<?php echo ($current_page == 'logout.php') ? 'active' : ''; ?> ">
            <a href="../endpoint/logout.php" class="logout">
                <i class='bx bxs-log-out-circle'></i>
                <span class="text">Logout</span>
            </a>
        </li>
    </ul>
</section>