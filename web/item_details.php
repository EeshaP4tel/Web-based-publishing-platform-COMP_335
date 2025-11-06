<?php
$cn = mysqli_connect("qvc353.encs.concordia.ca","qvc353_2","lightwheel91","qvc353_2");
if (!$cn) { die("DB connect fail: ".mysqli_connect_error()); }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { die("Invalid item id"); }

$info = mysqli_query($cn, "SELECT i.title, i.description, m.name AS author_name
                           FROM items i
                           JOIN authors a ON i.author_id=a.author_id
                           JOIN members m ON a.member_id=m.member_id
                           WHERE i.item_id={$id}");
$item = mysqli_fetch_assoc($info);
if (!$item) { die("Item not found."); }

echo "<h1>".htmlspecialchars($item['title'])."</h1>";
echo "<p><b>Author:</b> ".htmlspecialchars($item['author_name'])."</p>";
echo "<p>".nl2br(htmlspecialchars($item['description']))."</p>";

echo "<h2>Comments</h2>";
$cs = mysqli_query($cn, "SELECT c.comment_text, c.comment_date, m.name
                         FROM comments c
                         JOIN members m ON c.member_id=m.member_id
                         WHERE c.item_id={$id}
                         ORDER BY c.comment_date DESC");
if (mysqli_num_rows($cs) == 0) {
  echo "<p>No comments yet.</p>";
} else {
  echo "<ul>";
  while ($c = mysqli_fetch_assoc($cs)) {
    echo "<li><b>".htmlspecialchars($c['name'])."</b> (".$c['comment_date']."): ".
         htmlspecialchars($c['comment_text'])."</li>";
  }
  echo "</ul>";
}
echo "<p><a href='items.php'>&laquo; Back to items</a></p>";
