<?php
require __DIR__.'/config.php';
$cn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

$committee_id = (int)$_GET['committee_id'];

// Get committee name
$committee_sql = "SELECT committee_name FROM committees WHERE committee_id = $committee_id";
$committee_result = mysqli_query($cn, $committee_sql);
$committee = mysqli_fetch_assoc($committee_result);

echo "<h1>Members of: " . htmlspecialchars($committee['committee_name']) . "</h1>";

// Get members
$sql = "SELECT m.name, m.email, m.organization 
        FROM committee_members cm 
        JOIN members m ON cm.member_id = m.member_id 
        WHERE cm.committee_id = $committee_id";
$result = mysqli_query($cn, $sql);

echo "<table border='1' cellpadding='6'>";
echo "<tr><th>Name</th><th>Email</th><th>Organization</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['email']) . "</td>";
    echo "<td>" . htmlspecialchars($row['organization']) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<p><a href='committees.php'>Back to Committees</a></p>";
mysqli_close($cn);
?>
