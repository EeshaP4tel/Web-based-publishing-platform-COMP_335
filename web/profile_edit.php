<?php
require_once "auth.php";
require_login();
require_once "config.php";

$id = $_SESSION['member_id'];
$errors = [];

// Load current data
$stmt = $mysqli->prepare("SELECT name, email, organization, recovery_email FROM members WHERE member_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($name, $email, $org, $rec);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $org  = trim($_POST['organization'] ?? '');
    $rec  = trim($_POST['recovery_email'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($name === '') $errors[] = "Name required.";
    if ($rec !== '' && !filter_var($rec, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid recovery email.";
    }

    if (empty($errors)) {
        if ($pass !== '') {
            // update with new password
            $stmt = $mysqli->prepare("
                UPDATE members
                SET name=?, organization=?, recovery_email=?, password=?
                WHERE member_id=?
            ");
            $stmt->bind_param("ssssi", $name, $org, $rec, $pass, $id);
        } else {
            // keep existing password
            $stmt = $mysqli->prepare("
                UPDATE members
                SET name=?, organization=?, recovery_email=?
                WHERE member_id=?
            ");
            $stmt->bind_param("sssi", $name, $org, $rec, $id);
        }

        if ($stmt->execute()) {
            $_SESSION['member_name'] = $name;
            header("Location: profile.php");
            exit;
        } else {
            $errors[] = "Update failed: " . $mysqli->error;
        }
        $stmt->close();
    }
}

include __DIR__.'/header.php';
?>

<h2>Edit Profile</h2>

<?php foreach ($errors as $e): ?>
  <p style="color:red;"><?php echo htmlspecialchars($e); ?></p>
<?php endforeach; ?>

<form method="post">
  Name:<br>
  <input name="name" value="<?php echo htmlspecialchars($name); ?>"><br><br>

  Organization:<br>
  <input name="organization" value="<?php echo htmlspecialchars($org); ?>"><br><br>

  Recovery Email:<br>
  <input name="recovery_email" type="email" value="<?php echo htmlspecialchars($rec); ?>"><br><br>

  New Password (leave blank to keep same):<br>
  <input name="password" type="password"><br><br>

  <button>Save</button>
</form>

<p><a href="profile.php">Back to profile</a></p>

<?php include __DIR__.'/footer.php'; ?>
