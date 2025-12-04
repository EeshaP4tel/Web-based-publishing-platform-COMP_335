<?php
require_once "auth.php";
require_login();
require_once "config.php";

$member_id = $_SESSION['member_id'];
$item_id   = isset($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
$comment   = trim($_POST['comment_text'] ?? '');

if ($item_id <= 0) {
    die("Invalid item.");
}

if ($comment === "") {
    header("Location: item_details.php?item_id=$item_id&error=empty_comment");
    exit;
}

$sql = "INSERT INTO comments (item_id, member_id, comment_text) VALUES (?, ?, ?)";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iis", $item_id, $member_id, $comment);

if ($stmt->execute()) {
    header("Location: item_details.php?item_id=$item_id&msg=comment_posted");
    exit;
} else {
    die("Error posting comment: " . $mysqli->error);
}
?>
