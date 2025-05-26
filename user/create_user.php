<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "miaa_locals");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New User</title>
    <link rel="stylesheet" href="CSS/style.css" />
    <link rel="stylesheet" href="CSS/login_form.css" />
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap");

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Poppins", sans-serif;
        }

        :root {
            --white-color: #fff;
            --blue-color: #000000;
            --grey-color: #707070;
            --grey-color-light: #aaa;
        }

        body {
            background-color: #e7f2fd;
            transition: all 0.5s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        body.dark {
            background-color: #333;
            --white-color: #333;
            --blue-color: #fff;
            --grey-color: #f2f2f2;
            --grey-color-light: #aaa;
        }

        .login-container {
            background: var(--white-color);
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 280px;
            text-align: center;
        }

        .login-form h2 {
            margin-bottom: 4rem;
            color: var(--blue-color);
        }

        .input-group {
            position: relative;
            margin-bottom: 1rem;
        }

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--grey-color-light);
            border-radius: 4px;
            outline: none;
            font-size: 0.9rem;
            transition: 0.3s;
        }

        .input-group label {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: var(--grey-color);
            font-size: 0.8rem;
            pointer-events: none;
            transition: 0.3s;
        }

        .input-group input:focus,
        .input-group input:valid,
        .input-group select:focus {
            border-color: var(--blue-color);
        }

        .input-group input:focus + label,
        .input-group input:valid + label {
            top: 8px;
            font-size: 0.7rem;
            color: var(--blue-color);
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: var(--blue-color);
            color: var(--white-color);
            border: none;
            border-radius: 4px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            opacity: 0.8;
        }

        button:disabled {
            background-color: var(--grey-color-light);
            cursor: not-allowed;
        }

        .register-link {
            margin-top: 0.8rem;
            font-size: 0.8rem;
            color: var(--grey-color);
        }

        .register-link a {
            color: var(--blue-color);
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .terms {
            display: flex;
            align-items: center;
            font-size: 0.8rem;
            margin: 0.8rem 0;
        }

        .login-logo {
            display: block;
            width: 160px;
            margin: 0 auto 1rem;
        }
    </style>
</head>
<body>
<div class="login-container">
    <form class="login-form" action="register.php" method="POST">
        <img src="../images/login-image/miaa-ms-logo.png" alt="Logo" class="login-logo">
        <div class="input-group">
            <input type="number" id="employee_id" name="employee_id" required>
            <label for="employee_id">Employee ID</label>
        </div>
        <div class="input-group">
            <input type="text" id="full_name" name="full_name" required>
            <label for="full_name">Full Name</label>
        </div>
        <div class="input-group">
        <select name="employee_type" id="edit_employee_type" required>
            <option value="" disabled selected>Select Employee Type</option>
                <?php
                // Fetch employee types
                $sql = "SELECT type_name FROM employee_type";
                $result = $conn->query($sql);

                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row["type_name"]) . '">' . htmlspecialchars($row["type_name"]) . '</option>';
                    }
                } else {
                    echo '<option value="">Error loading employee types</option>';
                }
                ?>
            </select>
        </div>
        <div class="input-group">   
        <select name="office" id="edit_office" required>
            <option value="">Select Office</option>
                <?php
                // Fetch office list
                $sql = "SELECT office_name FROM office_list";
                $result = $conn->query($sql);

                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row["office_name"]) . '">' . htmlspecialchars($row["office_name"]) . '</option>';
                    }
                } else {
                    echo '<option value="">Error loading offices</option>';
                }

                // Close connection
                $conn->close();
                ?>
            </select>
        </div>
        <div class="input-group">
            <input type="text" id="username" name="username" required>
            <label for="username">Username</label>
        </div>
        <div class="input-group">
        <input type="password" id="password" name="password" required>
        <label for="password">Password</label>
    </div>
    <div class="input-group">
        <input type="password" id="confirm_password" name="confirm_password" required>
        <label for="confirm_password">Confirm Password</label>
    </div>
    <p id="passwordError" style="color: red; font-size: 12px; margin: 5px 0; display: none;">Passwords do not match!</p>

    <div class="terms">
        <input type="checkbox" id="terms" required>
        <label for="terms">&nbsp &nbsp &nbsp Confirm and continue</a></label>
    </div>
    <button type="submit" id="registerBtn" disabled>Register</button>
    <p class="register-link">
        <a href="../index.php">Back to Login</a>
    </p>

    <script>
        function validateForm() {
            let password = document.getElementById("password").value;
            let confirmPassword = document.getElementById("confirm_password").value;
            let termsChecked = document.getElementById("terms").checked;
            let errorText = document.getElementById("passwordError");
            let registerBtn = document.getElementById("registerBtn");

            if (password !== confirmPassword) {
                errorText.style.display = "block";
                registerBtn.disabled = true;
            } else {
                errorText.style.display = "none";
                registerBtn.disabled = !termsChecked;
            }
        }

        document.getElementById("password").addEventListener("input", validateForm);
        document.getElementById("confirm_password").addEventListener("input", validateForm);
        document.getElementById("terms").addEventListener("change", validateForm);
    </script>

</body>
</html>
