<?php
require_once "auth.php";
require_author();
require_once "config.php";

$item_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($item_id <= 0) {
    die("Invalid item.");
}

$member_id = $_SESSION['member_id'];

// 1) Make sure this item belongs to the logged-in author
$sql = "SELECT i.item_id
        FROM items i
        JOIN authors a ON i.author_id = a.author_id
        WHERE i.item_id = ? AND a.member_id = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ii", $item_id, $member_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    die("Item not found or you are not the author.");
}
$stmt->close();

// 2) Delete dependent rows in other tables first
// downloads
$stmt = $mysqli->prepare("DELETE FROM downloads WHERE item_id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$stmt->close();

// donations
$stmt = $mysqli->prepare("DELETE FROM donations WHERE item_id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$stmt->close();

// comments
$stmt = $mysqli->prepare("DELETE FROM comments WHERE item_id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$stmt->close();

// plagiarism votes
$stmt = $mysqli->prepare("DELETE FROM plagiarism_votes WHERE item_id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$stmt->close();

// 3) Now it's safe to delete the item itself
$stmt = $mysqli->prepare("DELETE FROM items WHERE item_id = ?");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$stmt->close();

// 4) Go back to My Items
header("Location: my_items.php");
exit;
