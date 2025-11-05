<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Agent Registered Students</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Optional Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Search input icon */
    #agentStudentSearch {
      padding-left: 40px;
    }
    #agentStudentSearch + i {
      position: absolute;
      top: 50%;
      left: 10px;
      transform: translateY(-50%);
      color: #6c757d;
    }
    /* Blinking red dot for filters */
    .blink-dot {
      height: 10px;
      width: 10px;
      background-color: red;
      border-radius: 50%;
      display: inline-block;
      margin-left: 5px;
      display: none
      /* animation: blink 5s infinite; */
    }
    /* @keyframes blink {
      0%, 50%, 100% { opacity: 1; }
      25%, 75% { opacity: 0; }
    } */
    /* Make the nav-tabs sticky */
    .sticky-tabs {
      position: sticky;
      top: 0;
      z-index: 2;
      background: #fff;
      border-bottom: 1px solid #dee2e6;
    }
  </style>
</head>
<body>
  <div class="d-flex">
    <div>
      @include('Users.Agent.Components.sidebar')
    </div>
    <div class="w-100 main-com">
      @include('Users.Agent.Components.header')
      <div class="container mt-4">
        <!-- Institution Info Card (if needed) -->
        <div id="institutionInfoDiv" class="bg-white p-2 rounded d-none">
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
        <!-- White Container for Actions, Filters, and Table -->
        <div class="bg-white p-4 rounded">
          <!-- Actions Row: Search, Export CSV and Reset Filters -->
          <div class="row mb-3 align-items-center position-relative">
            <div class="col-md-6 position-relative">
              <input type="text" id="agentStudentSearch" class="form-control placeholder-14 text-13" placeholder="Search by Name, Email, or Institute">
              <i class="fa-solid fa-magnifying-glass"></i>
            </div>
            <div class="col-md-6">
              <div class="d-flex justify-content-end gap-2">
                <button id="exportCsvBtn" class="btn btn-outline-success text-13">Export CSV</button>
                <button id="refreshFiltersBtn" class="btn btn-outline-secondary text-13">Reset Filters</button>
              </div>
            </div>
          </div>
          <!-- Sticky Nav-Tabs for Additional Filters -->
          <ul class="nav nav-tabs sticky-tabs mb-3" id="filterTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active text-13" id="status-tab" data-bs-toggle="tab" data-bs-target="#statusFilterTab" type="button" role="tab">
                Status <span id="statusDot" style="display:none;" ></span>
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link text-13" id="program-tab" data-bs-toggle="tab" data-bs-target="#programFilterTab" type="button" role="tab">
                Program <span id="programDot" style="display:none;" ></span>
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link text-13" id="institution-tab" data-bs-toggle="tab" data-bs-target="#institutionFilterTab" type="button" role="tab">
                Institution <span id="institutionDot" style="display:none;" ></span>
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link text-13" id="date-tab" data-bs-toggle="tab" data-bs-target="#dateFilterTab" type="button" role="tab">
                Date Range <span id="dateDot" style="display:none;"></span>
              </button>
            </li>
          </ul>
          <div class="tab-content mb-3" id="filterTabContent">
            <!-- Status Filter -->
            <div class="tab-pane fade show active" id="statusFilterTab" role="tabpanel">
              <select id="agentStatusFilter" class="form-select text-13">
                <option value="">All</option>
                <option value="Approved" selected>Approved</option>
                <option value="NotApproved">Not Approved</option>
              </select>
            </div>
            <!-- Program Filter -->
            <div class="tab-pane fade" id="programFilterTab" role="tabpanel">
              <select id="programSort" class="form-select text-13">
                <option value="">Sort By Program</option>
                <!-- Options will be populated dynamically -->
              </select>
            </div>
            <!-- Institution Filter -->
            <div class="tab-pane fade" id="institutionFilterTab" role="tabpanel">
              <select id="institutionFilter" class="form-select text-13">
                <option value="">All Institutions</option>
                <!-- Options will be populated dynamically -->
              </select>
            </div>
            <!-- Date Range Filter -->
            <div class="tab-pane fade" id="dateFilterTab" role="tabpanel">
              <div class="d-flex gap-2">
                <input type="date" id="headerStartDate" class="form-control text-13" style="width:150px;">
                <input type="date" id="headerEndDate" class="form-control text-13" style="width:150px;">
              </div>
            </div>
          </div>
          <!-- Table -->
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th class="text-13 text-secondary">Name</th>
                  <th class="text-13 text-secondary">Institute</th>
                  <th class="text-13 text-secondary">Email</th>
                  <th class="text-13 text-secondary">Program</th>
                  <th class="text-13 text-secondary">Phone</th>
                  <th class="text-13 text-secondary">Status</th>
                  <th class="text-13 text-secondary">
                    Created At 
                    <i id="sortDate" class="fa-solid fa-sort" style="cursor:pointer;"></i>
                  </th>
                  <th class="text-13 text-secondary">Actions</th>
                </tr>
              </thead>
              <tbody id="studentTableBody">
                <!-- Data will be dynamically populated here -->
              </tbody>
            </table>
          </div>
          <!-- Pagination Container -->
          <div id="paginationContainer" class="mt-3 d-flex justify-content-center gap-2"></div>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Global variables and constants
    const token = sessionStorage.getItem('token');
    let agentStudentsList = [];
    let currentPage = 1;
    const rowsPerPage = 10;
    let sortOrder = "asc"; // sort order for created_at

    // On DOM load, check for token and fetch students.
    document.addEventListener("DOMContentLoaded", function() {
      if (!token) {
        window.location.href = "/";
      }
      fetchAgentStudents();
    });

    // Fetch agent registered students (using agent_uid from sessionStorage)
    function fetchAgentStudents() {
      const agentUid = sessionStorage.getItem("agent_uid");
      const url = `/api/agent/students?agent_uid=${encodeURIComponent(agentUid)}`;
      fetch(url, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Authorization': token
        }
      })
      .then(response => {
        if (response.status === 401 || response.status === 403) {
          window.location.href = '/Unauthorised';
          throw new Error('Unauthorized Access');
        }
        return response.json();
      })
      .then(data => {
        if (data.status === 'success' && data.data.length > 0) {
          agentStudentsList = data.data;
          currentPage = 1;
          populateProgramSort(agentStudentsList);
          populateInstitutionFilter(agentStudentsList);
          let filteredData = applyAllFilters();
          renderAgentStudents(getPaginatedData(filteredData));
          renderPagination(filteredData);
          updateFilterDots();
        } else {
          renderAgentStudents([]);
          document.getElementById('paginationContainer').innerHTML = '';
        }
      })
      .catch(error => {
        console.error('Error fetching agent students:', error);
        document.getElementById('studentTableBody').innerHTML = `<tr><td colspan="12" class="text-center">Failed to fetch data. Please try again.</td></tr>`;
      });
    }

    // Populate Program Sort dropdown based on given data
    function populateProgramSort(data) {
      const programSort = document.getElementById('programSort');
      let programs = new Set();
      data.forEach(student => {
        if (student.course) {
          try {
            const courseObj = JSON.parse(student.course);
            if (courseObj.program_name) programs.add(courseObj.program_name);
          } catch(e) { }
        }
      });
      programSort.innerHTML = '<option value="">Sort By Program</option>';
      Array.from(programs).sort().forEach(prog => {
        const option = document.createElement('option');
        option.value = prog;
        option.text = prog;
        programSort.appendChild(option);
      });
    }

    // Populate Institution Filter dropdown based on given data
    function populateInstitutionFilter(data) {
      const institutionFilter = document.getElementById('institutionFilter');
      let institutions = new Set();
      data.forEach(student => {
        if (student.institute) {
          try {
            const instObj = JSON.parse(student.institute);
            if (instObj.institution_name) institutions.add(instObj.institution_name);
          } catch(e) { }
        }
      });
      institutionFilter.innerHTML = '<option value="">All Institutions</option>';
      Array.from(institutions).sort().forEach(inst => {
        const option = document.createElement('option');
        option.value = inst;
        option.text = inst;
        institutionFilter.appendChild(option);
      });
    }

    // Apply all filters (search, status, program, institution, date range)
    function applyAllFilters() {
      let filtered = agentStudentsList.slice();
      const searchTerm = document.getElementById('agentStudentSearch').value.trim().toLowerCase();
      const statusFilter = document.getElementById('agentStatusFilter').value;
      const programFilter = document.getElementById('programSort').value;
      const institutionFilter = document.getElementById('institutionFilter').value;
      const startDate = document.getElementById('headerStartDate').value;
      const endDate = document.getElementById('headerEndDate').value;

      if (searchTerm) {
        filtered = filtered.filter(student => {
          const studentName = student.name ? student.name.toLowerCase() : "";
          const studentEmail = student.email ? student.email.toLowerCase() : "";
          let instName = "";
          if (student.institute) {
            try {
              const instObj = JSON.parse(student.institute);
              instName = instObj.institution_name ? instObj.institution_name.toLowerCase() : "";
            } catch(e) { }
          }
          return studentName.includes(searchTerm) || studentEmail.includes(searchTerm) || instName.includes(searchTerm);
        });
      }
      if (statusFilter) {
        filtered = filtered.filter(student => {
          return statusFilter === "Approved" ? student.status === "Active" : student.status !== "Active";
        });
      }
      if (programFilter) {
        filtered = filtered.filter(student => {
          if (student.course) {
            try {
              const courseObj = JSON.parse(student.course);
              return courseObj.program_name === programFilter;
            } catch(e) {
              return false;
            }
          }
          return false;
        });
      }
      if (institutionFilter) {
        filtered = filtered.filter(student => {
          if (student.institute) {
            try {
              const instObj = JSON.parse(student.institute);
              return instObj.institution_name === institutionFilter;
            } catch(e) {
              return false;
            }
          }
          return false;
        });
      }
      if (startDate && endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        filtered = filtered.filter(student => {
          const created = new Date(student.created_at);
          return created >= start && created <= end;
        });
      }
      return filtered;
    }

    // Update blinking dot indicators on each filter tab
    function updateFilterDots() {
      document.getElementById('statusDot').style.display = document.getElementById('agentStatusFilter').value ? 'inline-block' : 'none';
      document.getElementById('programDot').style.display = document.getElementById('programSort').value ? 'inline-block' : 'none';
      document.getElementById('institutionDot').style.display = document.getElementById('institutionFilter').value ? 'inline-block' : 'none';
      let startDate = document.getElementById('headerStartDate').value;
      let endDate = document.getElementById('headerEndDate').value;
      document.getElementById('dateDot').style.display = (startDate || endDate) ? 'inline-block' : 'none';
    }

    // Render Agent Students table
    function renderAgentStudents(students) {
      const tableBody = document.getElementById('studentTableBody');
      tableBody.innerHTML = '';
      if (students.length > 0) {
        students.forEach(student => {
          const studentData = JSON.stringify(student).replace(/"/g, '&quot;');
          const instituteName = student.institute ? JSON.parse(student.institute).institution_short_code : 'N/A';
          const courseData = student.course ? JSON.parse(student.course) : null;
          const programName = courseData ? courseData.program_name : 'N/A';
          const programParts = programName.split('(');
          const abbreviation = programParts[1] ? '(' + programParts[1] : '';
          const statusText = student.status === "Active" ? "Approved" : "Not Approved";
          const row = `
            <tr>
              <td class="text-13 align-middle">${student.name}</td>
              <td class="text-13 align-middle">${instituteName}</td>
              <td class="text-13 align-middle">${student.email}</td>
              <td class="text-13 align-middle">${abbreviation}</td>
              <td class="text-13 align-middle">${student.phone}</td>
              <td class="text-13 align-middle ${student.status === 'Active' ? 'text-success' : 'text-danger'}">${statusText}</td>
              <td class="text-13 align-middle">${student.created_at ? student.created_at : 'N/A'}</td>
              <td class="text-13 align-middle">
                <span class="d-flex justify-content-center">
                  <button type="button" class="btn btn-outline-primary btn-sm text-13 m-1" onclick="printStudentDetails(${studentData})">
                    <i class="fa-solid fa-print"></i>
                  </button>
                </span>
              </td>
            </tr>
          `;
          tableBody.innerHTML += row;
        });
      } else {
        tableBody.innerHTML = `<tr><td colspan="12" class="text-center">No students found based on selected criteria.</td></tr>`;
      }
    }

    // Get paginated data based on currentPage and rowsPerPage
    function getPaginatedData(data) {
      const start = (currentPage - 1) * rowsPerPage;
      return data.slice(start, start + rowsPerPage);
    }

    // Render pagination controls
    function renderPagination(filteredData) {
      const totalPages = Math.ceil(filteredData.length / rowsPerPage);
      if (totalPages === 0) {
        document.getElementById('paginationContainer').innerHTML = '';
        return;
      }
      let paginationHTML = '';
      paginationHTML += `<button class="btn text-13 btn-outline-primary mx-1" ${currentPage === 1 ? 'disabled' : ''} onclick="changePageAgent(1)"><i class="fa-solid fa-angles-left"></i></button>`;
      paginationHTML += `<button class="btn text-13 btn-outline-primary mx-1" ${currentPage === 1 ? 'disabled' : ''} onclick="changePageAgent(${currentPage - 1})"><i class="fa-solid fa-angle-left"></i></button>`;
      paginationHTML += `<span class="mx-2 text-13 align-self-center">${currentPage} / ${totalPages}</span>`;
      paginationHTML += `<button class="btn text-13 btn-outline-primary mx-1" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePageAgent(${currentPage + 1})"><i class="fa-solid fa-angle-right"></i></button>`;
      paginationHTML += `<button class="btn text-13 btn-outline-primary mx-1" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePageAgent(${totalPages})"><i class="fa-solid fa-angles-right"></i></button>`;
      document.getElementById('paginationContainer').innerHTML = paginationHTML;
    }

    function changePageAgent(page) {
      currentPage = page;
      let filteredData = applyAllFilters();
      filteredData.sort((a, b) => {
        const dateA = new Date(a.created_at);
        const dateB = new Date(b.created_at);
        return sortOrder === 'asc' ? dateA - dateB : dateB - dateA;
      });
      renderAgentStudents(getPaginatedData(filteredData));
      renderPagination(filteredData);
    }

    // Event listeners for filters and update blinking dots
    document.getElementById('agentStudentSearch').addEventListener('input', function() {
      currentPage = 1;
      let filtered = applyAllFilters();
      filtered.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
      renderAgentStudents(getPaginatedData(filtered));
      renderPagination(filtered);
      updateFilterDots();
    });

    document.getElementById('agentStatusFilter').addEventListener('change', function() {
      currentPage = 1;
      let filtered = applyAllFilters();
      filtered.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
      renderAgentStudents(getPaginatedData(filtered));
      renderPagination(filtered);
      updateFilterDots();
    });

    document.getElementById('programSort').addEventListener('change', function() {
      currentPage = 1;
      let filtered = applyAllFilters();
      filtered.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
      renderAgentStudents(getPaginatedData(filtered));
      renderPagination(filtered);
      updateFilterDots();
    });

    document.getElementById('institutionFilter').addEventListener('change', function() {
      currentPage = 1;
      let filtered = applyAllFilters();
      populateProgramSort(filtered);
      filtered.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
      renderAgentStudents(getPaginatedData(filtered));
      renderPagination(filtered);
      updateFilterDots();
    });

    document.getElementById('headerStartDate').addEventListener('change', function() {
      currentPage = 1;
      let filtered = applyAllFilters();
      filtered.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
      renderAgentStudents(getPaginatedData(filtered));
      renderPagination(filtered);
      updateFilterDots();
    });
    document.getElementById('headerEndDate').addEventListener('change', function() {
      currentPage = 1;
      let filtered = applyAllFilters();
      filtered.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
      renderAgentStudents(getPaginatedData(filtered));
      renderPagination(filtered);
      updateFilterDots();
    });

    // Sort by Created At column
    document.getElementById('sortDate').addEventListener('click', function() {
      sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
      this.className = sortOrder === 'asc' ? 'fa-solid fa-sort-up' : 'fa-solid fa-sort-down';
      currentPage = 1;
      let filtered = applyAllFilters();
      filtered.sort((a, b) => {
        const dateA = new Date(a.created_at);
        const dateB = new Date(b.created_at);
        return sortOrder === 'asc' ? dateA - dateB : dateB - dateA;
      });
      renderAgentStudents(getPaginatedData(filtered));
      renderPagination(filtered);
    });

    // Export CSV of the filtered data
    document.getElementById('exportCsvBtn').addEventListener('click', function() {
      let filtered = applyAllFilters();
      if (filtered.length === 0) {
        Swal.fire('Error', 'No data available to export based on selected criteria.', 'error');
        return;
      }
      let csvContent = "data:text/csv;charset=utf-8,";
      csvContent += "Name,Institute,Email,Program,Phone,Status,Created At\n";
      filtered.forEach(student => {
        const instituteName = student.institute ? JSON.parse(student.institute).institution_short_code : 'N/A';
        const courseData = student.course ? JSON.parse(student.course) : null;
        const programName = courseData ? courseData.program_name : 'N/A';
        const statusText = student.status === "Active" ? "Approved" : "Not Approved";
        let row = `"${student.name}","${instituteName}","${student.email}","${programName}","${student.phone}","${statusText}","${student.created_at}"`;
        csvContent += row + "\n";
      });
      const encodedUri = encodeURI(csvContent);
      const link = document.createElement("a");
      link.setAttribute("href", encodedUri);
      link.setAttribute("download", "agent_registered_students.csv");
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    });

    // Reset Filters Button
    document.getElementById('refreshFiltersBtn').addEventListener('click', function() {
      document.getElementById('agentStudentSearch').value = "";
      document.getElementById('agentStatusFilter').value = "";
      document.getElementById('programSort').value = "";
      document.getElementById('institutionFilter').value = "";
      document.getElementById('headerStartDate').value = "";
      document.getElementById('headerEndDate').value = "";
      currentPage = 1;
      populateProgramSort(agentStudentsList);
      populateInstitutionFilter(agentStudentsList);
      renderAgentStudents(getPaginatedData(agentStudentsList));
      renderPagination(agentStudentsList);
      updateFilterDots();
    });

    // Print function (unchanged except for including additional student details)
    function printStudentDetails(student) {
      const currentDateTime = new Date().toLocaleString();
      const printWindow = window.open('', '', 'height=900,width=1200');
      let instituteData = student.institute ? JSON.parse(student.institute) : null;
      let courseData = student.course ? JSON.parse(student.course) : null;
      
      function calculateMarks(data) {
        if (!data) return { totalMarks: "N/A", percentage: "N/A" };
        try {
          let parsedData = JSON.parse(data);
          if (Array.isArray(parsedData) && parsedData.length > 0) {
            let totalFullMarks = 0, totalMarksObtained = 0;
            parsedData.forEach(item => {
              totalFullMarks += parseFloat(item.fullMarks);
              totalMarksObtained += parseFloat(item.marksObtained);
            });
            let percentage = totalFullMarks ? ((totalMarksObtained / totalFullMarks) * 100).toFixed(2) + '%' : 'N/A';
            return { totalMarks: totalMarksObtained, percentage: percentage };
          }
        } catch (err) {
          console.error("Error parsing marks data", err);
        }
        return { totalMarks: "N/A", percentage: "N/A" };
      }
      let classXMarks = calculateMarks(student.class_x_data);
      let classXIIMarks = calculateMarks(student.class_xii_data);
      let collegeMarks = calculateMarks(student.college_data);
      
      printWindow.document.write(`
        <html>
        <head>
          <title>Student Profile</title>
          <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
          <style>
            @media print {
              .button-container { display: none; }
            }
          </style>
        </head>
        <body class="container mt-4">
          <div class="row border p-3">
            <div class="col-12 text-end text-secondary text-13">${currentDateTime}</div>
            <div class="col-12 text-center">
              <img src="${student.student_photo ? '/assets/student_documents/' + student.student_photo : '/assets/web_assets/default-profile.jpg'}" class="rounded-circle border border-primary p-1" width="100" height="100">
              <h4 class="mt-2 text-13">${student.name}</h4>
              <p class="text-13">${student.email} | ${student.phone} | WhatsApp: ${student['whatsapp-no']}</p>
            </div>
            <div class="col-6 text-13"><strong>Roll No:</strong> ${student.role_number ? student.role_number : 'N/A'}</div>
            <div class="col-6 text-13"><strong>Program:</strong> ${courseData ? courseData.program_name : 'N/A'}</div>
            <div class="col-12 bg-light p-2 mt-2">
              <h6 class="text-primary text-13"><i class="fa-solid fa-user"></i> Personal Information</h6>
            </div>
            <div class="col-6 text-13"><strong>Date of Birth:</strong> ${student.date_of_birth || 'N/A'}</div>
            <div class="col-6 text-13"><strong>Identity:</strong> ${student.identity_type || 'N/A'} - ${student.identity_details || 'N/A'}</div>
            <div class="col-12 bg-light p-2 mt-2">
              <h6 class="text-primary text-13"><i class="fa-solid fa-map-marker-alt"></i> Address</h6>
            </div>
            <div class="col-12 text-13">${student.city || 'N/A'}, ${student.state || 'N/A'}, ${student.country || 'N/A'} - ${student.pin || 'N/A'}</div>
            <div class="col-12 bg-light p-2 mt-2">
              <h6 class="text-primary text-13"><i class="fa-solid fa-users"></i> Parent Details</h6>
            </div>
            <div class="col-12 text-13">
              <strong>Father:</strong> ${student.father_name || 'N/A'} 
              (<span>${student.father_occupation || 'N/A'}</span> | <span>${student.father_phone || 'N/A'}</span>)
            </div>
            <div class="col-12 text-13">
              <strong>Father's Address:</strong> ${student.father_street || ''} ${student.father_po || ''} ${student.father_ps || ''} ${student.father_city || ''} ${student.father_state || ''} ${student.father_country || ''} ${student.father_pincode || ''}
            </div>
            <div class="col-12 text-13">
              <strong>Mother:</strong> ${student.mother_name || 'N/A'} 
              (<span>${student.mother_occupation || 'N/A'}</span> | <span>${student.mother_phone || 'N/A'}</span>)
            </div>
            <div class="col-12 text-13">
              <strong>Mother's Address:</strong> ${student.mother_street || ''} ${student.mother_po || ''} ${student.mother_ps || ''} ${student.mother_city || ''} ${student.mother_state || ''} ${student.mother_country || ''} ${student.mother_pincode || ''}
            </div>
            <div class="col-12 bg-light p-2 mt-2">
              <h6 class="text-primary text-13"><i class="fa-solid fa-graduation-cap"></i> Academic</h6>
            </div>
            ${student.class_x_board ? `
              <div class="col-12 text-13"><strong>Class X:</strong> ${student.class_x_board}</div>
              <div class="col-6 text-13"><strong>Total Marks:</strong> ${classXMarks.totalMarks}</div>
              <div class="col-6 text-13"><strong>Overall Percentage:</strong> ${classXMarks.percentage}</div>
              <hr/>
            ` : ''}
            ${student.class_xii_board ? `
              <div class="col-12 text-13"><strong>Class XII:</strong> ${student.class_xii_board}</div>
              <div class="col-6 text-13"><strong>Total Marks:</strong> ${classXIIMarks.totalMarks}</div>
              <div class="col-6 text-13"><strong>Overall Percentage:</strong> ${classXIIMarks.percentage}</div>
              <hr/>
            ` : ''}
            ${student.college_university ? `
              <div class="col-12 text-13"><strong>College/University:</strong> ${student.college_university}</div>
              <div class="col-6 text-13"><strong>Total Marks:</strong> ${collegeMarks.totalMarks}</div>
              <div class="col-6 text-13"><strong>Overall Percentage:</strong> ${collegeMarks.percentage}</div>
              <hr/>
            ` : ''}
            <div class="col-12 text-center mt-3 button-container">
              <button class="btn btn-success btn-sm" onclick="window.print()">Print</button>
            </div>
          </div>
        </body>
        </html>
      `);
      printWindow.document.close();
    }
  </script>
  <!-- Include Bootstrap Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
