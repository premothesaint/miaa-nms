<?php
session_start();
include '../db.php'; // Your database connection file

// Assuming the user is logged in and their username is stored in a session
$username = $_SESSION['username']; 

$sql = "SELECT username FROM miaalocals_users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

echo json_encode($user);
?>
