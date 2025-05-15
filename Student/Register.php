<?php 

//Inlude diri ang server.php para maka gamit tas function 
include './server.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.phptutorial.net/app/css/style.css">
    <title>Login</title>

    <style>
        body {
            position: relative;
            margin: 0;
            height: 100vh;
            font-family: 'Poppins', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .wrapper {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
            width: 450px;
            position: relative;
            z-index: 1;
        }

        h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        img {
            width: 120px;
            height: 120px;
            margin-bottom: 10px;
        }

        .input-box {
            margin-bottom: 15px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        /* Flexbox for arranging fields in one row */
        .row {
            display: flex;
            gap: 10px;
            justify-content: space-between;
        }

        .row .input-box {
            flex: 1;
        }

        .terms {
            margin: 10px 0;
            font-size: 14px;
            text-align: left;
        }

        .btn {
            width: 100%;
            padding: 10px;
            background-color: #553392;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #CBA1DC;
        }

        .bottom-links {
            margin-top: 10px;
            font-size: 14px;
        }

        .bottom-links a {
            color: red;
            text-decoration: none;
        }

        .bottom-links a:hover {
            text-decoration: underline;
        }

        @media (max-width: 480px) {
            .row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <form method="POST">
            <img src="image/ccs.png" alt="CCS LOGO">
            <h1>Sign Up</h1>

            <div class="input-box">
                <input type="text" name="idno" id="idno" placeholder="Enter your ID number" required>
            </div>

            <!-- Name Fields in One Row -->
            <div class="row">
                <div class="input-box">
                    <input type="text" name="firstname" id="firstname" placeholder="First Name" required>
                </div>
                <div class="input-box">
                    <input type="text" name="lastname" id="lastname" placeholder="Last Name" required>
                </div>
                <div class="input-box">
                    <input type="text" name="middlename" id="middlename" placeholder="Middle Name">
                </div>
            </div>

            <!-- Course and Year Level in One Row -->
            <div class="row">
                <div class="input-box">
                    <select name="course" id="course" required>
                        <option value="" disabled selected>Select Course</option>
                        <option value="BSIT">BSIT</option>
                        <option value="BSCpE">BSCpE</option>
                        <option value="BSBA">BSBA</option>
                        <option value="BSCS">BSCS</option>
                        <option value="BSEd">BSEd</option>
                        <option value="BSHM">BSHM</option>
                        <option value="BSN">BSB</option>
                        <option value="BEEd">BEEd</option>
                        <option value="BSCE">BSCE</option>
                        <option value="BSME">BSME</option>
                        <option value="BSCRIM">BSCRIM</option>
                        <option value="BSCA">BSCA</option>
                    </select>
                </div>

                <div class="input-box">
                    <select name="yearlevel" id="yearlevel" required>
                        <option value="" disabled selected>Select Year Level</option>
                        <option value="1">1st year</option>
                        <option value="2">2nd year</option>
                        <option value="3">3rd year</option>
                        <option value="4">4th year</option>
                    </select>
                </div>
            </div>

            <div class="input-box">
                <input type="email" name="email" id="email" placeholder="Enter your Email" required>
            </div>

            <div class="input-box">
                <input type="text" name="username" id="username" placeholder="Enter your Username" required>
            </div>

            <div class="input-box">
                <input type="password" name="password" id="password" placeholder="Enter your Password" required>
            </div>

            <button  class="btn" name="SubmitForm" type="submit">Sign Up</button>

            <div class="bottom-links">
                Already have an account? <a href="Login.php">Sign in →</a>
            </div>
        </form>
    </div>   
</body>
</html>