<?php
include 'db_connection.php';


/* FETCH DATA */
$categories = $conn->query("SELECT * FROM bookcategory");
$books = $conn->query("SELECT * FROM book");

$bookCount=$books->num_rows;

/* ADD BOOK */
if (isset($_POST['add_book'])) {

    $book_id = trim($_POST['book_id']);
    $book_name = trim($_POST['book_name']);
    $category_id = trim($_POST['category_id']);

    if (!preg_match('/^B\d{3}$/', $book_id)) {
        $_SESSION['message'] = "Book ID must be B001 format";
    } else {

        $stmt = $conn->prepare("SELECT book_id FROM book WHERE book_id=?");
        $stmt->bind_param("s", $book_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $_SESSION['message'] = "Book already exists";
        } else {

            $insert = $conn->prepare("
                INSERT INTO book (book_id, book_name, category_id)
                VALUES (?, ?, ?)
            ");

            $insert->bind_param("sss", $book_id, $book_name, $category_id);
            $insert->execute();

            $_SESSION['message'] = "Book added successfully";
        }
    }

    header("Location: main.php?page=Books");
    exit();
}

/* UPDATE BOOK */
if (isset($_POST['update_book'])) {

    $book_id = $_POST['book_id'];
    $book_name = $_POST['book_name'];
    $category_id = $_POST['category_id'];

    $stmt = $conn->prepare("
        UPDATE book 
        SET book_name=?, category_id=? 
        WHERE book_id=?
    ");

    $stmt->bind_param("sss", $book_name, $category_id, $book_id);
    $stmt->execute();

    $_SESSION['message'] = "Book updated successfully";

    header("Location: main.php?page=Books");
    exit();
}

/* DELETE BOOK */
if (isset($_POST['delete_book'])) {

    $book_id = $_POST['book_id'];

    $stmt = $conn->prepare("DELETE FROM book WHERE book_id=?");
    $stmt->bind_param("s", $book_id);
    $stmt->execute();

    $_SESSION['message'] = "Book deleted successfully";

    header("Location: main.php?page=Books");
    exit();
}
?>

<link rel="stylesheet" href="./css/books.css">

<div class="container">

<h1 style="text-align: center;color: #1b5ee4; font-weight: bolder;">📘 Manage Books</h1>
<p style="text-align: left; color: #000000; font-size: 1.2rem;">Total Books: <span style="color:red; font-weight: bolder; font-size:1.5rem;"><?php echo $bookCount; ?></span></p>

<!-- MESSAGE -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="message">
        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>

<!-- ================= TABLE ================= -->
<table>

<tr>
    <th>Book ID</th>
    <th>Book Name</th>
    <th>Category</th>
    <th>Action</th>
</tr>

<?php while ($book = $books->fetch_assoc()): ?>
<tr>

    <td><?= $book['book_id'] ?></td>
    <td><?= $book['book_name'] ?></td>
    <td><?= $book['category_id'] ?></td>

    <td>

        <button class="btn-edit" onclick="editBook(
            '<?= $book['book_id'] ?>',
            '<?= $book['book_name'] ?>',
            '<?= $book['category_id'] ?>'
        )">
            Edit
        </button>

        <form method="POST" style="display:inline;">
            <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
            <button class="btn-delete" name="delete_book">Delete</button>
        </form>

    </td>

</tr>
<?php endwhile; ?>

</table>

<hr>

<!-- ================= ADD FORM ================= -->
<h3>Add Book</h3>

<form method="POST">

<input type="text" name="book_id" placeholder="B001" required>
<input type="text" name="book_name" placeholder="Book Name" required>

<select name="category_id" required>
    <option value="">Select Category</option>
    <?php while ($cat = $categories->fetch_assoc()): ?>
        <option value="<?= $cat['category_id'] ?>">
            <?= $cat['category_Name'] ?>
        </option>
    <?php endwhile; ?>
</select>

<button class="btn-add" name="add_book">Add Book</button>

</form>

<hr>

<!-- ================= UPDATE FORM ================= -->
<h3>Update Book</h3>

<form method="POST">

<input type="text" name="book_id" id="b_id" readonly>
<input type="text" name="book_name" id="b_name" required>

<select name="category_id" id="b_category" required>
    <?php
    $cats = $conn->query("SELECT * FROM bookcategory");
    while ($c = $cats->fetch_assoc()):
    ?>
        <option value="<?= $c['category_id'] ?>">
            <?= $c['category_Name'] ?>
        </option>
    <?php endwhile; ?>
</select>

<button class="btn-update" name="update_book">Update Book</button>

</form>

</div>

<script>
function editBook(id, name, category) {
    document.getElementById("b_id").value = id;
    document.getElementById("b_name").value = name;
    document.getElementById("b_category").value = category;
}
</script>