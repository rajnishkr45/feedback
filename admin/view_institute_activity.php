<?php
include 'admin_name.php';

// Fetch the list of professors and their activities
$professors = [];
$query = "SELECT 
            ia.id, 
            ia.professor_id, 
            ia.role, 
            ia.extra_info, 
            ia.status, 
            ia.points, 
            ia.image_proof, 
            p.name AS professor_name, 
            p.dept AS professor_dept 
          FROM 
            institute_activity ia
          JOIN 
            professors p 
          ON 
            ia.professor_id = p.prof_id";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $professors[] = $row;
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
            <a href="#" class="notification"><i class='bx bxs-bell'></i><span class="num">9</span></a>
            <a href="#" class="profile"><img src="../dp/<?php echo $profilePicture ?? 'default.png'; ?>" alt="Profile Pic"></a>
        </nav>
        <main>
            <div class="head-title">
                <div class="left">
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Institute Activity</a></li>
                    </ul>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Assign Points for Submitted Proofs</h3>
                    </div>
                    <table style="font-size: 14px;">
                        <thead>
                            <tr>
                                <th>SN.</th>
                                <th>Professor Name</th>
                                <th>Department</th>
                                <th>Role</th>
                                <th>Extra Info</th>
                                <th>Status</th>
                                <th>Proof</th>
                                <th>Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $serial = 0;
                            foreach ($professors as $task):
                                $serial++;
                                ?>
                                <tr>
                                    <td><?= $serial; ?></td>
                                    <td><?= htmlspecialchars($task['professor_name']); ?></td>
                                    <td><?= htmlspecialchars($task['professor_dept']); ?></td>
                                    <td><?= htmlspecialchars($task['role']); ?></td>
                                    <td><?= htmlspecialchars($task['extra_info']); ?></td>
                                    <td><?= htmlspecialchars($task['status']); ?></td>
                                    <td>
                                        <?php if ($task['image_proof']): ?>
                                            <a href="<?= htmlspecialchars($task['image_proof']); ?>" target="_blank" class="btn">Download Proof</a>
                                        <?php else: ?>
                                            <p>No proof uploaded</p>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($task['status'] === 'Approved'): ?>
                                            <span><?= htmlspecialchars($task['points']); ?> Points</span>
                                        <?php else: ?>
                                            <input type="number" class="pointsInput" data-id="<?= $task['id']; ?>" min="0" placeholder="Points" />
                                            <button class="btn assignPointsBtn" data-id="<?= $task['id']; ?>">Assign</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
                $(document).on('click', '.assignPointsBtn', function () {
                    const activityId = $(this).data('id');
                    const points = $(`.pointsInput[data-id="${activityId}"]`).val();

                    if (points === "") {
                        Swal.fire({
                            icon: 'error',
                            title: 'Points Required',
                            text: 'Please enter the points before assigning.',
                        });
                        return;
                    }

                    $.ajax({
                        url: 'institute_activity_update_points.php',
                        type: 'POST',
                        data: { activity_id: activityId, points: points },
                        success: function (response) {
                            const res = JSON.parse(response);
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Points Assigned',
                                    text: res.message,
                                    showConfirmButton: true,
                                }).then(() => {
                                    location.reload(); // Reload to reflect changes
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: res.message,
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while assigning points.',
                            });
                        },
                    });
                });
            </script>

        </main>
    </section>
</body>

</html>
