<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['student_id']) || $_SESSION['account_type'] != 0) {
    header("Location: login.php");
    exit;
}

include("connection.php");

// Retrieve users information from the database
$sql = "SELECT users_info.student_id, users_info.first_name, users_info.last_name, users_info.student_email, users_program.Program, users_permissions.account_type FROM users_info INNER JOIN users_program ON users_info.student_id = users_program.student_id INNER JOIN users_permissions ON users_info.student_id = users_permissions.student_id";
$stmt = $conn->prepare($sql);
$stmt->execute();
$stmt->bind_result($student_id, $first_name, $last_name, $student_email, $program, $account_type);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User List</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <h1>User List</h1>
    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Program</th>
                <th>Account Type</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["student_id"] . "</td>";
                    echo "<td>" . $row["first_name"] . "</td>";
                    echo "<td>" . $row["last_name"] . "</td>";
                    echo "<td>" . $row["student_email"] . "</td>";
                    echo "<td>" . $row["Program"] . "</td>";
                    echo "<td>" . ($row["account_type"] == 0 ? "Admin" : "Regular User") . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>No users found</td></tr>";
            }
            ?>
        </tbody>
    </table>
    <a href="index.php">Go back to Home</a>
</body>

</html>
