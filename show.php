<?php
require 'db.php'; // Include your database connection

if (isset($_GET['input_id'])) {
    $input_id = intval($_GET['input_id']);

    $query = "SELECT employee_id, full_name, user_office, date_added, date_edited FROM miaalocals_user_inputs WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $input_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode(["error" => "No data found"]);
    }

    $stmt->close();
    $conn->close();
}
?>
