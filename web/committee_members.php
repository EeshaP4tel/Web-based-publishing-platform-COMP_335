<?php
require __DIR__.'/config.php';
$cn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$committee_id = (int)$_GET['committee_id'];

$cq = mysqli_query($cn, "SELECT committee_name FROM committees WHERE committee_id=$committee_id");
$c = mysqli_fetch_assoc($cq);

echo "<h1>Members of: ".htmlspecialchars($c['committee_name'])."</h1>";

$sql = "SELECT m.name, m.email, m.organization
        FROM committee_members cm
        JOIN members m ON cm.member_id = m.member_id
        WHERE cm.committee_id = $committee_id";
$res = mysqli_query($cn, $sql);

echo "<table border='1' cellpadding='6'>";
echo "<tr><th>Name</th><th>Email</th><th>Organization</th></tr>";

while ($r = mysqli_fetch_assoc($res)) {
    echo "<tr>";
    echo "<td>".htmlspecialchars($r['name'])."</td>";
    echo "<td>".htmlspecialchars($r['email'])."</td>";
    echo "<td>".htmlspecialchars($r['organization'])."</td>";
    echo "</tr>";
}

echo "</table>";
echo "<p><a href='committees.php'>Back to Committees</a></p>";

mysqli_close($cn);
?>
