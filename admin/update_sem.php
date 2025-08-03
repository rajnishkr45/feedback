<?php include 'admin_name.php'; ?>
<!DOCTYPE html>
<html lang="en">
<?php include './dependencies.php'; ?>

<head>
    <style>
        .table-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #209482;
            color: white;
        }

        .btn-main {
            background: #209482;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 15px;
            margin-top: 10px;
        }

        .btn-main:hover {
            background: #167c6b;
        }

        select,
        button {
            padding: 8px;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <section id="content">
        <nav>
            <i class='bx bx-menu'></i>
            <form>
                <div class="form-input">
                    <input type="search" placeholder="Search...">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form>
        </nav>

        <main>
            <div class="head-title">
                <div class="left">
                    <ul class="breadcrumb">
                        <li><a href="#">Dashboard</a></li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li><a class="active" href="#">Manage Students</a></li>
                    </ul>
                </div>
            </div>

            <div class="table-container">
                <h2>Update Student Semester</h2>
                <form id="semester-select-form">
                    <select id="semester" name="semester" required>
                        <option value="" selected hidden>Choose Semester</option>
                        <?php for ($i = 1; $i <= 8; $i++) echo "<option value='$i'>{$i} Semester</option>"; ?>
                        <option value="0">Pass Out</option>
                    </select>
                    <button type="submit" class="btn-main">Fetch Students</button>
                </form>

                <div id="students-table"></div>
            </div>
        </main>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function () {
            // Fetch students
            $('#semester-select-form').on('submit', function (event) {
                event.preventDefault();
                var semester = $('#semester').val();

                $.ajax({
                    url: 'fetch_student_semester.php',
                    type: 'POST',
                    data: { semester: semester },
                    success: function (response) {
                        $('#students-table').html(response);
                    },
                    error: function () {
                        Swal.fire('Error!', 'Failed to fetch students.', 'error');
                    }
                });
            });

            // Update selected students
            $(document).on('click', '#update-semester', function () {
                var newSemester = prompt("Enter new semester (1-8 or 0 for Pass Out):");
                if (newSemester === null || newSemester === "") return;

                var studentIds = [];
                $('input[name="student_ids[]"]:checked').each(function () {
                    studentIds.push($(this).val());
                });

                if (studentIds.length === 0) {
                    Swal.fire('Warning!', 'Please select at least one student.', 'warning');
                    return;
                }

                $.ajax({
                    url: 'update_semester.php',
                    type: 'POST',
                    dataType: "json",
                    data: { student_ids: studentIds, new_semester: newSemester },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire('Success!', response.message, 'success');
                            $('#semester-select-form').submit(); // Refresh list
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error!', 'Server error while updating semester.', 'error');
                    }
                });
            });
        });
    </script>
</body>
</html>
