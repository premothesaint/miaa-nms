<?php
session_start();

// Prevent access if no valid session exists
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

// Prevent browser from caching the page (so back button won’t show old session)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

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
    <link rel="stylesheet" href="CSS/dashboard.css" />
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
    
        .div4 ul {
        font-size: 12px;
        color: grey;
        }

        #locals3DDonutChart {
          max-width: 480px;
          max-height: 480px;
      }

      .div3 {
          display: flex;
          justify-content: center;
          align-items: center;
          height: 100%; /* Ensures the div takes its full height */
          flex-direction: column; /* Ensures content stacks properly */
          text-align: center; /* Centers the text */
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
              <span class="navlink">Manage Users
              <span class="notification-badge" id="userNotification" style="display: none;"></span>

              </span>
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


<?php include 'db.php'; ?>

<div class="dashboard">
    <div class="stats parent">
        <div class="div1">
            <i class='bx bx-phone-call'></i>
            <div>
                <h3 id="totalLocals">0</h3>
                <p>Total Number of Locals</p>
            </div>
        </div>

        <div class="div2">
            <i class='bx bx-user-check'></i>
            <div>
                <h3 id="activeUsers">0</h3>
                <p>Number of Users</p>
            </div>
        </div>

        <div class="div3">
            <h3>Locals Per Office</h3>
            <canvas id="locals3DDonutChart" width="250" height="250"></canvas>

        </div>

        <div class="div4">
            <h3>Recent Activities</h3>
            <ul id="recentActivities"></ul>
        </div>



    </div>
</div>

<script src="script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Fetch locals
        fetch("fetch.php")
            .then(response => response.json())
            .then(data => {
                document.getElementById("totalLocals").innerText = data.length;
            })
            .catch(error => console.error("Error fetching locals:", error));

        // Fetch total users
        fetch("fetch_users.php")
            .then(response => response.json())
            .then(data => {
                document.getElementById("activeUsers").innerText = data.total;
            })
            .catch(error => console.error("Error fetching total users:", error));

        // Fetch locals per office for 3D donut chart
        fetch("fetch_locals_per_office.php")
            .then(response => response.json())
            .then(data => {
                let labels = data.map(item => item.office);
                let values = data.map(item => item.count);
                let colors = ["#FF6384", "#36A2EB", "#FFCE56", "#4CAF50", "#9C27B0", "#E91E63"];

                let ctx = document.getElementById("locals3DDonutChart").getContext("2d");

                new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        labels: labels,
                        datasets: [{
                            data: values,
                            backgroundColor: colors,
                            borderColor: "#ffffff",
                            borderWidth: 2,
                            hoverOffset: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false // 🔹 Removes the legend (label at the top)
                            },
                            tooltip: {
                                enabled: true
                            },
                            datalabels: {
                                color: "#fff",
                                font: {
                                    weight: 'bold'
                                },
                                formatter: (value, ctx) => {
                                    let total = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    let percentage = ((value / total) * 100).toFixed(1) + "%";
                                    return percentage;
                                }
                            }
                        },
                        animation: {
                            animateRotate: true,
                            animateScale: true
                        },
                        cutout: "60%", // Creates a more 3D effect
                        rotation: -0.5 * Math.PI
                    }
                });
            })
            .catch(error => console.error("Error fetching locals per office:", error));
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


    function fetchRecentActivities() {
    fetch('fetch_recent_activities.php')
        .then(response => response.json())
        .then(data => {
            let activityList = document.getElementById("recentActivities");
            activityList.innerHTML = ""; // Clear previous content

            data.forEach(activity => {
                let listItem = document.createElement("li");
                listItem.textContent = activity.message;
                activityList.appendChild(listItem);
            });
        })
        .catch(error => console.error("Error fetching activities:", error));
}

// Fetch recent activities every 15 seconds
setInterval(fetchRecentActivities, 15000);

// Initial fetch when page loads
document.addEventListener("DOMContentLoaded", fetchRecentActivities);



</script>











  </body>
</html>
