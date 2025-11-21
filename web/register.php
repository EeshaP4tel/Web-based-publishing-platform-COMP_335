<?php
require_once "config.php";

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $org   = trim($_POST['organization'] ?? '');
    $intro_raw = trim($_POST['introducer_id'] ?? '');
    $rec   = trim($_POST['recovery_email'] ?? '');
    $is_author = isset($_POST['is_author']);
    $orcid = trim($_POST['orcid'] ?? '');

    // Basic validation
    if ($name === '') $errors[] = "Name required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email.";
    if ($pass === '') $errors[] = "Password required.";
    if ($is_author && $orcid === '') $errors[] = "ORCID required for author.";

    // Handle introducer_id properly
    $intro_id = null;
    if ($intro_raw !== '') {
        if (!ctype_digit($intro_raw)) {
            $errors[] = "Introducer ID must be a valid numeric member ID.";
        } else {
            $intro_id = (int)$intro_raw;

            // Check that introducer exists
            $stmt = $mysqli->prepare("SELECT member_id FROM members WHERE member_id = ?");
            $stmt->bind_param("i", $intro_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows === 0) {
                $errors[] = "Introducer member ID does not exist.";
            }
            $stmt->close();
        }
    }

    // Check unique email
    if (empty($errors)) {
        $stmt = $mysqli->prepare("SELECT member_id FROM members WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = "Email already exists.";
        }
        $stmt->close();
    }

    if (empty($errors)) {
        // Insert member
        $stmt = $mysqli->prepare("
            INSERT INTO members(name,email,password,organization,introducer_id,recovery_email)
            VALUES (?,?,?,?,?,?)
        ");
        // types: s = string, i = integer
        // name (s), email (s), pass (s), org (s), intro_id (i), rec (s)
        $stmt->bind_param("sssiss", $name, $email, $pass, $org, $intro_id, $rec);

        if ($stmt->execute()) {
            $member_id = $stmt->insert_id;
            $stmt->close();

            // If author, create author row
            if ($is_author) {
                $stmt2 = $mysqli->prepare("INSERT INTO authors(member_id, orcid) VALUES (?, ?)");
                $stmt2->bind_param("is", $member_id, $orcid);
                $stmt2->execute();
                $stmt2->close();
            }

            $_SESSION['member_id'] = $member_id;
            $_SESSION['member_name'] = $name;

            header("Location: profile.php");
            exit;
        } else {
            $errors[] = "Database error: " . $mysqli->error;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<body>

<h2>Register</h2>

<?php foreach ($errors as $e): ?>
    <p style="color:red"><?= htmlspecialchars($e) ?></p>
<?php endforeach; ?>

<form method="POST">
    Name:<br><input name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"><br><br>
    Email:<br><input name="email" type="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"><br><br>
    Password:<br><input name="password" type="password"><br><br>
    Organization:<br><input name="organization" value="<?= htmlspecialchars($_POST['organization'] ?? '') ?>"><br><br>
    Introducer Member ID (optional):<br>
    <input name="introducer_id" value="<?= htmlspecialchars($_POST['introducer_id'] ?? '') ?>"><br><br>
    Recovery Email:<br><input name="recovery_email" type="email" value="<?= htmlspecialchars($_POST['recovery_email'] ?? '') ?>"><br><br>

    <label><input type="checkbox" name="is_author" <?= isset($_POST['is_author']) ? 'checked' : '' ?>> Register as Author</label><br><br>
    ORCID (if author):<br>
    <input name="orcid" value="<?= htmlspecialchars($_POST['orcid'] ?? '') ?>"><br><br>

    <button type="submit">Register</button>
</form>

</body>
</html>
