<?php
require_once "auth.php";
require_login();
require_once "config.php";

$member_id    = $_SESSION['member_id'];
$committee_id = isset($_GET['committee_id']) ? (int)$_GET['committee_id'] : 0;

if ($committee_id <= 0) {
    die("Invalid committee.");
}

// Check if already a member of this committee
$stmt = $mysqli->prepare("SELECT 1 FROM committee_members WHERE committee_id=? AND member_id=?");
$stmt->bind_param("ii", $committee_id, $member_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    // Not yet a member → add them
    $stmt->close();
    $stmt2 = $mysqli->prepare("INSERT INTO committee_members(committee_id, member_id) VALUES(?, ?)");
    $stmt2->bind_param("ii", $committee_id, $member_id);
    $stmt2->execute();
    $stmt2->close();
    $msg = "You have been added to this committee.";
} else {
    $stmt->close();
    $msg = "You are already a member of this committee.";
}

// send them back with a message
header("Location: committees.php?msg=" . urlencode($msg));
exit;
