<?php
require __DIR__.'/config.php';
include __DIR__.'/header.php';

$cn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$cn) { die("DB connect fail: " . mysqli_connect_error()); }

echo "<h1>Committees</h1>";

// If we were redirected back from committee_join.php, show the message
if (isset($_GET['msg'])) {
    echo "<p style='color:green;'>" . htmlspecialchars($_GET['msg']) . "</p>";
}

$sql = "SELECT committee_id, committee_name, description FROM committees";
$res = mysqli_query($cn, $sql);

echo "<table border='1' cellpadding='6'>";
echo "<tr><th>Name</th><th>Description</th><th>Action</th></tr>";

while ($row = mysqli_fetch_assoc($res)) {
    $id = (int)$row['committee_id'];
    echo "<tr>";
    // committee name
    echo "<td>" . htmlspecialchars($row['committee_name']) . "</td>";
    // description
    echo "<td>" . htmlspecialchars($row['description']) . "</td>";
    // actions
    echo "<td><a href='committee_members.php?committee_id=$id'>View Members</a>";
    if (isset($_SESSION['member_id'])) {
        echo " | <a href='committee_join.php?committee_id=$id'>Request to Join</a>";
    }
    echo "</td>";
    echo "</tr>";
}

echo "</table>";
echo "<p><a href='items.php'>Back to Items</a></p>";

mysqli_close($cn);
include __DIR__.'/footer.php';
