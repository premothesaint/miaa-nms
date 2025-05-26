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
$servername = "localhost"; // Change if your DB is hosted remotely
$username = "root"; // Change to your actual database username
$password = ""; // Change to your actual database password
$dbname = "miaa_locals"; // Change to your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
    <link rel="stylesheet" href="CSS/manage_locals.css" />
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
        <input type="text" id="searchInput" placeholder="Search" onkeyup="filterTable()" />
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
      <i class='bx bx-grid-alt'></i>
    </span>
    <span class="navlink">Dashboard</span>
  </a>
</li>

<li class="item">
  <a href="local_list.php" class="nav_link">
    <span class="navlink_icon">
      <i class='bx bx-list-ul'></i>
    </span>
    <span class="navlink">Local List</span>
  </a>
</li>

<li class="item active">
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
          </l>          
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

    </nav>
    
<div class="datagrid-container">
    <table class="datagrid" id="dataTable">
        <thead>
            <tr>
                <th>Local #</th>
                <th>Division</th>
                <th>Contact Name</th>
                <th>Date & Time Added</th>
                <th>Properties</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="tableBody">
            <!-- Data will be loaded dynamically here -->
        </tbody>
    </table>
</div>


  <button class="floating-btn" onclick="openModal()">
    <i class='bx bx-plus'></i>
  </button>


    <div id="modalForm" class="modal">
        <div class="modal-content">
            <h2>Add Local</h2>
            <label for="local">Local</label>
            <input type="number" id="local" placeholder="ENTER LOCAL" name="local" required/>

            <label for="office">Office</label>
            <select id="office" name="office" required>
                <option value="">Select Office</option>
                <?php
                // Fetch office list
                include 'db_connect.php'; // Ensure database connection is included
                $sql = "SELECT office_name FROM office_list";
                $result = $conn->query($sql);
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . htmlspecialchars($row["office_name"]) . '">' . htmlspecialchars($row["office_name"]) . '</option>';
                    }
                } else {
                    echo '<option value="">Error loading offices</option>';
                }
                ?>
            </select>

            <label for="contact_name">Contact Name</label>
            <input type="text" id="contact_name" placeholder="CONTACT NAME" style="text-transform: uppercase;" name="contact_name" required/>

            <div class="modal-buttons">
                <button class="btn btn-save" onclick="saveLocal()">Save</button>
                <button class="btn btn-cancel" onclick="closeModal()">Cancel</button>
            </div>
        </div>
    </div>


    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h2>Edit Local</h2>
            <input type="hidden" id="editId" name="input_id">

            
            <label for="editLocal">Local</label>
            <input type="number" id="editLocal" placeholder="ENTER LOCAL" name="local" required/>

            <label for="editOffice">Office</label>
            <input list="officeList" id="editOffice" name="office" required>

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

                // Close connection
                $conn->close();
                ?>
            </datalist>


            <label for="editContact_name">Contact Name</label>
            <input type="text" id="editContact_name" placeholder="CONTACT NAME" style="text-transform: uppercase;" name="contact_name" required/>

            <div class="modal-buttons">
                <button class="btn btn-save" onclick="updateLocal()">Update</button>
                <button class="btn btn-cancel" onclick="closeEditModal()">Cancel</button>
            </div>
        </div>
    </div>
    
    <div id="myModal" class="modal">
      <div class="modal-content">
          <h2 style="text-align: left;">Details 🛈</h2>
          <hr/>
          <p style="text-align: left; font-size: 14px; margin: 10px 0;"><strong>ADDED BY</strong></p>
          <hr/>
          <p style="text-align: left; font-size: 14px; margin: 10px 0;"><strong>NAME:</strong> <span id="addedBy"></span></p>
          <p style="text-align: left; font-size: 14px; margin: 10px 0;"><strong>EMPLOYEE ID:</strong> <span id="employeeId"></span></p>
          <p style="text-align: left; font-size: 14px; margin: 10px 0;"><strong>USER OFFICE:</strong> <span id="user_office"></span></p>
          <hr/>
          <p style="text-align: left; font-size: 14px; margin: 10px 0;"><strong>DATE & TIME</strong></p>
          <hr/>
          <p style="text-align: left; font-size: 14px; margin: 10px 0;"><strong>ADDED:</strong> <span id="addedDate"></span></p>
          <p style="text-align: left; font-size: 14px; margin: 10px 0;"><strong>EDITED:</strong> <span id="editedDate"></span></p>
          <button id="closeModal" 
              style="background-color: black; color: white; border: none; padding: 10px 50px; border-radius: 3px; cursor: pointer; transition: background-color 0.3s; display: block; margin: 20px auto 0; text-align: center;" 
              onmouseover="this.style.backgroundColor='grey'" 
              onmouseout="this.style.backgroundColor='black'">
              OK
          </button>
      </div>
    </div>

          


    <!-- JavaScript -->
    <script src="script.js"></script>
    <script>

    document.addEventListener("DOMContentLoaded", loadData);

    function editLocal(id, local, office, contactName) {
        // Set the values in the input fields
        document.getElementById('editId').value = id;
        document.getElementById('editLocal').value = local;
        document.getElementById('editOffice').value = office;
        document.getElementById('editContact_name').value = contactName;

        // Show the modal
        document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
        // Hide the modal
        document.getElementById('editModal').style.display = 'none';
    }

    // Close the modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }

    function updateLocal() {
        const input_id = document.getElementById('editId').value;
        const local = document.getElementById('editLocal').value;
        const office = document.getElementById('editOffice').value;
        const contactName = document.getElementById('editContact_name').value.toUpperCase(); // Convert to uppercase

        if (!input_id || !local || !office || !contactName) {
            alert("All fields are required!");
            return;
        }

        // Send data via AJAX
        fetch('update_local.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `input_id=${input_id}&local=${local}&office=${office}&contact_name=${contactName}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Local updated successfully!");
                closeEditModal();
                loadData(); // Refresh table
            } else {
                alert("Update failed: " + data.error);
            }
        })
        .catch(error => console.error("Error:", error));
    }




    function saveLocal() {
    let local = document.getElementById("local").value;
    let office = document.getElementById("office").value;
    let contact_name = document.getElementById("contact_name").value;

    if (local === "" || office === "" || contact_name === "") {
        alert("All fields are required!");
        return;
    }

    let formData = new FormData();
    formData.append("local", local);
    formData.append("office", office);
    formData.append("contact_name", contact_name);

    fetch("insert.php", {
              method: "POST",
              body: formData
          })
          .then(response => response.text())
          .then(data => {
              alert(data);
              closeModal();
              loadData();
          })
          .catch(error => console.error("Error:", error));
          }

          function loadData() {
          fetch("fetch.php")
              .then(response => response.json())
              .then(data => {
                  let tableBody = document.getElementById("tableBody");
                  tableBody.innerHTML = "";

                  data.forEach(row => {
                      // Convert date format
                      let formattedDateTime = new Date(row.date_added).toLocaleString("en-US", {
                          year: "numeric",
                          month: "long",
                          day: "numeric",
                          hour: "2-digit",
                          minute: "2-digit",
                          second: "2-digit",
                          hour12: true
                      });

                      let tr = document.createElement("tr");
                      tr.innerHTML = `
                          <td>${row.local}</td>
                          <td>${row.office}</td> <!-- Changed from 'Division' to match 'office' -->
                          <td>${row.contact_name}</td>
                          <td>${formattedDateTime}</td> <!-- Moved to the correct column position -->
                          <td>
                              <button class="show-btn" onclick="showProperties(${row.input_id})">Show</button>
                          </td>
                          <td>
                              <button class="edit-btn" onclick="editLocal(${row.input_id}, '${row.local}', '${row.office}', '${row.contact_name}', '${row.full_name}')">Edit</button>
                              <button class="delete-btn" onclick="deleteLocal(${row.input_id})">Delete</button>
                          </td>
                      `;
                      tableBody.appendChild(tr);
                  });
              })
              .catch(error => console.error("Error:", error));  
      }
        function filterTable() {
            let input = document.getElementById("searchInput");
            let filter = input.value.toLowerCase();
            let tableBody = document.getElementById("tableBody");
            let rows = tableBody.getElementsByTagName("tr");

            for (let i = 0; i < rows.length; i++) {
                let cells = rows[i].getElementsByTagName("td");
                let found = false;

                for (let j = 0; j < cells.length - 1; j++) { // Exclude the last column (Actions)
                    let cell = cells[j];
                    if (cell) {
                        let textValue = cell.textContent || cell.innerText;
                        if (textValue.toLowerCase().startsWith(filter)) {
                            found = true;
                            break;
                        }
                    }
                }
                
                rows[i].style.display = found ? "" : "none";
            }
        }

        // Load data on page load
        window.onload = loadData;
      function deleteLocal(id) {
                  if (confirm("Are you sure you want to delete this entry?")) {
                      fetch("delete.php", {
                          method: "POST",
                          headers: { "Content-Type": "application/x-www-form-urlencoded" },
                          body: `id=${id}`
                      })
                      .then(response => response.text())
                      .then(message => {
                          alert(message);
                          loadData();
                      });
                  }
      }


      function confirmLogout(event) {
        event.preventDefault(); // Prevent immediate navigation
        let confirmAction = confirm("Are you sure you want to log out?");
        if (confirmAction) {
          window.location.href = "index.php"; // Redirect to your logout script
        }
      }



      function openModal() {
          document.getElementById("modalForm").style.display = "flex";
      }

      function closeModal() {
          document.getElementById("modalForm").style.display = "none";
          // Clear input fields
          document.getElementById("local").value = "";
          document.getElementById("office").value = "";
          document.getElementById("contact_name").value = "";
      }


