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

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add') {
        $name = trim($_POST['committee_name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if ($name === '') {
            $errors[] = "Committee name is required.";
        } else {
            $stmt = $mysqli->prepare("INSERT INTO committees(committee_name, description) VALUES(?, ?)");
            $stmt->bind_param("ss", $name, $desc);
            $stmt->execute();
            $stmt->close();
        }
    } elseif ($action === 'edit') {
        $cid  = (int)($_POST['committee_id'] ?? 0);
        $name = trim($_POST['committee_name'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if ($name === '') {
            $errors[] = "Committee name is required.";
        } else {
            $stmt = $mysqli->prepare("UPDATE committees SET committee_name=?, description=? WHERE committee_id=?");
            $stmt->bind_param("ssi", $name, $desc, $cid);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $cid = (int)$_GET['delete_id'];
    // first delete committee_members for this committee
    $stmt = $mysqli->prepare("DELETE FROM committee_members WHERE committee_id=?");
    $stmt->bind_param("i", $cid);
    $stmt->execute();
    $stmt->close();

    // then delete the committee
    $stmt = $mysqli->prepare("DELETE FROM committees WHERE committee_id=?");
    $stmt->bind_param("i", $cid);
    $stmt->execute();
    $stmt->close();
}

// If editing, load committee
if ($edit_id > 0) {
    $stmt = $mysqli->prepare("SELECT committee_name, description FROM committees WHERE committee_id=?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $stmt->bind_result($edit_name, $edit_desc);
    $stmt->fetch();
    $stmt->close();
}

$res = $mysqli->query("SELECT committee_id, committee_name, description FROM committees ORDER BY committee_id");

include __DIR__.'/header.php';
?>

<h1>Manage Committees</h1>

<?php foreach ($errors as $e): ?>
  <p style="color:red;"><?= htmlspecialchars($e) ?></p>
<?php endforeach; ?>

<h3>Add Committee</h3>
<form method="post">
  <input type="hidden" name="action" value="add">
  Name:<br>
  <input name="committee_name"><br><br>
  Description:<br>
  <textarea name="description" rows="3" cols="40"></textarea><br><br>
  <button type="submit">Add</button>
</form>

<?php if ($edit_id > 0): ?>
  <h3>Edit Committee #<?= $edit_id ?></h3>
  <form method="post">
    <input type="hidden" name="action" value="edit">
    <input type="hidden" name="committee_id" value="<?= $edit_id ?>">
    Name:<br>
    <input name="committee_name" value="<?= htmlspecialchars($edit_name) ?>"><br><br>
    Description:<br>
    <textarea name="description" rows="3" cols="40"><?= htmlspecialchars($edit_desc) ?></textarea><br><br>
    <button type="submit">Save Changes</button>
  </form>
<?php endif; ?>

<h3>Existing Committees</h3>
<table border="1" cellpadding="6">
  <tr>
    <th>ID</th>
    <th>Name</th>
    <th>Description</th>
    <th>Actions</th>
  </tr>
  <?php while ($row = $res->fetch_assoc()): ?>
    <tr>
      <td><?= (int)$row['committee_id'] ?></td>
      <td><?= htmlspecialchars($row['committee_name']) ?></td>
      <td><?= htmlspecialchars($row['description']) ?></td>
      <td>
        <a href="admin_committees.php?edit_id=<?= (int)$row['committee_id'] ?>">Edit</a> |
        <a href="admin_committees.php?delete_id=<?= (int)$row['committee_id'] ?>" onclick="return confirm('Delete this committee?');">Delete</a>
      </td>
    </tr>
  <?php endwhile; ?>
</table>

<p><a href="admin.php">Back to Admin Dashboard</a></p>

<?php include __DIR__.'/footer.php'; ?>
