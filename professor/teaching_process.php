<?php
include 'pro_name.php';

// Fetch allotted subjects for the professor directly in PHP
$subjects = [];
$query = "SELECT ac.subject_id, s.subject_name, s.semester, s.branch 
          FROM assigned_class AS ac
          JOIN subjects AS s ON ac.subject_id = s.subject_id
          WHERE ac.professor_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $professor_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}
$stmt->close();
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
                        <li><a class="active" href="#">Teaching Process</a></li>
                    </ul>
                </div>
            </div>

            <br>
            <div class="form-container" style="max-width: 400px; margin-top:0">
                <h2 class="intro">Teaching Process</h2>
                <form id="classContributionForm" enctype="multipart/form-data">
                    <input type="hidden" name="professor_id" value="<?php echo htmlspecialchars($professor_id); ?>">

                    <div class="input-group">
                        <label for="subjectSelect">Subject:</label>
                        <select name="subject_id" id="subjectSelect">
                            <option value="" disabled selected>Select a Subject</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?php echo $subject['subject_id']; ?>">
                                    <?php echo htmlspecialchars($subject['subject_name'] . " - Sem " . $subject['semester'] . " (" . $subject['branch'] . ")"); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="scheduledClasses">Total Scheduled Classes:</label>
                        <input type="number" name="scheduled_classes" id="scheduledClasses" placeholder="Enter scheduled classes">
                    </div>

                    <div class="input-group">
                        <label for="heldClasses">Actual Classes Held:</label>
                        <input type="number" name="actual_classes" id="heldClasses" placeholder="Enter held classes">
                    </div>

                    <button type="submit">Submit</button>
                </form>
            </div>
        </main>
    </section>

    <script>
        $(document).ready(function () {
            // Form validation and submission with SweetAlert
            $('#classContributionForm').on('submit', function (event) {
                event.preventDefault();

                // Get form values
                const scheduledClasses = parseInt($('#scheduledClasses').val());
                const heldClasses = parseInt($('#heldClasses').val());
                const subjectId = $('#subjectSelect').val();

                // Validation checks
                if (!subjectId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please select the subject.'
                    });
                    return;
                }

                if (isNaN(scheduledClasses) || scheduledClasses <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Total scheduled classes must be a positive number.'
                    });
                    return;
                }

                if (isNaN(heldClasses) || heldClasses < 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Actual classes held cannot be negative.'
                    });
                    return;
                }

                if (heldClasses > scheduledClasses) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Actual classes held cannot exceed scheduled classes.'
                    });
                    return;
                }

                // Form is valid, proceed with AJAX submission
                let formData = new FormData(this);
                $.ajax({
                    url: 'submit_teaching_process.php',  // Script to handle form submission
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        let res = JSON.parse(response);
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Submission Successful!',
                                text: 'Your class contribution has been successfully recorded.',
                                showConfirmButton: true,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Submission Failed',
                                text: res.message
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while submitting your data. Please try again.'
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>
