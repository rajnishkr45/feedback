<?php
include 'pro_name.php';

// Fetch professor role & department
$stmt = $conn->prepare("SELECT role, dept FROM professors WHERE email = ?");
$stmt->bind_param("s", $professor_email);
$stmt->execute();
$result1 = $stmt->get_result();

$proRole = $proDept = null;
if ($result1->num_rows > 0) {
    $row1 = $result1->fetch_assoc();
    $proRole = $row1['role'];
    $proDept = $row1['dept'];
}

// Prepare query based on role
if ($proRole == 'HOD') {
    $sql = "SELECT e.feedback_id, e.event_name, e.event_date, e.role AS event_role, e.contribution, e.points, e.proof_image, e.created_at,
                   p.name, p.dept, p.role AS professor_role
            FROM eventfeedback e
            INNER JOIN professors p ON e.professor_id = p.prof_id 
            WHERE p.dept = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $proDept);
} else {
    $sql = "SELECT feedback_id, event_name, event_date, e.role AS event_role, e.contribution, e.proof_image, e.points, e.created_at 
            FROM eventfeedback e 
            WHERE professor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $professor_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'dependencies.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<body>

    <!-- SIDEBAR -->
    <?php include 'navbar.php'; ?>

    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
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
                <i class='bx bxs-bell'></i>
                <span class="num">9</span>
            </a>
            <a href="#" class="profile">
                <img src="../dp/<?php echo $profilePicture ?? 'default.png'; ?>" alt="Profile Pic">
            </a>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Contribution to Society</a></li>
                    </ul>
                </div>
                <a href="#" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Download PDF</span>
                </a>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Contribution to Society</h3>
                    </div>

                    <?php if ($result && $result->num_rows > 0) { ?>
                        <table class="professor-table" style="font-size:14px;">
                            <thead>
                                <tr>
                                    <th>SN.</th>
                                    <?php if ($proRole == 'HOD') echo "<th>Prof Name</th>"; ?>
                                    <th>Event Name</th>
                                    <th>Event Date</th>
                                    <th>Role</th>
                                    <th>Contribution</th>
                                    <th>Proof</th>
                                    <?php if ($proRole == 'HOD') echo "<th>Points</th>"; ?>
                                    <th>Submitted on</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $serial = 1;
                                while ($row = $result->fetch_assoc()) { ?>
                                    <tr>
                                        <td><?= $serial++ ?></td>
                                        <?php if ($proRole == 'HOD') echo "<td>".htmlspecialchars($row['name'])."</td>"; ?>
                                        <td><?= htmlspecialchars($row['event_name']) ?></td>
                                        <td><?= htmlspecialchars($row['event_date']) ?></td>
                                        <td><?= htmlspecialchars($row['event_role']) ?></td>
                                        <td><?= htmlspecialchars($row['contribution']) ?></td>
                                        <td>
                                            <?php if ($row['proof_image']) { ?>
                                                <center>
                                                    <a href="../uploads/<?= htmlspecialchars($row['proof_image']) ?>" download style="text-decoration:none; font-size:20px; color:#ffc941;">
                                                        <i class="bx bxs-download"></i>
                                                    </a>
                                                </center>
                                            <?php } else { echo "No Proof"; } ?>
                                        </td>
                                        <?php if ($proRole == 'HOD') { ?>
                                            <td>
                                                <select class="marks-update" data-feedback-id="<?= $row['feedback_id'] ?>" style="padding:3px; border-radius:3px; border:1px solid #479ff7;">
                                                    <?php 
                                                    for ($i=0; $i<=5; $i+=0.5) {
                                                        $selected = ($row['points'] == $i) ? "selected" : "";
                                                        echo "<option value='$i' $selected>$i</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </td>
                                        <?php } ?>
                                        <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else {
                        echo "<p>No feedback available at the moment.</p>";
                    } ?>
                </div>
            </div>
        </main>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function () {
            $(".marks-update").on("change", function () {
                var feedbackId = $(this).data("feedback-id");
                var newMarks = $(this).val();

                $.ajax({
                    url: "assign_cts_points.php",
                    type: "POST",
                    data: { feedback_id: feedbackId, marks: newMarks },
                    success: function (response) {
                        try {
                            var result = JSON.parse(response.trim());
                            if (result.success) {
                                Swal.fire("✅ Success", "Marks updated successfully!", "success");
                            } else {
                                Swal.fire("❌ Error", result.message, "error");
                            }
                        } catch (e) {
                            Swal.fire("⚠️ Error", "Invalid response from server!", "error");
                        }
                    },
                    error: function () {
                        Swal.fire("❌ Error", "AJAX request failed!", "error");
                    }
                });
            });
        });
    </script>
</body>
</html>
