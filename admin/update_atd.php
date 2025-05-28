<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Management</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        *{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        input,
        button {
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: calc(100% - 22px); /* Adjust width for padding */
        }

        button {
            background-color: #007bff;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
            text-align: center;
        }

        .status-dropdown {
            padding: 5px;
        }

        .loading {
            text-align: center;
            font-size: 18px;
            color: #888;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Attendance Management</h2>
        <input type="text" id="regNo" placeholder="Enter Registration Number" required>
        <button id="searchBtn">Search</button>

        <div id="attendanceTable"></div>
    </div>

    <script>
        document.getElementById("searchBtn").addEventListener("click", function () {
            const regNo = document.getElementById("regNo").value.trim();
            if (!regNo) {
                Swal.fire("Error", "Please enter a registration number.", "error");
                return;
            }

            const tableContainer = document.getElementById("attendanceTable");
            tableContainer.innerHTML = `<div class="loading">Loading...</div>`;

            // Fetch attendance records via AJAX
            fetch("fetch_attendance.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `reg_no=${regNo}`,
            })
                .then((response) => response.text())
                .then((data) => {
                    tableContainer.innerHTML = data;

                    // Attach event listeners to dropdowns
                    document.querySelectorAll(".status-dropdown").forEach(dropdown => {
                        dropdown.addEventListener("change", function () {
                            const id = this.dataset.id;
                            const status = this.value;

                            updateAttendance(id, status);
                        });
                    });
                })
                .catch((error) => {
                    Swal.fire("Error", "Failed to fetch data.", "error");
                    console.error("Error:", error);
                });
        });

        function updateAttendance(id, status) {
            // Send AJAX request to update attendance status
            fetch("update_attendance.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `id=${id}&status=${status}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire("Success", "Attendance status updated successfully!", "success");
                    } else {
                        Swal.fire("Error", data.message, "error");
                    }
                })
                .catch(error => {
                    Swal.fire("Error", "An unexpected error occurred.", "error");
                    console.error(error);
                });
        }
    </script>
</body>

</html>
