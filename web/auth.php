<?php
require_once 'config.php';

function require_login() {
    if (!isset($_SESSION['member_id'])) {
        header("Location: login.php");
        exit;
    }
}

function require_author() {
    require_login();

    $member_id = $_SESSION['member_id'];
    global $mysqli;

    $stmt = $mysqli->prepare("SELECT author_id FROM authors WHERE member_id = ?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo "<h2>Access denied</h2>";
        echo "<p>You are not registered as an author.</p>";
        exit;
    }
}
?>
