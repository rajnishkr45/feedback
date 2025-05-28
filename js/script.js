$(document).ready(function () {
    $("#sendOtpLink").on("click", function () {
        let emailInput = $("#email").val();
        let name = $("#name").val();
        let regNo = $("#reg_no").val();
        let semester = $("#semester").val();
        let branch = $("#branch").val();
        let passingYear = $("#passing_year").val();
        let phone = $("#phone").val();

        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Form validation
        if (!name) {
            $("#loadingOverlay").css("display", "none");
            Swal.fire({
                icon: "error",
                title: "Missing Name",
                text: "Please enter your name!",
            });
            return;
        }

        if (!regNo || regNo.length != 11) {
            $("#loadingOverlay").css("display", "none");
            Swal.fire({
                icon: "error",
                title: "Invalid Registration No.",
                text: "Please enter a valid 11-digit registration number!",
            });
            return;
        }

        if (!semester) {
            Swal.fire({
                icon: "error",
                title: "Select Semester",
                text: "Please select your semester!",
            });
            return;
        }

        if (!branch) {
            Swal.fire({
                icon: "error",
                title: "Select Department",
                text: "Please choose your department!",
            });
            return;
        }

        if (!phone || phone.length != 10) {
            $("#loadingOverlay").css("display", "none");
            Swal.fire({
                icon: "error",
                title: "Invalid Phone Number",
                text: "Please enter a valid 10-digit phone number!",
            });
            return;
        }


        if (!passingYear) {
            Swal.fire({
                icon: "error",
                title: "Select Passing Year",
                text: "Please choose your passing year!",
            });
            return;
        }

        if (!emailRegex.test(emailInput)) {
            Swal.fire({
                icon: "error",
                title: "Invalid Email",
                text: "Please enter a valid email address",
            });
            return;
        }

        // Disable the link to prevent multiple clicks
        let sendOtpLink = $(this);
        sendOtpLink.css("pointer-events", "none");
        // $("#email").prop("disabled", true);
        $("#loadingOverlay").css("display", "flex");

        // Make an AJAX request to send OTP
        $.ajax({
            url: "endpoint/api.php",
            type: "POST",
            data: {
                action: "sendotp",
                email: emailInput,
                reg_no: regNo,
            },
            success: function (response) {
                if (response === "OTP Sent Successfully") {
                    $("#otp-div").removeClass("hidden");
                    $("#otp").prop("disabled", false);
                    sendOtpLink.html("Sent!").css("color", "green");
                    // Freeze the input fields
                    $("#name").prop("disabled", true);
                    $("#reg_no").prop("disabled", true);
                    $("#semester").prop("disabled", true);
                    $("#branch").prop("disabled", true);
                    $("#phone").prop("disabled", true);
                    $("#passing_year").prop("disabled", true);
                    $("#email").prop("disabled", true);
                } else if (response === "Email already exist") {
                    Swal.fire({
                        icon: "error",
                        title: "Email already exists!",
                        text: "The email you entered is already registered. Please use another email.",
                    });
                    $("#otp").val("");
                    $("#otp-div").addClass("hidden");
                    $("#otp").prop("disabled", true);
                } else if (response === "Registration number already exists") {
                    Swal.fire({
                        icon: "error",
                        title: "Registration number already exists!",
                        text: "The registration number you entered is already registered. Please use another number.",
                    });
                }
                else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response,
                    });
                }
                $("#loadingOverlay").css("display", "none");
                sendOtpLink.css("pointer-events", "auto");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown);
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "An error occurred while processing your request",
                });
                $("#loadingOverlay").css("display", "none");
                sendOtpLink.css("pointer-events", "auto");
                $("#email").prop("disabled", false);
            }
        });
    });

    $("#verifyOtp").on("click", function () {
        let enteredOTP = $("#otp").val();
        let enteredEmail = $("#email").val();

        $("#loadingOverlay").css("display", "flex");

        // Make an AJAX request to verify OTP
        $.ajax({
            url: "endpoint/api.php",
            type: "POST",
            data: {
                action: "verifyotp",
                otp: enteredOTP,
                otp_email: enteredEmail,
            },
            success: function (response) {
                if (response === "OTP Verified") {
                    $("#verifyOtp").html("Verified!").addClass("text-green-500");
                    $("#otp").prop("disabled", true);
                    $("#password").prop("disabled", false);

                    Swal.fire({
                        icon: "success",
                        title: "OTP Verified",
                        text: "You can now proceed with the registration.",
                    });
                } else if (response === "OTP Expired") {
                    Swal.fire({
                        icon: "error",
                        title: "Expired OTP",
                        text: "Please resend a new OTP.",
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Invalid OTP",
                        text: "Please enter the correct OTP.",
                    });
                }
                $("#loadingOverlay").css("display", "none");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown);
                $("#loadingOverlay").css("display", "none");
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "An error occurred while processing your request",
                });
            },
        });
    });

    $("#reg-form").submit(function (e) {
        e.preventDefault();
        $("#loadingOverlay").css("display", "flex");

        let name = $("#name").val();
        let email = $("#email").val();
        let password = $("#password").val();
        let otp = $("#otp").val();
        let regNo = $("#reg_no").val();
        let phone = $("#phone").val();
        let passingYear = $("#passing_year").val();
        let semester = $("#semester").val();
        let branch = $("#branch").val();
        let terms = $("#terms").prop("checked");


        if (!name || !regNo || !phone || !passingYear || !otp || !password) {
            $("#loadingOverlay").css("display", "none");
            Swal.fire({
                icon: "error",
                title: "Missing Information",
                text: "Please fill all the fields correctly!",
            });
            return;
        }

        if (!terms) {
            $("#loadingOverlay").css("display", "none");
            Swal.fire({
                icon: "error",
                title: "T&C Missing",
                text: "Please confirm Terms and Condition!",
            });
            return;
        }

        if (password.length < 8) {
            $("#loadingOverlay").css("display", "none");
            Swal.fire({
                icon: "error",
                title: "Password Too Short",
                text: "Password must be greater than 8 characters!",
            });
            return;
        }

        let formData = {
            action: "registration",
            name: name,
            email: email,
            password: password,
            otp: otp,
            reg_no: regNo,
            phone: phone,
            passing_year: passingYear,
            semester: semester,
            branch: branch,
        };

        $.ajax({
            type: "POST",
            url: "endpoint/api.php",
            data: formData,
            success: function (response) {
                if (response === "reg_success") {
                    Swal.fire({
                        icon: "success",
                        title: "Registration Successful",
                        text: "You will be redirected to the login page in 3 seconds",
                        showConfirmButton: false,
                        timer: 3000,
                    }).then(function () {
                        window.location.href = "login";
                    });
                } else if (response === "OTP Expired") {
                    Swal.fire({
                        icon: "error",
                        title: "Expired OTP",
                        text: "Please resend a new OTP and register again!",
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: response,
                    });
                }
                $("#loadingOverlay").css("display", "none");
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown);
                $("#loadingOverlay").css("display", "none");
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "An error occurred while processing your request",
                });
            },
        });
    });

    // Show/Hide Password Toggle
    $("#sendOtpLink2").on("click", function () {
        let passwordInput = $("#password");
        if (passwordInput.attr("type") === "password") {
            passwordInput.attr("type", "text");
            $(this).html('<i class="fa-solid fa-eye-slash"></i>');
        } else {
            passwordInput.attr("type", "password");
            $(this).html('<i class="fa-solid fa-eye"></i>');
        }
    });
});
