<?php
include 'admin_name.php';

// Fetch departmental activities
$activities = [];
$query = "SELECT 
            da.id, 
            da.professor_id, 
            da.activity_name, 
            da.semester, 
            da.points, 
            da.max_points, 
            p.name AS professor_name, 
            p.dept AS professor_dept
          FROM 
            departmental_activities da
          JOIN 
            professors p 
          ON 
            da.professor_id = p.prof_id";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $activities[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'dependencies.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="../css/form.css?v=1">

<body>
    <?php include 'navbar.php'; ?>

    <section id="content">
        <main>
            <div class="head-title">
                <div class="left">
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Departmental Activities</a></li>
                    </ul>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Departmental Activities and Points</h3>
                    </div>
                    <table style="font-size: 14px;">
                        <thead>
                            <tr>
                                <th>SN.</th>
                                <th>Professor Name</th>
                                <th>Department</th>
                                <th>Activity</th>
                                <th>Semester</th>
                                <th>Points</th>
                                <th>Max Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $serial = 0;
                            foreach ($activities as $activity):
                                $serial++;
                                ?>
                                <tr>
                                    <td><?= $serial; ?></td>
                                    <td><?= htmlspecialchars($activity['professor_name']); ?></td>
                                    <td><?= htmlspecialchars($activity['professor_dept']); ?></td>
                                    <td><?= htmlspecialchars($activity['activity_name']); ?></td>
                                    <td>Semester <?= htmlspecialchars($activity['semester']); ?></td>
                                    <td><?= htmlspecialchars($activity['points']); ?></td>
                                    <td><?= htmlspecialchars($activity['max_points']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </section>
</body>
</html>
