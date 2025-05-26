<?php
include 'db.php'; // Include your database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['employee_id'])) {
    $employee_id = $_POST['employee_id'];
    
    $stmt = $conn->prepare("UPDATE miaalocals_user SET status = 'inactive' WHERE employee_id = ?");
    $stmt->bind_param("s", $employee_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
?>
