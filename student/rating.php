<?php
include 'std_name.php';

// Fetch student's semester and branch based on their session email
$student_email = $_SESSION['email'];
$student_data = $conn->query("SELECT semester, branch FROM students WHERE email = '$student_email'")->fetch_assoc();
$student_semester = $student_data['semester'];
$student_branch = $student_data['branch'];

// Fetch questions for rating
$questions = $conn->query("SELECT question_id, question_text FROM feedback_questions");

// Fetch subjects based on student's semester and branch
$subjects = $conn->query("SELECT subject_id, subject_name FROM subjects WHERE semester = '$student_semester' AND branch = '$student_branch'");

$subject_options = '';
while ($row = $subjects->fetch_assoc()) {
    $subject_options .= '<option value="' . $row['subject_id'] . '">' . $row['subject_name'] . '</option>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AICTE | Dashboard</title>

    <script src="../js/jscript.js?v=1.1" defer></script>
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/form.css?v=1.2">
</head>

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
                <img src="../dp/<?php echo $profilePicture ?? 'default.png'; ?>">
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
                            <a class="active" href="#">Feedback</a>
                        </li>
                    </ul>
                </div>
                <a href="#" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Download PDF</span>
                </a>
            </div>

            <div class="form-container" style="max-width:750px; min-width: 350px;">
                <h2 clas="intro">Rate Professor</h2>
                <form id="ratingForm">
                    <div class="input-group">
                        <label for="semester">Select Semester:</label>
                        <select name="semester" id="semester">
                            <option value="<?= $student_semester; ?>" selected><?= $student_semester; ?></option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="subject">Select Subject:</label>
                        <select name="subject" id="subject">
                            <option value="">--Select Subject--</option>
                            <?= $subject_options; ?>
                        </select>
                    </div>
                    <div class="input-group">
                        <label for="professor">Select Professor:</label>
                        <select name="professor_id" id="professor">
                            <option value="">--Select Professor--</option>
                        </select>
                    </div>

                    <div id="questions">
                        <?php if ($questions->num_rows > 0): ?>
                            <h3 style="font-weight:600; font-size:18px ; color:#ff8843; margin:5px auto;">Rate the following
                                questions (1-10):
                            </h3>
                            <?php while ($row = $questions->fetch_assoc()): ?>
                                <div class="input-group">
                                    <label for="question_<?php echo $row['question_id']; ?>">
                                        <?php echo $row['question_text']; ?>
                                    </label>
                                    <select name="rating[<?php echo $row['question_id']; ?>]"
                                        id="question_<?php echo $row['question_id']; ?>">
                                        <option value="">--Select Rating--</option>
                                        <?php for ($i = 1; $i <= 10; $i++): ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No questions available for rating.</p>
                        <?php endif; ?>
                    </div>

                    <button type="submit">Submit Rating</button>
                </form>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            // Fetch professors based on the student's semester and branch
            $('#subject').change(function () {
                let selectedSubject = $(this).val();
                let semester = "<?= $student_semester; ?>"; // Default semester
                let branch = "<?= $student_branch; ?>"; // Default branch

                if (selectedSubject) {
                    $.ajax({
                        url: 'fetch_professors.php',
                        type: 'GET',
                        data: {
                            subject_id: selectedSubject,
                            semester: semester,
                            branch: branch
                        },
                        success: function (data) {
                            $('#professor').html(data);
                        }
                    });
                } else {
                    $('#professor').html('<option value="">--Select Professor--</option>');
                }
            });


            $('#ratingForm').on('submit', function (e) {
                e.preventDefault();

                if (validateForm()) {
                    Swal.fire({
                        title: 'Submitting Feedback...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    let ratings = {};
                    $('#questions select').each(function () {
                        let questionId = $(this).attr('id').split('_')[1]; // Extract question ID
                        ratings[questionId] = $(this).val(); // Store rating
                    });

                    let feedbackData = {
                        professor_id: $('#professor').val(),
                        semester: $('#semester').val(),
                        subject: $('#subject').val(),
                        ratings: ratings
                    };

                    $.ajax({
                        url: 'process_ratings.php',
                        type: 'POST',
                        contentType: 'application/json',  // Send JSON
                        data: JSON.stringify(feedbackData),
                        success: function (response) {
                            const res = JSON.parse(response);

                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Thank you!',
                                    text: res.message
                                });
                                $('#ratingForm')[0].reset();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: res.message || 'Failed to submit your rating. Please try again.'
                                });
                            }
                        },
                        error: function () {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'There was an error processing your request. Please try again.'
                            });
                        }
                    });
                }
            });

            function validateForm() {
                const professor = document.getElementById('professor').value;
                const subject = document.getElementById('subject').value;
                const ratings = document.querySelectorAll('#questions select');

                if (!professor || !subject || Array.from(ratings).some(select => !select.value)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Empty Field!',
                        text: 'Please, fill all fields!'
                    });
                    return false;
                }
                return true;
            }
        });
    </script>
    
</body>
</html>