<!DOCTYPE html>
<html>
<head>
    <title>Bitcot Task - PHP App</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: auto; padding: 20px; }
        .form-container { background: #f2f2f2; padding: 20px; border-radius: 5px; }
        .user-list { margin-top: 20px; }
        input[type=text], input[type=submit] { width: 100%; padding: 12px; margin: 8px 0; display: inline-block; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        input[type=submit] { background-color: #4CAF50; color: white; cursor: pointer; }
    </style>
</head>
<body>
<h2>User Submission Form</h2>
<div class="form-container">
  <form method="post">
    <label for="name">Name:</label>
    <input type="text" id="name" name="name" placeholder="Your name.." required>
    <input type="submit" name="submit" value="Submit">
  </form>
</div>
<?php
$host = getenv('DB_HOST');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');
$db = getenv('DB_NAME');
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("<h3>Connection failed: " . $conn->connect_error . "</h3>");
}
$conn->query("CREATE TABLE IF NOT EXISTS users (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(50) NOT NULL)");
if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $stmt = $conn->prepare("INSERT INTO users (name) VALUES (?)");
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $stmt->close();
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}
$result = $conn->query("SELECT name FROM users ORDER BY id DESC");
if ($result->num_rows > 0) {
    echo "<div class='user-list'><h3>Submitted Users:</h3><ul>";
    while($row = $result->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($row["name"]) . "</li>";
    }
    echo "</ul></div>";
}
$conn->close();
?>
</body>
</html>