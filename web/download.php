<?php
require __DIR__.'/config.php';
$cn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$cn) { die("DB connect fail: ".mysqli_connect_error()); }

$item_id = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;
if ($item_id <= 0) { die("Invalid item id."); }

/*
  DUMMY DATA ADDED Im just recording the download with a fixed member ID chosen quickly.
  In a real app, you'd use the logged-in user’s id from $_SESSION #task 2.
*/
$member_id = 2; 

$stmt = mysqli_prepare($cn, "INSERT INTO downloads(member_id,item_id) VALUES (?,?)");
mysqli_stmt_bind_param($stmt, "ii", $member_id, $item_id);
mysqli_stmt_execute($stmt);

echo "<p>Download recorded for item #".(int)$item_id." (pretend file downloaded).</p>";
echo "<p><a href='item_details.php?id=".(int)$item_id."'>&laquo; back</a></p>";
