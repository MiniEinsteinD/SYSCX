<?php
   // Start the session
   session_start();
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
            echo '<li><a href="http://127.0.0.1/SYSC4504_Labs/Daniah_Mohammed_a03/index.php">Home</a></li>';
            echo '<li><a href="http://127.0.0.1/SYSC4504_Labs/Daniah_Mohammed_a03/profile.php">Profile</a></li>';
            echo '<li><a href="http://127.0.0.1/SYSC4504_Labs/Daniah_Mohammed_a03/logout.php">Log out</a></li>';
            
            // Check if the user is an admin
            if ($_SESSION['account_type'] == 'Admin') {
                echo '<li><a href="http://127.0.0.1/SYSC4504_Labs/Daniah_Mohammed_a03/user_list.php">User List</a></li>';
            }
        } else {
            // Not logged in
            echo '<li><a href="http://127.0.0.1/SYSC4504_Labs/Daniah_Mohammed_a03/index.php">Home</a></li>';
            echo '<li><a href="http://127.0.0.1/SYSC4504_Labs/Daniah_Mohammed_a03/login.php">Login</a></li>';
            echo '<li><a href="http://127.0.0.1/SYSC4504_Labs/Daniah_Mohammed_a03/register.php">Register</a></li>';
        }
      ?>
     </ul>
   </nav>
   <main>
      <section>
         <h2>REGISTER A NEW PROFILE</h2>
         <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <fieldset>
            <legend><h3><span>Personal information</span></h3></legend>
            <table>
               <tbody>
                  <tr>
                     <td>
                        <label>First Name: </label>
                        <input type="text" name="first_name" placeholder="ex: John Snow"/>
                     </td>
                     <td>
                        <label>Last Name: </label>
                        <input type="text" name="last_name"/>
                     </td>
                     <td>
                        <label>DOB: </label>
                        <input type="date" name="DOB"/>
                     </td>
                  </tr>
               </tbody>
            </table>

            <h3><span>Profile INFORMATION</span></h3>
            <table>
               <tbody>
                  <tr>
                     <td>
                        <label>Email address </label>
                        <input type="text" name="student_email"/>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <label>Program </label>
                        <select name="program">
                           <option>Choose Program</option>
                           <option>Computer Systems Engineering</option>
                           <option>Software Engineering</option>
                           <option>Communications Engineering</option>
                           <option>Biomedical and Electrical</option>
                           <option>Electrical Engineering</option>
                           <option>Special</option>
                       </select>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <label>Password: </label>
                        <input type="password" name="password" id="password"/>
                     </td>
                     <td>
                        <label>Confirm Password: </label>
                        <input type="password" name="confirm_password" id="confirm_password"/>
                        <span id="password_error" style="color: red;"></span>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <input type="submit" name="submit_register" value="Register">
                        <input type="reset" value="Reset">
                     </td>
                  </tr>
               </tbody>
            </table>
         </fieldset>
         </form>
         <script>
            function validateForm() {
               var password = document.getElementById("password").value;
               var confirmPassword = document.getElementById("confirm_password").value;
               if (password != confirmPassword) {
                  document.getElementById("password_error").innerText = "Passwords do not match.";
                  return false;
               }
               return true;
            }
         </script>
         <?php
            include("connection.php");
            // Connect to the database
            $conn = new mysqli($server_name, $username, $password, $database_name);
            if ($conn->connect_error) {
               die("Error: Couldn't connect to the database.");
            }

            // Check if form is submitted
            if (isset($_POST['submit_register'])) {
               // Retrieve form data
               $first_name = $_POST['first_name'];
               $last_name = $_POST['last_name'];
               $dob = $_POST['DOB'];
               $email = $_POST['student_email'];
               $program = $_POST['program'];
               $password = $_POST['password'];
               
            
               // Check if the email exists in the database
               $sql = "SELECT * FROM users_info WHERE student_email = ?";
               $stmt = $conn->prepare($sql);
               $stmt->bind_param("s", $email);
               $stmt->execute();
               $result = $stmt->get_result();
               if ($result->num_rows > 0) {
                  echo "<p style='color: red;'>Email address already exists. Please enter a new email address.</p>";
                  $stmt->close();
               } else {
                  // Hash the password
                  $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                  // Insert data into users_info table
                  $sql = "INSERT INTO users_info (student_email, first_name, last_name, DOB) VALUES (?, ?, ?, ?)";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("ssss", $email, $first_name, $last_name, $dob);
                  $stmt->execute();

                  // Get the auto-generated student_id
                  $student_id = $stmt->insert_id;
                  $stmt->close();

                  // Insert data into users_passwords table
                  $sql = "INSERT INTO users_passwords (student_id, password) VALUES (?, ?)";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("is", $student_id, $hashed_password);
                  $stmt->execute();
                  $stmt->close();

                  // Insert data into users_info table
                  $sql = "INSERT INTO users_info (student_email, first_name, last_name, DOB) VALUES (?, ?, ?, ?)";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("ssss", $email, $first_name, $last_name, $dob);
                  $stmt->execute();
                  $stmt->close();
                  
                  $sql = "SELECT LAST_INSERT_ID() AS student_id";
                  $result = $conn->query($sql);
                  $row = $result->fetch_assoc();
                  $student_id = $row['student_id'];

                  // Insert data into users_program table
                  $sql = "INSERT INTO users_program (student_id, Program) VALUES (?, ?)";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("is", $student_id, $program);
                  $stmt->execute();
                  $stmt->close();

                  $sql = "INSERT INTO users_avatar (student_id, avatar) VALUES (?, 0)";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("i", $student_id);
                  $stmt->execute();
                  $stmt->close();

                  $sql = "INSERT INTO users_address (student_id, street_number, street_name, city, province, postal_code) VALUES (?, 0, NULL, NULL, NULL, NULL)";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("i", $student_id);
                  $stmt->execute();
                  $stmt->close();

                  // Insert data into users_permissions table
                  $sql = "INSERT INTO users_permissions (student_id, account_type) VALUES (?, 1)";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("i", $student_id);
                  $stmt->execute();
                  $stmt->close();

                  

                  // Set session variables
                  $_SESSION['student_id'] = $student_id;
                  $_SESSION['first_name'] = $first_name;
                  $_SESSION['last_name'] = $last_name;
                  $_SESSION['DOB'] = $dob;
                  $_SESSION['student_email'] = $email;
                  $_SESSION['program'] = $program;
                  
                  //Redirect to profile.php
                  header("Location: profile.php");
                  exit;
               }
            }
            $conn->close();
         ?>
      </section>
   </main> 
</body>
</html>
