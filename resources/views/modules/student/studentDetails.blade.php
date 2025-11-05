<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Students</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Optional Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
      <div class="container mt-4">
        <!-- Institution Info Card (only shown if institution data is in sessionStorage) -->
       
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

        <p class="my-4 text-secondary text-14">
          <i class="fa-solid fa-angle-right"></i>
          <span class="text-primary">List of Students</span>
        </p>
        
        <!-- Nav Tabs -->
        <ul class="nav nav-tabs" id="studentTabs" role="tablist">
          <li class="nav-item text-13" role="presentation">
            <button class="nav-link active" id="normal-tab" data-bs-toggle="tab" data-bs-target="#normalStudents" type="button" role="tab" aria-controls="normalStudents" aria-selected="true" onclick="switchTab('normal')">Students</button>
          </li>
          <li class="nav-item text-13" role="presentation">
            <button class="nav-link" id="agent-tab" data-bs-toggle="tab" data-bs-target="#agentStudents" type="button" role="tab" aria-controls="agentStudents" aria-selected="false" onclick="switchTab('agent')">Agent Registered</button>
          </li>
        </ul>
        <div class="tab-content bg-white p-4 rounded" id="studentTabsContent">
          <!-- Normal Students Tab -->
          <div class="tab-pane fade show active" id="normalStudents" role="tabpanel" aria-labelledby="normal-tab">
            <div class="row mb-3 align-items-center justify-content-between">
              <div class="col-md-6 position-relative">
                <input type="text" id="studentSearch" class="form-control placeholder-14 text-13 ps-5" placeholder="Search by Name, Email, or Institute">
                <i class="fa-solid fa-search position-absolute text-secondary" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
              </div>
              <div class="col-md-4 text-end">
                <select id="statusFilter" class="form-select text-13">
                  <option value="">All Statuses</option>
                  <option value="Active" selected>Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th class="text-13 text-secondary">Name</th>
                    <th class="text-13 text-secondary">Institute</th>
                    <th class="text-13 text-secondary">Email</th>
                    <th class="text-13 text-secondary">Program</th>
                    <th class="text-13 text-secondary">Password</th>
                    <th class="text-13 text-secondary">Phone</th>
                    <th class="text-13 text-secondary">Semester</th>
                    <th class="text-13 text-secondary">Status</th>
                    <th class="text-13 text-secondary">Date</th>
                    <th class="text-13 text-secondary">Actions</th>
                  </tr>                
                </thead>
                <tbody id="studentTableBody">
                  <!-- Data will be dynamically populated here -->
                </tbody>
              </table>
            </div>
            <div id="paginationContainer" class="mt-3 d-flex justify-content-center gap-2"></div>
          </div>
          <!-- Agent Registered Tab -->
          <div class="tab-pane fade" id="agentStudents" role="tabpanel" aria-labelledby="agent-tab">
            <div class="row mb-3 align-items-center justify-content-between bg-white">
              <div class="col-md-6 position-relative">
                <input type="text" id="agentStudentSearch" class="form-control placeholder-14 text-13 ps-5" placeholder="Search by Name, Email, or Institute">
                <i class="fa-solid fa-search position-absolute text-secondary" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
              </div>
              <div class="col-md-4 text-end">
                <select id="agentStatusFilter" class="form-select text-13">
                  <option value="">All</option>
                  <option value="Approved" selected>Approved</option>
                  <option value="NotApproved">Not Approved</option>
                </select>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th class="text-13 text-secondary">Name</th>
                    <th class="text-13 text-secondary">Institute</th>
                    <th class="text-13 text-secondary">Program</th>
                    <th class="text-13 text-secondary">Email</th>
                    <th class="text-13 text-secondary">Phone</th>
                    <th class="text-13 text-secondary">Referred By</th>
                    <th class="text-13 text-secondary">Status</th>
                    <th class="text-13 text-secondary">Date</th>
                    <th class="text-13 text-secondary">Actions</th>
                  </tr>                
                </thead>
                <tbody id="agentStudentTableBody">
                  <!-- Data will be dynamically populated here -->
                </tbody>
              </table>
            </div>
            <div id="agentPaginationContainer" class="mt-3 d-flex justify-content-center gap-2"></div>
          </div>
        </div>
      </div>

  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Redirect if token is missing
      if (!sessionStorage.getItem("token")) {
        window.location.href = "/";
      }
      // Show institution info card if details exist
      const instName = sessionStorage.getItem("institution_name");
      const instType = sessionStorage.getItem("institution_type");
      const institutionInfoDiv = document.getElementById("institutionInfoDiv");
      if (instName && instType) {
        const instLogoPath = sessionStorage.getItem("institution_logo");
      const logoImg = document.getElementById("instImg");
      logoImg.src = instLogoPath || '/assets/web_assets/logo.png';
        document.getElementById("instituteName").innerHTML = `
          <span class="text-secondary">${instName}</span>`;
        document.getElementById("instituteType").innerHTML = `
          <i class="fa-solid fa-graduation-cap me-2"></i>
          ${instType}`;
        institutionInfoDiv.classList.remove("d-none");
      }
      // Fetch institutions on page load
      fetchInstitutions();
    });
    document.addEventListener("DOMContentLoaded", function() {
      if (!sessionStorage.getItem("token")) {
        window.location.href = "/";
      }
      // On page refresh, default to the "Normal Students" tab
      switchTab('normal');
    });
    let currentTab = "normal"; // Track current tab
    const token = sessionStorage.getItem('token');
    let studentsList = []; // For normal students
    let agentStudentsList = []; // For agent registered students
    let currentPage = 1;
    const rowsPerPage = 10; // 10 entries per page

    function switchTab(tab) {
      currentTab = tab;
      currentPage = 1;
      if (tab === "normal") {
        fetchNormalStudents();
      } else if (tab === "agent") {
        fetchAgentStudents();
      }
    }

    // Normal Students fetch function
    function fetchNormalStudents() {
      const instituteId = sessionStorage.getItem("institution_id");
      const url = instituteId 
        ? `/api/view-students-by-institute?institute_id=${instituteId}` 
        : '/api/view-students';
      
      fetch(url, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Authorization': token
        },
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
          studentsList = data.data;
          currentPage = 1;
          const filteredData = getFilteredStudents('', document.getElementById('statusFilter').value);
          const paginatedData = getPaginatedData(filteredData);
          renderNormalStudents(paginatedData);
          renderPaginationNormal(filteredData);
        } else {
          renderNormalStudents([]);
          document.getElementById('paginationContainer').innerHTML = '';
        }
      })
      .catch(error => {
        console.error('Error fetching students:', error);
        document.getElementById('studentTableBody').innerHTML = `<tr><td colspan="8" class="text-center">Failed to fetch data. Please try again.</td></tr>`;
      });
    }

    // Agent Students fetch function (updated to fetch by institution id if available)
    function fetchAgentStudents() {
      const instituteId = sessionStorage.getItem("institution_id");
      const url = instituteId 
        ? `/api/agent/students?institute_id=${instituteId}` 
        : '/api/agent/students';
      fetch(url, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Authorization': token
        },
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
          renderAgentStudents();
          renderPaginationAgent();
        } else {
          renderAgentStudents();
          document.getElementById('agentPaginationContainer').innerHTML = '';
        }
      })
      .catch(error => {
        console.error('Error fetching agent students:', error);
        document.getElementById('agentStudentTableBody').innerHTML = `<tr><td colspan="12" class="text-center">Failed to fetch data. Please try again.</td></tr>`;
      });
    }

    // Render Normal Students (unchanged)
    function renderNormalStudents(students) {
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
          const studentId = student.id && student.id.$oid ? student.id.$oid : (student.id || '');
          const row = `
            <tr>
              <td class="text-13 align-middle">${student.name}</td>
              <td class="text-13 align-middle">${instituteName}</td>
              <td class="text-13 align-middle">${student.email}</td>
              <td class="text-13 align-middle">${abbreviation}</td>
              <td class="text-13 align-middle">
                ${student.plain_password ?
                `<span class="text-13 d-flex">
                    <input type="password" id="password-${studentId}" value="${student.plain_password}" readonly style="border: none; background: transparent; flex: 1;">
                    <span class="toggle-password" onclick="toggleStudentPassword('${studentId}')" style="cursor: pointer; margin-left: 5px;"><i class="fa-solid fa-eye-slash"></i></span>
                  </span>` :
                "N/A"}
              </td>
              <td class="text-13 align-middle">${student.phone}</td>
              <td class="text-13 align-middle">${student.current_semester}</td>
              <td class="text-13 align-middle ${student.status === 'Active' ? 'text-success' : 'text-danger'}">${student.status}</td>
              <td class="text-13 align-middle">${student.created_at}</td>
              <td class="text-13 align-middle">
                <span class="text-13 d-flex justify-content-center">
                  <button type="button" class="btn btn-outline-primary btn-sm text-13 m-1" onclick="printStudentDetails(${studentData})">
                    <i class="fa-solid fa-print"></i>
                  </button>
                  <button type="button" class="btn btn-sm ${student.status === 'Active' ? 'btn-outline-danger' : 'btn-outline-success'} text-13 m-1" onclick="toggleStudentStatus('${student.email}', '${student.status}')">
                    <i class="fa-solid ${student.status === 'Active' ? 'fa-ban' : 'fa-power-off'}"></i>
                  </button>
                </span>
              </td>
            </tr>
          `;
          tableBody.innerHTML += row;
        });
      } else {
        tableBody.innerHTML = `<tr><td colspan="8" class="text-center">No students found.</td></tr>`;
      }
    }

    // Helper: Group not-approved referrals by identity_type and identity_details
    function groupNotApproved(data) {
      let groups = {};
      data.forEach(student => {
        let key = (student.identity_type || "Unknown") + '|' + (student.identity_details || "Unknown");
        if (!groups[key]) {
          groups[key] = [];
        }
        groups[key].push(student);
      });
      let groupArray = [];
      for (let key in groups) {
        let group = groups[key];
        if (group.length > 1) {
          groupArray.push({
            group: true,
            identity_type: group[0].identity_type,
            identity_details: group[0].identity_details,
            count: group.length,
            representative: group[0],
            referrals: group
          });
        } else {
          groupArray.push(group[0]);
        }
      }
      return groupArray;
    }

    // Render Agent Students with grouping logic
    function renderAgentStudents() {
      const searchTerm = document.getElementById('agentStudentSearch').value;
      const agentStatus = document.getElementById('agentStatusFilter').value;
      let filtered = filterAgentStudents(agentStatus, searchTerm);
      let combined = [];
      if (agentStatus === "Approved") {
        combined = filtered;
      } else if (agentStatus === "NotApproved") {
        combined = groupNotApproved(filtered);
      } else {
        let notApproved = filtered.filter(s => s.status !== "Active");
        let groupedNotApproved = groupNotApproved(notApproved);
        let approved = filtered.filter(s => s.status === "Active");
        combined = groupedNotApproved.concat(approved);
      }
      window.combinedAgentData = combined;
      const paginatedData = getPaginatedData(combined);
      const tableBody = document.getElementById('agentStudentTableBody');
      tableBody.innerHTML = '';
      if (paginatedData.length > 0) {
        paginatedData.forEach(item => {
          if (item.group) {
            // Grouped item: show "Referred By" count in a separate column.
            const rep = item.representative;
            const instituteName = rep.institute ? JSON.parse(rep.institute).institution_short_code : 'N/A';
            const courseData = rep.course ? JSON.parse(rep.course) : null;
            const programName = courseData ? courseData.program_name : 'N/A';
            const programParts = programName.split('(');
            const abbreviation = programParts[1] ? '(' + programParts[1] : '';
            const row = `
              <tr>
                <td class="text-13 align-middle">${rep.name}</td>
                <td class="text-13 align-middle">${instituteName}</td>
                <td class="text-13 align-middle">${abbreviation}</td>
                <td class="text-13 align-middle">${rep.email}</td>
                <td class="text-13 align-middle">${rep.phone}</td>
                <td class="text-13 align-middle text-success">Referred By (${item.count})</td>
                <td class="text-13 align-middle text-danger">Not Approved</td>
                <td class="text-13 align-middle">${rep.updated_at ? rep.updated_at : 'N/A'}</td>
                <td class="text-13 align-middle">
                  <span class="d-flex justify-content-center">
                    <button type="button" class="btn btn-outline-info btn-sm text-13 m-1" onclick='showGroupDetails(${JSON.stringify(item).replace(/"/g, "&quot;")})'>
                      <i class="fa-solid fa-circle-info"></i>
                    </button>
                  </span>
                </td>
              </tr>
            `;
            tableBody.innerHTML += row;
          } else {
            // Single item: for non-group items, leave "Referred By" column blank.
            const student = item;
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
                <td class="text-13 align-middle">${abbreviation}</td>
                <td class="text-13 align-middle">${student.email}</td>
                <td class="text-13 align-middle">${student.phone}</td>
                <td class="text-13 align-middle">${student.agent_name || 'N/A'}</td>
                <td class="text-13 align-middle ${student.status === 'Active' ? 'text-success' : 'text-danger'}">${statusText}</td>
                <td class="text-13 align-middle">${student.updated_at ? student.updated_at : 'N/A'}</td>
                <td class="text-13 align-middle">
                  <span class="d-flex justify-content-center">
                    <button type="button" class="btn btn-outline-info btn-sm text-13 m-1" onclick='showSingleAgentDetails(${JSON.stringify(student).replace(/"/g, "&quot;")})'>
                      <i class="fa-solid fa-circle-info"></i>
                    </button>
                  </span>
                </td>
              </tr>
            `;
            tableBody.innerHTML += row;
          }
        });
      } else {
        tableBody.innerHTML = `<tr><td colspan="12" class="text-center">No students found.</td></tr>`;
      }
    }

    // Render pagination controls for Normal Students tab (unchanged)
    function renderPaginationNormal(filteredData) {
      if(filteredData.length === 0) {
        document.getElementById('paginationContainer').innerHTML = '';
        return;
      }
      const totalPages = Math.ceil(filteredData.length / rowsPerPage);
      let paginationHTML = '';
      paginationHTML += `<button class="btn btn-outline-primary mx-1" ${currentPage === 1 ? 'disabled' : ''} onclick="changePageNormal(1)"><i class="fa-solid fa-angles-left"></i></button>`;
      paginationHTML += `<button class="btn btn-outline-primary mx-1" ${currentPage === 1 ? 'disabled' : ''} onclick="changePageNormal(${currentPage - 1})"><i class="fa-solid fa-angle-left"></i></button>`;
      paginationHTML += `<span class="mx-2 text-13 align-self-center">${currentPage} / ${totalPages}</span>`;
      paginationHTML += `<button class="btn btn-outline-primary mx-1" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePageNormal(${currentPage + 1})"><i class="fa-solid fa-angle-right"></i></button>`;
      paginationHTML += `<button class="btn btn-outline-primary mx-1" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePageNormal(${totalPages})"><i class="fa-solid fa-angles-right"></i></button>`;
      document.getElementById('paginationContainer').innerHTML = paginationHTML;
    }

    // Render pagination controls for Agent Students tab using combined data (unchanged)
    function renderPaginationAgent() {
      const combined = window.combinedAgentData || [];
      if(combined.length === 0) {
        document.getElementById('agentPaginationContainer').innerHTML = '';
        return;
      }
      const totalPages = Math.ceil(combined.length / rowsPerPage);
      let paginationHTML = '';
      paginationHTML += `<button class="btn btn-outline-primary mx-1" ${currentPage === 1 ? 'disabled' : ''} onclick="changePageAgent(1)"><i class="fa-solid fa-angles-left"></i></button>`;
      paginationHTML += `<button class="btn btn-outline-primary mx-1" ${currentPage === 1 ? 'disabled' : ''} onclick="changePageAgent(${currentPage - 1})"><i class="fa-solid fa-angle-left"></i></button>`;
      paginationHTML += `<span class="mx-2 text-13 align-self-center">${currentPage} / ${totalPages}</span>`;
      paginationHTML += `<button class="btn btn-outline-primary mx-1" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePageAgent(${currentPage + 1})"><i class="fa-solid fa-angle-right"></i></button>`;
      paginationHTML += `<button class="btn btn-outline-primary mx-1" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePageAgent(${totalPages})"><i class="fa-solid fa-angles-right"></i></button>`;
      document.getElementById('agentPaginationContainer').innerHTML = paginationHTML;
    }

    // Change page functions
    function changePageNormal(page) {
      currentPage = page;
      const searchTerm = document.getElementById('studentSearch').value;
      const statusFilter = document.getElementById('statusFilter').value;
      const filteredData = getFilteredStudents(searchTerm, statusFilter);
      renderNormalStudents(getPaginatedData(filteredData));
      renderPaginationNormal(filteredData);
    }
    function changePageAgent(page) {
      currentPage = page;
      renderAgentStudents();
      renderPaginationAgent();
    }

    // Filter functions (unchanged)
    function getFilteredStudents(query = '', statusFilter = '') {
      let filtered = studentsList;
      if (query) {
        const lowerQuery = query.toLowerCase();
        filtered = filtered.filter(student => {
          const nameMatch = student.name.toLowerCase().includes(lowerQuery);
          const emailMatch = student.email.toLowerCase().includes(lowerQuery);
          let instituteMatch = false;
          if (student.institute) {
            try {
              const instituteData = JSON.parse(student.institute);
              instituteMatch = instituteData.institution_name.toLowerCase().includes(lowerQuery);
            } catch (e) {
              instituteMatch = false;
            }
          }
          return nameMatch || emailMatch || instituteMatch;
        });
      }
      if (statusFilter) {
        filtered = filtered.filter(student => {
          if (statusFilter === "Active") {
            return student.status === "Active";
          } else {
            return student.status !== "Active";
          }
        });
      }
      return filtered;
    }
    function filterAgentStudents(agentStatus, searchTerm) {
      let filtered = agentStudentsList;
      if (searchTerm) {
        const lowerQuery = searchTerm.toLowerCase();
        filtered = filtered.filter(student => {
          return student.name.toLowerCase().includes(lowerQuery) || student.email.toLowerCase().includes(lowerQuery);
        });
      }
      if (agentStatus) {
        if (agentStatus === "Approved") {
          filtered = filtered.filter(student => student.status === "Active");
        } else if (agentStatus === "NotApproved") {
          filtered = filtered.filter(student => student.status !== "Active");
        }
      }
      return filtered;
    }

    // Event listeners for search and filter
    document.getElementById('studentSearch').addEventListener('input', function() {
      currentPage = 1;
      const searchTerm = this.value;
      const statusFilter = document.getElementById('statusFilter').value;
      const filtered = getFilteredStudents(searchTerm, statusFilter);
      renderNormalStudents(getPaginatedData(filtered));
      renderPaginationNormal(filtered);
    });
    document.getElementById('statusFilter').addEventListener('change', function() {
      currentPage = 1;
      const searchTerm = document.getElementById('studentSearch').value;
      const statusFilter = this.value;
      const filtered = getFilteredStudents(searchTerm, statusFilter);
      renderNormalStudents(getPaginatedData(filtered));
      renderPaginationNormal(filtered);
    });
    document.getElementById('agentStudentSearch').addEventListener('input', function() {
      currentPage = 1;
      renderAgentStudents();
      renderPaginationAgent();
    });
    document.getElementById('agentStatusFilter').addEventListener('change', function() {
      currentPage = 1;
      renderAgentStudents();
      renderPaginationAgent();
    });

    // Toggle Student Status Function (unchanged)
    function toggleStudentStatus(email, currentStatus) {
      let newState = currentStatus === "Active" ? "Inactive" : "Active";
      Swal.fire({
        title: 'Are you sure?',
        text: `This will change the student status to ${newState}.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, change it!',
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('/api/student/toggle-status', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'Authorization': token
            },
            body: JSON.stringify({ email: email })
          })
          .then(response => {
            if (response.status === 401 || response.status === 403) {
              window.location.href = '/Unauthorised';
              throw new Error('Unauthorized Access');
            }
            return response.json();
          })
          .then(data => {
            if (data.status === 'success') {
              Swal.fire('Success', data.message, 'success');
              if(currentTab === "agent"){
                fetchAgentStudents();
              } else {
                fetchNormalStudents();
              }
            } else {
              Swal.fire('Error', data.message || 'An error occurred.', 'error');
            }
          })
          .catch(error => {
            console.error('Error toggling student status:', error);
            Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
          });
        }
      });
    }

    // Print function (unchanged)
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
            <div class="col-6 text-13"><strong>Institute:</strong> ${instituteData ? instituteData.institution_name : 'N/A'}</div>
            <div class="col-6 text-13"><strong>Institute Type:</strong> ${instituteData ? instituteData.institution_type : 'N/A'}</div>
            <div class="col-6 text-13"><strong>Program:</strong> ${courseData ? courseData.program_name : 'N/A'}</div>
            <div class="col-6 text-13"><strong>Type:</strong> ${courseData ? courseData.program_type : 'N/A'}</div>
            <div class="col-6 text-13"><strong>Duration:</strong> ${courseData ? courseData.program_duration + ' Years' : 'N/A'}</div>
            <div class="col-12 bg-light p-2 mt-2">
              <h6 class="text-primary text-13"><i class="fa-solid fa-book"></i> Education</h6>
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
              <button class="btn btn-secondary btn-sm" onclick="window.close()">Close</button>
            </div>
          </div>
        </body>
        </html>
      `);
      printWindow.document.close();
    }

    // Modal for grouped items – show multiple agent referrals.
    function showGroupDetails(group) {
      window.currentGroup = group;
      let htmlContent = `<table class="table table-striped  text-13"><thead><tr>
        <th class="text-13 text-secondary">Agent Name</th>
        <th class="text-13 text-secondary">Agent Email</th>
        <th class="text-13 text-secondary">Date</th>
        <th class="text-13 text-secondary">Action</th>
      </tr></thead><tbody>`;
      group.referrals.forEach((referral, index) => {
        htmlContent += `<tr class="text-13">
          <td class="text-13">${referral.agent_name || 'N/A'}</td>
          <td class="text-13">${referral.agent_email}</td>
          <td class="text-13">${referral.updated_at || 'N/A'}</td>`;
        if(referral.status !== "Active") {
          htmlContent += `<td class="text-13"><button class="btn btn-sm btn-outline-success text-13" onclick="approveReferralFromGroup(${index})">Approve</button></td>`;
        } else {
          htmlContent += `<td class="text-13"></td>`;
        }
        htmlContent += `</tr>`;
      });
      htmlContent += `</tbody></table>`;
      Swal.fire({
        title: 'Agent Referrals',
        html: htmlContent,
        width: '600px'
      });
    }

    // Modal for single (non-group) referral
    function showSingleAgentDetails(student) {
      let htmlContent = `<table class="table table-striped  text-13"><thead><tr>
        <th class="text-13 text-secondary">Agent Name</th>
        <th class="text-13 text-secondary">Agent Email</th>
        <th class="text-13 text-secondary">Date</th>`;
        if(student.status !== "Active") {
         htmlContent += `<th class="text-13 text-secondary">Action</th>`;
      } else {
         htmlContent += `<td class="text-13"></td>`;
      }
        htmlContent += `</tr></thead><tbody>`;
      htmlContent += `<tr class="text-13">
          <td class="text-13">${student.agent_name || 'N/A'}</td>
          <td class="text-13">${student.agent_email}</td>
          <td class="text-13">${student.updated_at || 'N/A'}</td>`;
      if(student.status !== "Active") {
         htmlContent += `<td class="text-13"><button class="btn btn-sm btn-outline-success text-13" onclick="confirmApprove('${student.email}', '${student.agent_email}', '${student.agent_uid}')">Approve</button></td>`;
      } else {
         htmlContent += `<td class="text-13"></td>`;
      }
      htmlContent += `</tr></tbody></table>`;
      Swal.fire({
        title: 'Agent Referral',
        html: htmlContent,
        width: '600px'
      });
    }

    // Approve function for referral from group
    function approveReferralFromGroup(index) {
      let group = window.currentGroup;
      let referral = group.referrals[index];
      if(referral.status === "Active") {
        Swal.fire({
          icon: 'info',
          title: 'Already Approved',
          text: 'This student has already been approved based on identity details.'
        });
        return;
      }
      approveStudent(referral.email, referral.agent_email, referral.agent_uid);
    }

    // Approval functions (unchanged)
    function confirmApprove(studentEmail, agentEmail, agentUid) {
      Swal.fire({
        title: 'Confirm Approval',
        text: 'Are you sure you want to approve this student?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, approve',
        cancelButtonText: 'Cancel',
      }).then((result) => {
        if (result.isConfirmed) {
          approveStudent(studentEmail, agentEmail, agentUid);
        }
      });
    }
    function approveStudent(studentEmail, agentEmail, agentUid, newEmail = null) {
      Swal.fire({
        title: 'Approving student...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      const payload = { 
        email: studentEmail, 
        agent_email: agentEmail, 
        agent_uid: agentUid 
      };
      if(newEmail) {
        payload.new_email = newEmail;
      }
      fetch('/api/agent/approve-student', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': token
        },
        body: JSON.stringify(payload)
      })
      .then(response => {
        if (response.status === 401 || response.status === 403) {
          window.location.href = '/Unauthorised';
          throw new Error('Unauthorized Access');
        }
        return response.json();
      })
      .then(data => {
        Swal.close();
        if (data.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: 'Approved',
            text: data.message,
            timer: 2000,
            showConfirmButton: false
          }).then(() => {
            fetchAgentStudents();
          });
        } else if (data.status === 'error' && data.message.includes('Email already exists')) {
          Swal.fire({
            title: 'Email already exists',
            text: 'Enter a new email to update the student record',
            input: 'email',
            inputPlaceholder: 'Enter new email',
            showCancelButton: true,
            confirmButtonText: 'Submit'
          }).then((result) => {
            if(result.value){
              approveStudent(studentEmail, agentEmail, agentUid, result.value);
            }
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message || 'Failed to approve student.'
          });
        }
      })
      .catch(error => {
        Swal.close();
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'An error occurred: ' + error.message
        });
      });
    }
    
    // Utility: Pagination (unchanged)
    function getPaginatedData(data) {
      const start = (currentPage - 1) * rowsPerPage;
      const end = start + rowsPerPage;
      return data.slice(start, end);
    }
    
    // Toggle password visibility (unchanged)
    function toggleStudentPassword(studentId) {
      const passwordInput = document.getElementById('password-' + studentId);
      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
      } else {
        passwordInput.type = 'password';
      }
    }
  </script>

  <!-- Include Bootstrap Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
