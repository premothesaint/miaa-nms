<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['employee_id'])) {
    header("Location: index.php");
    exit();
}

// Auto logout after inactivity (set to 15 minutes)
$inactive = 900; // 900 seconds = 15 minutes
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $inactive) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
$_SESSION['last_activity'] = time(); // Update last activity time

// Get user details
if (isset($_SESSION['admin_id'])) {
    $username = $_SESSION['admin_username'];
    $role = "Admin";
} elseif (isset($_SESSION['employee_id'])) {
    $username = $_SESSION['user_username'];
    $role = "Employee";
} else {
    $username = "Guest";
    $role = "Unknown";
}

?>
<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "miaa_locals");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch users
$sql = "SELECT employee_id, full_name, username, employee_type, user_office, date_created, status FROM miaalocals_user";
$result = $conn->query($sql);

// Check if query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Boxicons CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <title>MIAA-LOCALS MANAGEMENT SYSTEM</title>
    <link rel="stylesheet" href="CSS/style.css" />
    <link rel="stylesheet" href="CSS/manage_users.css" />
    <style>

    .notification-badge {
        background-color: red;
        color: white;
        font-size: 12px;
        font-weight: bold;
        padding: 2px 6px;
        border-radius: 50%;
        margin-left: 8px;
        display: none;
    }
    .datagrid th, .datagrid td {
    border: 1px solid #ddd;
    width: 100px;
    padding: 8px;
    text-align: left;
    white-space: nowrap;
    font-size: x-small;
    }

    .actions a {
    text-decoration: none;
    padding: 5px 10px;
    border-radius: 5px;
    }

    .edit_btn {
    background-color: #007bff;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 3px;
    cursor: pointer;
    transition: background-color 0.3s;
    text-decoration: none;
    }

    .edit_btn:hover {
    background-color: #0056b3;

    }   

    .toggle-btn {
    background-color:rgb(0, 0, 0);
    margin-left: 5px;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 3px;
    cursor: pointer;
    transition: background-color 0.3s;
    text-decoration: none;
    }
    .toggle-btn:hover {
        background-color:rgb(100, 100, 100);
    }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="logo_item">
            <i class="bx bx-menu" id="sidebarOpen"></i>
            <img src="images/miaa-logo.jpg" alt=""> LOCALS MS
        </div>
        <div class="search_bar">
            <input type="text" id="searchInput" placeholder="Search here..." onkeyup="filterTable()" />
        </div>
    </nav>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="menu_content">
            <ul class="menu_items">
                <li class="item">
                    <a href="dashboard.php" class="nav_link">
                        <span class="navlink_icon"><i class='bx bx-grid-alt'></i></span>
                        <span class="navlink">Dashboard</span>
                    </a>
                </li>
                <li class="item">
                    <a href="local_list.php" class="nav_link">
                        <span class="navlink_icon"><i class='bx bx-list-ul'></i></span>
                        <span class="navlink">Local List</span>
                    </a>
                </li>
                <li class="item">
                    <a href="manage_locals.php" class="nav_link">
                        <span class="navlink_icon"><i class='bx bx-edit'></i></span>
                        <span class="navlink">Manage Locals</span>
                    </a>
                </li>
                <li class="item active">
                    <a href="#" class="nav_link">
                        <span class="navlink_icon"><i class='bx bx-user'></i></span>
                        <span class="navlink">Manage Users</span>
                        <span class="submenu_icon"><i class='bx bx-chevron-down'></i></span>
                    </a>
                    <ul class="submenu">
                        <li><a href="user_list.php" class="active">User List</a></li>
                        <li>
                            <a href="user_approval.php">
                                User Approval 
                                <span class="notification-badge" id="userNotification" style="display: none;"></span>
                            </a>
                        </li>

                    </ul>
                </li>
                <li class="item">
                    <a href="index.php" class="nav_link" onclick="confirmLogout(event)">
                        <span class="navlink_icon"><i class='bx bx-log-out'></i></span>
                        <span class="navlink">Log Out</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

<!-- Data Grid -->
<div class="datagrid-container">
    <table class="datagrid">
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Full Name</th>
                <th>Employee Type</th>
                <th>Username</th>
                <th>Office</th>
                <th>Date Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr id="row_<?php echo $row['employee_id']; ?>" data-status="<?php echo $row['status']; ?>">
                <td><?php echo $row['employee_id']; ?></td>
                <td><?php echo $row['full_name']; ?></td>
                <td><?php echo $row['employee_type']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['user_office']; ?></td>
                <td><?php echo $row['date_created']; ?></td>
                <td>
                    <div class="actions">
                        <a href="#" class="edit_btn" onclick="openEditModal(
                            '<?php echo $row['employee_id']; ?>',
                            '<?php echo $row['full_name']; ?>',
                            '<?php echo $row['username']; ?>',
                            '<?php echo $row['employee_type']; ?>',
                            '<?php echo $row['user_office']; ?>'
                        )">Edit</a>
                        
                        <button class="toggle-btn" onclick="toggleStatus('<?php echo $row['employee_id']; ?>')">
                            <?php echo ($row['status'] == 'active') ? 'Deactivate' : 'Activate'; ?>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Edit User Modal -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <h2>Edit User</h2>
        <form id="editUserForm">
            <label>Employee ID:</label>
            <input type="number" name="employee_id" id="edit_employee_id" required readonly>

            <label>Full Name:</label>
            <input type="text" name="full_name" id="edit_full_name" required>

            <label>Username:</label>
            <input type="text" name="username" id="edit_username" required>

            <label>Employee Type:</label>
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


            <label>Office:</label>
            <input list="officeList" name="user_office" id="edit_office" required>

            <datalist id="officeList">
                <?php
                include 'db.php'; // Ensure the database connection is included

                // Fetch office list
                $sql = "SELECT office_name FROM office_list";
                $result = $conn->query($sql);

                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row["office_name"]) . '">';
                    }
                }

                // Close connectionbtn btn-cancel
                $conn->close();
                ?>
            </datalist>


            <div class="modal-buttons">
                <button type="submit" class="btn btn-save">Save</button>
                <button type="button" class="btn btn-cancel" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>


