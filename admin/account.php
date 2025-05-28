<?php
include('admin_name.php');

// Handle image upload
if (isset($_POST['upload_dp'])) {
    // Check if file was uploaded without errors
    if (isset($_FILES['dp']) && $_FILES['dp']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['dp']['tmp_name'];
        $file_name = $_FILES['dp']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $allowed_ext = array('jpg', 'jpeg', 'png', 'gif');
        $max_size = 150 * 1024; // 150KB

        // Check file size
        if ($_FILES['dp']['size'] > $max_size) {
            echo "Error: File size exceeds the maximum limit of 150KB.";
            exit;
        }

        // Check if the file type is allowed
        if (in_array(strtolower($file_ext), $allowed_ext)) {
            // Generate a unique filename
            $new_file_name = uniqid() . '.' . $file_ext;
            // Move the uploaded file to the desired directory
            if (move_uploaded_file($file_tmp, '../dp/' . $new_file_name)) {
                // Update the database with the new filename
                $update_sql = "UPDATE admins SET dp = '$new_file_name' WHERE email = '$admin_email'";
                if ($conn->query($update_sql) === TRUE) {
                    // Redirect back to the dashboard after successful upload
                    header('location: account.php');
                    exit;
                } else {
                    echo "Error updating record: " . $conn->error;
                }
            } else {
                echo "File upload failed.";
            }
        } else {
            echo "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    } else {
        echo "Error uploading file.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php
include './dependencies.php';
?>
<link rel="stylesheet" href="../css/account.css?v=1">

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
                            <a class="active" href="#">Settings</a>
                        </li>
                    </ul>
                </div>
            </div>


            <?php
            $semail = $_SESSION['admin_email'];
            $sql = "SELECT `admin_id`, `name`,`email`, `phone`,`dp` FROM `admins` WHERE email='$semail'";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='account_card'>";
                    echo "<img src='../dp/" . $row['dp'] . "' alt='Profile Picture'>";
                    echo "<h3>" . $row['name'] . "</h3>";
                    echo "<p><span>Email:</span> " . $row['email'] . "</p>";
                    echo "<p><span>Phone:</span> " . $row['phone'] . "</p>";
                    echo '<div class="upload-form">
                        <form id="uploadForm" action="" method="post" enctype="multipart/form-data">
                            <input type="file" id="dp" name="dp" accept="image/*" style="display:none;">
                            <div class="combiner">
                                <label for="dp" id="uploadIcon"><i class="bx bx-camera"></i></label>
                                <div id="fileInfo"></div>
                                <button type="submit" name="upload_dp" id="updateButton"><i class="bx bx-cloud-upload"></i></button>
                                <button id="updatePasswordBtn" class="password-update" style="width:auto;">Update Pass</button>
                            </div>
                        </form>
                    </div>';
                    echo "</div>";
                }
            } else {
                echo "<p style='text-align: center;'>No professor records found.</p>";
            }
            ?>

        </main>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.getElementById('updatePasswordBtn').addEventListener('click', function (e) {

            e.preventDefault();

            Swal.fire({
                title: 'Update Password',
                html: `
          <form id="passwordUpdateForm">
            <div class="form-group">
              <label for="currentPassword">Current Password</label>
              <input type="text" id="currentPassword" class="swal2-input" placeholder="Current Password" required>
            </div>
            <div class="form-group">
              <label for="newPassword">New Password</label>
              <input type="text" id="newPassword" class="swal2-input" placeholder="New Password" required>
            </div>
            <div class="form-group">
              <label for="confirmPassword">Confirm Password</label>
              <input type="password" id="confirmPassword" class="swal2-input" placeholder="Confirm Password" required>
            </div>
          </form>
        `,
                focusConfirm: false,
                confirmButtonText: 'Update',
                cancelButtonText: 'Cancel',
                showCancelButton: true,
                preConfirm: () => {
                    const currentPassword = Swal.getPopup().querySelector('#currentPassword').value;
                    const newPassword = Swal.getPopup().querySelector('#newPassword').value;
                    const confirmPassword = Swal.getPopup().querySelector('#confirmPassword').value;

                    if (currentPassword == "" || newPassword == "" || confirmPassword == "") {
                        Swal.showValidationMessage('Empty Fields');
                        return false;
                    }
                    if (newPassword !== confirmPassword) {
                        Swal.showValidationMessage('New passwords do not match');
                        return false;
                    }

                    // Perform the AJAX request to update the password
                    const formData = new FormData();
                    formData.append('currentPassword', currentPassword);
                    formData.append('newPassword', newPassword);

                    return fetch('update_pass.php', { // Update this URL to your password update endpoint
                        method: 'POST',
                        body: formData,
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Updated!', 'Your password has been updated.', 'success');
                            } else {
                                Swal.fire('Error!', 'There was an issue updating your password.', 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'There was an issue updating your password.', 'error');
                        });
                }
            });
        });

    </script>

</body>

</html>