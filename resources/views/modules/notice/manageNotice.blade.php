<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Notices</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    .text-13 { font-size: 13px; }
    .text-14 { font-size: 14px; }
    .text-15 { font-size: 15px; }
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
      <span class="text-primary">Manage Notices</span>
    </p>

    <!-- Institution + Year Dropdowns -->
    <div id="dropdownsContainer" class="bg-white p-4 rounded mb-4">
      <div class="row g-3">
        <div class="col-md-6" id="institutionDropdownContainer">
          <label class="form-label text-13">Select Institution</label>
          <select id="institutionDropdown" class="form-select text-13">
            <option value="" disabled selected>Choose Institution</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label text-13">Select Year</label>
          <select id="yearDropdown" class="form-select text-13">
            <option value="" disabled selected>Choose Year</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Prompt before institution chosen -->
    <div id="search_Data_div" class="p-4 bg-white rounded text-center d-flex flex-column justify-content-center align-items-center vh-50">
      <img src="{{ asset('assets/web_assets/search.png') }}" alt="" style="width:300px">
      <p class="fs-5">Select an Institution first</p>
    </div>

    <!-- Notices Table -->
    <div id="noticeSection" class="mt-4 d-none bg-white p-4 rounded">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <!-- Filters -->
        <div class="d-flex">
          <select id="filterCourse" class="form-select form-select-sm text-13 me-2">
            <option value="">All Programs</option>
          </select>
          <select id="filterSemester" class="form-select form-select-sm text-13 me-2 d-none-important">
            <option value="">All Semesters</option>
          </select>
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
          <button class="btn btn-outline-secondary btn-sm text-13 me-2" onclick="printNotices()">
            <i class="fa fa-print me-1"></i>Print All
          </button>
          <button class="btn btn-success btn-sm text-13" onclick="openAddNoticeModal()">
            <i class="fa fa-plus me-1"></i>Add Notice
          </button>
        </div>
      </div>

      <div class="table-responsive position-relative">
        <table class="table table-striped text-center text-13">
          <thead class="bg-light">
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>Program Name</th>
              <th>Semester</th>
              <th>Created At</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="noticeTableBody"></tbody>
        </table>
        <div id="paginationContainer" class="mt-3 d-flex justify-content-center gap-2"></div>
      </div>
    </div>
  </div>

  <!-- Add/Edit Modal -->
  <div class="modal fade" id="noticeModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="noticeForm">
          <div class="modal-header">
            <h5 class="modal-title">Add Notice</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" id="noticeId">
            <div class="mb-3">
              <label class="form-label">Title</label>
              <input type="text" id="noticeTitle" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Message</label>
              <textarea id="noticeMessage" class="form-control" rows="3" required></textarea>
            </div>
            <!-- Only for Add -->
            <div id="noticeProgramWrapper" class="mb-3">
              <label class="form-label">Program Code</label>
              <select id="noticeProgram" class="form-control" required></select>
            </div>
            <div id="noticeSemesterWrapper" class="mb-3">
              <label class="form-label">Semester</label>
              <select id="noticeSemester" class="form-control" required></select>
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

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const token     = sessionStorage.getItem('token');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let selectedInstitution = '',
        selectedYear        = '',
        coursesList         = [],
        currentNotices      = [],
        filteredNotices     = [],
        currentPage         = 1,
        rowsPerPage         = 10,
        selectedSemester    = '';

    function populateYearDropdown() {
      const dd = document.getElementById('yearDropdown');
      const thisYear = new Date().getFullYear();
      dd.innerHTML = '';
      for (let y = thisYear; y >= thisYear - 4; y--) {
        dd.add(new Option(y, y));
      }
      dd.value = thisYear;
      selectedYear = thisYear;
      dd.onchange = () => { selectedYear = dd.value; goToPage(1); maybeLoadNotices(); };
    }

    function fetchInstitutions() {
      fetch('/api/view-institutions', { headers:{ 'Authorization': token } })
        .then(r=>r.json()).then(json=>{
          if (json.status!=='success') return;
          const dd = document.getElementById('institutionDropdown');
          dd.innerHTML = '<option value="" disabled selected>Choose Institution</option>';
          json.data.filter(i=>i.status==='Active')
                   .forEach(i=>dd.add(new Option(i.institution_name, i.id.$oid)));
          dd.onchange = () => { selectedInstitution = dd.value; goToPage(1); maybeLoadNotices(); };
        });
    }

    function maybeLoadNotices() {
      if (!selectedInstitution||!selectedYear) return;
      document.getElementById('search_Data_div').classList.add('d-none');
      document.getElementById('noticeSection').classList.remove('d-none');
      fetch('/api/courses-semister', {
        method:'POST',
        headers:{
          'Authorization': token,
          'Content-Type':'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ institute_id: selectedInstitution, year:+selectedYear })
      })
      .then(r=>r.json()).then(j=>{
        if (j.status!=='success') return;
        coursesList = j.data;
        populateCourseFilter();
        renderNoticeTable();
      });
    }

    function populateCourseFilter() {
      const cdd = document.getElementById('filterCourse'),
            sdd = document.getElementById('filterSemester');
      cdd.innerHTML = '<option value="">All Programs</option>';
      coursesList.forEach(c=> cdd.add(new Option(`${c.program_name} (${c.program_code})`, c.program_code)));
      cdd.onchange = () => {
        selectedSemester = '';
        // show semester dropdown & populate
        const sel = coursesList.find(c=>c.program_code===cdd.value);
        if (cdd.value && sel && sel.semesters) {
          sdd.innerHTML = '<option value="">All Semesters</option>';
          sel.semesters.forEach(sm=> sdd.add(new Option(`Sem ${sm}`, sm)));
          sdd.classList.remove('d-none-important');
        } else {
          sdd.classList.add('d-none-important');
        }
        goToPage(1); applyFilters();
      };
      sdd.onchange = () => {
        selectedSemester = sdd.value;
        goToPage(1); applyFilters();
      };
      document.getElementById('filterDate').onchange = () => { goToPage(1); applyFilters(); };
      document.getElementById('refreshBtn').onclick   = () => {
        document.getElementById('filterCourse').value   = '';
        document.getElementById('filterDate').value     = '';
        document.getElementById('filterSemester').value = '';
        document.getElementById('filterSemester').classList.add('d-none-important');
        goToPage(1); maybeLoadNotices();
      };
    }

    function renderNoticeTable() {
      fetch(`/api/notices?institution_id=${selectedInstitution}&year=${selectedYear}`, {
        headers:{ 'Authorization': token }
      })
      .then(r=>r.json()).then(j=>{
        currentNotices = j.data||[];
        applyFilters();
      });
    }

    function applyFilters() {
      const code = document.getElementById('filterCourse').value,
            date = document.getElementById('filterDate').value;
      filteredNotices = currentNotices
        .filter(n=> !code || n.program_code===code)
        .filter(n=> !selectedSemester || n.semester==selectedSemester)
        .filter(n=> !date || n.created_at.startsWith(date));
      renderPage();
    }

    function getPaginatedData(data) {
      const start = (currentPage-1)*rowsPerPage;
      return data.slice(start, start+rowsPerPage);
    }

    function renderPage() {
      const tbody = document.getElementById('noticeTableBody');
      tbody.innerHTML = '';
      const pageData = getPaginatedData(filteredNotices);
      if (!pageData.length) {
        tbody.innerHTML = `
          <tr><td colspan="6" class="text-center text-13">
            No notices found.
          </td></tr>`;
      } else {
        pageData.forEach((n,i)=>{
          const safe = encodeURIComponent(JSON.stringify(n));
          tbody.insertAdjacentHTML('beforeend', `
            <tr>
              <td>${(currentPage-1)*rowsPerPage + i+1}</td>
              <td>${n.title}</td>
              <td>${n.program_name}</td>
              <td>${n.semester}</td>
              <td>${n.created_at}</td>
              <td>
                <button class="btn btn-outline-primary btn-sm text-13 me-1"
                        onclick='editNotice("${safe}")'>
                  <i class="fa fa-edit"></i>
                </button>
                <button class="btn btn-outline-danger btn-sm text-13"
                        onclick='deleteNotice("${n.id.$oid}")'>
                  <i class="fa fa-trash"></i>
                </button>
              </td>
            </tr>`);
        });
      }
      renderPagination();
    }

    function renderPagination() {
      const totalPages = Math.ceil(filteredNotices.length/rowsPerPage);
      let html =
        `<button class="btn btn-outline-primary btn-sm text-13" ${currentPage===1?'disabled':''}
           onclick="goToPage(1)"><i class="fa-solid fa-angles-left"></i></button>` +
        `<button class="btn btn-outline-primary btn-sm text-13" ${currentPage===1?'disabled':''}
           onclick="goToPage(${currentPage-1})"><i class="fa-solid fa-angle-left"></i></button>` +
        `<span class="btn btn-outline-primary btn-sm text-13 mx-2">${currentPage} / ${totalPages}</span>` +
        `<button class="btn btn-outline-primary btn-sm text-13" ${currentPage===totalPages?'disabled':''}
           onclick="goToPage(${currentPage+1})"><i class="fa-solid fa-angle-right"></i></button>` +
        `<button class="btn btn-outline-primary btn-sm text-13" ${currentPage===totalPages?'disabled':''}
           onclick="goToPage(${totalPages})"><i class="fa-solid fa-angles-right"></i></button>`;
      document.getElementById('paginationContainer').innerHTML = html;
    }

    function goToPage(p) {
      currentPage = p;
      renderPage();
    }

    function openAddNoticeModal() {
      document.getElementById('noticeForm').reset();
      document.getElementById('noticeId').value = '';
      document.getElementById('noticeProgramWrapper').classList.remove('d-none-important');
      document.getElementById('noticeSemesterWrapper').classList.remove('d-none-important');
      document.getElementById('noticeProgram').setAttribute('required','');
      document.getElementById('noticeSemester').setAttribute('required','');
      const psel = document.getElementById('noticeProgram'),
            ssel = document.getElementById('noticeSemester');
      psel.innerHTML=''; ssel.innerHTML='';
      coursesList.forEach(c=> psel.add(new Option(`${c.program_name} (${c.program_code})`,c.program_code)));
      for(let s=1;s<=8;s++) ssel.add(new Option(`Semester ${s}`,s));
      new bootstrap.Modal(document.getElementById('noticeModal')).show();
    }

    function editNotice(enc) {
      const n = JSON.parse(decodeURIComponent(enc));
      document.getElementById('noticeId').value      = n.id.$oid;
      document.getElementById('noticeTitle').value   = n.title;
      document.getElementById('noticeMessage').value = n.message;
      document.getElementById('noticeProgramWrapper').classList.add('d-none-important');
      document.getElementById('noticeSemesterWrapper').classList.add('d-none-important');
      document.getElementById('noticeProgram').removeAttribute('required');
      document.getElementById('noticeSemester').removeAttribute('required');
      new bootstrap.Modal(document.getElementById('noticeModal')).show();
    }

    function deleteNotice(id) {
      Swal.fire({
        title:'Delete Notice?', text:'This action cannot be undone.', icon:'warning',
        showCancelButton:true, confirmButtonColor:'#d33', cancelButtonColor:'#3085d6',
        confirmButtonText:'Yes, delete it!'
      }).then(r=>{
        if(!r.isConfirmed) return;
        fetch(`/api/notices/${id}`, {
          method:'DELETE',
          headers:{ 'Authorization':token,'X-CSRF-TOKEN':csrfToken }
        })
        .then(r=>r.json()).then(j=>{
          Swal.fire(j.status==='success'?'Deleted!':'Error',j.message,j.status==='success'?'success':'error');
          if(j.status==='success') renderNoticeTable();
        });
      });
    }

    document.getElementById('noticeForm').addEventListener('submit', e=>{
      e.preventDefault();
      const id = document.getElementById('noticeId').value;
      const payload = {
        title: document.getElementById('noticeTitle').value,
        message: document.getElementById('noticeMessage').value
      };
      if(!id){
        payload.institution_id = selectedInstitution;
        payload.program_code   = document.getElementById('noticeProgram').value;
        payload.semester       = document.getElementById('noticeSemester').value;
      }
      const url    = id? `/api/notices/${id}` : '/api/notices/create';
      const method = id? 'PUT' : 'POST';
      fetch(url, {
        method, headers:{
          'Authorization':token,
          'Content-Type':'application/json',
          'X-CSRF-TOKEN':csrfToken
        },
        body: JSON.stringify(payload)
      })
      .then(r=>r.json()).then(j=>{
        Swal.fire(j.status==='success'?'Success':'Error',j.message,j.status==='success'?'success':'error');
        if(j.status==='success'){
          bootstrap.Modal.getInstance(document.getElementById('noticeModal')).hide();
          renderNoticeTable();
        }
      });
    });

    function exportCSV(){
      if(!filteredNotices.length){
        Swal.fire('No data','There are no notices to export.','info');
        return;
      }
      const headers=['Title','Program Name','Semester','Created At'];
      const rows = filteredNotices.map(n=>[
        n.title, n.program_name, n.semester, n.created_at
      ]);
      let csv = headers.join(',')+'\n'
        + rows.map(r=> r.map(cell=>`"${cell}"`).join(',')).join('\n');
      const blob=new Blob([csv],{type:'text/csv;charset=utf-8;'});
      const url=URL.createObjectURL(blob);
      const a=document.createElement('a');
      a.href=url;
      a.download=`notices_${selectedInstitution}_${selectedYear}.csv`;
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
    }

    function printNotices() {
      const instName = sessionStorage.getItem('institution_name')
        || (() => {
             const dd = document.getElementById('institutionDropdown');
             return dd && dd.options[dd.selectedIndex]
               ? dd.options[dd.selectedIndex].text
               : 'Institution';
           })();
      const today = new Date().toLocaleDateString();
      const data = filteredNotices.slice().sort((a,b)=>
        new Date(b.created_at) - new Date(a.created_at)
      );
      let html = `<!DOCTYPE html><html><head>
        <meta charset="utf-8"><title>Notices</title>
        <style>
          body{font-family:Arial;padding:20px;}
          h1,h3{text-align:center;margin:0;}h1{margin-bottom:10px;}h3{margin-bottom:30px;color:#555;}
          .notice{border-bottom:1px solid #ccc;padding:15px 0;} .notice:last-child{border-bottom:none;}
          .notice-title{font-size:18px;font-weight:bold;margin:0 0 5px;}
          .notice-meta{font-size:12px;color:#888;margin-bottom:10px;}
          .notice-message{font-size:14px;margin:0;}
        </style></head><body>
        <h1>${instName}</h1><h3>${today}</h3><div>`;
      data.forEach((n,i)=>{
        html += `<div class="notice"><div class="notice-title">${i+1}. ${n.title}</div>
          <div class="notice-meta">
            Program: ${n.program_name} | Semester: ${n.semester} | Created: ${n.created_at}
          </div><p class="notice-message">${n.message}</p></div>`;
      });
      html += `</div></body></html>`;
      const w = window.open('','_blank');
      w.document.write(html); w.document.close(); w.focus(); w.print();
    }

    document.addEventListener('DOMContentLoaded', ()=>{
      if(!token) return window.location.href='/';
      populateYearDropdown();
      const instId   = sessionStorage.getItem('institution_id'),
            instName = sessionStorage.getItem('institution_name'),
            instType = sessionStorage.getItem('institution_type');
      if(instId && instName && instType){
        const instLogoPath = sessionStorage.getItem("institution_logo");
        const logoImg = document.getElementById("instImg");
        logoImg.src = instLogoPath || '/assets/web_assets/logo.png';        
        document.getElementById('instituteName').innerHTML =
          `<span class="text-secondary">${instName}</span>`;
        document.getElementById('instituteType').innerHTML =
          `<i class="fa-solid fa-graduation-cap me-2"></i>
           <span>${instType}</span>`;
        document.getElementById('institutionInfoDiv').classList.remove('d-none');
        document.getElementById('institutionDropdownContainer').classList.add('d-none');
        document.getElementById('search_Data_div').classList.add('d-none');
        maybeLoadNotices();
      } else {
        fetchInstitutions();
      }
    });
  </script>
</body>
</html>
