<?php
include 'admin_name.php';
?>
<!DOCTYPE html>
<html lang="en">

<?php
include './dependencies.php';
?>
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
                            <a class="active" href="#">Add admin</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="form-container">
                <h2 class="intro">Add Admin</h2>
                <form id="adminForm" method="POST" action="addAdmin.php" onsubmit="return validateForm()">
                    <div class="input-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" placeholder="Enter Admin Name">
                    </div>
                    <div class="input-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" placeholder="Enter valid email">
                    </div>
                    <div class="input-group">
                        <label for="phone">Phone:</label>
                        <input type="text" id="phone" name="phone" placeholder="Enter 10 digit phone no.">
                    </div>
                    <div class="input-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" placeholder="••••••••">
                    </div>
                    <button type="submit">Add Admin</button>
                </form>
            </div>
        </main>
    </section>
    <script>
        function validateForm() {
            let name = document.getElementById('name').value.trim();
            let email = document.getElementById('email').value.trim();
            let phone = document.getElementById('phone').value.trim();
            let password = document.getElementById('password').value.trim();

            // Name validation
            if (name === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Name is required!',
                });
                return false;
            }

            // Email validation
            if (email === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Email is required!',
                });
                return false;
            }

            // Phone validation
            if (phone === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Phone number is required!',
                });
                return false;
            } else if (!/^\d{10}$/.test(phone)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Phone number must be 10 digits!',
                });
                return false;
            }

            // Password validation
            if (password === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Password is required!',
                });
                return false;
            } else if (password.length < 6) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Password must be at least 6 characters long!',
                });
                return false;
            }

            return true;
        }
    </script>
</body>

</html>