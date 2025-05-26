<?php
include 'db.php';

// Handle Approve and Delete actions
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $conn->query("UPDATE users SET status='approved' WHERE employee_id=$id");
}
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE employee_id=$id");
}

$result = $conn->query("SELECT * FROM users");
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
    
  </head>
  <body>
    <!-- navbar -->
    <nav class="navbar">
      <div class="logo_item">
        <i class="bx bx-menu" id="sidebarOpen"></i>
        <img src="images/miaa-logo.jpg" alt=""></i>LOCALS MS

      </div>

      <div class="search_bar">
        <input type="text" placeholder="Search" />
      </div>

    </nav>

    <!-- sidebar -->
    <nav class="sidebar">
      <div class="menu_content">
        <ul class="menu_items">
          <div class="menu_title menu_editor"></div>
          <!-- duplicate these li tag if you want to add or remove navlink only -->
          <!-- Start -->
          <li class="item">
            <a href="dashboard.php" class="nav_link">
              <span class="navlink_icon">
                <i class='bx bx-grid-alt' ></i>
              </span>
              <span class="navlink">Dashboard</span>
            </a>
          </li>
          <!-- End -->

          <li class="item" >
            <a href="" class="nav_link">
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
          <li class="item active">
            <a href="manage_users.php" class="nav_link">
              <span class="navlink_icon">
                <i class='bx bx-user'></i>
              </span>
              <span class="navlink">Manage Users</span>
              <span class="submenu_icon">
                <i class='bx bx-chevron-down'></i>
              </span>
            </a>
            <ul class="submenu">
                <li><a href="user_list.php" class="<?= basename($_SERVER['PHP_SELF']) == 'add_user.php' ? 'active' : '' ?>">User List</a></li>
                <li><a href="user_approval.php" class="<?= basename($_SERVER['PHP_SELF']) == 'user_approval.php' ? 'active' : '' ?>">User Approval</a></li>
                
            </ul>

        </li>

          <li class="item">
          <a href="index.php" class="nav_link" onclick="confirmLogout(event)">
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

        <!-- Sidebar Open / Close -->
        <div class="bottom_content">
          <div class="bottom expand_sidebar">
            <span> Expand</span>
            <i class='bx bxs-chevrons-right'></i>
          </div>
          <div class="bottom collapse_sidebar">
            <span> Collapse</span>
            <i class='bx bxs-chevrons-left' ></i>
          </div>
        </div>
      </div>
    </nav>

    <div class="datagrid-container">
        <table class="datagrid">
            <tr>
                <th>Employee ID</th>
                <th>Full Name</th>
                <th>Office</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['employee_id']; ?></td>
                <td><?php echo $row['full_name']; ?></td>
                <td><?php echo $row['office']; ?></td>
                <td>
                    <div class="actions">
                        <a href="?approve=<?php echo $row['employee_id']; ?>" class="approve-btn">Approve</a>
                        <a href="?delete=<?php echo $row['employee_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?');">Delete</a>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
    
    <!-- JavaScript -->
    <script src="script.js">
    </script>
  </body>
</html>
<?php $conn->close(); ?>