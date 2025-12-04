<?php
require_once "auth.php";
require_login();
require_once "config.php";
include __DIR__ . "/header.php";

/* FIX: Accept both ?item_id= and ?id= */
$item_id = 0;

if (isset($_GET['item_id'])) {
    $item_id = (int)$_GET['item_id'];
} elseif (isset($_GET['id'])) {
    $item_id = (int)$_GET['id'];
}

if ($item_id <= 0) {
    echo "<p>Invalid item.</p>";
    include __DIR__.'/footer.php';
    exit;
}

$member_id = $_SESSION['member_id'];

/* Fetch item + author */
$sql = "
    SELECT i.item_id, i.title, i.description,
           a.member_id AS author_member_id,
           m.name AS author_name
    FROM items i
    JOIN authors a ON i.author_id = a.author_id
    JOIN members m ON a.member_id = m.member_id
    WHERE i.item_id = ?
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    echo "<p>Item not found.</p>";
    include __DIR__ . "/footer.php";
    exit;
}

$is_author = ($member_id == $item['author_member_id']);
?>

<h2><?= htmlspecialchars($item['title']); ?></h2>
<p><strong>Author:</strong> <?= htmlspecialchars($item['author_name']); ?></p>
<p><?= nl2br(htmlspecialchars($item['description'])); ?></p>

<hr>
<h3>Comments</h3>

<?php
/* Load comments */
$sql = "
    SELECT c.comment_id, c.comment_text, c.comment_date,
           m.name AS commenter_name, c.member_id AS commenter_id
    FROM comments c
    JOIN members m ON m.member_id = c.member_id
    WHERE c.item_id = ?
    ORDER BY c.comment_date ASC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$comments = $stmt->get_result();

while ($c = $comments->fetch_assoc()):
?>
    <div style="border:1px solid #ccc; padding:10px; margin:10px 0;">
        <strong><?= htmlspecialchars($c['commenter_name']) ?></strong>
        <small>(<?= $c['comment_date'] ?>)</small>

        <p><?= nl2br(htmlspecialchars($c['comment_text'])) ?></p>

        <!-- PUBLIC REPLIES -->
        <?php
        $sql = "
            SELECT m.message_text, m.sent_at, mem.name AS sender_name
            FROM messages m
            JOIN members mem ON mem.member_id = m.sender_id
            WHERE m.comment_id = ? AND m.is_public = 1
            ORDER BY m.sent_at ASC
        ";

        $rstmt = $mysqli->prepare($sql);
        $rstmt->bind_param("i", $c['comment_id']);
        $rstmt->execute();
        $replies = $rstmt->get_result();

        while ($r = $replies->fetch_assoc()):
        ?>
            <div style="margin-left:25px; background:#f4f4f4; padding:8px;">
                <strong><?= htmlspecialchars($r['sender_name']) ?> (Author Reply):</strong><br>
                <?= nl2br(htmlspecialchars($r['message_text'])) ?><br>
                <small><?= $r['sent_at'] ?></small>
            </div>
        <?php endwhile; ?>

        <!-- AUTHOR REPLY -->
        <?php if ($is_author): ?>
            <form method="POST" action="send_message.php" style="margin-top:10px;">
                <input type="hidden" name="comment_id" value="<?= $c['comment_id'] ?>">
                <input type="hidden" name="receiver_id" value="<?= $c['commenter_id'] ?>">
                <input type="hidden" name="item_id" value="<?= $item_id ?>">

                <textarea name="message_text" required
                          placeholder="Write your reply..."
                          style="width:100%; height:60px;"></textarea><br>

                <label>
                    <input type="checkbox" name="is_public" value="1"> Make public
                </label><br>

                <button type="submit">Send Reply</button>
            </form>
        <?php endif; ?>
    </div>
<?php endwhile; ?>

<hr>
<h3>Add a Comment</h3>

<form method="POST" action="post_comment.php">
    <input type="hidden" name="item_id" value="<?= $item_id; ?>">
    <textarea name="comment_text" required style="width:100%; height:80px;"></textarea><br>
    <button type="submit">Post Comment</button>
</form>

<?php include __DIR__ . "/footer.php"; ?>
