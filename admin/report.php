<?php
include 'admin_name.php'; // Include your database connection and other necessary files
?>

<!DOCTYPE html>
<html lang="en">
<?php
include './dependencies.php';
?>

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
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <ul class="breadcrumb">
                        <li>
                            <a href="#">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="#">Analytics</a>
                        </li>
                    </ul>
                </div>
                <a href="download_report_csv.php?dept=" id="download-csv-btn" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Download CSV</span>
                </a>

            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Professor's Performance Report</h3>
                        <form id="search-form">
                            <select id="dept-select" name="dept">
                                <option value="" selected hidden>Choose Dept.</option>
                                <option value="">All</option>
                                <option value="CSE">CSE</option>
                                <option value="CYB">CYB</option>
                                <option value="EE">EEE</option>
                                <option value="ME">ME</option>
                                <option value="CE">CE</option>
                                <option value="FTS">FTS</option>
                            </select>
                        </form>
                    </div>
                    <table class="professor-table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Name</th>
                                <th>Dept.</th>
                                <th>Role</th>
                                <th>Rating</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="professor-list">
                            <!-- Dynamic content will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script>
        // Update the table when a department is selected, and update the CSV download link
        $('#dept-select').on('change', function () {
            var dept = $(this).val(); // Get the selected department value

            // AJAX to update the table based on selected department
            $.ajax({
                url: 'fetch_report.php', // The PHP file to handle the search
                type: 'GET',
                data: { dept: dept }, // Send the selected department
                success: function (response) {
                    // Update the professor table with the returned data
                    $('#professor-list').html(response);

                    // Update the CSV download link to include the selected department
                    $('#download-csv-btn').attr('href', 'download_csv.php?dept=' + dept);
                },
                error: function (xhr, status, error) {
                    console.error('AJAX Error: ' + status + error);
                }
            });
        });

        // Update CSV link on page load
        $(document).ready(function () {
            var dept = $('#dept-select').val();
            $('#download-csv-btn').attr('href', 'download_report_csv.php?dept=' + dept);
        });
    </script>

</body>

</html>