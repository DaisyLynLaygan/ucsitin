
<?php
session_start();
<<<<<<< HEAD
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}
=======
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
include 'connection.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch admin from database
    $sql = "SELECT * FROM admins WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();

        // Check password (Change this to password_verify() if using hashed passwords)
        if ($password === $admin['password']) { // If using password_verify(), modify accordingly
            $_SESSION['admin_username'] = $admin['username']; 
            $_SESSION['admin_logged_in'] = true; // ✅ Add this line
            header("Location: dashboard.php");
            exit();
        } else {
<<<<<<< HEAD
            echo "<script>alert('Invalid password'); window.location='login.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid username'); window.location='login.php';</script>";
=======
            echo "<script>alert('Invalid password'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid username'); window.location='index.php';</script>";
>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
    }
    
    $stmt->close();
    $conn->close();
}
?>

<<<<<<< HEAD
=======

>>>>>>> 611347cb0330f243f87d23d32877186abb3261f1
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>CCS Sitin Management System</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
=======
    <title>CCS Sitin Monitoring System</title>
    <link rel="stylesheet" href="https://www.phptutorial.net/app/css/style.css">
    <style>
       {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
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
            <h1>Welcome To Sit-in Admin!</h1>
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
