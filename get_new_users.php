<?php
include 'db.php'; // Ensure this points to your correct database connection file

header("Content-Type: application/json");

// Query to count pending user approvals
$sql = "SELECT COUNT(*) AS new_user_count FROM user_approval";
$result = $conn->query($sql);
$row = $result->fetch_assoc();

echo json_encode(["new_user_count" => $row['new_user_count']]);

$conn->close();
?>
