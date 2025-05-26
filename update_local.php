<?php
header('Content-Type: application/json');
$conn = new mysqli('localhost', 'root', '', 'miaa_locals');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["input_id"], $_POST["local"], $_POST["office"], $_POST["contact_name"])) {
        $input_id = $_POST["input_id"];
        $local = $_POST["local"];
        $office = $_POST["office"];
        $contact_name = strtoupper($_POST["contact_name"]); // Convert to uppercase
        $date_edited = date("Y-m-d H:i:s"); // Current timestamp

        // Check if input_id exists in the miaalocals_user table
        $check_stmt = $conn->prepare("SELECT employee_id FROM miaalocals_user WHERE employee_id = ?");
        $check_stmt->bind_param("i", $input_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            echo json_encode(["success" => false, "error" => "Update not allowed. Employee ID exists."]);
            $check_stmt->close();
            $conn->close();
            exit();
        }
        $check_stmt->close();

        // Check if the same local exists for a different office
        $check_duplicate_stmt = $conn->prepare("SELECT input_id FROM miaalocals_user_inputs WHERE local = ? AND office != ?");
        $check_duplicate_stmt->bind_param("ss", $local, $office);
        $check_duplicate_stmt->execute();
        $duplicate_result = $check_duplicate_stmt->get_result();

        if ($duplicate_result->num_rows > 0) {
            echo json_encode(["success" => false, "error" => "Duplicate local found in a different office. Update not allowed."]);
            $check_duplicate_stmt->close();
            $conn->close();
            exit();
        }
        $check_duplicate_stmt->close();

        // Proceed with the update if no duplicate local in a different office
        $stmt = $conn->prepare("UPDATE miaalocals_user_inputs SET local=?, office=?, contact_name=?, date_edited=? WHERE input_id=?");
        $stmt->bind_param("ssssi", $local, $office, $contact_name, $date_edited, $input_id);

        $response = [];
        if ($stmt->execute()) {
            $response["success"] = true;
            $response["date_edited"] = $date_edited;
        } else {
            $response["success"] = false;
            $response["error"] = $stmt->error;
        }

        $stmt->close();
    } else {
        $response["success"] = false;
        $response["error"] = "Missing required fields";
    }

    $conn->close();
    echo json_encode($response);
}
?>
