<?php
include 'db_connection.php';

/* =========================
   FETCH USERS
========================= */
$result = $conn->query("SELECT * FROM user");

/* =========================
   UPDATE USER (NO PASSWORD / ID CHANGE)
========================= */
if (isset($_POST['update_user'])) {

    $user_id = $_POST['user_id'];
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $username = $_POST['username'];

    $stmt = $conn->prepare("
        UPDATE user 
        SET email=?, first_name=?, last_name=?, username=? 
        WHERE user_id=?
    ");

    $stmt->bind_param(
        "sssss",
        $email,
        $first_name,
        $last_name,
        $username,
        $user_id
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = "User updated successfully";
    } else {
        $_SESSION['message'] = "Update failed";
    }

    header("Location: admin.php");
    exit();
}

/* =========================
   DELETE USER
========================= */
if (isset($_POST['delete_user'])) {

    $user_id = $_POST['user_id'];

    $stmt = $conn->prepare("DELETE FROM user WHERE user_id=?");
    $stmt->bind_param("s", $user_id);
    $stmt->execute();

    $_SESSION['message'] = "User deleted successfully";

    header("Location: admin.php");
    exit();
}
?>

<link rel="stylesheet" href="./css/admin.css">

<div class="container">

    <h1><span>A</span>dmin Panel</h1>

    <?php if (isset($_SESSION['message'])): ?>
        <p class="message">
            <?= $_SESSION['message'];
            unset($_SESSION['message']); ?>
        </p>
    <?php endif; ?>

    <!-- ================= TABLE ================= -->
    <table>

        <tr>
            <th>User ID</th>
            <th>Email</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Username</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>

                <td><?= $row['user_id'] ?></td>
                <td><?= $row['email'] ?></td>
                <td><?= $row['first_name'] ?></td>
                <td><?= $row['last_name'] ?></td>
                <td><?= $row['username'] ?></td>

                <td>

                    <button onclick="editUser(
            '<?= $row['user_id'] ?>',
            '<?= $row['email'] ?>',
            '<?= $row['first_name'] ?>',
            '<?= $row['last_name'] ?>',
            '<?= $row['username'] ?>'
        )">
                        Edit
                    </button>

                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="user_id" value="<?= $row['user_id'] ?>">
                        <button name="delete_user">Delete</button>
                    </form>

                </td>

            </tr>
        <?php endwhile; ?>

    </table>

    <hr>

    <!-- ================= UPDATE FORM ================= -->
    <h3>Update User</h3>

    <form method="POST">

        <input type="text" name="user_id" id="u_id" readonly>

        <input type="email" name="email" id="u_email" required>
        <input type="text" name="first_name" id="u_first" required>
        <input type="text" name="last_name" id="u_last" required>
        <input type="text" name="username" id="u_username" required>

        <button name="update_user">Update User</button>

    </form>

</div>

<script>
    function editUser(id, email, first, last, username) {

        document.getElementById("u_id").value = id;
        document.getElementById("u_email").value = email;
        document.getElementById("u_first").value = first;
        document.getElementById("u_last").value = last;
        document.getElementById("u_username").value = username;
    }
</script>