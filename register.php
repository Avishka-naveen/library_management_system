<?php

include 'db_connection.php';
if (isset($_POST['userId']) && isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {

    $userId = $_POST['userId'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];




    $sql = "INSERT INTO user(user_id,email,first_name,last_name,username ,password) VALUES ('$userId','$email','$firstName','$lastName','$username','$password')";
    if ($conn->query($sql) === TRUE) {
        $successMessage = "New User added successfully!";

        $userId = '';
        $firstName = '';
        $lastName = '';
        $username = '';
        $email = '';
        $password = '';
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        $errorMessage = "Error: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/register.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <title>Register - Create Account</title>
</head>

<body>
    <div class="container">
        <div class="sub-container">
            <div class="left-card">
                <h1>Register !</h1>
                <p>Create a new account</p>
                <form action="register.php" onsubmit=" return validateRegistrationForm()" method="post">
                    <div class="input-box">
                        <div>
                            <i class="fas fa-id-card"></i>
                            <input type="text" placeholder="User ID" name="userId" id="userId" required>
                        </div>
                        <div>
                            <i class="fas fa-user"></i>
                            <input type="text" placeholder="First Name" name="firstName" id="firstName" required>
                        </div>
                        <div>
                            <i class="fas fa-user"></i>
                            <input type="text" placeholder="Last Name" name="lastName" id="lastName" required>
                        </div>
                        <div>
                            <i class="fas fa-user-circle"></i>
                            <input type="text" placeholder="Username" name="username" id="username" required>
                        </div>
                        <div>
                            <i class="fas fa-envelope"></i>
                            <input type="email" placeholder="Email" name="email" id="email" required>
                        </div>
                        <div>
                            <i class="fas fa-lock"></i>
                            <input type="password" placeholder="Password" name="password" id="password" required>
                        </div>

                    </div>
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
        background-color: #dc3545;
        height: 50px;
        display:flex;
        align-items:center;
        justify-content:center;
        border-radius:8px;
        font-size:large;
        font-weight:bold;
    ">
                            <?php echo $errorMessage; ?>
                        </div>
                    <?php } ?>
                    <button type="submit">Register</button>
                </form>
                <p class="login-link">Already have an account? <a href="login.php">Login here</a></p>
            </div>
            <div class="right-card">
                <img class="register-img" src="./assets/register.svg" alt="Register Image">
            </div>
        </div>
    </div>
    <script src="./js/register.js"></script>
</body>

</html>