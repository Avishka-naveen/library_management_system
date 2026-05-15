<?php
include 'db_connection.php';


/* =========================
   FETCH CATEGORIES
========================= */
$categories = [];

$sql = "SELECT * FROM bookcategory";
$result = $conn->query($sql);

$categoryCount=$result->num_rows;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

/* =========================
   ADD CATEGORY
========================= */
if (isset($_POST['add_category'])) {

    $category_id   = $_POST['category_id'];
    $category_Name = $_POST['category_Name'];
    $date_modified = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("
        INSERT INTO bookcategory (category_id, category_Name, date_modified)
        VALUES (?, ?, ?)
    ");

    $stmt->bind_param("sss", $category_id, $category_Name, $date_modified);
    $stmt->execute();

    $_SESSION['message'] = "Category added successfully";

    header("Location: main.php?page=BookCategory");
    exit();
}

/* =========================
   UPDATE CATEGORY
========================= */
if (isset($_POST['update_category'])) {

    $category_id   = $_POST['category_id'];
    $category_Name = $_POST['category_Name'];
    $date_modified = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("
        UPDATE bookcategory 
        SET category_Name=?, date_modified=? 
        WHERE category_id=?
    ");

    $stmt->bind_param("sss", $category_Name, $date_modified, $category_id);
    $stmt->execute();

    $_SESSION['message'] = "Category updated successfully";

    header("Location: main.php?page=BookCategory");
    exit();
}

/* =========================
   DELETE CATEGORY
========================= */
if (isset($_POST['delete_category'])) {

    $category_id = $_POST['category_id'];

    $stmt = $conn->prepare("
        DELETE FROM bookcategory 
        WHERE category_id=?
    ");

    $stmt->bind_param("s", $category_id);
    $stmt->execute();

    $_SESSION['message'] = "Category deleted successfully";

    header("Location: main.php?page=BookCategory");
    exit();
}
?>

<link rel="stylesheet" href="./css/bookCategory.css">

<div class="container">

<h1 style="text-align: center;color: #1b5ee4; font-weight: bolder;">📚 Book Category Management</h1>
<p style="text-align: left; color: #000000; font-size: 1.2rem;">Total Categories: <span style="color:red; font-weight: bolder; font-size:1.5rem;"><?php echo $categoryCount; ?></span></p>


<!-- MESSAGE -->
<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-info">
        <?= $_SESSION['message']; unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>

<!-- ================= TABLE ================= -->
<table>

<tr>
    <th>Category ID</th>
    <th>Name</th>
    <th>Date Modified</th>
    <th>Action</th>
</tr>

<?php foreach ($categories as $cat): ?>
<tr>

    <td><?= $cat['category_id']; ?></td>
    <td><?= $cat['category_Name']; ?></td>
    <td><?= $cat['date_modified']; ?></td>

    <td>
        <button onclick="editCategory(
            '<?= $cat['category_id']; ?>',
            '<?= $cat['category_Name']; ?>'
        )">
            Edit
        </button>

        <form method="POST" style="display:inline;">
            <input type="hidden" name="category_id" value="<?= $cat['category_id']; ?>">
            <button name="delete_category">Delete</button>
        </form>
    </td>

</tr>
<?php endforeach; ?>

</table>

<hr>

<!-- ================= ADD FORM ================= -->
<h3>Add Category</h3>

<form method="POST">

<input type="text" name="category_id" placeholder="C001" required>
<input type="text" name="category_Name" placeholder="Category Name" required>

<button name="add_category">Add Category</button>

</form>

<hr>

<!-- ================= UPDATE FORM ================= -->
<h3>Update Category</h3>

<form method="POST">

<input type="text" name="category_id" id="c_id" readonly>
<input type="text" name="category_Name" id="c_name" required>

<button name="update_category">Update Category</button>

</form>

</div>

<script>
function editCategory(id, name) {
    document.getElementById("c_id").value = id;
    document.getElementById("c_name").value = name;
}
</script>