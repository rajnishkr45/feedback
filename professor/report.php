<?php
include 'pro_name.php'; // Fetch professor details

// Fetch the role and department of the logged-in professor
$query = "SELECT role, dept FROM professors WHERE email = '$professor_email'";
$result1 = $conn->query($query);

if ($result1->num_rows > 0) {
    $row1 = $result1->fetch_assoc();
    $proRole = $row1['role'];
    $proDept = $row1['dept'];

    // Fetch feedback based on the role
    if ($proRole == 'HOD') {
        // If the professor is HOD, show all feedback for the same department
        $sql = "SELECT eventFeedback.feedback_id, eventFeedback.event_name, eventFeedback.event_date, 
                       eventFeedback.role AS event_role, eventFeedback.contribution, eventFeedback.status,eventFeedback.proof_image, eventFeedback.created_at, 
                       professors.name, professors.dept, professors.role AS professor_role
                FROM eventFeedback
                INNER JOIN professors ON eventFeedback.professor_id = professors.prof_id 
                WHERE professors.dept = '$proDept'";
    } else {
        // If not an HOD, show only feedback for the logged-in professor
        $sql = "SELECT feedback_id, event_name, event_date, eventFeedback.role AS event_role, contribution,proof_image, status, created_at 
                FROM eventFeedback WHERE professor_id = '$professor_id'";
    }

    // Execute the query
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">

<?php
include 'dependencies.php';
?>

<body>

    <!-- SIDEBAR -->
    <?php
    include 'navbar.php';
    ?>

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
                        <li>
                            <a href="#">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Analytics</a>
                        </li>
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
                    <?php
                    // Check if the result set contains any rows
                    if ($result && $result->num_rows > 0) {
                        echo "<table class='professor-table' style='font-size:14px'>
            <thead>
                <tr>
                    <th>SN.</th>";

                        // Display 'Prof Name' column only for HODs
                        if ($proRole == 'HOD') {
                            echo "<th>Prof Name</th>";
                        }
                        echo "<th>Event Name</th>
                    <th>Event Date</th>
                    <th>Role</th>
                    <th>Contribution</th>
                    <th>Proof</th>";
                        if ($proRole == 'HOD') {
                            echo " <th>Points</th>";
                        }
                        echo "<th>Submitted on</th>
                </tr>
            </thead>
            <tbody>";

                        // Fetch and display each row of the result set
                        $serial = 0;
                        while ($row = $result->fetch_assoc()) {
                            $serial++;
                            echo "<tr>
                             <td>$serial</td>";

                            // Display professor's name only for HODs
                            if ($proRole == 'HOD') {
                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            }
                            echo "<td>" . htmlspecialchars($row['event_name']) . "</td>
                        <td>" . htmlspecialchars($row['event_date']) . "</td>
                        <td>" . htmlspecialchars($row['event_role']) . "</td>
                        <td>" . htmlspecialchars($row['contribution']) . "</td>";
                            echo "<td>";
                            if ($row['proof_image']) {
                                // Adding download attribute to enable file download
                                echo "<center><a href='../uploads/" . htmlspecialchars($row['proof_image']) . "' download style='text-decoration:none; font-size:20px; color:#ffc941;'>
                              <i class='bx bxs-download'></i>
                            </a></center>";
                            } else {
                                echo "No Proof";
                            }
                            echo "</td>";

                            // Check if the professor is HOD to allow updating the marks
                            if ($proRole == 'HOD') {
                                echo '<td>
                                <select name="marks" style="padding:3px; outline:none; border-radius:3px; border:1px solid #479ff7;" class="marks-update" data-feedback-id="' . htmlspecialchars($row['feedback_id']) . '">';
                                // Generate options for marks from 0 to 5 with increments of 0.5
                                for ($i = 0; $i <= 5; $i += 0.5) {
                                    echo "<option value='$i'" . ($row['status'] == $i ? ' selected' : '') . ">$i</option>";
                                }

                                echo '</select></td>';
                            }
                            echo "<td>" . htmlspecialchars(date('Y-m-d', strtotime($row['created_at']))) . "</td>
                </tr>";
                        }
                        echo "</tbody></table>";
                    } else {
                        echo "<p>No feedback available at the moment.</p>";
                    }

                    // Close the database connection
                    $conn->close();
                    ?>
                </div>
        </main>
    </section>
    <!-- AJAX Script -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            // Listen for changes in the marks dropdown
            $('.marks-update').on('change', function () {
                var feedbackId = $(this).data('feedback-id');
                var newMarks = $(this).val();

                // Send the new marks via AJAX
                $.ajax({
                    url: 'update_status.php',  // Updated to a new file for marks
                    type: 'POST',
                    data: {
                        feedback_id: feedbackId,
                        marks: newMarks
                    },
                    success: function (response) {
                        var result = JSON.parse(response);
                        if (result.success) {
                            Swal.fire({
                                title: 'Marks Updated',
                                icon: 'success',
                                text: 'Marks updated successfully.',
                            });
                        } else {
                            alert('Failed to update marks: ' + result.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        alert('AJAX error: ' + error);
                    }
                });
            });
        });

    </script>
</body>

</html>