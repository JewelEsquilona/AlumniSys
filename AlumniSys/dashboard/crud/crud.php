<?php
session_start();

// Database connection
$host = 'fdb1032.awardspace.net'; // Your database host
$user = '4451373_senku';          // Your database username
$password = ':!_[5@XA9%BwbD_j';   // Replace with your actual password
$dbname = '4451373_senku';        // Your database name

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Enable error reporting
$con = mysqli_connect($host, $user, $password, $dbname);
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle deletion if requested
if (isset($_POST['delete_alumni'])) {
    $alumni_id = mysqli_real_escape_string($con, $_POST['delete_alumni']);

    // Start transaction
    $con->begin_transaction();

    try {
        // Delete from tbl_2024_2025_ws first to avoid foreign key constraint issues
        $delete_ws_query = "DELETE FROM tbl_2024_2025_ws WHERE Alumni_ID_Number='$alumni_id'";
        mysqli_query($con, $delete_ws_query);

        // Now delete from tbl_2024_2025
        $delete_query = "DELETE FROM tbl_2024_2025 WHERE Alumni_ID_Number='$alumni_id'";
        mysqli_query($con, $delete_query);

        // Commit transaction
        $con->commit();
        $_SESSION['message'] = "Alumni Deleted Successfully";
    } catch (Exception $e) {
        // Rollback transaction on error
        $con->rollback();
        $_SESSION['message'] = "Alumni Not Deleted: " . $e->getMessage();
    }

    header("Location: index.php");
    exit(0);
}

// Fetch alumni details
$query = "
SELECT 
    a.Student_Number, 
    a.Last_Name, 
    a.First_Name, 
    a.Middle_Name,
    a.Course,
    a.Department, 
    a.Section, 
    a.Year_Graduated, 
    a.Contact_Number, 
    a.Personal_Email, 
    ws.Working_Status,
    a.Alumni_ID_Number
FROM 
    tbl_2024_2025 a
LEFT JOIN 
    tbl_2024_2025_ws ws ON a.Alumni_ID_Number = ws.Alumni_ID_Number
";
$query_run = mysqli_query($con, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alumni Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
       html,
body {
    height: 100%;
    margin: 0;
    background: linear-gradient(45deg, #49a09d, #5f2c82);
    font-family: sans-serif;
    font-weight: 100;
}

.container {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

table {
    width: 800px;
    border-collapse: collapse;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
}

th,
td {
    padding: 15px;
    background-color: rgba(255, 255, 255, 0.2);
    color: #fff;
}

th {
    text-align: left;
    background-color: #55608f; /* Moved background color for th directly */
}

tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.3);
}

td {
    position: relative;
}

td:hover::before {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    top: -9999px;
    bottom: -9999px;
    background-color: rgba(255, 255, 255, 0.2);
    z-index: -1;
}

    </style>
</head>
<body>
    <div class="container mt-4">
        <h4>Alumni Details</h4>

        <!-- Link to Add Alumni Form -->
        <a href="alumni-add.php" class="btn btn-primary mb-3">Add New Alumni</a>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-info">
                <?= htmlspecialchars($_SESSION['message'] ?? ''); ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Student Number</th>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Course</th>
                    <th>Department</th>
                    <th>Section</th>
                    <th>Year Graduated</th>
                    <th>Contact Number</th>
                    <th>Personal Email</th>
                    <th>Working Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($query_run) > 0) {
                    foreach ($query_run as $alumni) {
                ?>
                        <tr>
                            <td><?= htmlspecialchars($alumni['Student_Number'] ?? '') ?></td>
                            <td><?= htmlspecialchars($alumni['Last_Name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($alumni['First_Name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($alumni['Middle_Name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($alumni['Course'] ?? '') ?></td>
                            <td><?= htmlspecialchars($alumni['Department'] ?? '') ?></td>
                            <td><?= htmlspecialchars($alumni['Section'] ?? '') ?></td>
                            <td><?= htmlspecialchars($alumni['Year_Graduated'] ?? '') ?></td>
                            <td><?= htmlspecialchars($alumni['Contact_Number'] ?? '') ?></td>
                            <td><?= htmlspecialchars($alumni['Personal_Email'] ?? '') ?></td>
                            <td><?= htmlspecialchars($alumni['Working_Status'] ?? '') ?></td>
                            <td>
                                <a href="alumni-edit.php?id=<?= htmlspecialchars($alumni['Alumni_ID_Number'] ?? '') ?>" class="btn btn-warning btn-sm">Edit</a>
                                <form action="index.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="delete_alumni" value="<?= htmlspecialchars($alumni['Alumni_ID_Number'] ?? '') ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this record?');">Delete</button>
                                </form>
                            </td>
                        </tr>
                <?php
                    }
                } else {
                    echo "<tr><td colspan='12'>No Record Found</td></tr>";
                }
                ?>
            </tbody>
        </table>
   </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close database connection
mysqli_close($con);
?>
