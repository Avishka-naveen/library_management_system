<?php
include 'db_connection.php';

/* =========================
   ADD FINE
========================= */
if (isset($_POST['add_fine'])) {

    $fine_id = $_POST['fine_id'];
    $member_id = $_POST['member_id'];
    $book_id = $_POST['book_id'];
    $fine_amount = $_POST['fine_amount'];
    $fine_date_modified = date('Y-m-d H:i:s');

    if ($fine_amount < 2 || $fine_amount > 500) {
        $_SESSION['message'] = "Fine must be between 2 - 500 LKR";
        header("Location: main.php?page=Fine");
        exit();
    }

    $stmt = $conn->prepare("
        INSERT INTO fine 
        (fine_id, member_id, book_id, fine_amount, fine_date_modified)
        VALUES (?, ?, ?, ?, ?)
    ");

    $stmt->bind_param("sssds",
        $fine_id,
        $member_id,
        $book_id,
        $fine_amount,
        $fine_date_modified
    );

    $stmt->execute();
    

    $_SESSION['message'] = "Fine added successfully";
    header("Location: main.php?page=Fine");
    exit();
}

/* =========================
   UPDATE FINE
========================= */
if (isset($_POST['update_fine'])) {

    $fine_id = $_POST['fine_id'];
    $member_id = $_POST['member_id'];
    $fine_amount = $_POST['fine_amount'];
    $fine_date_modified = date('Y-m-d H:i:s');

    if ($fine_amount < 2 || $fine_amount > 500) {
        $_SESSION['message'] = "Fine must be between 2 - 500 LKR";
        header("Location: main.php?page=Fine");
        exit();
    }

    $stmt = $conn->prepare("
        UPDATE fine 
        SET member_id=?, fine_amount=?, fine_date_modified=?
        WHERE fine_id=?
    ");

    $stmt->bind_param("sdss",
        $member_id,
        $fine_amount,
        $fine_date_modified,
        $fine_id
    );

    $stmt->execute();

    $_SESSION['message'] = "Fine updated successfully";
    header("Location: main.php?page=Fine");
    exit();
}

/* =========================
   DELETE FINE
========================= */
if (isset($_POST['delete_fine'])) {

    $fine_id = $_POST['fine_id'];

    $stmt = $conn->prepare("DELETE FROM fine WHERE fine_id=?");
    $stmt->bind_param("s", $fine_id);
    $stmt->execute();

    $_SESSION['message'] = "Fine deleted successfully";
    header("Location: main.php?page=Fine");
    exit();
}

/* =========================
   FETCH DATA (JOIN)
========================= */
$query = "
SELECT 
    f.fine_id,
    f.member_id,
    m.first_name,
    m.last_name,
    f.book_id,
    b.book_name,
    f.fine_amount,
    f.fine_date_modified
FROM fine f
LEFT JOIN member m ON f.member_id = m.member_id
LEFT JOIN book b ON f.book_id = b.book_id
";

$result = $conn->query($query);
$fineCount = $result->num_rows;
?>

<link rel="stylesheet" href="./css/fine.css">

<div class="container">

<h1 style="text-align: center;color: #1b5ee4; font-weight: bolder;">⚖️ Fine Management </h1>
<p style="text-align: left; color: #000000; font-size: 1.2rem;">Total Fines: <span style="color:red; font-weight: bolder; font-size:1.5rem;"><?php echo $fineCount; ?></span></p>


<?php if (isset($_SESSION['message'])): ?>
    <p class="message">
        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </p>
<?php endif; ?>

<!-- ================= TABLE ================= -->
<table>

<tr>
    <th>Fine ID</th>
    <th>Member ID</th>
    <th>Member Name</th>
    <th>Book Name</th>
    <th>Amount</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>

    <td><?= $row['fine_id'] ?></td>
    <td><?= $row['member_id'] ?></td>
    <td><?= $row['first_name'] . " " . $row['last_name'] ?></td>
    <td><?= $row['book_name'] ?></td>
    <td>Rs. <?= $row['fine_amount'] ?></td>
    <td><?= $row['fine_date_modified'] ?></td>

    <td>

        <button onclick="editFine(
            '<?= $row['fine_id'] ?>',
            '<?= $row['member_id'] ?>',
            '<?= $row['fine_amount'] ?>'
        )">
            Edit
        </button>

        <form method="POST" style="display:inline;">
            <input type="hidden" name="fine_id" value="<?= $row['fine_id'] ?>">
            <button name="delete_fine">Delete</button>
        </form>

    </td>

</tr>
<?php endwhile; ?>

</table>

<hr>

<!-- ================= ADD FORM ================= -->
<h3 style="text-align: center;color: #1b5ee4; font-weight: bolder;"> Add Fine</h3>

<form method="POST">

<input type="text" name="fine_id" placeholder="F001" required>
<input type="text" name="member_id" placeholder="M001" required>
<input type="text" name="book_id" placeholder="B001" required>
<input type="number" name="fine_amount" placeholder="2 - 500 LKR" required>

<button name="add_fine">Add Fine</button>

</form>

<hr>

<!-- ================= UPDATE FORM ================= -->
<h3>Update Fine</h3>

<form method="POST">

<input type="text" name="fine_id" id="f_id" readonly>
<input type="text" name="member_id" id="f_member" required>
<input type="number" name="fine_amount" id="f_amount" required>

<button name="update_fine">Update Fine</button>

</form>

</div>

<script>
function editFine(id, member, amount){

    document.getElementById("f_id").value = id;
    document.getElementById("f_member").value = member;
    document.getElementById("f_amount").value = amount;
}
</script>