<!-- Modal CSS -->
<style>
  .modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
  }
  .modal-content {
    background: white;
    padding: 20px;
    border-radius: 10px;
    width: 350px;
    text-align: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    animation: fadeIn 0.3s ease-in-out;
  }
  .modal h2 {
    margin-bottom: 15px;
  }
  .modal label {
    display: block;
    margin: 10px 0 5px;
    text-align: left;
  }
  .modal input, .modal select {
    width: 100%;
    padding: 8px;
    border: 1px solid #ccc;
    border-radius: 5px;
  }
  .modal-buttons {
    margin-top: 15px;
    display: flex;
    justify-content: space-between;
  }
  .btn {
    margin-top: 8px;
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
  }
  .btn-save {
    background-color: var(--blue-color);
    color: white;
  }
  .btn-cancel {
    background-color: #ccc;
  }
  .btn-save:hover {
    background-color: var(--grey-color);
  }
  .btn-cancel:hover {
    background-color: #aaa;
  }
</style>


    
    <!-- JavaScript -->
    <script src="script.js"></script>
    <script>
    function openEditModal(employee_id, full_name, username, employee_type, user_office) {
        document.getElementById("edit_employee_id").value = employee_id;
        document.getElementById("edit_full_name").value = full_name;
        document.getElementById("edit_username").value = username;
        document.getElementById("edit_employee_type").value = employee_type;
        document.getElementById("edit_office").value = user_office;

        document.getElementById("editUserModal").style.display = "block";
    }

    function closeModal() {
        document.getElementById("editUserModal").style.display = "none";
    }

    // Restrict employee_id input to integers only (although it's read-only)
    document.getElementById("edit_employee_id").addEventListener("input", function () {
        this.value = this.value.replace(/\D/g, ''); // Remove non-numeric characters
    });

    // Handle form submission
    document.getElementById("editUserForm").addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent default form submission

        let formData = new FormData(this);

        fetch("update_user.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("User updated successfully!");
                location.reload(); // Reload page to reflect changes
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("An error occurred while updating the user.");
        });
    });


    function confirmLogout(event) {
    event.preventDefault(); // Prevent default link behavior
    if (confirm("Are you sure you want to log out?")) {
        window.location.href = "logout.php"; // Redirect if confirmed
    }
}
function fetchNewUserCount() {
        fetch('get_new_users.php')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById("userNotification");
                if (data.new_user_count > 0) {
                    badge.textContent = data.new_user_count;
                    badge.style.display = "inline-block";
                } else {
                    badge.style.display = "none";
                }
            })
            .catch(error => console.error("Error fetching data:", error));
    }

    // Refresh notification count every 10 seconds
    setInterval(fetchNewUserCount, 10000);

    // Initial fetch when page loads
    document.addEventListener("DOMContentLoaded", fetchNewUserCount);

    function filterTable() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll("#tableBody tr");

            rows.forEach(row => {
                let found = false;
                let cells = row.getElementsByTagName("td");

                for (let i = 0; i < cells.length - 1; i++) { // Exclude Actions column
                    let text = cells[i].textContent || cells[i].innerText;
                    if (text.toLowerCase().startsWith(input)) {
                        found = true;
                        break;
                    }
                }

                row.style.display = found ? "" : "none";
            });
        }

        function toggleStatus(employeeId) {
    var row = document.getElementById("row_" + employeeId);
    var currentStatus = row.getAttribute("data-status");
    var newStatus = currentStatus === "active" ? "inactive" : "active";
    var confirmationMessage = newStatus === "active" ? "Are you sure you want to activate this user?" : "Are you sure you want to deactivate this user?";

    if (confirm(confirmationMessage)) {
        // AJAX request to update status
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "update_status.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                row.setAttribute("data-status", newStatus);
                document.querySelector("#row_" + employeeId + " .toggle-btn").innerText = newStatus === "active" ? "Deactivate" : "Activate";

                // Show success message
                alert("User has been " + (newStatus === "active" ? "activated" : "deactivated") + " successfully.");
            }
        };
        xhr.send("employee_id=" + employeeId + "&status=" + newStatus);
    }
}

</script>

<script>
</script>

    
        
</body>
</html>

