<?php
include 'pro_name.php';
$professor_id = $professor['prof_id']; // Assuming you want to store the professor ID.
?>

<!DOCTYPE html>
<html lang="en">

<?php
include 'dependencies.php';
?>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="../css/form.css?v=1">

<body>


    <!-- SIDEBAR -->
    <?php
    include 'navbar.php';
    ?>
    <!-- SIDEBAR -->


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


        <main>
            <div class="head-title">
                <div class="left">
                    <ul class="breadcrumb">
                        <li>
                            <a href="#">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Contribution</a>
                        </li>
                    </ul>
                </div>
            </div>

            <br>
            <div class="form-container" style="max-width: 450px; margin-top:0">
                <h2 class="intro">Contribution to Society</h2>
                <form id="feedbackForm" enctype="multipart/form-data">
                    <input type="hidden" name="professor_id" value="<?php echo htmlspecialchars($professor_id); ?>">
                    <div class="input-group">
                        <label for="eventName">Event Name:</label>
                        <select name="event_name" id="eventSelect" required>
                            <option value="" disabled selected>Select an Event</option>
                            <option value="Induction Program">Induction Program</option>
                            <option value="Unnat Bharat Abhiyan">Unnat Bharat Abhiyan</option>
                            <option value="Yoga Classes">Yoga Classes</option>
                            <option value="Blood Donation">Blood Donation</option>
                            <option value="other">Other</option>
                        </select>
                        <input type="text" id="customEventName" name="custom_event_name" placeholder="Enter Event Name"
                            style="display:none; margin-top:15px;">
                    </div>

                    <div class="input-group">
                        <label for="date">Event Date:</label>
                        <input type="date" name="date" id="date" placeholder="Select date" required>
                    </div>

                    <div class="input-group">
                        <label for="role">Your Role:</label>
                        <input type="text" name="role" id="role" placeholder="What was your role?" required>
                    </div>

                    <div class="input-group">
                        <label for="contribution">Your Contribution:</label>
                        <textarea name="contribution" id="contribution" placeholder="Explain your contribution"
                            required></textarea>
                    </div>

                    <div class="input-group">
                        <label for="proof_image">Upload Proof (max 250KB):</label>
                        <input type="file" name="proof_image" id="proof_image" accept="image/*" required>
                    </div>

                    <button type="submit">Submit</button>
                </form>
            </div>
        </main>
    </section>

    <script>
        $(document).ready(function () {
            // Toggle custom event name input based on dropdown selection
            $('#eventSelect').on('change', function () {
                if ($(this).val() === 'other') {
                    $('#customEventName').show();
                } else {
                    $('#customEventName').hide().val(''); // Hide and clear input
                }
            });

            $('#feedbackForm').on('submit', function (event) {
                event.preventDefault();

                // Validate form inputs
                var eventSelect = $('#eventSelect').val();
                var customEventName = $('#customEventName').val();
                var eventName = eventSelect === 'other' ? customEventName : eventSelect;
                var eventDate = $('#date').val();
                var role = $('#role').val();
                var contribution = $('#contribution').val();
                var proofImage = $('#proof_image').val();

                if (!eventName || !eventDate || !role || !contribution || !proofImage) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please fill in all fields and upload a proof image.'
                    });
                    return;
                }

                var formData = new FormData(this);
                $.ajax({
                    url: 'uploadEvent.php',
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        var res = JSON.parse(response);
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Feedback Submitted!',
                                text: 'Your feedback has been successfully submitted.',
                                showConfirmButton: false,
                                timer: 3000
                            }).then(() => {
                                window.location.href = 'report.php';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Submission Failed!',
                                text: res.message
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while submitting your feedback. Please try again.'
                        });
                    }
                });
            });
        });

    </script>

</body>

</html>