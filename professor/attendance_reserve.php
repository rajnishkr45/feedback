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
                        <h3>Mark Attendance</h3>

                        <form id="search-form">

                            <select id="semester" required>
                                <option value="" disabled selected>Semester</option>
                                <?php for ($i = 1; $i <= 8; $i++)
                                    echo "<option value='$i'>{$i} Semester</option>"; ?>
                            </select>

                            <select id="branch" required>
                                <option value="" disabled selected>Branch</option>
                                <option value="CSE">CSE</option>
                                <option value="CYB">CYB</option>
                                <option value="EEE">EEE</option>
                                <option value="ME">ME</option>
                                <option value="CE">CE</option>
                                <option value="FTS">FTS</option>
                            </select>

                            <select id="subject" required>
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
                                <th>Attendance</th>
                            </tr>
                        </thead>
                        <tbody id="student-list">
                            <!-- Student rows will be dynamically inserted here -->
                        </tbody>
                    </table>
                    <button id="submit-attendance">Submit Attendance</button>
                </div>
            </div>

        </main>
    </section>

    <script>
        $(document).ready(function () {
            const professorId = <?php echo $professor_id; ?>;

            // Load subjects based on professor ID, semester, and branch
            function loadSubjects() {
                const semester = $('#semester').val();
                const branch = $('#branch').val();

                if (!semester || !branch) return;

                $.ajax({
                    url: 'get_subjects.php',
                    type: 'GET',
                    data: { professor_id: professorId, semester, branch },
                    dataType: 'json',
                    success: function (data) {
                        const subjectDropdown = $('#subject');
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

            // Load students dynamically based on filters
            function loadStudents() {
                const semester = $('#semester').val();
                const branch = $('#branch').val();
                const subject = $('#subject').val();

                if (!semester || !branch || !subject) return;

                $.ajax({
                    url: 'get_students.php',
                    type: 'GET',
                    data: { semester, branch, subject },
                    dataType: 'json',
                    success: function (data) {
                        const studentList = $('#student-list');
                        studentList.empty();

                        if (data.message) {
                            Swal.fire('Notice', data.message, 'info');
                        } else {
                            data.forEach((student, index) => {
                                studentList.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${student.reg_no}</td>
                                <td>${student.name}</td>
                                <td>
                                    <select class="attendance-status" data-student-id="${student.id}">
                                        <option value="Present">Present</option>
                                        <option value="Absent">Absent</option>
                                    </select>
                                </td>
                            </tr>
                        `);
                            });
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Failed to load students.', 'error');
                    }
                });
            }

            // Event listeners
            $('#semester, #branch').on('change', function () {
                loadSubjects(); // Update subjects when semester or branch changes
                loadStudents(); // Update students list when semester or branch changes
            });
            $('#subject').on('change', loadStudents);

            // Submit attendance
            $('#submit-attendance').on('click', function () {
                const attendanceData = [];
                $('.attendance-status').each(function () {
                    const studentId = $(this).data('student-id');
                    const status = $(this).val();
                    attendanceData.push({ student_id: studentId, status });
                });

                const subjectId = $('#subject').val();
                const semester = $('#semester').val();

                if (attendanceData.length > 0) {
                    $.ajax({
                        url: 'submit_attendance.php',
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({ subject_id: subjectId, semester, attendance: attendanceData }),
                        success: function (response) {
                            Swal.fire('Success', 'Attendance submitted successfully!', 'success');

                            $('#semester').val('');
                            $('#branch').val('');
                            $('#subject').empty().append('<option value="" disabled selected>Select Subject</option>');
                            $('#student-list').empty();
                        },
                        error: function () {
                            Swal.fire('Error', 'Failed to submit attendance.', 'error');
                        }
                    });
                } else {
                    Swal.fire('Warning', 'No attendance data to submit.', 'warning');
                }
            });

            // Initial load
            $('#semester, #branch').trigger('change');
        });

    </script>

</body>

</html>