<?php
include './db_connection.php';


$message = "";

$result = $conn->query("SELECT * FROM bookborrower");
$borrowCount=$result->num_rows;

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
   ADD BORROW RECORD
========================= */
if (isset($_POST['submit'])) {

    $borrow_id = $_POST['borrow_id'];
    $book_id = $_POST['book_id'];
    $member_id = $_POST['member_id'];
    $borrow_status = $_POST['borrow_status'];
    $modified_date = date('Y-m-d');

    if (!preg_match('/^BR\d{3}$/', $borrow_id)) {
        $message = "Invalid Borrow ID (BR001)";
    }
    elseif (!preg_match('/^B\d{3}$/', $book_id)) {
        $message = "Invalid Book ID (B001)";
    }
    elseif (!preg_match('/^M\d{3}$/', $member_id)) {
        $message = "Invalid Member ID (M001)";
    }
    // ADDED: Check if Book ID exists in book table
    elseif (!validateBookExists($conn, $book_id)) {
        $message = "Error: Book ID '$book_id' does not exist in the Books table! Please add the book first.";
    }
    // ADDED: Check if Member ID exists in member table
    elseif (!validateMemberExists($conn, $member_id)) {
        $message = "Error: Member ID '$member_id' does not exist in the Members table! Please register the member first.";
    }
    else {

        $stmt = $conn->prepare("
            INSERT INTO bookborrower 
            (borrow_id, book_id, member_id, borrow_status, borrower_date_modified)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param("sssss", 
            $borrow_id, 
            $book_id, 
            $member_id, 
            $borrow_status, 
            $modified_date
        );

        if ($stmt->execute()) {
            $message = "Record Added Successfully";
        } else {
            $message = "Error adding record";
        }
    }
}


/* =========================
   DELETE RECORD
========================= */
if (isset($_POST['delete'])) {

    $borrow_id = $_POST['borrow_id'];

    $stmt = $conn->prepare("DELETE FROM bookborrower WHERE borrow_id=?");
    $stmt->bind_param("s", $borrow_id);

    if ($stmt->execute()) {
        $message = "Record Deleted Successfully";
    } else {
        $message = "Delete Failed";
    }
}


/* =========================
   UPDATE RECORD
========================= */
if (isset($_POST['update'])) {

    $borrow_id = $_POST['borrow_id'];
    $book_id = $_POST['book_id'];
    $member_id = $_POST['member_id'];
    $borrow_status = $_POST['borrow_status'];

    // ADDED: Validate Book ID format
    if (!preg_match('/^B\d{3}$/', $book_id)) {
        $message = "Invalid Book ID (B001)";
    }
    // ADDED: Validate Member ID format
    elseif (!preg_match('/^M\d{3}$/', $member_id)) {
        $message = "Invalid Member ID (M001)";
    }
    // ADDED: Check if Book ID exists in book table
    elseif (!validateBookExists($conn, $book_id)) {
        $message = "Error: Book ID '$book_id' does not exist in the Books table!";
    }
    // ADDED: Check if Member ID exists in member table
    elseif (!validateMemberExists($conn, $member_id)) {
        $message = "Error: Member ID '$member_id' does not exist in the Members table!";
    }
    else {
        $stmt = $conn->prepare("
            UPDATE bookborrower 
            SET book_id=?, member_id=?, borrow_status=? 
            WHERE borrow_id=?
        ");

        $stmt->bind_param("ssss", 
            $book_id, 
            $member_id, 
            $borrow_status, 
            $borrow_id
        );

        if ($stmt->execute()) {
            $message = "Record Updated Successfully";
        } else {
            $message = "Update Failed";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Book Borrow Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
.container {
    background: #ffffff;
    padding: 20px;
    border-radius: 10px;
    min-height: 100vh;
}
h2 { color: #0d6efd;
    margin-bottom: 20px;
    text-align: center;
}
.th{
   background-color: #0d6efd;
   color: white;
}
</style>
</head>

<body>

<div class="container mt-4">

<h2 style="text-align: center;color: #1b5ee4; font-weight: bolder;">📖 Book Borrow Management</h2>
<p style="text-align: left; color: #000000; font-size: 1.2rem;">Total Borrow Records: <span style="color:red; font-weight: bolder; font-size:1.5rem;"><?php echo $borrowCount; ?></span></p>


<?php if ($message != ""): ?>
    <div class="alert alert-info"><?= $message ?></div>
<?php endif; ?>

<!-- ================= ADD FORM ================= -->
<form method="POST">

<div class="mb-2">
<input type="text" name="borrow_id" class="form-control" placeholder="BR001" required>
</div>

<div class="mb-2">
<input type="text" name="book_id" class="form-control" placeholder="B001" required>
</div>

<div class="mb-2">
<input type="text" name="member_id" class="form-control" placeholder="M001" required>
</div>

<div class="mb-2">
<select name="borrow_status" class="form-control">
<option value="available">Available</option>
<option value="borrowed">Borrowed</option>
</select>
</div>

<button class="btn btn-success" name="submit">Add</button>

</form>

<hr>

<!-- ================= TABLE ================= -->
<table class="table table-bordered mt-3">

<tr>
<th style="background-color: #0d6efd; color: white;">Borrow ID</th>
<th style="background-color: #0d6efd; color: white;">Book ID</th>
<th style="background-color: #0d6efd; color: white;">Member ID</th>
<th style="background-color: #0d6efd; color: white;">Status</th>
<th style="background-color: #0d6efd; color: white;">Action</th>
</tr>

<?php
$result = $conn->query("SELECT * FROM bookborrower");
$borrowCount=$result->num_rows;

while ($row = $result->fetch_assoc()):
?>

<tr>
<td><?= $row['borrow_id'] ?></td>
<td><?= $row['book_id'] ?></td>
<td><?= $row['member_id'] ?></td>
<td><?= $row['borrow_status'] ?></td>

<td>

<!-- UPDATE -->
<button class="btn btn-primary btn-sm"
    onclick="editRow('<?= $row['borrow_id'] ?>','<?= $row['book_id'] ?>','<?= $row['member_id'] ?>','<?= $row['borrow_status'] ?>')">
    Edit
</button>

<!-- DELETE -->
<form method="POST" style="display:inline;">
    <input type="hidden" name="borrow_id" value="<?= $row['borrow_id'] ?>">
    <button class="btn btn-danger btn-sm" name="delete">Delete</button>
</form>

</td>
</tr>

<?php endwhile; ?>

</table>

<!-- ================= UPDATE FORM ================= -->
<hr>

<h4>Update Record</h4>

<form method="POST">

<input type="hidden" name="borrow_id" id="u_borrow_id">

<input type="text" name="book_id" id="u_book_id" class="form-control mb-2">

<input type="text" name="member_id" id="u_member_id" class="form-control mb-2">

<select name="borrow_status" id="u_status" class="form-control mb-2">
    <option value="available">Available</option>
    <option value="borrowed">Borrowed</option>
</select>

<button class="btn btn-warning" name="update">Update</button>

</form>

</div>

<script>
function editRow(borrow_id, book_id, member_id, status){

    document.getElementById('u_borrow_id').value = borrow_id;
    document.getElementById('u_book_id').value = book_id;
    document.getElementById('u_member_id').value = member_id;
    document.getElementById('u_status').value = status;
}
</script>

</body>
</html>