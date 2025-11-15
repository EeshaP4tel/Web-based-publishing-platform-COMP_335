<?php include __DIR__.'/header.php'; ?>
<h1>Welcome</h1>
<p>This is our simple CopyForward website. Welcome! Below are the most recent items.</p>

<?php
$sql = "SELECT i.item_id, i.title, i.upload_date, m.name AS author_name
        FROM items i
        JOIN authors a ON i.author_id=a.author_id
        JOIN members m ON a.member_id=m.member_id
        ORDER BY i.upload_date DESC, i.item_id DESC
        LIMIT 5";
$res = mysqli_query($cn, $sql);

if (!$res) {
  echo "<p>Could not load items.</p>";
} else {
  echo "<table>";
  echo "<tr><th>Title</th><th>Author</th><th>Uploaded</th></tr>";
  while ($r = mysqli_fetch_assoc($res)) {
    $id = (int)$r['item_id'];
    echo "<tr>";
    echo "<td><a href='item_details.php?id={$id}'>".htmlspecialchars($r['title'])."</a></td>";
    echo "<td>".htmlspecialchars($r['author_name'])."</td>";
    echo "<td>".htmlspecialchars($r['upload_date'])."</td>";
    echo "</tr>";
  }
  echo "</table>";
}
?>

<?php include __DIR__.'/footer.php'; ?>
