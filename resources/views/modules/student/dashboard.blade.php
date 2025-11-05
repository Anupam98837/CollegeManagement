<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Student Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <style>
    .text-13 { font-size: 13px; }
    .text-14 { font-size: 14px; }
    .accordion-button { font-size: 1rem; }
    .accordion-body { font-size: 0.9rem; }
    .accordion-scroll { height: 400px; overflow-y: auto; }
    .search-wrapper { position: relative; }
    .search-wrapper .fa-search {
      position: absolute;
      top: 50%;
      left: 15px;
      transform: translateY(-50%);
      pointer-events: none;
    }
    .search-wrapper input { padding-left: 2.5rem; }
    .institute-banner {
      background-color: #f7f7f7;
      padding: 0.75rem 1rem;
      border: 1px solid #ddd;
      border-radius: 0.25rem;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>
      <div class="container my-4">
        <!-- Institution Info Banner (if available) -->
        
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

        <!-- Summary Cards Row -->
        <div class="row my-4">
          <!-- Total Students Card -->
          <div class="col-12 col-md-4 mb-3 mb-md-0">
            <div class="card h-100" style="background-color: #fff; border-left: 5px solid #007bff;">
              <div class="card-body d-flex flex-column justify-content-center text-center">
                <h5 class="card-title text-primary">
                  <i class="fa-solid fa-users"></i> Total Students
                </h5>
                <p class="card-text" id="totalStudentCount">
                  <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
                </p>
              </div>
            </div>
          </div>
          <!-- Active Students Card -->
          <div class="col-12 col-md-4 mb-3 mb-md-0">
            <div class="card h-100" style="background-color: #fff; border-left: 5px solid #28a745;">
              <div class="card-body d-flex flex-column justify-content-center text-center">
                <h5 class="card-title text-success">
                  <i class="fa-solid fa-check"></i> Active Students
                </h5>
                <p class="card-text" id="activeStudentCount">
                  <span class="spinner-border spinner-border-sm text-success" role="status" aria-hidden="true"></span>
                </p>
              </div>
            </div>
          </div>
          <!-- Inactive Students Card -->
          <div class="col-12 col-md-4">
            <div class="card h-100" style="background-color: #fff; border-left: 5px solid #dc3545;">
              <div class="card-body d-flex flex-column justify-content-center text-center">
                <h5 class="card-title text-danger">
                  <i class="fa-solid fa-times"></i> Inactive Students
                </h5>
                <p class="card-text" id="inactiveStudentCount">
                  <span class="spinner-border spinner-border-sm text-danger" role="status" aria-hidden="true"></span>
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Student List Accordion (Full Width) -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h5 class="text-14 mb-0">Student List</h5>
              </div>
              <div class="card-body">
                <!-- Accordion: Each item contains its own search and status filter, table, and pagination controls -->
                <div class="accordion accordion-scroll" id="studentAccordion">
                  <!-- Dynamic accordion items will be generated here -->
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- End container -->

  <!-- Bootstrap Bundle JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Global variables
    const rowsPerPage = 10;
    let groupCurrentPage = {}; // To store current page per accordion group

    document.addEventListener("DOMContentLoaded", function() {
      if (!sessionStorage.getItem("token")) {
        window.location.href = "/";
      }
      fetchStudents();
    });

    // Display institution info if available.
    document.addEventListener("DOMContentLoaded", () => {
      const instName = sessionStorage.getItem("institution_name");
      const instType = sessionStorage.getItem("institution_type");
      const institutionInfoDiv = document.getElementById("institutionInfoDiv");
      if (instName && instType) {
        const instLogoPath = sessionStorage.getItem("institution_logo");
      const logoImg = document.getElementById("instImg");
      logoImg.src = instLogoPath || '/assets/web_assets/logo.png';

        document.getElementById("instituteName").innerHTML = `<span class="text-secondary">${instName}</span>`;
        document.getElementById("instituteType").innerHTML = `<i class="fa-solid fa-graduation-cap me-2"></i>${instType}`;
        institutionInfoDiv.classList.remove("d-none");
      }
    });

    const token = sessionStorage.getItem("token");
    let studentsData = [];

    // Fetch students from API.
    function fetchStudents() {
      const institutionId = sessionStorage.getItem("institution_id");
      const url = institutionId 
                  ? `/api/view-students-by-institute?institute_id=${institutionId}` 
                  : '/api/view-students';
      fetch(url, {
        method: "GET",
        headers: {
          "Accept": "application/json",
          "Authorization": token
        }
      })
      .then(response => {
        if (response.status === 401 || response.status === 403) {
          window.location.href = "/Unauthorised";
          throw new Error("Unauthorized Access");
        }
        return response.json();
      })
      .then(data => {
        if (data.status === "success") {
          studentsData = data.data;
          updateStudentCounts();
          populateStudentAccordion();
        } else {
          console.error("Error fetching students:", data.message);
        }
      })
      .catch(error => console.error("Error:", error));
    }

    // Update summary counts.
    function updateStudentCounts() {
      const total = studentsData.length;
      const active = studentsData.filter(student => student.status === "Active").length;
      const inactive = total - active;
      document.getElementById("totalStudentCount").textContent = total;
      document.getElementById("activeStudentCount").textContent = active;
      document.getElementById("inactiveStudentCount").textContent = inactive;
    }

    // Populate student accordion.
    function populateStudentAccordion() {
      const accordion = document.getElementById("studentAccordion");
      accordion.innerHTML = "";
      const institutionId = sessionStorage.getItem("institution_id");

      if (institutionId) {
        // Only one accordion item using the institution name.
        const inst = sessionStorage.getItem("institution_name") || "Unknown Institution";
        const instSafe = inst.replace(/\s+/g, '');
        groupCurrentPage[instSafe] = 1;
        const accordionItem = document.createElement("div");
        accordionItem.classList.add("accordion-item");
        accordionItem.innerHTML = `
          <h2 class="accordion-header" id="heading-${instSafe}">
            <button class="accordion-button text-14" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${instSafe}" aria-expanded="true" aria-controls="collapse-${instSafe}">
              ${inst} (${studentsData.length})
            </button>
          </h2>
          <div id="collapse-${instSafe}" class="accordion-collapse collapse show" aria-labelledby="heading-${instSafe}" data-bs-parent="#studentAccordion">
            <div class="accordion-body text-13">
              <div class="d-flex mb-2">
                <div class="flex-grow-1 me-2">
                  <div class="search-wrapper">
                    <input type="text" class="form-control student-search-input" placeholder="Search by name or email...">
                    <i class="fa-solid fa-search text-secondary"></i>
                  </div>
                </div>
                <div>
                  <select class="form-select student-status-filter">
                    <option value="all" selected>All</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                  </select>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-striped table-sm" id="studentTable-${instSafe}">
                  <thead>
                    <tr>
                      <th class="text-13 text-secondary">Name</th>
                      <th class="text-13 text-secondary">Email</th>
                      <th class="text-13 text-secondary">Status</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
              <div id="paginationContainer-${instSafe}" class="mt-2 d-flex justify-content-center gap-2"></div>
            </div>
          </div>
        `;
        accordion.appendChild(accordionItem);
        renderGroupTable(instSafe, studentsData);
        attachStudentFilters(instSafe);
      } else {
        // Group students by institution.
        const grouped = {};
        studentsData.forEach(student => {
          let inst = "Unknown Institution";
          if (student.institute) {
            try {
              const instData = JSON.parse(student.institute);
              inst = instData.institution_name || "Unknown Institution";
            } catch (e) {
              inst = "Unknown Institution";
            }
          }
          if (!grouped[inst]) grouped[inst] = [];
          grouped[inst].push(student);
        });
        const institutions = Object.keys(grouped);
        institutions.forEach((inst, index) => {
          const students = grouped[inst];
          const instSafe = inst.replace(/\s+/g, '');
          groupCurrentPage[instSafe] = 1;
          const accordionItem = document.createElement("div");
          accordionItem.classList.add("accordion-item");
          accordionItem.innerHTML = `
            <h2 class="accordion-header" id="heading-${instSafe}">
              <button class="accordion-button ${index === 0 ? "" : "collapsed"} text-14" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${instSafe}" aria-expanded="${index === 0 ? "true" : "false"}" aria-controls="collapse-${instSafe}">
                ${inst} (${students.length})
              </button>
            </h2>
            <div id="collapse-${instSafe}" class="accordion-collapse collapse ${index === 0 ? "show" : ""}" aria-labelledby="heading-${instSafe}" data-bs-parent="#studentAccordion">
              <div class="accordion-body text-13">
                <div class="d-flex mb-2">
                  <div class="flex-grow-1 me-2">
                    <div class="search-wrapper">
                      <input type="text" class="form-control student-search-input" placeholder="Search by name or email...">
                      <i class="fa-solid fa-search text-secondary"></i>
                    </div>
                  </div>
                  <div>
                    <select class="form-select student-status-filter">
                      <option value="all" selected>All</option>
                      <option value="active">Active</option>
                      <option value="inactive">Inactive</option>
                    </select>
                  </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-striped table-sm" id="studentTable-${instSafe}">
                    <thead>
                      <tr>
                        <th class="text-13 text-secondary">Name</th>
                        <th class="text-13 text-secondary">Email</th>
                        <th class="text-13 text-secondary">Status</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
                <div id="paginationContainer-${instSafe}" class="mt-2 d-flex justify-content-center gap-2"></div>
              </div>
            </div>
          `;
          accordion.appendChild(accordionItem);
          renderGroupTable(instSafe, students);
          attachStudentFilters(instSafe);
        });
      }
    }

    // Render table with pagination for a given group (identified by instSafe)
    function renderGroupTable(instSafe, groupStudents) {
      const currentPage = groupCurrentPage[instSafe] || 1;
      const start = (currentPage - 1) * rowsPerPage;
      const end = start + rowsPerPage;
      const pageStudents = groupStudents.slice(start, end);
      const tbody = document.querySelector(`#studentTable-${instSafe} tbody`);
      tbody.innerHTML = "";
      if (pageStudents.length > 0) {
        pageStudents.forEach(student => {
          tbody.innerHTML += `
            <tr class="student-row" data-student-name="${student.name.toLowerCase()}" data-student-email="${student.email.toLowerCase()}">
              <td>${student.name}</td>
              <td>${student.email}</td>
              <td style="color: ${student.status === 'Active' ? 'green' : 'red'};">${student.status || "N/A"}</td>
            </tr>
          `;
        });
      } else {
        tbody.innerHTML = `<tr><td colspan="3" class="text-center">No students found.</td></tr>`;
      }
      renderGroupPagination(instSafe, groupStudents);
    }

    // Render pagination controls for a given group.
    function renderGroupPagination(instSafe, groupStudents) {
      const totalPages = Math.ceil(groupStudents.length / rowsPerPage);
      const paginationContainer = document.getElementById(`paginationContainer-${instSafe}`);
      let paginationHTML = '';
      paginationHTML += `<button class="btn btn-outline-primary mx-1" ${groupCurrentPage[instSafe] === 1 ? 'disabled' : ''} onclick="changeGroupPage('${instSafe}', 1)"><i class="fa-solid fa-angles-left"></i></button>`;
      paginationHTML += `<button class="btn btn-outline-primary mx-1" ${groupCurrentPage[instSafe] === 1 ? 'disabled' : ''} onclick="changeGroupPage('${instSafe}', ${groupCurrentPage[instSafe] - 1})"><i class="fa-solid fa-angle-left"></i></button>`;
      paginationHTML += `<span class="mx-2 text-13 align-self-center">${groupCurrentPage[instSafe]} / ${totalPages}</span>`;
      paginationHTML += `<button class="btn btn-outline-primary mx-1" ${groupCurrentPage[instSafe] === totalPages ? 'disabled' : ''} onclick="changeGroupPage('${instSafe}', ${groupCurrentPage[instSafe] + 1})"><i class="fa-solid fa-angle-right"></i></button>`;
      paginationHTML += `<button class="btn btn-outline-primary mx-1" ${groupCurrentPage[instSafe] === totalPages ? 'disabled' : ''} onclick="changeGroupPage('${instSafe}', ${totalPages})"><i class="fa-solid fa-angles-right"></i></button>`;
      paginationContainer.innerHTML = paginationHTML;
    }

    // Change page for a given group.
    function changeGroupPage(instSafe, page) {
      groupCurrentPage[instSafe] = page;
      // Get current filtered group students.
      const { filteredStudents } = getGroupFilteredStudents(instSafe);
      renderGroupTable(instSafe, filteredStudents);
    }

    // Attach filtering for a given accordion item.
    function attachStudentFilters(instSafe) {
      const searchInput = document.querySelector(`#collapse-${instSafe} .student-search-input`);
      const statusFilter = document.querySelector(`#collapse-${instSafe} .student-status-filter`);
      
      function filterAndRender() {
        // Get the original group students from the table data in the accordion.
        // Since our populateStudentAccordion builds the table using the full group,
        // we can extract the full group array from studentsData (or from grouping logic)
        let groupStudents = [];
        const institutionId = sessionStorage.getItem("institution_id");
        if (institutionId) {
          groupStudents = studentsData;
        } else {
          // Group by institution
          const grouped = {};
          studentsData.forEach(student => {
            let inst = "Unknown Institution";
            if (student.institute) {
              try {
                const instData = JSON.parse(student.institute);
                inst = instData.institution_name || "Unknown Institution";
              } catch (e) {
                inst = "Unknown Institution";
              }
            }
            if (!grouped[inst]) grouped[inst] = [];
            grouped[inst].push(student);
          });
          // Find the group for this instSafe:
          for (const key in grouped) {
            if (key.replace(/\s+/g, '') === instSafe) {
              groupStudents = grouped[key];
              break;
            }
          }
        }
        // Apply search filter.
        const query = searchInput.value.toLowerCase();
        let filteredStudents = groupStudents.filter(student => {
          const name = student.name.toLowerCase();
          const email = student.email.toLowerCase();
          return name.includes(query) || email.includes(query);
        });
        // Apply status filter.
        const statusVal = statusFilter.value;
        if (statusVal && statusVal !== "all") {
          filteredStudents = filteredStudents.filter(student => student.status.toLowerCase() === statusVal);
        }
        // Reset current page if necessary.
        groupCurrentPage[instSafe] = 1;
        renderGroupTable(instSafe, filteredStudents);
      }
      searchInput.addEventListener("input", filterAndRender);
      statusFilter.addEventListener("change", filterAndRender);
    }

    // Helper function to return the filtered students for a group.
    function getGroupFilteredStudents(instSafe) {
      let groupStudents = [];
      const institutionId = sessionStorage.getItem("institution_id");
      if (institutionId) {
        groupStudents = studentsData;
      } else {
        const grouped = {};
        studentsData.forEach(student => {
          let inst = "Unknown Institution";
          if (student.institute) {
            try {
              const instData = JSON.parse(student.institute);
              inst = instData.institution_name || "Unknown Institution";
            } catch (e) {
              inst = "Unknown Institution";
            }
          }
          if (!grouped[inst]) grouped[inst] = [];
          grouped[inst].push(student);
        });
        for (const key in grouped) {
          if (key.replace(/\s+/g, '') === instSafe) {
            groupStudents = grouped[key];
            break;
          }
        }
      }
      const searchInput = document.querySelector(`#collapse-${instSafe} .student-search-input`);
      const statusFilter = document.querySelector(`#collapse-${instSafe} .student-status-filter`);
      const query = searchInput.value.toLowerCase();
      let filteredStudents = groupStudents.filter(student => {
        const name = student.name.toLowerCase();
        const email = student.email.toLowerCase();
        return name.includes(query) || email.includes(query);
      });
      const statusVal = statusFilter.value;
      if (statusVal && statusVal !== "all") {
        filteredStudents = filteredStudents.filter(student => student.status.toLowerCase() === statusVal);
      }
      return { filteredStudents };
    }
  </script>
</body>
</html>
