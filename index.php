<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCS Sit-in Monitoring System</title>
    <link rel="stylesheet" href="https://www.phptutorial.net/app/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        .container {
            display: flex;
            height: 100vh;
            box-shadow: 10px 10px;
        }
        .left {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color:  #A854E2;
            background-image: url("OP.jpg");
        }
        .right {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: whitesmoke;
            position: relative;
        }
        .login-box {
            width: 300px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 10px 10px;
        }
        .login-box h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        .input-group {
            margin-bottom: 15px;
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
            background:rgb(136, 214, 253);
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn:hover {
            background:rgb(0, 179, 90);
        }

        /* Login Icon and Text */
        .login-container {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 50px;
            background-color: rgb(23, 219, 206);
            transition: all 0.3s ease-in-out;
        }
        .login-container a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #0056b3;;
            font-size: 18px;
            font-weight: bold;
        }
        .login-container:hover {
            background:rgb(24, 197, 185);
            transform: scale(1.1);
        }
        .login-icon {
            width: 30px;
            height: 30px;
            margin-left: 10px;
            transition: all 0.3s ease-in-out;
        }
        .login-container:hover .login-icon {
            transform: rotate(360deg);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="left">
        <!-- Background Image -->
    </div>
    <div class="right">
        <!-- Login Icon beside the Login text at top-right corner -->
        <div class="login-container">
            <a href="./Student/login.php">
                Login <img src="click.png" alt="Login" class="login-icon">
            </a>
        </div>

        <form method="POST" style="background-color:whitesmoke;">
            <center>
             <img src="CCS LOGO.png" alt="CCS Logo" width="50%" height="auto"/>
            </center>
            <h1><b>Welcome To Sit-in Monitoring System</b></h1>
        </form>
    </div>
</div>

</body>
</html>
