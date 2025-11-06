<?php
$cn = mysqli_connect("qvc353.encs.concordia.ca","qvc353_2","lightwheel91","qvc353_2");
if (!$cn) { die("DB connect fail: ".mysqli_connect_error()); }

$sql = "SELECT i.item_id, i.title, i.upload_date, m.name AS author_name
        FROM items i
        JOIN authors a ON i.author_id = a.author_id
        JOIN members m ON a.member_id = m.member_id
        ORDER BY i.item_id DESC";
$res = mysqli_query($cn, $sql);

echo "<h1>Items</h1>";
echo "<table border='1' cellpadding='6'><tr><th>ID</th><th>Title</th><th>Author</th><th>Uploaded</th></tr>";
while ($r = mysqli_fetch_assoc($res)) {
  $id = (int)$r['item_id'];
  echo "<tr>";
  echo "<td>{$id}</td>";
  echo "<td><a href='item_details.php?id={$id}'>".htmlspecialchars($r['title'])."</a></td>";
  echo "<td>".htmlspecialchars($r['author_name'])."</td>";
  echo "<td>".htmlspecialchars($r['upload_date'])."</td>";
  echo "</tr>";
}
echo "</table>";
