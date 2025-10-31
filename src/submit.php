<?php
include 'db.php';
$name = $_POST['name'];
$email = $_POST['email'];
$conn->query("INSERT INTO users (name,email) VALUES ('$name','$email')");
header('Location: /');
?>