<?php
include 'std_name.php';

$student_email = $_SESSION['email'];
$student_data = $conn->query("SELECT semester, branch FROM students WHERE email = '$student_email'")->fetch_assoc();
$student_semester = $student_data['semester'];
$student_branch = $student_data['branch'];

$questions = $conn->query("SELECT question_id, question_text FROM feedback_questions");

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
    <title>Professor Feedback</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- SweetAlert -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom Style -->
    <style>
        body {
            background: #f1f4f9;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            border-radius: 15px;
        }

        .card-header {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }

        .star-rating .fa-star {
            font-size: 1.6rem;
            color: #ccc;
            margin-right: 6px;
            cursor: pointer;
            transition: color 0.2s, transform 0.2s;
        }

        .star-rating .fa-star.checked {
            color: #ffc107;
            transform: scale(1.2);
        }

        .question-box {
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            background-color: #ffffff;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .form-label {
            font-weight: 500;
        }

        .btn-success {
            font-weight: 500;
            font-size: 1rem;
            padding: 10px;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">Professor Feedback Form</h4>
                    </div>
                    <div class="card-body">
                        <form id="ratingForm">
                            <div class="mb-3">
                                <label class="form-label">Semester</label>
                                <input type="text" class="form-control" value="<?= $student_semester; ?>" disabled>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <select class="form-select" name="subject" id="subject">
                                    <option value="">-- Select Subject --</option>
                                    <?= $subject_options; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Professor</label>
                                <select class="form-select" name="professor_id" id="professor">
                                    <option value="">-- Select Professor --</option>
                                </select>
                            </div>

                            <?php if ($questions->num_rows > 0): ?>
                                <h5 class="text-secondary mb-3">Rate the following (1 to 10 stars)</h5>
                                <?php while ($q = $questions->fetch_assoc()): ?>
                                    <div class="question-box">
                                        <label class="form-label"><?= $q['question_text']; ?></label>
                                        <div class="star-rating" data-question-id="<?= $q['question_id']; ?>">
                                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                                <i class="fa fa-star" data-value="<?= $i; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="alert alert-warning">No questions available for feedback.</div>
                            <?php endif; ?>

                            <button type="submit" class="btn btn-success w-100 mt-3">Submit Feedback</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            const ratings = {};

            // ⭐ Handle star click
            $('.star-rating .fa-star').on('click', function () {
                const value = $(this).data('value');
                const container = $(this).closest('.star-rating');
                const questionId = container.data('question-id');
                ratings[questionId] = value;

                container.find('.fa-star').each(function (index) {
                    $(this).toggleClass('checked', index < value);
                });
            });

            // 📚 Fetch professors based on subject
            $('#subject').change(function () {
                const subject_id = $(this).val();
                const semester = '<?= $student_semester ?>';
                const branch = '<?= $student_branch ?>';

                if (subject_id) {
                    $.get('fetch_professors.php', { subject_id, semester, branch }, function (data) {
                        $('#professor').html(data);
                    });
                } else {
                    $('#professor').html('<option value="">-- Select Professor --</option>');
                }
            });

            // 📩 Submit feedback
            $('#ratingForm').on('submit', function (e) {
                e.preventDefault();

                const subject = $('#subject').val();
                const professor = $('#professor').val();
                const totalQuestions = $('.star-rating').length;
                const answeredCount = Object.keys(ratings).length;

                if (!subject || !professor || answeredCount < totalQuestions) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Incomplete Feedback',
                        text: 'Please select subject, professor, and rate all questions.'
                    });
                    return;
                }

                $.ajax({
                    url: 'process_ratings.php',
                    type: 'POST',
                    contentType: 'application/json',
                    dataType: 'json', // ✅ Ensures jQuery auto-parses JSON
                    data: JSON.stringify({
                        professor_id: professor,
                        semester: '<?= $student_semester ?>',
                        subject: subject,
                        ratings: ratings
                    }),
                    success: function (res) {
                        Swal.fire({
                            icon: res.success ? 'success' : 'error',
                            title: res.success ? 'Thank you!' : 'Error',
                            text: res.message || 'Something went wrong. Please try again.'
                        });

                        if (res.success) {
                            $('#ratingForm')[0].reset();
                            $('.fa-star').removeClass('checked');
                            Object.keys(ratings).forEach(key => delete ratings[key]);
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Server error. Please try again later.', 'error');
                    }
                });
            });
        });
    </script>


</body>

</html>