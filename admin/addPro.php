<?php
// Database connection
include '../endpoint/config.php';

require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to generate a strong random password
function generateStrongPassword($length = 10)
{
    $charset = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ@#$%&";
    $password = "";
    for ($i = 0; $i < $length; $i++) {
        $randomIndex = mt_rand(0, strlen($charset) - 1);
        $password .= $charset[$randomIndex];
    }
    return $password;
}

function sendPassword($email, $password, $role)
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
        $mail->setFrom('rajnishroushan2020@gmail.com', 'Developers Team');
        $mail->addAddress($email); // Add a recipient

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = 'AICTE FEEDBACK Registration Password';
        $mail->Body = 'You have been registered in <b>AICTE FEEDBACK PORTAL</b> as <span style="color:#209e74;">' .$role. '</span><br>Please keep your Password safely <br><br>Your password is: <b>' . $password . '</b><br><br>Please change this password after logging in.<br>This email is for registration purposes only.';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log('Message could not be sent. Mailer Error: ' . $mail->ErrorInfo);
        return false;
    }
}

// Check if email is already registered
if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $checkQuery = "SELECT * FROM professors WHERE email = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
        exit();
    }

    // Generate a strong password
    $password = generateStrongPassword();

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new professor data
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $dept = $_POST['dept'];
    $role = $_POST['role'];

    $insertQuery = "INSERT INTO professors (name, phone, email, dept, role, password) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssssss", $name, $phone, $email, $dept, $role, $hashedPassword);

    if ($stmt->execute()) {
        // Send the generated password to the user's email
        if (sendPassword($email, $password,$role)) {
            echo json_encode(['status' => 'success', 'message' => 'Account created successfully and password sent to email']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Account created, but failed to send password to email']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create account']);
    }
    $stmt->close();
}
$conn->close();
?>