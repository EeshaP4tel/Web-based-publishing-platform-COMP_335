<?php
require_once __DIR__.'/config.php';
$cn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$cn) { die("DB connect fail: ".mysqli_connect_error()); }

// Check if user is logged in
$is_logged_in = isset($_SESSION['member_id']);
$user_name = $is_logged_in ? $_SESSION['member_name'] : '';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>CopyForward</title>
  <style>
    body { font-family: Arial, sans-serif; margin:0; padding:0; }
    header, footer { background:#f3f3f3; padding:10px 14px; }
    nav a { margin-right:12px; }
    .container { padding:14px; }
    table { border-collapse:collapse; width:100%; }
    th, td { border:1px solid #ddd; padding:8px; }
    th { background:#fafafa; text-align:left; }
    .btn { display:inline-block; padding:6px 10px; border:1px solid #333; text-decoration:none; }
    .user-info { float: right; }
  </style>
</head>
<body>
<header>
  <h2 style="margin:0;">CopyForward (COMP 353)</h2>
  <div class="user-info">
        <?php if ($is_logged_in): ?>
      <a href="profile.php">My Profile</a>
      <?php 
      // Check if user is an author
      $stmt = $mysqli->prepare("SELECT author_id FROM authors WHERE member_id = ?");
      $stmt->bind_param("i", $_SESSION['member_id']);
      $stmt->execute();
      $stmt->store_result();
      if ($stmt->num_rows > 0): ?>
        <a href="upload.php">Upload</a>
        <a href="my_items.php">My Items</a>
      <?php endif;
      $stmt->close();
      ?>
    <?php endif; ?>
  </div>
    <nav>
    <a href="home.php">Home</a>
    <a href="items.php">Items</a>
    <a href="donate.php">Donate</a>
    <a href="committees.php">Committees</a>
    <a href="messages.php">Messages</a>
    <?php if ($is_logged_in): ?>
      <a href="profile.php">My Profile</a>

      <?php if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
        <a href="admin.php">Admin</a>
      <?php endif; ?>

      <?php 
      // Check if user is an author
      $stmt = $mysqli->prepare("SELECT author_id FROM authors WHERE member_id = ?");
      $stmt->bind_param("i", $_SESSION['member_id']);
      $stmt->execute();
      $stmt->store_result();
      if ($stmt->num_rows > 0): ?>
        <a href="upload.php">Upload</a>
        <a href="my_items.php">My Items</a>
      <?php endif;
      $stmt->close();
      ?>
    <?php endif; ?>
  </nav>
</header>
<div class="container">
