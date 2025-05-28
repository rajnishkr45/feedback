<?php
// Database configuration
include 'pro_name.php';
?>

<!DOCTYPE html>
<html lang="en">

<?php
include './dependencies.php';
?>

<head>
    <title>Attendance System</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
        }

        #submit-attendance {
            background-color: #4CAF50;
            color: #fff;
            border-radius: 5px;
            margin: 15px auto 5px;
            width: 200px;
            outline: none;
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 18px;
            display: block;
            transition: all 0.3s ease-in-out;
        }

        #submit-attendance:hover {
            background-color: #3e8e41;
        }
    </style>
</head>

<body>
    <!-- SIDEBAR -->
    <?php include 'navbar.php'; ?>


    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu'></i>
            <form>
                <div class="form-input">
                    <input type="search" placeholder="Search...">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form>
            <label for="switch-mode" class="switch-mode"></label>
            <a href="#" class="notification">
                <i class='bx bxs-bell'></i>
                <span class="num">9</span>
            </a>
            <a href="#" class="profile">
                <img src="../dp/<?php echo $profilePicture ?? 'default.png'; ?>" alt="Profile Pic">
            </a>
        </nav>

        <!-- MAIN -->
        <main>
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>View Attendance</h3>

                        <form id="search-form">

                            <select id="view-semester" required>
                                <option value="" disabled selected>Semester</option>
                                <?php for ($i = 1; $i <= 8; $i++)
                                    echo "<option value='$i'>{$i} Semester</option>"; ?>
                            </select>

                            <select id="view-branch" required>
                                <option value="" disabled selected>Branch</option>
                                <option value="CSE">CSE</option>
                                <option value="CYB">CYB</option>
                                <option value="EEE">EEE</option>
                                <option value="ME">ME</option>
                                <option value="CE">CE</option>
                                <option value="FTS">FTS</option>
                            </select>

                            <select id="view-subject" required>
                                <option value="" disabled selected>Subject</option>
                            </select>

                        </form>
                    </div>


                    <table class="professor-table">
                        <thead>
                            <tr>
                                <th>SN.</th>
                                <th>Reg No.</th>
                                <th>Name</th>
                                <th>Total Class</th>
                                <th>Present Class</th>
                                <th>Total %</th>
                            </tr>
                        </thead>
                        <tbody id="attendance-table-container">
                            <!-- Student rows will be dynamically inserted here -->
                        </tbody>
                    </table>
                    
                </div>
            </div>

        </main>
    </section>

    <script>
        $(document).ready(function () {
            const professorId = <?php echo $professor_id; ?>;

            // Load subjects based on professor ID, semester, and branch
            function loadSubjects() {
                const semester = $('#view-semester').val();
                const branch = $('#view-branch').val();

                if (!semester || !branch) return;

                $.ajax({
                    url: 'get_subjects.php',
                    type: 'GET',
                    data: { professor_id: professorId, semester, branch },
                    dataType: 'json',
                    success: function (data) {
                        const subjectDropdown = $('#view-subject');
                        subjectDropdown.empty();
                        subjectDropdown.append('<option value="" disabled selected>Select Subject</option>');

                        if (data.message) {
                            Swal.fire('Notice', data.message, 'info');
                        } else {
                            data.forEach(subject => {
                                subjectDropdown.append(`<option value="${subject.subject_id}">${subject.subject_name}</option>`);
                            });
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Failed to load subjects.', 'error');
                    }
                });
            }

            // Fetch attendance data
            function fetchAttendance() {
                const semester = $('#view-semester').val();
                const branch = $('#view-branch').val();
                const subject = $('#view-subject').val();

                if (!semester || !branch || !subject) return;

                $.ajax({
                    url: 'get_attendance.php',
                    type: 'GET',
                    data: { semester, branch, subject },
                    dataType: 'json',
                    success: function (data) {
                        const attendanceTable = $('#attendance-table-container');
                        attendanceTable.empty();

                        if (data.message) {
                            Swal.fire('Notice', data.message, 'info');
                        } else {
                            let tableHTML = `
                                <table>
                                    <tbody>
                            `;
                            data.forEach((attendance, index) => {
                                tableHTML += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${attendance.reg_no}</td>
                                        <td>${attendance.name}</td>
                                        <td>${attendance.total_classes}</td>
                                        <td>${attendance.present_classes}</td>
                                        <td>${attendance.percentage}</td>
                                    </tr>
                                `;
                            });
                            tableHTML += `</tbody></table>`;
                            attendanceTable.html(tableHTML);
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Failed to load attendance data.', 'error');
                    }
                });
            }

            // Event listeners
            $('#view-semester, #view-branch').on('change', function () {
                loadSubjects(); // Update subjects when semester or branch changes
            });

            $('#view-subject').on('change', fetchAttendance); // Fetch attendance when subject changes
            $('#view-attendance-btn').on('click', fetchAttendance); // Trigger fetching attendance when button is clicked

            // Initial load for subjects
            $('#view-semester, #view-branch').trigger('change');
        });
    </script>

</body>

</html>