<?php
session_start(); // Add session start at the beginning
include 'db_connection.php';

/* =========================
   HELPER FUNCTIONS FOR VALIDATION
========================= */
function validateBookExists($conn, $book_id) {
    $stmt = $conn->prepare("SELECT * FROM book WHERE book_id = ?");
    $stmt->bind_param("s", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

function validateMemberExists($conn, $member_id) {
    $stmt = $conn->prepare("SELECT * FROM member WHERE member_id = ?");
    $stmt->bind_param("s", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}

/* =========================
   ADD FINE
========================= */
if (isset($_POST['add_fine'])) {

    $fine_id = $_POST['fine_id'];
    $member_id = $_POST['member_id'];
    $book_id = $_POST['book_id'];
    $fine_amount = $_POST['fine_amount'];
    $fine_date_modified = date('Y-m-d H:i:s');

    // Validate Fine ID format
    if (!preg_match('/^F\d{3}$/', $fine_id)) {
        $_SESSION['message'] = "Invalid Fine ID format! Must be F001, F002, etc.";
        header("Location: main.php?page=Fine");
        exit();
    }
    // Validate Member ID format
    elseif (!preg_match('/^M\d{3}$/', $member_id)) {
        $_SESSION['message'] = "Invalid Member ID format! Must be M001, M002, etc.";
        header("Location: main.php?page=Fine");
        exit();
    }
    // Validate Book ID format
    elseif (!preg_match('/^B\d{3}$/', $book_id)) {
        $_SESSION['message'] = "Invalid Book ID format! Must be B001, B002, etc.";
        header("Location: main.php?page=Fine");
        exit();
    }
    // Check if Member exists
    elseif (!validateMemberExists($conn, $member_id)) {
        $_SESSION['message'] = "Error: Member ID '$member_id' does not exist! Please register the member first.";
        header("Location: main.php?page=Fine");
        exit();
    }
    // Check if Book exists
    elseif (!validateBookExists($conn, $book_id)) {
        $_SESSION['message'] = "Error: Book ID '$book_id' does not exist! Please add the book first.";
        header("Location: main.php?page=Fine");
        exit();
    }
    // Validate fine amount range
    elseif ($fine_amount < 2 || $fine_amount > 500) {
        $_SESSION['message'] = "Fine must be between 2 - 500 LKR";
        header("Location: main.php?page=Fine");
        exit();
    }
    // Check if Fine ID already exists
    else {
        $check_stmt = $conn->prepare("SELECT * FROM fine WHERE fine_id = ?");
        $check_stmt->bind_param("s", $fine_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $_SESSION['message'] = "Error: Fine ID '$fine_id' already exists!";
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

        if ($stmt->execute()) {
            $_SESSION['message'] = "Fine added successfully";
        } else {
            $_SESSION['message'] = "Error adding fine: " . $conn->error;
        }
        
        header("Location: main.php?page=Fine");
        exit();
    }
}

/* =========================
   UPDATE FINE
========================= */
if (isset($_POST['update_fine'])) {

    $fine_id = $_POST['fine_id'];
    $member_id = $_POST['member_id'];
    $fine_amount = $_POST['fine_amount'];
    $fine_date_modified = date('Y-m-d H:i:s');

    // Validate Member ID format
    if (!preg_match('/^M\d{3}$/', $member_id)) {
        $_SESSION['message'] = "Invalid Member ID format! Must be M001, M002, etc.";
        header("Location: main.php?page=Fine");
        exit();
    }
    // Check if Member exists
    elseif (!validateMemberExists($conn, $member_id)) {
        $_SESSION['message'] = "Error: Member ID '$member_id' does not exist!";
        header("Location: main.php?page=Fine");
        exit();
    }
    // Validate fine amount range
    elseif ($fine_amount < 2 || $fine_amount > 500) {
        $_SESSION['message'] = "Fine must be between 2 - 500 LKR";
        header("Location: main.php?page=Fine");
        exit();
    }
    else {
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

        if ($stmt->execute()) {
            $_SESSION['message'] = "Fine updated successfully";
        } else {
            $_SESSION['message'] = "Error updating fine: " . $conn->error;
        }
        
        header("Location: main.php?page=Fine");
        exit();
    }
}

/* =========================
   DELETE FINE
========================= */
if (isset($_POST['delete_fine'])) {

    $fine_id = $_POST['fine_id'];

    $stmt = $conn->prepare("DELETE FROM fine WHERE fine_id=?");
    $stmt->bind_param("s", $fine_id);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Fine deleted successfully";
    } else {
        $_SESSION['message'] = "Error deleting fine: " . $conn->error;
    }
    
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
ORDER BY f.fine_date_modified DESC
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
<table class="table">

<tr>
    <th style="background-color: #1b5ee4; color: white;">Fine ID</th>
    <th style="background-color: #1b5ee4; color: white;">Member ID</th>
    <th style="background-color: #1b5ee4; color: white;">Member Name</th>
    <th style="background-color: #1b5ee4; color: white;">Book Name</th>
    <th style="background-color: #1b5ee4; color: white;">Amount</th>
    <th style="background-color: #1b5ee4; color: white;">Date</th>
    <th style="background-color: #1b5ee4; color: white;">Action</th>
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

        <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this fine?')">
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

<input type="text" name="fine_id" placeholder="F001" required pattern="^F\d{3}$" title="Format: F001, F002, etc.">
<input type="text" name="member_id" placeholder="M001" required pattern="^M\d{3}$" title="Format: M001, M002, etc.">
<input type="text" name="book_id" placeholder="B001" required pattern="^B\d{3}$" title="Format: B001, B002, etc.">
<input type="number" name="fine_amount" placeholder="2 - 500 LKR" required min="2" max="500">

<button name="add_fine">Add Fine</button>

</form>

<hr>

<!-- ================= UPDATE FORM ================= -->
<h3>Update Fine</h3>

<form method="POST">

<input type="text" name="fine_id" id="f_id" readonly>
<input type="text" name="member_id" id="f_member" required pattern="^M\d{3}$" title="Format: M001, M002, etc.">
<input type="number" name="fine_amount" id="f_amount" required min="2" max="500">

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