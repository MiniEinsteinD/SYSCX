<?php
   include("connection.php");
   session_start();
   // Check if the user is not logged in, redirect to login.php
   if (!isset($_SESSION['student_id'])) {
      header("Location: login.php");
      exit(); // Ensure script stops executing after redirection
   }
   if (isset($_POST["submit_profile"])) {
      // Connect to the database
      $conn = new mysqli($server_name, $username, $password, $database_name);
      if ($conn->connect_error) {
         die("Error: Couldn't connect to the database.");
      }

      // Retrieve form data
      $first_name = $_POST['first_name'];
      $last_name = $_POST['last_name'];
      $dob = $_POST['DOB'];
      $email = $_POST['student_email'];
      $program = $_POST['program'];
      $avatar = $_POST['avatar'];
      $street_number = $_POST['street_number'];
      $street_name = $_POST['street_name'];
      $city = $_POST['city'];
      $province = $_POST['province'];
      $postal_code = $_POST['postal_code'];


      // Set session variables
      $_SESSION['first_name'] = $_POST['first_name'];
      $_SESSION['last_name'] = $_POST['last_name'];
      $_SESSION['DOB'] = $_POST['DOB'];
      $_SESSION['student_email'] = $_POST['student_email'];
      $_SESSION['program'] = $_POST['program'];
      $_SESSION['street_number'] = $_POST['street_number'];
      $_SESSION['street_name'] = $_POST['street_name'];
      $_SESSION['city'] = $_POST['city'];
      $_SESSION['province'] = $_POST['province'];
      $_SESSION['postal_code'] = $_POST['postal_code'];
      $_SESSION['avatar'] = $_POST['avatar'];

      // Ensure student_id is set
      if(isset($_SESSION['student_id'])) {
         $student_id = $_SESSION['student_id'];

         // Update users_info table
         $sql = "UPDATE users_info SET first_name = ?, last_name = ?, DOB = ?, student_email = ? WHERE student_id = ?";
         $stmt = $conn->prepare($sql);
         $stmt->bind_param("ssssi", $first_name, $last_name, $dob, $email, $student_id);
         $stmt->execute();
         $stmt->close();

         // Update users_address table
         $sql = "UPDATE users_address SET street_number = ?, street_name = ?, city = ?, province = ?, postal_code = ? WHERE student_id = ?";
         $stmt = $conn->prepare($sql);
         $stmt->bind_param("issssi", $street_number, $street_name, $city, $province, $postal_code, $student_id);
         $stmt->execute();
         $stmt->close();

         // Update users_program table
         $sql = "UPDATE users_program SET Program = ? WHERE student_id = ?";
         $stmt = $conn->prepare($sql);
         $stmt->bind_param("si", $program, $student_id);
         $stmt->execute();
         $stmt->close();

         // Update users_avatar table
         $sql = "UPDATE users_avatar SET avatar = ? WHERE student_id = ?";
         $stmt = $conn->prepare($sql);
         $stmt->bind_param("ii", $avatar, $student_id);
         $stmt->execute();
         $stmt->close();
   
         
      } else {
         echo "Error: Student ID not found.";
      }

      // Close the database connection
      $conn->close();
   }
   // Retrieve session variables
   if(isset($_SESSION['student_id'])) {
      $student_id = $_SESSION['student_id'];
      $first_name = isset($_SESSION['first_name']) ? $_SESSION['first_name'] : "";
      $last_name = isset($_SESSION['last_name']) ? $_SESSION['last_name'] : "";
      $dob = isset($_SESSION['DOB']) ? $_SESSION['DOB'] : "";
      $student_email = isset($_SESSION['student_email']) ? $_SESSION['student_email'] : "";
      $program = isset($_SESSION['program']) ? $_SESSION['program'] : "";
      $street_number = isset($_SESSION['street_number']) ? $_SESSION['street_number'] : "";
      $street_name = isset($_SESSION['street_name']) ? $_SESSION['street_name'] : "";
      $city = isset($_SESSION['city']) ? $_SESSION['city'] : "";
      $province = isset($_SESSION['province']) ? $_SESSION['province'] : "";
      $postal_code = isset($_SESSION['postal_code']) ? $_SESSION['postal_code'] : "";
      $avatar = isset($_SESSION['avatar']) ? $_SESSION['avatar'] : "";
   }



