<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $table = '';
    if ($role == 'student') {
        $table = 'students';
    } elseif ($role == 'professor') {
        $table = 'professors';
    } elseif ($role == 'admin') {
        $table = 'admins';
    }

    $sql = "SELECT * FROM $table WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Email not registered!'
        ]);
    } else {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {

            if ($role == 'student') {
                $_SESSION['email'] = $user['email'];
            } elseif ($role == 'professor') {
                $_SESSION['prof_email'] = $user['email'];
            } elseif ($role == 'admin') {
                $_SESSION['admin_email'] = $user['email'];
            }
            $_SESSION['role'] = $role;

            echo json_encode([
                'success' => true
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid credentials, try again !'
            ]);
        }
    }
    $stmt->close();
}

$conn->close();
?>