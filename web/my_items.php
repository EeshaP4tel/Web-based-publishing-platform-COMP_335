<?php
require_once "auth.php";
require_author();               
require_once "config.php";
include __DIR__.'/header.php';

$member_id = $_SESSION['member_id'];

$sql = "SELECT i.item_id, i.title, i.upload_date
        FROM items i
        JOIN authors a ON i.author_id = a.author_id
        WHERE a.member_id = ?
        ORDER BY i.item_id DESC";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h1>My Items</h1>

<table border="1" cellpadding="6">
  <tr>
    <th>ID</th>
    <th>Title</th>
    <th>Uploaded</th>
    <th>Actions</th>
  </tr>
  <?php while ($r = $result->fetch_assoc()): ?>
    <tr>
      <td><?= (int)$r['item_id'] ?></td>
      <td><?= htmlspecialchars($r['title']) ?></td>
      <td><?= htmlspecialchars($r['upload_date']) ?></td>
      <td>
        <a href="item_edit.php?id=<?= (int)$r['item_id'] ?>">Edit</a> |
        <a href="item_delete.php?id=<?= (int)$r['item_id'] ?>" onclick="return confirm('Delete this item?');">Delete</a>
      </td>
    </tr>
  <?php endwhile; ?>
</table>

<p><a href="upload.php">Upload new item</a></p>

<?php include __DIR__.'/footer.php'; ?>
