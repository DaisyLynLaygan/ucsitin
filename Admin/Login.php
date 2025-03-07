
<?php
session_start();
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
            echo "<script>alert('Invalid password'); window.location='index.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid username'); window.location='index.php';</script>";
    }
    
    $stmt->close();
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sitin Monitoring System</title>
    <link rel="stylesheet" href="https://www.phptutorial.net/app/css/style.css">
    <style>
       {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
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
        }
        .right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: whitesmoke;
            padding: 20px;
        }
        .login-box {
            width: 100%;
            max-width: 350px;
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
        }
        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn-container {
            display: flex;
            justify-content: space-between;
            padding-top: 10px;
        }
        .btn {
            flex: 1;
            padding: 10px;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
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
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                height: auto;
            }
            .left {
                display: none;
            }
            .login-box {
                width: 100%;
            }
        }
    </style>
</head>
<body>

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
    </div>
</div>

</body>
</html>


