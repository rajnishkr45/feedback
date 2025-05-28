<?php
session_start();

if (isset($_SESSION['admin_email']) || isset($_SESSION['prof_email']) || isset($_SESSION['email'])) {
    switch (true) {
        case isset($_SESSION['admin_email']):
            header('Location: admin/dashboard'); // Redirect for admin
            break;

        case isset($_SESSION['prof_email']):
            header('Location: professor/dashboard'); // Redirect for professor
            break;

        case isset($_SESSION['email']):
            header('Location: student/dashboard'); // Redirect for general user
            break;
    }
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DCE + AICTE | Login</title>

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/form.css">
    <link rel="stylesheet" href="css/home.css">

</head>

<body>
    <nav class="navbar">
        <img src="img/DCE logo.jpg" alt="Logo" class="logo" aria-label="logo">
        <ul class="nav-links">
            <a href="./">Home</a>
            <a href="./#about">About</a>
            <a href="./#developer">Contacts</a>
            <a href="login" class="active">Login <span class="material-symbols-outlined">lock</span></a>
            <a href="#" id="theme">
                <span class="material-symbols-outlined">dark_mode</span></a>
        </ul>
        <div class="menu-icon">
            <div class="bar"></div>
            <div class="bar"></div>
            <div class="bar"></div>
        </div>
    </nav>

    <div class="form-container" style="max-width: 400px; margin-top:100px;">
        <h2 class="intro">Sign in to your account</h2>
        <form id="loginForm">
            <div class="role-selection">
                <div class="merge">
                    <label for="role"><strong>Role :</strong></label>
                </div>

                <div class="merge">
                    <input type="radio" id="student" name="role" value="student">
                    <label for="student">Student</label>
                </div>

                <div class="merge">
                    <input type="radio" id="professor" name="role" value="professor">
                    <label for="professor">Prof.</label>
                </div>

                <div class="merge">
                    <input type="radio" id="admin" name="role" value="admin">
                    <label for="admin">Admin</label>
                </div>
            </div>
            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" placeholder="Enter valid email">
            </div>
            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" placeholder="Enter your password">
                <span id="sendOtpLink"><i class="fa-solid fa-eye"></i></span>
            </div>
            <button type="submit">Login <i class="fa-solid fa-arrow-right"></i></button>
        </form>
        <br>Don't have account ? <a href="register">Sign up</a>
        <br>
        Forgot Password ? <a href="reset">Reset</a>
        <br>
    </div>
    <script src="js/navbar.js"></script>
    <!-- <script src="js/theme.js"></script> -->

    <script>
        $(document).ready(function () {
            $('#sendOtpLink').on('click', function () {
                let passwordInput = $('#password');
                let passStatus = $(this).find('i');

                if (passwordInput.attr('type') === 'password') {
                    passwordInput.attr('type', 'text');
                    passStatus.removeClass('fa-eye').addClass('fa-eye-slash');
                } else {
                    passwordInput.attr('type', 'password');
                    passStatus.removeClass('fa-eye-slash').addClass('fa-eye');
                }
            });

            document.getElementById('loginForm').addEventListener('submit', function (event) {
                event.preventDefault();
                if (validateForm()) {
                    submitForm();
                }
            });
        });

        function validateForm() {
            const role = document.querySelector('input[name="role"]:checked');
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!role) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Role !',
                    text: 'Please select a role!'
                });
                return false;
            }

            if (!email) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing email !',
                    text: 'Please enter your email!'
                });
                return false;
            }

            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid email!',
                    text: 'Please enter a valid email address!'
                });
                return false;
            }

            if (!password) {
                Swal.fire({
                    icon: 'error',
                    title: 'Missing Password !',
                    text: 'Please enter your password!'
                });
                return false;
            }

            return true;
        }

        function submitForm() {
            const formData = new FormData(document.getElementById('loginForm'));

            fetch('endpoint/login.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Login Successful!',
                            text: 'You will be redirected to your dashboard in 3 seconds!',
                            showConfirmButton: false,
                            timer: 3000
                        }).then(() => {
                            // Get role from form data
                            const role = formData.get('role'); // Ensure formData is correctly defined and accessible
                            let redirectUrl = '';

                            // Determine redirect URL based on role
                            switch (role) {
                                case 'admin':
                                    redirectUrl = 'admin/dashboard';
                                    break;
                                case 'professor':
                                    redirectUrl = 'professor/dashboard';
                                    break;
                                case 'student':
                                    redirectUrl = 'student/dashboard';
                                    break;
                                default:
                                    redirectUrl = 'index.html'; // Fallback in case of unknown role
                            }
                            // Redirect to the determined URL
                            window.location.href = redirectUrl;
                        });

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Login Failed !',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred. Please try again.'
                    });
                });


        }
    </script>
</body>

</html>