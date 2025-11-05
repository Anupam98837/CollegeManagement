<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Study Materials</title>
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
<body class="text-13">
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


    <!-- Header -->
    <p class="mb-4 text-secondary text-14">
      <i class="fa-solid fa-angle-right"></i>
      <span class="text-primary text-14">Manage Study Materials</span>
    </p>

    <!-- Institution + Year Dropdowns -->
    <div id="dropdownsContainer" class="bg-white p-4 rounded mb-4 text-13">
      <div class="row g-3">
        <div class="col-md-6" id="institutionDropdownContainer">
          <label class="form-label text-13">Select Institution</label>
          <select id="institutionDropdown" class="form-select text-13">
            <option value="" disabled selected>Choose Institution</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label text-13">Select Year</label>
          <select id="yearDropdown" class="form-select text-13"></select>
        </div>
      </div>
    </div>

    <!-- Prompt before institution/year chosen -->
    <div id="promptDiv" class="p-4 bg-white rounded text-center d-flex flex-column justify-content-center align-items-center vh-50 text-13">
      <img src="{{ asset('assets/web_assets/search.png') }}" alt="" style="width:300px">
      <p class="fs-5 text-14">Select an Institution &amp; year first</p>
    </div>

    <!-- Main Section -->
    <div id="materialSection" class="d-none bg-white p-4 rounded text-13">
      <div class="row g-2 mb-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label text-13">Course</label>
          <select id="filterCourse" class="form-select text-13"><option value="">All Courses</option></select>
        </div>
        <div class="col-md-2">
          <label class="form-label text-13">Semester</label>
          <select id="filterSemester" class="form-select text-13"><option value="">All</option></select>
        </div>
        <div class="col-md-2">
          <label class="form-label text-13">Public</label>
          <select id="filterPublic" class="form-select text-13">
            <option value="">All</option>
            <option value="Yes">Yes</option>
            <option value="No">No</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label text-13">Date</label>
          <input type="date" id="filterDate" class="form-control text-13">
        </div>
        <div class="col-md-3 text-end">
          <button id="refreshBtn" class="btn btn-outline-secondary btn-sm text-13">
            <i class="fa fa-sync me-1"></i>Refresh
          </button>
          <button class="btn btn-outline-secondary btn-sm text-13" onclick="exportCSV()">
            <i class="fa fa-file-csv me-1"></i>CSV
          </button>
          {{-- <button class="btn btn-outline-secondary btn-sm text-13" onclick="printMaterials()">
            <i class="fa fa-print me-1"></i>Print
          </button> --}}
          <button class="btn btn-success btn-sm text-13" onclick="openAddModal()">
            <i class="fa fa-plus me-1"></i>Add
          </button>
        </div>
      </div>

      <!-- Table -->
      <div class="table-responsive">
        <table class="table table-striped text-center text-13">
            <thead>
                <tr>
                  <th class="text-13">#</th>
                  <th class="text-13">Institution</th>
                  <th class="text-13">Course</th>
                  <th class="text-13">Semester</th>
                  <th class="text-13">Subject</th>
                  <th class="text-13">Title</th>
                  <th class="text-13">Description</th>
                  <th class="text-13">Year</th>
                  <th class="text-13">Public</th>
                  <th class="text-13">Actions</th>
                </tr>
              </thead>              
          <tbody id="materialTableBody"></tbody>
        </table>
        <div id="paginationContainer" class="mt-3 d-flex justify-content-center gap-2"></div>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="materialModal" tabindex="-1">
    <div class="modal-dialog">
      <form class="modal-content text-13" id="materialForm" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title text-14" id="modalTitle">Add Study Material</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="materialId" name="id">
          <div class="mb-3">
            <label class="form-label text-13">Course</label>
            <select id="matCourse" name="course_id" class="form-select text-13" required></select>
          </div>
          <div class="mb-3 row gx-2">
            <div class="col">
              <label class="form-label text-13">Semester</label>
              <select id="matSemester" name="semester" class="form-select text-13" required></select>
            </div>
            <div class="col">
              <label class="form-label text-13">Year</label>
              <select id="matYear" name="year" class="form-select text-13" required></select>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label text-13">Subject</label>
            <select id="matSubject" name="subject_id" class="form-select text-13" required></select>
          </div>
          <div class="mb-3">
            <label class="form-label text-13">Title</label>
            <input type="text" id="matTitle" name="title" class="form-control text-13" required>
          </div>
          <div class="mb-3">
            <label class="form-label text-13">Description</label>
            <textarea id="matDesc" name="description" class="form-control text-13" rows="3" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label text-13">File</label>
            <input type="file" id="matFile" name="material" class="form-control text-13"
                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
          </div>
          <div class="mb-3">
            <label class="form-label text-13">Public?</label>
            <select id="matPublic" name="is_public" class="form-select text-13" required>
              <option value="Yes">Yes</option>
              <option value="No">No</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm text-13" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm text-13">Save</button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const token     = sessionStorage.getItem('token'),
          csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    let inst='', year='', course='', sem='',
        materials=[], filtered=[], page=1, perPage=10,
        coursesList=[];

    document.addEventListener('DOMContentLoaded', ()=> {
      if (!token) return window.location.href='/';
      initYear();
      checkSessionInstitution();
      document.getElementById('filterPublic').onchange =
      document.getElementById('filterDate').onchange = ()=>{ page=1; applyFilters(); };
    //   document.getElementById('refreshBtn').onclick = ()=>{
    //     document.getElementById('filterPublic').value='';
    //     document.getElementById('filterDate').value='';
    //     loadMaterials();
    //   };
    });

    function checkSessionInstitution(){
      const sid   = sessionStorage.getItem('institution_id'),
            sname = sessionStorage.getItem('institution_name'),
            stype = sessionStorage.getItem('institution_type');
      if (sid && sname && stype) {
        const instLogoPath = sessionStorage.getItem("institution_logo");
      const logoImg = document.getElementById("instImg");
      logoImg.src = instLogoPath || '/assets/web_assets/logo.png';
        inst = sid;
        year = document.getElementById('yearDropdown').value;
        document.getElementById('instituteName').innerHTML =
          `<span class="text-secondary">${sname}</span>`;
        document.getElementById('instituteType').innerHTML =
          `<i class="fa-solid fa-graduation-cap me-2"></i><span>${stype}</span>`;
        document.getElementById('institutionInfoDiv').classList.remove('d-none');
        document.getElementById('dropdownsContainer').classList.add('d-none');
        document.getElementById('promptDiv').classList.add('d-none');
        document.getElementById('materialSection').classList.remove('d-none');
        loadCourses();
        loadMaterials();
      } else {
        document.getElementById('dropdownsContainer').classList.remove('d-none');
        fetchInstitutions();
      }
    }

    function initYear(){
      const yd = document.getElementById('yearDropdown'),
            now = new Date().getFullYear();
      yd.innerHTML = '';
      for(let y=now; y>=now-4; y--) yd.add(new Option(y,y));
      yd.value=now; year=now;
      yd.onchange = () => {
        year = yd.value;
        loadCourses();
        loadMaterials();
      };
    }

    function fetchInstitutions(){
      fetch('/api/view-institutions',{headers:{'Authorization':token}})
        .then(r=>r.json()).then(j=>{
          const dd = document.getElementById('institutionDropdown');
          dd.innerHTML = '<option value="" disabled selected>Choose Institution</option>';
          j.data.filter(i=>i.status==='Active')
            .forEach(i=> dd.add(new Option(i.institution_name, i.id.$oid)));
          dd.onchange = () => {
            inst = dd.value;
            const info = j.data.find(x=>x.id.$oid===inst);
            if (!info) return;
            document.getElementById('instituteName').innerHTML =
              `<i class="fa-solid fa-school me-2 text-primary"></i><span class="text-secondary">${info.institution_name}</span>`;
            document.getElementById('instituteType').innerHTML =
              `<i class="fa-solid fa-graduation-cap me-2"></i><span>${info.type}</span>`;
            document.getElementById('promptDiv').classList.add('d-none');
            document.getElementById('materialSection').classList.remove('d-none');
            loadCourses();
            loadMaterials();
          };
        });
    }

    function loadCourses(){
      if(!inst||!year) return;
      fetch('/api/courses-semister',{
        method:'POST',
        headers:{'Authorization':token,'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},
        body: JSON.stringify({ institute_id:inst, year:+year })
      })
      .then(r=>r.json()).then(j=>{
        coursesList = j.data||[];
        ['filterCourse','matCourse'].forEach(id=>{
          const dd=document.getElementById(id);
          dd.innerHTML = '<option value="">All Courses</option>';
          coursesList.forEach(c=>
            dd.add(new Option(`${c.program_name} (${c.program_code})`, c.program_code))
          );
          dd.onchange = () => {
            if (id==='filterCourse') {
              course = dd.value; fillSemesters(); loadMaterials();
            } else {
              populateModalSem();
            }
          };
        });
        fillSemesters();
      });
    }

    function fillSemesters(){
      const dd = document.getElementById('filterSemester');
      dd.innerHTML = '<option value="">All</option>';
      coursesList
        .filter(c=>!course||c.program_code===course)
        .forEach(c=>c.semesters.forEach(s=> dd.add(new Option(s,s))));
      dd.onchange = () => { sem = dd.value; loadMaterials(); };
    }

    function loadMaterials(){
      if(!inst||!year) return;
      let qs = `institution_id=${inst}&year=${year}`;
      if(course) qs+=`&course_id=${course}`;
      if(sem)    qs+=`&semester=${sem}`;
      fetch(`/api/study-materials/view?${qs}`,{headers:{'Authorization':token}})
        .then(r=>r.json()).then(j=>{
          materials = j.data||[];
          applyFilters();
        });
    }

    function applyFilters(){
      const d = document.getElementById('filterDate').value,
            p = document.getElementById('filterPublic').value;
      filtered = materials
        .filter(m=>!d||m.created_at.startsWith(d))
        .filter(m=>!p||m.is_public===p);
      renderPage();
    }

    function renderPage() {
  const tb = document.getElementById('materialTableBody'),
        start = (page - 1) * perPage,
        pageData = filtered.slice(start, start + perPage);
  tb.innerHTML = '';

  if (!pageData.length) {
    tb.innerHTML = `<tr><td colspan="10" class="text-center text-13">No materials found.</td></tr>`;
  } else {
    pageData.forEach((m, i) => {
      const pubClass     = m.is_public === 'Yes' ? 'text-success' : 'text-danger';
      const toggleClass  = m.is_public === 'Yes' ? 'btn-success' : 'btn-danger';
      const toggleIcon   = m.is_public === 'Yes' ? 'fa-toggle-on' : 'fa-toggle-off';
      const courseMatch  = m.course_name.match(/\(([^)]+)\)/);
      const courseDisplay = courseMatch ? courseMatch[1] : m.course_name;

      tb.insertAdjacentHTML('beforeend', `
        <tr>
          <td class="text-13">${start + i + 1}</td>
          <td class="text-13">${m.institution_name}</td>
          <td class="text-13">${courseDisplay}</td>
          <td class="text-13">${m.semester}</td>
          <td class="text-13">${m.subject_name}</td>
          <td class="text-13">${m.title}</td>
          <td class="text-13">${m.description}</td>
          <td class="text-13">${m.year}</td>
          <td class="text-13 ${pubClass}">${m.is_public}</td>
          <td>
            <button class="btn btn-outline-primary btn-sm text-13 me-1" onclick='editMaterial(${JSON.stringify(m)})'>
              <i class="fa fa-edit"></i>
            </button>
            <button class="btn ${toggleClass} btn-sm text-13" onclick='togglePublic("${m.id.$oid}")'>
              <i class="fa ${toggleIcon}"></i>
            </button>
          </td>
        </tr>
      `);
    });
  }

  renderPagination();
}



    function renderPagination(){
      const total = Math.ceil(filtered.length/perPage),
            pc = document.getElementById('paginationContainer');
      pc.innerHTML = `
        <button class="btn btn-outline-primary btn-sm text-13"${page===1?' disabled':''} onclick="goPage(1)"><i class="fa-solid fa-angles-left"></i></button>
        <button class="btn btn-outline-primary btn-sm text-13"${page===1?' disabled':''} onclick="goPage(${page-1})"><i class="fa-solid fa-angle-left"></i></button>
        <span class="btn btn-outline-primary btn-sm text-13 mx-2">${page}/${total}</span>
        <button class="btn btn-outline-primary btn-sm text-13"${page===total?' disabled':''} onclick="goPage(${page+1})"><i class="fa-solid fa-angle-right"></i></button>
        <button class="btn btn-outline-primary btn-sm text-13"${page===total?' disabled':''} onclick="goPage(${total})"><i class="fa-solid fa-angles-right"></i></button>`;
    }

    function goPage(p){ page=p; renderPage(); }

    function openAddModal(){
      document.getElementById('materialForm').reset();
      document.getElementById('materialId').value = '';
      const mc = document.getElementById('matCourse'),
            ms = document.getElementById('matSemester'),
            my = document.getElementById('matYear'),
            sb = document.getElementById('matSubject');
      mc.innerHTML = ''; ms.innerHTML = ''; my.innerHTML = ''; sb.innerHTML = '';
      coursesList.forEach(c=> mc.add(new Option(`${c.program_name} (${c.program_code})`, c.program_code)));
      Array.from(document.getElementById('yearDropdown').options)
           .forEach(o=> my.add(new Option(o.text,o.value)));
      mc.onchange = populateModalSem;
      new bootstrap.Modal(document.getElementById('materialModal')).show();
    }

    function populateModalSem(){
      const sel = document.getElementById('matCourse').value,
            semEl = document.getElementById('matSemester'),
            sb = document.getElementById('matSubject'),
            cobj = coursesList.find(c=>c.program_code===sel);
      semEl.innerHTML = '';
      sb.innerHTML = '';
      (cobj?.semesters||[]).forEach(s=> semEl.add(new Option(s,s)));
      fetch(`/api/view-subjects?institution_id=${inst}&year=${year}&course_id=${sel}&semester=${semEl.value}`,{headers:{'Authorization':token}})
        .then(r=>r.json()).then(j=>{
          (j.data||[]).forEach(s=> sb.add(new Option(s.subject_name, s.id.$oid)));
        });
    }

    document.getElementById('materialForm').addEventListener('submit', e => {
      e.preventDefault();

      const form = e.target;
      const id   = form.querySelector('#materialId').value.trim();
      const fd   = new FormData(form);

      fd.append('institution_id', inst);
      fd.append('course_id',       form.querySelector('#matCourse').value);
      fd.append('year',            form.querySelector('#matYear').value);
      fd.append('semester',        form.querySelector('#matSemester').value);
      fd.append('subject_id',      form.querySelector('#matSubject').value);

      let url, method;
      if (id) {
        fd.append('_method', 'PUT');
        url    = `/api/study-materials/edit/${id}`;
        method = 'POST';
      } else {
        url    = '/api/study-materials/add';
        method = 'POST';
      }

      fetch(url, {
        method,
        headers: {
          'Authorization': token,
          'X-CSRF-TOKEN':  csrfToken
        },
        body: fd
      })
      .then(r => r.json())
      .then(j => {
        Swal.fire(
          j.status === 'success' ? 'Success' : 'Error',
          j.message,
          j.status
        );
        if (j.status === 'success') {
          bootstrap.Modal
            .getInstance(document.getElementById('materialModal'))
            .hide();
          loadMaterials();
        }
      })
      .catch(err => {
        console.error('Submit failed', err);
        Swal.fire('Error', 'Something went wrong.', 'error');
      });
    });

    function editMaterial(m){
      openAddModal();
      setTimeout(()=>{
        document.getElementById('matCourse').value=m.course_id;
        populateModalSem();
        setTimeout(()=>{
          document.getElementById('materialId').value = m.id.$oid;
          document.getElementById('matSemester').value=m.semester;
          document.getElementById('matSubject').value=m.subject_id;
          document.getElementById('matYear').value=m.year;
          document.getElementById('matTitle').value=m.title;
          document.getElementById('matDesc').value=m.description;
          document.getElementById('matPublic').value=m.is_public;
        },200);
      },200);
    }

    function togglePublic(id){
      fetch(`/api/study-materials/toggle/${id}`,{
        method:'PUT',headers:{'Authorization':token,'X-CSRF-TOKEN':csrfToken}
      }).then(r=>r.json()).then(j=>{
        Swal.fire(j.status==='success'?'Success':'Error', j.message, j.status);
        if(j.status==='success') loadMaterials();
      });
    }

    function exportCSV() {
  if (!filtered.length) {
    return Swal.fire('No data', 'No materials to export.', 'info');
  }

  // CSV headers matching the table columns
  const headers = [
    'Institution',
    'Course',
    'Semester',
    'Subject',
    'Title',
    'Description',
    'Year',
    'Public'
  ];

  // Build rows: extract the parentheses content for course
  const rows = filtered.map(m => {
    const match = m.course_name.match(/\(([^)]+)\)/);
    const courseDisplay = match ? match[1] : m.course_name;
    return [
      m.institution_name,
      courseDisplay,
      m.semester,
      m.subject_name,
      m.title,
      m.description,
      m.year,
      m.is_public
    ];
  });

  // Assemble CSV string
  const csvContent =
    headers.join(',') + '\n' +
    rows.map(row => row.map(cell => `"${cell.replace(/"/g, '""')}"`).join(',')).join('\n');

  // Trigger download
  const blob = new Blob([csvContent], { type: 'text/csv' });
  const url  = URL.createObjectURL(blob);
  const a    = document.createElement('a');
  a.href     = url;
  a.download = `study_materials_${inst}_${year}.csv`;
  document.body.appendChild(a);
  a.click();
  document.body.removeChild(a);
}


    // function printMaterials(){
    //   const name = document.getElementById('instName').textContent.trim(),
    //         today= new Date().toLocaleDateString();
    //   let html=`<html><head><title>Print</title><style>
    //     body{font-family:Arial;padding:20px;}h1,h3{text-align:center;}
    //     .mat{border-bottom:1px solid #ccc;padding:10px;}
    //     .title{font-weight:bold;}
    //   </style></head><body><h1 class="text-14">${name}</h1><h3 class="text-14">${today}</h3>`;
    //   filtered.forEach((m,i)=>{
    //     html+=`<div class="mat"><div class="title text-14">${i+1}. ${m.title}</div>
    //       <div class="text-13">Year: ${m.year} | Public: ${m.is_public}</div><p class="text-13">${m.description}</p></div>`;
    //   });
    //   html+='</body></html>';
    //   const w = window.open('','_blank');
    //   w.document.write(html); w.document.close(); w.print();
    // }
    // Replace your existing refresh handler with this:
document.getElementById('refreshBtn').onclick = () => {

  const btn  = document.getElementById('refreshBtn');
  const icon = btn.querySelector('i');

  // start rotating the icon
  icon.classList.add('fa-spin');

  // reset all filters to default
  ['filterCourse','filterSemester','filterPublic','filterDate'].forEach(id => {
    const el = document.getElementById(id);
    el.value = '';
  });
  course = '';
  sem    = '';
  page   = 1;

  // re-fetch & re-render
  loadMaterials();

  // stop rotating after a short delay (adjust as needed)
  setTimeout(() => {
    icon.classList.remove('fa-spin');
  }, 600);
};

  </script>
</body>
</html>
