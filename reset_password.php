<?php
header("Content-Type: application/json");
$conn = new mysqli("localhost", "root", "", "miaa_locals");

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed!"]));
}

$data = json_decode(file_get_contents("php://input"), true);
$employee_id = $data['employee_id'];
$new_password = password_hash($data['new_password'], PASSWORD_BCRYPT); // Secure password hashing

// Step 1: Check if employee exists in miaalocals_user
$checkQuery = $conn->prepare("SELECT * FROM miaalocals_user WHERE employee_id = ?");
$checkQuery->bind_param("s", $employee_id);
$checkQuery->execute();
$result = $checkQuery->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $full_name = $row['full_name'];
    $employee_type = $row['employee_type'];
    $user_office = $row['user_office'];
    $username = $row['username'];
    $date_created = $row['date_created'];

    // Step 2: Check if employee_id already exists in user_approval
    $checkApprovalQuery = $conn->prepare("SELECT employee_id FROM user_approval WHERE employee_id = ?");
    $checkApprovalQuery->bind_param("s", $employee_id);
    $checkApprovalQuery->execute();
    $approvalResult = $checkApprovalQuery->get_result();

    if ($approvalResult->num_rows > 0) {
        // Step 3: Update existing record in user_approval
        $updateQuery = $conn->prepare("UPDATE user_approval SET password = ? WHERE employee_id = ?");
        $updateQuery->bind_param("ss", $new_password, $employee_id);
        
        if ($updateQuery->execute()) {
            echo json_encode(["success" => true, "message" => "Password updated successfully, please wait for the admin approval."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error updating password"]);
        }
    } else {
        // Step 4: Insert new record into user_approval
        $insertQuery = $conn->prepare("INSERT INTO user_approval (employee_id, full_name, employee_type, user_office, username, password, date_created) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertQuery->bind_param("sssssss", $employee_id, $full_name, $employee_type, $user_office, $username, $new_password, $date_created);
        
        if ($insertQuery->execute()) {
            echo json_encode(["success" => true, "message" => "New record created and password set"]);
        } else {
            echo json_encode(["success" => false, "message" => "Error inserting new record"]);
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "Employee ID not found"]);
}

$conn->close();
?>

