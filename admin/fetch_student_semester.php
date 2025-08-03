<?php
include 'admin_name.php';

$semester = $_POST['semester'] ?? '';

if (!isset($conn) || !$conn) {
    echo "<p style='color:red;'>Database connection failed.</p>";
    exit();
}

// ✅ Initialize before conditions to avoid warnings
$params = [];
$types = "";

$sql = "SELECT id, name, email, branch, phone, reg_no, semester FROM students WHERE 1=1";

if ($semester !== '') {
    $sql .= " AND semester = ?";
    $params[] = $semester;
    $types .= "i"; 
}

// ✅ Add descending order by reg_no
$sql .= " ORDER BY branch ASC";

$stmt = $conn->prepare($sql);

// ✅ Bind parameters only if present
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='color:orange;'>No students found for this semester.</p>";
    exit();
}

echo "<table>
<thead>
<tr>
    <th>Select</th>
    <th>Name</th>
    <th>Branch</th>
    <th>Reg. No</th>
    <th>Semester</th>
</tr>
</thead><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td><input type='checkbox' name='student_ids[]' value='{$row['id']}'></td>
            <td>{$row['name']}</td>
            <td>{$row['branch']}</td>
            <td>{$row['reg_no']}</td>
            <td>{$row['semester']}</td>
          </tr>";
}

echo "</tbody></table>";
echo "<button id='update-semester' class='btn-main'>Update Semester</button>";

$stmt->close();
$conn->close();
?>
