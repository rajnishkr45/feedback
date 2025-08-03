<?php
$conn = new mysqli("localhost", "root", "", "feedback");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$currentYear = date("Y");

// Add new session
if (isset($_POST['add_session'])) {
    $session_name = $_POST['session_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $check = $conn->query("SELECT * FROM sessions WHERE session_name='$session_name'");
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO sessions (session_name, start_date, end_date, status) 
                      VALUES ('$session_name', '$start_date', '$end_date', 'Inactive')");
        echo "<script>Swal.fire('✅ Success', 'Session Added Successfully!', 'success');</script>";
    } else {
        echo "<script>Swal.fire('⚠️ Warning', 'Session Already Exists!', 'warning');</script>";
    }
}

// Activate session (Only for current year)
if (isset($_GET['activate'])) {
    $id = $_GET['activate'];
    $sessionData = $conn->query("SELECT * FROM sessions WHERE session_id=$id")->fetch_assoc();

    if ($sessionData) {
        $sessionYear = (int)substr($sessionData['session_name'], 0, 4); // Extract starting year

        if ($sessionYear == $currentYear) {
            $conn->query("UPDATE sessions SET status='Inactive'");
            $conn->query("UPDATE sessions SET status='Active' WHERE session_id=$id");
            echo "<script>Swal.fire('✅ Activated', 'Session Activated Successfully!', 'success');</script>";
        } else {
            echo "<script>Swal.fire('❌ Not Allowed', 'You cannot activate an old session!', 'error');</script>";
        }
    }
}

// Deactivate session (Allowed only for current year)
if (isset($_GET['deactivate'])) {
    $id = $_GET['deactivate'];
    $sessionData = $conn->query("SELECT * FROM sessions WHERE session_id=$id")->fetch_assoc();

    if ($sessionData) {
        $sessionYear = (int)substr($sessionData['session_name'], 0, 4);

        if ($sessionYear == $currentYear) {
            $conn->query("UPDATE sessions SET status='Inactive' WHERE session_id=$id");
            echo "<script>Swal.fire('✅ Deactivated', 'Session Deactivated Successfully!', 'success');</script>";
        } else {
            echo "<script>Swal.fire('❌ Not Allowed', 'You cannot deactivate an old session!', 'error');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Academic Session Management</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: Arial, sans-serif; background: #eef2f7; padding: 20px; }
        .container { background: white; padding: 25px; border-radius: 12px; max-width: 800px; margin: auto; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; }
        .form-box { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
        .form-box input { flex: 1; padding: 10px; font-size: 14px; border: 1px solid #ccc; border-radius: 6px; }
        .form-box button { background: #007BFF; color: white; padding: 10px 15px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .form-box button:hover { background: #0056b3; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; background: white; }
        table th, table td { padding: 12px; text-align: center; border-bottom: 1px solid #ddd; }
        table th { background: #007BFF; color: white; }
        .btn { padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 14px; }
        .btn-activate { background: #28a745; color: white; }
        .btn-deactivate { background: #dc3545; color: white; }
        .stored-title { text-align: center; font-size: 18px; margin-top: 20px; font-weight: bold; color: #444; }
    </style>
</head>
<body>
<div class="container">
    <h2>📅 Academic Session Management</h2>

    <!-- Add Session -->
    <form method="POST" class="form-box">
        <input type="text" name="session_name" placeholder="Session Name (e.g., 2024-25)" required>
        <input type="date" name="start_date" required>
        <input type="date" name="end_date" required>
        <button type="submit" name="add_session">➕ Add Session</button>
    </form>

    <div class="stored-title">📋 Stored Sessions</div>

    <table>
        <tr>
            <th>ID</th>
            <th>Session</th>
            <th>Start</th>
            <th>End</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php
        $sessions = $conn->query("SELECT * FROM sessions ORDER BY session_id DESC");
        while ($row = $sessions->fetch_assoc()) {
            $sessionYear = (int)substr($row['session_name'], 0, 4);
            $isCurrentYear = ($sessionYear == $currentYear);

            echo "<tr>
                <td>{$row['session_id']}</td>
                <td>{$row['session_name']}</td>
                <td>{$row['start_date']}</td>
                <td>{$row['end_date']}</td>
                <td style='font-weight:bold;color:" . ($row['status']=='Active'?'green':'red') . "'>{$row['status']}</td>
                <td>";

            if ($isCurrentYear) {
                if ($row['status'] == 'Active') {
                    echo "<a class='btn btn-deactivate' href='?deactivate={$row['session_id']}'>Deactivate</a>";
                } else {
                    echo "<a class='btn btn-activate' href='?activate={$row['session_id']}'>Activate</a>";
                }
            } else {
                echo "<span style='color:gray;'>🚫 Locked</span>";
            }

            echo "</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>
