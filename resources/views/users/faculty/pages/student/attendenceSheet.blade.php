{{-- resources/views/manageFacultyAttendance.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Attendance Sheet</title>
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

    {{-- Main content --}}
    <div class="w-100 main-com">
      @include('users.faculty.components.header')

      <div class="container mt-4">
        {{-- Institution Details --}}
        <div class="card text-center border-0 mb-4">
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

        {{-- Selectors --}}
        <div class="row g-3 mb-3">
          <div class="col-md-3">
            <label class="form-label text-13">Select Year</label>
            <select id="yearSelect" class="form-select text-13">
              <option disabled selected>Choose Year</option>
              @php
                $cy = now()->year;
                $yrs = range($cy, $cy - 4);
              @endphp
              @foreach($yrs as $y)
                <option value="{{ $y }}">{{ $y }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3" id="courseCol">
            <label class="form-label text-13">Select Course</label>
            <select id="courseSelect" class="form-select text-13">
              <option disabled selected>Choose Course</option>
            </select>
          </div>
          <div class="col-md-2" id="semCol">
            <label class="form-label text-13">Semester</label>
            <select id="semSelect" class="form-select text-13">
              <option disabled selected>Choose Sem</option>
            </select>
          </div>
          <div class="col-md-4" id="dateCol">
            <label class="form-label text-13">Select Date</label>
            <input type="date" id="dateSelect" class="form-control text-13" />
          </div>
        </div>

        {{-- Prompt --}}
        <div id="promptMessage" class="text-center text-secondary mb-3">
          <i class="fa-solid fa-info-circle fa-2x mb-2"></i>
          <p class="fs-5">Select year, course, semester &amp; date to load attendance sheet</p>
        </div>

        {{-- Attendance table --}}
        <div id="attendanceSection" class="bg-white p-4 rounded d-none">
          {{-- Summary --}}
          <div id="attendanceSummary" class="text-end mb-3 text-14 d-none">
            <span>Present: <span id="totalPresent">0</span></span>
            <span class="ms-3">Absent: <span id="totalAbsent">0</span></span>
            <button id="refreshAttendance" class="btn btn-sm btn-outline-secondary ms-3" title="Refresh">
              <i class="fa-solid fa-arrows-rotate"></i>
            </button>
            <button id="exportCsv" class="btn btn-sm btn-outline-secondary ms-2 d-none" title="Export CSV">
              <i class="fa-solid fa-file-csv"></i>
            </button>
          </div>

          <div id="loadingSpinner" class="text-center my-5 d-none">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
          </div>
          <div class="table-responsive d-none" id="attendanceTableWrapper">
            <table class="table table-striped text-13 text-center">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Present</th>
                </tr>
              </thead>
              <tbody id="attendanceBody"></tbody>
            </table>
          </div>
          <div id="noStudents" class="text-center text-secondary d-none">
            <p class="fs-5">No students found.</p>
          </div>
          <div class="text-end mt-3">
            <button id="saveAttendance" class="btn btn-primary d-none">Save Attendance</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Scripts --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // set institution details
      document.getElementById('instituteName').querySelector('span').textContent =
        sessionStorage.getItem('institution_name') || 'Unavailable';
      document.getElementById('instituteType').textContent =
        sessionStorage.getItem('institution_type') || 'Unavailable';

      const token         = sessionStorage.getItem('token'),
            facultyId     = sessionStorage.getItem('id'),
            institutionId = sessionStorage.getItem('institution_id'),
            csrfToken     = document.querySelector('meta[name="csrf-token"]').content;

      if (!token || !facultyId || !institutionId) {
        return window.location.href = '/';
      }

      const yearSelect      = document.getElementById('yearSelect'),
            courseSelect    = document.getElementById('courseSelect'),
            semSelect       = document.getElementById('semSelect'),
            dateSelect      = document.getElementById('dateSelect'),
            promptMessage   = document.getElementById('promptMessage'),
            loadingSpin     = document.getElementById('loadingSpinner'),
            tableWrap       = document.getElementById('attendanceTableWrapper'),
            attendanceBody  = document.getElementById('attendanceBody'),
            noStudents      = document.getElementById('noStudents'),
            saveBtn         = document.getElementById('saveAttendance'),
            summary         = document.getElementById('attendanceSummary'),
            totalP          = document.getElementById('totalPresent'),
            totalA          = document.getElementById('totalAbsent'),
            refreshBtn      = document.getElementById('refreshAttendance'),
            exportBtn       = document.getElementById('exportCsv');

      const todayStr = new Date().toISOString().slice(0,10);

      // standalone function to fetch courses
      async function loadCourses() {
        resetAll();
        const year = +yearSelect.value;
        const res = await fetch('/api/courses-semister', {
          method: 'POST',
          headers: {
            'Authorization': token,
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({ institute_id: institutionId, year })
        });
        const j = await res.json();
        if (j.status === 'success' && j.data.length) {
          courseSelect.innerHTML = '<option disabled selected>Choose Course</option>';
          j.data.forEach(c =>
            courseSelect.add(new Option(`${c.program_name} (${c.program_code})`, c.program_code))
          );
        } else {
          Swal.fire('Error','No courses found for that year','error');
        }
      }
      yearSelect.addEventListener('change', loadCourses);
      yearSelect.value = new Date().getFullYear();
      loadCourses();

      courseSelect.addEventListener('change', async () => {
        resetFromSemester();
        const year = +yearSelect.value, code = courseSelect.value;
        const res = await fetch(
          `/api/faculty/${facultyId}/courses/semesters?institution_id=${institutionId}&year=${year}`,
          { headers: { 'Authorization': token } }
        );
        const j = await res.json();
        if (j.status === 'success' && j.data.courses_with_semesters[code]) {
          semSelect.innerHTML = '<option disabled selected>Choose Sem</option>';
          j.data.courses_with_semesters[code].forEach(s =>
            semSelect.add(new Option(`Sem ${s}`, s))
          );
          semSelect.value = j.data.courses_with_semesters[code][0];
          semSelect.dispatchEvent(new Event('change'));
        } else {
          Swal.fire('Error','No semesters assigned','error');
        }
      });

      semSelect.addEventListener('change', () => {
        resetFromDate();
        dateSelect.value = todayStr;
        dateSelect.dispatchEvent(new Event('change'));
      });

      dateSelect.addEventListener('change', () => {
        if (!yearSelect.value||!courseSelect.value||!semSelect.value||!dateSelect.value) return;
        loadAttendanceSheet();
      });

      refreshBtn.addEventListener('click', loadAttendanceSheet);

      exportBtn.addEventListener('click', () => {
        if (!attendanceBody.children.length) {
          return Swal.fire('No data','Nothing to export','info');
        }
        let csv = 'Name,Email,Phone,Present\n';
        Array.from(attendanceBody.children).forEach(tr => {
          const cols = tr.querySelectorAll('td');
          const name = cols[1].textContent.trim();
          const email = cols[2].textContent.trim();
          const phone = cols[3].textContent.trim();
          const present = cols[4].querySelector('input').checked ? 'Yes' : 'No';
          csv += `"${name}","${email}","${phone}","${present}"\n`;
        });
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `attendance_${dateSelect.value}.csv`;
        a.click();
        URL.revokeObjectURL(url);
      });

      saveBtn.addEventListener('click', async () => {
        const rows = attendanceBody.querySelectorAll('tr');
        const attendance = [];
        rows.forEach(tr => {
          const studentId = tr.querySelector('input[type="checkbox"]').dataset.student;
          const present   = tr.querySelector('input[type="checkbox"]').checked;
          attendance.push({ student_id: studentId, present });
        });
        const payload = {
          institution_id: institutionId,
          faculty_id: facultyId,
          program_code: courseSelect.value,
          semester: +semSelect.value,
          year: yearSelect.value,
          date: dateSelect.value,
          attendance
        };
        await fetch('/api/attendance/record', {
          method:'POST',
          headers:{
            'Authorization':token,
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':csrfToken
          },
          body: JSON.stringify(payload)
        });
        Swal.fire('Saved','Attendance has been saved','success');
      });

      function resetAll(){
        totalP.textContent = '0';
        totalA.textContent = '0';
        summary.classList.add('d-none');
        exportBtn.classList.add('d-none');
        saveBtn.classList.add('d-none');
        semSelect.innerHTML = '<option disabled selected>Choose Sem</option>';
        dateSelect.value = '';
        attendanceBody.innerHTML = '';
        document.getElementById('attendanceSection').classList.add('d-none');
        tableWrap.classList.add('d-none');
        noStudents.classList.add('d-none');
      }
      function resetFromSemester(){
        dateSelect.value = '';
        attendanceBody.innerHTML = '';
        summary.classList.add('d-none');
        exportBtn.classList.add('d-none');
        saveBtn.classList.add('d-none');
        document.getElementById('attendanceSection').classList.add('d-none');
        tableWrap.classList.add('d-none');
        noStudents.classList.add('d-none');
      }
      function resetFromDate(){
        promptMessage.classList.remove('d-none');
        document.getElementById('attendanceSection').classList.add('d-none');
        tableWrap.classList.add('d-none');
        noStudents.classList.add('d-none');
        summary.classList.add('d-none');
        exportBtn.classList.add('d-none');
        saveBtn.classList.add('d-none');
      }

      async function loadAttendanceSheet(){
        promptMessage.classList.add('d-none');
        document.getElementById('attendanceSection').classList.remove('d-none');
        loadingSpin.classList.remove('d-none');
        tableWrap.classList.add('d-none');
        noStudents.classList.add('d-none');
        attendanceBody.innerHTML = '';

        const year  = +yearSelect.value,
              code  = courseSelect.value,
              sem   = +semSelect.value,
              date  = dateSelect.value;

        const resStu = await fetch(`/api/students/program/${code}/semester/${sem}/year/${year}`, {
          headers:{ 'Authorization': token }
        });
        const jStu = await resStu.json();
        loadingSpin.classList.add('d-none');

        if (jStu.status !== 'success' || !jStu.data.length) {
          noStudents.classList.remove('d-none');
          return;
        }
        const students = jStu.data;

        const resAtt = await fetch(
          `/api/attendance?institution_id=${institutionId}&faculty_id=${facultyId}` +
          `&program_code=${code}&semester=${sem}&year=${year}&date=${date}`,
          { headers:{ 'Authorization': token } }
        );
        const jAtt = await resAtt.json();
        const presentIds = jAtt.status==='success'? jAtt.data.present_ids : [];
        const totalPcnt  = jAtt.status==='success'? jAtt.data.total_present : 0;
        const totalAcnt  = jAtt.status==='success'? jAtt.data.total_absent  : 0;

        totalP.textContent = totalPcnt;
        totalA.textContent = totalAcnt;
        summary.classList.remove('d-none');
        exportBtn.classList.remove('d-none');

        if (date === todayStr && students.length) {
          saveBtn.classList.remove('d-none');
        }

        students.forEach((s,i) => {
          const isChecked = presentIds.includes(s.id.$oid);
          const tr = document.createElement('tr');
          tr.innerHTML = `
            <td>${i+1}</td>
            <td>${s.name}</td>
            <td>${s.email}</td>
            <td>${s.phone||''}</td>
            <td><input type="checkbox" data-student="${s.id.$oid}" ${isChecked ? 'checked disabled' : ''}></td>
          `;
          attendanceBody.appendChild(tr);
        });
        tableWrap.classList.remove('d-none');
      }
    });
  </script>
</body>
</html>
