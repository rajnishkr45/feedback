<?php
session_start();
include 'config.php'; // Include your database configuration
require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize variables
$otp = '';
$email = '';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'send_otp':
                $role = $_POST['role'];
                $email = $_POST['email'];

                // Check if email exists based on role
                $table = ($role === 'student') ? 'students' : (($role === 'professor') ? 'professors' : 'admins');

                $sql = "SELECT * FROM $table WHERE email=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('s', $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 0) {
                    echo json_encode(['success' => false, 'message' => 'Email not registered!']);
                } else {
                    // Generate OTP and send email
                    $otp = rand(100000, 999999); // Generate a 6-digit OTP
                    $_SESSION['otp'] = $otp; // Store OTP in session
                    $_SESSION['otp_time'] = time(); // Store time of OTP sent
                    $_SESSION['email'] = $email; // Store email for later verification
                    $_SESSION['role'] = $role; // Store role

                    // Create a new PHPMailer instance
                    $mail = new PHPMailer(true);

                    try {
                        //Server settings
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'your email'; // Your SMTP username
                        $mail->Password = 'yout password'; // Your Gmail password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS encryption
                        $mail->Port = 587; // TCP port to connect to

                        //Recipients
                        $mail->setFrom('your email', 'Password Team');
                        $mail->addAddress($email); // Add a recipient

                        // Content
                        $mail->isHTML(true); // Set email format to HTML
                        $mail->Subject = 'Your OTP for Password Reset';
                        $mail->Body = 'Your OTP is: <strong>' . $otp . '</strong><br>Please do not share the OTP!<br>This OTP is valid for 5 minutes only.';

                        // Send the email
                        $mail->send();
                        echo json_encode(['success' => true, 'message' => 'OTP sent to your email!']);
                    } catch (Exception $e) {
                        echo json_encode(['success' => false, 'message' => 'Message could not be sent. Mailer Error: ' . $mail->ErrorInfo]);
                    }
                }
                break;

            case 'verify_otp':
                // Verify OTP
                $input_otp = $_POST['otp'];
                if ($_SESSION['otp'] == $input_otp && (time() - $_SESSION['otp_time']) <= 300) { // Check if OTP is correct and within 5 minutes
                    echo json_encode(['success' => true, 'message' => 'OTP verified!']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid OTP or OTP expired.']);
                }
                break;

            case 'reset_password':
                // Reset password
                $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT); // Hash new password
                $role = $_SESSION['role'];
                $email = $_SESSION['email'];

                $table = ($role === 'student') ? 'students' : (($role === 'professor') ? 'professors' : 'admins');

                $sql = "UPDATE $table SET password=? WHERE email=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ss', $new_password, $email);

                if ($stmt->execute()) {
                    // Destroy session
                    session_destroy();
                    echo json_encode(['success' => true, 'message' => 'Password has been reset successfully.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error resetting password.']);
                }
                break;
        }
        exit; // Stop further processing
    }
}
?>