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
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Boxicons CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <title>MIAA-LOCALS MANAGEMENT SYSTEM</title>
    <link rel="stylesheet" href="CSS/style.css" />
    <link rel="stylesheet" href="CSS/local_list.css" />
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
    </style>
  </head>
  <body>
    <!-- navbar -->
    <nav class="navbar">
      <div class="logo_item">
        <i class="bx bx-menu" id="sidebarOpen"></i>
        <img src="images/miaa-logo.jpg" alt=""></i>LOCALS MS

      </div>

      <div class="search_bar">
            <input type="text" id="searchInput" placeholder="Search here..." onkeyup="filterTable()" />
      </div>

    </nav>

    <!-- sidebar -->
    <nav class="sidebar">
      <div class="menu_content">
        <ul class="menu_items">
          <div class="menu_title menu_editor"></div>
          <!-- duplicate these li tag if you want to add or remove navlink only -->
          <!-- Start -->
          <li class="item  active">
            <a href="dashboard.php" class="nav_link">
              <span class="navlink_icon">
                <i class='bx bx-grid-alt' ></i>
              </span>
              <span class="navlink">Dashboard</span>
            </a>
          </li>
          <!-- End -->

          <li class="item" >
            <a href="local_list.php" class="nav_link">
              <span class="navlink_icon">
                <i class='bx bx-list-ul'></i>
              </span>
              <span class="navlink">Local List</span>
            </a>
          </li>
          <li class="item">
            <a href="manage_locals.php" class="nav_link">
              <span class="navlink_icon">
                <i class='bx bx-edit'></i>
              </span>
              <span class="navlink">Manage Locals</span>
            </a>
          </li>
          <li class="item">
            <a href="user_list.php" class="nav_link">
              <span class="navlink_icon">
              <i class='bx bx-user'></i>
              </span>
              <span class="navlink">Manage Users</span>
                <span class="notification-badge" id="userNotification" style="display: none;"></span>

            </a>
          </li>
          
          <li class="item">
            <a href="logout.php" class="nav_link" onclick="confirmLogout(event)">
              <span class="navlink_icon">
                <i class='bx bx-log-out'></i>
              </span>
              <span class="navlink">Log Out</span>
            </a>
          </li>

        </ul>
        <ul class="menu_items">
          <div class="menu_title menu_setting"></div>
        </ul>

    </nav>
    <!-- DataGrid View -->
    <div class="datagrid-container">
        <table class="datagrid">
            <thead>
                <tr>
                    <th>Local #</th>
                    <th>Division</th>
                    <th>Contact Name</th>
                </tr>
            </thead>
            <tbody id="tableBody">
            <?php
                include 'db.php'; // Database connection file
                $query = "SELECT local, office, contact_name FROM miaalocals_user_inputs";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>{$row['local']}</td>
                                <td>{$row['office']}</td>
                                <td>{$row['contact_name']}</td> 
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No records found</td></tr>";
                }
                mysqli_close($conn);
            ?>
            </tbody>
        </table>
    </div>
    <!-- JavaScript -->
    <script src="script.js"></script>
    <script>
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
                let cells = row.getElementsByTagName("td");
                let found = false;

                for (let i = 0; i < cells.length; i++) { // Loop through all columns
                    let text = cells[i].textContent || cells[i].innerText;
                    if (text.toLowerCase().includes(input)) { // Check if input is inside any cell
                        found = true;
                        break;
                    }
                }

                row.style.display = found ? "" : "none"; // Show row if match is found
            });
        }
    </script>
  </body>
</html>
