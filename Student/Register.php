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
<title>Registration</title>
<style>
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
        background-color: #f7f7f7;
    }
    .container {
        width: 50%;
        max-width: 500px;
        background: whitesmoke;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        margin: auto;
    }
    h2 {
        text-align: center;
        color: #6A0DAD;
        margin-bottom: 20px;
    }
    .form-group {
        margin-bottom: 10px;
    }
    label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
    }
    input, select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .register-btn {
        width: 100%;
        padding: 10px;
        background-color: #6A0DAD;
        color: white;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
    }
    .login-link {
        text-align: center;
        margin-top: 10px;
    }
    .login-link a {
        color: #6A0DAD;
        font-weight: bold;
        text-decoration: none;
    }
</style>
</head>
<body>
<div class="container">
    <h2>Registration</h2>
    <form method="POST">
        <div class="form-group">
            <label for="idno">Student ID Number:</label>
            <input type="text" id="idno" name="idno">
        </div>
        <div class="form-group">
            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname">
        </div>
        <div class="form-group">
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname">
        </div>
        <div class="form-group">
            <label for="middlename">Middle Name:</label>
            <input type="text" id="middlename" name="middlename">
        </div>
        <div class="form-group">
            <label for="course">Course:</label>
            <select id="course" name="course">
                <option>Select Course</option>
            </select>
        </div>
        <div class="form-group">
            <label for="year">Year Level:</label>
            <select id="year" name="year">
                <option>Select Year Level</option>
            </select>
        </div>
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username">
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email">
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="repeat-password">Repeat Password:</label>
            <input type="password" id="repeat-password" name="repeat-password">
        </div>
        <button type="submit" class="register-btn">Register</button>
    </form>
    <div class="login-link">
        If you already have an account, <a href="login.php">Login</a>.
    </div>
</div>
</body>
</html>
