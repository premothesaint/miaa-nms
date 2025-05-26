<?php
session_start();
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $local = isset($_POST['local']) ? $_POST['local'] : '';
    $office = isset($_POST['office']) ? $_POST['office'] : '';
    $contact_name = isset($_POST['contact_name']) ? $_POST['contact_name'] : '';
    $employee_id = isset($_SESSION['employee_id']) ? $_SESSION['employee_id'] : '';

    if (empty($local) || empty($office) || empty($contact_name) || empty($employee_id)) {
        echo "All fields are required.";
        exit;
    }

    // Check if local already exists in ANY office
    $stmt = $conn->prepare("SELECT * FROM miaalocals_user_inputs WHERE local = ?");
    $stmt->bind_param("s", $local);
    $stmt->execute();
    $checkResult = $stmt->get_result();

    if ($checkResult->num_rows > 0) {
        echo "This local number already exists in another office.";
        exit;
    }
    $stmt->close();

    // Get the full name and user_office of the employee
    $stmt = $conn->prepare("SELECT full_name, user_office FROM miaalocals_user WHERE employee_id = ?");
    $stmt->bind_param("s", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $full_name = $row['full_name'];
        $user_office = $row['user_office'];
    } else {
        echo "Employee not found.";
        exit;
    }
    $stmt->close();

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert into miaalocals_user_inputs including user_office
        $stmt = $conn->prepare("INSERT INTO miaalocals_user_inputs (employee_id, local, office, contact_name, full_name, user_office, date_added) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssssss", $employee_id, $local, $office, $contact_name, $full_name, $user_office);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        echo "Local saved successfully!";
    } catch (Exception $e) {
        $conn->rollback();
        echo "Failed to save Local. Please try again.";
    }

    $conn->close();
}
