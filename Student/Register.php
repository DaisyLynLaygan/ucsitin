<?php
include './connection.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and collect the form inputs
    $idno = $_POST['idno'];
    $lastname = $_POST['lastname'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $course = $_POST['course'];
    $year = $_POST['year'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hash the password before storing it
    $hashed_password = $password;

    // Prepare an SQL query to insert the data
    $stmt = $conn->prepare("INSERT INTO student (idno, lastname, firstname, middlename, course, year, email, username, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssisss", $idno, $lastname, $firstname, $middlename, $course, $year, $email, $username, $hashed_password);

    // Execute the query and check if it was successful
    if ($stmt->execute()) {
        header("Location: login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://www.phptutorial.net/app/css/style.css">
<title>Register</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: Arial, sans-serif;
    }
    body {
        overflow: hidden;
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
        background-image: url("OP.jpg");
        background-size: cover;
        background-position: center;
    }
    .right {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: whitesmoke;
        padding: 20px;
    }
    .form-container {
        width: 100%;
        max-width: 450px;
        display: flex;
        flex-direction: column;
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.1);
    }
    .scroll-container {
        max-height: 50vh;
        overflow-y: auto;
        padding-right: 5px;
    }
    .scroll-container::-webkit-scrollbar {
        width: 8px;
    }
    .scroll-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 5px;
    }
    .scroll-container::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 5px;
    }
    .form-container img {
        width: 30%;
        height: auto;
        margin-bottom: 15px;
    }
    .form-container h1 {
        margin-bottom: 15px;
        color: #6A0DAD; /* Purple */
    }
    .input-group {
        margin-bottom: 10px;
        text-align: left;
    }
    .input-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }
    .input-group input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .btn-container {
        display: flex;
        justify-content: space-between;
        padding-top: 10px;
        position: sticky;
        bottom: 0;
        background: white;
        padding-bottom: 10px;
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
    .btn-register {
        color: #6A0DAD;
    }
    @media (max-width: 687px) {
        .container {
            flex-direction: column;
            height: auto;
        }
        .left {
            display: none; /* Hide image on small screens */
        }
        .form-container {
            width: 100%;
        }
    }
</style>
</head>
<body>
<div class="container">
    <div class="left"></div>
    <div class="right">
        <div class="form-container">
            <form method="POST">
                <center>
                    <img src="../sitin/CCS LOGO.png" width="30%" height="auto"/>
                </center>
               <center><h2><b>Sign Up!</b></h2></center>
                <div class="scroll-container">
                    <div class="input-group">
                        <label for="idno">ID No:</label>
                        <input type="number" name="idno" id="idno">
                    </div>
                    <div class="input-group">
                        <label for="lastname">Last Name:</label>
                        <input type="text" name="lastname" id="lastname">
                    </div>
                    <div class="input-group">
                        <label for="firstname">First Name:</label>
                        <input type="text" name="firstname" id="firstname">
                    </div>
                    <div class="input-group">
                        <label for="middlename">Middle Name:</label>
                        <input type="text" name="middlename" id="middlename">
                    </div>
                    <div class="input-group">
                        <label for="course">Course:</label>
                        <input type="text" name="course" id="course">
                    </div>
                    <div class="input-group">
                        <label for="year">Year Level:</label>
                        <input type="number" name="year" id="year">
                    </div>
                    <div class="input-group">
                        <label for="email">Email Address:</label>
                        <input type="email" name="email" id="email">
                    </div>
                    <div class="input-group">
                        <label for="username">Username:</label>
                        <input type="text" name="username" id="username">
                    </div>
                    <div class="input-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password">
                    </div>
                </div>
                <div class="btn-container">
                    <button type="submit" class="btn btn-signin">Sign In</button>
                    <a href="login.php" class="btn btn-register"><center>Login</center></a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
