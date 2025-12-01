<?php
require_once __DIR__.'/auth.php';
require_login();
require_once __DIR__.'/config.php';

if (empty($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    die("Access denied.");
}

$cn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$cn) { die("DB connect fail: ".mysqli_connect_error()); }

include __DIR__.'/header.php';

echo "<h1>Admin Dashboard</h1>";

// basic stats
$users = mysqli_fetch_assoc(mysqli_query($cn, "SELECT COUNT(*) AS c FROM members"))['c'];
$items = mysqli_fetch_assoc(mysqli_query($cn, "SELECT COUNT(*) AS c FROM items"))['c'];
$don   = mysqli_fetch_assoc(mysqli_query($cn, "SELECT IFNULL(SUM(amount),0) AS t FROM donations"))['t'];

echo "<p>Total Users: $users</p>";
echo "<p>Total Items: $items</p>";
echo "<p>Total Donations: $".number_format($don,2)."</p>";

echo "<h3>Quick Links</h3>";
echo "<ul>";
echo "<li><a href='admin_members.php'>Manage Members</a></li>";
echo "<li><a href='admin_committees.php'>Manage Committees</a></li>";
echo "<li><a href='admin_charities.php'>Manage Charities</a></li>";
echo "</ul>";

echo "<h3>Recent Donations</h3>";

$sql = "SELECT m.name, i.title, c.charity_name, d.amount, d.donation_date
        FROM donations d
        JOIN members m ON d.member_id = m.member_id
        JOIN items i ON d.item_id = i.item_id
        JOIN charities c ON d.charity_id = c.charity_id
        ORDER BY d.donation_date DESC 
        LIMIT 5";

$res = mysqli_query($cn, $sql);

echo "<table border='1' cellpadding='6'>";
echo "<tr><th>Donor</th><th>Item</th><th>Charity</th><th>Amount</th><th>Date</th></tr>";

while ($r = mysqli_fetch_assoc($res)) {
    echo "<tr>";
    echo "<td>".htmlspecialchars($r['name'])."</td>";
    echo "<td>".htmlspecialchars($r['title'])."</td>";
    echo "<td>".htmlspecialchars($r['charity_name'])."</td>";
    echo "<td>$".number_format($r['amount'],2)."</td>";
    echo "<td>".htmlspecialchars($r['donation_date'])."</td>";
    echo "</tr>";
}

echo "</table>";

mysqli_close($cn);
include __DIR__.'/footer.php';
