<?php
header("Content-Type: application/json");
include 'db.php';

$sql = "SELECT COUNT(*) AS total FROM miaalocals_user";
$result = $conn->query($sql);

$totalUsers = $result ? $result->fetch_assoc() : ["total" => 0];

echo json_encode($totalUsers);
?>
