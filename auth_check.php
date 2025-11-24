<?php
session_start();
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header("Location: login.php?msg=Please login first&type=error");
    exit;
}
