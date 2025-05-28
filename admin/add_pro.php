<?php
include 'admin_name.php';
?>
<!DOCTYPE html>
<html lang="en">

<?php
include './dependencies.php';
?>
<link rel="stylesheet" href="../css/form.css?v=1">
<link rel="stylesheet" href="../css/custom.css?v=1">

<body>

    <div id="loadingOverlay" style="display: none;">
        <div class="loader">
            <div class="bar1"></div>
            <div class="bar2"></div>
            <div class="bar3"></div>
            <div class="bar4"></div>
            <div class="bar5"></div>
            <div class="bar6"></div>
            <div class="bar7"></div>
            <div class="bar8"></div>
            <div class="bar9"></div>
            <div class="bar10"></div>
            <div class="bar11"></div>
            <div class="bar12"></div>
        </div>
    </div>


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
                            <a class="active" href="#">Add Professor</a>
                        </li>
                    </ul>
                </div>

            </div>

            <div class="form-container">
                <h2 class="intro">Create Prof. Account</h2>
                <form id="reg-form">

                    <div class="cluster">
                        <div class="input-group">
                            <label for="name">Prof. name:</label>
                            <input type="text" name="name" id="name" placeholder="Enter Prof Name">
                        </div>

                        <div class="input-group">
                            <label for="phone">Phone:</label>
                            <input type="number" name="phone" id="phone" placeholder="Enter phone no.">
                        </div>
                    </div>

                    <div class="cluster">
                        <div class="input-group">
                            <label for="dept">Department</label>
                            <select name="dept" id="dept">
                                <option value="" selected hidden> Choose Department </option>
                                <option value="CSE">CSE</option>
                                <option value="CYB">CYBER</option>
                                <option value="EE">EEE</option>
                                <option value="ME">ME</option>
                                <option value="CE">Civil</option>
                                <option value="FTS">FTS</option>
                                <option value="Applied Sci">Applied Science</option>
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="role">Role</label>
                            <select name="role" id="role">
                                <option value="" selected hidden> Choose Role </option>
                                <option value="Associate Professor">Associate Professor</option>
                                <option value="Assistant Professor">Assistant Professor</option>
                                <option value="Professor">Professor</option>
                                <option value="HOD">HOD</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group">
                        <label for="email">Prof email:</label>
                        <input type="email" name="email" id="email" placeholder="valid email">
                    </div>

                    <div class="terms-container">
                        <input type="checkbox" id="terms" name="terms">
                        <label for="terms">I accept the <a href="#" class="terms">Terms of use</a></label>
                    </div>

                    <button type="submit">Create an account</button>
                </form>
            </div>

        </main>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
        $(document).ready(function () {

            $('#reg-form').on('submit', function (e) {
                e.preventDefault();

                //Form validation
                let name = $('#name').val().trim();
                let phone = $('#phone').val().trim();
                let email = $('#email').val().trim();
                let dept = $('select[name="dept"]').val();
                let role = $('select[name="role"]').val();
                let terms = $('#terms').is(':checked');

                if (name === "" || phone === "" || email === "" || dept === "" || role === "") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Missing Value',
                        text: 'Please fill all the fields!'
                    });
                    return;
                }

                if (!terms) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Please accept the terms and conditions!'
                    });
                    return;
                }

                // Show the loading overlay
                $("#loadingOverlay").css("display", "flex");

                // Submit form via AJAX
                $.ajax({
                    url: 'addPro.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        $("#loadingOverlay").css("display", "none");
                        if (response.status === 'error') {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: response.message
                            });
                        } else if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then(function () {
                                $('#reg-form')[0].reset(); // Clear the form fields
                            });
                        }
                    },
                    error: function () {
                        $("#loadingOverlay").css("display", "none"); // Hide the loading overlay on error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred. Please try again.'
                        });
                    }
                });
            });
        });
    </script>

</body>

</html>