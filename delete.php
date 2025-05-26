<?php
$conn = new mysqli("localhost", "root", "", "miaa_locals");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if ID is set
if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("DELETE FROM miaalocals_user_inputs WHERE input_id = ?");
    
    // Check if the statement was prepared successfully
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    // Bind parameter
    $stmt->bind_param("i", $id);

    // Execute and check if the query was successful
    if ($stmt->execute()) {
        echo "Record deleted successfully.";
    } else {
        echo "Error deleting record: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>
