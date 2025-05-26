<?php
session_start();

// Check if neither admin nor user is logged in
if (!isset($_SESSION['admin_username']) && !isset($_SESSION['user_username'])) {
    header("Location: ../index.php");
    exit();
}

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

// Determine logged-in user
if (isset($_SESSION['admin_username'])) {
    $username = $_SESSION['admin_username'];
} elseif (isset($_SESSION['user_username'])) {
    $username = $_SESSION['user_username'];
    $employee_id = $_SESSION; // Admins don't have employee_id
} else {
    $username = null;
    $employee_id = null;
}

// Database connection
$servername = "localhost";
$db_username = "root"; 
$db_password = ""; 
$dbname = "miaa_locals";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Database Connection Failed: " . $conn->connect_error);
    header("Location: ../error.php"); // Redirect to an error page
    exit();
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
    <link rel="stylesheet" href="../CSS/style.css" />
    <link rel="stylesheet" href="../CSS/manage_locals.css" />
  </head>
  <body>
    <!-- navbar -->
    <nav class="navbar">
    <div class="logo_item">
        <i class="bx bx-menu" id="sidebarOpen"></i>
        <img src="../images/miaa-logo.jpg" alt="">LOCAL MS
    </div>

    <div class="search_bar">
        <input type="text" id="searchInput" placeholder="Search" onkeyup="filterTable()" />
    </div>

    <small>Login as: <?php echo htmlspecialchars($username); ?></small>

    </nav>



    <!-- sidebar -->
    <nav class="sidebar">
      <div class="menu_content">
        <ul class="menu_items">
          <div class="menu_title menu_editor"></div>
          <!-- duplicate these li tag if you want to add or remove navlink only -->
          <!-- Start -->
          

        <li class="item">
        <a href="user_local_list.php" class="nav_link">
            <span class="navlink_icon">
            <i class='bx bx-list-ul'></i>
            </span>
            <span class="navlink">Local List</span>
        </a>
        </li>

        <li class="item active">
        <a href="user_manage_locals.php" class="nav_link">
            <span class="navlink_icon">
            <i class='bx bx-edit'></i>
            </span>
            <span class="navlink">My Locals</span>
        </a>
        </li>

    
      <li class="item">
        <a href="../index.php" class="nav_link" onclick="confirmLogout(event)">
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
      </div>
    </nav>
    
    <!-- DataGrid View -->
    
    <div class="datagrid-container">
    <table class="datagrid" id="dataTable">
        <thead>
            <tr>
                <th>Local #</th>
                <th>Division</th>
                <th>Contact Name</th>
                <th>Date & Time Added</th>
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


  <div id="modalForm" class="modal" action="save_local.php">
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

    <!-- JavaScript -->
    <script src="../script.js"></script>
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
    fetch('../update_local.php', {
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

fetch("save_local.php", {
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
      fetch("../user_fetch_locals.php")
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
                  fetch("../delete.php", {
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
    window.location.href = "../logout.php"; // Redirect to your logout script
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

    </script>

  </body>
</html>
