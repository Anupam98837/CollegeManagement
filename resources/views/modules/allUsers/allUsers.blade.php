<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>All Users</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .text-13 { font-size: 13px; }
    .text-14 { font-size: 14px; }
    .placeholder-14::placeholder { font-size: 14px; }
    /* Loading spinner overlay */
    #loadingSpinner {
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(255,255,255,0.7);
      z-index: 9999;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    /* Refresh icon rotation */
    @keyframes rotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    .rotate {
      animation: rotate 1s linear;
    }
  </style>
</head>
<body>
      <div class="container mt-4">
        <!-- Page Title and Count Summary -->
        <div class="mb-3">
          <p class="my-4 text-secondary text-14">
            <i class="fa-solid fa-angle-right"></i>
            <span class="text-primary">All Users</span>
          </p>
          <!-- New Counts Structure -->
          <div class="row mb-3">
            <!-- Students Card -->
            <div class="col-md-4">
              <div class="card text-secondary shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                  <div class="me-3">
                    <i class="fa-solid fa-user-graduate fa-2x text-primary"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h5 class="card-title mb-2">Students</h5>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                      <span class="fw-bold">Total:</span>
                      <span id="studentCount">0</span>
                      <span class="badge bg-success text-white ms-3">Active: <span id="activeStudentCount">0</span></span>
                      <span class="badge bg-danger text-white ms-2">Inactive: <span id="inactiveStudentCount">0</span></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Agents Card -->
            <div class="col-md-4">
              <div class="card text-secondary shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                  <div class="me-3">
                    <i class="fa-solid fa-user-tie fa-2x text-info"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h5 class="card-title mb-2">Agents</h5>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                      <span class="fw-bold">Total:</span>
                      <span id="agentCount">0</span>
                      <span class="badge bg-success text-white ms-3">Active: <span id="activeAgentCount">0</span></span>
                      <span class="badge bg-danger text-white ms-2">Inactive: <span id="inactiveAgentCount">0</span></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          
            <!-- Institution Roles Card -->
            <div class="col-md-4">
              <div class="card text-secondary shadow-sm border-0">
                <div class="card-body d-flex align-items-center justify-content-between">
                  <div class="me-3">
                    <i class="fa-solid fa-building fa-2x text-warning"></i>
                  </div>
                  <div class="flex-grow-1">
                    <h5 class="card-title mb-2">Institution Roles</h5>
                    <div class="d-flex flex-wrap align-items-center gap-2">
                      <span class="fw-bold">Total:</span>
                      <span id="institutionCount">0</span>
                      <span class="badge bg-success text-white ms-3">Active: <span id="activeInstitutionCount">0</span></span>
                      <span class="badge bg-danger text-white ms-2">Inactive: <span id="inactiveInstitutionCount">0</span></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div> 
          <!-- Hidden overall status counts (kept for JS compatibility) -->
          <div id="statusCounts" style="display: none;">
            <span class="badge bg-success text-white">Active: <span id="activeCount">0</span></span>
            <span class="badge bg-danger text-white">Inactive: <span id="inactiveCount">0</span></span>
          </div>
          
          <!-- Total Count -->
          <div class="mb-3">
            <strong>Total Users: </strong><span id="totalCount">0</span>
          </div>
        </div>
        <!-- Filters -->
        <div class="mb-4">
          <div class="row g-2">
            <!-- Search Bar: full width on mobile, col-12 -->
            <!-- Other Filters: each col-md-4 -->
            <div class="col-4">
              <select id="institutionFilter" class="form-select text-13">
                <option value="all" selected>All Institutions</option>
              </select>
            </div>
            <div class="col-4">
              <select id="userTypeFilter" class="form-select text-13">
                <option value="all" selected>All</option>
                <option value="students">Students</option>
                <option value="agents">Agents</option>
                <option value="institution">Institution Role</option>
              </select>
            </div>
            <div class="col-4">
              <select id="statusFilter" class="form-select text-13">
                <option value="all" selected>Status: All</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
              </select>
            </div>
          </div>
          
        </div>

        <!-- Table Container with Refresh Button at top left -->
        <div class="bg-white p-4 rounded position-relative">
          <div class="row g-2">
            <div class="col-6">
              <div class="position-relative">
                <input type="text" id="userSearch" class="form-control placeholder-14 text-13 ps-5" placeholder="Search by Name, Institution or Designation">
                <i class="fa-solid fa-search position-absolute text-secondary" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
              </div>
            </div>
            <div class="col-6 text-end">
              <button id="resetFiltersBtn" class="btn btn-outline-secondary text-13">
                <i class="fa-solid fa-arrows-rotate" id="refreshIcon"></i>
                Reset Filters
              </button>
            </div>
          </div>
              
          {{-- <div class="d-flex justify-content-start mb-2">
            <button id="tableRefreshBtn" class="btn btn-outline-primary btn-sm text-13">
              <i class="fa-solid fa-arrow-rotate-right" id="tableRefreshIcon"></i> Refresh
            </button>
          </div> --}}
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th class="text-14 text-secondary">Name</th>
                  <th class="text-14 text-secondary">Institution Name</th>
                  <th class="text-14 text-secondary">Designation</th>
                  <th class="text-14 text-secondary">Status</th>
                </tr>
              </thead>
              <tbody id="usersTableBody">
                <!-- User data will be populated here -->
              </tbody>
            </table>
          </div>
          <!-- Pagination Controls -->
          <div id="paginationContainer" class="mt-3 d-flex justify-content-center gap-2"></div>
        </div>
      </div>
    </div>
    <!-- Loading Spinner (hidden by default) -->
    <div id="loadingSpinner" style="display: none;">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
  <!-- JavaScript -->
  <script>
    const token = sessionStorage.getItem('token');
    if (!token) {
      window.location.href = "/";
    }

    let usersList = [];
    let currentPage = 1;
    const rowsPerPage = 50;

    // Spinner functions
    function showSpinner() {
      document.getElementById("loadingSpinner").style.display = "flex";
    }
    function hideSpinner() {
      document.getElementById("loadingSpinner").style.display = "none";
    }

    // Animate refresh icons on click
    function animateIcon(iconId) {
      const icon = document.getElementById(iconId);
      icon.classList.add("rotate");
      setTimeout(() => icon.classList.remove("rotate"), 1000);
    }

    // Annotate data with userType based on endpoint
    function annotateData(data, type) {
      if(data.status === "success" && data.data && data.data.length > 0){
        data.data = data.data.map(user => ({ ...user, userType: type }));
      }
      return data;
    }

    // Fetch users based on type selection
    function fetchUsers() {
      showSpinner();
      const userType = document.getElementById("userTypeFilter").value;
      currentPage = 1;
      let fetchPromises = [];

      if (userType === "students") {
        fetchPromises.push(
          fetch("/api/view-students", { method: "GET", headers: { 'Accept': 'application/json', 'Authorization': token } })
            .then(response => response.json()).then(data => annotateData(data, "student"))
        );
      } else if (userType === "agents") {
        fetchPromises.push(
          fetch("/api/view-agents", { method: "GET", headers: { 'Accept': 'application/json', 'Authorization': token } })
            .then(response => response.json()).then(data => annotateData(data, "agent"))
        );
      } else if (userType === "institution") {
        fetchPromises.push(
          fetch("/api/all-institution/roles", { method: "GET", headers: { 'Accept': 'application/json', 'Authorization': token } })
            .then(response => response.json()).then(data => annotateData(data, "institution"))
        );
      } else if (userType === "all") {
        fetchPromises.push(
          fetch("/api/view-students", { method: "GET", headers: { 'Accept': 'application/json', 'Authorization': token } })
            .then(response => response.json()).then(data => annotateData(data, "student"))
        );
        fetchPromises.push(
          fetch("/api/view-agents", { method: "GET", headers: { 'Accept': 'application/json', 'Authorization': token } })
            .then(response => response.json()).then(data => annotateData(data, "agent"))
        );
        fetchPromises.push(
          fetch("/api/all-institution/roles", { method: "GET", headers: { 'Accept': 'application/json', 'Authorization': token } })
            .then(response => response.json()).then(data => annotateData(data, "institution"))
        );
      }

      Promise.all(fetchPromises)
      .then(results => {
        let combinedData = [];
        results.forEach(result => {
          if (result.status === "success" && result.data && result.data.length > 0) {
            combinedData = combinedData.concat(result.data);
          }
        });
        usersList = combinedData;
        updateStatusCounts();
        updateTypeCounts();
        populateInstitutionFilter();
        applyFilters();
      })
      .catch(error => {
        console.error("Error fetching users:", error);
        document.getElementById("usersTableBody").innerHTML = `<tr><td colspan="4" class="text-center">Failed to fetch data. Please try again.</td></tr>`;
      })
      .finally(() => {
        hideSpinner();
      });
    }

    // Update counts
    function updateStatusCounts() {
      const activeUsers = usersList.filter(u => (u.status || "").toLowerCase() === "active");
      const inactiveUsers = usersList.filter(u => (u.status || "").toLowerCase() === "inactive");

      document.getElementById("activeCount").textContent = activeUsers.length;
      document.getElementById("inactiveCount").textContent = inactiveUsers.length;

      document.getElementById("activeStudentCount").textContent =
        activeUsers.filter(u => u.userType === "student").length;
      document.getElementById("activeAgentCount").textContent =
        activeUsers.filter(u => u.userType === "agent").length;
      document.getElementById("activeInstitutionCount").textContent =
        activeUsers.filter(u => u.userType === "institution").length;

      document.getElementById("inactiveStudentCount").textContent =
        inactiveUsers.filter(u => u.userType === "student").length;
      document.getElementById("inactiveAgentCount").textContent =
        inactiveUsers.filter(u => u.userType === "agent").length;
      document.getElementById("inactiveInstitutionCount").textContent =
        inactiveUsers.filter(u => u.userType === "institution").length;
    }


    function updateTypeCounts() {
      const studentCount = usersList.filter(u => u.userType === "student").length;
      const agentCount = usersList.filter(u => u.userType === "agent").length;
      const institutionCount = usersList.filter(u => u.userType === "institution").length;
      document.getElementById("studentCount").textContent = studentCount;
      document.getElementById("agentCount").textContent = agentCount;
      document.getElementById("institutionCount").textContent = institutionCount;
      document.getElementById("totalCount").textContent = usersList.length;
    }

    // Populate Institution Filter dropdown
    function populateInstitutionFilter() {
      const instSet = new Set();
      usersList.forEach(user => {
        let instName = "";
        if(user.institute) {
          try {
            const instData = typeof user.institute === "object" ? user.institute : JSON.parse(user.institute);
            if(instData.institution_name) {
              instName = instData.institution_name;
            }
          } catch(e) { }
        } else if(user.institution_name){
          instName = user.institution_name;
        }
        if(instName) {
          instSet.add(instName.toLowerCase());
        }
      });
      const instFilter = document.getElementById("institutionFilter");
      instFilter.innerHTML = `<option value="all" selected>All Institutions</option>`;
      Array.from(instSet).sort().forEach(inst => {
        const option = document.createElement("option");
        option.value = inst;
        option.textContent = inst.charAt(0).toUpperCase() + inst.slice(1);
        instFilter.appendChild(option);
      });
    }

    // Render users into the table (or show doData.png if none)
    function renderUsers(users) {
      console.log(users)
      const tableBody = document.getElementById("usersTableBody");
      tableBody.innerHTML = "";
      if (users.length > 0) {
        users.forEach(user => {
          let institutionName = "N/A";
          if (user.institute) {
            try {
              const instData = typeof user.institute === "object" ? user.institute : JSON.parse(user.institute);
              institutionName = instData.institution_short_code || "N/A";
            } catch (e) {
              institutionName = "N/A";
            }
          } else if(user.institution_name){
            institutionName = user.institution_short_code;
          }
          const designation = user.designation || "N/A";
          const status = user.status || "N/A";
          const name = user.name || "N/A";
          const statusClass = status.toLowerCase() === "active" ? "text-success" : (status.toLowerCase() === "inactive" ? "text-danger" : "");
          const row = `
            <tr>
              <td class="text-13">${name}</td>
              <td class="text-13">${institutionName}</td>
              <td class="text-13">${designation}</td>
              <td class="text-13 ${statusClass}">${status}</td>
            </tr>
          `;
          tableBody.innerHTML += row;
        });
      } else {
        tableBody.innerHTML = `<tr><td colspan="4" class="text-center">
           <img src="{{ asset('assets/web_assets/noData.png') }}" alt="No Data" class="img-fluid" style="max-width:200px;"><br>
          <p> No User Found.</p>
        </td></tr>`;
      }
    }

    // Pagination functions
    function getPaginatedData(data) {
      const start = (currentPage - 1) * rowsPerPage;
      return data.slice(start, start + rowsPerPage);
    }

    function renderPagination(filteredData) {
      const totalPages = Math.ceil(filteredData.length / rowsPerPage);
      let paginationHTML = '';

      // Start button
      paginationHTML += `<button class="btn btn-outline-primary mx-1 text-13" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(1)"><i class="fa-solid fa-angles-left"></i></button>`;
      
      // Prev button
      paginationHTML += `<button class="btn btn-outline-primary mx-1 text-13" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})"><i class="fa-solid fa-angle-left"></i></button>`;
      
      // Current page / total pages display
      paginationHTML += `<span class="btn btn-outline-primary mx-2 align-self-center text-13">${currentPage} / ${totalPages}</span>`;
      
      // Next button
      paginationHTML += `<button class="btn btn-outline-primary mx-1 text-13" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})"><i class="fa-solid fa-angle-right"></i></button>`;
      
      // Last button
      paginationHTML += `<button class="btn btn-outline-primary mx-1 text-13" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${totalPages})"><i class="fa-solid fa-angles-right"></i></button>`;
      
      // Center the pagination controls
      document.getElementById('paginationContainer').innerHTML = `<div class="d-flex justify-content-center align-items-center">${paginationHTML}</div>`;
    }


    function changePage(page) {
      currentPage = page;
      applyFilters();
    }

    // Apply filters
    function applyFilters() {
      const query = document.getElementById("userSearch").value.toLowerCase();
      const statusFilter = document.getElementById("statusFilter").value;
      const institutionFilter = document.getElementById("institutionFilter").value;
      let filtered = usersList.filter(user => {
        const name = (user.name || "").toLowerCase();
        let institution = "";
        if (user.institute) {
          try {
            const instData = typeof user.institute === "object" ? user.institute : JSON.parse(user.institute);
            institution = (instData.institution_name || "").toLowerCase();
          } catch (e) {
            institution = "";
          }
        } else if(user.institution_name) {
          institution = user.institution_name.toLowerCase();
        }
        const designation = (user.designation || "").toLowerCase();
        const status = (user.status || "").toLowerCase();

        const queryMatch = name.includes(query) || institution.includes(query) || designation.includes(query);
        const statusMatch = statusFilter === "all" || status === statusFilter.toLowerCase();
        const institutionMatch = institutionFilter === "all" || institution === institutionFilter.toLowerCase();
        return queryMatch && statusMatch && institutionMatch;
      });
      document.getElementById("totalCount").textContent = filtered.length;
      const paginatedData = getPaginatedData(filtered);
      renderUsers(paginatedData);
      renderPagination(filtered);
    }

    // Reset Filters Button event
    document.getElementById("resetFiltersBtn").addEventListener("click", function() {
      document.getElementById("userSearch").value = "";
      document.getElementById("institutionFilter").value = "all";
      document.getElementById("userTypeFilter").value = "all";
      document.getElementById("statusFilter").value = "all";
      currentPage = 1;
      animateIcon("refreshIcon");
      fetchUsers();
    });

    // Event listeners for filters
    document.getElementById("userSearch").addEventListener("input", function() {
      currentPage = 1;
      applyFilters();
    });
    document.getElementById("userTypeFilter").addEventListener("change", function() {
      fetchUsers();
    });
    document.getElementById("statusFilter").addEventListener("change", function() {
      currentPage = 1;
      applyFilters();
    });
    document.getElementById("institutionFilter").addEventListener("change", function() {
      currentPage = 1;
      applyFilters();
    });

    document.addEventListener("DOMContentLoaded", function() {
      fetchUsers();
    });
  </script>

  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
