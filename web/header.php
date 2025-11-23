<?php
require_once __DIR__.'/config.php';
$cn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$cn) { die("DB connect fail: ".mysqli_connect_error()); }
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
  </style>
</head>
<body>
<header>
  <h2 style="margin:0;">CopyForward (COMP 353)</h2>
  <nav>
    <a href="home.php">Home</a>
    <a href="items.php">Items</a>
    <a href="donate.php">Donate</a>
  </nav>
</header>
<div class="container">
