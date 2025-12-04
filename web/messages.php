<?php
require_once "auth.php";
require_login();
require_once "config.php";
include __DIR__ . "/header.php";

$member_id = $_SESSION['member_id'];

$sql = "
    SELECT m.*, mem.name AS sender_name, i.title AS item_title
    FROM messages m
    JOIN members mem ON mem.member_id = m.sender_id
    JOIN items i ON i.item_id = m.item_id
    WHERE m.receiver_id = ? AND m.is_public = 0
    ORDER BY m.sent_at DESC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $member_id);
$stmt->execute();
$messages = $stmt->get_result();
?>

<h2>Your Private Messages</h2>

<?php while ($msg = $messages->fetch_assoc()): ?>
    <div style="border:1px solid #ddd; padding:12px; margin:10px 0;">
        <strong>From:</strong> <?= htmlspecialchars($msg['sender_name']) ?><br>
        <strong>Item:</strong> <?= htmlspecialchars($msg['item_title']) ?><br><br>

        <?= nl2br(htmlspecialchars($msg['message_text'])) ?><br><br>

        <small>Sent: <?= $msg['sent_at'] ?></small>
    </div>
<?php endwhile; ?>

<?php include __DIR__ . "/footer.php"; ?>
