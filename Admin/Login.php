
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
    <title>CCS Sitin Management System</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
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
        }
        .right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: white;
            padding: 40px;
        }
        .login-box {
            width: 100%;
            max-width: 350px;
            text-align: center;
        }
        .login-box h1 {
            margin-bottom: 20px;
            color: #6A0DAD;
            font-weight: 600;
        }
        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .input-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn-container {
            margin-top: 10px;
        }
        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
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
        }
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                height: auto;
            }
            .left {
                padding: 40px;
            }
        }
    </style>
</head>
<body>
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
    </div>
</div>

</body>
</html>
