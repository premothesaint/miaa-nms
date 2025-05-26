<?php
session_start();

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// Include database connection
require_once 'db.php';

// Fallback for random_bytes() if PHP < 7.0
if (!function_exists('random_bytes')) {
    function random_bytes($length) {
        $bytes = '';
        for ($i = 0; $i < $length; $i++) {
            $bytes .= chr(mt_rand(0, 255));
        }
        return $bytes;
    }
}

// Validate CSRF token if it's a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Invalid CSRF token");
    }

    // Input validation and sanitization - PHP 5.x compatible version
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Validate username format (alphanumeric, underscore, @ and .)
    if (!preg_match('/^[a-zA-Z0-9_@.]+$/', $username)) {
        error_log("Invalid username format attempt: " . $username);
        sendErrorResponse("Invalid username format");
    }

    // Validate password is not empty
    if (empty($password)) {
        sendErrorResponse("Password cannot be empty");
    }

    // Check in the admin table first
    $admin = authenticateUser($conn, 'admin', $username, $password);
    if ($admin) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        sendSuccessResponse('dashboard.php', "Login successful! Welcome, {$admin['username']}");
    }

    // Check in the miaalocals_user table
    $user = authenticateUser($conn, 'miaalocals_user', $username, $password);
    if ($user) {
        // Check if user is inactive
        if ($user['status'] === "inactive") {
            sendErrorResponse("This user is deactivated. Please contact the admin for assistance.");
        }

        $_SESSION['employee_id'] = $user['employee_id'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['user_office'] = $user['user_office'];
        
        // Regenerate session ID to prevent session fixation
        session_regenerate_id(true);
        
        sendSuccessResponse('user/user_manage_locals.php', "Login successful! Welcome, {$user['full_name']}");
    }

    // If we get here, login failed
    // Use generic error message to prevent username enumeration
    error_log("Failed login attempt for username: " . $username);
    sendErrorResponse("Invalid username or password");
}

/**
 * Authenticate a user against a specific table
 */
function authenticateUser($conn, $table, $username, $password) {
    $sql = "SELECT * FROM $table WHERE username = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }
    
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $stmt->close();
            return $row;
        }
    }
    
    $stmt->close();
    return false;
}

/**
 * Send error response with alert and redirect
 */
function sendErrorResponse($message) {
    echo "<script>
        alert('" . addslashes($message) . "');
        window.location='index.php';
    </script>";
    exit();
}

/**
 * Send success response with alert and redirect
 */
function sendSuccessResponse($location, $message) {
    echo "<script>
        alert('" . addslashes($message) . "');
        window.location='" . htmlspecialchars($location, ENT_QUOTES) . "';
    </script>";
    exit();
}