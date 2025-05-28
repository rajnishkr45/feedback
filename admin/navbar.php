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
            <a href="dashboard">
                <i class='bx bxs-dashboard'></i>
                <span class="text">Dashboard</span>
            </a>
        </li>
        <li class="<?php echo ($current_page == 'assign_class.php') ? 'active' : ''; ?>">
            <a href="assign_class">
                <i class='bx bxs-calendar-plus'></i>
                <span class="text">Assign Class</span>
            </a>
        </li>
        <li class="<?php echo ($current_page == 'report.php') ? 'active' : ''; ?>">
            <a href="report">
                <i class='bx bxs-doughnut-chart'></i>
                <span class="text">Analytics</span>
            </a>
        </li>
        <li class="<?php echo ($current_page == 'institute_activity.php') ? 'active' : ''; ?>">
            <a href="instute_activity">
                <i class='bx bxs-message-dots'></i>
                <span class="text">Institute</span>
            </a>
        </li>
        <li class="<?php echo ($current_page == 'manage_pro.php') ? 'active' : ''; ?>">
            <a href="manage_pro">
                <i class='bx bxs-user-voice'></i>
                <span class="text">Proffesors</span>
            </a>
        </li>
      
        <li class="<?php echo ($current_page == 'manage_std.php') ? 'active' : ''; ?>">
            <a href="manage_std">
                <i class='bx bxs-group'></i>
                <span class="text">Students</span>
            </a>
        </li>
    </ul>
    <ul class="side-menu">
        <li class="<?php echo ($current_page == 'account.php') ? 'active' : ''; ?>">
            <a href="account">
                <i class='bx bxs-cog'></i>
                <span class="text">Settings</span>
            </a>
        </li>
        <li class="<?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>">
            <a href="../endpoint/logout" class="logout">
                <i class='bx bxs-log-out-circle'></i>
                <span class="text">Logout</span>
            </a>
        </li>
    </ul>
</section>