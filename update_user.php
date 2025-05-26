<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "miaa_locals");

// Check database connection
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Handle UPDATE request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (
        isset($_POST['employee_id'], $_POST['full_name'], $_POST['username'], $_POST['employee_type'], $_POST['user_office']) &&
        !empty(trim($_POST['employee_id'])) &&
        !empty(trim($_POST['full_name'])) &&
        !empty(trim($_POST['username'])) &&
        !empty(trim($_POST['employee_type'])) &&
        !empty(trim($_POST['user_office']))
    ) {
        $id = trim($_POST['employee_id']);
        $full_name = trim($_POST['full_name']);
        $username = trim($_POST['username']);
        $employee_type = trim($_POST['employee_type']);
        $user_office = trim($_POST['user_office']);
        $password = isset($_POST['password']) ? trim($_POST['password']) : null;

        // Update `miaalocals_user`
        if ($password) {
            $update_sql = "UPDATE miaalocals_user SET full_name = ?, username = ?, password = ?, employee_type = ?, user_office = ? WHERE employee_id = ?";
        } else {
            $update_sql = "UPDATE miaalocals_user SET full_name = ?, username = ?, employee_type = ?, user_office = ? WHERE employee_id = ?";
        }
        
        $update_stmt = $conn->prepare($update_sql);

        if ($update_stmt) {
            if ($password) {
                $update_stmt->bind_param("sssssi", $full_name, $username, $password, $employee_type, $user_office, $id);
            } else {
                $update_stmt->bind_param("ssssi", $full_name, $username, $employee_type, $user_office, $id);
            }

            if ($update_stmt->execute()) {
                // Update `miaalocals_user_inputs`
                $update_inputs_sql = "UPDATE miaalocals_user_inputs SET full_name = ?, user_office = ? WHERE employee_id = ?";
                $update_inputs_stmt = $conn->prepare($update_inputs_sql);

                if ($update_inputs_stmt) {
                    $update_inputs_stmt->bind_param("ssi", $full_name, $user_office, $id);
                    $update_inputs_stmt->execute();
                    $update_inputs_stmt->close();
                }

                http_response_code(200);
                echo json_encode([
                    "success" => true,
                    "message" => "User updated successfully!",
                    "employee_id" => $id,
                    "full_name" => $full_name,
                    "username" => $username,
                    "employee_type" => $employee_type,
                    "user_office" => $user_office
                ]);
            } else {
                http_response_code(500);
                echo json_encode(["success" => false, "message" => "Error updating user: " . $update_stmt->error]);
            }

            $update_stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Prepare statement failed: " . $conn->error]);
        }
    } else {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Invalid request. Missing fields."]);
    }
}

$conn->close();
?>
