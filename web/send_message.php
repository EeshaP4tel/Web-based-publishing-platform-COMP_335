<?php
require_once "auth.php";
require_login();
require_once "config.php";

$sender_id  = $_SESSION['member_id'];
$receiver_id = (int)$_POST['receiver_id'];
$item_id     = (int)$_POST['item_id'];
$comment_id  = (int)$_POST['comment_id'];
$message_text = trim($_POST['message_text']);
$is_public    = isset($_POST['is_public']) ? 1 : 0;

if ($message_text === "") {
    header("Location: item_details.php?item_id=$item_id&error=empty");
    exit;
}

$sql = "
    INSERT INTO messages (sender_id, receiver_id, item_id, comment_id, message_text, is_public)
    VALUES (?, ?, ?, ?, ?, ?)
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("iiiisi", $sender_id, $receiver_id, $item_id, $comment_id, $message_text, $is_public);
$stmt->execute();

header("Location: item_details.php?item_id=$item_id&msg=sent");
exit;
