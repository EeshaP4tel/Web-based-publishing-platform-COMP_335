<?php
require __DIR__.'/config.php';
$cn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

echo "<h1>Committees</h1>";

$sql = "SELECT * FROM committees";
$result = mysqli_query($cn, $sql);

echo "<table border='1' cellpadding='6'>";
echo "<tr><th>Committee Name</th><th>Description</th><th>Action</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
    $id = (int)$row['committee_id'];
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['committee_name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
    echo "<td><a href='committee_members.php?committee_id=$id'>View Members</a></td>";
    echo "</tr>";
}
echo "</table>";

echo "<p><a href='items.php'>Back to Items</a></p>";
mysqli_close($cn);
?>
