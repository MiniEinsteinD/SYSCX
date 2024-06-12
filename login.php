<?php
session_start();

if(isset($_SESSION['student_id'])) {
    header("Location: index.php");
    exit;
}

include("connection.php");

if(isset($_POST['login'])) {
    $conn = new mysqli($server_name, $username, $password, $database_name);
    if ($conn->connect_error) {
        die("Error: Couldn't connect to the database.");
    }

    $email = $_POST['student_email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users_passwords WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];
        if (password_verify($password, $hashed_password)) {
            // Passwords match, set session variables
            $_SESSION['student_id'] = $email;

            // Redirect to index.php
            header("Location: index.php");
            exit;
        } else {
            $error_message = "Incorrect email or password!";
        }
    } else {
        $error_message = "Incorrect email or password!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="utf-8">
   <title>Login to SYSCX</title>
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
         <li><a href="http://127.0.0.1/SYSC4504_Labs/SYSCX/index.php">Home</a></li>
         <li><a href="http://127.0.0.1/SYSC4504_Labs/SYSCX/register.php">Register</a></li>
         <li><a href="http://127.0.0.1/SYSC4504_Labs/SYSCX/login.php">Log in</a></li>
      </ul>
   </nav>

   <main>
      <section>
         <h2>LOGIN</h2>
         <form method="POST" action="">
            <fieldset>
               <legend><h3><span>Enter your email and password</span></h3></legend>
               <table>
                  <tbody>
                     <tr>
                        <td>
                           <label>Email address: </label>
                           <input type="text" name="student_email"/>
                        </td>
                     </tr>
                     <tr>
                        <td>
                           <label>Password: </label>
                           <input type="password" name="password"/>
                        </td>
                     </tr>
                     <?php if(isset($error_message)) { ?>
                        <tr>
                           <td><span style="color: red;"><?php echo $error_message; ?></span></td>
                        </tr>
                     <?php } ?>
                     <tr>
                        <td>
                           <input type="submit" name="login" value="Log in">
                           <a href="register.php">Don't have an account? Register here</a>
                        </td>
                     </tr>
                  </tbody>
               </table>
            </fieldset>
         </form>
      </section>
   </main>
</body>
</html>
