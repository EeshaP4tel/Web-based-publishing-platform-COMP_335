<?php
require __DIR__.'/config.php';
$cn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Handle vote
if ($_POST['vote']) {
    $item_id = (int)$_POST['item_id'];
    $vote_value = mysqli_real_escape_string($cn, $_POST['vote']);
    
    // Use first committee (Plagiarism Review)
    $committee_id = 1;
    
    // Simple insert - in real system, check if user already voted
    $sql = "INSERT INTO plagiarism_votes (item_id, committee_id, member_id, vote_value) 
            VALUES ($item_id, $committee_id, 1, '$vote_value')";
    mysqli_query($cn, $sql);
    
    echo "<p>Vote recorded. Thanks!</p>";
}

echo "<h1>Plagiarism Voting</h1>";

// Get items
$sql = "SELECT i.item_id, i.title, m.name as author 
        FROM items i 
        JOIN authors a ON i.author_id = a.author_id 
        JOIN members m ON a.member_id = m.member_id";
$result = mysqli_query($cn, $sql);

while ($item = mysqli_fetch_assoc($result)) {
    $item_id = (int)$item['item_id'];
    
    echo "<h3>" . htmlspecialchars($item['title']) . "</h3>";
    echo "<p>Author: " . htmlspecialchars($item['author']) . "</p>";
    
    echo "<form method='post'>";
    echo "<input type='hidden' name='item_id' value='$item_id'>";
    echo "<button type='submit' name='vote' value='plagiarized'>Vote Plagiarized</button> ";
    echo "<button type='submit' name='vote' value='not_plagiarized'>Vote Not Plagiarized</button>";
    echo "</form>";
    echo "<hr>";
}

echo "<p><a href='committees.php'>Back to Committees</a></p>";
mysqli_close($cn);
?>
