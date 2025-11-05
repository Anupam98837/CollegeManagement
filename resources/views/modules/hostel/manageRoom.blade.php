<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Manage Hostel Rooms</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome (for icons) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" />
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
          <span class="text-primary">Manage Hostel Rooms</span>
        </p>
        
        <!-- Institution Dropdown -->
        <div class="bg-white p-4 rounded mb-4" id="institutionDropdownContainer">
          <label for="institutionSelect" class="form-label text-13">Select Institution</label>
          <select id="institutionSelect" class="form-select text-13">
            <option value="" disabled selected>Loading institutions...</option>
          </select>
        </div>
        
        <!-- Default Message (Shown by default) -->
        <div id="default_room_div" class="p-4 bg-white rounded text-center d-flex flex-column justify-content-center align-items-center vh-50">
          <img src="{{ asset('assets/web_assets/search.png') }}" alt="Search Icon" style="width: 300px;">
          <p class="fs-5">Select an Institution first</p>
        </div>
        
        <!-- Room Management Container (Hidden until an institution is selected) -->
        <div id="roomContainer" class="d-none rounded bg-white p-4">
          <!-- Room List Header containing Tabs, Search and Add Button -->
          <div id="roomListHeader">
            <!-- Tabs (Chrome‑style, at the very top) -->
            <ul class="nav nav-tabs mb-3" id="roomTabNavigation" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active text-13" id="active-room-tab" data-bs-toggle="tab" data-bs-target="#activeRooms" type="button" role="tab" aria-controls="activeRooms" aria-selected="true">Active</button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link text-13" id="inactive-room-tab" data-bs-toggle="tab" data-bs-target="#inactiveRooms" type="button" role="tab" aria-controls="inactiveRooms" aria-selected="false">Inactive</button>
              </li>
            </ul>
            <!-- Row with Search Bar and Add Room Button (col-6 each) -->
            <div class="row mb-3">
              <div class="col-md-6">
                <div class="position-relative">
                  <input type="text" id="roomSearchInput" class="form-control placeholder-14 text-13 ps-5" placeholder="Search by Room Number or Hostel" onkeyup="filterRoomTable()">
                  <i class="fa-solid fa-search position-absolute text-secondary text-13" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
                </div>
              </div>
              <div class="col-md-6 text-end">
                <button id="addRoomBtn" class="btn btn-outline-primary btn-sm text-13">Add Room</button>
              </div>
            </div>
          </div>
          
          <!-- Tabs Content for Room Accordion -->
          <div class="tab-content" id="roomTabContent">
            <!-- Active Rooms Tab -->
            <div class="tab-pane fade show active" id="activeRooms" role="tabpanel" aria-labelledby="active-room-tab">
              <!-- Accordion container for Active Rooms (grouped by Hostel) -->
              <div id="roomAccordionActive" class="accordion"></div>
            </div>
            <!-- Inactive Rooms Tab -->
            <div class="tab-pane fade" id="inactiveRooms" role="tabpanel" aria-labelledby="inactive-room-tab">
              <!-- Accordion container for Inactive Rooms (grouped by Hostel) -->
              <div id="roomAccordionInactive" class="accordion"></div>
            </div>
          </div>
          
          <!-- Room Form Container (Hidden by Default) -->
          <div id="roomFormContainer" class="d-none position-relative bg-white p-4 rounded" style="box-shadow: rgba(0,0,0,0.02) 0px 1px 3px 0px, rgba(27,31,35,0.15) 0px 0px 0px 1px;">
            <!-- Back to List Button -->
            <button id="cancelRoomBtn" class="btn btn-outline-secondary btn-sm position-absolute" style="top: 10px; right: 10px;">Back to List</button>
            <form id="roomForm" enctype="multipart/form-data">
              <div class="row mb-3">
                <div class="col-md-4">
                  <label for="roomNumber" class="form-label text-13">Room Number <span class="text-danger">*</span></label>
                  <input type="text" id="roomNumber" class="form-control text-13" placeholder="Enter Room Number" required>
                </div>
                <div class="col-md-4">
                  <label for="numberOfBeds" class="form-label text-13">Number of Beds <span class="text-danger">*</span></label>
                  <input type="number" id="numberOfBeds" class="form-control text-13" placeholder="Enter Number of Beds" required>
                </div>
                <div class="col-md-4">
                  <label for="roomHostelSelect" class="form-label text-13">Hostel <span class="text-danger">*</span></label>
                  <select id="roomHostelSelect" class="form-select text-13" required>
                    <option value="" disabled selected>Select Hostel</option>
                    <!-- Options populated from all hostels (as JSON) -->
                  </select>
                </div>
              </div>
              <div class="mb-3">
                <label for="roomNote" class="form-label text-13">Note</label>
                <textarea id="roomNote" class="form-control text-13" placeholder="Enter Note (Optional)" rows="3"></textarea>
              </div>
              <div class="text-end">
                <button type="button" id="saveRoomBtn" class="btn btn-outline-primary text-13">Save Room</button>
              </div>
            </form>
          </div>
        </div><!-- End Room Management Container -->
      </div><!-- End Container -->
  
  <!-- Bootstrap Bundle JS & SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Global variables.
    const token = sessionStorage.getItem('token');
    let allRoomsData = [];
    const rowsPerPageRooms = 5;
    let roomEditId = null;
    let allHostelsForRoom = [];
    // Global object to track the current page for each hostel group.
    let currentPageByHostel = {};

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
    
    // Fetch hostels for the room form drop-down.
    function fetchHostelsForRoom() {
      // Using getAllHostels endpoint.
      fetch('/api/hostel/all', {
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
          allHostelsForRoom = data.data;
          updateHostelDropdownForRoom();
        } else {
          console.error("Error fetching hostels for room", data.message);
        }
      })
      .catch(error => console.error("Error:", error));
    }
    
    function updateHostelDropdownForRoom() {
      const dropdown = document.getElementById("roomHostelSelect");
      dropdown.innerHTML = '<option value="" disabled selected>Select Hostel</option>';
      allHostelsForRoom.forEach(hostel => {
        const option = document.createElement("option");
        // Save hostel details as JSON string
        option.value = JSON.stringify({
          id: hostel.id?.$oid || hostel.id,
          hostel_name: hostel.hostel_name,
          hostel_type: hostel.hostel_type,
          hostel_address: hostel.hostel_address,
          hostel_fees: hostel.hostel_fees,
          hostel_capacity: hostel.hostel_capacity
        });
        option.textContent = hostel.hostel_name;
        dropdown.appendChild(option);
      });
    }
    
    // When an institution is selected, hide default message, show room container, fetch rooms and hostels.
    document.addEventListener("DOMContentLoaded", () => {
      fetchInstitutions();
      document.getElementById('institutionSelect').addEventListener('change', function() {
        const institute_id = this.value;
        console.log("Selected Institution ID:", institute_id);
        document.getElementById("default_room_div").classList.add("d-none");
        document.getElementById("roomContainer").classList.remove("d-none");
        // Reset pagination tracking for all hostel groups
        currentPageByHostel = {};
        fetchRooms(institute_id);
        fetchHostelsForRoom();
      });
    });
    
    // --- Room Table (Accordion) Functions ---
    function fetchRooms(instituteId) {
      fetch(`/api/room/view?institution_id=${encodeURIComponent(instituteId)}`, {
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
          allRoomsData = data.data;
          updateRoomTable();
        } else {
          console.error("Error fetching rooms", data.message);
        }
      })
      .catch(error => console.error("Error:", error));
    }
    
    function updateRoomTable() {
      const searchValue = document.getElementById("roomSearchInput").value.toLowerCase();
      const activeData = allRoomsData.filter(r =>
        r.status === "Active" &&
        (r.room_number.toLowerCase().includes(searchValue) || 
         r.note.toLowerCase().includes(searchValue))
      );
      const inactiveData = allRoomsData.filter(r =>
        r.status === "Inactive" &&
        (r.room_number.toLowerCase().includes(searchValue) || 
         r.note.toLowerCase().includes(searchValue))
      );
      renderRoomAccordion(activeData, "roomAccordionActive");
      renderRoomAccordion(inactiveData, "roomAccordionInactive");
    }
    
    // New function to render rooms as an accordion grouped by hostel.
    function renderRoomAccordion(data, containerId) {
      const container = document.getElementById(containerId);
      container.innerHTML = "";
      if(data.length === 0) {
        container.innerHTML = '<p class="text-center">No Rooms Found.</p>';
        return;
      }
      // Group rooms by hostel name.
      const grouped = {};
      data.forEach(room => {
        let hostelName = 'N/A';
        if (room.hostel) {
          try {
            let hostelData = JSON.parse(room.hostel);
            hostelName = hostelData.hostel_name || 'N/A';
          } catch (e) {
            hostelName = room.hostel;
          }
        }
        if (!grouped[hostelName]) grouped[hostelName] = [];
        grouped[hostelName].push(room);
      });
      // Build the accordion items.
      let counter = 0;
      for (const hostelName in grouped) {
        counter++;
        const groupRooms = grouped[hostelName];
        // Initialize pagination state for this group if not yet set.
        if (typeof currentPageByHostel[hostelName] === 'undefined') {
          currentPageByHostel[hostelName] = 1;
        }
        const currentPage = currentPageByHostel[hostelName];
        const totalPages = Math.ceil(groupRooms.length / rowsPerPageRooms);
        // Adjust current page if necessary.
        if (currentPage > totalPages) {
          currentPageByHostel[hostelName] = totalPages;
        }
        const start = (currentPageByHostel[hostelName] - 1) * rowsPerPageRooms;
        const paginatedRooms = groupRooms.slice(start, start + rowsPerPageRooms);
        
        // Build table rows for the current page.
        let tableRowsHtml = "";
        paginatedRooms.forEach(room => {
          const roomId = room.id?.$oid || room.id;
          tableRowsHtml += `
            <tr>
              <td class="text-13 align-middle">${room.room_number}</td>
              <td class="text-13 align-middle">${room.number_of_beds}</td>
              <td class="text-13 align-middle">${room.note || ''}</td>
              <td class="text-13 align-middle text-center">
                <span class="room-status ${room.status === 'Active' ? 'text-success' : 'text-danger'}">${room.status}</span>
              </td>
              <td class="text-13 align-middle text-end">
                <button class="btn btn-outline-primary btn-sm room-edit-btn" data-id="${roomId}">
                  <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button class="btn ${room.status === 'Active' ? 'btn-outline-danger' : 'btn-outline-success'} btn-sm room-toggle-btn" data-id="${roomId}" onclick="toggleRoom('${roomId}', '${room.status}')">
                  <i class="fa-solid ${room.status === 'Active' ? 'fa-ban' : 'fa-power-off'}"></i>
                </button>
              </td>
            </tr>
          `;
        });
        
        // Build pagination controls.
        let paginationHtml = '';
        function createPageButton(iconHtml, page, disabled) {
          return `<button class="btn btn-outline-primary btn-sm mx-1 text-13" ${disabled ? 'disabled' : `onclick="changePageForHostel('${hostelName}', ${page}, '${containerId}')" `}>${iconHtml}</button>`;
        }
        paginationHtml += createPageButton('<i class="fa-solid fa-angles-left"></i>', 1, currentPageByHostel[hostelName] === 1);
        paginationHtml += createPageButton('<i class="fa-solid fa-angle-left"></i>', currentPageByHostel[hostelName] - 1, currentPageByHostel[hostelName] === 1);
        paginationHtml += `<button class="btn btn-outline-primary btn-sm mx-1 text-13" disabled>${currentPageByHostel[hostelName]} / ${totalPages}</button>`;
        paginationHtml += createPageButton('<i class="fa-solid fa-angle-right"></i>', currentPageByHostel[hostelName] + 1, currentPageByHostel[hostelName] === totalPages);
        paginationHtml += createPageButton('<i class="fa-solid fa-angles-right"></i>', totalPages, currentPageByHostel[hostelName] === totalPages);
        
        // Unique IDs for accordion items.
        const accordionItemId = `accordionItem-${counter}`;
        const accordionHeaderId = `heading-${counter}`;
        const accordionCollapseId = `collapse-${counter}`;
        
        const accordionItemHtml = `
          <div class="accordion-item">
            <h2 class="accordion-header" id="${accordionHeaderId}">
              <button class="accordion-button collapsed text-13" type="button" data-bs-toggle="collapse" data-bs-target="#${accordionCollapseId}" aria-expanded="false" aria-controls="${accordionCollapseId}">
                ${hostelName} (${groupRooms.length} rooms)
              </button>
            </h2>
            <div id="${accordionCollapseId}" class="accordion-collapse collapse" aria-labelledby="${accordionHeaderId}" data-bs-parent="#${containerId}">
              <div class="accordion-body">
                <div class="table-responsive">
                  <table class="table">
                    <thead class="table-light">
                      <tr>
                        <th class="text-secondary text-13">Room Number</th>
                        <th class="text-secondary text-13">Number of Beds</th>
                        <th class="text-secondary text-13">Note</th>
                        <th class="text-secondary text-13 text-center">Status</th>
                        <th class="text-secondary text-13 text-end">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      ${tableRowsHtml}
                    </tbody>
                  </table>
                </div>
                <div class="mt-3 text-center">
                  ${paginationHtml}
                </div>
              </div>
            </div>
          </div>
        `;
        
        container.innerHTML += accordionItemHtml;
      }
      // Attach event listeners for room-edit buttons.
      document.querySelectorAll(".room-edit-btn").forEach(button => {
        button.addEventListener("click", function() {
          roomEditId = this.getAttribute("data-id");
          const row = this.closest("tr");
          document.getElementById("roomNumber").value = row.cells[0].textContent.trim();
          document.getElementById("numberOfBeds").value = row.cells[1].textContent.trim();
          // Get the hostel name from the accordion header.
          const accordionHeader = this.closest(".accordion-item").querySelector(".accordion-button");
          let hostelName = accordionHeader.textContent.trim().split(" (")[0];
          const dropdown = document.getElementById("roomHostelSelect");
          for (let i = 0; i < dropdown.options.length; i++) {
            try {
              let optData = JSON.parse(dropdown.options[i].value);
              if(optData.hostel_name === hostelName) {
                dropdown.selectedIndex = i;
                break;
              }
            } catch(e) {}
          }
          document.getElementById("roomNote").value = row.cells[2].textContent.trim();
          toggleRoomView(true);
        });
      });
    }
    
    // Function to handle pagination button clicks inside an accordion group.
    function changePageForHostel(hostelName, page, containerId) {
      currentPageByHostel[hostelName] = page;
      if (containerId === "roomAccordionActive") {
        const searchValue = document.getElementById("roomSearchInput").value.toLowerCase();
        const activeData = allRoomsData.filter(r =>
          r.status === "Active" &&
          (r.room_number.toLowerCase().includes(searchValue) || r.note.toLowerCase().includes(searchValue))
        );
        renderRoomAccordion(activeData, containerId);
      } else if (containerId === "roomAccordionInactive") {
        const searchValue = document.getElementById("roomSearchInput").value.toLowerCase();
        const inactiveData = allRoomsData.filter(r =>
          r.status === "Inactive" &&
          (r.room_number.toLowerCase().includes(searchValue) || r.note.toLowerCase().includes(searchValue))
        );
        renderRoomAccordion(inactiveData, containerId);
      }
    }
    
    function filterRoomTable() {
      updateRoomTable();
    }
    
    // --- Room Form Functions ---
    document.getElementById("addRoomBtn").addEventListener("click", () => {
      clearRoomForm();
      roomEditId = null;
      toggleRoomView(true);
    });
    
    document.getElementById("cancelRoomBtn").addEventListener("click", () => {
      toggleRoomView(false);
    });
    
    document.getElementById("saveRoomBtn").addEventListener("click", () => {
      const institution_id = document.getElementById('institutionSelect').value;
      const roomNumber = document.getElementById("roomNumber").value.trim();
      const numberOfBeds = document.getElementById("numberOfBeds").value.trim();
      const hostelDataJson = document.getElementById("roomHostelSelect").value;
      const roomNote = document.getElementById("roomNote").value.trim();
      if (!roomNumber || !numberOfBeds || !hostelDataJson) {
        Swal.fire({ title: 'Error', text: 'Please fill in all required fields.', icon: 'error', confirmButtonText: 'OK' });
        return;
      }
      const payload = {
        institution_id,
        room_number: roomNumber,
        number_of_beds: numberOfBeds,
        hostel: hostelDataJson, // Stored as JSON string.
        note: roomNote
      };
      const saveBtn = document.getElementById("saveRoomBtn");
      saveBtn.disabled = true;
      const originalBtnContent = saveBtn.innerHTML;
      saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
    
      if (roomEditId) {
        fetch(`/api/room/edit/${roomEditId}`, {
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
              toggleRoomView(false);
              fetchRooms(institution_id);
              roomEditId = null;
            });
          } else {
            throw new Error(data.message);
          }
        })
        .catch(error => {
          console.error("Error updating room:", error);
          Swal.fire({ title: 'Error', text: error.message || 'An error occurred while updating the room.', icon: 'error', confirmButtonText: 'OK' });
        })
        .finally(() => {
          saveBtn.disabled = false;
          saveBtn.innerHTML = originalBtnContent;
        });
      } else {
        fetch("/api/room/add", {
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
              toggleRoomView(false);
              fetchRooms(institution_id);
            });
          } else {
            Swal.fire({ title: 'Error', text: data.message, icon: 'error', confirmButtonText: 'OK' });
          }
        })
        .catch(error => {
          console.error("Error adding room:", error);
          Swal.fire({ title: 'Error', text: 'An error occurred while adding the room.', icon: 'error', confirmButtonText: 'OK' });
        })
        .finally(() => {
          saveBtn.disabled = false;
          saveBtn.innerHTML = originalBtnContent;
        });
      }
    });
    
    function clearRoomForm() {
      document.getElementById("roomNumber").value = "";
      document.getElementById("numberOfBeds").value = "";
      document.getElementById("roomHostelSelect").selectedIndex = 0;
      document.getElementById("roomNote").value = "";
      roomEditId = null;
    }
    
    function toggleRoomView(showForm) {
      if (showForm) {
        document.getElementById("roomFormContainer").classList.remove("d-none");
        document.getElementById("roomListHeader").classList.add("d-none");
        document.getElementById("roomTabContent").classList.add("d-none");
      } else {
        document.getElementById("roomFormContainer").classList.add("d-none");
        document.getElementById("roomListHeader").classList.remove("d-none");
        document.getElementById("roomTabContent").classList.remove("d-none");
      }
    }
    
    function toggleRoom(roomId, currentStatus) {
      Swal.fire({
        title: 'Are you sure?',
        text: `Do you really want to change the status to ${currentStatus === 'Active' ? 'Inactive' : 'Active'}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, change it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/api/room/toggle/${roomId}`, {
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
                fetchRooms(institute_id);
              });
            } else {
              Swal.fire({ title: 'Error', text: data.message, icon: 'error', confirmButtonText: 'OK' });
            }
          })
          .catch(error => {
            console.error("Error toggling room status:", error);
            Swal.fire({ title: 'Error', text: 'An error occurred while toggling the room status.', icon: 'error', confirmButtonText: 'OK' });
          });
        }
      });
    }
  </script>
</body>
</html>
