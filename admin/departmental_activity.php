<?php
include 'admin_name.php';
include '../endpoint/academic_session.php';

$session = getActiveSession($conn);
$current_session_id = $session ? $session['session_id'] : 0;

// Fetch departmental activities for current session
$query = "SELECT da.id, da.activity_name, da.details, da.points, da.proof, da.created_at,
                 p.name AS professor_name, p.dept AS professor_dept
          FROM departmental_activities da
          JOIN professors p ON da.professor_id = p.prof_id
          WHERE da.session_id = ?
          ORDER BY da.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $current_session_id);
$stmt->execute();
$result = $stmt->get_result();
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
                    <li><a class="active" href="#">Assign Departmental Points</a></li>
                </ul>
            </div>
        </div>

        <div class="table-data">
            <div class="order">
                <div class="head"><h3>Assign Points to Departmental Activities</h3></div>

                <table style="font-size:14px;">
                    <thead>
                        <tr>
                            <th>SN.</th>
                            <th>Professor Name</th>
                            <th>Department</th>
                            <th>Activity Name</th>
                            <th>Details</th>
                            <th>Proof</th>
                            <th>Current Points</th>
                            <th>Assign / Update</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $serial = 0; while ($row = $result->fetch_assoc()): $serial++; ?>
                        <tr id="row-<?= $row['id']; ?>">
                            <td><?= $serial; ?></td>
                            <td><?= htmlspecialchars($row['professor_name']); ?></td>
                            <td><?= htmlspecialchars($row['professor_dept']); ?></td>
                            <td><?= htmlspecialchars($row['activity_name']); ?></td>
                            <td><?= htmlspecialchars($row['details']); ?></td>
                            <td>
                                <?php if ($row['proof']): ?>
                                    <a href="../<?= htmlspecialchars($row['proof']); ?>" target="_blank" style="color:#007BFF;text-decoration:none;">View Proof</a>
                                <?php else: ?>
                                    No Proof
                                <?php endif; ?>
                            </td>
                            <td class="points-display"><?= $row['points'] !== null ? $row['points'] : 'Pending'; ?></td>
                            <td>
                                <select class="assign-points" data-id="<?= $row['id']; ?>" style="padding:3px;border-radius:3px;border:1px solid #ccc;">
                                    <option value="">--Select--</option>
                                    <?php for ($i = 0; $i <= 5; $i += 0.5): ?>
                                        <option value="<?= $i; ?>" <?= $row['points'] == $i ? 'selected' : ''; ?>><?= $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</section>

<script>
$(document).ready(function () {
    $('.assign-points').on('change', function () {
        const activityId = $(this).data('id');
        const points = $(this).val();

        if (points === "") return;

        $.ajax({
            url: 'update_departmental_point.php',
            type: 'POST',
            data: { id: activityId, points: points },
            success: function (response) {
                const res = JSON.parse(response);
                Swal.fire({
                    icon: res.success ? 'success' : 'error',
                    title: res.success ? 'Updated!' : 'Failed!',
                    text: res.message
                });

                if (res.success) {
                    // Update the points without page reload
                    $(`#row-${activityId} .points-display`).text(points);
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong. Try again later.'
                });
            }
        });
    });
});
</script>
</body>
</html>
