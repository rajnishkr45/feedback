<?php
include 'admin_name.php';
$sessions = $conn->query("SELECT session_id, session_name FROM sessions ORDER BY session_id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>ACR Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container my-5">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary text-white text-center">
                <h3 class="mb-0">📊 ACR Dashboard</h3>
            </div>

            <div class="card-body">
                <div class="row g-3 align-items-end mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Select Session</label>
                        <select id="sessionSelect" class="form-select">
                            <option value="">-- Select Session --</option>
                            <?php while ($row = $sessions->fetch_assoc()) { ?>
                                <option value="<?= $row['session_id'] ?>">
                                    <?= htmlspecialchars($row['session_name']) ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <button id="fetchData" class="btn btn-success w-100">
                            <i class="bi bi-bar-chart-fill"></i> Show ACR
                        </button>
                    </div>
                </div>

                <div id="acrContainer" style="display:none;">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover text-center align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Rank</th>
                                    <th>Professor Name</th>
                                    <th>Feedback (25)</th>
                                    <th>Contribution (10)</th>
                                    <th>Teaching (25)</th>
                                    <th>Institute (10)</th>
                                    <th>Department (20)</th>
                                    <th>ACR (10)</th>
                                    <th>Total (100)</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        $("#fetchData").click(function () {
            const sessionId = $("#sessionSelect").val();
            if (!sessionId) {
                Swal.fire("⚠️ Error", "Please select a session!", "error");
                return;
            }

            $.post("fetch_acr_data.php", { session_id: sessionId }, function (data) {
                const res = JSON.parse(data);
                if (res.success) {
                    let rows = "";
                    res.professors.forEach((prof, index) => {
                        rows += `
                        <tr ${index === 0 ? 'class="table-success fw-bold"' : ''}>
                            <td><b>${index + 1}</b></td>
                            <td style="text-align:left;">${prof.name}</td>
                            <td>${prof.feedback}</td>
                            <td>${prof.contribution}</td>
                            <td>${prof.teaching}</td>
                            <td>${prof.institute}</td>
                            <td>${prof.department}</td>
                            <td>${prof.acr}</td>
                            <td><b>${prof.total}</b></td>
                        </tr>`;
                    });
                    $("#acrContainer tbody").html(rows);
                    $("#acrContainer").fadeIn();
                } else {
                    Swal.fire("Error", res.message, "error");
                }
            });
        });
    </script>
</body>

</html>