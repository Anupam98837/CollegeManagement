{{-- resources/views/manageRoutine.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Manage Routine</title>
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
    rel="stylesheet"/>
  <link 
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    rel="stylesheet"/>
  <link 
    href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" 
    rel="stylesheet"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <style>
    .text-13 { font-size: 13px; }
    .text-14 { font-size: 14px; }
  </style>
</head>
<body>
  <div class="container mt-4">
    <p class="mb-4 text-secondary text-14">
      <i class="fa-solid fa-angle-right"></i>
      <span class="text-primary">Manage Routine</span>
    </p>

    {{-- Institution & Year selectors --}}
    <div class="bg-white p-4 rounded mb-4">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label text-13">Select Institution</label>
          <select id="instSelect" class="form-select text-13"></select>
        </div>
        <div class="col-md-6">
          <label class="form-label text-13">Select Year</label>
          <select id="yearSelect" class="form-select text-13"></select>
        </div>
      </div>
    </div>

    {{-- Accordion placeholder --}}
    <div id="routineAccordion" class="accordion"></div>
  </div>

  {{-- Manage Routine Modal --}}
  <div class="modal fade" id="routineModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content text-13">
        <div class="modal-header">
          <h5 class="modal-title" id="routineModalLabel">Manage Routine</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          {{-- When there is at least one existing entry, show Add Routine button --}}
          <div class="mb-3" id="addRoutineButtonContainer">
            <button id="showAddFormBtn" class="btn btn-sm btn-success">
              <i class="fa fa-plus"></i> Add Routine
            </button>
          </div>

          {{-- Existing routines table --}}
          <div id="editRoutineTable" class="table-responsive d-none mb-4">
            <table class="table table-sm text-13">
              <thead>
                <tr>
                  <th>Day</th><th>Start</th><th>End</th>
                  <th>Subject</th><th>Faculty</th><th>Actions</th>
                </tr>
              </thead>
              <tbody id="routineRows"></tbody>
            </table>
            <div class="text-end">
              <button id="saveAllEditsBtn" class="btn btn-primary btn-sm">Save Changes</button>
            </div>
          </div>

          {{-- Add form --}}
          <form id="addRoutineForm" class="row g-3 d-none">
            <input type="hidden" id="r_course">
            <input type="hidden" id="r_sem">
            <div class="col-md-4">
              <label class="form-label">Day of Week</label>
              <select id="r_day" class="form-select" required>
                <option value="">Choose day</option>
                <option>Monday</option><option>Tuesday</option>
                <option>Wednesday</option><option>Thursday</option>
                <option>Friday</option><option>Saturday</option>
                <option>Sunday</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Start Time</label>
              <input type="time" id="r_start" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">End Time</label>
              <input type="time" id="r_end" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Subject</label>
              <select id="r_subject" class="form-select" required></select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Faculty</label>
              <select id="r_faculty" class="form-select"></select>
            </div>
            <div class="col-12 text-end">
              <button type="submit" class="btn btn-success btn-sm">Save Routine</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script 
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
  </script>
  <script 
    src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js">
  </script>
  <script>
    const token    = sessionStorage.getItem('token'),
          csrf     = document.querySelector('meta[name=csrf-token]').content;
    let subjects   = [], faculties = [], existingDays = [];

    document.addEventListener('DOMContentLoaded', ()=>{
      if(!token) return window.location='/';
      initYearSelect();
      fetchInstitutions();
      instSelect.addEventListener('change', loadCoursesSemesters);
      yearSelect.addEventListener('change', loadCoursesSemesters);

      // show add form button
      showAddFormBtn.addEventListener('click', ()=> {
        editRoutineTable.classList.add('d-none');
        showAddFormWithDisabledDays();
      });
    });

    function initYearSelect(){
      const ys = yearSelect, thisY = new Date().getFullYear();
      ys.innerHTML = '<option value="" disabled selected>Year</option>';
      for(let y=thisY; y>=thisY-4; y--) ys.add(new Option(y,y));
      ys.value = thisY;
    }

    function fetchInstitutions(){
      fetch('/api/view-institutions',{ headers:{ 'Authorization':token }})
        .then(r=>r.json()).then(j=>{
          instSelect.innerHTML = '<option value="" disabled selected>Institution</option>';
          j.data.filter(i=>i.status==='Active')
            .forEach(i=> instSelect.add(new Option(i.institution_name, i.id.$oid)));
        });
    }

    function loadCoursesSemesters(){
      const inst = instSelect.value, yr = yearSelect.value;
      if(!inst||!yr) return;
      fetch('/api/courses-semister',{
        method:'POST', headers:{
          'Content-Type':'application/json',
          'Authorization':token,'X-CSRF-TOKEN':csrf
        },
        body: JSON.stringify({ institute_id:inst, year:+yr })
      })
      .then(r=>r.json()).then(j=>{
        renderAccordion(j.data||[]);
      });
    }

    function renderAccordion(data){
      routineAccordion.innerHTML = '';
      data.forEach((c,ci)=>{
        const cid = `course${ci}`;
        let html = `
          <div class="accordion-item">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed text-13" 
                      data-bs-toggle="collapse" data-bs-target="#${cid}">
                ${c.program_name} (${c.program_code})
              </button>
            </h2>
            <div id="${cid}" class="accordion-collapse collapse">
              <div class="accordion-body">`;
        c.semesters.forEach(sem=>{
          html += `
            <div class="d-flex justify-content-between mb-2">
              <span>Semester ${sem}</span>
              <button class="btn btn-sm btn-primary text-13"
                      onclick="openRoutineModal('${c.program_code}',${sem})">
                Manage Routine
              </button>
            </div>`;
        });
        html += `</div></div></div>`;
        routineAccordion.insertAdjacentHTML('beforeend', html);
      });
    }

    function openRoutineModal(course, sem){
      // fetch subjects & faculties
      const yr = yearSelect.value;
      fetch(`/api/view-subjects?institution_id=${instSelect.value}` +
            `&course_id=${course}&semester=${sem}&year=${yr}`, 
            { headers:{ 'Authorization':token } })
      .then(r=>r.json()).then(j=> subjects=j.data||[]);

      fetch(`/api/institutions/${instSelect.value}/faculties`,
            { headers:{ 'Authorization':token } })
      .then(r=>r.json()).then(j=> faculties=j.data||[]);

      // fetch existing routines
      fetch(`/api/routine/view?institution_id=${instSelect.value}` +
            `&program_code=${course}&semester=${sem}`, 
            { headers:{ 'Authorization':token } })
      .then(r=>r.json()).then(j=>{
        const rowsBody = routineRows;
        rowsBody.innerHTML = '';
        existingDays = j.data.map(rt=>rt.day_of_week);
        if(j.data.length){
          // show existing table + Add button
          j.data.forEach(rt=>{
            rowsBody.insertAdjacentHTML('beforeend',`
              <tr data-id="${rt.id}">
                <td>${rt.day_of_week}</td>
                <td>${rt.start_time}</td>
                <td>${rt.end_time}</td>
                <td>${rt.subject_code}</td>
                <td>${rt.faculty_id||'—'}</td>
                <td>
                  <button class="btn btn-sm btn-outline-primary"
                          onclick="editRoutineEntry(${rt.id})">
                    Edit
                  </button>
                </td>
              </tr>`);
          });
          editRoutineTable.classList.remove('d-none');
          addRoutineForm.classList.add('d-none');
          addRoutineButtonContainer.classList.remove('d-none');
        } else {
          // no existing -> show add form immediately
          editRoutineTable.classList.add('d-none');
          showAddFormWithDisabledDays();
        }
        // set hidden fields
        r_course.value = course; r_sem.value = sem;
        new bootstrap.Modal(routineModal).show();
      });
    }

    function showAddFormWithDisabledDays(){
      // populate day dropdown, disabling ones already used
      r_day.innerHTML = '<option value="">Choose day</option>';
      ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']
        .forEach(d=>{
          const opt = document.createElement('option');
          opt.value = d; opt.text = d;
          if(existingDays.includes(d)) opt.disabled = true;
          r_day.append(opt);
        });
      // reset other fields
      r_start.value = ''; r_end.value = '';
      r_subject.innerHTML = '<option value="">Subject</option>';
      subjects.forEach(s=> r_subject.add(new Option(s.subject_name, s.subject_code)));
      r_faculty.innerHTML = '<option value="">Faculty</option>';
      faculties.forEach(f=> r_faculty.add(new Option(f.name,f.id.$oid)));
      addRoutineForm.classList.remove('d-none');
      editRoutineTable.classList.add('d-none');
      addRoutineButtonContainer.classList.add('d-none');
    }

    addRoutineForm.addEventListener('submit',e=>{
      e.preventDefault();
      const payload = {
        institution_id: instSelect.value,
        program_code:   r_course.value,
        semester:       +r_sem.value,
        day_of_week:    r_day.value,
        start_time:     r_start.value,
        end_time:       r_end.value,
        subject_code:   r_subject.value,
        faculty_id:     r_faculty.value||null
      };
      fetch('/api/routine/add',{
        method:'POST', headers:{
          'Content-Type':'application/json',
          'Authorization':token,'X-CSRF-TOKEN':csrf
        },
        body:JSON.stringify(payload)
      })
      .then(r=>r.json()).then(j=>{
        Swal.fire(j.status==='success'?'Success':'Error',j.message,j.status);
        if(j.status==='success'){
          bootstrap.Modal.getInstance(routineModal).hide();
          loadCoursesSemesters();
        }
      });
    });

    function editRoutineEntry(id){
      Swal.fire({
        title: 'Edit Routine',
        html: `
          <select id="e_day" class="form-select mb-2" disabled>
            <option>${document.querySelector(`[data-id="${id}"] td`).innerText}</option>
          </select>
          <input type="time" id="e_start" class="form-control mb-2" value="${document.querySelector(`[data-id="${id}"] td:nth-child(2)`).innerText}"/>
          <input type="time" id="e_end" class="form-control mb-2" value="${document.querySelector(`[data-id="${id}"] td:nth-child(3)`).innerText}"/>`,
        preConfirm: ()=>{
          return {
            start_time:  document.getElementById('e_start').value,
            end_time:    document.getElementById('e_end').value,
          };
        }
      }).then(res=>{
        if(!res.value) return;
        fetch(`/api/routine/edit/${id}`,{
          method:'PUT', headers:{
            'Content-Type':'application/json',
            'Authorization':token,'X-CSRF-TOKEN':csrf
          },
          body: JSON.stringify(res.value)
        })
        .then(r=>r.json()).then(j=>{
          Swal.fire(j.status==='success'?'Success':'Error',j.message,j.status);
          if(j.status==='success'){
            bootstrap.Modal.getInstance(routineModal).hide();
            loadCoursesSemesters();
          }
        });
      });
    }
  </script>
</body>
</html>
