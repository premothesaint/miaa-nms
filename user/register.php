<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "miaa_locals"; // Change this if needed

$conn = new mysqli($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee_id'];
    $full_name = $_POST['full_name'];
    $employee_type = $_POST['employee_type']; 
    $office = $_POST['office']; 
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $date_created = date("Y-m-d H:i:s");
    
    // Check if employee_id, username, or password already exists
    $checkQuery = "SELECT employee_id, username, password FROM user_approval WHERE employee_id = ? OR username = ?";
    $stmt = $conn->prepare($checkQuery);

    if (!$stmt) {
        die("<script>alert('Error preparing check statement: " . $conn->error . "');</script>");
    }

    $stmt->bind_param("is", $employee_id, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if ($row['employee_id'] == $employee_id) {
            echo "<script>
                alert('Employee ID already exists. Please use a different Employee ID.');
                window.history.back();
            </script>";
            exit();
        }
        if ($row['username'] == $username) {
            echo "<script>
                alert('Username already exists. Please choose a different username.');
                window.history.back();
            </script>";
            exit();
        }
        if (password_verify($_POST['password'], $row['password'])) {
            echo "<script>
                alert('This password is already in use. Please choose a different password.');
                window.history.back();
            </script>";
            exit();
        }
    }
    
    // Insert new user
    $sql = "INSERT INTO user_approval (employee_id, full_name, employee_type, user_office, username, password, date_created) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("<script>alert('Error preparing insert statement: " . $conn->error . "');</script>");
    }

    $stmt->bind_param("issssss", $employee_id, $full_name, $employee_type, $office, $username, $password, $date_created);

    if ($stmt->execute()) {
        echo "<script>
            alert('Registration has been forwarded to the Admin. Please wait for approval.');
            window.location.href = '../index.php';
        </script>";
    } else {
        echo "<script>
            alert('Registration failed. Please try again.');
            window.history.back();
        </script>";
    }

    $stmt->close();
}

$conn->close();
?>
