<?php
session_start();

// If user is already logged in, redirect to the appropriate dashboard
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
} elseif (isset($_SESSION['employee_id'])) {
    header("Location: user/user_manage_locals.php");
    exit();
}

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Local MS</title>
    <link rel="stylesheet" href="CSS/style.css" />
    <link rel="stylesheet" href="CSS/login_form.css" />
    <style>
      .modal-content {
            background-color: #fefefe;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            width: 400px;
            max-width: 90%;
        }
        
        .modal-header {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }
        
        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #333;
        }
        
        .modal-footer {
            margin-top: 20px;
            display: flex;
            justify-content: flex-end;
        }
        
        #savePassword {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        #savePassword:hover {
            background-color: #333;
        }
        
        #savePassword:disabled {
            background-color: #cccccc;
            cursor: not-allowed;
        }
        
        .error-message {
            color: #ff4444;
            font-size: 0.8rem;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    
<div class="login-container">
    <img src="images/login-image/miaa-ms-logo.png" alt="Logo" class="login-logo">
    <form class="login-form" action="login.php" method="POST">

    <?php 
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    ?>
    
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    
    <div class="input-group">
        <input type="text" id="username" name="username" required pattern="[a-zA-Z0-9_@.]+" title="Only letters, numbers, underscores, '@' and '.' are allowed">
        <label for="username">Username</label>
    </div>

    <div class="input-group">
            <input type="password" id="password" name="password" title="Password must be at least 8 characters">
            <label for="password">Password</label>
    </div>

        <button type="submit">Login</button>

        <p class="register-link">
            <a href="user/create_user.php">Create New User</a> | <a href="#" id="forgotPasswordLink">Forgot Password?</a>
        </p>

    </form>
</div>


<!-- Forgot Password Modal -->
<div id="forgotPasswordModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <span class="modal-title">Reset Password</span>
            <span class="close">&times;</span>
        </div>
        <form id="resetPasswordForm">
            <div class="input-group">
                <input type="text" id="employee_id" name="employee_id" required>
                <label for="employee_id">Employee ID</label>
            </div>
            <div class="input-group">
                <input type="password" id="new_password" name="new_password" required>
                <label for="new_password">New Password</label>
            </div>
            <div class="input-group">
                <input type="password" id="confirm_password" name="confirm_password" required>
                <label for="confirm_password">Confirm Password</label>
                <span id="passwordError" class="error-message" style="display: block; font-size: 12px; color: red; height: 14px;"></span>
            </div>
            <div class="modal-footer">
                <button type="submit" id="savePassword" disabled>Save</button>
            </div>
        </form>
    </div>
</div>
<script src="script.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const modal = document.getElementById("forgotPasswordModal");
        const link = document.getElementById("forgotPasswordLink");
        const closeBtn = document.getElementsByClassName("close")[0];
        const newPassword = document.getElementById("new_password");
        const confirmPassword = document.getElementById("confirm_password");
        const passwordError = document.getElementById("passwordError");
        const saveButton = document.getElementById("savePassword");

        link.onclick = function (e) {
            e.preventDefault();
            modal.style.display = "block";
        };

        closeBtn.onclick = function () {
            modal.style.display = "none";
        };

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        };

        function validatePasswords() {
            if (newPassword.value !== confirmPassword.value) {
                passwordError.textContent = "Passwords do not match!";
                saveButton.disabled = true;
            } else {
                passwordError.textContent = "";
                saveButton.disabled = false;
            }
        }

        newPassword.addEventListener("input", validatePasswords);
        confirmPassword.addEventListener("input", validatePasswords);

        document.getElementById("resetPasswordForm").addEventListener("submit", function (e) {
            e.preventDefault();
            if (newPassword.value === confirmPassword.value) {
                alert("Password reset request submitted!");
                modal.style.display = "none";
                this.reset();
                saveButton.disabled = true;
            }
        });
    });

    document.getElementById("resetPasswordForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const employeeId = document.getElementById("employee_id").value;
    const newPassword = document.getElementById("new_password").value;

    fetch("reset_password.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ employee_id: employeeId, new_password: newPassword })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Password reset successfully!");
            document.getElementById("forgotPasswordModal").style.display = "none";
            this.reset();
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error("Error:", error));
});


</script>


</body>
</html>