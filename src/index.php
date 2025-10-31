<?php include 'db.php'; ?>
<html>
<body>
<h2>Simple PHP Form</h2>
<form action="submit.php" method="POST">
Name: <input type="text" name="name" required><br>
Email: <input type="email" name="email" required><br>
<input type="submit" value="Submit">
</form>
<hr>
<h3>Submitted Data:</h3>
<ul>
<?php
$result = $conn->query("SELECT name, email, created_at FROM users ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
echo '<li>'.$row['name'].' - '.$row['email'].' ('.$row['created_at'].')</li>';
}
?>
</ul>
</body>
</html>