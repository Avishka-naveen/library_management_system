<?php
include 'db_connection.php';
session_start();

if (isset($_POST['username']) && isset($_POST['password'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();

        if ($password == $row['password']) {


            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['last_name'] = $row['last_name'];
            $_SESSION['first_name'] = $row['first_name'];
            $_SESSION['email'] = $row['email'];

            header("Location: main.php");
            $successMessage = "Login successful!";
            exit();
        } else {
             $errorMessage = 'Incorrect password';
        }
    } else {
        $errorMessage = 'User not found';
    }
}

?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <title>Document</title>
</head>

<body>
    <div class="container">
        <div class="sub-container">
            <div class="left-card">
                <h1>LOGIN !</h1>
                <p>login to your account</p>
                <form action="login.php" method="POST" onsubmit="return validateForm()">
                    <div class="input-box">
                        <div>
                            <i class="fas fa-user"></i>
                            <input type="text" placeholder="Username" required name="username">
                        </div>
                        <div>
                            <i class="fas fa-lock"></i>
                            <input type="password" placeholder="Password" required name="password" id="password">
                        </div>
                    </div>
                    <button type="submit">Login</button>
                    <?php if (isset($successMessage)) { ?>
                        <div id="success-message" style="
        color:white;
        margin-top: 10px;
        background-color: #40ab0bfc;
        height: 50px;
        display:flex;
        align-items:center;
        justify-content:center;
        border-radius:8px;
        font-size:large;
        font-weight:bold;
    ">
                            <?php echo $successMessage; ?>
                        </div>
                    <?php } ?>
                    <?php if (isset($errorMessage)) { ?>
                        <div id="error-message" style="
        color:white;
        margin-top: 10px;
        background-color: #ff0000;
        height: 50px;
        display:flex;
        align-items:center;
        justify-content:center;
        border-radius:8px;
        font-size:large;
        font-weight:bold;
    ">
                            <?php echo  $errorMessage; ?>
                        </div>
                    <?php } ?>
                </form>
                <div class="bottom-section">
                    <p class="register-link">Don't have an account? <a href="register.php">Register here</a></p>
                </div>
            </div>
            <div class="right-card">
                <img class="login-img" src="./assets/login.svg" alt="Login Image">
            </div>
        </div>
    </div>

    <script src="./js//login.js"></script>
</body>

</html>