<?php
// config.php
$host = "localhost";
$user = "root";       // default XAMPP user
$pass = "";           // default XAMPP password is empty
$dbname = "cse445_student_db";

$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Set character set
$conn->set_charset("utf8");
?>
