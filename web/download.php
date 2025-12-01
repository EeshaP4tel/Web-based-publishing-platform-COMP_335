<?php
require __DIR__.'/config.php';

// user must be logged in
if (!isset($_SESSION['member_id'])) {
    die("You must be logged in to download items.");
}

// DB connection 
$cn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$cn) { die("DB connect fail: ".mysqli_connect_error()); }

$item_id = isset($_GET['item_id']) ? (int)$_GET['item_id'] : 0;
if ($item_id <= 0) { die("Invalid item id."); }

$member_id = (int)$_SESSION['member_id'];

//DOWNLOAD LIMIT RULES

// time windows
$one_year_ago   = date('Y-m-d H:i:s', strtotime('-1 year'));
$one_day_ago    = date('Y-m-d H:i:s', strtotime('-1 day'));
$seven_days_ago = date('Y-m-d H:i:s', strtotime('-7 days'));

// Has this member donated in the last year
$stmt = mysqli_prepare($cn,
    "SELECT COUNT(*)
     FROM donations
     WHERE member_id=? AND donation_date >= ?"
);
mysqli_stmt_bind_param($stmt, "is", $member_id, $one_year_ago);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $don_count);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Depending on donations, choose daily vs weekly limit
if ($don_count > 0) {
    // donated in last year so 1 download per day
    $stmt = mysqli_prepare($cn,
        "SELECT COUNT(*)
         FROM downloads
         WHERE member_id=? AND download_datetime >= ?"
    );
    mysqli_stmt_bind_param($stmt, "is", $member_id, $one_day_ago);
} else {
    // no donation in last year so 1 download per 7 days
    $stmt = mysqli_prepare($cn,
        "SELECT COUNT(*)
         FROM downloads
         WHERE member_id=? AND download_datetime >= ?"
    );
    mysqli_stmt_bind_param($stmt, "is", $member_id, $seven_days_ago);
}

mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $dl_count);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

//  block
if ($dl_count >= 1) {
    echo "<p>You have reached your download limit. Please try again later.</p>";
    echo "<p><a href='item_details.php?id={$item_id}'>&laquo; back</a></p>";
    mysqli_close($cn);
    exit;
}

//RECORD

$stmt = mysqli_prepare($cn, "INSERT INTO downloads(member_id,item_id) VALUES (?,?)");
mysqli_stmt_bind_param($stmt, "ii", $member_id, $item_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);


echo "<p>Download recorded for item #".(int)$item_id." (pretend file downloaded).</p>";
echo "<p><a href='item_details.php?id=".(int)$item_id."'>&laquo; back</a></p>";

mysqli_close($cn);
