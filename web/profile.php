<?php
require_once "auth.php";
require_login();
require __DIR__.'/header.php';   

$id = $_SESSION['member_id'];

// get basic info
$stmt = $mysqli->prepare("SELECT name, email, organization, join_date FROM members WHERE member_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name, $email, $org, $join);
$stmt->fetch();
$stmt->close();

// donations this year
$year = date('Y');
$stmt2 = $mysqli->prepare("
    SELECT IFNULL(SUM(amount),0)
    FROM donations
    WHERE member_id = ? AND YEAR(donation_date) = ?
");
$stmt2->bind_param("is", $id, $year);
$stmt2->execute();
$stmt2->bind_result($sum);
$stmt2->fetch();
$stmt2->close();
?>

<h2>Your Profile</h2>

<p>Name: <?= htmlspecialchars($name) ?></p>
<p>Email: <?= htmlspecialchars($email) ?></p>
<p>Organization: <?= htmlspecialchars($org) ?></p>
<p>Joined: <?= htmlspecialchars($join) ?></p>

<p>Total donations in <?= $year ?>: $<?= number_format($sum, 2) ?></p>

<p><a href="profile_edit.php">Edit Profile</a></p>
<p><a href="upload.php">Upload (Authors only)</a></p>
<p><a href="logout.php">Logout</a></p>

<?php include __DIR__.'/footer.php'; ?>
