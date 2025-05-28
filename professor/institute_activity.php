<?php
include 'pro_name.php';

// Assuming you have the logged-in professor's ID in $professor_id
$professor_id;

// Fetch the list of tasks assigned to the logged-in professor
$query = "SELECT id, role, extra_info, image_proof, status FROM institute_activity WHERE professor_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<?php include 'dependencies.php'; ?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="../css/form.css?v=2">
<link rel="stylesheet" href="../css/professors.css">

<style>
    .btn {
        padding: 5px 10px;
        background-color: #35b3ed;
        color: white;
        border: none;
        font-size: 14px;
        border-radius: 4px;
        cursor: pointer;
        transition: 0.2s all ease-in-out;
    }


    .btn:hover {
        background-color: #2f9ccf;
    }
</style>

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
            <a href="#" class="notification">
                <i class='bx bxs-bell'></i>
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
                        <li><a class="active" href="#">Instute activity</a></li>
                    </ul>
                </div>
            </div>

            <br>
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Institute activity assigned aasks</h3>
                    </div>
                    <?php if ($result->num_rows > 0): ?>
                        <table style='font-size:14px'>
                            <thead>
                                <tr>
                                    <th>SN.</th>
                                    <th>Role</th>
                                    <th>Extra Info</th>
                                    <th>Status</th>
                                    <th>Proof</th>
                                    <th>Upload Proof</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                $serial = 0;
                                while ($assignment = $result->fetch_assoc()):
                                    $serial++;
                                    ?>

                                    <tr>
                                        <td><?php echo $serial; ?></td>
                                        <td><?php echo htmlspecialchars($assignment['role']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['extra_info']); ?></td>
                                        <td><?php echo htmlspecialchars($assignment['status']); ?></td>
                                        <td>
                                            <?php if ($assignment['image_proof']): ?>
                                                <img src="<?php echo htmlspecialchars($assignment['image_proof']); ?>"
                                                    alt="Proof Image" style="width: 100px; height: auto;">
                                            <?php else: ?>
                                                <p>No proof uploaded</p>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($assignment['status'] == 'Pending'): ?>
                                                <!-- Upload Proof Form -->
                                                <form class="submitProofForm" enctype="multipart/form-data">
                                                    <input type="file" name="image_proof" required>
                                                    <input type="hidden" name="assignment_id"
                                                        value="<?php echo $assignment['id']; ?>">
                                                    <button type="submit" class="btn">Submit Proof</button>
                                                </form>
                                            <?php else: ?>
                                                <p>Completed</p>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No tasks assigned.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </section>

    <script>
        $(document).ready(function () {
            // Form submission for each proof upload
            $('.submitProofForm').on('submit', function (event) {
                event.preventDefault();

                let formData = new FormData(this);
                $.ajax({
                    url: 'instute_activity_submit_proof.php',  // Your proof upload backend script
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        let res = JSON.parse(response);
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Proof Submitted!',
                                text: 'Your proof has been submitted successfully.',
                                showConfirmButton: true,
                            }).then(() => {
                                location.reload(); // Reload to update the table
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Proof Submission Failed',
                                text: res.message
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while submitting the proof. Please try again.'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>