<?php
include './admin_name.php';
?>

<!DOCTYPE html>
<html lang="en">

<?php
include 'dependencies.php';
?>

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
                        <li><a class="active" href="#">Assigned Classes</a></li>
                    </ul>
                </div>
                <a href="#" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Download PDF</span>
                </a>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Assigned Classes</h3>
                        <form id="search-form">
                            <select id="branch-select" name="branch">
                                <option value="" selected disabled>Choose Branch</option>
                                <option value="all">All</option>
                                <option value="CSE">CSE</option>
                                <option value="CYB">CYBER</option>
                                <option value="EE">EEE</option>
                                <option value="ME">ME</option>
                                <option value="CE">Civil</option>
                                <option value="FTS">FTS</option>
                            </select>
                            <select id="semester-select" name="semester">
                                <option value="" selected disabled>Choose Semester</option>
                                <option value="all">All</option>
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
                    <table class="assigned-class-table">
                        <thead>
                            <tr>
                                <th>SN.</th>
                                <th>Professor Name</th>
                                <th>Subject</th>
                                <th>Branch</th>
                                <th>Semester</th>
                                <th colspan="2">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="assigned-class-list">
                            <!-- Assigned classes will be dynamically loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </section>

    <script>
        // Function to delete assigned class
        function deleteAssignedClass(id, rowElement) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'delete_assigned.php', // The PHP file to handle the deletion
                        type: 'POST',
                        data: { id: id }, // Send the ID of the class to delete
                        success: function (response) {
                            Swal.fire(
                                'Deleted!',
                                response, // Display the response message
                                'success'
                            );

                            // Remove the row from the table
                            $(rowElement).remove();
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX Error: ' + status + error);
                            Swal.fire(
                                'Error!',
                                'Failed to delete the record.',
                                'error'
                            );
                        }
                    });
                }
            });
        }

        // Function to fetch assigned classes based on selected branch and semester
        $('#branch-select, #semester-select').on('change', function () {
            var branch = $('#branch-select').val(); // Get the selected branch value
            var semester = $('#semester-select').val(); // Get the selected semester value

            // Only fetch if both branch and semester are selected
            if (branch && semester) {
                $.ajax({
                    url: 'fetch_assigned_class.php', // The PHP file to handle the search
                    type: 'GET',
                    data: { branch: branch, semester: semester }, // Send both selected values
                    success: function (response) {
                        // Update the assigned class table with the returned data
                        $('#assigned-class-list').html(response);
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error: ' + status + error);
                    }
                });
            } else {
                // If either field is not selected, clear the table
                $('#assigned-class-list').empty();
            }
        });
    </script>

</body>

</html>