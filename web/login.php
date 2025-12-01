<?php
require_once "config.php";

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $pass  = $_POST['password'];

    $stmt = $mysqli->prepare("
        SELECT member_id, name, password, is_admin
        FROM members 
        WHERE email = ?
    ");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->bind_result($id, $name, $dbpass, $is_admin);
$stmt->fetch();
$stmt->close();

if ($id && $pass === $dbpass) {
    $_SESSION['member_id']    = $id;
    $_SESSION['member_name']  = $name;
    $_SESSION['is_admin']     = (int)$is_admin;
    header("Location: profile.php");
    exit;
} else {
    $errors[] = "Invalid login.";
}

}
?>
<!DOCTYPE html>
<html>
<body>

<h2>Login</h2>

<?php foreach($errors as $e) echo "<p style='color:red'>$e</p>"; ?>

<form method="POST">
    Email:<br><input name="email"><br><br>
    Password:<br><input name="password" type="password"><br><br>

    <button>Login</button>
</form>

</body>
</html>
