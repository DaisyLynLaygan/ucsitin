<<<<<<< HEAD
<?php
session_start();
if (isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}
include './connection.php';

=======

<?php
session_start();
include './connection.php';


>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
// Handle login when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    // Trim and sanitize user input
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Fetch user details using prepared statement
<<<<<<< HEAD
    $stmt = $conn->prepare("SELECT idno, username, password, profile_picture FROM student WHERE username = ?");
=======
    $stmt = $conn->prepare("SELECT idno, username, password FROM student WHERE username = ?");
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
<<<<<<< HEAD
        // Verify password (using password_verify)
        if (password_verify($password, $row["password"])) {
=======
        // Verify password
        if ($password = $row["password"]) {
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
            // Store user session
            $_SESSION['username'] = $row['username'];
            $_SESSION['idno'] = $row['idno'];
            $_SESSION['profile_picture'] = $row['profile_picture'];
<<<<<<< HEAD
            $_SESSION['student_logged_in'] = true;
=======
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid password! Please try again.'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('User not found! Please check your username.'); window.location.href='login.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>CCS Sitin Management System</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <style>
=======
    <title>CCS Sitin Monitoring System</title>
    <link rel="stylesheet" href="https://www.phptutorial.net/app/css/style.css">
    <style>
       /* * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        .container {
            display: flex;
            height: 100vh;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2); /* Added box shadow */
   /*     }
        .left {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #DAD2FF;
            background-image: url("OP.jpg");
        }
        .right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: whitesmoke;
     /*   }
        .login-box {
            width: 320px;
            padding: 25px;
            background: white;
            border-radius: 8px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2); /* Added box shadow */
            text-align: center;
       /* }
        .login-box img {
            width: 30%;
            height: auto;
            margin-bottom: 15px; /* Added space below the logo */
            padding-top: 10px; /* Moved the logo slightly above */
     /*   }
        .login-box h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .input-group {
            margin-bottom: 15px;
            text-align:left;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
        }
        .input-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            width: 100%;
            padding: 10px;
            background: #007BFF;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            margin-top:10px;
        }
        .btn:hover {
            background: #0056b3;
        } */
    
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
<<<<<<< HEAD
            font-family: 'Poppins', sans-serif;
=======
            font-family: Arial, sans-serif;
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        }
        body {
            display: flex;
            height: 100vh;
            justify-content: center;
            align-items: center;
            background-color: whitesmoke;
        }
        .container {
            display: flex;
<<<<<<< HEAD
            width: 100%;
            max-width: 1500px;
            height: 100vh;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2);
        }
        .left {
            flex: 1;
            background-color: #A854E2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .left img {
            width: 50%;
            height: auto;
            margin-bottom: 15px;
=======
            width: 90%;
            max-width: 900px;
            height: 90vh;
            background: white;
            border-radius: 10px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .left {
            flex: 1;
            background: url("OP.jpg") no-repeat center center;
            background-size: cover;
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        }
        .right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
<<<<<<< HEAD
            background-color: white;
            padding: 40px;
=======
            background-color: whitesmoke;
            padding: 20px;
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        }
        .login-box {
            width: 100%;
            max-width: 350px;
<<<<<<< HEAD
            text-align: center;
        }
        .login-box h1 {
            margin-bottom: 20px;
            color: #6A0DAD;
            font-weight: 600;
=======
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .login-box img {
            width: 30%;
            height: auto;
            margin-bottom: 15px;
        }
        .login-box h1 {
            margin-bottom: 15px;
            color: #6A0DAD;
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        }
        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
<<<<<<< HEAD
            font-weight: 600;
=======
            font-weight: bold;
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
<<<<<<< HEAD
            font-size: 16px;
        }
        .btn-container {
            margin-top: 10px;
        }
        .btn {
            width: 100%;
            padding: 12px;
=======
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            padding-top: 10px;
        }
        .btn {
            flex: 1;
            padding: 10px;
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
<<<<<<< HEAD
            background: #6A0DAD;
            font-weight: 600;
        }
        .btn:hover {
            background: #4B0082;
        }
        .register-link {
            margin-top: 15px;
            display: block;
            text-decoration: none;
            color: #6A0DAD;
            font-weight: 600;
        }
        .register-link:hover {
            text-decoration: underline;
=======
            margin: 0 5px;
        }
        .btn-login {
            background: #6A0DAD;
        }
        .btn-login:hover {
            background: #4B0082;
        }
        .btn-register {
            background: #D8BFD8;
            color: black;
            text-align: center;
            display: inline-block;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            flex: 1;
        }
        .btn-register:hover {
            background: #C3A6C3;
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                height: auto;
            }
            .left {
<<<<<<< HEAD
                padding: 40px;
=======
                display: none;
            }
            .login-box {
                width: 100%;
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
            }
        }
    </style>
</head>
<body>
<<<<<<< HEAD
<div class="container">
    <div class="left">
        <img src="../CCS LOGO.png" alt="CCS Logo">
        <h2>CCS Sit-In Monitoring System</h2>
    </div>
    <div class="right">
        <div class="login-box">
            <h1>Welcome To Sit-in!</h1>
            <form method="POST">
                <div class="input-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <button type="submit" class="btn" name="login">Login</button>
                <a href="register.php" class="register-link">Create Account</a>
            </form>
        </div>
=======

<div class="container">
    <div class="left"></div>
    <div class="right">
        <form method="POST" style="background-color: whitesmoke;">
            <center>
                <img src="../CCS LOGO.png" width="30%" height="auto"/>
            </center>
            <h1><b>CCS Sitin Monitoring System</b></h1>
            <div>
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <section>
                <button type="submit" name="login">Login</button> <!-- Ensure 'name="login"' is included -->
                <a href="register.php">Sign Up</a>
            </section>
        </form>
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
    </div>
</div>

</body>
</html>
<<<<<<< HEAD
=======


>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