?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <title>Update SYSCX profile</title>
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
            
            if (isset($_SESSION['account_type'])) {
               // Check if the user is an admin
               if ($_SESSION['account_type'] == 'Admin') {
                   echo '<li><a href="http://127.0.0.1/SYSC4504_Labs/SYSCX/user_list.php">User List</a></li>';
               }
           } else {
               // If 'account_type' key is not set, assume regular user
               $_SESSION['account_type'] = 'Regular';
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
         <h2>Update Profile information</h2>
         <form method="POST" action="">
            <fieldset>
            <legend><h3><span>Personal information</span></h3></legend>
            <table>
               <tbody>
                  <tr>
                     <td>
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo $_SESSION['first_name']; ?>">
                     </td>
                     <td>
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo $last_name; ?>">
                     </td>
                     <td>
                        <label for="DOB">Date of Birth:</label>
                        <input type="date" id="DOB" name="DOB" value="<?php echo $dob; ?>">
                     </td>
                  </tr>
               </tbody>
            </table>
         
            <h3><span>ADDRESS</span></h3>
            <table>
               <tbody>
                  <tr>
                        <td>
                           <label>Street Number: </label>
                           <?php if(isset($street_number)) echo '<input type="number" name="street_number" value="' . $street_number . '"/>'; ?>
                        </td>
                        <td colspan="2">
                           <label>Street Name: </label>
                           <?php if(isset($street_name)) echo '<input type="text" name="street_name" value="' . $street_name . '"/>'; ?>
                        </td>
                  </tr>
                  <tr>
                        <td>
                           <label>City: </label>
                           <?php if(isset($city)) echo '<input type="text" name="city" value="' . $city . '"/>'; ?>
                        </td>
                        <td>
                           <label>Province: </label>
                           <?php if(isset($province)) echo '<input type="text" name="province" value="' . $province . '"/>'; ?>
                        </td>
                        <td>
                           <label>Postal Code: </label>
                           <?php if(isset($postal_code)) echo '<input type="text" name="postal_code" value="' . $postal_code . '"/>'; ?>
                        </td>
                  </tr>
               </tbody>
            </table>
            <h3><span>Profile INFORMATION</span></h3>
            <table>
               <tbody>
                  <tr>
                     <td>
                        <label for="student_email">Email:</label>
                        <input type="email" id="student_email" name="student_email" value="<?php echo $student_email; ?>">
                     </td>
                  </tr>
                  <tr>
                     <td>
                     <label for="program">Program:</label>
                     <select name="program" id="program">
                           <option value="Computer Systems Engineering" <?php if($program == "Computer Systems Engineering") echo "selected"; ?>>Computer Systems Engineering</option>
                           <option value="Software Engineering" <?php if($program == "Software Engineering") echo "selected"; ?>>Software Engineering</option>
                           <option value="Communications Engineering" <?php if($program == "Communications Engineering") echo "selected"; ?>>Communications Engineering</option>
                           <option value="Biomedical and Electrical" <?php if($program == "Biomedical and Electrical") echo "selected"; ?>>Biomedical and Electrical</option>
                           <option value="Electrical Engineering" <?php if($program == "Electrical Engineering") echo "selected"; ?>>Electrical Engineering</option>
                           <option value="Special" <?php if($program == "Special") echo "selected"; ?>>Special</option>
                     </select>
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <label>Choose your Avatar: </label>
                        <input type="radio" name="avatar" id="avatar" value="1" <?php if($avatar == "1") echo "checked"; ?> > <img src="images/img_avatar1.png" alt="img_avatar1">  
                        <input type="radio" name="avatar" id="avatar" value="2" <?php if($avatar == "2") echo "checked"; ?>> <img src="images/img_avatar2.png" alt="img_avatar2"> 
                        <input type="radio" name="avatar" id="avatar" value="3" <?php if($avatar == "3") echo "checked"; ?>> <img src="images/img_avatar3.png" alt="img_avatar3">  
                        <input type="radio" name="avatar" id="avatar" value="4" <?php if($avatar == "4") echo "checked"; ?>> <img src="images/img_avatar4.png" alt="img_avatar4"> 
                        <input type="radio" name="avatar" id="avatar" value="5" <?php if($avatar == "5") echo "checked"; ?>> <img src="images/img_avatar5.png" alt="img_avatar5"> 
                     </td>
                  </tr>
                  <tr>
                     <td>
                        <input type="submit" name="submit_profile" value="Submit">
                        <input type="reset" value="Reset">
                     </td>
                  </tr>
               </tbody>
            </table>
         </fieldset>
         </form>

         <!-- JavaScript to set input field value after page reload -->
         <script>
            // Retrieve updated first name value from session
            var updatedFirstName = '<?php echo isset($_SESSION["first_name"]) ? $_SESSION["first_name"] : ""; ?>';
            var updatedLastName = '<?php echo isset($_SESSION["last_name"]) ? $_SESSION["last_name"] : ""; ?>';
            var updatedDOB = '<?php echo isset($_SESSION["DOB"]) ? $_SESSION["DOB"] : ""; ?>';
            var updatedStudentEmail = '<?php echo isset($_SESSION["student_email"]) ? $_SESSION["student_email"] : ""; ?>';
            var updatedProgram = '<?php echo isset($_SESSION["program"]) ? $_SESSION["program"] : ""; ?>';
            var updatedStreetNumber = '<?php echo isset($_SESSION["street_number"]) ? $_SESSION["street_number"] : ""; ?>';
            var updatedStreetName = '<?php echo isset($_SESSION["street_name"]) ? $_SESSION["street_name"] : ""; ?>';
            var updatedCity = '<?php echo isset($_SESSION["city"]) ? $_SESSION["city"] : ""; ?>';
            var updatedProvince = '<?php echo isset($_SESSION["province"]) ? $_SESSION["province"] : ""; ?>';
            var updatedPostalCode = '<?php echo isset($_SESSION["postal_code"]) ? $_SESSION["postal_code"] : ""; ?>';
            var updatedAvatar = '<?php echo isset($_SESSION["avatar"]) ? $_SESSION["avatar"] : ""; ?>';

            // Set input field values to the updated values
            document.getElementById("first_name").value = updatedFirstName;
            document.getElementById("last_name").value = updatedLastName;
            document.getElementById("DOB").value = updatedDOB;
            document.getElementById("student_email").value = updatedStudentEmail;
            document.getElementById("program").value = updatedProgram;
            document.getElementById("street_number").value = updatedStreetNumber;
            document.getElementById("street_name").value = updatedStreetName;
            document.getElementById("city").value = updatedCity;
            document.getElementById("province").value = updatedProvince;
            document.getElementById("postal_code").value = updatedPostalCode;
            document.getElementById("avatar").value = updatedAvatar;

         </script>
         <?php
            // Check if form is submitted
            if (isset($_POST["submit_profile"])) {
               include("connection.php");
               // Connect to the database
               $conn = new mysqli($server_name, $username, $password, $database_name);
               if ($conn->connect_error) {
                  die("Error: Couldn't connect to the database.");
               }

               // Retrieve form data
               $first_name = $_POST['first_name'];
               $last_name = $_POST['last_name'];
               $dob = $_POST['DOB'];
               $email = $_POST['student_email'];
               $program = $_POST['program'];
               $avatar = $_POST['avatar'];
               $street_number = $_POST['street_number'];
               $street_name = $_POST['street_name'];
               $city = $_POST['city'];
               $province = $_POST['province'];
               $postal_code = $_POST['postal_code'];


               // Set session variables
               $_SESSION['first_name'] = $_POST['first_name'];
               $_SESSION['last_name'] = $_POST['last_name'];
               $_SESSION['DOB'] = $_POST['DOB'];
               $_SESSION['student_email'] = $_POST['student_email'];
               $_SESSION['program'] = $_POST['program'];
               $_SESSION['street_number'] = $_POST['street_number'];
               $_SESSION['street_name'] = $_POST['street_name'];
               $_SESSION['city'] = $_POST['city'];
               $_SESSION['province'] = $_POST['province'];
               $_SESSION['postal_code'] = $_POST['postal_code'];
               $_SESSION['avatar'] = $_POST['avatar'];

               // Ensure student_id is set
               if(isset($_SESSION['student_id'])) {
                  $student_id = $_SESSION['student_id'];

                  // Update users_info table
                  $sql = "UPDATE users_info SET first_name = ?, last_name = ?, DOB = ?, student_email = ? WHERE student_id = ?";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("ssssi", $first_name, $last_name, $dob, $email, $student_id);
                  $stmt->execute();
                  $stmt->close();

                  // Update users_address table
                  $sql = "UPDATE users_address SET street_number = ?, street_name = ?, city = ?, province = ?, postal_code = ? WHERE student_id = ?";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("issssi", $street_number, $street_name, $city, $province, $postal_code, $student_id);
                  $stmt->execute();
                  $stmt->close();

                  // Update users_program table
                  $sql = "UPDATE users_program SET Program = ? WHERE student_id = ?";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("si", $program, $student_id);
                  $stmt->execute();
                  $stmt->close();

                  // Update users_avatar table
                  $sql = "UPDATE users_avatar SET avatar = ? WHERE student_id = ?";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("ii", $avatar, $student_id);
                  $stmt->execute();
                  $stmt->close();
   
                  
               } else {
                  echo "Error: Student ID not found.";
               }

               // Close the database connection
               $conn->close();
            }
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
         <!-- Assuming the avatar image source would change based on the selected avatar -->
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
</body>
</html>