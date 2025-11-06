<?php
require __DIR__.'/config.php';
$cn = mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME);
if (!$cn) { die("DB connect fail: ".mysqli_connect_error()); }
$r = mysqli_query($cn,"SELECT title FROM items");
echo "<h3>Items</h3><ul>";
while ($row = mysqli_fetch_assoc($r)) echo "<li>".htmlspecialchars($row['title'])."</li>";
echo "</ul>";
