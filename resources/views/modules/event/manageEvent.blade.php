<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Events</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    .text-13 { font-size: 13px; }
    .text-14 { font-size: 14px; }
    .d-none-important { display: none !important; }
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

    <p class="mb-4 text-secondary text-14">
      <i class="fa-solid fa-angle-right"></i>
      <span class="text-primary">Manage Events</span>
    </p>

    <!-- Institution Dropdown -->
    <div id="dropdownsContainer" class="bg-white p-4 rounded mb-4">
      <div class="row g-3">
        <div class="col-md-6" id="institutionDropdownContainer">
          <label class="form-label text-13">Select Institution</label>
          <select id="institutionDropdown" class="form-select text-13">
            <option value="" disabled selected>Choose Institution</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Prompt before institution chosen -->
    <div id="search_Data_div" class="p-4 bg-white rounded text-center 
         d-flex flex-column justify-content-center align-items-center vh-50">
      <img src="{{ asset('assets/web_assets/search.png') }}" alt="" style="width:300px">
      <p class="fs-5">Select an Institution first</p>
    </div>

    <!-- Events Table -->
    <div id="eventSection" class="mt-4 d-none bg-white p-4 rounded">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Filters -->
        <div class="d-flex">
          <input type="text" id="filterTitle" class="form-control form-control-sm text-13 me-2" placeholder="Search Title">
          <input type="date" id="filterDate" class="form-control form-control-sm text-13 me-2" />
        </div>
        <!-- Actions -->
        <div class="d-flex">
          <button id="refreshBtn" class="btn btn-outline-secondary btn-sm text-13 me-2">
            <i class="fa fa-sync me-1"></i>Refresh
          </button>
          <button class="btn btn-outline-secondary btn-sm text-13 me-2" onclick="exportCSV()">
            <i class="fa fa-file-csv me-1"></i>Export CSV
          </button>
          <button class="btn btn-outline-secondary btn-sm text-13 me-2" onclick="printEvents()">
            <i class="fa fa-print me-1"></i>Print All
          </button>
          <button class="btn btn-success btn-sm text-13" onclick="openAddEventModal()">
            <i class="fa fa-plus me-1"></i>Add Event
          </button>
        </div>
      </div>

      <div class="table-responsive position-relative">
        <table class="table table-striped text-center text-13">
          <thead class="bg-light">
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>Date</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="eventTableBody"></tbody>
        </table>
        <div id="paginationContainer" class="mt-3 d-flex justify-content-center gap-2"></div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Modal -->
  <div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="eventForm" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title">Add Event</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="eventId">
            <div class="mb-3">
              <label class="form-label">Title</label>
              <input type="text" id="eventTitle" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Description</label>
              <textarea id="eventDescription" class="form-control" rows="3" required></textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">Event Date</label>
              <input type="date" id="eventDate" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Image (optional)</label>
              <input type="file" id="eventImage" class="form-control" accept="image/*">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary btn-sm">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const token     = sessionStorage.getItem('token');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let selectedInstitution = '',
        allEvents           = [],
        filteredEvents      = [],
        currentPage         = 1,
        rowsPerPage         = 10;

    function getSessionInstitutionId() {
      const raw = sessionStorage.getItem('institution_id');
      if (!raw) return null;
      if (raw.trim().startsWith('{')) {
        try {
          const obj = JSON.parse(raw);
          return obj.$oid || obj._id || raw;
        } catch { return raw; }
      }
      return raw;
    }

    function fetchInstitutions() {
      fetch('/api/view-institutions', { headers:{ 'Authorization': token } })
        .then(r=>r.json()).then(json=>{
          if (json.status!=='success') return;
          const dd = document.getElementById('institutionDropdown');
          dd.innerHTML = '<option value="" disabled selected>Choose Institution</option>';
          json.data.filter(i=>i.status==='Active').forEach(i=>{
            const oid = i._id?.$oid||i.id?.$oid||i._id||i.id;
            dd.add(new Option(i.institution_name, oid));
          });
          dd.onchange = () => {
            selectedInstitution = dd.value;
            resetFilters();
            loadEvents();
          };
        });
    }

    function loadEvents() {
      if (!selectedInstitution) return;
      document.getElementById('search_Data_div').classList.add('d-none');
      document.getElementById('eventSection').classList.remove('d-none');
      fetch(`/api/events/view?institution_id=${selectedInstitution}`, {
        headers:{ 'Authorization': token }
      })
      .then(r=>r.json()).then(j=>{
        allEvents = j.data||[];
        applyFilters();
      });
    }

    function applyFilters() {
      const filterDate  = document.getElementById('filterDate').value;
      const filterTitle = document.getElementById('filterTitle').value.trim().toLowerCase();
      filteredEvents = allEvents
        .filter(e=> !filterDate  || e.event_date === filterDate)
        .filter(e=> !filterTitle|| e.title.toLowerCase().includes(filterTitle));
      currentPage = 1;
      renderPage();
    }

    function renderPage() {
      const start = (currentPage-1)*rowsPerPage;
      const pageData = filteredEvents.slice(start, start+rowsPerPage);
      const tbody = document.getElementById('eventTableBody');
      tbody.innerHTML = '';
      if (!pageData.length) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-13">No events found.</td></tr>`;
      } else {
        pageData.forEach((e,i) => {
          const idx   = start + i + 1;
          const oid   = e.id.$oid||e.id;
          const color = e.status === 'Active' ? 'text-success' : 'text-danger';
          // Toggle btn: red outline if Active, green if Inactive
          const btnClass = e.status === 'Active'
            ? 'btn btn-outline-danger btn-sm text-13'
            : 'btn btn-outline-success btn-sm text-13';
          tbody.insertAdjacentHTML('beforeend', `
            <tr>
              <td>${idx}</td>
              <td>${e.title}</td>
              <td>${e.event_date}</td>
              <td class="${color}">${e.status}</td>
              <td>
                <button class="btn btn-outline-primary btn-sm text-13 me-1"
                        onclick='editEvent("${encodeURIComponent(JSON.stringify(e))}")'>
                  <i class="fa fa-edit"></i>
                </button>
                <button class="${btnClass}"
                        onclick='toggleEvent("${oid}")'>
                  <i class="fa fa-power-off"></i>
                </button>
              </td>
            </tr>`);
        });
      }
      renderPagination();
    }

    function renderPagination() {
      const total = Math.max(1, Math.ceil(filteredEvents.length/rowsPerPage));
      let html =
        `<button class="btn btn-outline-primary btn-sm text-13" ${currentPage===1?'disabled':''}
           onclick="changePage(1)"><i class="fa-solid fa-angles-left"></i></button>`+
        `<button class="btn btn-outline-primary btn-sm text-13" ${currentPage===1?'disabled':''}
           onclick="changePage(${currentPage-1})"><i class="fa-solid fa-angle-left"></i></button>`+
        `<span class="btn btn-outline-primary btn-sm text-13 mx-2">${currentPage}/${total}</span>`+
        `<button class="btn btn-outline-primary btn-sm text-13" ${currentPage===total?'disabled':''}
           onclick="changePage(${currentPage+1})"><i class="fa-solid fa-angle-right"></i></button>`+
        `<button class="btn btn-outline-primary btn-sm text-13" ${currentPage===total?'disabled':''}
           onclick="changePage(${total})"><i class="fa-solid fa-angles-right"></i></button>`;
      document.getElementById('paginationContainer').innerHTML = html;
    }
    function changePage(p){ currentPage=p; renderPage(); }

    function resetFilters(){
      document.getElementById('filterDate').value  = '';
      document.getElementById('filterTitle').value = '';
      currentPage = 1;
    }

    document.getElementById('refreshBtn').onclick   = ()=>{ resetFilters(); loadEvents(); };
    document.getElementById('filterDate').onchange  = applyFilters;
    document.getElementById('filterTitle').oninput  = applyFilters;

    function toggleEvent(id){
      fetch(`/api/events/toggle/${id}`, {
        method:'PUT',
        headers:{ 'Authorization':token,'X-CSRF-TOKEN':csrfToken }
      })
      .then(r=>r.json()).then(j=>{
        // no confirmation
        if (j.status==='success') loadEvents();
      });
    }

    function openAddEventModal(){
      document.getElementById('eventForm').reset();
      document.getElementById('eventId').value='';
      new bootstrap.Modal(document.getElementById('eventModal')).show();
    }
    function editEvent(enc){
      const e = JSON.parse(decodeURIComponent(enc));
      document.getElementById('eventId').value        = e.id.$oid||e.id;
      document.getElementById('eventTitle').value     = e.title;
      document.getElementById('eventDescription').value = e.description;
      document.getElementById('eventDate').value      = e.event_date;
      new bootstrap.Modal(document.getElementById('eventModal')).show();
    }

    document.getElementById('eventForm').addEventListener('submit', e=>{
      e.preventDefault();
      const id   = document.getElementById('eventId').value;
      const url  = id ? `/api/events/edit/${id}` : '/api/events/add';
      const fd   = new FormData();
      fd.append('institution_id', selectedInstitution);
      fd.append('title', document.getElementById('eventTitle').value);
      fd.append('description', document.getElementById('eventDescription').value);
      fd.append('event_date', document.getElementById('eventDate').value);
      const img = document.getElementById('eventImage').files[0];
      if (img) fd.append('image', img);
      if (id) fd.append('_method','PUT');
      fetch(url, {
        method:'POST',
        headers:{ 'Authorization':token,'X-CSRF-TOKEN':csrfToken },
        body:fd
      }).then(r=>r.json()).then(j=>{
        if (j.status==='success') {
          bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
          loadEvents();
        }
      });
    });

    document.addEventListener('DOMContentLoaded', ()=>{
      if (!token) return window.location='/';
      fetchInstitutions();
      const raw  = getSessionInstitutionId(),
            name = sessionStorage.getItem('institution_name'),
            type = sessionStorage.getItem('institution_type');
      if (raw && name && type) {
        const instLogoPath = sessionStorage.getItem("institution_logo");
      const logoImg = document.getElementById("instImg");
      logoImg.src = instLogoPath || '/assets/web_assets/logo.png';

        selectedInstitution = raw;
        document.getElementById('instituteName').innerHTML =
          `
           <span class="text-secondary">${name}</span>`;
        document.getElementById('instituteType').innerHTML =
          `<i class="fa-solid fa-graduation-cap me-2"></i><span>${type}</span>`;
        document.getElementById('institutionInfoDiv').classList.remove('d-none');
        document.getElementById('institutionDropdownContainer').classList.add('d-none');
        document.getElementById('search_Data_div').classList.add('d-none');
        loadEvents();
      }
    });
  </script>
</body>
</html>
