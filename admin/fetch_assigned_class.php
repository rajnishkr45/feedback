<?php
session_start();
if (isset($_SESSION['admin_email'])) {
    include '../endpoint/config.php';
    $semester = isset($_GET['semester']) ? $_GET['semester'] : '';
    $branch = isset($_GET['branch']) ? $_GET['branch'] : '';

    // Prepare SQL query base
    $sql = "SELECT p.name AS professor_name, ac.id AS assigned_class_id, s.subject_name, ac.assigned_sem, ac.assigned_branch
        FROM assigned_class ac
        JOIN professors p ON ac.professor_id = p.prof_id
        JOIN subjects s ON ac.subject_id = s.subject_id";

    // Initialize conditions array
    $conditions = [];

    // Check if a specific semester is selected
    if ($semester && $semester !== 'all') {
        $conditions[] = "ac.assigned_sem = '$semester'";
    }

    // Check if a specific branch is selected
    if ($branch && $branch !== 'all') {
        $conditions[] = "ac.assigned_branch = '$branch'";
    }

    // If there are conditions, append them to the SQL query
    if (count($conditions) > 0) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }

    // Add the ORDER BY clause
    $sql .= " ORDER BY ac.assigned_sem ASC";

    // Execute the query
    $result = $conn->query($sql);

    // Check if results exist and output rows
    if ($result->num_rows > 0) {
        $serial = 1;
        while ($row = $result->fetch_assoc()) {
            $assigned_class_id = $row['assigned_class_id'];
            echo "<tr>
                <td>$serial</td>
                <td>{$row['professor_name']}</td>
                <td>{$row['subject_name']}</td>
                <td>{$row['assigned_branch']}</td>
                <td style='text-align:center;'>{$row['assigned_sem']}</td>
                <td style='text-align:center font-size:20px;>
                    <a href='edit_assigned_class.php?id=$assigned_class_id' class='edit-btn' title='Edit'>
                        <i class='bx bxs-edit'></i>
                    </a>
                    <a href='javascript:void(0);' class='delete-btn' title='Delete' onclick='deleteAssignedClass($assigned_class_id, this.closest(\"tr\"))'>
                        <i class='bx bxs-trash'></i>
                    </a>
                </td>
              </tr>";
            $serial++;
        }
    } else {
        echo "<tr><td colspan='6'>No assigned classes found.</td></tr>";
    }

    $conn->close();
} else {
    header('Location: ../login');
    exit;
}
?>