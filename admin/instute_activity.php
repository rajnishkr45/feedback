<?php
include 'admin_name.php';

// Fetch the list of professors from the database
$professors = [];
$query = "SELECT prof_id, name FROM professors";
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
            <a href="#" class="profile"><img src="../dp/<?php echo $profilePicture ?? 'default.png'; ?>"
                    alt="Profile Pic"></a>
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

            <br>
            <div class="form-container" style="max-width: 400px; margin-top:0">
                <h2 class="intro">Assign institute activity</h2>
                <form id="assignTaskForm" enctype="multipart/form-data">
                    <div class="input-group">
                        <label for="professorSelect">Professor:</label>
                        <select name="professor_id" id="professorSelect">
                            <option value="" disabled selected>Select a Professor</option>
                            <?php foreach ($professors as $professor): ?>
                                <option value="<?php echo $professor['prof_id']; ?>">
                                    <?php echo htmlspecialchars($professor['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="roleSelect">Role:</label>
                        <select name="role" id="roleSelect">
                            <option value="" disabled selected>Select a Role</option>
                            <option value="Head of Department">Head of Department</option>
                            <option value="Coordinator">Coordinator</option>
                            <option value="Warden">Warden</option>
                            <option value="Training and Placement Officer">Training and Placement Officer</option>
                            <option value="Estate Officer">Estate Officer</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="extra">Extra info:</label>
                        <input type="text" name="extra" id="extra" placeholder="Enter extra info">
                    </div>

                    <button type="submit">Assign Task</button>
                </form>
              
              <br>  Check institute activity result ?<a href="view_institute_activity.php">Click Here</a>
            </div>
        </main>
    </section>

    <script>
        $(document).ready(function () {
            // Form validation and submission with SweetAlert
            $('#assignTaskForm').on('submit', function (event) {
                event.preventDefault();

                // Get form values
                const professorId = $('#professorSelect').val();
                const role = $('#roleSelect').val();
                const extra = $('#extra').val();

                // Validation checks
                if (!professorId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please select a professor.'
                    });
                    return;
                }

                if (!role) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please select a role.'
                    });
                    return;
                }

                // Form is valid, proceed with AJAX submission
                let formData = new FormData(this);
                $.ajax({
                    url: 'submit_institute_activity.php',  // Adjust the URL to your script
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        let res = JSON.parse(response);
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Task Assigned!',
                                text: 'Task successfully assigned to the professor.',
                                showConfirmButton: true,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Task Assignment Failed',
                                text: res.message
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while submitting the task. Please try again.'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>