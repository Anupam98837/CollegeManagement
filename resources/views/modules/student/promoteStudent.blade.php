<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
  <title>Promote Students</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
</head>
<body>
  <div class="container mt-4">
    <!-- Institution Info -->
    <div id="institutionInfoDiv" class="bg-white p-2 rounded d-none mb-4">
      <div class="card text-center border-0">
        <div class="card-body">
          <img src="/assets/web_assets/logo.png"
               id="instImg"
               alt="Logo"
               width="100px"
               style="object-fit: contain; margin: 0 auto;"/>
          <h5 class="card-title fs-3"><span class="text-secondary" id="instituteName">Loading…</span></h5>
          <p class="card-text fs-4"><i class="fa-solid fa-graduation-cap me-2"></i><span id="instituteType">Loading…</span></p>
        </div>
      </div>
    </div>

    <p class="my-4 text-secondary text-14">
      <i class="fa-solid fa-angle-right"></i>
      <span class="text-primary">Promote Students</span>
    </p>

    <div id="accordionContainer" class="accordion"></div>
  </div>

  <script>
    // Data + state containers
    let studentsList = [];
    const tableData = {};        // key → full array of {name,email,phone,uid}
    const filterTerm = {};       // key → current filter string
    const currentPage = {};      // key → current page (1-based)
    const rowsPerPage = 10;      // fixed
    const selectionMode = {};    // key → false=select mode off, true=select mode on

    document.addEventListener("DOMContentLoaded", () => {
      const token = sessionStorage.getItem("token");
      if (!token) return window.location.href = "/";
      // Institution header
      const instName = sessionStorage.getItem("institution_name");
      const instType = sessionStorage.getItem("institution_type");
      if (instName && instType) {
        document.getElementById("instImg").src = sessionStorage.getItem("institution_logo") || '/assets/web_assets/logo.png';
        document.getElementById("instituteName").textContent = instName;
        document.getElementById("instituteType").textContent = instType;
        document.getElementById("institutionInfoDiv").classList.remove("d-none");
      }
      fetchPromoteStudents();
    });

    function fetchPromoteStudents() {
      const instituteId = sessionStorage.getItem("institution_id");
      const url = instituteId
        ? `/api/view-students-by-institute?institute_id=${instituteId}`
        : '/api/view-students';
      fetch(url, {
        headers: { 'Accept':'application/json', 'Authorization': sessionStorage.getItem("token") }
      })
      .then(r => {
        if (r.status===401||r.status===403) window.location.href='/Unauthorised';
        return r.json();
      })
      .then(data => {
        studentsList = Array.isArray(data.data) ? data.data : [];
        buildAccordions();
      })
      .catch(() => {
        document.getElementById('accordionContainer').innerHTML =
          `<div class="text-center py-4 text-danger">Failed to fetch students.</div>`;
      });
    }

    function buildAccordions() {
      const c = document.getElementById('accordionContainer');
      c.innerHTML = '';
      // group by institute
      const byInst = {};
      studentsList.forEach(s => {
        const inst = s.institute ? JSON.parse(s.institute) : {};
        const id = inst.institution_id || 'all';
        byInst[id] = byInst[id] || { name: inst.institution_name || '—', students: [] };
        byInst[id].students.push(s);
      });

      const openInst = sessionStorage.getItem("institution_id");
      const single = Object.keys(byInst).length === 1;
      let i = 0;

      for (let instId in byInst) {
        i++;
        const key = `inst${i}`;
        const info = byInst[instId];
        const courseCount = new Set(info.students.map(s=>JSON.parse(s.course||'{}').program_code)).size;
        // open if single institute OR matches session
        const isOpen = single || instId === openInst;

        c.insertAdjacentHTML('beforeend', `
          <div class="accordion-item">
            <h2 class="accordion-header text-14 text-secondary" id="hd-${key}">
              <button class="accordion-button ${isOpen?'':'collapsed'}" 
                      type="button" data-bs-toggle="collapse" 
                      data-bs-target="#cp-${key}" aria-expanded="${isOpen}" 
                      aria-controls="cp-${key}">
                ${info.name} <span class="text-success">(${courseCount} courses)</span>
              </button>
            </h2>
            <div id="cp-${key}" class="accordion-collapse collapse ${isOpen?'show':''}"
                 aria-labelledby="hd-${key}" data-bs-parent="#accordionContainer">
              <div class="accordion-body">
                <div id="crs-${key}" class="accordion"></div>
              </div>
            </div>
          </div>
        `);

        buildCourses(key, info.students);
      }
    }

    function buildCourses(instKey, list) {
      const byCourse = {};
      list.forEach(s => {
        const c = s.course ? JSON.parse(s.course) : {};
        const code = c.program_code||'—', name=c.program_name||code;
        byCourse[code] = byCourse[code]||{ name, students: [] };
        byCourse[code].students.push(s);
      });

      const cont = document.getElementById(`crs-${instKey}`);
      let j=0;
      for (let code in byCourse) {
        j++;
        const key = `${instKey}-c${j}`;
        const info = byCourse[code];
        const semCount = new Set(info.students.map(s=>s.current_semester)).size;

        cont.insertAdjacentHTML('beforeend', `
          <div class="accordion-item">
            <h2 class="accordion-header text-14 text-secondary" id="hd-${key}">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#cp-${key}" aria-expanded="false" aria-controls="cp-${key}">
                ${info.name} <span class="text-success">(${semCount} semesters)</span>
              </button>
            </h2>
            <div id="cp-${key}" class="accordion-collapse collapse" 
                 aria-labelledby="hd-${key}" data-bs-parent="#crs-${instKey}">
              <div class="accordion-body">
                <div id="sem-${key}" class="accordion"></div>
              </div>
            </div>
          </div>
        `);

        buildSemesters(key, info.students);
      }
    }

    function buildSemesters(courseKey, list) {
      const bySem = {};
      list.forEach(s => {
        const sem = s.current_semester;
        bySem[sem] = bySem[sem]||[];
        bySem[sem].push(s);
      });

      const cont = document.getElementById(`sem-${courseKey}`);
      let k=0;
      for (let sem in bySem) {
        k++;
        const key = `${courseKey}-s${k}`;
        const arr = bySem[sem];
        tableData[key]    = arr.map(s => ({ name:s.name, email:s.email, phone:s.phone, uid:s.uid }));
        filterTerm[key]  = '';
        currentPage[key] = 1;
        selectionMode[key] = false;

        cont.insertAdjacentHTML('beforeend', `
          <div class="accordion-item">
            <h2 class="accordion-header text-14 text-secondary" id="hd-${key}">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                      data-bs-target="#cp-${key}" aria-expanded="false" aria-controls="cp-${key}">
                Semester ${sem} <span class="text-success">(${arr.length} students)</span>
              </button>
            </h2>
            <div id="cp-${key}" class="accordion-collapse collapse" 
                 aria-labelledby="hd-${key}" data-bs-parent="#sem-${courseKey}">
              <div class="accordion-body">
                <div class="d-flex justify-content-between mb-2">
                  <div class="input-group input-group-sm text-13 w-50">
                    <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                    <input type="text" class="form-control text-13" placeholder="Search by name or email"
                           oninput="onFilter('${key}', this.value)">
                  </div>
                  <div>
                    <button class="btn btn-sm btn-success me-1 text-13" onclick="initiatePromote('${key}')">
                      Promote
                    </button>
                    <button class="btn btn-sm btn-info text-13" onclick="exportCSV('${key}', ${JSON.stringify(arr[0].course?.program_name||'')}, '${sem}')">
                      Export CSV
                    </button>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-bordered table-sm" id="table-${key}">
                    <thead>
                      <tr>
                        <th class="text-14 text-secondary sel-col d-none">
                          <input type="checkbox" id="chkAll-${key}" onchange="toggleAll('${key}',this.checked)"/>
                        </th>
                        <th class="text-14 text-secondary">Name</th>
                        <th class="text-14 text-secondary">Email</th>
                        <th class="text-14 text-secondary">Phone</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
                <div id="pagination-${key}" class="d-flex justify-content-center gap-2 mt-2 text-13"></div>
              </div>
            </div>
          </div>
        `);

        renderTable(key);
      }
    }

    function renderTable(key) {
      const all = tableData[key];
      const term = (filterTerm[key]||'').toLowerCase();
      const filtered = all.filter(r =>
        r.name.toLowerCase().includes(term) ||
        r.email.toLowerCase().includes(term)
      );
      const totalPages = Math.ceil(filtered.length/rowsPerPage) || 1;
      let pg = currentPage[key];
      if (pg>totalPages) pg = 1;
      currentPage[key] = pg;
      const slice = filtered.slice((pg-1)*rowsPerPage, pg*rowsPerPage);

      const tb = document.querySelector(`#table-${key} tbody`);
      tb.innerHTML = slice.map(r=>`
        <tr class="text-13">
          <td class="sel-col ${selectionMode[key]?'':'d-none'}">
            <input type="checkbox" class="chk-${key}" value="${r.uid}" ${selectionMode[key]?'checked':''}/>
          </td>
          <td>${r.name}</td>
          <td>${r.email}</td>
          <td>${r.phone}</td>
        </tr>
      `).join('') || `<tr><td colspan="4" class="text-center text-13">No records</td></tr>`;

      renderPagination(key, totalPages);
    }

    function renderPagination(key, totalPages) {
      const pg = currentPage[key];
      const pc = document.getElementById(`pagination-${key}`);
      pc.innerHTML = `
        <button class="btn btn-outline-primary text-13" ${pg===1?'disabled':''} onclick="changePage('${key}',1)"><i class="fa-solid fa-angles-left"></i></button>
        <button class="btn btn-outline-primary text-13" ${pg===1?'disabled':''} onclick="changePage('${key}',${pg-1})"><i class="fa-solid fa-angle-left"></i></button>
        <span class="align-self-center text-13">${pg}/${totalPages}</span>
        <button class="btn btn-outline-primary text-13" ${pg===totalPages?'disabled':''} onclick="changePage('${key}',${pg+1})"><i class="fa-solid fa-angle-right"></i></button>
        <button class="btn btn-outline-primary text-13" ${pg===totalPages?'disabled':''} onclick="changePage('${key}',${totalPages})"><i class="fa-solid fa-angles-right"></i></button>
      `;
    }

    function changePage(key,page) {
      currentPage[key] = page;
      renderTable(key);
    }

    function onFilter(key,val) {
      filterTerm[key] = val;
      currentPage[key] = 1;
      renderTable(key);
    }

    function toggleAll(key,checked) {
      document.querySelectorAll(`.chk-${key}`).forEach(cb=>cb.checked=checked);
    }

    function initiatePromote(key) {
      if (!selectionMode[key]) {
        selectionMode[key] = true;
        document.querySelectorAll(`#table-${key} .sel-col`).forEach(el=>el.classList.remove('d-none'));
        return;
      }
      const uids = Array.from(document.querySelectorAll(`.chk-${key}:checked`)).map(cb=>cb.value);
      if (!uids.length) return Swal.fire('No students selected','Please check at least one.','warning');
      Swal.fire({
        title: `Promote ${uids.length}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes'
      }).then(res => {
        if (!res.isConfirmed) return;
        fetch('/api/students/promote',{
          method:'POST',
          headers:{
            'Content-Type':'application/json',
            'Accept':'application/json',
            'Authorization': sessionStorage.getItem("token")
          },
          body: JSON.stringify({ student_uids: uids })
        })
        .then(r=>r.json())
        .then(d=>{
          if(d.status==='success'){
            Swal.fire('Success',d.message,'success');
            fetchPromoteStudents();
          } else {
            Swal.fire('Error',d.message||'Failed','error');
          }
        })
        .catch(()=> Swal.fire('Error','Something went wrong.','error'));
      });
    }

    function exportCSV(key, courseName, sem) {
      const instName = sessionStorage.getItem("institution_name")||'';
      const all = tableData[key];
      const term = (filterTerm[key]||'').toLowerCase();
      const filtered = all.filter(r =>
        r.name.toLowerCase().includes(term) ||
        r.email.toLowerCase().includes(term)
      );
      let csv = `"Institution","${instName}"\n`;
      csv += `"Course","${courseName}"\n`;
      csv += `"Semester","${sem}"\n\nName,Email,Phone\n`;
      filtered.forEach(r => {
        csv += `"${r.name}","${r.email}","${r.phone}"\n`;
      });
      const blob = new Blob([csv],{type:'text/csv'});
      const a = document.createElement('a');
      a.href = URL.createObjectURL(blob);
      a.download = `export_${key}.csv`;
      a.click();
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
