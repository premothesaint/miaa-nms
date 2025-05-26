<?php
require 'db.php'; // Adjust based on your DB connection setup

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['employee_ids']) && is_array($data['employee_ids'])) {
    $ids = implode(",", array_map("intval", $data['employee_ids']));
    
    $sql = "DELETE FROM user_approval WHERE employee_id IN ($ids)";
    
    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid input"]);
}

$conn->close();
?>