function showProperties(inputId) {
    fetch("fetch.php")
        .then(response => response.json())
        .then(data => {
            let selectedRow = data.find(row => row.input_id == inputId);
            if (selectedRow) {
                // Format Date & Time
                let formattedDateTime = new Date(selectedRow.date_added).toLocaleString("en-US", {
                    year: "numeric",
                    month: "long",
                    day: "numeric",
                    hour: "2-digit",
                    minute: "2-digit",
                    second: "2-digit",
                    hour12: true
                });

                let editedDateTime = selectedRow.date_edited
                    ? new Date(selectedRow.date_edited).toLocaleString("en-US", {
                          year: "numeric",
                          month: "long",
                          day: "numeric",
                          hour: "2-digit",
                          minute: "2-digit",
                          second: "2-digit",
                          hour12: true
                      })
                    : "N/A";

                // Populate modal fields
                document.getElementById("addedBy").textContent = selectedRow.full_name;
                document.getElementById("employeeId").textContent = selectedRow.employee_id || "N/A";
                document.getElementById("user_office").textContent = selectedRow.user_office;
                document.getElementById("addedDate").textContent = formattedDateTime;
                document.getElementById("editedDate").textContent = editedDateTime;


                // Show Modal
                document.getElementById("myModal").style.display = "block";
            }
        })
        .catch(error => console.error("Error:", error));
}

// Close modal when "OK" is clicked
document.getElementById("closeModal").addEventListener("click", function () {
    document.getElementById("myModal").style.display = "none";
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
      
    </script>

  </body>
</html>
