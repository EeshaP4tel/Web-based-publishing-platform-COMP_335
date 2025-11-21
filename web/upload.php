<?php
require_once "auth.php";
require_author();

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $desc  = trim($_POST['description']);

    if (!$title) $errors[] = "Title required.";

    if (empty($errors)) {
        $member_id = $_SESSION['member_id'];

        // Get author_id
        $stmt = $mysqli->prepare("SELECT author_id FROM authors WHERE member_id=?");
        $stmt->bind_param("i", $member_id);
        $stmt->execute();
        $stmt->bind_result($author_id);
        $stmt->fetch();
        $stmt->close();

        $stmt2 = $mysqli->prepare("
            INSERT INTO items(author_id, title, description)
            VALUES (?, ?, ?)
        ");
        $stmt2->bind_param("iss", $author_id, $title, $desc);
        $stmt2->execute();
        $stmt2->close();

        $success = "Item added successfully!";
    }
}
?>
<!DOCTYPE html>
<html>
<body>
<h2>Upload Item</h2>

<?php if ($success) echo "<p style='color:green'>$success</p>"; ?>
<?php foreach($errors as $e) echo "<p style='color:red'>$e</p>"; ?>

<form method="POST">
    Title:<br><input name="title"><br><br>
    Description:<br><textarea name="description"></textarea><br><br>
    <button>Upload</button>
</form>

</body>
</html>
