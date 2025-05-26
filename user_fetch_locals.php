<?php
session_start();
include 'db.php';

$employee_id = $_SESSION['employee_id'];
$stmt = $conn->prepare("SELECT * FROM miaalocals_user_inputs WHERE employee_id = ?");
$stmt->bind_param("s", $employee_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "No matching records found for employee_id: $employee_id"]);
    exit();
}


$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);

$stmt->close();
$conn->close();
?>
