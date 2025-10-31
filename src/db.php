<?php
$servername = "bitcot-db-new.cbioyyk4o6pl.ap-south-1.rds.amazonaws.com";
$username = "dbadmin";
$password = "7771905843";  // 👈 replace this
$dbname = "bitcot_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

