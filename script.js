const body = document.querySelector("body");
const darkLight = document.querySelector("#darkLight");
const sidebar = document.querySelector(".sidebar");
const submenuItems = document.querySelectorAll(".submenu_item");
const sidebarOpen = document.querySelector("#sidebarOpen");
const sidebarClose = document.querySelector(".collapse_sidebar");
const sidebarExpand = document.querySelector(".expand_sidebar");
sidebarOpen.addEventListener("click", () => sidebar.classList.toggle("close"));

sidebarClose.addEventListener("click", () => {
  sidebar.classList.add("close", "hoverable");
});
sidebarExpand.addEventListener("click", () => {
  sidebar.classList.remove("close", "hoverable");
});

sidebar.addEventListener("mouseenter", () => {
  if (sidebar.classList.contains("hoverable")) {
    sidebar.classList.remove("close");
  }
});
sidebar.addEventListener("mouseleave", () => {
  if (sidebar.classList.contains("hoverable")) {
    sidebar.classList.add("close");
  }
});


submenuItems.forEach((item, index) => {
  item.addEventListener("click", () => {
    item.classList.toggle("show_submenu");
    submenuItems.forEach((item2, index2) => {
      if (index !== index2) {
        item2.classList.remove("show_submenu");
      }
    });
  });
});

if (window.innerWidth < 768) {
  sidebar.classList.add("close");
} else {
  sidebar.classList.remove("close");
}


document.addEventListener("DOMContentLoaded", function() {
  const totalLocals = 100; // Example value
  const activeLocals = 60; // Example value
  const inactiveLocals = totalLocals - activeLocals;
  
  document.getElementById("total-locals").innerText = totalLocals;
  document.getElementById("active-locals").innerText = activeLocals;
  
  // Pie chart
  const ctx = document.getElementById("localsPieChart").getContext("2d");
  new Chart(ctx, {
      type: "pie",
      data: {
          labels: ["Active Locals", "Inactive Locals"],
          datasets: [{
              data: [activeLocals, inactiveLocals],
              backgroundColor: ["#4CAF50", "#F44336"]
          }]
      }
  });
  
  // Activity History (Example Data)
  const activityList = document.getElementById("activity-list");
  const activities = [
      "Local A marked as active",
      "Local B deactivated",
      "New local added: Local C"
  ];
  activities.forEach(activity => {
      const li = document.createElement("li");
      li.textContent = activity;
      activityList.appendChild(li);
  });
});

const termsCheckbox = document.getElementById("terms");
const registerBtn = document.getElementById("registerBtn");

termsCheckbox.addEventListener("change", function () {
    registerBtn.disabled = !this.checked;
});