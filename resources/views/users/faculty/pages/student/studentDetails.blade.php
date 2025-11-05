{{-- resources/views/manageFacultyCourses.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>My Assigned Courses & Students</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="{{ asset('css/Components/manageRole.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
  <div class="d-flex">
    {{-- Sidebar --}}
    <div>      
      @include('users.faculty.components.sidebar')
    </div>

    {{-- Main Content --}}
    <div class="w-100 main-com">
      @include('users.faculty.components.header')

      {{-- Institution Details --}}
      <div class="container mt-4">
        <div class="card text-center border-0">
          <div class="card-body">
            <h5 class="card-title fs-3" id="instituteName">
              <i class="fa-solid fa-school me-2 text-primary"></i>
              <span class="text-secondary">Loading Institution...</span>
            </h5>
            <p class="card-text fs-4" id="instituteType">
              <i class="fa-solid fa-graduation-cap me-2"></i>
              Loading Type...
            </p>
          </div>
        </div>
      </div>

      <div class="container mt-4">
        {{-- Year Selector --}}
        <div id="yearSelector" class="bg-white p-4 rounded mb-4 d-none">
          <div class="row g-3 align-items-center">
            <div class="col-md-4">
              <label for="selectYear" class="form-label text-13">Select Year</label>
              <select id="selectYear" class="form-select text-13">
                <option value="" disabled selected>Choose Year</option>
                @php
                  $currentYear = now()->year;
                  $years = range($currentYear, $currentYear - 4);
                @endphp
                @foreach($years as $y)
                  <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        {{-- Prompt to select year --}}
        <div id="selectYearMessage" class="mt-4 bg-white p-4 rounded text-center text-secondary text-14">
          <div class="p-4 d-flex flex-column justify-content-center align-items-center">
            <img src="{{ asset('assets/web_assets/search.png') }}" style="width:200px" alt="">
            <p class="fs-5">Select a Year first</p>
          </div>
        </div>

        {{-- Courses Accordion --}}
        <div id="coursesSection" class="bg-white p-4 rounded mb-5 d-none">
          <h4 class="mb-3 text-14">Assigned Courses</h4>
          <div id="coursesSpinner" class="d-none text-center my-5">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
          </div>
          <div class="accordion" id="typeAccordion"></div>
          <div id="noCourses" class="text-center mt-5 d-none">
            <img src="{{ asset('assets/web_assets/noData.png') }}" alt="No Courses" style="width:200px;"/>
            <p class="fs-5 text-secondary mt-3">No Courses Found</p>
          </div>
        </div>

        {{-- Students Table --}}
        <div id="studentsSection" class="bg-white p-4 rounded d-none">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="text-14 mb-0">Students</h4>
            <div class="d-flex align-items-center">
              <input type="text" id="studentSearch" placeholder="Search by name or phone" class="form-control form-control-sm me-2" style="width:200px;">
              <button id="exportCsv" class="btn btn-sm btn-outline-secondary me-2 d-none">
                <i class="fa-solid fa-file-csv me-1"></i>Export CSV
              </button>
              <button id="closeStudents" class="btn btn-sm btn-secondary">
                <i class="fa-solid fa-times me-1"></i>Close
              </button>
            </div>
          </div>
          <div id="studentsSpinner" class="d-none text-center my-5">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
          </div>
          <div class="table-responsive">
            <table id="studentsTable" class="table table-striped text-13 text-center d-none">
              <thead class="table-light"><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th></tr></thead>
              <tbody></tbody>
            </table>
            <div id="noStudents" class="text-center mt-5 d-none">
              <img src="{{ asset('assets/web_assets/noData.png') }}" alt="No Students" style="width:200px;"/>
              <p class="fs-5 text-secondary mt-3">No Student Found</p>
            </div>
          </div>
          <div id="studentPager" class="d-flex justify-content-center align-items-center mt-3"></div>
        </div>

      </div>
    </div>
  </div>

  {{-- Scripts --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Institution info
      document.getElementById('instituteName').querySelector('span').textContent = sessionStorage.getItem('institution_name') || 'Unavailable';
      document.getElementById('instituteType').textContent       = sessionStorage.getItem('institution_type') || 'Unavailable';

      const token             = sessionStorage.getItem('token');
      const facultyId         = sessionStorage.getItem('id');
      const institutionId     = sessionStorage.getItem('institution_id');
      const csrfToken         = document.querySelector('meta[name="csrf-token"]').content;
      if (!token || !facultyId || !institutionId) return window.location.href = '/';

      // Student pagination setup
      let studentData = [], filteredStudents = [], studentPage = 1, studentPerPage = 10;

      const yearSelector      = document.getElementById('yearSelector');
      const selectYear        = document.getElementById('selectYear');
      const selectYearMessage = document.getElementById('selectYearMessage');
      const coursesSection    = document.getElementById('coursesSection');
      const coursesSpinner    = document.getElementById('coursesSpinner');
      const typeAccordion     = document.getElementById('typeAccordion');
      const noCourses         = document.getElementById('noCourses');

      const studentsSection   = document.getElementById('studentsSection');
      const studentsSpinner   = document.getElementById('studentsSpinner');
      const studentsTableEl   = document.getElementById('studentsTable');
      const noStudents        = document.getElementById('noStudents');
      const exportCsvBtn      = document.getElementById('exportCsv');
      const closeStudentsBtn  = document.getElementById('closeStudents');
      const studentSearch     = document.getElementById('studentSearch');
      const studentPager      = document.getElementById('studentPager');

      yearSelector.classList.remove('d-none');

      // Auto-select current year and fetch on load
      const nowYear = new Date().getFullYear();
      selectYear.value = nowYear;
      fetchCoursesAndAssignments();

      selectYear.addEventListener('change', fetchCoursesAndAssignments);

      document.getElementById('typeAccordion').addEventListener('click', e => {
        if (!e.target.classList.contains('view-students-btn')) return;
        const { course, sem, year } = e.target.dataset;
        loadStudents(course, sem, +year);
      });

      closeStudentsBtn.addEventListener('click', () => {
        studentsSection.classList.add('d-none');
        coursesSection.classList.remove('d-none');
      });

      studentSearch.addEventListener('input', () => {
        const q = studentSearch.value.toLowerCase();
        filteredStudents = studentData.filter(s =>
          s.name.toLowerCase().includes(q) ||
          s.phone.toLowerCase().includes(q)
        );
        studentPage = 1;
        renderStudentTable();
        renderStudentPager();
      });

      exportCsvBtn.addEventListener('click', () => {
        const rows = [["#","Name","Email","Phone"]];
        filteredStudents.forEach((s, i) => rows.push([i+1, s.name, s.email, s.phone]));
        const csv = rows.map(r => r.map(c => `"${c.replace(/"/g,'""')}"`).join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a'); a.href = url; a.download = `students_${Date.now()}.csv`;
        document.body.appendChild(a); a.click(); a.remove();
      });

      async function fetchCoursesAndAssignments() {
        const year = +selectYear.value;
        selectYearMessage.classList.add('d-none');
        coursesSection.classList.remove('d-none');
        studentsSection.classList.add('d-none');
        typeAccordion.innerHTML = '';
        noCourses.classList.add('d-none');
        coursesSpinner.classList.remove('d-none');
        typeAccordion.classList.add('d-none');

        const resp1 = await fetch('/api/courses-semister', {
          method: 'POST',
          headers: { 'Authorization': token, 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
          body: JSON.stringify({ institute_id: institutionId, year })
        });
        const cj = await resp1.json();
        if (cj.status !== 'success') {
          coursesSpinner.classList.add('d-none');
          return Swal.fire('Error', cj.message, 'error');
        }
        const allCourses = cj.data;

        const resp2 = await fetch(`/api/faculty/${facultyId}/courses/semesters?institution_id=${institutionId}&year=${year}`, {
          headers: { 'Authorization': token }
        });
        const aj = await resp2.json();
        if (aj.status !== 'success') {
          coursesSpinner.classList.add('d-none');
          return Swal.fire('Error', aj.message, 'error');
        }
        const assigned = aj.data.courses_with_semesters;

        renderCoursesAccordion(allCourses, assigned, year);
      }

      function renderCoursesAccordion(allCourses, assigned, year) {
        const grouped = allCourses.reduce((a, c) => { (a[c.program_type] ||= []).push(c); return a; }, {});
        let found = false, idx = 0;
        for (const [type, list] of Object.entries(grouped)) {
          const has = list.some(c => assigned[c.program_code]);
          if (!has) continue;
          found = true;
          const hid = `typeH${idx}`, cid = `typeC${idx}`, aid = `acc${idx}`;
          typeAccordion.insertAdjacentHTML('beforeend', `
            <div class="accordion-item">
              <h2 class="accordion-header" id="${hid}">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${cid}">${type}</button>
              </h2>
              <div id="${cid}" class="accordion-collapse collapse" data-bs-parent="#typeAccordion">
                <div class="accordion-body px-4"><div class="accordion" id="${aid}"></div></div>
              </div>
            </div>
          `);
          const acc = document.getElementById(aid);
          list.forEach((c, i) => {
            if (!assigned[c.program_code]) return;
            const ch = `courH${idx}_${i}`, cc = `courC${idx}_${i}`;
            acc.insertAdjacentHTML('beforeend', `
              <div class="accordion-item">
                <h2 class="accordion-header" id="${ch}">
                  <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${cc}">${c.program_name} (${c.program_code})</button>
                </h2>
                <div id="${cc}" class="accordion-collapse collapse" data-bs-parent="#${aid}">
                  <div class="accordion-body px-4">
                    ${assigned[c.program_code].map(s => `
                      <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Semester ${s}</span>
                        <button class="btn btn-sm btn-primary view-students-btn"
                          data-course="${c.program_code}" data-sem="${s}" data-year="${year}">
                          View Students
                        </button>
                      </div>
                    `).join('')}
                  </div>
                </div>
              </div>
            `);
          });
          idx++;
        }
        coursesSpinner.classList.add('d-none');
        if (!found) {
          noCourses.classList.remove('d-none');
        } else {
          typeAccordion.classList.remove('d-none');
        }
      }

      async function loadStudents(code, sem, year) {
        coursesSection.classList.add('d-none');
        studentsSection.classList.remove('d-none');
        studentsTableEl.classList.add('d-none');
        noStudents.classList.add('d-none');
        exportCsvBtn.classList.add('d-none');
        studentsSpinner.classList.remove('d-none');

        const response = await fetch(
          `/api/students/program/${code}/semester/${sem}/year/${year}`,
          { headers: { 'Authorization': token } }
        );
        const j = await response.json();
        studentsSpinner.classList.add('d-none');

        if (j.status === 'success') {
          studentData = j.data;
          filteredStudents = [...studentData];
          studentPage = 1;
          if (!filteredStudents.length) {
            noStudents.classList.remove('d-none');
            studentPager.innerHTML = '';
            return;
          }
          studentsTableEl.classList.remove('d-none');
          renderStudentTable();
          renderStudentPager();
          exportCsvBtn.classList.remove('d-none');

        } else if (j.status === 'error' && j.message === 'No students found for the given criteria.') {
          noStudents.classList.remove('d-none');
          studentPager.innerHTML = '';
          return;

        } else {
          return Swal.fire('Error', j.message, 'error');
        }
      }

      function renderStudentTable() {
        const tbody = studentsTableEl.querySelector('tbody');
        tbody.innerHTML = '';
        const start = (studentPage - 1) * studentPerPage;
        filteredStudents.slice(start, start + studentPerPage).forEach((s, i) => {
          tbody.insertAdjacentHTML('beforeend', `
            <tr>
              <td>${start + i + 1}</td>
              <td>${s.name}</td>
              <td>${s.email}</td>
              <td>${s.phone}</td>
            </tr>
          `);
        });
      }

      function renderStudentPager() {
        const total = filteredStudents.length;
        const pages = Math.ceil(total / studentPerPage);
        studentPager.innerHTML = `
          <button class="btn btn-sm btn-outline-primary me-2" ${studentPage === 1 ? 'disabled' : ''} onclick="gotoStudentPage(1)">&laquo;</button>
          <button class="btn btn-sm btn-outline-primary me-2" ${studentPage === 1 ? 'disabled' : ''} onclick="gotoStudentPage(${studentPage - 1})">&lsaquo;</button>
          <span>${studentPage} / ${pages}</span>
          <button class="btn btn-sm btn-outline-primary ms-2" ${studentPage === pages ? 'disabled' : ''} onclick="gotoStudentPage(${studentPage + 1})">&rsaquo;</button>
          <button class="btn btn-sm btn-outline-primary ms-2" ${studentPage === pages ? 'disabled' : ''} onclick="gotoStudentPage(${pages})">&raquo;</button>
        `;
      }

      window.gotoStudentPage = function(p) {
        studentPage = p;
        renderStudentTable();
        renderStudentPager();
      };
    });
  </script>
</body>
</html>
