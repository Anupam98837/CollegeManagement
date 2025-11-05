<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Role Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <style>
    /* Custom text sizes */
    .text-13 { font-size: 13px; }
    .text-14 { font-size: 14px; }
    /* Accordion header and body font sizes */
    .accordion-button { font-size: 1rem; }
    .accordion-body { font-size: 0.9rem; }
    /* Scrollable container for the accordion list */
    .accordion-scroll { height: 400px; overflow-y: auto; }
    /* Ensure accordion takes full width */
    #roleAccordion { width: 100%; }
    /* Search input wrapper for icon positioning */
    .search-wrapper { position: relative; }
    .search-wrapper .fa-search {
      position: absolute;
      top: 50%;
      left: 15px;
      transform: translateY(-50%);
      pointer-events: none;
    }
    .search-wrapper input { padding-left: 2.5rem; }
    /* Optional Institution info banner */
    .institute-banner {
      background-color: #f7f7f7;
      padding: 0.75rem 1rem;
      border: 1px solid #ddd;
      border-radius: 0.25rem;
      margin-bottom: 1rem;
    }
    /* Set a minimum height for bottom row cards */
    .min-400 { min-height: 400px; }
  </style>
</head>
<body>
      <div class="container my-4">
        <!-- (Optional) Institution Info Banner -->
        <div id="instituteBanner" class="institute-banner d-none">
          <strong id="instName" class="text-14"></strong> &mdash;
          <span id="instType" class="text-13"></span>
        </div>

        <!-- Top Row: Summary Cards -->
        <div class="row mb-4">
          <!-- Total Roles -->
          <div class="col-12 col-md-4 mb-3 mb-md-0">
            <div class="card h-100" style="background-color: #fff; border-left: 5px solid #007bff;">
              <div class="card-body d-flex flex-column justify-content-center text-center">
                <h5 class="card-title text-primary">
                  <i class="fa-solid fa-users"></i> Total Roles
                </h5>
                <p class="card-text" id="totalRoleCount">
                  <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
                </p>
              </div>
            </div>
          </div>
          <!-- Active Roles -->
          <div class="col-12 col-md-4 mb-3 mb-md-0">
            <div class="card h-100" style="background-color: #fff; border-left: 5px solid #28a745;">
              <div class="card-body d-flex flex-column justify-content-center text-center">
                <h5 class="card-title text-success">
                  <i class="fa-solid fa-check"></i> Active Roles
                </h5>
                <p class="card-text" id="activeRoleCount">
                  <span class="spinner-border spinner-border-sm text-success" role="status" aria-hidden="true"></span>
                </p>
              </div>
            </div>
          </div>
          <!-- Inactive Roles -->
          <div class="col-12 col-md-4">
            <div class="card h-100" style="background-color: #fff; border-left: 5px solid #dc3545;">
              <div class="card-body d-flex flex-column justify-content-center text-center">
                <h5 class="card-title text-danger">
                  <i class="fa-solid fa-times"></i> Inactive Roles
                </h5>
                <p class="card-text" id="inactiveRoleCount">
                  <span class="spinner-border spinner-border-sm text-danger" role="status" aria-hidden="true"></span>
                </p>
              </div>
            </div>
          </div>
        </div>
        
        <!-- New Row: Accordion (Roles Grouped by Institution) -->
        <div class="row align-items-stretch">
          <!-- Accordion Column -->
          <div class="col-12">
            <div class="card h-100 min-400">
              <div class="card-header">
                <h5 class="text-14 mb-0">Roles Grouped by Institution</h5>
              </div>
              <div class="card-body">
                <div class="accordion accordion-scroll" id="roleAccordion">
                  <!-- Dynamic accordion items will be injected here -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- End container -->

  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // On page load, check for token and fetch roles.
    document.addEventListener("DOMContentLoaded", function () {
      if (!sessionStorage.getItem("token")) {
        window.location.href = "/";
      }
      fetchRoles();
    });

    const token = sessionStorage.getItem("token");
    let rolesData = [];

    function fetchRoles() {
      // Determine API endpoint based on institution_id in sessionStorage.
      const institutionId = sessionStorage.getItem("institution_id");
      const url = institutionId 
        ? `/api/institution/${institutionId}/roles` 
        : `/api/all-institution/roles`;

      fetch(url, {
        method: "GET",
        headers: {
          "Accept": "application/json",
          "Authorization": token
        }
      })
      .then(response => {
        if (response.status === 401 || response.status === 403) {
          window.location.href = "/Unauthorised";
          throw new Error("Unauthorized Access");
        }
        return response.json();
      })
      .then(data => {
        if (data.status === "success") {
          rolesData = data.data;
          updateRoleCounts();
          populateRoleAccordion();
        } else {
          console.error("Error fetching roles:", data.message);
        }
      })
      .catch(error => console.error("Error:", error));
    }

    function updateRoleCounts() {
      const total = rolesData.length;
      const active = rolesData.filter(role => role.status === "Active").length;
      const inactive = total - active;
      document.getElementById("totalRoleCount").textContent = total;
      document.getElementById("activeRoleCount").textContent = active;
      document.getElementById("inactiveRoleCount").textContent = inactive;
    }

    // Populate the accordion by grouping roles by institution name.
    function populateRoleAccordion() {
      // Group roles by institution_name.
      const grouped = {};
      rolesData.forEach(role => {
        const inst = role.institution_name || "Unknown Institution";
        if (!grouped[inst]) {
          grouped[inst] = [];
        }
        grouped[inst].push(role);
      });

      const accordion = document.getElementById("roleAccordion");
      accordion.innerHTML = "";
      // Use Object.keys(grouped) and ensure the first group is expanded by default.
      const institutions = Object.keys(grouped);
      institutions.forEach((inst, index) => {
        const roles = grouped[inst];
        // Create a safe ID for the accordion item.
        const instSafe = inst.replace(/\s+/g, '');
        // For the first institution, expand by default.
        const isFirst = index === 0;
        const accordionItem = document.createElement("div");
        accordionItem.classList.add("accordion-item");
        accordionItem.innerHTML = `
          <h2 class="accordion-header" id="heading-${instSafe}">
            <button class="accordion-button ${isFirst ? "" : "collapsed"} text-14" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${instSafe}" aria-expanded="${isFirst ? "true" : "false"}" aria-controls="collapse-${instSafe}">
              ${inst} (${roles.length})
            </button>
          </h2>
          <div id="collapse-${instSafe}" class="accordion-collapse collapse ${isFirst ? "show" : ""}" aria-labelledby="heading-${instSafe}" data-bs-parent="#roleAccordion">
            <div class="accordion-body text-13">
              <!-- Search and filter controls -->
              <div class="d-flex mb-2">
                <div class="flex-grow-1 me-2">
                  <div class="search-wrapper">
                    <input type="text" class="form-control role-search-input" placeholder="Search roles by name, email, or designation...">
                    <i class="fa-solid fa-search"></i>
                  </div>
                </div>
                <div>
                  <select class="form-select role-status-filter">
                    <option value="all" selected>All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                  </select>
                </div>
              </div>
              <!-- Roles Table -->
              <div class="table-responsive">
                <table class="table table-striped table-sm" id="roleTable-${instSafe}">
                  <thead>
                    <tr>
                      <th class="text-13 text-secondary">Name</th>
                      <th class="text-13 text-secondary">Designation</th>
                      <th class="text-13 text-secondary">Email</th>
                      <th class="text-13 text-secondary">Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    ${roles.map(role => `
                      <tr class="role-row" 
                          data-role-name="${role.name.toLowerCase()}" 
                          data-org-email="${role.org_email.toLowerCase()}" 
                          data-designation="${role.designation.toLowerCase()}"
                          data-role-status="${role.status.toLowerCase()}">
                        <td>${role.name}</td>
                        <td>${role.designation}</td>
                        <td>${role.org_email}</td>
                        <td style="color: ${role.status === 'Active' ? 'green' : 'red'};">${role.status}</td>
                      </tr>
                    `).join('')}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        `;
        accordion.appendChild(accordionItem);
      });
      attachRoleFilters();
    }

    // Attach filtering functionality to each accordion item.
    function attachRoleFilters() {
      const accordionItems = document.querySelectorAll(".accordion-item");
      accordionItems.forEach(item => {
        const searchInput = item.querySelector(".role-search-input");
        const statusFilter = item.querySelector(".role-status-filter");
        const tableBody = item.querySelector("table tbody");
        const tableRows = item.querySelectorAll("tr.role-row");

        function filterRows() {
          let visibleCount = 0;
          tableRows.forEach(row => {
            const roleName = row.getAttribute("data-role-name");
            const orgEmail = row.getAttribute("data-org-email");
            const designation = row.getAttribute("data-designation");
            const roleStatus = row.getAttribute("data-role-status");
            const query = searchInput.value.toLowerCase();
            const filterStatus = statusFilter.value;
            const matchesQuery = roleName.includes(query) || orgEmail.includes(query) || designation.includes(query);
            const matchesStatus = (filterStatus === "all") ||
                                  (filterStatus === "active" && roleStatus === "active") ||
                                  (filterStatus === "inactive" && roleStatus === "inactive");
            if (matchesQuery && matchesStatus) {
              row.style.display = "";
              visibleCount++;
            } else {
              row.style.display = "none";
            }
          });
          // If no rows are visible, show a "No roles found" message.
          const existingNoData = tableBody.querySelector(".no-data");
          if (existingNoData) existingNoData.remove();
          if (visibleCount === 0) {
            const tr = document.createElement("tr");
            tr.classList.add("no-data");
            tr.innerHTML = `<td colspan="4" class="text-center">No roles found.</td>`;
            tableBody.appendChild(tr);
          }
        }
        searchInput.addEventListener("input", filterRows);
        statusFilter.addEventListener("change", filterRows);
      });
    }
  </script>
</body>
</html>
