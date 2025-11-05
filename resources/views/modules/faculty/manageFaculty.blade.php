{{-- resources/views/manageFaculty.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Manage Faculty</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/Components/manageRole.css') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>


      <div class="container mt-4">
        <p class="mb-4 text-secondary text-14">
          <i class="fa-solid fa-angle-right"></i>
          <span class="text-primary">Manage Faculty</span>
        </p>

        {{-- Institution selector --}}
        <div class="bg-white p-4 rounded">
          <div class="row g-3 align-items-center">
            <div class="col-12">
              <select id="institutionDropdown" class="form-control text-13">
                <option value="" disabled selected>Choose Institution</option>
              </select>
            </div>
          </div>
        </div>

        {{-- Faculty table --}}
        <div id="facultySection" class="mt-4 bg-white p-4 rounded d-none mb-5">
          <div class="row mb-3 align-items-center justify-content-between">
            <div class="col-md-6 position-relative">
              <input type="text" id="facultySearch" class="form-control placeholder-14 text-13 ps-5"
                     placeholder="Search faculty by name, email or phone...">
              <i class="fa-solid fa-search position-absolute text-secondary"
                 style="top:50%;left:15px;transform:translateY(-50%);"></i>
            </div>
          </div>
          <div class="table-responsive">
            <table class="table table-striped text-center text-13">
              <thead class="bg-light">
                <tr>
                  <th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Actions</th>
                </tr>
              </thead>
              <tbody id="facultyTableBody"></tbody>
            </table>
          </div>
          {{-- Custom pagination --}}
          <div class="d-flex justify-content-center align-items-center mt-3" id="facultyPager"></div>
        </div>

        {{-- Prompt to select --}}
        <div id="selectInstitutionMessage" class="mt-4 bg-white p-4 rounded text-center text-secondary text-14">
          <div class="p-4 bg-white rounded text-center d-flex flex-column justify-content-center align-items-center vh-50">
            <img src="{{ asset('assets/web_assets/search.png') }}" style="width:300px" alt="">
            <p class="fs-5">Select an Institution first</p>
          </div>
        </div>
      </div>

  {{-- Scripts --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const token       = sessionStorage.getItem('token');
    const designation = sessionStorage.getItem('designation') || 'Unknown';
    const csrfToken   = document.querySelector('meta[name="csrf-token"]').content;
    let allFaculties  = [], filteredFaculties = [], currentPage = 1, perPage = 10;

    document.addEventListener('DOMContentLoaded', ()=>{
      if (!token) return window.location.href = '/';
      fetchInstitutions();
      document.getElementById('institutionDropdown')
        .addEventListener('change', e => loadFaculties(e.target.value));
      document.getElementById('facultySearch')
        .addEventListener('input', onSearch);
    });

    function fetchInstitutions(){
      fetch('/api/view-institutions', { headers:{ 'Authorization': token } })
        .then(r=>r.json()).then(json=>{
          if (json.status!=='success') return;
          const dd = document.getElementById('institutionDropdown');
          json.data
            .filter(i => i.status==='Active')
            .forEach(i => dd.add(new Option(i.institution_name, i.id.$oid)));
        });
    }

    function loadFaculties(instId){
      fetch(`/api/institutions/${instId}/faculties`, {
        headers:{ 'Authorization': token }
      })
      .then(r=>r.json()).then(json=>{
        if (json.status!=='success') {
          return Swal.fire('Error', json.message,'error');
        }
        allFaculties      = json.data;
        filteredFaculties = allFaculties;
        currentPage       = 1;
        document.getElementById('selectInstitutionMessage').classList.add('d-none');
        document.getElementById('facultySection').classList.remove('d-none');
        renderTable();
      });
    }

    function onSearch(e){
      const q = e.target.value.toLowerCase();
      filteredFaculties = allFaculties.filter(f =>
        f.name.toLowerCase().includes(q) ||
        f.org_email.toLowerCase().includes(q) ||
        (f.official_phone||'').includes(q)
      );
      currentPage = 1;
      renderTable();
    }

    function renderTable(){
      const start    = (currentPage-1)*perPage;
      const pageData = filteredFaculties.slice(start, start+perPage);
      const tbody    = document.getElementById('facultyTableBody');
      tbody.innerHTML = '';
      pageData.forEach((f,i)=>{
        tbody.insertAdjacentHTML('beforeend',`
          <tr>
            <td>${start+i+1}</td>
            <td>${f.name}</td>
            <td>${f.org_email}</td>
            <td>${f.official_phone||''}</td>
            <td>
              <button class="btn btn-sm btn-primary"
                onclick="startAssign('${f.id.$oid}','${f.institution_id}')">
                Assign Courses
              </button>
            </td>
          </tr>
        `);
      });
      renderPager();
    }

    function renderPager(){
      const total = filteredFaculties.length,
            pages = Math.ceil(total/perPage),
            pager = document.getElementById('facultyPager');
      pager.innerHTML = `
        <button class="btn btn-sm btn-outline-primary me-2" 
                ${currentPage===1?'disabled':''}
                onclick="gotoPage(1)">&laquo;</button>
        <button class="btn btn-sm btn-outline-primary me-2" 
                ${currentPage===1?'disabled':''}
                onclick="gotoPage(${currentPage-1})">&lsaquo;</button>
        <span>${currentPage} / ${pages}</span>
        <button class="btn btn-sm btn-outline-primary ms-2" 
                ${currentPage===pages?'disabled':''}
                onclick="gotoPage(${currentPage+1})">&rsaquo;</button>
        <button class="btn btn-sm btn-outline-primary ms-2" 
                ${currentPage===pages?'disabled':''}
                onclick="gotoPage(${pages})">&raquo;</button>
      `;
    }

    function gotoPage(p){
      currentPage = p;
      renderTable();
    }

    // ——— Two-step assign: year → load courses → load existing → pick & assign ———
    async function startAssign(facultyId, instId){
      // 1) select year (last 5 years), current year pre-selected
      const thisY = new Date().getFullYear();
      const opts  = {};
      for(let y=thisY; y>=thisY-4; y--) opts[y]=y;
      const { value: year } = await Swal.fire({
        title: 'Select Year',
        input: 'select',
        inputOptions: opts,
        inputPlaceholder: 'Choose year',
        inputValue: thisY,
        showCancelButton: true
      });
      if (!year) return;

      // 2) fetch course+sem data
      const resp = await fetch('/api/courses-semister', {
        method:'POST',
        headers:{
          'Authorization': token,
          'Content-Type':'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ institute_id: instId, year:+year })
      });
      const j = await resp.json();
      if (j.status!=='success') {
        return Swal.fire('Error', j.message,'error');
      }
      const courses = j.data;

      // 3) fetch existing assignment
      let existing = {};
      const eResp  = await fetch(
        `/api/faculty/${facultyId}/courses/semesters?institution_id=${instId}&year=${year}`,
        { headers:{ 'Authorization': token } }
      );
      const eJson = await eResp.json();
      if (eJson.status==='success') {
        existing = eJson.data.courses_with_semesters || {};
      }

      // 4) show accordion grouped by program_type & assign
      const { value: form } = await Swal.fire({
        title: 'Assign Courses & Semesters',
        html: `<div id="accordionCourses" class="accordion mt-3" style="max-height:300px;overflow:auto"></div>`,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Assign',
        didOpen: () => {
          const acc = document.getElementById('accordionCourses');
          acc.innerHTML = '';
          // group by program_type
          const grouped = {};
          courses.forEach(c => {
            if (!grouped[c.program_type]) grouped[c.program_type] = [];
            grouped[c.program_type].push(c);
          });
          let typeIdx = 0;
          for (const [ptype, list] of Object.entries(grouped)) {
            const pthId = `ptypeHeader${typeIdx}`;
            const pbcId = `ptypeBody${typeIdx}`;
            acc.insertAdjacentHTML('beforeend', `
              <div class="accordion-item">
                <h2 class="accordion-header" id="${pthId}">
                  <button class="accordion-button collapsed" type="button"
                          data-bs-toggle="collapse" data-bs-target="#${pbcId}">
                    ${ptype}
                  </button>
                </h2>
                <div id="${pbcId}" class="accordion-collapse collapse" data-bs-parent="#accordionCourses">
                  <div class="accordion-body">
                    <div class="accordion" id="innerAccordion${typeIdx}">
                      ${list.map((c, cidx) => {
                        const hid = `h${typeIdx}_${cidx}`, bid = `b${typeIdx}_${cidx}`;
                        const boxes = c.semesters.map(s => {
                          const chk = Array.isArray(existing[c.program_code]) && existing[c.program_code].includes(s) ? 'checked' : '';
                          return `
                            <div class="form-check">
                              <input class="form-check-input sem-chk" 
                                     data-course="${c.program_code}" 
                                     value="${s}" type="checkbox" ${chk}>
                              <label class="form-check-label">Sem ${s}</label>
                            </div>`;
                        }).join('');
                        return `
                          <div class="accordion-item">
                            <h2 class="accordion-header" id="${hid}">
                              <button class="accordion-button collapsed" type="button"
                                      data-bs-toggle="collapse" data-bs-target="#${bid}">
                                ${c.program_name} (${c.program_code})
                              </button>
                            </h2>
                            <div id="${bid}" class="accordion-collapse collapse" data-bs-parent="#innerAccordion${typeIdx}">
                              <div class="accordion-body">${boxes}</div>
                            </div>
                          </div>`;
                      }).join('')}
                    </div>
                  </div>
                </div>
              </div>
            `);
            typeIdx++;
          }
        },
        preConfirm: () => {
          const picked = document.querySelectorAll('.sem-chk:checked');
          if (!picked.length) {
            Swal.showValidationMessage('Pick at least one semester');
            return;
          }
          const map = {};
          picked.forEach(cb => {
            const cr = cb.dataset.course, sv = +cb.value;
            map[cr] = map[cr]||[];
            map[cr].push(sv);
          });
          return { courses: map };
        }
      });
      if (!form) return;

      // 5) finalize assignment
      const payload = {
        institution_id: instId,
        faculty_id    : facultyId,
        assigned_by   : designation,
        courses       : form.courses,
        students      : []
      };
      const r2 = await fetch('/api/faculty-course-assignments',{
        method:'POST',
        headers:{
          'Authorization': token,
          'Content-Type':'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(payload)
      });
      const rj = await r2.json();
      if (rj.status==='success') {
        Swal.fire('Success', rj.message,'success');
      } else {
        Swal.fire('Error', rj.message||'Failed','error');
      }
    }
  </script>
</body>
</html>