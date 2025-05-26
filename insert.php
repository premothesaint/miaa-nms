<?php
include 'db.php'; // Ensure this file contains the correct database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $local = $_POST['local'];
    $office = $_POST['office'];
    $contact_name = $_POST['contact_name'];

    // First, check if the office exists in your offices table
    // Assuming you have a table named 'offices' with an 'office_name' column
    $check_office_sql = "SELECT COUNT(*) FROM office_list WHERE office_name = ?";
    $check_office_stmt = $conn->prepare($check_office_sql);
    if (!$check_office_stmt) {
        die("Prepare failed for check_office_stmt: " . $conn->error);
    }
    $check_office_stmt->bind_param("s", $office);
    $check_office_stmt->execute();
    $check_office_stmt->bind_result($office_count);
    $check_office_stmt->fetch();
    $check_office_stmt->close();

    if ($office_count == 0) {
        echo "Invalid office! This office does not exist in our records.";
        $conn->close();
        exit();
    }

    // Check if contact_name already exists in the same office
    $check_sql = "SELECT COUNT(*) FROM miaalocals_user_inputs WHERE contact_name = ? AND office = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $contact_name, $office);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        echo "Contact name already exists in this office!";
        $conn->close();
        exit();
    }

    // Check if the same local exists in any office
    $check_local_sql = "SELECT COUNT(*) FROM miaalocals_user_inputs WHERE local = ?";
    $check_local_stmt = $conn->prepare($check_local_sql);
    $check_local_stmt->bind_param("s", $local);
    $check_local_stmt->execute();
    $check_local_stmt->bind_result($local_count);
    $check_local_stmt->fetch();
    $check_local_stmt->close();

    if ($local_count > 0) {
        echo "Local number already exists in another office!";
        $conn->close();
        exit();
    }

    // Insert new record into miaalocals_user_inputs with employee_id and full_name
    $sql = "INSERT INTO miaalocals_user_inputs (local, office, contact_name, employee_id, full_name) 
            VALUES (?, ?, ?, 'ADMIN', 'ADMIN')";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error); // Debugging output
    }

    $stmt->bind_param("sss", $local, $office, $contact_name);
    
    if ($stmt->execute()) {
        echo "New record inserted successfully!";
    } else {
        echo "Error inserting into miaalocals_user_inputs: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>