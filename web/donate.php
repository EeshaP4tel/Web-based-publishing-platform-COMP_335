<?php include __DIR__.'/header.php'; ?>
<h1>Donate</h1>
<p>Pick an item, a charity, and set the split. Charity must be at least 60%.</p>

<?php
$items = mysqli_query($cn, "SELECT item_id, title FROM items ORDER BY title");
$charities = mysqli_query($cn, "SELECT charity_id, charity_name FROM charities ORDER BY charity_name");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $member_id = (int)($_POST['member_id'] ?? 0);
  $item_id = (int)($_POST['item_id'] ?? 0);
  $charity_id = (int)($_POST['charity_id'] ?? 0);
  $amount = (float)($_POST['amount'] ?? 0);
  $pc = (int)($_POST['pct_charity'] ?? 0);
  $pa = (int)($_POST['pct_author'] ?? 0);
  $pf = (int)($_POST['pct_cfp'] ?? 0);

  $errors = [];
  if ($member_id <= 0) $errors[] = "Member id required.";
  if ($item_id <= 0) $errors[] = "Item required.";
  if ($charity_id <= 0) $errors[] = "Charity required.";
  if ($amount <= 0) $errors[] = "Amount must be > 0.";
  if ($pc + $pa + $pf !== 100) $errors[] = "Percents must add up to 100.";
  if ($pc < 60) $errors[] = "Charity must be at least 60%.";

  if (empty($errors)) {
    $stmt = mysqli_prepare($cn, "INSERT INTO donations(member_id,item_id,charity_id,amount,pct_charity,pct_author,pct_cfp)
                                 VALUES (?,?,?,?,?,?,?)");
    mysqli_stmt_bind_param($stmt, "iiidiii", $member_id, $item_id, $charity_id, $amount, $pc, $pa, $pf);
    mysqli_stmt_execute($stmt);
    echo "<p><b>Thank you! Donation saved.</b></p>";
  } else {
    echo "<ul style='color:red;'>";
    foreach ($errors as $e) echo "<li>".htmlspecialchars($e)."</li>";
    echo "</ul>";
  }
}
?>

<form method="post">
  <label>Your member ID:
    <select name="member_id">
      <option value="1">1 (Eesha)</option>
      <option value="2">2 (Bhavya)</option>
      <option value="3">3 (A Razk)</option>
    </select>
  </label>
  <br><br>

  <label>Item:
    <select name="item_id">
      <?php 
      if ($items && mysqli_num_rows($items) > 0) {
          mysqli_data_seek($items, 0); // Reset pointer
          while ($i = mysqli_fetch_assoc($items)) {
              echo "<option value='".(int)$i['item_id']."'>".htmlspecialchars($i['title'])."</option>";
          }
      } else {
          echo "<option value=''>No items available</option>";
      }
      ?>
    </select>
  </label>
  <br><br>

  <label>Charity:
    <select name="charity_id">
      <?php 
      if ($charities && mysqli_num_rows($charities) > 0) {
          mysqli_data_seek($charities, 0); // Reset pointer
          while ($c = mysqli_fetch_assoc($charities)) {
              echo "<option value='".(int)$c['charity_id']."'>".htmlspecialchars($c['charity_name'])."</option>";
          }
      } else {
          echo "<option value=''>No charities available</option>";
      }
      ?>
    </select>
  </label>
  <br><br>

  <label>Amount ($): <input type="number" step="0.01" min="1" name="amount" required></label>
  <br><br>

  <label>Percent to Charity: <input type="number" name="pct_charity" min="0" max="100" value="60"></label><br>
  <label>Percent to Author:  <input type="number" name="pct_author"  min="0" max="100" value="20"></label><br>
  <label>Percent to CFP:     <input type="number" name="pct_cfp"     min="0" max="100" value="20"></label><br><br>

  <button class="btn" type="submit">Donate</button>
</form>

<?php include __DIR__.'/footer.php'; ?>
