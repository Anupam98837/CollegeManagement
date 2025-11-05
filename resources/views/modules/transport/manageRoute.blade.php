<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Manage Transport Routes</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Custom CSS -->
    <style>
        .icon-rotate { animation: rotation 2s infinite linear; }
        @keyframes rotation {
            from { transform: rotate(0deg); }
            to { transform: rotate(359deg); }
        }
        .d-none { display: none; }
    </style>
</head>
<body>
      <div class="container mt-4">
        <!-- Institution Info Card -->
        <div id="institutionInfoDiv" class="bg-white p-2 rounded d-none">
          <div class="card text-center border-0">
            <div class="card-body">
              <img 
                src="/assets/web_assets/logo.png" 
                id="instImg" 
                alt="Institution Logo" 
                width="100px" 
                style="object-fit: contain; display: block; margin: 0 auto;" 
              />
              <h5 class="card-title fs-3" id="instituteName">
                <span class="text-secondary">Loading Institution...</span>
              </h5>
              <p class="card-text fs-4" id="instituteType">
                <i class="fa-solid fa-graduation-cap me-2"></i>
                Loading Type...
              </p>
            </div>
          </div>
        </div>

        <p class="mt-4 text-secondary text-14">
          <i class="fa-solid fa-angle-right"></i>
          <span class="text-primary">Manage Transport Routes</span>
        </p>
        <!-- Institution Dropdown -->
        <div class="bg-white p-4 rounded mb-4" id="institutionDropdownContainer">
          <label for="institutionSelect" class="form-label text-13">Select Institution</label>
          <select id="institutionSelect" class="form-select text-13">
            <option value="" disabled selected>Loading institutions...</option>
          </select>
        </div>

        <!-- Default Message (Shown by default) -->
        <div id="default_route_div" class="p-4 bg-white rounded text-center d-flex flex-column justify-content-center align-items-center vh-50">
          <img src="{{ asset('assets/web_assets/search.png') }}" alt="Search Icon" style="width: 300px;">
          <p class="fs-5">Select an Institution first</p>
        </div>

        
        <!-- Route Management Container (Hidden until an institution is selected) -->
        <div id="routeContainer" class="d-none rounded bg-white p-4">
          <!-- Route List Header containing Tabs, Search and Add Button -->
          <div id="routeListHeader">
            <ul class="nav nav-tabs mb-3" id="routeTabNavigation" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active text-13" id="active-route-tab" data-bs-toggle="tab" data-bs-target="#activeRoutes" type="button" role="tab" aria-controls="activeRoutes" aria-selected="true">Active</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link text-13" id="inactive-route-tab" data-bs-toggle="tab" data-bs-target="#inactiveRoutes" type="button" role="tab" aria-controls="inactiveRoutes" aria-selected="false">Inactive</button>
                </li>
              </ul>
            <!-- Row with Search Bar and Add Route Button (col-6 each) -->
            <div class="row mb-3">
              <div class="col-md-6">
                <div class="position-relative">
                  <input type="text" id="routeSearchInput" class="form-control placeholder-14 text-13 ps-5" placeholder="Search by Route or Period" onkeyup="filterRouteTable()">
                  <i class="fa-solid fa-search position-absolute text-secondar text-13" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
                </div>
              </div>
              <div class="col-md-6 text-end">
                <button id="addRouteBtn" class="btn btn-outline-primary btn-sm text-13">Add Route</button>
              </div>
            </div>
          </div>
          <!-- Tabs Content for Route Table -->
          <div class="tab-content" id="routeTabContent">
            <!-- Active Routes Tab -->
            <div class="tab-pane fade show active" id="activeRoutes" role="tabpanel" aria-labelledby="active-route-tab">
              <div id="routeTableContainer" class="">
                <div class="table-responsive">
                  <table class="table" id="routeTable">
                    <thead class="table-light">
                      <tr>
                        <th class="text-secondary text-13">Route From</th>
                        <th class="text-secondary text-13">Route To</th>
                        <th class="text-secondary text-13">Fare</th>
                        <th class="text-secondary text-13">Period</th>
                        <th class="text-secondary text-13">Transport Vehicle</th>
                        <th class="text-secondary text-13 text-center">Status</th>
                        <th class="text-secondary text-13 text-end">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Route rows will be appended dynamically -->
                    </tbody>
                  </table>
                </div>
                <div id="routePaginationContainer" class="mt-3 text-center"></div>
              </div>
            </div>
            <!-- Inactive Routes Tab -->
            <div class="tab-pane fade" id="inactiveRoutes" role="tabpanel" aria-labelledby="inactive-route-tab">
              <div id="routeTableContainerInactive" class="">
                <div class="table-responsive">
                  <table class="table" id="routeTableInactive">
                    <thead class="table-light">
                      <tr>
                        <th class="text-secondary text-13">Route From</th>
                        <th class="text-secondary text-13">Route To</th>
                        <th class="text-secondary text-13">Fare</th>
                        <th class="text-secondary text-13">Period</th>
                        <th class="text-secondary text-13">Transport Vehicle</th>
                        <th class="text-secondary text-13 text-center">Status</th>
                        <th class="text-secondary text-13 text-end">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Route rows will be appended dynamically -->
                    </tbody>
                  </table>
                </div>
                <div id="routePaginationContainerInactive" class="mt-3 text-center"></div>
              </div>
            </div>
          </div>
          <!-- Route Form Container (Hidden by Default) -->
          <div id="routeFormContainer" class="d-none position-relative bg-white p-4 rounded" style="box-shadow: rgba(0,0,0,0.02) 0px 1px 3px 0px, rgba(27,31,35,0.15) 0px 0px 0px 1px;">
            <!-- Back to List Button -->
            <button id="cancelRouteBtn" class="btn btn-outline-secondary btn-sm position-absolute" style="top: 10px; right: 10px;">Back to List</button>
            <form id="routeForm" enctype="multipart/form-data">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="routeFrom" class="form-label text-13">Route From <span class="text-danger">*</span></label>
                  <input type="text" id="routeFrom" class="form-control text-13" placeholder="Enter Route From" required>
                </div>
                <div class="col-md-6">
                  <label for="routeTo" class="form-label text-13">Route To <span class="text-danger">*</span></label>
                  <input type="text" id="routeTo" class="form-control text-13" placeholder="Enter Route To" required>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-4">
                  <label for="routeFare" class="form-label text-13">Route Fare <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" id="routeFare" class="form-control text-13" placeholder="Enter Fare" required>
                </div>
                <div class="col-md-4">
                    <label for="period" class="form-label text-13">Period <span class="text-danger">*</span></label>
                    <select id="period" class="form-select text-13" required>
                         <option value="" disabled selected>Select Period</option>
                         <option value="1 day">1 day</option>
                         <option value="1 month">1 month</option>
                         <option value="3 month">3 month</option>
                         <option value="4 month">4 month</option>
                         <option value="6 month">6 month</option>
                         <option value="12 month">12 month</option>
                    </select>
                </div>                
                <div class="col-md-4">
                  <label for="transportVehicleSelect" class="form-label text-13">Transport Vehicle <span class="text-danger">*</span></label>
                  <select id="transportVehicleSelect" class="form-select text-13" required>
                    <option value="" disabled selected>Select Vehicle</option>
                    <!-- Options populated from already added vehicles -->
                  </select>
                </div>
              </div>
              <div class="text-end">
                <button type="button" id="saveRouteBtn" class="btn btn-outline-primary text-13">Save Route</button>
              </div>
            </form>
          </div>
        </div><!-- End Route Management Container -->
      </div><!-- End Container -->

  <!-- Bootstrap Bundle JS & SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Global variables.
    const token = sessionStorage.getItem('token');
    let allRoutesData = [];
    let currentPageRoutes = 1;
    const rowsPerPageRoutes = 5;
    let routeEditId = null;
    let allVehiclesForRoutes = [];

    // Redirect if token is missing.
    document.addEventListener("DOMContentLoaded", () => {
      if (!token) {
        window.location.href = "/";
      }
    });

    // Show institution info if available.
    document.addEventListener("DOMContentLoaded", () => {
      const instName = sessionStorage.getItem("institution_name");
      const instType = sessionStorage.getItem("institution_type");
      const institutionInfoDiv = document.getElementById("institutionInfoDiv");
      if (instName && instType) {
        const instLogoPath = sessionStorage.getItem("institution_logo");
      const logoImg = document.getElementById("instImg");
      logoImg.src = instLogoPath || '/assets/web_assets/logo.png';
        document.getElementById("instituteName").innerHTML = `<span class="text-secondary">${instName}</span>`;
        document.getElementById("instituteType").innerHTML = `<i class="fa-solid fa-graduation-cap me-2"></i>${instType}`;
        institutionInfoDiv.classList.remove("d-none");
      }
    });

    // Fetch institutions and populate dropdown.
    function fetchInstitutions() {
      const institutionId = sessionStorage.getItem("institution_id");
      const institutionSelect = document.getElementById('institutionSelect');
      if (institutionId) {
        fetch(`/api/view-institution/${institutionId}`, {
          method: 'GET',
          headers: { 'Accept': 'application/json', 'Authorization': token }
        })
        .then(response => {
          if ([401,403].includes(response.status)) {
            window.location.href = '/Unauthorised';
            throw new Error('Unauthorized Access');
          }
          return response.json();
        })
        .then(data => {
          institutionSelect.innerHTML = '';
          if (data.status === 'success' && data.data) {
            const inst = data.data;
            const option = document.createElement('option');
            option.value = inst.id?.$oid || inst._id || inst.id;
            option.textContent = inst.institution_name;
            option.selected = true;
            institutionSelect.appendChild(option);
            institutionSelect.dispatchEvent(new Event('change'));
            document.getElementById("institutionDropdownContainer").style.display = 'none';
          } else {
            institutionSelect.innerHTML = '<option value="">No institutions available</option>';
          }
        })
        .catch(error => {
          console.error('Error fetching institution:', error);
          Swal.fire('Error', 'Failed to load institution.', 'error');
        });
      } else {
        fetch('/api/view-institutions', {
          method: 'GET',
          headers: { 'Accept': 'application/json', 'Authorization': token }
        })
        .then(response => {
          if ([401,403].includes(response.status)) {
            window.location.href = '/Unauthorised';
            throw new Error('Unauthorized Access');
          }
          return response.json();
        })
        .then(data => {
          institutionSelect.innerHTML = '<option value="" disabled selected>Select Institution</option>';
          if (data.status === 'success' && data.data.length > 0) {
            data.data.forEach(inst => {
              const option = document.createElement('option');
              option.value = inst.id?.$oid || inst._id || inst.id;
              option.textContent = inst.institution_name;
              institutionSelect.appendChild(option);
            });
          } else {
            institutionSelect.innerHTML = '<option value="">No institutions available</option>';
          }
        })
        .catch(error => {
          console.error('Error fetching institutions:', error);
          Swal.fire('Error', 'Failed to load institutions.', 'error');
        });
      }
    }

    // Fetch vehicles for routes dropdown.
    function fetchVehiclesForRoutes(instituteId) {
      fetch(`/api/vahicle/view?institution_id=${encodeURIComponent(instituteId)}`, {
        method: "GET",
        headers: { "Accept": "application/json", "Authorization": token }
      })
      .then(response => {
        if ([401,403].includes(response.status)) {
          window.location.href = '/Unauthorised';
          throw new Error("Unauthorized Access");
        }
        return response.json();
      })
      .then(data => {
        if (data.status === "success") {
          allVehiclesForRoutes = data.data;
          updateVehicleDropdownForRoutes();
        } else {
          console.error("Error fetching vehicles for routes", data.message);
        }
      })
      .catch(error => console.error("Error:", error));
    }

    function updateVehicleDropdownForRoutes() {
      const dropdown = document.getElementById("transportVehicleSelect");
      dropdown.innerHTML = '<option value="" disabled selected>Select Vehicle</option>';
      allVehiclesForRoutes.forEach(vehicle => {
        const option = document.createElement("option");
        option.value = JSON.stringify({
            id: vehicle.id?.$oid || vehicle.id,
            vehicle_number: vehicle.vehicle_number,
            vehicle_model: vehicle.vehicle_model,
            driver_name: vehicle.driver_name,
            driver_phone: vehicle.driver_phone
        });
        option.textContent = vehicle.vehicle_number;
        dropdown.appendChild(option);
      });
    }

    // When an institution is selected, hide default message, show route container, fetch routes and vehicles.
    document.addEventListener("DOMContentLoaded", () => {
      fetchInstitutions();
      document.getElementById('institutionSelect').addEventListener('change', function() {
        const institute_id = this.value;
        console.log("Selected Institution ID:", institute_id);
        document.getElementById("default_route_div").classList.add("d-none");
        document.getElementById("routeContainer").classList.remove("d-none");
        fetchRoutes(institute_id);
        fetchVehiclesForRoutes(institute_id);
      });
    });

    // --- Route Table Functions ---
    function fetchRoutes(instituteId) {
      fetch(`/api/transport-route/view?institution_id=${encodeURIComponent(instituteId)}`, {
        method: "GET",
        headers: { "Accept": "application/json", "Authorization": token }
      })
      .then(response => {
        if ([401,403].includes(response.status)) {
          window.location.href = '/Unauthorised';
          throw new Error("Unauthorized Access");
        }
        return response.json();
      })
      .then(data => {
        if (data.status === "success") {
          allRoutesData = data.data;
          currentPageRoutes = 1;
          updateRouteTable();
        } else {
          console.error("Error fetching routes", data.message);
        }
      })
      .catch(error => console.error("Error:", error));
    }

    function updateRouteTable() {
      const searchValue = document.getElementById("routeSearchInput").value.toLowerCase();
      const activeData = allRoutesData.filter(r =>
        r.status === "Active" &&
        (r.route_from.toLowerCase().includes(searchValue) || 
         r.route_to.toLowerCase().includes(searchValue) ||
         r.period.toLowerCase().includes(searchValue))
      );
      const inactiveData = allRoutesData.filter(r =>
        r.status === "Inactive" &&
        (r.route_from.toLowerCase().includes(searchValue) || 
         r.route_to.toLowerCase().includes(searchValue) ||
         r.period.toLowerCase().includes(searchValue))
      );
      renderRouteTable(activeData, "routeTable", "routePaginationContainer");
      renderRouteTable(inactiveData, "routeTableInactive", "routePaginationContainerInactive");
    }

    function renderRouteTable(data, tableId, paginationContainerId) {
      const tbody = document.querySelector(`#${tableId} tbody`);
      tbody.innerHTML = "";
      if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center">No Routes Found.</td></tr>`;
        document.getElementById(paginationContainerId).innerHTML = "";
        return;
      }
      const totalPages = Math.ceil(data.length / rowsPerPageRoutes);
      if (currentPageRoutes > totalPages) currentPageRoutes = totalPages;
      const start = (currentPageRoutes - 1) * rowsPerPageRoutes;
      const paginatedData = data.slice(start, start + rowsPerPageRoutes);
      paginatedData.forEach(route => {
        const routeId = route.id?.$oid || route.id;
        if(route.transport_vehicles){
          try {
            vehicleData = JSON.parse(route.transport_vehicles);
            // console.log(vehicleData);
            vehicleNumber = vehicleData.vehicle_number || 'N/A';
          } catch(e){
            vehicleNumber = 'N/A';
          }
        }
        const row = document.createElement("tr");
        row.classList.add("route-row");
        row.innerHTML = `
          <td class="text-13 align-middle">${route.route_from}</td>
          <td class="text-13 align-middle">${route.route_to}</td>
          <td class="text-13 align-middle">${route.route_fare}</td>
          <td class="text-13 align-middle">${route.period}</td>
          <td class="text-13 align-middle d-none">${route.transport_vehicles}</td>
          <td class="text-13 align-middle">
            ${vehicleNumber}
            ${vehicleData ? `<button type="button" class="btn btn-link p-0" data-vehicle='${route.transport_vehicles}' onclick="showVehicleInfo(this)"><i class="fa-solid fa-info-circle"></i></button>` : ''}
          </td>
          <td class="text-13 align-middle text-center">
            <span class="route-status ${route.status === 'Active' ? 'text-success' : 'text-danger'}">${route.status}</span>
          </td>
          <td class="text-13 align-middle text-end">
            <button class="btn btn-outline-primary btn-sm route-edit-btn" data-id="${routeId}">
              <i class="fa-solid fa-pen-to-square"></i>
            </button>
            <button class="btn ${route.status === 'Active' ? 'btn-outline-danger' : 'btn-outline-success'} btn-sm route-toggle-btn" data-id="${routeId}" onclick="toggleRouteStatus('${routeId}', '${route.status}')">
              <i class="fa-solid ${route.status === 'Active' ? 'fa-ban' : 'fa-power-off'}"></i>
            </button>
          </td>
        `;
        tbody.appendChild(row);
      });
      renderRoutePagination(data, paginationContainerId);
      document.querySelectorAll(".route-edit-btn").forEach(button => {
        button.addEventListener("click", () => {
          routeEditId = button.getAttribute("data-id");
          const row = button.closest("tr");
          document.getElementById("routeFrom").value = row.cells[0].textContent.trim();
          document.getElementById("routeTo").value = row.cells[1].textContent.trim();
          document.getElementById("routeFare").value = row.cells[2].textContent.trim();
          document.getElementById("period").value = row.cells[3].textContent.trim();
          const currentVehicle = row.cells[4].textContent.trim();
          document.getElementById("transportVehicleSelect").value = currentVehicle;
          toggleRouteView(true);
        });
      });
    }

    function renderRoutePagination(data, containerId) {
      const paginationContainer = document.getElementById(containerId);
      if(data.length === 0) {
        paginationContainer.innerHTML = "";
        return;
      }
      paginationContainer.innerHTML = "";
      const totalPages = Math.ceil(data.length / rowsPerPageRoutes);
      function createButton(innerHTML, page, disabled = false) {
        const btn = document.createElement("button");
        btn.innerHTML = innerHTML;
        btn.classList.add("btn", "btn-outline-primary", "mx-1", "text-13");
        btn.disabled = disabled;
        if (!disabled) {
          btn.addEventListener("click", function() {
            currentPageRoutes = page;
            updateRouteTable();
          });
        }
        return btn;
      }
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angles-left"></i>', 1, currentPageRoutes === 1));
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angle-left"></i>', currentPageRoutes - 1, currentPageRoutes === 1));
      const pageLabel = createButton(`${currentPageRoutes} / ${totalPages}`, currentPageRoutes, true);
      paginationContainer.appendChild(pageLabel);
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angle-right"></i>', currentPageRoutes + 1, currentPageRoutes === totalPages));
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angles-right"></i>', totalPages, currentPageRoutes === totalPages));
    }

    function filterRouteTable() {
      updateRouteTable();
    }

    // --- Route Form Functions ---
    document.getElementById("addRouteBtn").addEventListener("click", () => {
      clearRouteForm();
      routeEditId = null;
      toggleRouteView(true);
    });

    document.getElementById("cancelRouteBtn").addEventListener("click", () => {
      toggleRouteView(false);
    });

    document.getElementById("saveRouteBtn").addEventListener("click", () => {
      const institution_id = document.getElementById('institutionSelect').value;
      const routeFrom = document.getElementById("routeFrom").value.trim();
      const routeTo = document.getElementById("routeTo").value.trim();
      const routeFare = document.getElementById("routeFare").value.trim();
      const period = document.getElementById("period").value.trim();
      const transportVehicle = document.getElementById("transportVehicleSelect").value;
      if (!routeFrom || !routeTo || !routeFare || !period || !transportVehicle) {
        Swal.fire({ title: 'Error', text: 'Please fill in all required fields.', icon: 'error', confirmButtonText: 'OK' });
        return;
      }
      const payload = {
        institution_id,
        route_from: routeFrom,
        route_to: routeTo,
        route_fare: routeFare,
        period: period,
        transport_vehicles: transportVehicle
      };
      const saveBtn = document.getElementById("saveRouteBtn");
      saveBtn.disabled = true;
      const originalBtnContent = saveBtn.innerHTML;
      saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

      if (routeEditId) {
        fetch(`/api/transport-route/edit/${routeEditId}`, {
          method: 'PUT',
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            "Authorization": token
          },
          body: JSON.stringify(payload)
        })
        .then(response => {
          if ([401,403].includes(response.status)) {
            window.location.href = '/Unauthorised';
            throw new Error("Unauthorized Access");
          }
          return response.json();
        })
        .then(data => {
          if (data.status === "success") {
            Swal.fire({ title: 'Success', text: data.message, icon: 'success', confirmButtonText: 'OK' })
            .then(() => {
              toggleRouteView(false);
              fetchRoutes(institution_id);
              routeEditId = null;
            });
          } else {
            throw new Error(data.message);
          }
        })
        .catch(error => {
          console.error("Error updating route:", error);
          Swal.fire({ title: 'Error', text: error.message || 'An error occurred while updating the route.', icon: 'error', confirmButtonText: 'OK' });
        })
        .finally(() => {
          saveBtn.disabled = false;
          saveBtn.innerHTML = originalBtnContent;
        });
      } else {
        fetch("/api/transport-route/add", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            "Authorization": token
          },
          body: JSON.stringify(payload)
        })
        .then(response => {
          if ([401,403].includes(response.status)) {
            window.location.href = '/Unauthorised';
            throw new Error("Unauthorized Access");
          }
          return response.json();
        })
        .then(data => {
          if (data.status === "success") {
            Swal.fire({ title: 'Success', text: data.message, icon: 'success', confirmButtonText: 'OK' })
            .then(() => {
              toggleRouteView(false);
              fetchRoutes(institution_id);
            });
          } else {
            Swal.fire({ title: 'Error', text: data.message, icon: 'error', confirmButtonText: 'OK' });
          }
        })
        .catch(error => {
          console.error("Error adding route:", error);
          Swal.fire({ title: 'Error', text: 'An error occurred while adding the route.', icon: 'error', confirmButtonText: 'OK' });
        })
        .finally(() => {
          saveBtn.disabled = false;
          saveBtn.innerHTML = originalBtnContent;
        });
      }
    });

    function clearRouteForm() {
      document.getElementById("routeFrom").value = "";
      document.getElementById("routeTo").value = "";
      document.getElementById("routeFare").value = "";
      document.getElementById("period").value = "";
      document.getElementById("transportVehicleSelect").selectedIndex = 0;
      routeEditId = null;
    }

    function toggleRouteView(showForm) {
      if (showForm) {
        document.getElementById("routeFormContainer").classList.remove("d-none");
        document.getElementById("routeListHeader").classList.add("d-none");
        document.getElementById("routeTabContent").classList.add("d-none");
      } else {
        document.getElementById("routeFormContainer").classList.add("d-none");
        document.getElementById("routeListHeader").classList.remove("d-none");
        document.getElementById("routeTabContent").classList.remove("d-none");
      }
    }

    function toggleRouteStatus(routeId, currentStatus) {
      Swal.fire({
        title: 'Are you sure?',
        text: `Do you really want to change the status to ${currentStatus === 'Active' ? 'Inactive' : 'Active'}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, change it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/api/transport-route/toggle/${routeId}`, {
            method: 'PATCH',
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
              "Authorization": token
            }
          })
          .then(response => {
            if ([401,403].includes(response.status)) {
              window.location.href = '/Unauthorised';
              throw new Error("Unauthorized Access");
            }
            return response.json();
          })
          .then(data => {
            if (data.status === "success") {
              Swal.fire({ title: 'Success', text: data.message, icon: 'success', confirmButtonText: 'OK' })
              .then(() => {
                const institute_id = document.getElementById('institutionSelect').value;
                fetchRoutes(institute_id);
              });
            } else {
              Swal.fire({ title: 'Error', text: data.message, icon: 'error', confirmButtonText: 'OK' });
            }
          })
          .catch(error => {
            console.error("Error toggling route status:", error);
            Swal.fire({ title: 'Error', text: 'An error occurred while toggling the route status.', icon: 'error', confirmButtonText: 'OK' });
          });
        }
      });
    }
    function showVehicleInfo(button) {
      const vehicleJson = button.getAttribute('data-vehicle');
      let vehicleData;
      try {
        vehicleData = JSON.parse(vehicleJson);
      } catch(e){
        vehicleData = null;
      }
      if(vehicleData){
        Swal.fire({
          title: 'Vehicle Details',
          html: `<p><strong>Vehicle Model:</strong> ${vehicleData.vehicle_model || 'N/A'}</p>
                 <p><strong>Driver Name:</strong> ${vehicleData.driver_name || 'N/A'}</p>
                 <p><strong>Driver Phone:</strong> ${vehicleData.driver_phone || 'N/A'}</p>`
        });
      } else {
        Swal.fire({
          title: 'Vehicle Details',
          text: 'No data available.'
        });
      }
    }
  </script>
</body>
</html>
