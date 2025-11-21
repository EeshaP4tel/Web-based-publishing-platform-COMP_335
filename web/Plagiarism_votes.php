<?php
require __DIR__.'/config.php';
$cn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Record vote
if (!empty($_POST['vote'])) {
    $item_id = (int)$_POST['item_id'];
    $vote    = mysqli_real_escape_string($cn, $_POST['vote']);

    // use committee 1 for simplicity
    $committee_id = 1;
    $member_id = 1; // static sample member

    $sql = "INSERT INTO plagiarism_votes(item_id, committee_id, member_id, vote_value)
            VALUES ($item_id, $committee_id, $member_id, '$vote')";
    mysqli_query($cn, $sql);

    echo "<p>Vote recorded.</p>";
}

echo "<h1>Plagiarism Voting</h1>";

$sql = "SELECT i.item_id, i.title, m.name AS author
        FROM items i
        JOIN authors a ON i.author_id=a.author_id
        JOIN members m ON a.member_id=m.member_id";

$res = mysqli_query($cn, $sql);

while ($r = mysqli_fetch_assoc($res)) {
    $id = (int)$r['item_id'];

    echo "<h3>".htmlspecialchars($r['title'])."</h3>";
    echo "<p>Author: ".htmlspecialchars($r['author'])."</p>";

    echo "<form method='post'>";
    echo "<input type='hidden' name='item_id' value='$id'>";
    echo "<button name='vote' value='plagiarized'>Vote Plagiarized</button> ";
    echo "<button name='vote' value='not_plagiarized'>Vote Not Plagiarized</button>";
    echo "</form><hr>";
}

echo "<p><a href='committees.php'>Back to Committees</a></p>";

mysqli_close($cn);
?>
