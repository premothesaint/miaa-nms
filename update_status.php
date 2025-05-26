<?php
include 'db.php'; // Ensure this connects to your database

if (isset($_POST['employee_id']) && isset($_POST['status'])) {
    $employee_id = $_POST['employee_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE miaalocals_user SET status = ? WHERE employee_id = ?");
    $stmt->bind_param("si", $status, $employee_id);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error";
    }
    $stmt->close();
    $conn->close();
}
?>
