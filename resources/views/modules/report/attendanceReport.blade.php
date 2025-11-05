{{-- resources/views/attendanceReport.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Attendance Report</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/Components/manageRole.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    .d-none { display: none; }
    .text-13 { font-size: 0.875rem; }
    .text-14 { font-size: 0.9375rem; }
    .sessions-list { list-style: none; padding: 0; margin: 0; text-align: left; }
    .sessions-list li { margin-bottom: 0.25rem; }
    .icon-rotate { animation: rotation 2s infinite linear; }
    @keyframes rotation { from { transform: rotate(0deg); } to { transform: rotate(359deg); } }
    .session-info { cursor: pointer; }
  </style>
</head>
<body>
  <div class="container mt-4">
    {{-- 1) Faculty header: only when facultyId exists --}}
    <div id="facultyHeader" class="card text-center border-0 mb-4 d-none">
      <div class="card-body">
        <h5 class="card-title fs-3">
          <img 
          src="/assets/web_assets/logo.png" 
          id="instImg" 
          alt="Institution Logo" 
          width="100px" 
          style="object-fit: contain; display: block; margin: 0 auto;" 
        />
          <span id="instituteName">Loading Institution…</span>
        </h5>
        <p class="card-text fs-4" id="instituteType">Loading Type…</p>
      </div>
    </div>
    <p class="my-4 text-secondary text-14">
      Attendance <i class="fa-solid fa-angle-right"></i> <span class="text-primary">Report</span>
    </p>

    {{-- 2) Admin institution selector --}}
    <div id="institutionSelector" class="bg-white p-4 rounded mb-4 d-none">
      <label for="institutionDropdown" class="form-label text-13">Select Institution</label>
      <select id="institutionDropdown" class="form-select text-13">
        <option selected disabled>Choose Institution</option>
      </select>
    </div>

    {{-- 3) Before-selection placeholder (admin only) --}}
    <div id="initialPrompt" class="text-center p-4 mb-4 d-none">
      <img src="{{ asset('assets/web_assets/search.png') }}" alt="Select Institute" class="img-fluid" style="max-width:300px;">
      <p class="mt-3 text-14 text-secondary">Select an Institute first</p>
    </div>
    
    {{-- 4) Filters (once an institute is chosen) --}}
    <div id="filters" class="row g-3 mb-3 d-none">
      <div class="col-md-2">
        <label class="form-label text-13">Year</label>
        <select id="yearSelect" class="form-select text-13"></select>
      </div>
      <div class="col-md-3">
        <label class="form-label text-13">Course</label>
        <select id="courseSelect" class="form-select text-13">
          <option disabled selected>Choose Course</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label text-13">Semester</label>
        <select id="semSelect" class="form-select text-13">
          <option disabled selected>Choose Sem</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label text-13">Start Date</label>
        <input type="date" id="startDate" class="form-control text-13" />
      </div>
      <div class="col-md-2">
        <label class="form-label text-13">End Date</label>
        <input type="date" id="endDate" class="form-control text-13" />
      </div>
      <div class="col-md-1 d-flex align-items-end">
        <button id="loadReport" class="btn btn-primary text-13 w-100">
          <i id="goIcon" class="fa-solid fa-arrows-rotate"></i> Go
        </button>
      </div>
    </div>

    {{-- 5) Prompt (filters empty) --}}
    <div id="promptMessage" class="text-center text-secondary mb-4 d-none">
      <i class="fa-solid fa-info-circle fa-2x mb-2"></i>
      <p class="fs-5">
        Select year, course, semester <br/>
        and a start & end date to load report
      </p>
    </div>

    {{-- 6) Report Section --}}
    <div id="reportSection" class="bg-white p-4 rounded d-none">
      <div class="d-flex justify-content-between align-items-center mb-3 text-14">
        <div><strong>Total Sessions:</strong> <span id="totalSessions">0</span></div>
        <div>
          <button id="refreshReport" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrows-rotate"></i>
          </button>
          <button id="exportReport" class="btn btn-sm btn-outline-secondary ms-2">
            <i class="fa-solid fa-file-csv"></i>
          </button>
        </div>
      </div>
      <div id="loadingSpinner" class="text-center my-5 d-none">
        <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
      </div>
      <div class="table-responsive d-none" id="reportTableWrapper">
        <table class="table table-striped text-13 text-center">
          <thead class="table-light">
            <tr>
              <th>#</th><th>Name</th><th>Email</th><th>Phone</th>
              <th>Total Sessions</th><th>Present Count</th><th>Percentage (%)</th><th>Info</th>
            </tr>
          </thead>
          <tbody id="reportBody"></tbody>
        </table>
      </div>
      <div id="noData" class="text-center text-secondary d-none">
        <p class="fs-5">No data available for selected range.</p>
      </div>
    </div>
  </div>

  {{-- Scripts --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const token         = sessionStorage.getItem('token');
    const facultyId     = sessionStorage.getItem('id');
    let   institutionId = (facultyId || sessionStorage.getItem('institution_id'))
                          ? sessionStorage.getItem('institution_id')
                          : null;
    const csrfToken     = document.querySelector('meta[name="csrf-token"]').content;

    // Element refs
    const facultyHeader       = document.getElementById('facultyHeader');
    const institutionSelector = document.getElementById('institutionSelector');
    const institutionDropdown = document.getElementById('institutionDropdown');
    const initialPrompt       = document.getElementById('initialPrompt');
    const filters             = document.getElementById('filters');
    const promptMsg           = document.getElementById('promptMessage');
    const reportSec           = document.getElementById('reportSection');
    const totalSess           = document.getElementById('totalSessions');
    const spinner             = document.getElementById('loadingSpinner');
    const tableWrap           = document.getElementById('reportTableWrapper');
    const reportBody          = document.getElementById('reportBody');
    const noDataDiv           = document.getElementById('noData');
    const yearSelect          = document.getElementById('yearSelect');
    const courseSelect        = document.getElementById('courseSelect');
    const semSelect           = document.getElementById('semSelect');
    const startDate           = document.getElementById('startDate');
    const endDate             = document.getElementById('endDate');
    const loadBtn             = document.getElementById('loadReport');
    const goIcon              = document.getElementById('goIcon');
    const refreshBtn          = document.getElementById('refreshReport');
    const exportBtn           = document.getElementById('exportReport');
    const instituteNameEl     = document.getElementById('instituteName');
    const instituteTypeEl     = document.getElementById('instituteType');

    if (!token) return window.location.href = '/';

    // 1) Initial role-based UI
    if (facultyId || sessionStorage.getItem('institution_id')) {

      facultyHeader.classList.remove('d-none');
      const instLogoPath = sessionStorage.getItem("institution_logo");
      const logoImg = document.getElementById("instImg");
      logoImg.src = instLogoPath || '/assets/web_assets/logo.png';
      instituteNameEl.textContent = sessionStorage.getItem('institution_name') || 'Unavailable';
      instituteTypeEl.textContent = sessionStorage.getItem('institution_type') || 'Unavailable';
      showFilters();
    } else {
      institutionSelector.classList.remove('d-none');
      initialPrompt.classList.remove('d-none');
      fetch('/api/view-institutions', { headers: {'Authorization': token} })
        .then(r=>r.json())
        .then(j=> j.data.filter(i=>i.status==='Active')
                       .forEach(i=>institutionDropdown.add(
                         new Option(i.institution_name, i.id.$oid))))
        .catch(()=> Swal.fire('Error','Failed to load institutions','error'));

      institutionDropdown.addEventListener('change', () => {
        institutionId = institutionDropdown.value;
        initialPrompt.classList.add('d-none');
        showFilters();
      });
    }

    // 2) Show filters (both roles)
    function showFilters() {
      if (facultyId) institutionSelector.classList.add('d-none');
      filters.classList.remove('d-none');
      promptMsg.classList.remove('d-none');
      populateYears();
      loadCourses();
    }

    function populateYears() {
      const cy = new Date().getFullYear();
      yearSelect.innerHTML = '';
      for (let y = cy; y >= cy - 4; y--) {
        yearSelect.add(new Option(y, y));
      }
      yearSelect.value = cy;
    }

    // 3) Load courses
    function loadCourses() {
      resetFromCourse();
      fetch('/api/courses-semister', {
        method:'POST',
        headers:{
          'Content-Type':'application/json',
          'X-CSRF-TOKEN':csrfToken,
          'Authorization':token
        },
        body: JSON.stringify({ institute_id: institutionId, year: +yearSelect.value })
      })
      .then(r=>r.json())
      .then(j=>{
        if (j.status==='success') {
          j.data.forEach(c=>
            courseSelect.add(new Option(`${c.program_name} (${c.program_code})`, c.program_code))
          );
        } else Swal.fire('Error','No courses found','error');
      })
      .catch(()=> Swal.fire('Error','Failed to load courses','error'));
    }

    // 4) Load semesters
    function loadSemesters() {
  resetFromSemester();

  const designation = (sessionStorage.getItem('designation') || '').toLowerCase();
  const isFaculty   = facultyId && designation === 'faculty';
  let url, opts;

  if (isFaculty) {
    // Faculty → GET their assigned semesters
    url = `/api/faculty/${facultyId}/courses/semesters?institution_id=${institutionId}&year=${yearSelect.value}`;
    opts = { method: 'GET', headers: { 'Authorization': token } };
  } else {
    // Non-Faculty → POST to courses-semister
    url = '/api/courses-semister';
    opts = {
      method: 'POST',
      headers: {
        'Content-Type':  'application/json',
        'X-CSRF-TOKEN':  csrfToken,
        'Authorization': token
      },
      body: JSON.stringify({
        institute_id: institutionId,
        year:         +yearSelect.value
      })
    };
  }

  console.log('→ loadSemesters()', { url, opts });

  fetch(url, opts)
    .then(res => {
      return res.json().then(j => {
        console.log('← loadSemesters() response', j);
        return { ok: res.ok, payload: j };
      });
    })
    .then(({ ok, payload: j }) => {
      if (!ok || j.status !== 'success') {
        const msg = j.message || `HTTP error`;
        return Swal.fire('Error', msg, 'error');
      }

      semSelect.innerHTML = `<option disabled selected>Choose Sem</option>`;

      let sems = [];
      if (isFaculty) {
        sems = j.data.courses_with_semesters[ courseSelect.value ] || [];
      } else {
        // courses-semister returns array of { program_code, semesters: […] }
        const entry = j.data.find(e => e.program_code === courseSelect.value);
        sems = entry?.semesters || [];
      }

      sems.forEach(s => {
        semSelect.add(new Option(`Sem ${s}`, s));
      });
    })
    .catch(err => {
      console.error('loadSemesters() network error', err);
      Swal.fire('Error', 'Failed to load semesters', 'error');
    });
}


    // 5) Load report
    function loadReport() {
      if (!yearSelect.value||!courseSelect.value||!semSelect.value||
          !startDate.value||!endDate.value) {
        return Swal.fire('Missing','Please fill all selectors','warning');
      }

      // start spinner on Go button
      loadBtn.disabled = true;
      goIcon.classList.add('icon-rotate');

      promptMsg.classList.add('d-none');
      reportSec.classList.remove('d-none');
      spinner.classList.remove('d-none');
      tableWrap.classList.add('d-none');
      noDataDiv.classList.add('d-none');
      reportBody.innerHTML = '';

      const params = new URLSearchParams({
        institution_id: institutionId,
        program_code:   courseSelect.value,
        semester:       semSelect.value,
        start_date:     startDate.value,
        end_date:       endDate.value,
      });
      const designation = (sessionStorage.getItem('designation') || '').toLowerCase();
      if (facultyId && designation === 'faculty') {
        params.append('faculty_id', facultyId);
      }


      fetch(`/api/attendance-report?${params}`, { headers:{ 'Authorization':token } })
        .then(r=>r.json())
        .then(j=>{
          spinner.classList.add('d-none');
          loadBtn.disabled = false;
          goIcon.classList.remove('icon-rotate');

          if (j.status!=='success'||!j.data.length) {
            noDataDiv.classList.remove('d-none');
            totalSess.textContent='0';
            return;
          }
          const data=j.data;
          totalSess.textContent = data[0].total_sessions||data.length;
          data.forEach((r,i)=>{
            const tr=document.createElement('tr');
            tr.innerHTML=`
              <td>${i+1}</td>
              <td>${r.student.name}</td>
              <td>${r.student.email}</td>
              <td>${r.student.phone||''}</td>
              <td>${r.total_sessions}</td>
              <td>${r.present_count}</td>
              <td>${r.attendance_percentage.toFixed(2)}</td>
              <td><i class="fa-solid fa-info-circle text-primary session-info"></i></td>
            `;
            reportBody.appendChild(tr);

            // attach click to info icon
            const icon = tr.querySelector('.session-info');
            icon.dataset.sessions = JSON.stringify(r.sessions||[]);
            icon.addEventListener('click',()=>{
              const sessions = JSON.parse(icon.dataset.sessions);
              if (!sessions.length) {
                return Swal.fire('No data','No sessions found for this record','info');
              }
              let table = `<table class="table table-striped text-13"><thead><tr><th>#</th><th>Date</th>${
                facultyId?'<th>By</th>':''
              }</tr></thead><tbody>`;
              sessions.forEach((s,idx)=>{
                table += `<tr><td>${idx+1}</td><td>${s.date}</td><td>${s.faculty.name}</td></tr>`;
              });
              table += '</tbody></table>';
              Swal.fire({ title:'Sessions', html:table, width:600 });
            });
          });
          tableWrap.classList.remove('d-none');
        })
        .catch(()=>{
          spinner.classList.add('d-none');
          loadBtn.disabled = false;
          goIcon.classList.remove('icon-rotate');
          Swal.fire('Error','Failed to load report','error');
        });
    }

    // 6) Export CSV
    function exportCSV() {
      if (!reportBody.children.length) return Swal.fire('No data','Nothing to export','info');
      let csv='Name,Email,Phone,Total Sessions,Present Count,Percentage,Sessions\n';
      Array.from(reportBody.children).forEach(tr=>{
        const cols=tr.querySelectorAll('td');
        const sess=Array.from(cols[7].querySelectorAll('li'))
                        .map(li=>li.textContent).join('|');
        csv+=[cols[1].textContent,cols[2].textContent,cols[3].textContent,
              cols[4].textContent,cols[5].textContent,cols[6].textContent,
              sess].map(v=>`"${v}"`).join(',')+'\n';
      });
      const blob=new Blob([csv],{type:'text/csv'});
      const url=URL.createObjectURL(blob);
      const a=document.createElement('a');
      a.href=url; a.download=`attendance_${startDate.value}_to_${endDate.value}.csv`;
      a.click(); URL.revokeObjectURL(url);
    }

    // 7) Reset helpers
    function resetFromCourse() {
      courseSelect.innerHTML=`<option disabled selected>Choose Course</option>`;
      semSelect.innerHTML=`<option disabled selected>Choose Sem</option>`;
      startDate.value=endDate.value='';
      reportSec.classList.add('d-none');
    }
    function resetFromSemester() {
      semSelect.innerHTML=`<option disabled selected>Choose Sem</option>`;
      startDate.value=endDate.value='';
      reportSec.classList.add('d-none');
    }

    // 8) Event bindings
    yearSelect.addEventListener('change', loadCourses);
    courseSelect.addEventListener('change', loadSemesters);
    loadBtn.addEventListener('click', loadReport);
    refreshBtn.addEventListener('click', loadReport);
    exportBtn.addEventListener('click', exportCSV);
  });
  </script>
</body>
</html>
