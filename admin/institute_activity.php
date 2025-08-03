<?php
include 'admin_name.php';

// Fetch professors securely
$professors = [];
$query = "SELECT prof_id, name FROM professors ORDER BY name ASC";
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
            <a href="#" class="profile">
                <img src="../dp/<?php echo htmlspecialchars($profilePicture ?? 'default.png'); ?>" alt="Profile Pic">
            </a>
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

            <div class="form-container" style="max-width: 450px; margin-top:0">
                <h2 class="intro">Assign Institute Activity</h2>
                <form id="assignTaskForm">
                    <div class="input-group">
                        <label for="professorSelect">Professor:</label>
                        <select name="professor_id" id="professorSelect" required>
                            <option value="" disabled selected>Select a Professor</option>
                            <?php foreach ($professors as $professor): ?>
                                <option value="<?= intval($professor['prof_id']); ?>">
                                    <?= htmlspecialchars($professor['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="roleSelect">Role:</label>
                        <select name="role" id="roleSelect" required>
                            <option value="" disabled selected>Select a Role</option>
                            <option value="Head of Department">Head of Department</option>
                            <option value="Coordinator">Coordinator</option>
                            <option value="Warden">Warden</option>
                            <option value="Training and Placement Officer">Training and Placement Officer</option>
                            <option value="Estate Officer">Estate Officer</option>
                        </select>
                    </div>

                    <div class="input-group">
                        <label for="extra">Extra Info:</label>
                        <input type="text" name="extra" id="extra" placeholder="Enter extra info">
                    </div>

                    <button type="submit">Assign Task</button>
                </form>

                <br>Check institute activity result?
                <a href="view_institute_activity.php"><b>Click Here</b></a>
            </div>
        </main>
    </section>

    <script>
        $(document).ready(function () {

            Swal.fire({
                icon: 'info',
                title: 'Important!',
                text: '⚠️ Only 2 Instute activities can be assigned per professor in a session.',
                confirmButtonText: 'Got it'
            });


            $('#assignTaskForm').on('submit', function (event) {
                event.preventDefault();

                let professorId = $('#professorSelect').val();
                let role = $('#roleSelect').val();

                if (!professorId || !role) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Missing Fields',
                        text: 'Please select both professor and role.'
                    });
                    return;
                }

                $.ajax({
                    url: 'submit_institute_activity.php',
                    type: 'POST',
                    data: $(this).serialize(), // ✅ Sends form data safely
                    success: function (response) {
                        let res = JSON.parse(response);
                        Swal.fire({
                            icon: res.success ? 'success' : 'warning',
                            title: res.success ? 'Task Assigned!' : 'Warning',
                            text: res.message
                        });
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong. Please try again.'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>