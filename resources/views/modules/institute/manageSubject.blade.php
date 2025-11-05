<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Manage Subjects</title>
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
          <select id="yearDropdown" class="form-select text-13">
            <option value="" disabled selected>Choose Year</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Header -->
    <p class="mb-4 text-secondary text-14">
      <i class="fa-solid fa-angle-right"></i>
      <span class="text-primary text-13">Manage Subjects</span>
    </p>

    <!-- Prompt before institution chosen -->
    <div id="searchPrompt" class="p-4 bg-white rounded text-center d-flex flex-column justify-content-center align-items-center vh-50 text-13">
      <img src="{{ asset('assets/web_assets/search.png') }}" alt="" style="width:300px">
      <p class="fs-5">Select an Institution first</p>
    </div>

    <!-- Tabs -->
    <div id="tabsContainer" class="d-none bg-white p-4 rounded">
      <ul class="nav nav-tabs mb-3 text-13" id="subjectTabs">
        <li class="nav-item">
          <button class="nav-link active text-13" data-bs-target="#tab-types" data-bs-toggle="tab">Subject Types</button>
        </li>
        <li class="nav-item">
          <button class="nav-link text-13" data-bs-target="#tab-subjects" data-bs-toggle="tab">Subjects</button>
        </li>
      </ul>

      <div class="tab-content">
        <!-- Subject Types Tab -->
        <div class="tab-pane fade show active" id="tab-types">
          <div class="d-flex justify-content-between mb-2">
            <button class="btn btn-success btn-sm text-13" onclick="openAddTypeModal()">
              <i class="fa fa-plus me-1"></i>Add Type
            </button>
            <button class="btn btn-outline-secondary btn-sm text-13" onclick="loadSubjectTypes()">
              <i class="fa fa-sync me-1"></i>Refresh
            </button>
          </div>
          <div class="table-responsive">
            <table class="table table-striped text-center text-13">
              <thead class="bg-light text-13">
                <tr>
                  <th>#</th>
                  <th>Type</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="typeTableBody"></tbody>
            </table>
          </div>
        </div>

        <!-- Subjects Tab -->
        <div class="tab-pane fade" id="tab-subjects">
          <div class="row g-2 mb-3 align-items-end">
            <div class="col-md-3">
              <label class="form-label text-13">Course</label>
              <select id="filterCourse" class="form-select text-13"><option value="">All Programs</option></select>
            </div>
            <div class="col-md-2">
              <label class="form-label text-13">Semester</label>
              <select id="filterSemester" class="form-select text-13"><option value="">All</option></select>
            </div>
            <div class="col-md-3">
              <label class="form-label text-13">Subject Type</label>
              <select id="filterType" class="form-select text-13"><option value="">All</option></select>
            </div>
            <div class="col-md-4 text-end">
              <button class="btn btn-success btn-sm text-13" onclick="openAddSubjectModal()">
                <i class="fa fa-plus me-1"></i>Add Subject
              </button>
              <button class="btn btn-outline-secondary btn-sm text-13 ms-2" onclick="renderSubjects()">
                <i class="fa fa-sync me-1"></i>Refresh
              </button>
            </div>
          </div>
          <div class="accordion" id="subjectAccordion"></div>
        </div>
      </div>
    </div>

  </div>

  <!-- Modals -->
  <div class="modal fade" id="typeModal" tabindex="-1">
    <div class="modal-dialog">
      <form class="modal-content" id="typeForm">
        <div class="modal-header text-13">
          <h5 class="modal-title" id="typeModalTitle">Add Type</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-13">
          <input type="hidden" id="typeId">
          <div class="mb-3">
            <label class="form-label">Subject Type</label>
            <input type="text" id="typeName" class="form-control text-13" required>
          </div>
        </div>
        <div class="modal-footer text-13">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm">Save</button>
        </div>
      </form>
    </div>
  </div>

  <div class="modal fade" id="subjectModal" tabindex="-1">
    <div class="modal-dialog">
      <form class="modal-content" id="subjectForm">
        <div class="modal-header text-13">
          <h5 class="modal-title" id="subjectModalTitle">Add Subject</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-13">
          <input type="hidden" id="subjectId">
          <div class="mb-2">
            <label class="form-label">Course</label>
            <select id="subCourse" class="form-select text-13" required></select>
          </div>
          <div class="row g-2">
            <div class="col">
              <label class="form-label">Year</label>
              <select id="subYear" class="form-select text-13" required></select>
            </div>
            <div class="col">
              <label class="form-label">Semester</label>
              <select id="subSem" class="form-select text-13" required></select>
            </div>
          </div>
          <div class="mb-2 mt-2">
            <label class="form-label">Type</label>
            <select id="subType" class="form-select text-13" required></select>
          </div>
          <div class="mb-2">
            <label class="form-label">Subject Code</label>
            <input type="text" id="subCode" class="form-control text-13" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Subject Name</label>
            <input type="text" id="subName" class="form-control text-13" required>
          </div>
        </div>
        <div class="modal-footer text-13">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary btn-sm">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const token     = sessionStorage.getItem('token'),
          csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let selectedInstitution = '', selectedYear = '';
    let coursesList = [], typeList = [];

    document.addEventListener('DOMContentLoaded', () => {
      if (!token) return window.location.href = '/';

      // populate years
      const yd = document.getElementById('yearDropdown'),
            thisY = new Date().getFullYear();
      yd.innerHTML = '<option value="" disabled>Choose Year</option>';
      for (let y = thisY; y >= thisY - 4; y--) yd.add(new Option(y, y));

      // session restore
      const instId   = sessionStorage.getItem('institution_id'),
            instName = sessionStorage.getItem('institution_name'),
            instType = sessionStorage.getItem('institution_type'),
            ydsel    = sessionStorage.getItem('selected_year');
            const logoImg = document.getElementById("instImg");
            const instLogoPath = sessionStorage.getItem("institution_logo");

      selectedYear = ydsel || thisY;
      if (instId && instName && instType) {
        selectedInstitution = instId;
        yd.value = selectedYear;
        logoImg.src = instLogoPath || '/assets/web_assets/logo.png';

        document.getElementById('instituteName').innerHTML =
          `<span class="text-secondary">${instName}</span>`;
        document.getElementById('instituteType').innerHTML =
          `<i class="fa-solid fa-graduation-cap me-2"></i><span>${instType}</span>`;
        document.getElementById('institutionInfoDiv').classList.remove('d-none');
        document.getElementById('institutionDropdownContainer').classList.add('d-none');
        document.getElementById('searchPrompt').classList.add('d-none');
        initAll();
      } else {
        document.getElementById('institutionDropdownContainer').classList.remove('d-none');
        document.getElementById('searchPrompt').classList.remove('d-none');
        fetchInstitutions();
      }

      // when year changes, reload courses + subjects
      yd.onchange = () => {
        selectedYear = yd.value;
        sessionStorage.setItem('selected_year', selectedYear);
        if (selectedInstitution) {
          loadCoursesAndSemesters();
          renderSubjects();
        }
      };
    });

    function fetchInstitutions() {
      fetch('/api/view-institutions', { headers: { 'Authorization': token } })
        .then(r => r.json()).then(json => {
          const dd = document.getElementById('institutionDropdown');
          dd.innerHTML = '<option value="" disabled selected>Choose Institution</option>';
          json.data.filter(i => i.status === 'Active')
                   .forEach(i => dd.add(new Option(i.institution_name, i.id.$oid)));
          dd.onchange = () => {
            selectedInstitution = dd.value;
            initAll();
          };
        });
    }

    function initAll() {
      document.getElementById('searchPrompt').classList.add('d-none');
      document.getElementById('tabsContainer').classList.remove('d-none');
      loadSubjectTypes();
      loadCoursesAndSemesters();
      renderSubjects();
      initTabs();
    }

    // Subject Types
    function loadSubjectTypes() {
      fetch(`/api/view-subject-types?institution_id=${selectedInstitution}`, {
        headers: { 'Authorization': token }
      })
      .then(r => r.json()).then(j => {
        typeList = j.data || [];
        renderTypes();
        fillFilterType();
        fillSubType();
      });
    }
    function renderTypes() {
      const body = document.getElementById('typeTableBody');
      body.innerHTML = '';
      typeList.forEach((t, i) => {
        body.insertAdjacentHTML('beforeend', `
          <tr class="text-13">
            <td>${i+1}</td>
            <td>${t.subject_type}</td>
            <td>${t.status}</td>
            <td>
              <button class="btn btn-sm btn-outline-primary me-1 text-13" onclick="openEditType('${t.id.$oid}')">
                <i class="fa fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-outline-secondary text-13" onclick="toggleType('${t.id.$oid}')">
                <i class="fa fa-toggle-on"></i>
              </button>
            </td>
          </tr>`);
      });
    }
    function openAddTypeModal() {
      document.getElementById('typeForm').reset();
      document.getElementById('typeId').value = '';
      document.getElementById('typeModalTitle').textContent = 'Add Type';
      new bootstrap.Modal(document.getElementById('typeModal')).show();
    }
    function openEditType(id) {
      const t = typeList.find(x => x.id.$oid === id);
      document.getElementById('typeId').value = id;
      document.getElementById('typeName').value = t.subject_type;
      document.getElementById('typeModalTitle').textContent = 'Edit Type';
      new bootstrap.Modal(document.getElementById('typeModal')).show();
    }
    document.getElementById('typeForm').addEventListener('submit', e => {
      e.preventDefault();
      const id = document.getElementById('typeId').value,
            name = document.getElementById('typeName').value.trim(),
            url = id ? `/api/edit-subject-type/${id}` : '/api/add-subject-type',
            method = id ? 'PUT' : 'POST';
      fetch(url, {
        method, headers: {
          'Authorization': token,
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          institution_id: selectedInstitution,
          subject_type: name
        })
      })
      .then(r => r.json()).then(j => {
        Swal.fire(j.status==='success'?'Success':'Error', j.message, j.status);
        if (j.status === 'success') {
          bootstrap.Modal.getInstance(document.getElementById('typeModal')).hide();
          loadSubjectTypes();
        }
      });
    });
    function toggleType(id) {
      fetch(`/api/toggle-subject-type/${id}`, {
        method:'PUT', headers:{
          'Authorization': token,
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ institution_id: selectedInstitution })
      })
      .then(r => r.json()).then(j => {
        Swal.fire(j.status==='success'?'Success':'Error', j.message, j.status);
        if (j.status==='success') loadSubjectTypes();
      });
    }
    function fillFilterType() {
      const dd = document.getElementById('filterType');
      dd.innerHTML = '<option value="">All</option>';
      typeList.filter(t => t.status==='Active')
              .forEach(t => dd.add(new Option(t.subject_type, t.id.$oid)));
    }

    // Courses & Semesters
    function loadCoursesAndSemesters() {
      fetch('/api/courses-semister', {
        method:'POST', headers:{
          'Authorization': token,
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
          institute_id: selectedInstitution,
          year: +selectedYear
        })
      })
      .then(r => r.json()).then(j => {
        coursesList = (j.data || []).map(c => ({
          program_code: c.program_code,
          program_name: c.program_name,
          semesters: c.semesters
        }));
        fillDropdown('filterCourse', coursesList, 'program_code','program_name','All Programs');
        fillDropdown('subCourse', coursesList, 'program_code','program_name');
        fillSubYear();
        populateSemesters();
        fillSubType();
        document.getElementById('subCourse').onchange = () => {
          const code = document.getElementById('subCourse').value;
          const course = coursesList.find(c => c.program_code===code);
          const ss = document.getElementById('subSem');
          ss.innerHTML = '';
          (course?.semesters||[]).forEach(sem => ss.add(new Option(sem,sem)));
        };
      });
    }
    function fillDropdown(id,list,valKey,textKey,first){
      const dd = document.getElementById(id);
      dd.innerHTML = first? `<option value="">${first}</option>`:'';
      list.forEach(o => dd.add(new Option(o[textKey], o[valKey])));
    }
    function fillSubYear(){
      const top = Array.from(document.getElementById('yearDropdown').options)
                       .filter(o => o.value);
      const dd = document.getElementById('subYear');
      dd.innerHTML = '';
      top.forEach(o => dd.add(new Option(o.text, o.value)));
      dd.value = selectedYear;
    }
    function populateSemesters(){
      ['filterSemester','subSem'].forEach(id => {
        const dd = document.getElementById(id);
        dd.innerHTML = '<option value="">All</option>';
        for (let s=1; s<=12; s++) dd.add(new Option(s,s));  
      });
    }
    function fillSubType(){
      const dd = document.getElementById('subType');
      dd.innerHTML = '<option value="" disabled selected>Choose Type</option>';
      typeList.filter(t => t.status==='Active')
              .forEach(t => dd.add(new Option(t.subject_type, t.id.$oid)));
    }

    // Subjects
    function initTabs() {
      document.querySelector('button[data-bs-target="#tab-subjects"]')
              .addEventListener('shown.bs.tab', renderSubjects);
      ['filterCourse','filterSemester','filterType']
        .forEach(id => document.getElementById(id)
          .addEventListener('change', renderSubjects));
    }
    function renderSubjects() {
      const fc = document.getElementById('filterCourse').value,
            fs = document.getElementById('filterSemester').value,
            ft = document.getElementById('filterType').value;
      let qs = `institution_id=${selectedInstitution}&year=${selectedYear}`;
      if (fc) qs += `&course_id=${fc}`;
      if (fs) qs += `&semester=${fs}`;
      if (ft) qs += `&subject_type_id=${ft}`;
      fetch(`/api/view-subjects?${qs}`,{ headers:{ 'Authorization':token } })
        .then(r=>r.json()).then(j=>renderSubjectAccordion(j.data||[]));
    }

    function renderSubjectAccordion(arr) {
      const acc = document.getElementById('subjectAccordion');
      if (!arr.length) {
        acc.innerHTML = `
          <div class="p-4 text-center text-13 text-secondary">
            No data found for year ${selectedYear}
          </div>`;
        return;
      }
      acc.innerHTML = '';
      const byCourse = arr.reduce((m,s)=>{ (m[s.course_id] ||= []).push(s); return m; }, {});
      Object.entries(byCourse).forEach(([courseCode, subjects], ci) => {
        const course = coursesList.find(c=>c.program_code===courseCode);
        const title = course? `${course.program_name} (${courseCode})` : courseCode;
        const bySem = subjects.reduce((m,s)=>{ (m[s.semester] ||= []).push(s); return m; }, {});
        const cId = `course-${ci}`;
        let html = `
          <div class="accordion-item text-13">
            <h2 class="accordion-header" id="heading-${cId}">
              <button class="accordion-button collapsed text-13" type="button"
                      data-bs-toggle="collapse" data-bs-target="#collapse-${cId}"
                      aria-expanded="false" aria-controls="collapse-${cId}">
                ${title}
              </button>
            </h2>
            <div id="collapse-${cId}" class="accordion-collapse collapse"
                 aria-labelledby="heading-${cId}" data-bs-parent="#subjectAccordion">
              <div class="accordion-body p-0">
                <div class="accordion accordion-flush text-13 p-2" id="semAccordion-${ci}">`;

        Object.entries(bySem).sort((a,b)=>a[0]-b[0]).forEach(([sem, subs], si) => {
          const sId = `course-${ci}-sem-${sem}`;
          html += `
          <div class="accordion-item bg-light border rounded text-13">
            <h2 class="accordion-header" id="heading-${sId}">
              <button class="accordion-button collapsed text-13" type="button"
                      data-bs-toggle="collapse" data-bs-target="#collapse-${sId}"
                      aria-expanded="false" aria-controls="collapse-${sId}">
                Semester ${sem}
              </button>
            </h2>
            <div id="collapse-${sId}" class="accordion-collapse collapse"
                 aria-labelledby="heading-${sId}" data-bs-parent="#semAccordion-${ci}">
              <div class="accordion-body text-13 p-2">
                <div class="table-responsive">
                  <table class="table table-striped table-sm text-13 mb-0">
                    <thead class="bg-secondary text-white text-13">
                      <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                      </tr>
                    </thead>
                    <tbody>`;
          subs.forEach((s, idx) => {
            const typeName = (typeList.find(t=>t.id.$oid===s.subject_type_id)||{}).subject_type||'';
            const safe = encodeURIComponent(JSON.stringify(s));
            html += `
                      <tr class="text-13">
                        <td>${idx+1}</td>
                        <td>${s.subject_code}</td>
                        <td>${s.subject_name}</td>
                        <td>${typeName}</td>
                        <td>${s.status}</td>
                        <td class="text-center">
                          <button class="btn btn-sm btn-outline-primary me-1 text-13"
                                  onclick="openEditSubject(decodeURIComponent('${safe}'))">
                            <i class="fa fa-edit"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-secondary text-13"
                                  onclick="toggleSubject('${s.id.$oid}')">
                            <i class="fa fa-toggle-on"></i>
                          </button>
                        </td>
                      </tr>`;
          }); // end subs.forEach
          html += `
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>`;
        }); // end semesters

        html += `
                </div>
              </div>
            </div>
          </div>`;
        acc.insertAdjacentHTML('beforeend', html);
      }); // end courses
    } // end renderSubjectAccordion

    function openAddSubjectModal(){
      document.getElementById('subjectForm').reset();
      document.getElementById('subjectId').value = '';
      document.getElementById('subjectModalTitle').textContent = 'Add Subject';
      document.getElementById('subCourse').dispatchEvent(new Event('change'));
      new bootstrap.Modal(document.getElementById('subjectModal')).show();
    }

    function openEditSubject(subjectJson) {
      const s = JSON.parse(subjectJson);
      document.getElementById('subjectId').value = s.id.$oid;
      document.getElementById('subCourse').value = s.course_id;
      document.getElementById('subCourse').dispatchEvent(new Event('change'));
      document.getElementById('subYear').value = s.year;
      document.getElementById('subSem').value = s.semester;
      document.getElementById('subType').value = s.subject_type_id;
      document.getElementById('subCode').value = s.subject_code;
      document.getElementById('subName').value = s.subject_name;
      document.getElementById('subjectModalTitle').textContent = 'Edit Subject';
      new bootstrap.Modal(document.getElementById('subjectModal')).show();
    }

    document.getElementById('subjectForm').addEventListener('submit', e => {
      e.preventDefault();
      const id = document.getElementById('subjectId').value;
      const payload = {
        institution_id:  selectedInstitution,
        course_id:       document.getElementById('subCourse').value,
        year:            document.getElementById('subYear').value,
        semester:        document.getElementById('subSem').value,
        subject_type_id: document.getElementById('subType').value,
        subject_code:    document.getElementById('subCode').value.trim(),
        subject_name:    document.getElementById('subName').value.trim(),
      };
      const url = id ? `/api/edit-subject/${id}` : '/api/add-subject',
            method = id ? 'PUT' : 'POST';
      fetch(url, {
        method, headers:{
          'Authorization': token,
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
      })
      .then(r=>r.json()).then(j=>{
        Swal.fire(j.status==='success'?'Success':'Error', j.message, j.status);
        if(j.status==='success'){
          bootstrap.Modal.getInstance(document.getElementById('subjectModal')).hide();
          renderSubjects();
        }
      });
    });

    function toggleSubject(id){
      fetch(`/api/toggle-subject-status/${id}`,{
        method:'PUT', headers:{
          'Authorization': token,
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ institution_id: selectedInstitution })
      })
      .then(r=>r.json()).then(j=>{
        Swal.fire(j.status==='success'?'Success':'Error', j.message, j.status);
        if(j.status==='success') renderSubjects();
      });
    }
  </script>
</body>
</html>
