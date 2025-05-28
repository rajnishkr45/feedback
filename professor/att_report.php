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
                        <h3>view Attendance</h3>

                        <form id="attendance-report-filters">
                            <select id="report-semester" required>
                                <option value="" disabled selected>Select Semester</option>
                                <?php for ($i = 1; $i <= 8; $i++) {
                                    echo "<option value='$i'>{$i}th Sem</option>";
                                } ?>
                            </select>

                            <select id="report-branch" required>
                                <option value="" disabled selected>Select Branch</option>
                                <option value="CSE">CSE</option>
                                <option value="CYB">CYB</option>
                                <option value="EEE">EEE</option>
                                <option value="ME">ME</option>
                                <option value="CE">CE</option>
                                <option value="FTS">FTS</option>
                            </select>

                            <button type="button" id="view-report-btn">View Report</button>
                        </form>
                    </div>
                    <!-- Table to Display Attendance Report -->
                    <div id="attendance-report-table">
                        <!-- Content will be dynamically generated -->
                    </div>
                </div>
            </div>
        </main>
    </section>


    <script>
        $(document).ready(function () {
            // Fetch attendance report
            function fetchAttendanceReport() {
                const semester = $('#report-semester').val();
                const branch = $('#report-branch').val();

                if (!semester || !branch) {
                    Swal.fire('Error', 'Please select both semester and branch.', 'error');
                    return;
                }

                $.ajax({
                    url: 'attendance_report.php',
                    type: 'GET',
                    data: { semester, branch },
                    dataType: 'json',
                    success: function (data) {
                        const reportTable = $('#attendance-report-table');
                        reportTable.empty();

                        if (data.message) {
                            Swal.fire('Notice', data.message, 'info');
                        } else {
                            let tableHTML = `
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Reg No.</th>
                                            <th>Name</th>
                                            ${data.subjects.map(sub => `<th>${sub.subject_name}<br>(P/T)</th>`).join('')}
                                            <th>Total Present</th>
                                            <th>Total Classes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;

                            data.students.forEach(student => {
                                tableHTML += `
                                    <tr>
                                        <td>${student.reg_no}</td>
                                        <td>${student.name}</td>
                                        ${student.attendance.map(att => `<td>${att.present}/${att.total}</td>`).join('')}
                                        <td>${student.total_present}</td>
                                        <td>${student.total_classes}</td>
                                    </tr>
                                `;
                            });

                            tableHTML += `
                                    </tbody>
                                </table>
                            `;

                            reportTable.html(tableHTML);
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Failed to load attendance report.', 'error');
                    }
                });
            }

            // Event listener for "View Report" button
            $('#view-report-btn').on('click', fetchAttendanceReport);
        });
    </script>
</body>

</html>