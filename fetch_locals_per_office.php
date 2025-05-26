<?php
header("Content-Type: application/json");
include 'db.php';

$sql = "SELECT office, COUNT(*) AS count FROM miaalocals_user_inputs GROUP BY office";
$result = $conn->query($sql);

$localsPerOffice = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

echo json_encode($localsPerOffice);
?>
