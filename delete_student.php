<?php
require_once "config.php";
require_once "auth_check.php";

// Validate ID
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    $msg  = "Invalid student ID.";
    $type = "error";
    header("Location: index.php?msg=" . urlencode($msg) . "&type=$type");
    exit;
}

$id = (int) $_GET['id'];

// Prepare delete query
$stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
if (!$stmt) {
    $msg  = "Database error: could not prepare delete statement.";
    $type = "error";
    header("Location: index.php?msg=" . urlencode($msg) . "&type=$type");
    exit;
}

$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    $msg  = "Student deleted successfully.";
    $type = "success";
} else {
    $msg  = "Failed to delete student. Please try again.";
    $type = "error";
}

$stmt->close();

// Redirect back to main page
header("Location: index.php?msg=" . urlencode($msg) . "&type=$type");
exit;
