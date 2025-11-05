<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Agent Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <style>
    .accordion-scroll { height: 400px; overflow-y: auto; }
    .search-wrapper { position: relative; }
    .search-wrapper .fa-search {
      position: absolute;
      top: 50%;
      left: 15px;
      transform: translateY(-50%);
      pointer-events: none;
    }
    .search-wrapper input { padding-left: 2.5rem; }
    .institute-banner { background-color: #f7f7f7; padding: 0.75rem 1rem; border: 1px solid #ddd; border-radius: 0.25rem; margin-bottom: 1rem; }
  </style>
</head>
<body>
      <div class="container my-4">
        <!-- (Optional) Institution Info Banner -->
        <div id="instituteBanner" class="institute-banner d-none">
          <strong id="instName" class="text-14"></strong> &mdash;
          <span id="instType" class="text-13"></span>
        </div>

        <!-- Summary Cards Row -->
        <div class="row mb-4">
          <!-- Total Agents -->
          <div class="col-12 col-md-4 mb-3 mb-md-0">
            <div class="card h-100" style="background-color: #fff; border-left: 5px solid #007bff;">
              <div class="card-body d-flex flex-column justify-content-center text-center">
                <h5 class="card-title text-primary">
                  <i class="fa-solid fa-users"></i> Total Agents
                </h5>
                <p class="card-text" id="totalAgentCount">
                  <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
                </p>
              </div>
            </div>
          </div>
          <!-- Active Agents -->
          <div class="col-12 col-md-4 mb-3 mb-md-0">
            <div class="card h-100" style="background-color: #fff; border-left: 5px solid #28a745;">
              <div class="card-body d-flex flex-column justify-content-center text-center">
                <h5 class="card-title text-success">
                  <i class="fa-solid fa-check"></i> Active Agents
                </h5>
                <p class="card-text" id="activeAgentCount">
                  <span class="spinner-border spinner-border-sm text-success" role="status" aria-hidden="true"></span>
                </p>
              </div>
            </div>
          </div>
          <!-- Inactive Agents -->
          <div class="col-12 col-md-4">
            <div class="card h-100" style="background-color: #fff; border-left: 5px solid #dc3545;">
              <div class="card-body d-flex flex-column justify-content-center text-center">
                <h5 class="card-title text-danger">
                  <i class="fa-solid fa-times"></i> Inactive Agents
                </h5>
                <p class="card-text" id="inactiveAgentCount">
                  <span class="spinner-border spinner-border-sm text-danger" role="status" aria-hidden="true"></span>
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Agent List Accordion with Filter & Search -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5 class="text-14 mb-0">Agent List</h5>
              </div>
              <div class="card-body">
                <!-- Filter & Search Row -->
                <div class="d-flex justify-content-end mb-3">
                  <div class="me-2" style="width: 150px;">
                    <select id="statusFilter" class="form-select text-13">
                      <option value="all">All Agents</option>
                      <option value="active">Active Agents</option>
                      <option value="inactive">Inactive Agents</option>
                    </select>
                  </div>
                  <div class="flex-grow-1">
                    <input type="text" id="agentAccordionSearch" class="form-control text-13" placeholder="Search by name or email...">
                  </div>
                </div>
                <!-- Accordion -->
                <div class="accordion" id="agentAccordion">
                  <!-- Accordion items will be populated dynamically -->
                </div>
              </div>
            </div>
          </div>
        </div>

      </div><!-- End container -->

  <!-- Bootstrap Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Global variables for pagination
    const pageSize = 5;
    let activeCurrentPage = 1;
    let inactiveCurrentPage = 1;

    document.addEventListener("DOMContentLoaded", function() {
      if (!sessionStorage.getItem("token")) {
        window.location.href = "/";
      }
      // Fetch agents when the page loads.
      fetchAgents();
      // Re-populate accordion on filter or search change.
      document.getElementById("statusFilter").addEventListener("change", () => {
        activeCurrentPage = 1;
        inactiveCurrentPage = 1;
        populateAgentAccordion();
      });
      document.getElementById("agentAccordionSearch").addEventListener("input", () => {
        activeCurrentPage = 1;
        inactiveCurrentPage = 1;
        populateAgentAccordion();
      });
    });

    const token = sessionStorage.getItem("token");
    let agentsData = [];

    // Fetch agents from the API.
    function fetchAgents() {
      const url = "/api/view-agents";
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
          agentsData = data.data;
          updateAgentCounts();
          populateAgentAccordion();
        } else {
          console.error("Error fetching agents:", data.message);
        }
      })
      .catch(error => console.error("Error:", error));
    }

    // Update summary counts.
    function updateAgentCounts() {
      const total = agentsData.length;
      const active = agentsData.filter(agent => agent.status === "Active").length;
      const inactive = total - active;
      document.getElementById("totalAgentCount").textContent = total;
      document.getElementById("activeAgentCount").textContent = active;
      document.getElementById("inactiveAgentCount").textContent = inactive;
    }

    // Generate HTML table for a given agents list with pagination.
    // 'group' is either 'active' or 'inactive'
    function generateAgentTable(agents, currentPage, group) {
      let totalPages = Math.ceil(agents.length / pageSize);
      if(totalPages === 0) totalPages = 1; // Ensure at least one page
      const startIndex = (currentPage - 1) * pageSize;
      const endIndex = startIndex + pageSize;
      const agentsPage = agents.slice(startIndex, endIndex);

      let tableHTML = `<div class="table-responsive">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th class="text-secondary text-13">Name</th>
              <th class="text-secondary text-13">Email</th>
              <th class="text-secondary text-13">Mobile</th>
              <th class="text-secondary text-13">WhatsApp</th>
              <th class="text-secondary text-13">Address</th>
              <th class="text-secondary text-13">PAN</th>
              <th class="text-secondary text-13">Password</th>
              <th class="text-secondary text-13">Status</th>
              <th class="text-secondary text-13">Created At</th>
              <th class="text-secondary text-13">Documents</th>
            </tr>
          </thead>
          <tbody>`;
      if (agentsPage.length === 0) {
        tableHTML += `<tr><td colspan="10" class="text-center text-secondary text-13">No agents found.</td></tr>`;
      } else {
        agentsPage.forEach((agent, index) => {
          // Construct the full address.
          let address = agent.street + ', ' + agent.post_office + ', ' + agent.police_station + ', ' + agent.city + ', ' + agent.state + ', ' + agent.country + ' - ' + agent.pincode;
          // Create an info icon button for address.
          let addressButton = `<button class="btn btn-link text-13 p-0" onclick="showAddressInfo('${encodeURIComponent(address)}')"><i class="fa fa-info-circle text-primary"></i></button>`;
          
          // Prepare document file names from path.
          let panFileName = agent.pan_card_path.split('/').pop();
          let aadharFileName = agent.aadhar_card_path.split('/').pop();
          // Create a file icon button for documents.
          let documentButton = `<button class="btn btn-link text-13 p-0" onclick="showDocumentInfo('${encodeURIComponent(agent.pan_card_path)}','${encodeURIComponent(agent.aadhar_card_path)}','${panFileName}','${aadharFileName}')"><i class="fa fa-file text-primary"></i></button>`;
          
          // Status styling: green if Active, red otherwise.
          let statusHTML = `<span class="${agent.status === 'Active' ? 'text-success' : 'text-danger'}">${agent.status}</span>`;
          
          // Password toggle input and button.
          let passwordHTML = `
            <div class="input-group">
              <input type="password" class="form-control form-control-sm text-13" id="${group}-password-${startIndex+index}" value="${agent.plain_password}" readonly style="border: none; background: transparent;">
              <span class="input-group-text p-0" style="cursor: pointer;" onclick="togglePassword('${group}-password-${startIndex+index}', '${group}-toggle-icon-${startIndex+index}')">
                <i class="fa fa-eye" id="${group}-toggle-icon-${startIndex+index}"></i>
              </span>
            </div>
          `;
          
          tableHTML += `
            <tr class="agent-row" data-agent-name="${agent.name.toLowerCase()}" data-agent-email="${agent.email.toLowerCase()}">
              <td class="text-13">${agent.name}</td>
              <td class="text-13">${agent.email}</td>
              <td class="text-13">${agent.mobile}</td>
              <td class="text-13">${agent.whatsapp}</td>
              <td class="text-13">${addressButton}</td>
              <td class="text-13">${agent.pan}</td>
              <td class="text-13">${passwordHTML}</td>
              <td class="text-13">${statusHTML}</td>
              <td class="text-13">${agent.created_at}</td>
              <td class="text-13">${documentButton}</td>
            </tr>
          `;
        });
      }
      tableHTML += `</tbody></table></div>`;
      
      // Append pagination controls with First, Previous, Page label, Next, and Last buttons.
      tableHTML += `<div class="d-flex justify-content-center align-items-center my-3 gap-3 ">
        <button class="btn btn-sm btn-outline-primary" onclick="changePage('${group}', 1)" ${currentPage === 1 ? 'disabled' : ''}>
          <i class="fa-solid fa-angles-left"></i>
        </button>
        <button class="btn btn-sm btn-outline-primary" onclick="changePage('${group}', ${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
          <i class="fa-solid fa-angle-left"></i>
        </button>
        <span class="text-13">Page ${currentPage} / ${totalPages}</span>
        <button class="btn btn-sm btn-outline-primary" onclick="changePage('${group}', ${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
          <i class="fa-solid fa-angle-right"></i>
        </button>
        <button class="btn btn-sm btn-outline-primary" onclick="changePage('${group}', ${totalPages})" ${currentPage === totalPages ? 'disabled' : ''}>
          <i class="fa-solid fa-angles-right"></i>
        </button>
      </div>`;
      
      return tableHTML;
    }

    // Change page for a given group (active or inactive) and repopulate the accordion.
    function changePage(group, newPage) {
      if (group === 'active') {
        activeCurrentPage = newPage;
      } else {
        inactiveCurrentPage = newPage;
      }
      populateAgentAccordion();
    }

    // Populate the accordion with agent data.
    function populateAgentAccordion() {
      const accordion = document.getElementById("agentAccordion");
      const filterVal = document.getElementById("statusFilter").value;
      const searchQuery = document.getElementById("agentAccordionSearch").value.toLowerCase();

      // Filter agents.
      let activeAgents = agentsData.filter(agent => agent.status === "Active");
      let inactiveAgents = agentsData.filter(agent => agent.status !== "Active");

      // Apply search filtering.
      activeAgents = activeAgents.filter(agent =>
        agent.name.toLowerCase().includes(searchQuery) || agent.email.toLowerCase().includes(searchQuery)
      );
      inactiveAgents = inactiveAgents.filter(agent =>
        agent.name.toLowerCase().includes(searchQuery) || agent.email.toLowerCase().includes(searchQuery)
      );

      // Re-calculate total pages and adjust current page if needed.
      let activeTotalPages = Math.ceil(activeAgents.length / pageSize) || 1;
      let inactiveTotalPages = Math.ceil(inactiveAgents.length / pageSize) || 1;
      if (activeCurrentPage > activeTotalPages) activeCurrentPage = activeTotalPages;
      if (inactiveCurrentPage > inactiveTotalPages) inactiveCurrentPage = inactiveTotalPages;

      let accordionHTML = "";
      if (filterVal === "all" || filterVal === "active") {
        accordionHTML += `
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingActive">
              <button class="accordion-button text-13" type="button" data-bs-toggle="collapse" data-bs-target="#collapseActive" aria-expanded="true" aria-controls="collapseActive">
                Active Agents (${activeAgents.length})
              </button>
            </h2>
            <div id="collapseActive" class="accordion-collapse collapse show" aria-labelledby="headingActive" data-bs-parent="#agentAccordion">
              <div class="accordion-body p-0">
                ${generateAgentTable(activeAgents, activeCurrentPage, 'active')}
              </div>
            </div>
          </div>
        `;
      }
      if (filterVal === "all" || filterVal === "inactive") {
        accordionHTML += `
          <div class="accordion-item">
            <h2 class="accordion-header" id="headingInactive">
              <button class="accordion-button collapsed text-13" type="button" data-bs-toggle="collapse" data-bs-target="#collapseInactive" aria-expanded="false" aria-controls="collapseInactive">
                Inactive Agents (${inactiveAgents.length})
              </button>
            </h2>
            <div id="collapseInactive" class="accordion-collapse collapse" aria-labelledby="headingInactive" data-bs-parent="#agentAccordion">
              <div class="accordion-body p-0">
                ${generateAgentTable(inactiveAgents, inactiveCurrentPage, 'inactive')}
              </div>
            </div>
          </div>
        `;
      }
      accordion.innerHTML = accordionHTML;
    }

    // Toggle password visibility.
    function togglePassword(inputId, iconId) {
      var input = document.getElementById(inputId);
      var icon = document.getElementById(iconId);
      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
      } else {
        input.type = "password";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
      }
    }

    // Open document in new tab. Prefix the document path with the current origin.
    function openDocument(path) {
      window.open(window.location.origin + "/" + decodeURIComponent(path), "_blank");
    }

    // Show address info in a SweetAlert popup.
    function showAddressInfo(encodedAddress) {
      let address = decodeURIComponent(encodedAddress);
      Swal.fire({
        title: '<i class="fa fa-info-circle text-primary"></i> Full Address',
        html: `<p class="text-13">${address}</p>`,
        confirmButtonText: 'Close'
      });
    }

    // Show document info in a SweetAlert popup.
    function showDocumentInfo(encodedPanPath, encodedAadharPath, panFileName, aadharFileName) {
      let panPath = decodeURIComponent(encodedPanPath);
      let aadharPath = decodeURIComponent(encodedAadharPath);
      Swal.fire({
        title: '<i class="fa fa-file text-primary"></i> Document Details',
        html: `<div class="text-13">
                <p><i class="fa fa-file text-secondary"></i> PAN:
                <button class="btn btn-sm btn-outline-secondary ms-2" onclick="openDocument('${encodedPanPath}')">View</button></p>
                <p><i class="fa fa-file text-secondary"></i> Aadhar:
                <button class="btn btn-sm btn-outline-secondary ms-2" onclick="openDocument('${encodedAadharPath}')">View</button></p>
              </div>`,
        showCloseButton: true,
        focusConfirm: false,
        confirmButtonText: 'Close'
      });
    }
  </script>
</body>
</html>
