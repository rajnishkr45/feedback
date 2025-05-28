<?php
session_start();
if (!isset($_SESSION['admin_email'])) {
    header('Location: ../login');
    exit;
}

include '../endpoint/config.php'; // Database connection

$dept_search = isset($_GET['dept']) ? trim($_GET['dept']) : '';

// SQL Query
if (!empty($dept_search)) {
    $sql = "SELECT prof_id, name, email, phone, role, dept FROM professors WHERE dept LIKE ?";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%$dept_search%";
    $stmt->bind_param("s", $searchTerm);
} else {
    $sql = "SELECT prof_id, name, email, phone, role, dept FROM professors";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $serial = 1;
    while ($row = $result->fetch_assoc()) {
        echo "<tr data-prof_id='" . htmlspecialchars($row['prof_id']) . "'>
            <td>{$serial}</td>
            <td><span class='prof-name'>" . htmlspecialchars($row['name']) . "</span></td>
            <td><span class='prof-email'>" . htmlspecialchars($row['email']) . "</span></td>
            <td><span class='prof-phone'>" . htmlspecialchars($row['phone']) . "</span></td>
            <td><span class='prof-dept'>" . htmlspecialchars($row['dept']) . "</span></td>
            <td><span class='prof-role'>" . htmlspecialchars($row['role']) . "</span></td>
            <td style='text-align:center;'>
                <span class='edit-btn' 
                    data-prof_id='" . htmlspecialchars($row['prof_id']) . "' 
                    data-name='" . htmlspecialchars($row['name']) . "' 
                    data-email='" . htmlspecialchars($row['email']) . "' 
                    data-phone='" . htmlspecialchars($row['phone']) . "' 
                    data-dept='" . htmlspecialchars($row['dept']) . "' 
                    data-role='" . htmlspecialchars($row['role']) . "'>
                    <i class='bx bxs-edit'></i>
                </span>
                <span class='delete-btn' data-id='" . htmlspecialchars($row['prof_id']) . "'>
                    <i class='bx bxs-trash'></i>
                </span>
            </td>
        </tr>";
        $serial++;
    }
} else {
    echo "<tr><td colspan='7'>No professors found</td></tr>";
}

$stmt->close();
$conn->close();
?>
