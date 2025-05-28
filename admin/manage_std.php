<?php
// Database configuration
include 'admin_name.php';
?>

<!DOCTYPE html>
<html lang="en">

<?php
include './dependencies.php';
?>

<head>
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15px auto;
            padding: 20px;
            border: 1px solid #888;
            width: 60%;
        }

        .modal h2 {
            margin-top: 10px;
            color: #209482;
        }

        .close {
            color: #dc3f61b9;
            float: right;
            font-size: 42px;
            font-weight: 600;
        }

        .close:hover,
        .close:focus {
            color: #ff3460;
            text-decoration: none;
            cursor: pointer;
        }


        .input-group {
            margin-bottom: 15px;
            position: relative;
        }

        .input-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
        }

        .input-group input,
        select {
            width: 100%;
            padding: 10px;
            border: 2px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }

        .modal button {
            width: 100%;
            padding: 10px;
            background-color: #35b3ed;
            color: white;
            border: none;
            font-size: 18px;
            border-radius: 4px;
            cursor: pointer;
            transition: 0.2s all ease-in-out;
        }

        button:hover {
            background-color: #2f9ccf;
        }

        .dark .input-group input,
        .dark select,
        {
        background: #1b1b1f;
        color: #fff;
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            .modal-content {
                width: 75%;
                margin-right: 20px;
            }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <?php include 'navbar.php'; ?>
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
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Student List</a></li>
                    </ul>
                </div>
                <a href="#" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Download CSV</span>
                </a>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Student's Details</h3>
                        <form id="search-form">
                            <select id="dept-select" name="dept">
                                <option value="" selected hidden>Select Dept.</option>
                                <option value="">All</option>
                                <option value="CSE">CSE</option>
                                <option value="CYB">CYB</option>
                                <option value="EE">EEE</option>
                                <option value="ME">ME</option>
                                <option value="CE">CE</option>
                                <option value="FTS">FTS</option>
                            </select>

                            <select id="semester" name="semester">
                                <option value="" disabled selected>Select sem</option>
                                <option value="">All</option>
                                <option value="1">1st Sem</option>
                                <option value="2">2nd Sem</option>
                                <option value="3">3rd Sem</option>
                                <option value="4">4th Sem</option>
                                <option value="5">5th Sem</option>
                                <option value="6">6th Sem</option>
                                <option value="7">7th Sem</option>
                                <option value="8">8th Sem</option>
                            </select>
                        </form>
                    </div>
                    
                    <table class="professor-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Reg No.</th>
                                <th>Sem</th>
                                <th>Dept</th>
                                <th>Phone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="professor-list">
                            <!-- Rows will be inserted here by jQuery -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal for Editing Student Data -->
            <div id="editModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Edit Student</h2>
                    <form id="edit-form">
                        <input type="hidden" name="id" id="student-id">
                        <div class="input-group">
                            <label for="name">Your name:</label>
                            <input type="text" name="name" id="name" placeholder="Rajnish Kumar" required>
                        </div>
                        <div class="input-group">
                            <label for="reg_no">Registration no.:</label>
                            <input type="number" name="reg_no" id="reg_no" placeholder="11 digit reg no." required>
                        </div>
                        <div class="input-group">
                            <label for="semester">Select Semester:</label>
                            <select id="semester-edit" name="semester" required>
                                <option value="" disabled selected>Select semester</option>
                                <option value="1">1st Semester</option>
                                <option value="2">2nd Semester</option>
                                <option value="3">3rd Semester</option>
                                <option value="4">4th Semester</option>
                                <option value="5">5th Semester</option>
                                <option value="6">6th Semester</option>
                                <option value="7">7th Semester</option>
                                <option value="8">8th Semester</option>
                                <option value="9">Pass Out</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="branch">Select Department:</label>
                            <select id="branch" name="branch" required>
                                <option value="" selected hidden>Choose Department</option>
                                <option value="CSE">CSE</option>
                                <option value="CYB">CYBER</option>
                                <option value="EE">EEE</option>
                                <option value="ME">ME</option>
                                <option value="CE">Civil</option>
                                <option value="FTS">FTS</option>
                            </select>
                        </div>
                        <div class="input-group">
                            <label for="email">Your email:</label>
                            <input type="email" name="email" id="email" placeholder="valid email" required>
                        </div>
                        <div class="input-group">
                            <label for="phone">Phone:</label>
                            <input type="text" name="phone" id="phone" placeholder="Enter your phone no." required>
                        </div>
                        <button type="submit">Update Student</button>
                    </form>
                </div>
            </div>

        </main>
    </section>

    <!-- JavaScript to handle AJAX request -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Trigger AJAX call on change of department or semester
            $('#dept-select, #semester').on('change', function () {
                fetchFilteredData();
            });

            function fetchFilteredData() {
                // Get selected values
                const dept = $('#dept-select').val();
                const semester = $('#semester').val();

                // Send AJAX request with JSON payload
                $.ajax({
                    url: 'fetch_students.php', // Ensure this URL is correct
                    type: 'POST',
                    contentType: 'application/json',
                    dataType: 'json',
                    data: JSON.stringify({ dept: dept, semester: semester }),
                    success: function (response) {
                        let html = '';
                        if (Array.isArray(response) && response.length > 0) {
                            response.forEach(function (student) {
                                html += `
                            <tr data-id="${student.id}">
                                <td style='text-align:center;'>${student.id}</td>
                                <td><span class='prof-name'>${student.name}</span></td>
                                <td style='text-align:center;'><span class='prof-reg'>${student.reg_no}</span></td>
                                <td style='text-align:center;'><span class='prof-semester'>${student.semester}</span></td>
                                <td style="display:none;"><span class='prof-email'>${student.email}</span></td>
                                <td style='text-align:center;'><span class='prof-dept'>${student.branch}</span></td>
                                <td style='text-align:center;'><span class='prof-phone'>${student.phone}</span></td>
                                <td style='text-align:center;'>
                                    <span class='edit-btn' data-id='${student.id}'><i class='bx bxs-edit'></i></span>
                                    <span class='delete-btn' data-prof-id='${student.id}'><i class='bx bxs-trash'></i></span>
                                </td>
                            </tr>`;
                            });
                        } else {
                            html = "<tr><td colspan='7'>No students found</td></tr>";
                        }
                        $('#professor-list').html(html);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error('Error fetching data: ', textStatus, errorThrown);
                        $('#professor-list').html("<tr><td colspan='7'>Failed to fetch data</td></tr>");
                    }
                });
            }

            // Event listener for the edit button
            $(document).on('click', '.edit-btn', function () {
                // Get the student data from the row
                const studentRow = $(this).closest('tr');
                const id = studentRow.data('id'); // Use data-id attribute
                const name = studentRow.find('.prof-name').text();
                const reg_no = studentRow.find('.prof-reg').text();
                const semester = studentRow.find('.prof-semester').text();
                const dept = studentRow.find('.prof-dept').text();
                const email = studentRow.find('.prof-email').text(); // Ensure you have this in your response
                const phone = studentRow.find('.prof-phone').text();

                // Set the modal fields
                $('#student-id').val(id);
                $('#name').val(name);
                $('#reg_no').val(reg_no);
                $('#semester-edit').val(semester);
                $('#branch').val(dept);
                $('#email').val(email);
                $('#phone').val(phone);

                // Show the modal
                $('#editModal').css('display', 'block');
            });

            // Close the modal when the close button is clicked
            $('.close').click(function () {
                $('#editModal').css('display', 'none');
            });

            // Close the modal when clicking outside of it
            $(window).click(function (event) {
                if (event.target.id === "editModal") {
                    $('#editModal').css('display', 'none');
                }
            });

            // Handle form submission for editing
            $('#edit-form').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: 'update_student.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        // Show a success message using SweetAlert
                        swal.fire({
                            icon: 'success', // Icon should be a string
                            title: 'Updated Successfully', // Title should be a string
                            text: "Student's details updated successfully" // Text should be a string
                        });
                        fetchFilteredData();
                        $('#editModal').css('display', 'none');
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // Show an error message using SweetAlert
                        swal.fire({
                            icon: 'error',
                            title: 'Update Failed',
                            text: 'There was an error updating the student details. Please try again.'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>