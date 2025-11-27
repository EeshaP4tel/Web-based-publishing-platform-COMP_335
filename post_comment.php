<?php
require_once __DIR__.'/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_id = (int)($_POST['item_id'] ?? 0);
    $member_id = (int)($_POST['member_id'] ?? 0);
    $comment_text = trim($_POST['comment_text'] ?? '');

    $errors = [];
    
    if ($item_id <= 0) $errors[] = "Invalid item.";
    if ($member_id <= 0) $errors[] = "Please select your member ID.";
    if (empty($comment_text)) $errors[] = "Comment text is required.";

    if (empty($errors)) {
        $stmt = $mysqli->prepare("INSERT INTO comments (item_id, member_id, comment_text) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $item_id, $member_id, $comment_text);
        
        if ($stmt->execute()) {
            header("Location: item_details.php?id=" . $item_id . "&success=1");
            exit;
        } else {
            $errors[] = "Failed to post comment: " . $mysqli->error;
        }
        $stmt->close();
    }
    
    // If there are errors, show them and redirect back
    if (!empty($errors)) {
        $_SESSION['comment_errors'] = $errors;
        header("Location: item_details.php?id=" . $item_id);
        exit;
    }
} else {
    header("Location: home.php");
    exit;
}
?>
