<?php
include '../endpoint/config.php'; // Include database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'fetch') {
        $prof_id = trim($_POST['prof_id']);

        if (!ctype_digit($prof_id)) {
            echo json_encode(['success' => false, 'message' => 'Invalid professor ID']);
            exit;
        }

        $stmt = $conn->prepare("SELECT prof_id, name, email, phone, dept, role FROM professors WHERE prof_id = ?");
        $stmt->bind_param("i", $prof_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $data = $result->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Professor not found']);
        }
        exit;
    }

    if ($_POST['action'] === 'update') {
        $prof_id = trim($_POST['prof_id']);
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $dept = trim($_POST['dept']);
        $role = trim($_POST['role']);

        if (!ctype_digit($prof_id) || empty($name) || empty($email) || empty($phone) || empty($dept) || empty($role)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required']);
            exit;
        }

        $stmt = $conn->prepare("UPDATE professors SET name = ?, email = ?, phone = ?, dept = ?, role = ? WHERE prof_id = ?");
        $stmt->bind_param("sssssi", $name, $email, $phone, $dept, $role, $prof_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Professor updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Update failed']);
        }
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Professor</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS file -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <h2>Manage Professor</h2>

    <label for="profIdInput">Enter Professor ID:</label>
    <input type="text" id="profIdInput" placeholder="Professor ID">
    <button id="fetchProfessor">Fetch Details</button>

    <div id="professorForm" style="display: none;">
        <h3>Edit Professor Details</h3>
        <input type="hidden" id="editProfId">
        <label>Name:</label>
        <input type="text" id="editName"><br>
        <label>Email:</label>
        <input type="email" id="editEmail"><br>
        <label>Phone:</label>
        <input type="text" id="editPhone"><br>
        <label>Department:</label>
        <input type="text" id="editDept"><br>
        <label>Role:</label>
        <input type="text" id="editRole"><br>
        <button id="updateProfessor">Update</button>
    </div>

    <script>
        $(document).ready(function () {
            $("#fetchProfessor").click(function () {
                let prof_id = $("#profIdInput").val().trim();

                if (!prof_id) {
                    Swal.fire("Error", "Please enter a professor ID!", "error");
                    return;
                }

                $.ajax({
                    url: "update_prof.php",
                    type: "POST",
                    data: { action: "fetch", prof_id: prof_id },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            $("#editProfId").val(response.data.prof_id);
                            $("#editName").val(response.data.name);
                            $("#editEmail").val(response.data.email);
                            $("#editPhone").val(response.data.phone);
                            $("#editDept").val(response.data.dept);
                            $("#editRole").val(response.data.role);
                            $("#professorForm").show();
                        } else {
                            Swal.fire("Error", response.message, "error");
                        }
                    }
                });
            });

            $("#updateProfessor").click(function () {
                let prof_id = $("#editProfId").val();
                let name = $("#editName").val().trim();
                let email = $("#editEmail").val().trim();
                let phone = $("#editPhone").val().trim();
                let dept = $("#editDept").val().trim();
                let role = $("#editRole").val().trim();

                if (!name || !email || !phone || !dept || !role) {
                    Swal.fire("Error", "All fields are required!", "error");
                    return;
                }

                $.ajax({
                    url: "update_prof.php",
                    type: "POST",
                    data: { action: "update", prof_id: prof_id, name: name, email: email, phone: phone, dept: dept, role: role },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            Swal.fire("Success", response.message, "success");
                        } else {
                            Swal.fire("Error", response.message, "error");
                        }
                    }
                });
            });
        });
    </script>

</body>

</html>