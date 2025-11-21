<?php
require_once "auth.php";
require_login();

$id = $_SESSION['member_id'];

$stmt = $mysqli->prepare("SELECT name, email, organization, join_date FROM members WHERE member_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name, $email, $org, $join);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html>
<body>

<h2>Your Profile</h2>

<p>Name: <?= $name ?></p>
<p>Email: <?= $email ?></p>
<p>Organization: <?= $org ?></p>
<p>Joined: <?= $join ?></p>

<p><a href="upload.php">Upload (Authors only)</a></p>
<p><a href="logout.php">Logout</a></p>

</body>
</html>
