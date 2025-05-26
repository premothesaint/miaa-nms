<?php
include 'db.php'; // Adjust this to your database connection file

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data['user_ids']) || empty($data['user_ids'])) {
    echo json_encode(["message" => "No users selected"]);
    exit;
}

$userIds = implode(",", array_map('intval', $data['user_ids'])); // Secure against SQL injection

$conn->begin_transaction();

try {
    // Update password if employee_id already exists in miaalocals_user
    $updateQuery = "UPDATE miaalocals_user mu
                    JOIN user_approval ua ON mu.employee_id = ua.employee_id
                    SET mu.password = ua.password
                    WHERE mu.employee_id IN ($userIds)";
    $conn->query($updateQuery);

    // Insert only those who do not exist in miaalocals_user
    $insertQuery = "INSERT INTO miaalocals_user (employee_id, full_name, employee_type, username, password, user_office, date_created)
                    SELECT ua.employee_id, ua.full_name, ua.employee_type, ua.username, ua.password, ua.user_office, ua.date_created
                    FROM user_approval ua
                    WHERE ua.employee_id NOT IN (SELECT employee_id FROM miaalocals_user)";
    $conn->query($insertQuery);

    // Delete approved users from user_approval
    $deleteQuery = "DELETE FROM user_approval WHERE employee_id IN ($userIds)";
    $conn->query($deleteQuery);

    $conn->commit();
    echo json_encode(["message" => "Users approved successfully. Existing users had their passwords updated."]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["message" => "Error approving users: " . $e->getMessage()]);
}
?>
