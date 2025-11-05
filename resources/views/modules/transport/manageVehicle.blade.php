<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Manage Vehicles</title>
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
            <span class="text-primary">Manage Vehicles</span>
        </p>
        <!-- Institution Dropdown -->
        <div class="bg-white p-4 rounded mb-4" id="institutionDropdownContainer">
          <label for="institutionSelect" class="form-label text-13">Select Institution</label>
          <select id="institutionSelect" class="form-select text-13">
            <option value="" disabled selected>Loading institutions...</option>
          </select>
        </div>

        <!-- Default Message (Shown by default) -->
        <div id="default_vehicle_div" class="p-4 bg-white rounded text-center d-flex flex-column justify-content-center align-items-center vh-50">
          <img src="{{ asset('assets/web_assets/search.png') }}" alt="Search Icon" style="width: 300px;">
          <p class="fs-5">Select an Institution first</p>
        </div>

        <!-- Vehicle Management Container (Hidden until an institution is selected) -->
        <div id="vehicleContainer" class="d-none rounded bg-white p-4">
          <!-- NEW: Vehicle List Header containing Tabs, Search and Add Button -->
          <div id="vehicleListHeader">
            <!-- Tabs (Chrome‑style, outside container line) -->
            <ul class="nav nav-tabs mb-3" id="vehicleTabNavigation" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active text-13" id="active-tab" data-bs-toggle="tab" data-bs-target="#activeVehicles" type="button" role="tab" aria-controls="activeVehicles" aria-selected="true">Active</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link text-13" id="inactive-tab" data-bs-toggle="tab" data-bs-target="#inactiveVehicles" type="button" role="tab" aria-controls="inactiveVehicles" aria-selected="false">Inactive</button>
                </li>
              </ul>
            <!-- Row with Search Bar and Add Button -->
            <div class="row mb-3">
              <div class="col-md-6">
                <div class="position-relative">
                  <input type="text" id="vehicleSearchInput" class="form-control placeholder-14 text-13 ps-5" placeholder="Search by Vehicle Number or Driver Name" onkeyup="filterVehicleTable()">
                  <i class="fa-solid fa-search position-absolute text-secondary text-13" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
                </div>
              </div>
              <div class="col-md-6 text-end">
                <button id="addVehicleBtn" class="btn btn-outline-primary btn-sm text-13">Add Vehicle</button>
              </div>
            </div>
          </div>
          <!-- Tabs Content for Vehicle Table -->
          <div class="tab-content" id="vehicleTabContent">
            <!-- Active Vehicles Tab -->
            <div class="tab-pane fade show active" id="activeVehicles" role="tabpanel" aria-labelledby="active-tab">
              <div id="vehicleTableContainer" class="">
                <div class="table-responsive">
                  <table class="table" id="vehicleTable">
                    <thead class="table-light">
                      <tr>
                        <th class="text-secondary text-13">Vehicle Number</th>
                        <th class="text-secondary text-13">Vehicle Model</th>
                        <th class="text-secondary text-13">Driver Name</th>
                        <th class="text-secondary text-13">Driver Phone</th>
                        <th class="text-secondary text-13">Note</th>
                        <th class="text-secondary text-13 text-center">Status</th>
                        <th class="text-secondary text-13 text-end">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Vehicle rows will be appended dynamically -->
                    </tbody>
                  </table>
                </div>
                <!-- Pagination Controls (if needed) -->
                <div id="vehiclePaginationContainer" class="mt-3 text-center"></div>
              </div>
            </div>
            <!-- Inactive Vehicles Tab -->
            <div class="tab-pane fade" id="inactiveVehicles" role="tabpanel" aria-labelledby="inactive-tab">
              <div id="vehicleTableContainerInactive" class="">
                <div class="table-responsive">
                  <table class="table" id="vehicleTableInactive">
                    <thead class="table-light">
                      <tr>
                        <th class="text-secondary text-13">Vehicle Number</th>
                        <th class="text-secondary text-13">Vehicle Model</th>
                        <th class="text-secondary text-13">Driver Name</th>
                        <th class="text-secondary text-13">Driver Phone</th>
                        <th class="text-secondary text-13">Note</th>
                        <th class="text-secondary text-13 text-center">Status</th>
                        <th class="text-secondary text-13 text-end">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Vehicle rows will be appended dynamically -->
                    </tbody>
                  </table>
                </div>
                <!-- Pagination Controls (if needed) -->
                <div id="vehiclePaginationContainerInactive" class="mt-3 text-center"></div>
              </div>
            </div>
          </div>
          <!-- Vehicle Form Container (Hidden by Default) -->
          <div id="vehicleFormContainer" class="d-none position-relative bg-white p-4 rounded" style="box-shadow: rgba(0,0,0,0.02) 0px 1px 3px 0px, rgba(27,31,35,0.15) 0px 0px 0px 1px;">
            <!-- Back to List Button -->
            <button id="cancelVehicleBtn" class="btn btn-outline-secondary btn-sm position-absolute" style="top: 10px; right: 10px;">
              Back to List
            </button>
            <form id="vehicleForm" enctype="multipart/form-data">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="vehicleNumber" class="form-label text-13">Vehicle Number <span class="text-danger">*</span></label>
                  <input type="text" id="vehicleNumber" class="form-control text-13" placeholder="Enter Vehicle Number" required>
                </div>
                <div class="col-md-6">
                  <label for="vehicleModel" class="form-label text-13">Vehicle Model <span class="text-danger">*</span></label>
                  <input type="text" id="vehicleModel" class="form-control text-13" placeholder="Enter Vehicle Model" required>
                </div>
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="driverName" class="form-label text-13">Driver Name <span class="text-danger">*</span></label>
                  <input type="text" id="driverName" class="form-control text-13" placeholder="Enter Driver Name" required>
                </div>
                <div class="col-md-6">
                  <label for="driverPhone" class="form-label text-13">Driver Phone <span class="text-danger">*</span></label>
                  <input type="text" id="driverPhone" class="form-control text-13" placeholder="Enter Driver Phone" required>
                </div>
              </div>
              <div class="mb-3">
                <label for="vehicleNote" class="form-label text-13">Note</label>
                <textarea id="vehicleNote" class="form-control text-13" placeholder="Enter Note (Optional)" rows="3"></textarea>
              </div>
              <div class="text-end">
                <button type="button" id="saveVehicleBtn" class="btn btn-outline-primary text-13">Save Vehicle</button>
              </div>
            </form>
          </div>
        </div><!-- End Vehicle Management Container -->
      </div><!-- End Container -->

  <!-- Bootstrap Bundle JS & SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Global variables.
    const token = sessionStorage.getItem('token');
    let allVehiclesData = [];
    let currentPageVehicles = 1;
    const rowsPerPageVehicles = 5;
    let vehicleEditId = null;

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

    // When an institution is selected, hide default message, show vehicle container, and fetch vehicles.
    document.addEventListener("DOMContentLoaded", () => {
      fetchInstitutions();
      document.getElementById('institutionSelect').addEventListener('change', function() {
        const institute_id = this.value;
        console.log("Selected Institution ID:", institute_id);
        document.getElementById("default_vehicle_div").classList.add("d-none");
        document.getElementById("vehicleContainer").classList.remove("d-none");
        fetchVehicles(institute_id);
      });
    });

    // --- Vehicle Table Functions ---
    function fetchVehicles(instituteId) {
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
          allVehiclesData = data.data;
          currentPageVehicles = 1;
          updateVehicleTable();
        } else {
          console.error("Error fetching vehicles", data.message);
        }
      })
      .catch(error => console.error("Error:", error));
    }

    function updateVehicleTable() {
      const searchValue = document.getElementById("vehicleSearchInput").value.toLowerCase();
      const activeData = allVehiclesData.filter(v =>
        v.status === "Active" &&
        (v.vehicle_number.toLowerCase().includes(searchValue) || v.vehicle_model.toLowerCase().includes(searchValue) || v.driver_name.toLowerCase().includes(searchValue))
      );
      const inactiveData = allVehiclesData.filter(v =>
        v.status === "Inactive" &&
        (v.vehicle_number.toLowerCase().includes(searchValue) || v.vehicle_model.toLowerCase().includes(searchValue) || v.driver_name.toLowerCase().includes(searchValue))
      );
      renderVehicleTable(activeData, "vehicleTable", "vehiclePaginationContainer");
      renderVehicleTable(inactiveData, "vehicleTableInactive", "vehiclePaginationContainerInactive");
    }

    function renderVehicleTable(data, tableId, paginationContainerId) {
      const tbody = document.querySelector(`#${tableId} tbody`);
      tbody.innerHTML = "";
      if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center">No Vehicles Found.</td></tr>`;
        document.getElementById(paginationContainerId).innerHTML = "";
        return;
      }
      // Apply pagination on filtered data.
      const totalPages = Math.ceil(data.length / rowsPerPageVehicles);
      if (currentPageVehicles > totalPages) currentPageVehicles = totalPages;
      const start = (currentPageVehicles - 1) * rowsPerPageVehicles;
      const paginatedData = data.slice(start, start + rowsPerPageVehicles);
      paginatedData.forEach(vehicle => {
        const vehicleId = vehicle.id?.$oid || vehicle.id;
        const row = document.createElement("tr");
        row.classList.add("vehicle-row");
        row.innerHTML = `
          <td class="text-13 align-middle">${vehicle.vehicle_number}</td>
          <td class="text-13 align-middle">${vehicle.vehicle_model}</td>
          <td class="text-13 align-middle">${vehicle.driver_name}</td>
          <td class="text-13 align-middle">${vehicle.driver_phone}</td>
          <td class="text-13 align-middle">${vehicle.note || ''}</td>
          <td class="text-13 align-middle text-center">
            <span class="vehicle-status ${vehicle.status === 'Active' ? 'text-success' : 'text-danger'}">${vehicle.status}</span>
          </td>
          <td class="text-13 align-middle text-end">
            <button class="btn btn-outline-primary btn-sm vehicle-edit-btn" data-id="${vehicleId}">
              <i class="fa-solid fa-pen-to-square"></i>
            </button>
            <button class="btn ${vehicle.status === 'Active' ? 'btn-outline-danger' : 'btn-outline-success'} btn-sm vehicle-toggle-btn" data-id="${vehicleId}" onclick="toggleVehicleStatus('${vehicleId}', '${vehicle.status}')">
                <i class="fa-solid ${vehicle.status === 'Active' ? 'fa-ban' : 'fa-power-off'}"></i>
            </button>

          </td>
        `;
        tbody.appendChild(row);
      });
      renderVehiclePagination(data, paginationContainerId);
      document.querySelectorAll(".vehicle-edit-btn").forEach(button => {
        button.addEventListener("click", () => {
          vehicleEditId = button.getAttribute("data-id");
          const row = button.closest("tr");
          const vehicleNum = row.cells[0].textContent.trim();
          const vehicleMod = row.cells[1].textContent.trim();
          const driver = row.cells[2].textContent.trim();
          const phone = row.cells[3].textContent.trim();
          const note = row.cells[4].textContent.trim();
          document.getElementById("vehicleNumber").value = vehicleNum;
          document.getElementById("vehicleModel").value = vehicleMod;
          document.getElementById("driverName").value = driver;
          document.getElementById("driverPhone").value = phone;
          document.getElementById("vehicleNote").value = note;
          toggleVehicleView(true);
        });
      });
    }

    function renderVehiclePagination(data, containerId) {
      const paginationContainer = document.getElementById(containerId);
      if(data.length === 0) {
        paginationContainer.innerHTML = "";
        return;
      }
      paginationContainer.innerHTML = "";
      const totalPages = Math.ceil(data.length / rowsPerPageVehicles);
      function createButton(innerHTML, page, disabled = false) {
        const btn = document.createElement("button");
        btn.innerHTML = innerHTML;
        btn.classList.add("btn", "btn-outline-primary", "mx-1", "text-13");
        btn.disabled = disabled;
        if (!disabled) {
          btn.addEventListener("click", function() {
            currentPageVehicles = page;
            updateVehicleTable();
          });
        }
        return btn;
      }
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angles-left"></i>', 1, currentPageVehicles === 1));
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angle-left"></i>', currentPageVehicles - 1, currentPageVehicles === 1));
      const pageLabel = createButton(`${currentPageVehicles} / ${totalPages}`, currentPageVehicles, true);
      paginationContainer.appendChild(pageLabel);
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angle-right"></i>', currentPageVehicles + 1, currentPageVehicles === totalPages));
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angles-right"></i>', totalPages, currentPageVehicles === totalPages));
    }

    function filterVehicleTable() {
      updateVehicleTable();
    }

    // --- Vehicle Form Functions ---
    document.getElementById("addVehicleBtn").addEventListener("click", () => {
      clearVehicleForm();
      vehicleEditId = null;
      toggleVehicleView(true);
    });

    document.getElementById("cancelVehicleBtn").addEventListener("click", () => {
      toggleVehicleView(false);
    });

    document.getElementById("saveVehicleBtn").addEventListener("click", () => {
      const institution_id = document.getElementById('institutionSelect').value;
      const vehicleNumber = document.getElementById("vehicleNumber").value.trim();
      const vehicleModel  = document.getElementById("vehicleModel").value.trim();
      const driverName    = document.getElementById("driverName").value.trim();
      const driverPhone   = document.getElementById("driverPhone").value.trim();
      const note          = document.getElementById("vehicleNote").value.trim();
      if (!vehicleNumber || !vehicleModel || !driverName || !driverPhone) {
        Swal.fire({ title: 'Error', text: 'Please fill in all required fields.', icon: 'error', confirmButtonText: 'OK' });
        return;
      }
      const payload = {
        institution_id,
        vehicle_number: vehicleNumber,
        vehicle_model: vehicleModel,
        driver_name: driverName,
        driver_phone: driverPhone,
        note: note
      };
      const saveBtn = document.getElementById("saveVehicleBtn");
      saveBtn.disabled = true;
      const originalBtnContent = saveBtn.innerHTML;
      saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

      if (vehicleEditId) {
        fetch(`/api/vahicle/edit/${vehicleEditId}`, {
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
              toggleVehicleView(false);
              fetchVehicles(institution_id);
              vehicleEditId = null;
            });
          } else {
            throw new Error(data.message);
          }
        })
        .catch(error => {
          console.error("Error updating vehicle:", error);
          Swal.fire({ title: 'Error', text: error.message || 'An error occurred while updating the vehicle.', icon: 'error', confirmButtonText: 'OK' });
        })
        .finally(() => {
          saveBtn.disabled = false;
          saveBtn.innerHTML = originalBtnContent;
        });
      } else {
        fetch("/api/vahicle/add", {
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
              toggleVehicleView(false);
              fetchVehicles(institution_id);
            });
          } else {
            Swal.fire({ title: 'Error', text: data.message, icon: 'error', confirmButtonText: 'OK' });
          }
        })
        .catch(error => {
          console.error("Error adding vehicle:", error);
          Swal.fire({ title: 'Error', text: 'An error occurred while adding the vehicle.', icon: 'error', confirmButtonText: 'OK' });
        })
        .finally(() => {
          saveBtn.disabled = false;
          saveBtn.innerHTML = originalBtnContent;
        });
      }
    });

    function clearVehicleForm() {
      document.getElementById("vehicleNumber").value = "";
      document.getElementById("vehicleModel").value = "";
      document.getElementById("driverName").value = "";
      document.getElementById("driverPhone").value = "";
      document.getElementById("vehicleNote").value = "";
      vehicleEditId = null;
    }

    function toggleVehicleView(showForm) {
      if (showForm) {
        document.getElementById("vehicleFormContainer").classList.remove("d-none");
        document.getElementById("vehicleListHeader").classList.add("d-none");
        document.getElementById("vehicleTabContent").classList.add("d-none");
      } else {
        document.getElementById("vehicleFormContainer").classList.add("d-none");
        document.getElementById("vehicleListHeader").classList.remove("d-none");
        document.getElementById("vehicleTabContent").classList.remove("d-none");
      }
    }

    function toggleVehicleStatus(vehicleId, currentStatus) {
      Swal.fire({
        title: 'Are you sure?',
        text: `Do you really want to change the status to ${currentStatus === 'Active' ? 'Inactive' : 'Active'}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, change it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/api/vahicle/toggle/${vehicleId}`, {
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
                fetchVehicles(institute_id);
              });
            } else {
              Swal.fire({ title: 'Error', text: data.message, icon: 'error', confirmButtonText: 'OK' });
            }
          })
          .catch(error => {
            console.error("Error toggling vehicle status:", error);
            Swal.fire({ title: 'Error', text: 'An error occurred while toggling the vehicle status.', icon: 'error', confirmButtonText: 'OK' });
          });
        }
      });
    }
  </script>
</body>
</html>
