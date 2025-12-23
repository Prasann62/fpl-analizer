<?php
$servername = "localhost";
$username   = "u913997673_prasanna";
$password_db = "Ko%a/2klkcooj]@o";
$dbname     = "u913997673_prasanna";

$conn = new mysqli($servername, $username, $password_db, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = "admin@fplmanager.com";
$sql = "SELECT * FROM signin WHERE email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<h1>Admin User Found</h1>";
    echo "Email: " . $row['email'] . "<br>";
    echo "Password: " . $row['password'] . "<br>";
    echo "Role: [" . $row['role'] . "]<br>"; // Brackets to see whitespace
    echo "Length of Role: " . strlen($row['role']) . "<br>";
} else {
    echo "<h1>Admin User NOT Found</h1>";
    echo "Please run <a href='setup_admin_user.php'>setup_admin_user.php</a> again.";
}
$conn->close();
?>
