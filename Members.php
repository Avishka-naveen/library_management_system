<?php
include 'db_connection.php';

/* =========================
   ADD MEMBER
========================= */
if (isset($_POST['add_member'])) {

    $member_id = $_POST['member_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birthday = $_POST['birthday'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("
        INSERT INTO member 
        (member_id, first_name, last_name, birthday, email)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("sssss",
        $member_id,
        $first_name,
        $last_name,
        $birthday,
        $email
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = "Member added successfully";
    } else {
        $_SESSION['message'] = "Error adding member";
    }

    header("Location: main.php?page=Members");
    exit();
}

/* =========================
   UPDATE MEMBER
========================= */
if (isset($_POST['update_member'])) {

    $member_id = $_POST['member_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $birthday = $_POST['birthday'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("
        UPDATE member 
        SET first_name=?, last_name=?, birthday=?, email=?
        WHERE member_id=?
    ");

    $stmt->bind_param("sssss",
        $first_name,
        $last_name,
        $birthday,
        $email,
        $member_id
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = "Member updated successfully";
    } else {
        $_SESSION['message'] = "Error updating member";
    }

    header("Location: main.php?page=Members");
    exit();
}

/* =========================
   DELETE MEMBER
========================= */
if (isset($_POST['delete_member'])) {

    $member_id = $_POST['member_id'];

    $stmt = $conn->prepare("
        DELETE FROM member WHERE member_id=?
    ");

    $stmt->bind_param("s", $member_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Member deleted successfully";
    } else {
        $_SESSION['message'] = "Error deleting member";
    }

    header("Location: main.php?page=Members");
    exit();
}

/* =========================
   FETCH MEMBERS
========================= */
$result = $conn->query("SELECT * FROM member");

$memberCount=$result->num_rows;
?>



<link rel="stylesheet" href="./css/members.css">

<div class="container">

<h1 style="text-align: center;color: #1b5ee4; font-weight: bolder;">👥 Members Management</h1>

<p style="text-align: left; color: #000000; font-size: 1.2rem;">Total Members: <span style="color:red; font-weight: bolder; font-size:1.5rem;"><?php echo $memberCount; ?></span></p>

<!-- MESSAGE -->
<?php if (isset($_SESSION['message'])): ?>
    <p class="message">
        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </p>
<?php endif; ?>

<!-- ================= TABLE ================= -->
<table>

<tr>
    <th>ID</th>
    <th>First Name</th>
    <th>Last Name</th>
    <th>Birthday</th>
    <th>Email</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>

    <td><?= $row['member_id'] ?></td>
    <td><?= $row['first_name'] ?></td>
    <td><?= $row['last_name'] ?></td>
    <td><?= $row['birthday'] ?></td>
    <td><?= $row['email'] ?></td>

    <td>

        <button onclick="editMember(
            '<?= $row['member_id'] ?>',
            '<?= $row['first_name'] ?>',
            '<?= $row['last_name'] ?>',
            '<?= $row['birthday'] ?>',
            '<?= $row['email'] ?>'
        )">
            Edit
        </button>

        <form method="POST" style="display:inline;">
            <input type="hidden" name="member_id" value="<?= $row['member_id'] ?>">
            <button name="delete_member">Delete</button>
        </form>

    </td>

</tr>
<?php endwhile; ?>

</table>

<hr>

<!-- ================= ADD FORM ================= -->
<h3>Add Member</h3>

<form method="POST">

<input type="text" name="member_id" placeholder="M001" required>
<input type="text" name="first_name" placeholder="First Name" required>
<input type="text" name="last_name" placeholder="Last Name" required>
<input type="date" name="birthday" required>
<input type="email" name="email" placeholder="Email" required>

<button name="add_member">Add Member</button>

</form>

<hr>

<!-- ================= UPDATE FORM ================= -->
<h3>Update Member</h3>

<form method="POST">

<input type="text" name="member_id" id="m_id" readonly>
<input type="text" name="first_name" id="m_first" required>
<input type="text" name="last_name" id="m_last" required>
<input type="date" name="birthday" id="m_bday" required>
<input type="email" name="email" id="m_email" required>

<button name="update_member">Update Member</button>

</form>

</div>

<!-- ================= JS ================= -->
<script>
function editMember(id, first, last, birthday, email){

    document.getElementById("m_id").value = id;
    document.getElementById("m_first").value = first;
    document.getElementById("m_last").value = last;
    document.getElementById("m_bday").value = birthday;
    document.getElementById("m_email").value = email;
}
</script>