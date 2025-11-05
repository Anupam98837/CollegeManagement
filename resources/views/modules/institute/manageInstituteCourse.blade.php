<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Manage Institution Courses</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!-- SweetAlert2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Optional Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/admin/style.css') }}">
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
        

        <p class="my-4 text-secondary text-14">
          <i class="fa-solid fa-angle-right"></i>
          <span class="text-primary">Manage Institution Courses</span>
        </p>

        <!-- Institution Dropdown -->
        <div class="bg-white p-4 rounded mb-4" id="institutionDropdownContainer">
          <label for="institutionSelect" class="form-label text-13">Select Institution</label>
          <select id="institutionSelect" class="form-select text-13">
            <option value="" disabled selected>Loading institutions...</option>
          </select>
        </div>

        <!-- Prompt Message -->
        <div id="search_Data_div" class="p-4 bg-white rounded text-center d-flex flex-column justify-content-center align-items-center vh-50">
          <img src="{{ asset('assets/web_assets/search.png') }}" alt="" style="width: 300px;">
          <p class="fs-5">Select an Institute first</p>
        </div>

        <!-- Courses Container -->
        <div id="coursesContainer" class="bg-white p-4 rounded mb-4 d-none">
          <!-- View Mode: Display added courses in an accordion -->
          <div id="viewMode">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="text-13">Added Courses</h4>
              <button id="showAddCourseBtn" class="btn btn-outline-primary text-13">Add Course</button>
            </div>
            <div id="coursesViewAccordion" class="mb-4">
              <!-- Rendered via JavaScript -->
            </div>
          </div>
          <!-- Add Mode: Display courses not yet added in an accordion with checkboxes -->
          <div id="addMode" class="d-none">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="text-13">Add New Courses</h4>
              <button id="backToViewBtn" class="btn btn-outline-secondary text-13">Back</button>
            </div>
            <div id="coursesAddAccordion" class="mb-4">
              <!-- Rendered via JavaScript -->
            </div>
            <div class="d-flex justify-content-end">
              <button id="submitAddCoursesBtn" class="btn btn-outline-success text-13">Submit Selected Courses</button>
            </div>
          </div>
        </div>

        <!-- Intakes Accordion Container (for viewing intakes of a course) -->
        <div id="intakesAccordionContainer" class="bg-white p-4 rounded mb-4 d-none">
          <div class="d-flex justify-content-between align-items-center mb-3 position-relative">
            <h6 id="programNameHeader" class="text-primary text-center mb-3">Intakes for ''</h6>
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0" aria-label="Close" onclick="closeIntakesAccordion()"></button>
          </div>
          <!-- Search Input -->
          <div class="col-md-6 position-relative mx-auto mb-3">
            <input type="text" id="searchIntake" class="form-control placeholder-14 text-13 ps-5 text-center" placeholder="Search intakes by Year, Intake Type, or Intake ID">
            <i class="fa-solid fa-search position-absolute text-secondary" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
          </div>
          <div id="intakesAccordion">
            <!-- Rendered via JavaScript -->
          </div>
        </div>

        <!-- (Other forms for add/edit intake would remain here if needed) -->

      </div>

  <!-- Bootstrap Bundle JS (with Popper) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // On DOMContentLoaded, ensure token exists and load institution info.
    document.addEventListener("DOMContentLoaded", function() {
      if (!sessionStorage.getItem("token")) {
        window.location.href = "/";
      }
      const instName = sessionStorage.getItem("institution_name");
      const instType = sessionStorage.getItem("institution_type");
      const instLogoPath = sessionStorage.getItem("institution_logo");
      const institutionInfoDiv = document.getElementById("institutionInfoDiv");
      const logoImg = document.getElementById("instImg");

      if (instName && instType) {
        // Use session logo path if available, else default logo
        logoImg.src = instLogoPath || '/assets/web_assets/logo.png';

        document.getElementById("instituteName").innerHTML = `
          <span class="text-secondary">${instName}</span>
        `;
        document.getElementById("instituteType").innerHTML = `
          <i class="fa-solid fa-graduation-cap me-2"></i>
          <span>${instType}</span>
        `;

          institutionInfoDiv.classList.remove("d-none");
        }

      fetchInstitutions();
    });

    const token = sessionStorage.getItem('token') || '';
    let currentInstitutionId = null;
    let addedCourses = []; // Stores courses already added

    // Fetch institutions and populate dropdown.
    function fetchInstitutions() {
      const institutionSelect = document.getElementById('institutionSelect');
      const institutionId = sessionStorage.getItem("institution_id");
      if (institutionId) {
        // Single institution fetch.
        fetch(`/api/view-institution/${institutionId}`, {
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
          institutionSelect.innerHTML = '';
          if (data.status === 'success' && data.data) {
            const inst = data.data;
            const option = document.createElement('option');
            option.value = inst.id?.$oid || inst._id || inst.id;
            option.textContent = inst.institution_name;
            option.selected = true;
            institutionSelect.appendChild(option);
            institutionSelect.dispatchEvent(new Event('change'));
            document.getElementById("institutionDropdownContainer").style.display = 'none';
          } else {
            institutionSelect.innerHTML = '<option value="">No institutions available</option>';
          }
        })
        .catch(error => {
          console.error('Error fetching institution:', error);
          Swal.fire('Error', 'Failed to load institution.', 'error');
        });
      } else {
        // Fetch all institutions.
        fetch('/api/view-institutions', {
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
          institutionSelect.innerHTML = '<option value="" disabled selected>Select Institution</option>';
          if (data.status === 'success' && data.data.length > 0) {
            data.data.forEach(inst => {
              const option = document.createElement('option');
              option.value = inst.id?.$oid || inst._id || inst.id;
              option.textContent = inst.institution_name;
              institutionSelect.appendChild(option);
            });
          } else {
            institutionSelect.innerHTML = '<option value="">No institutions available</option>';
          }
        })
        .catch(error => {
          console.error('Error fetching institutions:', error);
          Swal.fire('Error', 'Failed to load institutions.', 'error');
        });
      }
    }

    // On institution change, update currentInstitutionId and load courses.
    document.getElementById('institutionSelect').addEventListener('change', function () {
      const institutionId = this.value;
      if (institutionId) {
        currentInstitutionId = institutionId;
        document.getElementById('search_Data_div').classList.add('d-none');
        document.getElementById('coursesContainer').classList.remove('d-none');
        fetchAddedCourses(institutionId);
      }
    });

    // Fetch courses already added for the institution.
    function fetchAddedCourses(instId) {
      fetch(`/api/view-institution-courses?institution_id=${instId}`, {
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
        addedCourses = [];
        if (data.status === 'success' && data.data.length > 0) {
          addedCourses = data.data;
        }
        renderViewAccordion();
      })
      .catch(error => {
        console.error('Error fetching added courses:', error);
        Swal.fire('Error', 'Failed to load added courses.', 'error');
      });
    }

    // Render added courses as an accordion grouped by course type.
    function renderViewAccordion() {
      const container = document.getElementById('coursesViewAccordion');
      if (addedCourses.length === 0) {
        container.innerHTML = `<p class="text-muted">No courses added. Click "Add Course" to add new courses.</p>`;
        return;
      }
      const grouped = {};
      addedCourses.forEach(course => {
        const type = course.program_type || 'Other';
        if (!grouped[type]) grouped[type] = [];
        grouped[type].push(course);
      });
      let index = 0;
      let accordionHTML = `<div class="accordion" id="viewAccordion">`;
      for (const type in grouped) {
        const courses = grouped[type];
        const headerId = `viewHeading-${index}`;
        const collapseId = `viewCollapse-${index}`;
        accordionHTML += `
          <div class="accordion-item">
            <h2 class="accordion-header" id="${headerId}">
              <button class="accordion-button text-13 ${index !== 0 ? 'collapsed' : ''}" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="${index === 0 ? 'true' : 'false'}" aria-controls="${collapseId}">
                ${type}
              </button>
            </h2>
            <div id="${collapseId}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" aria-labelledby="${headerId}" data-bs-parent="#viewAccordion">
              <div class="accordion-body text-13">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th class="text-13 text-secondary">Program Name</th>
                        <th class="text-13 text-secondary">Code</th>
                        <th class="text-13 text-secondary">Duration</th>
                        <th class="text-13 text-secondary">Board</th>
                        <th class="text-13 text-secondary text-end">Intake</th>
                      </tr>
                    </thead>
                    <tbody>
                      ${courses.map(course => `
                        <tr class="text-start">
                          <td class="text-13 align-middle">${course.program_name}</td>
                          <td class="text-13 align-middle">${course.program_code}</td>
                          <td class="text-13 align-middle">${course.program_duration} Year</td>
                          <td class="text-13 align-middle">${course.board}</td>
                          <td class="text-end">
                            <button class="btn btn-sm btn-success text-13 m-2" onclick="viewIntakes('${course.program_code}', '${course.program_name}')">
                              Intake
                            </button>
                          </td>
                        </tr>
                      `).join('')}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        `;
        index++;
      }
      accordionHTML += `</div>`;
      container.innerHTML = accordionHTML;
    }

    // Switch to Add Mode.
    document.getElementById('showAddCourseBtn').addEventListener('click', function () {
      document.getElementById('viewMode').classList.add('d-none');
      document.getElementById('addMode').classList.remove('d-none');
      fetchNotAddedCourses();
    });

    // Return to View Mode from Add Mode.
    document.getElementById('backToViewBtn').addEventListener('click', function () {
      document.getElementById('addMode').classList.add('d-none');
      document.getElementById('viewMode').classList.remove('d-none');
    });

    // Fetch courses not yet added.
    function fetchNotAddedCourses() {
      fetch('/api/view-courses', {
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
        if (data.status !== 'success' || !data.data.length) {
          document.getElementById('coursesAddAccordion').innerHTML = '<p class="text-danger">No courses available.</p>';
          return;
        }
        const allCourses = data.data;
        const addedCodes = new Set(addedCourses.map(course => course.course_id || course.program_code));
        const notAddedCourses = allCourses.filter(course => !addedCodes.has(course.program_code));
        if (notAddedCourses.length === 0) {
          document.getElementById('coursesAddAccordion').innerHTML = '<p class="text-muted">All available courses have been added.</p>';
          return;
        }
        // Group by program_type.
        const grouped = {};
        notAddedCourses.forEach(course => {
          const type = course.program_type || 'Other';
          if (!grouped[type]) grouped[type] = [];
          grouped[type].push(course);
        });
        renderAddAccordion(grouped);
      })
      .catch(error => {
        console.error('Error fetching courses:', error);
        Swal.fire('Error', 'Failed to load courses.', 'error');
      });
    }

    // Render Add Mode accordion.
    function renderAddAccordion(groupedCourses) {
      const container = document.getElementById('coursesAddAccordion');
      let index = 0;
      let accordionHTML = `<div class="accordion" id="addAccordion">`;
      for (const type in groupedCourses) {
        const courses = groupedCourses[type];
        const headerId = `addHeading-${index}`;
        const collapseId = `addCollapse-${index}`;
        accordionHTML += `
          <div class="accordion-item">
            <h2 class="accordion-header" id="${headerId}">
              <button class="accordion-button text-13 ${index !== 0 ? 'collapsed' : ''}" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="${index === 0 ? 'true' : 'false'}" aria-controls="${collapseId}">
                ${type}
              </button>
            </h2>
            <div id="${collapseId}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" aria-labelledby="${headerId}" data-bs-parent="#addAccordion">
              <div class="accordion-body text-13">
                <div class="col-md-6 position-relative mb-3">
                  <input type="text" class="form-control placeholder-14 text-13 accordion-search" style="padding-left: 40px;" placeholder="Search by Program Name, Code, or Board">
                  <i class="fa-solid fa-search position-absolute text-secondary" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
                </div>
                <div class="table-responsive">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th class="text-13 text-secondary">Program Name</th>
                        <th class="text-13 text-secondary">Code</th>
                        <th class="text-13 text-secondary">Duration</th>
                        <th class="text-13 text-secondary">Board</th>
                        <th class="text-13 text-secondary text-end">Select</th>
                      </tr>
                    </thead>
                    <tbody>
                      ${courses.map(course => `
                        <tr class="text-start">
                          <td>${course.program_name}</td>
                          <td>${course.program_code}</td>
                          <td>${course.program_duration} Year</td>
                          <td>${course.board}</td>
                          <td class="text-end">
                            <input type="checkbox" class="course-checkbox" value="${course.program_code}">
                          </td>
                        </tr>
                      `).join('')}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        `;
        index++;
      }
      accordionHTML += `</div>`;
      container.innerHTML = accordionHTML;
      
      // Search functionality.
      document.querySelectorAll('.accordion-search').forEach(searchInput => {
        searchInput.addEventListener('input', function () {
          const filter = searchInput.value.toLowerCase();
          const trItems = searchInput.closest('.accordion-body').querySelectorAll('tr');
          trItems.forEach(item => {
            item.style.display = item.textContent.toLowerCase().includes(filter) ? '' : 'none';
          });
        });
      });
    }

    // Submit selected courses using /api/add-institution-courses.
    document.getElementById('submitAddCoursesBtn').addEventListener('click', function () {
      const btn = this;
      const originalHTML = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
      
      const checkboxes = document.querySelectorAll('.course-checkbox');
      const selectedCourseCodes = Array.from(checkboxes)
                                    .filter(cb => cb.checked)
                                    .map(cb => cb.value);
      if (selectedCourseCodes.length === 0) {
        Swal.fire('Error', 'Please select at least one course.', 'error');
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        return;
      }
      
      fetch('/api/add-institution-courses', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': token,
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          institution_id: currentInstitutionId,
          course_ids: selectedCourseCodes
        })
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
          document.getElementById('addMode').classList.add('d-none');
          document.getElementById('viewMode').classList.remove('d-none');
          fetchAddedCourses(currentInstitutionId);
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      })
      .catch(error => {
        console.error('Error adding courses:', error);
        Swal.fire('Error', error.message || 'An error occurred while adding courses.', 'error');
      })
      .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
      });
    });

    // VIEW INTAKES: Fetch and render intakes as an accordion grouped by Year (read-only).
    function viewIntakes(programCode, programName) {
      if (!currentInstitutionId) {
        Swal.fire('Error', 'Please select an institution first.', 'error');
        return;
      }
      const apiUrl = `/api/view-intakes/${programCode}?institute_id=${currentInstitutionId}`;
      // Hide courses view and show intakes accordion.
      document.getElementById('coursesContainer').classList.add('d-none');
      document.getElementById('intakesAccordionContainer').classList.remove('d-none');
      localStorage.setItem('programCode', programCode);
      localStorage.setItem('programName', programName);
      const institutionSelect = document.getElementById('institutionSelect');
      const institutionName = institutionSelect.options[institutionSelect.selectedIndex].text.trim();
      document.getElementById('programNameHeader').textContent = `Intakes for ${programName} at (${institutionName})`;
  
      fetch(apiUrl, {
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
        const container = document.getElementById('intakesAccordion');
        if (data.status === 'success' && data.data.length > 0) {
          const grouped = {};
          data.data.forEach(intake => {
            const year = intake.year;
            if (!grouped[year]) grouped[year] = [];
            grouped[year].push(intake);
          });
          const years = Object.keys(grouped).sort((a, b) => b - a);
          let accordionHTML = `<div class="accordion" id="intakesAccordionInner">`;
          years.forEach((year, index) => {
            const intakesForYear = grouped[year];
            accordionHTML += `
              <div class="accordion-item">
                <h2 class="accordion-header" id="intakeHeading-${index}">
                  <button class=" text-13 accordion-button ${index !== 0 ? 'collapsed' : ''}" type="button" data-bs-toggle="collapse" data-bs-target="#intakeCollapse-${index}" aria-expanded="${index === 0 ? 'true' : 'false'}" aria-controls="intakeCollapse-${index}">
                    Year ${year}
                  </button>
                </h2>
                <div id="intakeCollapse-${index}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" aria-labelledby="intakeHeading-${index}" data-bs-parent="#intakesAccordionInner">
                  <div class="accordion-body">
                    <div class="table-responsive">
                      <table class="table table-striped">
                        <thead>
                          <tr>
                            <th class="text-13 text-secondary">Intake Type</th>
                            <th class="text-13 text-secondary">Duration</th>
                            <th class="text-13 text-secondary">Starting Sem</th>
                            <th class="text-13 text-secondary">Ending Sem</th>
                            <th class="text-13 text-secondary">Ending Year</th>
                            <th class="text-13 text-secondary">GEN Intake</th>
                            <th class="text-13 text-secondary">EWS Intake</th>
                            <th class="text-13 text-secondary">TFW Intake</th>
                            <th class="text-13 text-secondary">Total Intake</th>
                            <th class="text-13 text-secondary">Status</th>
                          </tr>
                        </thead>
                        <tbody>
                          ${intakesForYear.map(intake => `
                            <tr>
                              <td class="text-13  align-middle">${intake.intake_type}</td>
                              <td class="text-13  align-middle">${intake.year_duration}</td>
                              <td class="text-13  align-middle">${intake.starting_semester}</td>
                              <td class="text-13  align-middle">${intake.ending_semester || '-'}</td>
                              <td class="text-13  align-middle">${intake.ending_year || '-'}</td>
                              <td class="text-13  align-middle">${intake.gen_intake || '-'}</td>
                              <td class="text-13  align-middle">${intake.ews_intake || '-'}</td>
                              <td class="text-13  align-middle">${intake.tfw_intake || '-'}</td>
                              <td class="text-13  align-middle text-primary">${intake.total_intake || '-'}</td>
                              <td class="text-13  align-middle" style="color: ${intake.status === 'Active' ? 'green' : 'red'};">${intake.status}</td>
                            </tr>
                          `).join('')}
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            `;
          });
          accordionHTML += `</div>`;
          container.innerHTML = accordionHTML;
        } else {
          container.innerHTML = `<div class="text-center">
            No intakes found for this course.
            
            
            </div>`;
        }
      })
      .catch(error => console.error('Error fetching intakes:', error));
    }

    // Close the intakes accordion view and return to courses view.
    function closeIntakesAccordion() {
      document.getElementById('intakesAccordionContainer').classList.add('d-none');
      document.getElementById('coursesContainer').classList.remove('d-none');
      localStorage.removeItem('programCode');
      localStorage.removeItem('programName');
    }

    // On page load, fetch institutions.
    window.addEventListener('DOMContentLoaded', () => {
      fetchInstitutions();
    });
  </script>
</body>
</html>
