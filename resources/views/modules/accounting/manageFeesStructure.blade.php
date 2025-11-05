<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Fees Structure</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="{{ asset('css/admin/addfees.css') }}">
  <style>
    .icon-rotate { animation: rotation 2s infinite linear; }
    @keyframes rotation { from { transform: rotate(0deg); } to { transform: rotate(359deg); } }
    /* Additional custom styling if needed */
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

        <p class="my-4 text-secondary text-14">
          <i class="fa-solid fa-angle-right"></i> 
          <span class="text-primary">Fees Structure</span>
        </p>

        <!-- Institution Dropdown -->
        <div class="bg-white p-4 rounded mb-4" id="institutionDropdownContainer">
          <label for="institutionSelect" class="form-label text-13">Select Institution</label>
          <select id="institutionSelect" class="form-select text-13">
            <option value="" disabled selected>Loading institutions...</option>
          </select>
        </div>
        <div id="default_feesstructure_div" class="p-4 bg-white rounded text-center d-flex flex-column justify-content-center align-items-center vh-50">
            <img src="{{ asset('assets/web_assets/search.png') }}" alt="" style="width: 300px;">
            <p class="fs-5">Select an Institute first</p>
        </div>
        <!-- Fees Structure Form Container (Add New Fee Head & View List) -->
        <div id="feesFormContainer" class="container mt-4 rounded bg-white position-relative mb-5 pt-2 d-none">
          <p class="mb-4 text-secondary text-14">
            <i class="fa-solid fa-angle-right"></i> 
            <span class="text-primary">Add Fees Structure</span>
          </p>
          <form id="feesForm" class="p-3 rounded" style="background-color: #f9fafc; box-shadow: rgba(0,0,0,0.02) 0px 1px 3px 0px, rgba(27,31,35,0.15) 0px 0px 0px 1px;">
            <!-- Dynamic Fields Header -->
            <div class="d-flex justify-content-between align-items-center">
              <h6 class="text-secondary mb-0">Head of Account</h6>
              <button type="button" id="addFeeHeadButton" class="btn btn-outline-primary btn-sm shadow-sm">
                <i class="fa-regular fa-plus"></i> Add
              </button>
            </div>
            <!-- Dynamic Fields Container -->
            <div id="dynamicFieldsContainer">
              <!-- New fee head rows will be appended here -->
            </div>
            <div class="col-md-12 text-end">
              <button type="button" id="submitFeesButton" class="btn btn-outline-secondary text-13 d-none">Submit</button>
              <button type="button" id="canceladdFeesStructureButton" onclick="canceladdFeesStructure()" class="btn btn-outline-danger text-13 ms-2 d-none">Cancel</button>
            </div>
          </form>
          <!-- Existing Fees Table -->
          <div id="feesTableContainer" class="mt-4">
            <p class="mb-4 text-secondary text-14">
              <i class="fa-solid fa-angle-right"></i> 
              <span class="text-primary">Fees Structure</span>
            </p>
            <div class="table-responsive">
              <table class="table text-center" id="feesTable">
                <thead class="table-light">
                  <!-- Optional headers -->
                </thead>
                <tbody>
                  <!-- Fee structure rows will be appended here -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

  <!-- JavaScript Section -->
  <script>
    // Redirect if token is missing
    document.addEventListener("DOMContentLoaded", () => {
      if (!sessionStorage.getItem("token")) {
        window.location.href = "/";
      }
    });
    const token = sessionStorage.getItem('token');

    // Show institution info if available in sessionStorage
    document.addEventListener("DOMContentLoaded", () => {
        // sessionStorage.setItem("institution_id", '67b07d58896e5461600f07a2');
        // sessionStorage.setItem("institution_name", 'MSIT');
        // sessionStorage.setItem("institution_type", 'COLLAGE');
        // sessionStorage.removeItem("institution_id");
        // sessionStorage.removeItem("institution_name");
        // sessionStorage.removeItem("institution_type");

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

    // Fetch Institutions and populate dropdown
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

    // On Institution change, fetch fee structures
    document.addEventListener("DOMContentLoaded", () => {
      fetchInstitutions();
      document.getElementById('institutionSelect').addEventListener('change', function() {
        let institute_id = this.value;
        console.log("Selected Institution ID:", institute_id);
        fetchFeesStructures(institute_id);
      });
    });

    // Add Fee Head (Dynamic Row)
    const addFeeHeadButton = document.getElementById("addFeeHeadButton");
    const dynamicFieldsContainer = document.getElementById("dynamicFieldsContainer");
    addFeeHeadButton.addEventListener("click", () => {
      const newRow = document.createElement("div");
      newRow.classList.add("row", "g-3", "align-items-end", "mb-3");
      newRow.innerHTML = `
        <div class="col-md-6">
          <label class="form-label text-13">Head of Account<span class="text-danger">*</span></label>
          <input type="text" class="form-control text-13" placeholder="Enter Head of Account" required>
        </div>
        <div class="col-md-5">
          <label class="form-label text-13">Fees Type<span class="text-danger">*</span></label>
          <select class="form-control text-13" required>
            <option value="" disabled selected>Select Type</option>
            <option value="one-time">One Time</option>
            <option value="semester-wise">Semester Wise</option>
            <option value="other">Other</option>
          </select>
        </div>
        <div class="col-md-1 text-end">
          <button type="button" class="btn btn-outline-danger btn-sm removeFieldButton text-13 w-100">
            <i class="fa-solid fa-trash"></i>
          </button>
        </div>
      `;
      document.getElementById("canceladdFeesStructureButton").classList.remove('d-none');
      document.getElementById("submitFeesButton").classList.remove('d-none');
      dynamicFieldsContainer.appendChild(newRow);
      newRow.querySelector(".removeFieldButton").addEventListener("click", () => {
        dynamicFieldsContainer.removeChild(newRow);
      });
    });

    // Submit New Fees Structure
    document.getElementById("submitFeesButton").addEventListener("click", () => {
      const allFields = [];
      const rows = dynamicFieldsContainer.querySelectorAll(".row");
      const institution_id = document.getElementById('institutionSelect').value;
      rows.forEach(row => {
        const headOfAccount = row.querySelector("input").value;
        const feesType = row.querySelector("select").value;
        if (headOfAccount && feesType) {
          allFields.push({ head_of_account: headOfAccount, type: feesType });
        }
      });
      if (allFields.length === 0) {
        Swal.fire({ title: 'Error', text: 'Please add at least one fee head.', icon: 'error', confirmButtonText: 'OK' });
        return;
      }
      fetch("/api/add-fees-structure", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
          "Authorization": token
        },
        body: JSON.stringify({ institute_id: institution_id, fees: allFields }),
      })
      .then(response => {
        if ([401,403].includes(response.status)) {
          window.location.href = '/Unauthorised';
          throw new Error('Unauthorized Access');
        }
        return response.json();
      })
      .then(data => {
        if (data.status === "success") {
          Swal.fire({ title: 'Success', text: data.message, icon: 'success', confirmButtonText: 'OK' })
          .then(() => { dynamicFieldsContainer.innerHTML = ""; fetchFeesStructures(institution_id); });
          document.getElementById("canceladdFeesStructureButton").classList.add('d-none');
          document.getElementById("submitFeesButton").classList.add('d-none');
        } else {
          Swal.fire({ title: 'Error', text: data.message, icon: 'error', confirmButtonText: 'OK' });
        }
      })
      .catch(error => {
        console.error("Error submitting fees:", error);
        Swal.fire({ title: 'Error', text: 'An error occurred while submitting the form.', icon: 'error', confirmButtonText: 'OK' });
      });
    });

    function canceladdFeesStructure(){
      dynamicFieldsContainer.innerHTML = "";
      document.getElementById("canceladdFeesStructureButton").classList.add('d-none');
      document.getElementById("submitFeesButton").classList.add('d-none');
    }

    // Fetch Existing Fees Structure
    function fetchFeesStructures(instituteId) {
    document.getElementById('feesFormContainer').classList.remove('d-none');
      fetch(`/api/view-fees-structure?institute_id=${encodeURIComponent(instituteId)}`, {
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
        if (data.status === "success") {
          populateFeesTable(data.data);
        } else {
          console.error("Error fetching fees structure");
        }
      })
      .catch(error => console.error("Error:", error));
    }

    // Populate Fees Table with fetched data
    function populateFeesTable(fees) {
      const feesTableBody = document.querySelector("#feesTable tbody");
      feesTableBody.innerHTML = "";
      document.getElementById("default_feesstructure_div").classList.add("d-none");
      if (fees.length === 0) {
        feesTableBody.innerHTML = `<tr><td colspan="4" class="text-center">No Fee Structure Found.</td></tr>`;
        return;
      }
      fees.forEach(fee => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td class="text-13 align-middle text-start">${fee.head_of_account}</td>
          <td class="text-13 align-middle text-center">
            <select class="form-control form-control-sm updateType text-13 w-auto" data-id="${fee.id.$oid}" disabled>
              <option value="one-time" ${fee.type === "one-time" ? "selected" : ""}>One Time</option>
              <option value="semester-wise" ${fee.type === "semester-wise" ? "selected" : ""}>Semester Wise</option>
              <option value="other" ${fee.type === "other" ? "selected" : ""}>Other</option>
            </select>
          </td>
          <td class="text-13 align-middle text-start">
            <span class="fee-status ${fee.status === 'Active' ? 'text-success' : 'text-danger'}" data-id="${fee.id.$oid}">${fee.status}</span>
          </td>
          <td class="text-13 align-middle text-end">
            <div class="d-flex justify-content-end align-items-center gap-2">
              <button class="btn btn-outline-primary editButton text-13" data-id="${fee.id.$oid}">
                <i class="fa-regular fa-pen-to-square me-1"></i>
              </button>
              <button class="btn btn-outline-success saveButton text-13 d-none" onclick="editFees('${fee.id.$oid}')" data-id="${fee.id.$oid}">
                <i class="fa-regular fa-save me-1"></i>
              </button>
              <button class="btn toggleStatusButton text-13" data-id="${fee.id.$oid}" onclick="toggleFeeStructureStatus('${fee.id.$oid}', '${fee.status}')" style="color: ${fee.status === 'Active' ? 'red' : 'green'}; border-color: ${fee.status === 'Active' ? 'red' : 'green'};">
                <i class="fa-solid ${fee.status === 'Active' ? 'fa-ban' : 'fa-power-off'} me-1"></i>
              </button>
            </div>
          </td>
        `;
        feesTableBody.appendChild(row);
      });
      // Attach Edit Button Events
      document.querySelectorAll(".editButton").forEach(button => {
        button.addEventListener("click", event => {
          const feeId = event.target.getAttribute("data-id");
          const typeDropdown = document.querySelector(`select[data-id="${feeId}"]`);
          const saveButton = document.querySelector(`.saveButton[data-id="${feeId}"]`);
          const editButton = document.querySelector(`.editButton[data-id="${feeId}"]`);
          typeDropdown.disabled = false;
          saveButton.classList.remove("d-none");
          editButton.classList.add("d-none");
        });
      });
    }

    // Edit Fees Structure API call
    function editFees(feesId) {
      const selectedType = document.querySelector(`.updateType[data-id="${feesId}"]`).value;
      if (!selectedType) {
        Swal.fire({ title: 'Error', text: 'Please select a valid type before saving.', icon: 'error', confirmButtonText: 'OK' });
        return;
      }
      fetch(`/api/edit-fee-structure/${feesId}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Authorization': token
        },
        body: JSON.stringify({ type: selectedType }),
      })
      .then(response => {
        if ([401,403].includes(response.status)) {
          window.location.href = '/Unauthorised';
          throw new Error('Unauthorized Access');
        }
        return response.json();
      })
      .then(data => {
        if (data.status === 'success') {
          Swal.fire({ title: 'Success', text: data.message, icon: 'success', confirmButtonText: 'OK' })
          .then(() => { fetchFeesStructures(document.getElementById('institutionSelect').value); });
        } else {
          throw new Error(data.message);
        }
      })
      .catch(error => {
        Swal.fire({ title: 'Error', text: error.message || 'An error occurred while updating the fee structure.', icon: 'error', confirmButtonText: 'OK' });
        console.error('Error updating fees:', error);
      });
    }

    // Toggle Fee Structure Status
    function toggleFeeStructureStatus(feeStructureId, currentStatus) {
      Swal.fire({
        title: `Are you sure?`,
        text: `Do you want to toggle the status of this fee structure?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: `Yes, Toggle`,
        cancelButtonText: "Cancel"
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/api/toggle-fee-structure/${feeStructureId}`, {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Authorization': token
            }
          })
          .then(response => {
            if ([401,403].includes(response.status)) {
              window.location.href = '/Unauthorised';
              throw new Error('Unauthorized Access');
            }
            return response.json();
          })
          .then(data => {
            if (data.status === "success") {
              Swal.fire({ title: "Success", text: `Fee structure status toggled successfully!`, icon: "success", confirmButtonText: 'OK' })
              .then(() => {
                const statusText = document.querySelector(`.fee-status[data-id="${feeStructureId}"]`);
                const toggleButton = document.querySelector(`.toggleStatusButton[data-id="${feeStructureId}"]`);
                if (currentStatus === 'Active') {
                  statusText.textContent = "Inactive";
                  statusText.classList.remove("text-success");
                  statusText.classList.add("text-danger");
                  toggleButton.innerHTML = `<i class="fa-solid fa-power-off me-1"></i>`;
                  toggleButton.style.color = "green";
                  toggleButton.style.borderColor = "green";
                  toggleButton.setAttribute("onclick", `toggleFeeStructureStatus('${feeStructureId}', 'Inactive')`);
                } else {
                  statusText.textContent = "Active";
                  statusText.classList.remove("text-danger");
                  statusText.classList.add("text-success");
                  toggleButton.innerHTML = `<i class="fa-solid fa-ban me-1"></i>`;
                  toggleButton.style.color = "red";
                  toggleButton.style.borderColor = "red";
                  toggleButton.setAttribute("onclick", `toggleFeeStructureStatus('${feeStructureId}', 'Active')`);
                }
              });
            } else {
              Swal.fire("Error", data.message, "error");
            }
          })
          .catch(error => {
            console.error("Error:", error);
            Swal.fire("Error", "Something went wrong. Please try again.", "error");
          });
        }
      });
    }

  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
