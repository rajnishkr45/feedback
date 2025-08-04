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
                        <li><a class="active" href="#">Departmental Activities</a></li>
                    </ul>
                </div>
            </div>

            <br>
            <div class="form-container" style="max-width: 450px; margin-top:0">
                <h2 class="intro">Upload Departmental Activity</h2>

                <form id="departmentalActivityForm" enctype="multipart/form-data">
                    <input type="hidden" name="professor_id" value="<?php echo htmlspecialchars($professor_id); ?>">

                    <div class="input-group">
                        <label for="activityName">Activity Name:</label>
                        <input type="text" name="activity_name" id="activityName" placeholder="Enter activity name">
                    </div>

                    <div class="input-group">
                        <label for="details">Details:</label>
                        <textarea name="details" id="details" placeholder="Enter details"></textarea>
                    </div>

                    <div class="input-group">
                        <label for="proof">Upload Proof (Max 250KB):</label>
                        <input type="file" name="proof" id="proof" accept=".jpg,.jpeg,.png,.gif">
                    </div>

                    <button type="submit">Submit Activity</button>
                </form>
            </div>
        </main>
    </section>

    <script>
        $(document).ready(function () {
            // File size validation
            $('#proof').on('change', function () {
                if (this.files[0].size > 250 * 1024) {
                    Swal.fire({ icon: 'error', title: 'File too large', text: 'Proof must be under 250KB!' });
                    $(this).val('');
                }
            });

            // Form submission
            $('#departmentalActivityForm').on('submit', function (event) {
                event.preventDefault();

                const activityName = $('#activityName').val().trim();
                const details = $('#details').val().trim();

                if (!activityName) {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Please enter activity name.' });
                    return;
                }

                if (!details) {
                    Swal.fire({ icon: 'error', title: 'Oops...', text: 'Please enter activity details.' });
                    return;
                }

                const formData = new FormData(this);

                $.ajax({
                    url: 'upload_departmental_activity.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        const res = JSON.parse(response);
                        if (res.success) {
                            Swal.fire({ icon: 'success', title: 'Success!', text: res.message });
                            $('#departmentalActivityForm')[0].reset();
                        } else {
                            Swal.fire({ icon: 'warning', title: 'Warning', text: res.message });
                        }
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Something went wrong!' });
                    }
                });
            });
        });
    </script>
</body>

</html>