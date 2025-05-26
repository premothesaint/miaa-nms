<?php
// Database connection
$host = "localhost"; // Change this if needed
$username = "root";  // Change this with your DB username
$password = "";      // Change this with your DB password
$database = "miaa_locals"; // Change this with your actual database name

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed"]));
}

// Get input_id from request
if (isset($_GET['input_id'])) {
    $input_id = intval($_GET['input_id']); // Convert to integer for security
    
    // Prepare the SQL statement
    $stmt = $conn->prepare("SELECT employee_id, full_name, user_office, date_added, date_edited FROM miaalocals_user_inputs WHERE input_id = ?");
    $stmt->bind_param("i", $input_id);
    $stmt->execute();
    
    // Get result
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode(["success" => true, "data" => $row]);
    } else {
        echo json_encode(["success" => false, "message" => "No data found"]);
    }

    // Close connections
    $stmt->close();
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}

$conn->close();
?>
