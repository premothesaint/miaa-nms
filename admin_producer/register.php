<?php
require '../db.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST["full_name"]);
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    if ($password !== $confirm_password) {
        die("Passwords do not match!");
    }

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement to insert data
    $sql = "INSERT INTO admin (full_name, username, password) VALUES (?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sss", $full_name, $username, $hashed_password);

        if ($stmt->execute()) {
            echo "Admin registered successfully!";
            header("Location: ../index.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>
