<?php
$servername = "localhost";
$username   = "u913997673_prasanna";
$password_db = "Ko%a/2klkcooj]@o";
$dbname     = "u913997673_prasanna";

// Create connection
$conn = new mysqli($servername, $username, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin credentials
$admin_name = "Admin";
$admin_email = "admin@fplmanager.com";
$admin_pass = "admin123"; // IN PLAIN TEXT as per existing system (not recommended for production but matches current style)
$role = "admin";

// Check if admin already exists
$check_stmt = $conn->prepare("SELECT email FROM signin WHERE email = ?");
$check_stmt->bind_param("s", $admin_email);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo "Admin user already exists.";
} else {
    // Insert new admin
    $stmt = $conn->prepare("INSERT INTO signin (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $admin_name, $admin_email, $admin_pass, $role);

    if ($stmt->execute()) {
        echo "Admin user created successfully!<br>";
        echo "Email: " . $admin_email . "<br>";
        echo "Password: " . $admin_pass . "<br>";
        echo "Role: " . $role . "<br>";
        echo "<br><strong>Please delete this file after use!</strong>";
    } else {
        echo "Error creating admin user: " . $stmt->error;
    }
    $stmt->close();
}

$check_stmt->close();
$conn->close();
?>
