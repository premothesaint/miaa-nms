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
include 'db.php';

// Fetch all pending approvals
$result = $conn->query("SELECT * FROM user_approval");
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
        .floating-btn-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: flex;
            gap: 10px;
        }
        .floating-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        .floating-btn:hover {
            background-color: #0056b3;
        }
        .delete-btn {
            background-color: #dc3545;
        }
        .delete-btn:hover {
            background-color: #a71d2a;
        }
        .datagrid td input[type="checkbox"],
        .datagrid th input[type="checkbox"] {
            width: 15px; /* Increase checkbox size */
            height: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: auto;
            cursor: pointer;
        }
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
                        <li><a href="user_list.php">User List</a></li>
                        <li>
                            <a href="user_approval.php" class="active">
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
                    <th><input type="checkbox" id="select-all"></th>
                    <th>Employee ID</th>
                    <th>Full Name</th>
                    <th>Employee Type</th>
                    <th>Username</th>
                    <th>Division</th>
                    <th>Date Added</th>
                </tr>
            </thead>
            <tbody id="tableBody">
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><input type="checkbox" name="user_select[]" value="<?php echo $row['employee_id']; ?>"></td>
                    <td><?php echo $row['employee_id']; ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['employee_type']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['user_office']; ?></td>
                    <td><?php echo $row['date_created']; ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <!-- Floating Buttons -->
    <div class="floating-btn-container">
        <button class="floating-btn" onclick="approveUsers()">
            <i class='bx bx-upload'></i> Approved
        </button>
        <button class="floating-btn delete-btn" onclick="deleteUsers()">
            <i class='bx bx-trash'></i> Delete
        </button>
    </div>
    <!-- JavaScript -->
    <script src="script.js"></script>  
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const selectAllCheckbox = document.getElementById("select-all");
    const rowCheckboxes = document.querySelectorAll("input[name='user_select[]']");

    selectAllCheckbox.addEventListener("change", function () {
        rowCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
    });

    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener("change", function () {
            if (!this.checked) {
                selectAllCheckbox.checked = false;
            } else {
                selectAllCheckbox.checked = [...rowCheckboxes].every(cb => cb.checked);
            }
        });
    });

    function getSelectedUserIds() {
        let selectedUsers = [];
        rowCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedUsers.push(checkbox.value);
            }
        });
        return selectedUsers;
    }

    window.approveUsers = function () {
        let selectedUsers = getSelectedUserIds();
        if (selectedUsers.length === 0) {
            alert("Please select at least one user to approve.");
            return;
        }

        if (confirm("Are you sure you want to approve the selected users?")) {
            fetch("approve_users.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ user_ids: selectedUsers })
            })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                location.reload(); // Refresh the page after approval
            })
            .catch(error => console.error("Error:", error));
        }
    };
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

        function deleteUsers() {
    let selectedUsers = [];
    document.querySelectorAll("input[name='user_select[]']:checked").forEach((checkbox) => {
        selectedUsers.push(checkbox.value);
    });

    if (selectedUsers.length === 0) {
        alert("No users selected for deletion.");
        return;
    }

    if (!confirm("Are you sure you want to delete the selected users?")) {
        return;
    }

    fetch("delete_users.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: JSON.stringify({ employee_ids: selectedUsers }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            selectedUsers.forEach(id => {
                document.querySelector(`input[value='${id}']`).closest("tr").remove();
            });
        } else {
            alert("Failed to delete users.");
        }
    })
    .catch(error => console.error("Error:", error));
}

    </script> 
</body>
</html>
<?php $conn->close(); ?>
