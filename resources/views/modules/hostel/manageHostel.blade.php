<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Manage Hostels</title>
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
          <span class="text-primary">Manage Hostels</span>
        </p>
        <!-- Institution Dropdown -->
        <div class="bg-white p-4 rounded mb-4" id="institutionDropdownContainer">
          <label for="institutionSelect" class="form-label text-13">Select Institution</label>
          <select id="institutionSelect" class="form-select text-13">
            <option value="" disabled selected>Loading institutions...</option>
          </select>
        </div>

        <!-- Default Message (Shown by default) -->
        <div id="default_hostel_div" class="p-4 bg-white rounded text-center d-flex flex-column justify-content-center align-items-center vh-50">
          <img src="{{ asset('assets/web_assets/search.png') }}" alt="Search Icon" style="width: 300px;">
          <p class="fs-5">Select an Institution first</p>
        </div>

        <!-- Hostel Management Container (Hidden until an institution is selected) -->
        <div id="hostelContainer" class="d-none rounded bg-white p-4">
          <!-- Hostel List Header containing Tabs, Search and Add Button -->
          <div id="hostelListHeader">
            <!-- Tabs (Chrome‑style, at the very top) -->
            <ul class="nav nav-tabs mb-3" id="hostelTabNavigation" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active text-13" id="active-hostel-tab" data-bs-toggle="tab" data-bs-target="#activeHostels" type="button" role="tab" aria-controls="activeHostels" aria-selected="true">Active</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link text-13" id="inactive-hostel-tab" data-bs-toggle="tab" data-bs-target="#inactiveHostels" type="button" role="tab" aria-controls="inactiveHostels" aria-selected="false">Inactive</button>
              </li>
            </ul>
            <!-- Row with Search Bar and Add Hostel Button (col-6 each) -->
            <div class="row mb-3">
              <div class="col-md-6">
                <div class="position-relative">
                  <input type="text" id="hostelSearchInput" class="form-control placeholder-14 text-13 ps-5" placeholder="Search by Hostel Name or Type" onkeyup="filterHostelTable()">
                  <i class="fa-solid fa-search position-absolute text-secondary text-13" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
                </div>
              </div>
              <div class="col-md-6 text-end">
                <button id="addHostelBtn" class="btn btn-outline-primary btn-sm text-13">Add Hostel</button>
              </div>
            </div>
          </div>
          <!-- Tabs Content for Hostel Table -->
          <div class="tab-content" id="hostelTabContent">
            <!-- Active Hostels Tab -->
            <div class="tab-pane fade show active" id="activeHostels" role="tabpanel" aria-labelledby="active-hostel-tab">
              <div id="hostelTableContainer" class="">
                <div class="table-responsive">
                  <table class="table" id="hostelTable">
                    <thead class="table-light">
                      <tr>
                        <th class="text-secondary text-13">Hostel Name</th>
                        <th class="text-secondary text-13">Hostel Type</th>
                        <th class="text-secondary text-13">Hostel Address</th>
                        <th class="text-secondary text-13">Hostel Fees</th>
                        <th class="text-secondary text-13">Capacity</th>
                        <th class="text-secondary text-13 text-center">Status</th>
                        <th class="text-secondary text-13 text-end">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Hostel rows will be appended dynamically -->
                    </tbody>
                  </table>
                </div>
                <div id="hostelPaginationContainer" class="mt-3 text-center"></div>
              </div>
            </div>
            <!-- Inactive Hostels Tab -->
            <div class="tab-pane fade" id="inactiveHostels" role="tabpanel" aria-labelledby="inactive-hostel-tab">
              <div id="hostelTableContainerInactive" class="">
                <div class="table-responsive">
                  <table class="table" id="hostelTableInactive">
                    <thead class="table-light">
                      <tr>
                        <th class="text-secondary text-13">Hostel Name</th>
                        <th class="text-secondary text-13">Hostel Type</th>
                        <th class="text-secondary text-13">Hostel Address</th>
                        <th class="text-secondary text-13">Hostel Fees</th>
                        <th class="text-secondary text-13">Capacity</th>
                        <th class="text-secondary text-13 text-center">Status</th>
                        <th class="text-secondary text-13 text-end">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Hostel rows will be appended dynamically -->
                    </tbody>
                  </table>
                </div>
                <div id="hostelPaginationContainerInactive" class="mt-3 text-center"></div>
              </div>
            </div>
          </div>
          <!-- Hostel Form Container (Hidden by Default) -->
          <div id="hostelFormContainer" class="d-none position-relative bg-white p-4 rounded" style="box-shadow: rgba(0,0,0,0.02) 0px 1px 3px 0px, rgba(27,31,35,0.15) 0px 0px 0px 1px;">
            <!-- Back to List Button -->
            <button id="cancelHostelBtn" class="btn btn-outline-secondary btn-sm position-absolute" style="top: 10px; right: 10px;">Back to List</button>
            <form id="hostelForm" enctype="multipart/form-data">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="hostelName" class="form-label text-13">Hostel Name <span class="text-danger">*</span></label>
                  <input type="text" id="hostelName" class="form-control text-13" placeholder="Enter Hostel Name" required>
                </div>
                <div class="col-md-6">
                    <label for="hostelType" class="form-label text-13">
                      Hostel Type <span class="text-danger">*</span>
                    </label>
                    <select id="hostelType" class="form-control text-13" required>
                      <option value="">Select Hostel Type</option>
                      <option value="hat">Hat</option>
                      <option value="girls">Girls</option>
                      <option value="boys">Boys</option>
                      <option value="quet">Quet</option>
                    </select>
                  </div>                  
              </div>
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="hostelAddress" class="form-label text-13">Hostel Address <span class="text-danger">*</span></label>
                  <input type="text" id="hostelAddress" class="form-control text-13" placeholder="Enter Hostel Address" required>
                </div>
                <div class="col-md-3">
                  <label for="hostelFees" class="form-label text-13">Hostel Fees <span class="text-danger">*</span></label>
                  <input type="number" step="0.01" id="hostelFees" class="form-control text-13" placeholder="Enter Fees" required>
                </div>
                <div class="col-md-3">
                  <label for="hostelCapacity" class="form-label text-13">Hostel Capacity <span class="text-danger">*</span></label>
                  <input type="number" id="hostelCapacity" class="form-control text-13" placeholder="Enter Capacity" required>
                </div>
              </div>
              <div class="text-end">
                <button type="button" id="saveHostelBtn" class="btn btn-outline-primary text-13">Save Hostel</button>
              </div>
            </form>
          </div>
        </div><!-- End Hostel Management Container -->
      </div><!-- End Container -->

  <!-- Bootstrap Bundle JS & SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Global variables.
    const token = sessionStorage.getItem('token');
    let allHostelsData = [];
    let currentPageHostels = 1;
    const rowsPerPageHostels = 5;
    let hostelEditId = null;

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

    // When an institution is selected, hide default message, show hostel container, and fetch hostels.
    document.addEventListener("DOMContentLoaded", () => {
      fetchInstitutions();
      document.getElementById('institutionSelect').addEventListener('change', function() {
        const institute_id = this.value;
        console.log("Selected Institution ID:", institute_id);
        document.getElementById("default_hostel_div").classList.add("d-none");
        document.getElementById("hostelContainer").classList.remove("d-none");
        fetchHostels(institute_id);
      });
    });

    // --- Hostel Table Functions ---
    function fetchHostels(instituteId) {
      fetch(`/api/hostel/view?institution_id=${encodeURIComponent(instituteId)}`, {
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
          allHostelsData = data.data;
          currentPageHostels = 1;
          updateHostelTable();
        } else {
          console.error("Error fetching hostels", data.message);
        }
      })
      .catch(error => console.error("Error:", error));
    }

    function updateHostelTable() {
      const searchValue = document.getElementById("hostelSearchInput").value.toLowerCase();
      const activeData = allHostelsData.filter(h =>
        h.status === "Active" &&
        (h.hostel_name.toLowerCase().includes(searchValue) ||
         h.hostel_type.toLowerCase().includes(searchValue))
      );
      const inactiveData = allHostelsData.filter(h =>
        h.status === "Inactive" &&
        (h.hostel_name.toLowerCase().includes(searchValue) ||
         h.hostel_type.toLowerCase().includes(searchValue))
      );
      renderHostelTable(activeData, "hostelTable", "hostelPaginationContainer");
      renderHostelTable(inactiveData, "hostelTableInactive", "hostelPaginationContainerInactive");
    }

    function renderHostelTable(data, tableId, paginationContainerId) {
      const tbody = document.querySelector(`#${tableId} tbody`);
      tbody.innerHTML = "";
      if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center">No Hostels Found.</td></tr>`;
        document.getElementById(paginationContainerId).innerHTML = "";
        return;
      }
      const totalPages = Math.ceil(data.length / rowsPerPageHostels);
      if (currentPageHostels > totalPages) currentPageHostels = totalPages;
      const start = (currentPageHostels - 1) * rowsPerPageHostels;
      const paginatedData = data.slice(start, start + rowsPerPageHostels);
      paginatedData.forEach(hostel => {
        const hostelId = hostel.id?.$oid || hostel.id;
        const row = document.createElement("tr");
        row.classList.add("hostel-row");
        row.innerHTML = `
          <td class="text-13 align-middle">${hostel.hostel_name}</td>
          <td class="text-13 align-middle">${hostel.hostel_type}</td>
          <td class="text-13 align-middle">${hostel.hostel_address}</td>
          <td class="text-13 align-middle">${hostel.hostel_fees}</td>
          <td class="text-13 align-middle">${hostel.hostel_capacity}</td>
          <td class="text-13 align-middle text-center">
            <span class="hostel-status ${hostel.status === 'Active' ? 'text-success' : 'text-danger'}">${hostel.status}</span>
          </td>
          <td class="text-13 align-middle text-end">
            <button class="btn btn-outline-primary btn-sm hostel-edit-btn" data-id="${hostelId}">
              <i class="fa-solid fa-pen-to-square"></i>
            </button>
            <button class="btn ${hostel.status === 'Active' ? 'btn-outline-danger' : 'btn-outline-success'} btn-sm hostel-toggle-btn" data-id="${hostelId}" onclick="toggleHostelStatus('${hostelId}', '${hostel.status}')">
              <i class="fa-solid ${hostel.status === 'Active' ? 'fa-ban' : 'fa-power-off'}"></i>
            </button>
          </td>
        `;
        tbody.appendChild(row);
      });
      renderHostelPagination(data, paginationContainerId);
      document.querySelectorAll(".hostel-edit-btn").forEach(button => {
        button.addEventListener("click", () => {
          hostelEditId = button.getAttribute("data-id");
          const row = button.closest("tr");
          document.getElementById("hostelName").value = row.cells[0].textContent.trim();
          document.getElementById("hostelType").value = row.cells[1].textContent.trim();
          document.getElementById("hostelAddress").value = row.cells[2].textContent.trim();
          document.getElementById("hostelFees").value = row.cells[3].textContent.trim();
          document.getElementById("hostelCapacity").value = row.cells[4].textContent.trim();
          toggleHostelView(true);
        });
      });
    }

    function renderHostelPagination(data, containerId) {
      const paginationContainer = document.getElementById(containerId);
      if(data.length === 0) {
        paginationContainer.innerHTML = "";
        return;
      }
      paginationContainer.innerHTML = "";
      const totalPages = Math.ceil(data.length / rowsPerPageHostels);
      function createButton(innerHTML, page, disabled = false) {
        const btn = document.createElement("button");
        btn.innerHTML = innerHTML;
        btn.classList.add("btn", "btn-outline-primary", "mx-1", "text-13");
        btn.disabled = disabled;
        if (!disabled) {
          btn.addEventListener("click", function() {
            currentPageHostels = page;
            updateHostelTable();
          });
        }
        return btn;
      }
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angles-left"></i>', 1, currentPageHostels === 1));
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angle-left"></i>', currentPageHostels - 1, currentPageHostels === 1));
      const pageLabel = createButton(`${currentPageHostels} / ${totalPages}`, currentPageHostels, true);
      paginationContainer.appendChild(pageLabel);
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angle-right"></i>', currentPageHostels + 1, currentPageHostels === totalPages));
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angles-right"></i>', totalPages, currentPageHostels === totalPages));
    }

    function filterHostelTable() {
      updateHostelTable();
    }

    // --- Hostel Form Functions ---
    document.getElementById("addHostelBtn").addEventListener("click", () => {
      clearHostelForm();
      hostelEditId = null;
      toggleHostelView(true);
    });

    document.getElementById("cancelHostelBtn").addEventListener("click", () => {
      toggleHostelView(false);
    });

    document.getElementById("saveHostelBtn").addEventListener("click", () => {
      const institution_id = document.getElementById('institutionSelect').value;
      const hostelName = document.getElementById("hostelName").value.trim();
      const hostelType = document.getElementById("hostelType").value.trim();
      const hostelAddress = document.getElementById("hostelAddress").value.trim();
      const hostelFees = document.getElementById("hostelFees").value.trim();
      const hostelCapacity = document.getElementById("hostelCapacity").value.trim();
      if (!hostelName || !hostelType || !hostelAddress || !hostelFees || !hostelCapacity) {
        Swal.fire({ title: 'Error', text: 'Please fill in all required fields.', icon: 'error', confirmButtonText: 'OK' });
        return;
      }
      const payload = {
        institution_id,
        hostel_name: hostelName,
        hostel_type: hostelType,
        hostel_address: hostelAddress,
        hostel_fees: hostelFees,
        hostel_capacity: hostelCapacity,
      };
      const saveBtn = document.getElementById("saveHostelBtn");
      saveBtn.disabled = true;
      const originalBtnContent = saveBtn.innerHTML;
      saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

      if (hostelEditId) {
        fetch(`/api/hostel/edit/${hostelEditId}`, {
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
              toggleHostelView(false);
              fetchHostels(institution_id);
              hostelEditId = null;
            });
          } else {
            throw new Error(data.message);
          }
        })
        .catch(error => {
          console.error("Error updating hostel:", error);
          Swal.fire({ title: 'Error', text: error.message || 'An error occurred while updating the hostel.', icon: 'error', confirmButtonText: 'OK' });
        })
        .finally(() => {
          saveBtn.disabled = false;
          saveBtn.innerHTML = originalBtnContent;
        });
      } else {
        fetch("/api/hostel/add", {
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
              toggleHostelView(false);
              fetchHostels(institution_id);
            });
          } else {
            Swal.fire({ title: 'Error', text: data.message, icon: 'error', confirmButtonText: 'OK' });
          }
        })
        .catch(error => {
          console.error("Error adding hostel:", error);
          Swal.fire({ title: 'Error', text: 'An error occurred while adding the hostel.', icon: 'error', confirmButtonText: 'OK' });
        })
        .finally(() => {
          saveBtn.disabled = false;
          saveBtn.innerHTML = originalBtnContent;
        });
      }
    });

    function clearHostelForm() {
      document.getElementById("hostelName").value = "";
      document.getElementById("hostelType").value = "";
      document.getElementById("hostelAddress").value = "";
      document.getElementById("hostelFees").value = "";
      document.getElementById("hostelCapacity").value = "";
      hostelEditId = null;
    }

    function toggleHostelView(showForm) {
      if (showForm) {
        document.getElementById("hostelFormContainer").classList.remove("d-none");
        document.getElementById("hostelListHeader").classList.add("d-none");
        document.getElementById("hostelTabContent").classList.add("d-none");
      } else {
        document.getElementById("hostelFormContainer").classList.add("d-none");
        document.getElementById("hostelListHeader").classList.remove("d-none");
        document.getElementById("hostelTabContent").classList.remove("d-none");
      }
    }

    function toggleHostelStatus(hostelId, currentStatus) {
      Swal.fire({
        title: 'Are you sure?',
        text: `Do you really want to change the status to ${currentStatus === 'Active' ? 'Inactive' : 'Active'}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, change it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/api/hostel/toggle/${hostelId}`, {
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
                fetchHostels(institute_id);
              });
            } else {
              Swal.fire({ title: 'Error', text: data.message, icon: 'error', confirmButtonText: 'OK' });
            }
          })
          .catch(error => {
            console.error("Error toggling hostel status:", error);
            Swal.fire({ title: 'Error', text: 'An error occurred while toggling the hostel status.', icon: 'error', confirmButtonText: 'OK' });
          });
        }
      });
    }
  </script>
</body>
</html>
