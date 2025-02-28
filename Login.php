
<?php
session_start();
include './connection.php';


// Handle login when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    // Trim and sanitize user input
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Fetch user details using prepared statement
    $stmt = $conn->prepare("SELECT idno, username, password FROM student WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        // Verify password
        if ($password = $row["password"]) {
            // Store user session
            $_SESSION['username'] = $row['username'];
            $_SESSION['idno'] = $row['idno'];
            $_SESSION['profile_picture'] = $row['profile_picture'];
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
    
        * {
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
                <img src="../sitin/CCS LOGO.png" width="30%" height="auto"/>
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
    </div>
</div>

</body>
</html>


