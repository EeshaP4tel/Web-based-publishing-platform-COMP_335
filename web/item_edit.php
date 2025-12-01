<?php
require_once "auth.php";
require_author();
require_once "config.php";
include __DIR__.'/header.php';

$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($item_id <= 0) {
    echo "<p>Invalid item.</p>";
    include __DIR__.'/footer.php';
    exit;
}

$member_id = $_SESSION['member_id'];

// Make sure this item belongs to the logged-in author
$sql = "SELECT i.title, i.description
        FROM items i
        JOIN authors a ON i.author_id = a.author_id
        WHERE i.item_id = ? AND a.member_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $item_id, $member_id);
$stmt->execute();
$stmt->bind_result($title, $desc);
if (!$stmt->fetch()) {
    echo "<p>Item not found or you are not the author.</p>";
    $stmt->close();
    include __DIR__.'/footer.php';
    exit;
}
$stmt->close();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $desc  = trim($_POST['description'] ?? '');

    if ($title === '') $errors[] = "Title is required.";

    if (empty($errors)) {
        $stmt = $mysqli->prepare("UPDATE items SET title=?, description=? WHERE item_id=?");
        $stmt->bind_param("ssi", $title, $desc, $item_id);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: my_items.php");
            exit;
        } else {
            $errors[] = "Update failed: " . $mysqli->error;
        }
        $stmt->close();
    }
}
?>

<h2>Edit Item</h2>

<?php foreach ($errors as $e): ?>
  <p style="color:red;"><?= htmlspecialchars($e) ?></p>
<?php endforeach; ?>

<form method="post">
  Title:<br>
  <input name="title" value="<?= htmlspecialchars($title) ?>"><br><br>

  Description:<br>
  <textarea name="description" rows="6" cols="60"><?= htmlspecialchars($desc) ?></textarea><br><br>

  <button type="submit">Save</button>
</form>

<p><a href="my_items.php">Back to My Items</a></p>

<?php include __DIR__.'/footer.php'; ?>
