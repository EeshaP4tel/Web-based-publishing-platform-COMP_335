<?php include __DIR__.'/header.php'; ?>

<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { echo "<p>Invalid item id.</p>"; include __DIR__.'/footer.php'; exit; }

$q = "SELECT i.item_id, i.title, i.description, i.upload_date, m.name AS author_name
      FROM items i
      JOIN authors a ON i.author_id=a.author_id
      JOIN members m ON a.member_id=m.member_id
      WHERE i.item_id={$id}";
$info = mysqli_query($cn, $q);
$item = $info ? mysqli_fetch_assoc($info) : null;

if (!$item) {
  echo "<p>Item not found.</p>";
  include __DIR__.'/footer.php'; exit;
}
?>

<h1><?php echo htmlspecialchars($item['title']); ?></h1>
<p><b>Author:</b> <?php echo htmlspecialchars($item['author_name']); ?></p>
<p><b>Uploaded:</b> <?php echo htmlspecialchars($item['upload_date']); ?></p>
<p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>

<p>
  <a class="btn" href="download.php?item_id=<?php echo (int)$item['item_id']; ?>">Download</a>
</p>

<h2>Comments</h2>
<?php
$cs = mysqli_query($cn, "SELECT c.comment_text, c.comment_date, m.name
                         FROM comments c
                         JOIN members m ON c.member_id=m.member_id
                         WHERE c.item_id={$id}
                         ORDER BY c.comment_date DESC");
if ($cs && mysqli_num_rows($cs) > 0) {
  echo "<ul>";
  while ($c = mysqli_fetch_assoc($cs)) {
    echo "<li><b>".htmlspecialchars($c['name'])."</b> (".htmlspecialchars($c['comment_date'])."): ".
         htmlspecialchars($c['comment_text'])."</li>";
  }
  echo "</ul>";
} else {
  echo "<p>No comments yet.</p>";
}
?>

<h3>Add a comment</h3>
<?php
// Show success message
if (isset($_GET['success'])) {
    echo "<p style='color:green;'>Comment posted successfully!</p>";
}

// Show errors from post_comment.php
if (isset($_SESSION['comment_errors'])) {
    echo "<ul style='color:red;'>";
    foreach ($_SESSION['comment_errors'] as $error) {
        echo "<li>" . htmlspecialchars($error) . "</li>";
    }
    echo "</ul>";
    unset($_SESSION['comment_errors']); // Clear errors after displaying
}
?>

<form method="post" action="post_comment.php">
  <input type="hidden" name="item_id" value="<?php echo (int)$item['item_id']; ?>">
  
  <?php if (isset($_SESSION['member_id'])): ?>
    <input type="hidden" name="member_id" value="<?php echo (int)$_SESSION['member_id']; ?>">
    <p><strong>Posting as:</strong> <?php echo htmlspecialchars($_SESSION['member_name']); ?></p>
  <?php else: ?>
    <label>Your member ID:
      <select name="member_id" required>
        <option value="1">1 (Eesha)</option>
        <option value="2">2 (Bhavya)</option>
        <option value="3">3 (A Razk)</option>
      </select>
    </label><br><br>
  <?php endif; ?>
  
  <label>Comment:<br>
    <textarea name="comment_text" rows="3" cols="50" required></textarea>
  </label><br><br>
  <button class="btn" type="submit">Post Comment</button>
</form>

<?php include __DIR__.'/footer.php'; ?>
