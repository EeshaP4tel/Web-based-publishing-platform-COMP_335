<?php
require_once "auth.php";
require_login();
require_once "config.php";

if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    die("Access denied.");
}

$errors = [];
$edit_id = isset($_GET['edit_id']) ? (int)$_GET['edit_id'] : 0;
$edit_name = '';
$edit_desc = '';

// Handle add/edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $name = trim($_POST['charity_name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if ($name === '') {
            $errors[] = "Charity name is required.";
        } else {
            $stmt = $mysqli->prepare("INSERT INTO charities(charity_name, description) VALUES(?, ?)");
            $stmt->bind_param("ss", $name, $desc);
            $stmt->execute();
            $stmt->close();
        }
    } elseif ($action === 'edit') {
        $cid  = (int)($_POST['charity_id'] ?? 0);
        $name = trim($_POST['charity_name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if ($name === '') {
            $errors[] = "Charity name is required.";
        } else {
            $stmt = $mysqli->prepare("UPDATE charities SET charity_name=?, description=? WHERE charity_id=?");
            $stmt->bind_param("ssi", $name, $desc, $cid);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $cid = (int)$_GET['delete_id'];

    // Optionally delete donations for this charity first
    $stmt = $mysqli->prepare("DELETE FROM donations WHERE charity_id=?");
    $stmt->bind_param("i", $cid);
    $stmt->execute();
    $stmt->close();

    $stmt = $mysqli->prepare("DELETE FROM charities WHERE charity_id=?");
    $stmt->bind_param("i", $cid);
    $stmt->execute();
    $stmt->close();
}

// If editing, load charity
if ($edit_id > 0) {
    $stmt = $mysqli->prepare("SELECT charity_name, description FROM charities WHERE charity_id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $stmt->bind_result($edit_name, $edit_desc);
    $stmt->fetch();
    $stmt->close();
}

$res = $mysqli->query("SELECT charity_id, charity_name, description FROM charities ORDER BY charity_name");

include __DIR__.'/header.php';
?>

<h1>Manage Charities</h1>

<?php foreach ($errors as $e): ?>
  <p style="color:red;"><?= htmlspecialchars($e) ?></p>
<?php endforeach; ?>

<h3>Add Charity</h3>
<form method="post">
  <input type="hidden" name="action" value="add">
  Name:<br>
  <input name="charity_name"><br><br>
  Description:<br>
  <textarea name="description" rows="3" cols="40"></textarea><br><br>
  <button type="submit">Add</button>
</form>

<?php if ($edit_id > 0): ?>
  <h3>Edit Charity #<?= $edit_id ?></h3>
  <form method="post">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="charity_id" value="<?= $edit_id ?>">
    Name:<br>
    <input name="charity_name" value="<?= htmlspecialchars($edit_name) ?>"><br><br>
    Description:<br>
    <textarea name="description" rows="3" cols="40"><?= htmlspecialchars($edit_desc) ?></textarea><br><br>
    <button type="submit">Save Changes</button>
  </form>
<?php endif; ?>

<h3>Existing Charities</h3>
<table border="1" cellpadding="6">
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Description</th>
    <th>Actions</th>
  </tr>
  <?php while ($row = $res->fetch_assoc()): ?>
    <tr>
      <td><?= (int)$row['charity_id'] ?></td>
      <td><?= htmlspecialchars($row['charity_name']) ?></td>
      <td><?= htmlspecialchars($row['description']) ?></td>
      <td>
        <a href="admin_charities.php?edit_id=<?= (int)$row['charity_id'] ?>">Edit</a> |
        <a href="admin_charities.php?delete_id=<?= (int)$row['charity_id'] ?>" onclick="return confirm('Delete this charity?');">Delete</a>
      </td>
    </tr>
  <?php endwhile; ?>
</table>

<p><a href="admin.php">Back to Admin Dashboard</a></p>

<?php include __DIR__.'/footer.php'; ?>
