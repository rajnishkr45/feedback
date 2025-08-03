<?php
include 'pro_name.php';
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
                        <label for="scheduledClasses">Total Scheduled Classes:</label>
                        <input type="number" name="scheduled_classes" id="scheduledClasses" placeholder="Enter total scheduled classes">
                    </div>

                    <div class="input-group">
                        <label for="heldClasses">Total Classes Held:</label>
                        <input type="number" name="actual_classes" id="heldClasses" placeholder="Enter total classes held">
                    </div>

                    <button type="submit">Submit</button>
                </form>
            </div>
        </main>
    </section>

    <script>
        $(document).ready(function () {
            Swal.fire({
                icon: 'info',
                title: 'Important!',
                text: '⚠️ You can submit teaching process only ONCE per session. Please submit at the end of the session.',
                confirmButtonText: 'Got it'
            });

            $('#classContributionForm').on('submit', function (event) {
                event.preventDefault();

                const scheduledClasses = parseInt($('#scheduledClasses').val());
                const heldClasses = parseInt($('#heldClasses').val());

                if (isNaN(scheduledClasses) || scheduledClasses <= 0) {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Enter valid scheduled classes.' });
                    return;
                }

                if (isNaN(heldClasses) || heldClasses < 0) {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Enter valid held classes.' });
                    return;
                }

                if (heldClasses > scheduledClasses) {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Held classes cannot exceed scheduled classes.' });
                    return;
                }

                $.ajax({
                    url: 'submit_teaching_process.php',
                    type: 'POST',
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        const res = JSON.parse(response);
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Submitted!',
                                text: res.message
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed!',
                                text: res.message
                            });
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
