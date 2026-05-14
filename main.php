<?php
session_start();
include 'db_connection.php';

// SESSION CHECK (optional but recommended)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$firstName = $_SESSION['first_name'];
$lastName = $_SESSION['last_name'];

$page = $_GET['page'] ?? 'Books';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Library Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/main.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body>
    

<!-- SIDEBAR -->
<div class="sidebar" id="sidebar">

    <div class="sidebar-brand">
        <div class="logo">
            <?php echo strtoupper($firstName[0] . $lastName[0]); ?>
        </div>
        <h5>Hi <?= $firstName . " " . $lastName ?></h5>
    </div>

    <a href="main.php?page=Books" class="sidebar-nav-item">
        <i class="fas fa-book"></i> Books
    </a>

    <a href="main.php?page=BookCategory" class="sidebar-nav-item">
        <i class="fas fa-list"></i> Book Category
    </a>

    <a href="main.php?page=Members" class="sidebar-nav-item">
        <i class="fas fa-users"></i> Members
    </a>

    <a href="main.php?page=BookBorrow" class="sidebar-nav-item">
        <i class="fas fa-hand-holding"></i> Book Borrow
    </a>

    <a href="main.php?page=Fine" class="sidebar-nav-item">
        <i class="fas fa-money-bill"></i> Fine
    </a>

    <hr>

    <a href="logout.php" class="sidebar-nav-item text-danger" onclick="return confirmLogout();">
        <i class="fas fa-sign-out-alt"></i> Logout
        <script>
            function confirmLogout() {
                return confirm("Are you sure you want to logout?");
            }
        </script>
    </a>

</div>

<!-- MAIN CONTENT -->
<div class="main-content">

<?php
switch ($page) {

    case "Books":
        include "Books.php";
        break;

    case "BookCategory":
        include "BookCategory.php";
        break;

    case "Members":
        include "Members.php";
        break;

    case "BookBorrow":
        include "BookBorrow.php";
        break;

    case "Fine":
        include "Fine.php";
        break;

    default:
        echo "<h2>Page Not Found</h2>";
        break;
}
?>

</div>

</body>
</html>