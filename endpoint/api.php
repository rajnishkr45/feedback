<?php
session_start();
require 'config.php';
require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to generate a random OTP
function generateOTP()
{
    return rand(100000, 999999);
}

function sendOTP($email, $otp)
{
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'rajnishroushan2020@gmail.com'; // Your SMTP username
        $mail->Password = 'mtoavtxoxxoargab'; // Your Gmail password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        //Recipients
        $mail->setFrom('rajnishroushan2020@gmail.com', 'Password Team');
        $mail->addAddress($email); // Add a recipient

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'AICTE + DCE Registrtation OTP';
        $mail->Body = 'Here is your OTP for AICTE Feedback registration: <b>' . $otp . '</b><br><br>Please do not share the OTP!<br>This OTP is valid for 5 minutes only.';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    switch ($action) {
        case "sendotp":
            $email = isset($_POST['email']) ? $_POST['email'] : '';
            $reg_no = isset($_POST['reg_no']) ? $_POST['reg_no'] : ''; // Corrected this line to fetch reg_no

            // Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400); // Bad Request
                echo "Invalid email address";
                exit;
            }

            // Check if registration number already exists
            $stmt2 = $conn->prepare("SELECT COUNT(*) FROM students WHERE reg_no = ?");
            $stmt2->bind_param("s", $reg_no);
            $stmt2->execute();
            $stmt2->bind_result($count2);
            $stmt2->fetch();
            $stmt2->close();

            if ($count2 > 0) {
                echo "Registration number already exists";
                exit;
            }

            // Check if email already exists
            $stmt = $conn->prepare("SELECT COUNT(*) FROM students WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                echo "Email already exists";
                exit;
            }


            // Generate OTP
            $otp = generateOTP();

            // Send OTP
            if (sendOTP($email, $otp)) {
                $_SESSION['otp_email'] = $email;
                $_SESSION['otp'] = $otp;
                $_SESSION['otp_timestamp'] = time();
                echo "OTP Sent Successfully";
            } else {
                http_response_code(500); // Internal Server Error
                echo "Failed to send OTP";
            }
            break;

        case "verifyotp":
            $enteredOTP = isset($_POST['otp']) ? $_POST['otp'] : '';
            $enteredEmail = isset($_POST['otp_email']) ? $_POST['otp_email'] : '';

            $savedOTP = isset($_SESSION['otp']) ? $_SESSION['otp'] : '';
            $savedEmail = isset($_SESSION['otp_email']) ? $_SESSION['otp_email'] : '';
            $savedTimestamp = isset($_SESSION['otp_timestamp']) ? $_SESSION['otp_timestamp'] : 0;

            if ($enteredOTP == $savedOTP && $enteredEmail == $savedEmail) {
                // Check if the timestamp is within 5 minutes
                if (time() <= ($savedTimestamp + 300)) {
                    echo "OTP Verified";
                } else {
                    unset($_SESSION['otp']);
                    unset($_SESSION['otp_email']);
                    unset($_SESSION['otp_timestamp']);
                    echo "OTP Expired";
                }
            } else {
                echo "Invalid OTP or Email";
            }
            break;
        case "registration":
            $enteredEmail = isset($_POST['email']) ? $_POST['email'] : '';
            $enteredOTP = isset($_POST['otp']) ? $_POST['otp'] : '';

            $savedOTP = isset($_SESSION['otp']) ? $_SESSION['otp'] : '';
            $savedEmail = isset($_SESSION['otp_email']) ? $_SESSION['otp_email'] : '';
            $savedTimestamp = isset($_SESSION['otp_timestamp']) ? $_SESSION['otp_timestamp'] : 0;

            if ($enteredOTP == $savedOTP && $enteredEmail == $savedEmail) {
                // Check if the timestamp is within 5 minutes
                if (time() <= ($savedTimestamp + 300)) {
                    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
                    $name = mysqli_real_escape_string($conn, $_POST["name"]);
                    $reg_no = mysqli_real_escape_string($conn, $_POST["reg_no"]);
                    $phone = mysqli_real_escape_string($conn, $_POST["phone"]);
                    $passing_year = mysqli_real_escape_string($conn, $_POST["passing_year"]);
                    $email = mysqli_real_escape_string($conn, $_POST["email"]);
                    $semester = intval($_POST["semester"]); // Ensure semester is treated as an integer
                    $branch = mysqli_real_escape_string($conn, $_POST["branch"]);

                    $sql = "INSERT INTO students (name, reg_no, phone, passing_year, email, password, semester, branch, timestamp) 
                                VALUES (?, ?, ?,?, ?, ?, ?, ?, NOW())";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt, "sssissis", $name, $reg_no, $phone, $passing_year, $email, $password, $semester, $branch);
                    $result = mysqli_stmt_execute($stmt);

                    if ($result) {
                        unset($_SESSION['otp']);
                        unset($_SESSION['otp_email']);
                        unset($_SESSION['otp_timestamp']);
                        echo "reg_success";
                    } else {
                        echo "Registration failed";
                    }
                } else {
                    unset($_SESSION['otp']);
                    unset($_SESSION['otp_email']);
                    unset($_SESSION['otp_timestamp']);
                    echo "OTP Expired";
                }
            } else {
                echo "Error while registration";
            }
            break;
        default:
            http_response_code(400); // Bad Request
            echo "Invalid action";
    }
}
?>