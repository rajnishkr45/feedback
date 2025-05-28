<?php
include 'admin_name.php'; // Include your database connection and other necessary files
?>

<!DOCTYPE html>
<html lang="en">

<?php
include './dependencies.php';
?>
<style>
    #search-form button {
        padding: 7px 10px;
        background-color: #2991ff;
        color: white;
        margin-left: 10px;
        border: solid 2px #ccc;
        outline: none;
        border-radius: 4px;
        transition: all 0.3s ease-in-out;
        cursor: pointer;
    }

    #search-form button:hover {
        background-color: #0679f4;
    }
</style>

<body>

    <!-- SIDEBAR -->
    <?php
    include 'navbar.php';
    ?>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 5px;
            width: 50%;
        }

        .close {
            float: right;
            font-size: 28px;
            cursor: pointer;
        }
    </style>
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
                            <a class="active" href="#">Professors List</a>
                        </li>
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
                        <h3>Professor's Details</h3>
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
                                <option value="Applied Sci">Applied Science</option>
                            </select>
                            <button id="addPro" class="btn">Add New Prof.</button>
                        </form>
                    </div>
                    <table class="professor-table">
                        <thead>
                            <tr>
                                <th>SN.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Dept.</th>
                                <th>Role</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="professor-list">
                            <!-- Professors will be dynamically loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </section>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Professor</h2>
            <form id="editForm">
                <input type="hidden" id="prof_id" name="prof_id">
                <label>Name:</label>
                <input type="text" id="name" name="name" required>
                <label>Email:</label>
                <input type="email" id="email" name="email" required>
                <label>Phone:</label>
                <input type="number" id="phone" name="phone" required>
                <label>Department:</label>
                <input type="text" id="dept" name="dept" required>
                <label>Role:</label>
                <input type="text" id="role" name="role" required>
                <button type="submit">Update</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {

            // Handle Add New Professor Button Click
            document.getElementById("addPro").addEventListener('click', function (e) {
                e.preventDefault();
                window.location.href = "add_pro.php";
            });

            // Handle Department Filtering
            document.getElementById('dept-select').addEventListener('change', function () {
                let dept = this.value; // Get the selected department value

                $.ajax({
                    url: 'search_prof.php',
                    type: 'GET',
                    data: { dept: dept },
                    success: function (response) {
                        document.getElementById('professor-list').innerHTML = response;
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error: ' + status + " " + error);
                    }
                });
            });

            // Handle Dynamic Event Binding for Edit Buttons
            $(document).on('click', '.edit-btn', function () {
                let $row = $(this).closest('tr'); // Get closest row for correct data
                $('#prof_id').val($row.find('.edit-btn').data('prof-id') || '');
                $('#name').val($row.find('.prof-name').text().trim());
                $('#email').val($row.find('.prof-email').text().trim());
                $('#phone').val($row.find('.prof-phone').text().trim());
                $('#dept').val($row.find('.prof-dept').text().trim());
                $('#role').val($row.find('.prof-role').text().trim());
                $('#editModal').show();
            });

            // Close Modal when clicking the Close Button
            $('.close').on('click', function () {
                $('#editModal').hide();
            });

            // Handle Edit Form Submission
            $('#editForm').on('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                fetch('update_prof.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire({
                            title: data.success ? "Success" : "Error",
                            text: data.message || (data.success ? "Professor updated successfully!" : "Failed to update professor."),
                            icon: data.success ? "success" : "error"
                        }).then(() => {
                            if (data.success) {
                                location.reload();
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Fetch Error:', error);
                        Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                    });
            });

            // Handle Delete Professor Button Click
            $(document).on('click', '.delete-btn', function () {
                let profId = $(this).data('prof-id'); // Ensure correct ID is retrieved

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'delete_prof.php',
                            type: 'POST',
                            data: { prof_id: profId },
                            dataType: 'json',
                            success: function (response) {
                                if (response.success) {
                                    $(`#professor-list tr[data-id="${profId}"]`).remove();
                                    Swal.fire('Deleted!', 'Professor has been deleted.', 'success');
                                } else {
                                    Swal.fire('Error!', response.error || 'Failed to delete professor.', 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('Error!', 'An error occurred while processing the request.', 'error');
                            }
                        });
                    }
                });
            });

        });

    </script>

</body>

</html>