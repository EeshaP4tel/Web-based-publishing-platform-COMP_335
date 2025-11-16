<?php
require __DIR__.'/config.php';
$cn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

echo "<h1>Admin Dashboard</h1>";

// Basic counts
$users_count = mysqli_fetch_assoc(mysqli_query($cn, "SELECT COUNT(*) as c FROM members"))['c'];
$items_count = mysqli_fetch_assoc(mysqli_query($cn, "SELECT COUNT(*) as c FROM items"))['c'];
$donations_total = mysqli_fetch_assoc(mysqli_query($cn, "SELECT SUM(amount) as t FROM donations"))['t'];

echo "<h3>Summary</h3>";
echo "<p>Total Users: $users_count</p>";
echo "<p>Total Items: $items_count</p>";
echo "<p>Total Donations: $" . number_format($donations_total, 2) . "</p>";

// Recent donations
echo "<h3>Recent Donations</h3>";
$sql = "SELECT m.name, i.title, c.charity_name, d.amount 
        FROM donations d
        JOIN members m ON d.member_id = m.member_id
        JOIN items i ON d.item_id = i.item_id
        JOIN charities c ON d.charity_id = c.charity_id
        ORDER BY d.donation_date DESC LIMIT 5";
$result = mysqli_query($cn, $sql);

echo "<table border='1' cellpadding='6'>";
echo "<tr><th>Donor</th><th>Item</th><th>Charity</th><th>Amount</th></tr>";

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['name']) . "</td>";
    echo "<td>" . htmlspecialchars($row['title']) . "</td>";
    echo "<td>" . htmlspecialchars($row['charity_name']) . "</td>";
    echo "<td>$" . number_format($row['amount'], 2) . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<p><a href='items.php'>Back to Items</a></p>";
mysqli_close($cn);
?>
