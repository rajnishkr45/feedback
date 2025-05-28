<?php
include 'admin_name.php';
?>
<!DOCTYPE html>
<html lang="en">
<?php
include './dependencies.php';
?>
<link rel="stylesheet" href="../css/form.css?v=1">
<body>
    <!-- SIDEBAR -->
    <?php
    include 'navbar.php';
    ?>
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
                                <a class="active" href="#">Assign Class</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="form-container">
                    <h2 class="intro">Assign Classes</h2>
                    <form id="assignClassForm">
                        <div class="cluster">
                            <div class="input-group">
                                <label for="semester">Select Semester:</label>
                                <select id="semester" name="semester">
                                    <option value="" disabled selected> select semester </option>
                                    <option value="1">1st Semester</option>
                                    <option value="2">2nd Semester</option>
                                    <option value="3">3rd Semester</option>
                                    <option value="4">4th Semester</option>
                                    <option value="5">5th Semester</option>
                                    <option value="6">6th Semester</option>
                                    <option value="7">7th Semester</option>
                                    <option value="8">8th Semester</option>
                                </select>
                            </div>

                            <div class="input-group">
                                <label for="branch">Select Department:</label>
                                <select id="branch" name="branch">
                                    <option value="" selected hidden> Choose Department </option>
                                    <option value="CSE">CSE</option>
                                    <option value="CYB">CYBER</option>
                                    <option value="EE">EEE</option>
                                    <option value="ME">ME</option>
                                    <option value="CE">Civil</option>
                                    <option value="FTS">FTS</option>
                                </select>
                            </div>
                        </div>

                        <div class="input-group">
                            <label for="subject">Select Subject:</label>
                            <select id="subject" name="subject_id">
                                <option value="" disabled selected> select subjects </option>

                                <!-- Subject options will be dynamically populated based on the selected semester and branch -->
                            </select>
                        </div>

                        <div class="input-group">
                            <label for="professor">Select Professor:</label>
                            <select id="professor" name="professor_id">
                                <option value="" disabled selected> select professor </option>
                                <!-- Professor options will be loaded via PHP -->
                            </select>
                        </div>

                        <button type="submit">Assign Class</button>
                    </form>
                    <br>
                    View <a href="total_class.php">Assigned classes</a>
                </div>
            </main>
        </section>
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Fetch professors from the database and populate the dropdown
                fetch("fetch_professor.php")
                    .then(response => response.json())
                    .then(data => {
                        let professorSelect = document.getElementById('professor');
                        data.forEach(professor => {
                            let option = document.createElement('option');
                            option.value = professor.prof_id;
                            option.textContent = professor.name;
                            professorSelect.appendChild(option);
                        });
                    });

                // Function to dynamically load subjects based on semester and branch
                function loadSubjects() {
                    let semester = document.getElementById('semester').value;
                    let branch = document.getElementById('branch').value;

                    fetch(`fetch_subjects.php?semester=${semester}&branch=${branch}`)
                        .then(response => response.json())
                        .then(data => {
                            let subjectSelect = document.getElementById('subject');
                            subjectSelect.innerHTML = ""; // Clear current options

                            data.forEach(subject => {
                                let option = document.createElement('option');
                                option.value = subject.subject_id;
                                option.textContent = subject.subject_name;
                                subjectSelect.appendChild(option);
                            });
                        });
                }

                // Load subjects when either semester or branch changes
                document.getElementById("semester").addEventListener("change", loadSubjects);
                document.getElementById("branch").addEventListener("change", loadSubjects);

                // Handle form submission with AJAX and validation
                document.getElementById("assignClassForm").addEventListener("submit", function (e) {
                    e.preventDefault();

                    // Custom form validation
                    let semester = document.getElementById('semester').value;
                    let branch = document.getElementById('branch').value;
                    let professor = document.getElementById('professor').value;
                    let subject = document.getElementById('subject').value;

                    if (!semester || !branch || !professor || !subject) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Empty fields',
                            text: 'Please fill out all fields before submitting.',
                        });
                        return;
                    }

                    let formData = new FormData(this);

                    fetch('assignClass.php', {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: result.message,
                                }).then(() => {
                                    // Clear the form after the alert is closed
                                    document.getElementById("assignClassForm").reset();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: result.message,
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Something went wrong!',
                            });
                        });
                });
            });
        </script>
</body>

</html>