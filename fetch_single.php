<?php
include "db.php"; // Adjust as needed

$id = $_GET['id'];
$sql = "SELECT * FROM miaalocals_user_inputs WHERE input_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode([]);
}

$stmt->close();
$conn->close();
?>
