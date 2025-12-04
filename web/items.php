<?php 
require_once "auth.php";
require_login();
require_once "config.php";
include __DIR__.'/header.php'; 
?>

<h1>All Items</h1>

<?php
$sql = "SELECT i.item_id, i.title, i.upload_date, m.name AS author_name
        FROM items i
        JOIN authors a ON i.author_id=a.author_id
        JOIN members m ON a.member_id=m.member_id
        ORDER BY i.item_id DESC";

$res = $mysqli->query($sql);

if (!$res) {
    echo "<p>Could not load items.</p>";
} else {
    echo "<table>";
    echo "<tr><th>ID</th><th>Title</th><th>Author</th><th>Uploaded</th></tr>";

    while ($r = $res->fetch_assoc()) {
        $id = (int)$r['item_id'];
        ?>

        <tr>
            <td><?= $id ?></td>
            <td>
                <a href="item_details.php?item_id=<?= $id ?>">
                    <?= htmlspecialchars($r['title']) ?>
                </a>
            </td>
            <td><?= htmlspecialchars($r['author_name']) ?></td>
            <td><?= htmlspecialchars($r['upload_date']) ?></td>
        </tr>

        <?php
    }

    echo "</table>";
}
?>

<?php include __DIR__.'/footer.php'; ?>
