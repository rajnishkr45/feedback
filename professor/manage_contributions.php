<?php
include 'pro_name.php'; // contains $conn and $professor_id

// Fetch professor name
$stmt = $conn->prepare("SELECT name FROM professors WHERE prof_id = ?");
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$res = $stmt->get_result();
$profName = $res->fetch_assoc()['name'] ?? '';

// Fetch institute activities
$instituteActivities = [];
$result1 = $conn->prepare("SELECT role, extra_info, status, image_proof, created_at FROM institute_activity WHERE professor_id = ?");
$result1->bind_param("i", $professor_id);
$result1->execute();
$data1 = $result1->get_result();
while ($row = $data1->fetch_assoc())
    $instituteActivities[] = $row;

// Fetch teaching process
$teachingProcess = [];
$result2 = $conn->prepare("SELECT scheduled_classes, actual_classes, contribution_date FROM teaching_process WHERE professor_id = ?");
$result2->bind_param("i", $professor_id);
$result2->execute();
$data2 = $result2->get_result();
while ($row = $data2->fetch_assoc())
    $teachingProcess[] = $row;

// Fetch departmental activities
$departmentalActivities = [];
$result3 = $conn->prepare("SELECT activity_name, details, proof, created_at FROM departmental_activities WHERE professor_id = ?");
$result3->bind_param("i", $professor_id);
$result3->execute();
$data3 = $result3->get_result();
while ($row = $data3->fetch_assoc())
    $departmentalActivities[] = $row;

// Fetch contribution to society
$contributions = [];
$result4 = $conn->prepare("SELECT event_name, event_date, role, contribution, proof_image, created_at FROM eventfeedback WHERE professor_id = ?");
$result4->bind_param("i", $professor_id);
$result4->execute();
$data4 = $result4->get_result();
while ($row = $data4->fetch_assoc())
    $contributions[] = $row;
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'dependencies.php'; ?>

<body>

    <style>
        td {
            text-align: center;
        }

        td a {
            font-size: 20px;
            color: #595858;
        }
    </style>

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
            <a href="#" class="notification">
                <i class='bx bx-bell'></i>
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
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">My Activities & Contributions</a></li>
                    </ul>
                </div>
            </div>
          
            <!-- Teaching Process -->
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Teaching Process</h3>
                    </div>
                    <?php if (!empty($teachingProcess)) { ?>
                        <table class="professor-table">
                            <thead>
                                <tr>
                                    <th>SN.</th>
                                    <th>Scheduled Classes</th>
                                    <th>Actual Classes</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn = 1;
                                foreach ($teachingProcess as $row): ?>
                                    <tr>
                                        <td><?= $sn++ ?></td>
                                        <td><?= htmlspecialchars($row['scheduled_classes']) ?></td>
                                        <td><?= htmlspecialchars($row['actual_classes']) ?></td>
                                        <td><?= date('d-m-Y', strtotime($row['contribution_date'])) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php } else
                        echo "<p>No teaching process data.</p>"; ?>
                </div>
            </div>

            <!-- Institute Activities -->
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Institute Activities</h3>
                    </div>
                    <?php if (!empty($instituteActivities)) { ?>
                        <table class="professor-table">
                            <thead>
                                <tr>
                                    <th>SN.</th>
                                    <th>Role</th>
                                    <th>Extra Info</th>
                                    <th>Proof</th>
                                    <th>Created On</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn = 1;
                                foreach ($instituteActivities as $row): ?>
                                    <tr>
                                        <td><?= $sn++ ?></td>
                                        <td><?= htmlspecialchars($row['role']) ?></td>
                                        <td><?= htmlspecialchars($row['extra_info']) ?></td>
                                        <td>
                                            <?php if ($row['image_proof']): ?>
                                                <a href="../uploads/<?= htmlspecialchars($row['image_proof']) ?>" target="_blank">
                                                    <i class="bx bx-download"></i>
                                                </a>
                                            <?php else: ?> No Proof <?php endif; ?>
                                        </td>
                                        <td><?= date('d-m-Y', strtotime($row['created_at'])) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php } else
                        echo "<p>No institute activity uploaded.</p>"; ?>
                </div>
            </div>





            <!-- Departmental Activities -->
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Departmental Activities</h3>
                    </div>
                    <?php if (!empty($departmentalActivities)) { ?>
                        <table class="professor-table">
                            <thead>
                                <tr>
                                    <th>SN.</th>
                                    <th>Activity Name</th>
                                    <th>Details</th>
                                    <th>Proof</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn = 1;
                                foreach ($departmentalActivities as $row): ?>
                                    <tr>
                                        <td><?= $sn++ ?></td>
                                        <td><?= htmlspecialchars($row['activity_name']) ?></td>
                                        <td><?= htmlspecialchars($row['details']) ?></td>
                                        <td>
                                            <?php if ($row['proof']): ?>
                                                <a href="../uploads/<?= htmlspecialchars($row['proof']) ?>" target="_blank">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                            <?php else: ?> No Proof <?php endif; ?>
                                        </td>
                                        <td><?= date('d-m-Y', strtotime($row['created_at'])) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php } else
                        echo "<p>No departmental activity found.</p>"; ?>
                </div>
            </div>


            <!-- Contribution to Society -->
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Contribution to Society</h3>
                    </div>
                    <?php if (!empty($contributions)) { ?>
                        <table class="professor-table">
                            <thead>
                                <tr>
                                    <th>SN.</th>
                                    <th>Event Name</th>
                                    <th>Role</th>
                                    <th>Contribution</th>
                                    <th>Proof</th>
                                    <th>Submitted On</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sn = 1;
                                foreach ($contributions as $row): ?>
                                    <tr>
                                        <td><?= $sn++ ?></td>
                                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                                        <td><?= htmlspecialchars($row['role']) ?></td>
                                        <td><?= htmlspecialchars($row['contribution']) ?></td>
                                        <td>
                                            <?php if ($row['proof_image']): ?>
                                                <a href="../uploads/<?= htmlspecialchars($row['proof_image']) ?>" target="_blank">
                                                    <i class="bx bx-show"></i>
                                                </a>
                                            <?php else: ?> No Proof <?php endif; ?>
                                        </td>
                                        <td><?= date('d-m-Y', strtotime($row['created_at'])) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php } else
                        echo "<p>No contributions yet.</p>"; ?>
                </div>
            </div>
        </main>
    </section>
</body>

</html>