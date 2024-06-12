<?php
session_start();
// Check if the user is logged in
if(!isset($_SESSION['student_id'])) {
   // If the user is not logged in, redirect to the login page
   header("Location: login.php");
   exit;
}
// Retrieve student ID
$student_id = isset($_SESSION['student_id']) ? $_SESSION['student_id'] : "";
$first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : "";
$last_name = isset($_SESSION['last_name']) ? $_SESSION['last_name'] : "";
$dob = isset($_SESSION['DOB']) ? $_SESSION['DOB'] : "";
$student_email = isset($_SESSION['student_email']) ? $_SESSION['student_email'] : "";
$program = isset($_SESSION['program']) ? $_SESSION['program'] : "";
$avatar = isset($_SESSION['avatar']) ? $_SESSION['avatar'] : "";

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="utf-8">
   <title>Register on SYSCX</title>
   <link rel="stylesheet" href="assets/css/reset.css">
   <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
   <header>
      <h1>SYSCX</h1>
      <p>Social media for SYSC students in Carleton University</p>
   </header>
   <nav>
      <ul>
      <?php
        // Check if the user is logged in
        if (isset($_SESSION['student_id'])) {
            // Regular user or admin
            echo '<li><a href="http://127.0.0.1/SYSC4504_Labs/SYSCX/index.php">Home</a></li>';
            echo '<li><a href="http://127.0.0.1/SYSC4504_Labs/SYSCX/profile.php">Profile</a></li>';
            echo '<li><a href="http://127.0.0.1/SYSC4504_Labs/SYSCX/logout.php">Log out</a></li>';
            
            // Check if the user is an admin
            if ($_SESSION['account_type'] == 'Admin') {
                echo '<li><a href="http://127.0.0.1/SYSC4504_Labs/SYSCX/user_list.php">User List</a></li>';
            }
        } else {
            // Not logged in
            echo '<li><a href="http://127.0.0.1/SYSC4504_Labs/SYSCX/index.php">Home</a></li>';
            echo '<li><a href="http://127.0.0.1/SYSC4504_Labs/SYSCX/login.php">Login</a></li>';
            echo '<li><a href="http://127.0.0.1/SYSC4504_Labs/SYSCX/register.php">Register</a></li>';
        }
      ?>
     </ul>
   </nav>
   <main>
      <section>
         <form method="POST" action="">
            <fieldset>
            <legend><span>NEW POST</span></legend>
            <table>
               <tbody>
                  <tr>
                     <td>
                     <textarea name="new_post" rows="5" cols="50" placeholder="What's happening?! (max 200 characters)"></textarea><br>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <input type="submit" name="index_submit" value="Post">
                        <input type="reset" value="Reset">
                     </td>
                  </tr>
               </tbody>
            </table>
         </fieldset>
         </form>
      </section>
      <section>
         <h2>Posts</h2>
         <?php
            // Retrieve and display the last 5 added posts by the current user
            include("connection.php");
            $conn = new mysqli($server_name, $username, $password, $database_name);
            if ($conn->connect_error) {
                die("Error: Couldn't connect.");
            }

            // Retrieve and display the last 5 posts by the current user
            $sql = "SELECT * FROM users_posts WHERE student_id = ? ORDER BY post_date DESC LIMIT 10";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $student_id); // Assuming student_id is an integer
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<article>";
                    echo "<details>";
                    echo "<summary>Post " . $row["post_id"] . "</summary>";
                    echo "<p>" . $row["new_post"] . "</p>";
                    echo "</details>";
                    echo "</article>";
                }
            } else {
                echo "No posts yet.";
            }
            $conn->close();
            ?>
      </section>
   </main>
   <?php
      // Check if the user is logged in
      if(isset($_SESSION['student_id'])) {
         // Display user info only if the user is logged in
   ?>
   <div class="userInfo">
      <ul>
         <li><?php echo $first_name . " " . $last_name; ?></li>
         <li><img src="images/img_avatar<?php echo $avatar; ?>.png" alt="Avatar"></li>
         <li>Email: <?php echo $student_email; ?></li>
         <li>Program: <?php echo $program; ?></li>
      </ul>
   </div>
   <?php
      } else {
         // If the user is not logged in, display an empty sidebar
         echo "<div class='userInfo'></div>";
      }
   ?>
   <?php
    // Handle new post submission
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST["new_post"])) {
        include("connection.php");
        $conn = new mysqli($server_name, $username, $password, $database_name);
        if($conn->connect_error){
            die("Error: Couldn't connect.");
        }
        $new_post = $_POST['new_post'];

        // Insert new post into users_posts table
        $sql = "INSERT INTO users_posts (student_id, new_post) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $student_id, $new_post);
        $stmt->execute();
        $stmt->close();

        $conn->close();
        
        // Refresh the page
        header("Location: index.php");
        exit();
    }
    ?>
</body>
</html>
