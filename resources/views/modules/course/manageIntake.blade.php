<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Intake</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <!-- SweetAlert2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <!-- Optional Custom CSS -->
  <link rel="stylesheet" href="{{ asset('css/admin/style.css') }}" />
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
          <span class="text-primary">Intake</span>
        </p>

        <!-- Institution Dropdown -->
        <div class="bg-white p-4 rounded mb-4" id="institutionDropdownContainer">
          <label for="institutionSelect" class="form-label text-13">Select Institution</label>
          <select id="institutionSelect" class="form-select text-13">
            <option value="" disabled selected>Loading institutions...</option>
          </select>
        </div>

        <!-- Prompt (shown when no institution is selected) -->
        <div id="search_Data_div" class="p-4 bg-white rounded text-center d-flex flex-column justify-content-center align-items-center vh-50">
          <img src="{{ asset('assets/web_assets/search.png') }}" alt="" style="width: 300px;">
          <p class="fs-5">Select an Institute first</p>
        </div>

        <!-- Courses Accordion Container (shown after institution selection) -->
        <div id="coursesAccordionContainer" class="bg-white p-4 rounded mb-4 d-none">
          <!-- Courses will be rendered here -->
        </div>

        <!-- Add Intake Form (hidden initially) -->
        <div id="addIntakeFormContainer" class="bg-white p-4 rounded mb-4 d-none position-relative">
          <!-- Cross Button -->
          <button type="button" class="btn-close position-absolute top-0 end-0 m-3" aria-label="Close" onclick="closeAddIntakeForm()"></button>
          <h6 id="formHeader" class="text-primary mb-3">Add Intake for ''</h6>
          <form id="addIntakeForm">
            @csrf
            <!-- Hidden Fields for Program Details -->
            <input type="hidden" id="programCode" name="program_code">
            <input type="hidden" id="programType" name="program_type">
            <input type="hidden" id="programName" name="program_name">
            <input type="hidden" id="programDuration" name="program_duration">
  
            <div class="row g-3 mt-2">
              <div class="col-md-6 col-12">
                <label for="selectIntakeType" class="form-label text-13">Intake Type <span class="text-danger">*</span></label>
                <select id="selectIntakeType" class="form-control text-13" onchange="handleIntakeType(this)">
                  <option value="" disabled selected>Select an option</option>
                </select>
              </div>
              <!-- Year Dropdown -->
              <div class="col-md-6 col-12">
                <label for="year" class="form-label text-13">Year <span class="text-danger">*</span></label>
                <select id="year" name="year" class="form-control placeholder-14 text-13" required>
                  <option value="" disabled selected>Select Year</option>
                  <script>
                    const currentYear = new Date().getFullYear();
                    for (let i = currentYear; i >= currentYear - 3; i--) {
                      document.write(`<option value="${i}">${i}</option>`);
                    }
                  </script>
                </select>
              </div>
            </div>
  
            <div class="row g-3 mt-2">
              <!-- Duration -->
              <div class="col-md-3 col-12">
                <label for="duration" class="form-label text-13">Year Duration <span class="text-danger">*</span></label>
                <input type="number" id="duration" name="year_duration" class="form-control placeholder-14 text-13" placeholder="Enter duration in years" required>
              </div>
              <!-- Starting Semester -->
              <div class="col-md-3 col-12">
                <label for="startingSemester" class="form-label text-13">Starting Semester <span class="text-danger">*</span></label>
                <input type="number" id="startingSemester" name="starting_semester" class="form-control placeholder-14 text-13" placeholder="e.g., 1" required>
              </div>
              <!-- Ending Semester -->
              <div class="col-md-3 col-12">
                <label for="endingSemester" class="form-label text-13">Ending Semester <span class="text-danger">*</span></label>
                <input type="number" id="endingSemester" name="ending_semester" class="form-control placeholder-14 text-13" placeholder="e.g., 8" required>
              </div>
              <!-- Ending Year -->
              <div class="col-md-3 col-12">
                <label for="endingYear" class="form-label text-13">Ending Year <span class="text-danger">*</span></label>
                <input type="number" id="endingYear" name="ending_year" class="form-control placeholder-14 text-13" placeholder="Calculated automatically" readonly>
              </div>
            </div>
  
            <div class="row g-3 mt-2">
              <!-- GEN Intake -->
              <div class="col-md-5 col-12">
                <label for="genIntake" class="form-label text-13">GEN Intake <span class="text-danger">*</span></label>
                <input type="number" id="genIntake" name="gen_intake" class="form-control placeholder-14 text-13" placeholder="Enter GEN Intake" required>
              </div>
              <!-- GEN Intake ID -->
              <div class="col-md-5 col-12">
                <label for="genIntakeId" class="form-label text-13">GEN Intake ID <span class="text-danger">*</span></label>
                <input type="text" id="genIntakeId" name="gen_intake_id" class="form-control placeholder-14 text-13" placeholder="Unique GEN Intake ID" readonly required>
              </div>
              <div class="col-md-2 col-12 d-flex align-items-end">
                <button type="button" class="btn btn-outline-primary w-100 text-13" onclick="generateIntakeId('GEN')">Generate ID</button>
              </div>
            </div>
  
            <div class="row g-3 mt-2">
              <!-- EWS Intake -->
              <div class="col-md-5 col-12">
                <label for="ewsIntake" class="form-label text-13">EWS Intake <span class="text-danger">*</span></label>
                <input type="number" id="ewsIntake" name="ews_intake" class="form-control placeholder-14 text-13" placeholder="Enter EWS Intake" required>
              </div>
              <!-- EWS Intake ID -->
              <div class="col-md-5 col-12">
                <label for="ewsIntakeId" class="form-label text-13">EWS Intake ID <span class="text-danger">*</span></label>
                <input type="text" id="ewsIntakeId" name="ews_intake_id" class="form-control placeholder-14 text-13" placeholder="Unique EWS Intake ID" readonly required>
              </div>
              <div class="col-md-2 col-12 d-flex align-items-end">
                <button type="button" class="btn btn-outline-primary w-100 text-13" onclick="generateIntakeId('EWS')">Generate ID</button>
              </div>
            </div>
  
            <div class="row g-3 mt-2">
              <!-- TFW Intake -->
              <div class="col-md-5 col-12">
                <label for="tfwIntake" class="form-label text-13">TFW Intake <span class="text-danger">*</span></label>
                <input type="number" id="tfwIntake" name="tfw_intake" class="form-control placeholder-14 text-13" placeholder="Enter TFW Intake" required>
              </div>
              <!-- TFW Intake ID -->
              <div class="col-md-5 col-12">
                <label for="tfwIntakeId" class="form-label text-13">TFW Intake ID <span class="text-danger">*</span></label>
                <input type="text" id="tfwIntakeId" name="tfw_intake_id" class="form-control placeholder-14 text-13" placeholder="Unique TFW Intake ID" readonly required>
              </div>
              <div class="col-md-2 col-12 d-flex align-items-end">
                <button type="button" class="btn btn-outline-primary w-100 text-13" onclick="generateIntakeId('TFW')">Generate ID</button>
              </div>
            </div>
  
            <!-- Submit Button -->
            <div class="row mt-3">
              <div class="col-md-6">
                <button type="submit" id="addSubmitBtn" class="btn btn-primary text-13">
                  <span id="addSubmitSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                  Add Intake
                </button>
                <button type="button" class="btn btn-outline-secondary text-13" onclick="closeAddIntakeForm()">Cancel</button>
              </div>
            </div>
          </form>
        </div>
  
        <!-- Edit Intake Form (hidden initially) -->
        <div id="editIntakeFormContainer" class="bg-white p-4 rounded mb-4 d-none position-relative">
          <!-- Cross Button -->
          <button type="button" class="btn-close position-absolute top-0 end-0 m-3" aria-label="Close" onclick="closeEditIntakeForm()"></button>
          <h6 id="editFormHeader" class="text-primary mb-3">Edit Intake</h6>
          <form id="editIntakeForm">
            @csrf
            <!-- Hidden Field for Intake ID -->
            <input type="hidden" id="editIntakeId" name="intake_id">
            <div class="row g-3">
              <!-- Year -->
              <div class="col-md-6 col-12">
                <label for="editYear" class="form-label text-13">Year <span class="text-danger">*</span></label>
                <input type="number" id="editYear" name="year" class="form-control placeholder-14 text-13" readonly required>
              </div>
              <!-- Year Duration -->
              <div class="col-md-6 col-12">
                <label for="editYearDuration" class="form-label text-13">Year Duration <span class="text-danger">*</span></label>
                <input type="number" id="editYearDuration" name="year_duration" class="form-control placeholder-14 text-13" readonly required>
              </div>
            </div>
            <div class="row g-3 mt-2">
              <!-- Starting Semester -->
              <div class="col-md-4 col-12">
                <label for="editStartingSemester" class="form-label text-13">Starting Semester <span class="text-danger">*</span></label>
                <input type="number" id="editStartingSemester" name="starting_semester" class="form-control placeholder-14 text-13" readonly required>
              </div>
              <!-- Ending Semester -->
              <div class="col-md-4 col-12">
                <label for="editEndingSemester" class="form-label text-13">Ending Semester <span class="text-danger">*</span></label>
                <input type="number" id="editEndingSemester" name="ending_semester" class="form-control placeholder-14 text-13" readonly required>
              </div>
              <!-- Ending Year -->
              <div class="col-md-4 col-12">
                <label for="editEndingYear" class="form-label text-13">Ending Year <span class="text-danger">*</span></label>
                <input type="number" id="editEndingYear" name="ending_year" class="form-control placeholder-14 text-13" readonly required>
              </div>
            </div>
            <div class="row g-3 mt-2">
              <!-- GEN Intake -->
              <div class="col-md-4 col-12">
                <label for="editGenIntake" class="form-label text-13">GEN Intake <span class="text-danger">*</span></label>
                <input type="number" id="editGenIntake" name="edit_gen_intake" class="form-control placeholder-14 text-13" placeholder="Enter GEN Intake" required>
              </div>
              <!-- EWS Intake -->
              <div class="col-md-4 col-12">
                <label for="editEwsIntake" class="form-label text-13">EWS Intake <span class="text-danger">*</span></label>
                <input type="number" id="editEwsIntake" name="edit_ews_intake" class="form-control placeholder-14 text-13" placeholder="Enter EWS Intake" required>
              </div>
              <!-- TFW Intake -->
              <div class="col-md-4 col-12">
                <label for="editTfwIntake" class="form-label text-13">TFW Intake <span class="text-danger">*</span></label>
                <input type="number" id="editTfwIntake" name="edit_tfw_intake" class="form-control placeholder-14 text-13" placeholder="Enter TFW Intake" required>
              </div>
            </div>
            <!-- Submit Button -->
            <div class="row mt-3">
              <div class="col-md-6">
                <button type="submit" id="editSubmitBtn" class="btn btn-primary text-13">
                  <span id="editSubmitSpinner" class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
                  Update Intake
                </button>
                <button type="button" class="btn btn-outline-secondary text-13" onclick="closeEditIntakeForm()">Cancel</button>
              </div>
            </div>
          </form>
        </div>
  
        <!-- Courses Table (for non-merged courses or overall view) -->
        <div class="bg-white p-4 rounded d-none" id="coursesTableContainer">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="text-secondary">Active Courses</h6>
          </div>
          <div class="row mb-3">
            <div class="col-md-6 position-relative">
              <input type="text" id="searchCourse" class="form-control placeholder-14 text-13 ps-5" placeholder="Search by Board, Program Name, or Program Code">
              <i class="fa-solid fa-search position-absolute text-secondary" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
            </div>
          </div>
          <div class="table-responsive">
            <table id="coursesTable" class="table table-striped text-center">
              <thead>
                <tr>
                  <th class="text-secondary text-14">Board</th>
                  <th class="text-secondary text-14">Program_Type</th>
                  <th class="text-secondary text-14">Program_Name</th>
                  <th class="text-secondary text-14">Program_Code</th>
                  <th class="text-secondary text-14">Intake</th>
                </tr>
              </thead>
              <tbody>
                <!-- Dynamically populated -->
              </tbody>
            </table>
          </div>
        </div>
  
        <!-- Intakes Table -->
        <div class="bg-white p-4 rounded d-none" id="intakesTableContainer">
          <div class="d-flex justify-content-between align-items-center mb-3 position-relative">
            <h6 id="programNameHeader" class="text-primary text-center mb-3">Intakes for ''</h6>
            <!-- Cross Button for Intakes Table -->
            <button type="button" class="btn-close position-absolute top-0 end-0" aria-label="Close" onclick="closeIntakesTable()"></button>
          </div>
          <!-- Search Input Centered -->
          <div class="col-md-6 position-relative mx-auto mb-3">
            <input type="text" id="searchIntake" class="form-control placeholder-14 text-13 ps-5 text-center" placeholder="Search intakes by Year, Intake Type, or Intake ID">
            <i class="fa-solid fa-search position-absolute text-secondary" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
          </div>
          <div class="table-responsive">
            <table id="intakesTable" class="table table-striped text-center">
              <thead>
                <tr>
                  <th class="text-secondary text-13">Year</th>
                  <th class="text-secondary text-13">Duration(yr)</th>
                  <th class="text-secondary text-13">Intake_Type</th>
                  <th class="text-secondary text-13">Starting_Sem</th>
                  <th class="text-secondary text-13">Ending_Sem</th>
                  <th class="text-secondary text-13">Ending_Year</th>
                  <th class="text-secondary text-13">Course_Id</th>
                  <th class="text-secondary text-13">GEN_Intake</th>
                  <th class="text-secondary text-13">EWS_Intake</th>
                  <th class="text-secondary text-13">TFW_Intake</th>
                  <th class="text-secondary text-13">Total_Intake</th>
                  <th class="text-13 text-secondary">Status</th>
                  <th class="text-secondary text-13">Actions</th>
                </tr>
              </thead>
              <tbody>
                <!-- Dynamically populated -->
              </tbody>
            </table>
          </div>
        </div>
  
      </div>
  
  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    
    document.addEventListener("DOMContentLoaded", function() {
  if (!sessionStorage.getItem("token")) {
    // Redirect to blank path or your preferred path if token is missing.
    window.location.href = "/";
  }
});
document.addEventListener("DOMContentLoaded", function() {
      if (!sessionStorage.getItem("token")) {
        window.location.href = "/";
      }

      // Check if institution details exist in sessionStorage; if so, display the institution info card.
      const instName = sessionStorage.getItem("institution_name");
      const instType = sessionStorage.getItem("institution_type");
      const institutionInfoDiv = document.getElementById("institutionInfoDiv");
      if (instName && instType) {
        const instLogoPath = sessionStorage.getItem("institution_logo");
      const logoImg = document.getElementById("instImg");
      logoImg.src = instLogoPath || '/assets/web_assets/logo.png';

        document.getElementById("instituteName").innerHTML = `
          <span class="text-secondary">${instName}</span>
        `;
        document.getElementById("instituteType").innerHTML = `
          <i class="fa-solid fa-graduation-cap me-2"></i>
          ${instType}
        `;
        institutionInfoDiv.classList.remove("d-none");
      }
    });
    const token = sessionStorage.getItem('token');
    let currentInstitutionId = "";
    let existingMergedRecords = {};
    let updateMode = false;
    let institutionShortCode = {}; 
    // Fetch Institutions
    function fetchInstitutions() {
  const institutionId = sessionStorage.getItem("institution_id");
  const institutionSelect = document.getElementById('institutionSelect');
  
  if (institutionId) {
    // Fetch single institution by ID if it exists in sessionStorage
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
        institutionShortCode[inst.institution_name] = inst.institution_short_code;
        // Automatically trigger the change event so that merged courses load 
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
    // Otherwise, fetch all institutions
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

          institutionShortCode[inst.institution_name] = inst.institution_short_code;

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
  
// On Institution change, set currentInstitutionId and load merged courses.
document.getElementById('institutionSelect').addEventListener('change', function () {
  currentInstitutionId = this.value;
  if (currentInstitutionId) {
    // Hide the prompt and show the courses accordion container.
    document.getElementById('search_Data_div').classList.add('d-none');
    document.getElementById('coursesAccordionContainer').classList.remove('d-none');
    // Also hide any forms or tables that might be visible.
    document.getElementById('addIntakeFormContainer').classList.add('d-none');
    document.getElementById('editIntakeFormContainer').classList.add('d-none');
    document.getElementById('coursesTableContainer')?.classList.add('d-none');
    document.getElementById('intakesTableContainer').classList.add('d-none');
    fetchMergedCourses(currentInstitutionId);
  } else {
    document.getElementById('search_Data_div').classList.remove('d-none');
    document.getElementById('coursesAccordionContainer').classList.add('d-none');
  }
});

  
    // Fetch merged courses for the institution.
    function fetchMergedCourses(institutionId) {
      fetch(`/api/view-institution-courses?institution_id=${institutionId}`, {
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
        existingMergedRecords = {};
        if (data.status === 'success' && data.data.length > 0) {
          updateMode = true;
          data.data.forEach(record => {
            existingMergedRecords[record.course_id] = record.id;
          });
        } else {
          updateMode = false;
        }
        fetchAndDisplayCourses();
      })
      .catch(error => {
        console.error('Error fetching merged courses:', error);
        Swal.fire('Error', 'Failed to load institution courses.', 'error');
      });
    }
  
    // Fetch all courses, group by program_type, and filter by merged records.
    function fetchAndDisplayCourses() {
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
          document.getElementById('coursesAccordionContainer').innerHTML = '<p class="text-danger text-13">No courses available.</p>';
          return;
        }
  
        const grouped = {};
        data.data.forEach(course => {
          if (existingMergedRecords.hasOwnProperty(course.program_code)) {
            const type = course.program_type || 'Other';
            if (!grouped[type]) grouped[type] = [];
            grouped[type].push(course);
          }
        });
        renderCoursesAccordion(grouped);
      })
      .catch(error => {
        console.error('Error fetching courses:', error);
        Swal.fire('Error', 'Failed to load courses.', 'error');
      });
    }
  
    // Render courses in an accordion with a search bar in each accordion-body.
    function renderCoursesAccordion(groupedCourses) {
  const container = document.getElementById('coursesAccordionContainer');
  container.innerHTML = '<div class="accordion" id="accordionExample"></div>';
  const accordion = container.querySelector('#accordionExample');

  // Get all group keys and sort them so that "pg" comes first, then "ug", then the rest alphabetically.
  const keys = Object.keys(groupedCourses);
  keys.sort((a, b) => {
    const order = { 'pg': 0, 'ug': 1 };
    const aLower = a.toLowerCase();
    const bLower = b.toLowerCase();
    const aOrder = order.hasOwnProperty(aLower) ? order[aLower] : 2;
    const bOrder = order.hasOwnProperty(bLower) ? order[bLower] : 2;
    if (aOrder !== bOrder) return aOrder - bOrder;
    return a.localeCompare(b);
  });

  let accordionIndex = 0;
  
  // Create an accordion item for each group that has courses
  keys.forEach(type => {
    if (groupedCourses[type].length === 0) return;
    const courses = groupedCourses[type];
    const item = document.createElement('div');
    item.className = 'accordion-item';
    const headerId = `heading-${accordionIndex}`;
    const collapseId = `collapse-${accordionIndex}`;
    item.innerHTML = `
      <h2 class="accordion-header" id="${headerId}">
        <button class="accordion-button text-13 ${accordionIndex === 0 ? '' : 'collapsed'}" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="${accordionIndex === 0 ? 'true' : 'false'}" aria-controls="${collapseId}">
          ${type}
        </button>
      </h2>
      <div id="${collapseId}" class="accordion-collapse collapse ${accordionIndex === 0 ? 'show' : ''}" aria-labelledby="${headerId}" data-bs-parent="#accordionExample">
        <div class="accordion-body text-13">
          <div class="col-md-6 position-relative mb-3">
            <input type="text" class="form-control placeholder-14 text-13 ps-5 accordion-search" placeholder="Search by Program Name, Code, or Board">
            <i class="fa-solid fa-search position-absolute text-secondary" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
          </div>
          <div class="table-responsive">
            <table class="table table-borderless">
              <tbody>
                ${courses.map(course => `
                  <tr class="align-middle">
                    <td>
                      <strong>${course.program_name}</strong><br>
                      <small>Code: ${course.program_code} | Duration: ${course.program_duration} | Board: ${course.board}</small>
                    </td>
                    <td class="text-end">
                      <button class="btn btn-sm btn-outline-primary text-13 m-2" onclick="showAddIntakeForm('${course.program_code}', '${course.program_name}', '${course.program_type}', '${course.program_duration}')">
                        <i class="fa-regular fa-pen-to-square"></i>
                      </button>
                      <button class="btn btn-sm btn-outline-secondary text-13 m-2" onclick="viewIntakes('${course.program_code}', '${course.program_name}')">
                        <i class="fa-solid fa-list"></i>
                      </button>
                    </td>
                  </tr>
                `).join('')}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    `;
    accordion.appendChild(item);
    // Store the original table body HTML in a data attribute
    const tbody = item.querySelector('tbody');
    tbody.setAttribute('data-original', tbody.innerHTML);
    accordionIndex++;
  });

  // If no accordion items were added, display the no-data message
  if (accordion.children.length === 0) {
    container.innerHTML = `
      <div class="table-responsive">
        <table class="table table-borderless">
          <tbody>
            <tr>
              <td class="bg-white text-center" colspan="100%">
                <img src="{{ asset('assets/web_assets/noData.png') }}" alt="">
                <br>
                No course found
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    `;
  }

  // Attach search event listeners for each search input within the accordion
  document.querySelectorAll('.accordion-search').forEach(searchInput => {
    searchInput.addEventListener('input', function () {
      const filter = searchInput.value.toLowerCase().trim();
      const tbody = searchInput.closest('.accordion-body').querySelector('tbody');
      
      // If the search input is empty, restore the original table content
      if (filter === "") {
        tbody.innerHTML = tbody.getAttribute('data-original');
        return;
      }
      
      // Create a temporary container to work with the original rows
      const originalHTML = tbody.getAttribute('data-original');
      const tempContainer = document.createElement('tbody');
      tempContainer.innerHTML = originalHTML;
      const rows = tempContainer.querySelectorAll('tr');
      let matchedHTML = "";
      let count = 0;
      
      // Iterate through rows and add those that match the filter
      rows.forEach(row => {
        if (row.textContent.toLowerCase().includes(filter)) {
          matchedHTML += row.outerHTML;
          count++;
        }
      });
      
      // If no rows match, show the no-data row; otherwise, display the matched rows.
      if (count === 0) {
        tbody.innerHTML = `
          <tr>
            <td class="bg-white text-center" colspan="100%">
              <img src="{{ asset('assets/web_assets/noData.png') }}" alt="">
              <br>
              No course found
            </td>
          </tr>
        `;
      } else {
        tbody.innerHTML = matchedHTML;
      }
    });
  });
}


  
    // Show Add Intake Form and prefill hidden fields.
    function showAddIntakeForm(programCode, programName, programType, programDuration) {
      if (!currentInstitutionId) {
        Swal.fire('Error', 'Please select an institution first.', 'error');
        return;
      }
      // Hide other views
      document.getElementById('coursesAccordionContainer').classList.add('d-none');
      document.getElementById('coursesTableContainer')?.classList.add('d-none');
      document.getElementById('intakesTableContainer').classList.add('d-none');
  
      const shortFormMatch = programName.match(/\(([^)]+)\)/);
      const shortForm = shortFormMatch ? shortFormMatch[1] : programName;
      const selectIntakeType = document.getElementById('selectIntakeType');
      selectIntakeType.innerHTML = '';
      const opt1 = document.createElement('option');
      opt1.value = 'General';
      opt1.textContent = `${shortForm} (${programDuration} Year) GEN`;
      selectIntakeType.appendChild(opt1);
      if (programType !== 'PG' && programType !== 'ITI') {
        const opt2 = document.createElement('option');
        opt2.value = 'Lateral';
        opt2.textContent = `${shortForm} (${programDuration - 1} Year) LAT`;
        selectIntakeType.appendChild(opt2);
      }
      selectIntakeType.value = 'General';
      document.getElementById('programCode').value = programCode;
      document.getElementById('programType').value = programType;
      document.getElementById('programDuration').value = programDuration;
      document.getElementById('duration').value = programDuration;
      document.getElementById('startingSemester').value = 1;
      if (programDuration >= 1 && programDuration <= 5) {
        const startingSemester = parseInt(document.getElementById('startingSemester').value, 10) || 1;
        const endingSemester = startingSemester + (programDuration * 2) - 1;
        document.getElementById('endingSemester').value = endingSemester;
      }
      // Retrieve the institution name from the dropdown
        const institutionSelect = document.getElementById('institutionSelect');
        const institutionName = institutionSelect.options[institutionSelect.selectedIndex].text.trim();
      document.getElementById('programName').value = shortForm;
      document.getElementById('formHeader').textContent = `Add Intake for ${programName} at (${institutionName})`;
      document.getElementById('addIntakeFormContainer').classList.remove('d-none');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  
    function closeAddIntakeForm() {
      document.getElementById('addIntakeForm').reset();
      document.getElementById('addIntakeFormContainer').classList.add('d-none');
      // Show courses accordion back
      document.getElementById('coursesAccordionContainer').classList.remove('d-none');
    }
  
    // Handle Intake Type Change for Add Intake form.
    function handleIntakeType(selectElement) {
      const selectedType = selectElement.value;
      const programDuration = parseInt(document.getElementById('programDuration').value, 10);
      const year = parseInt(document.getElementById('year').value, 10);
      if (selectedType === 'General') {
        document.getElementById('duration').value = programDuration;
        document.getElementById('startingSemester').value = 1;
        if (year) {
          document.getElementById('genIntakeId').value = "";
          document.getElementById('ewsIntakeId').value = "";
          document.getElementById('tfwIntakeId').value = "";
          document.getElementById('endingYear').value = year + programDuration;
          document.getElementById('endingSemester').value = programDuration * 2;
        }
      } else if (selectedType === 'Lateral') {
        document.getElementById('duration').value = programDuration - 1;
        document.getElementById('startingSemester').value = 3;
        if (year) {
          document.getElementById('genIntakeId').value = "";
          document.getElementById('ewsIntakeId').value = "";
          document.getElementById('tfwIntakeId').value = "";
          document.getElementById('endingYear').value = year + (programDuration - 1);
          document.getElementById('endingSemester').value = 3 + (programDuration - 1) * 2 - 1;
        }
      }
    }
  
    // Update Ending Year (for Add Intake form)
    function updateEndingYear(formType) {
      const yearField = formType === 'edit' ? 'editYear' : 'year';
      const durationField = formType === 'edit' ? 'editYearDuration' : 'duration';
      const endingYearField = formType === 'edit' ? 'editEndingYear' : 'endingYear';
      const year = parseInt(document.getElementById(yearField).value, 10);
      const duration = parseInt(document.getElementById(durationField).value, 10);
      if (!isNaN(year) && !isNaN(duration)) {
        document.getElementById(endingYearField).value = year + duration;
      } else {
        document.getElementById(endingYearField).value = '';
      }
    }
    document.getElementById('year').addEventListener('change', () => updateEndingYear('add'));
    document.getElementById('duration').addEventListener('input', () => updateEndingYear('add'));
  
    // Add Intake Form Submission
    document.getElementById('addIntakeForm').addEventListener('submit', function (e) {
      e.preventDefault();
      toggleButtonState('addSubmitBtn', 'addSubmitSpinner', true);
      if (!currentInstitutionId) {
        Swal.fire('Error', 'Please select an institution first.', 'error');
        toggleButtonState('addSubmitBtn', 'addSubmitSpinner', false);
        return;
      }
      const formData = {
        institute_id: currentInstitutionId,
        program_code: document.getElementById('programCode').value,
        year: parseInt(document.getElementById('year').value, 10),
        year_duration: parseInt(document.getElementById('duration').value, 10),
        starting_semester: parseInt(document.getElementById('startingSemester').value, 10),
        ending_semester: parseInt(document.getElementById('endingSemester').value, 10),
        ending_year: parseInt(document.getElementById('endingYear').value, 10),
        intake_type: document.getElementById('selectIntakeType').value,
        gen_intake: parseInt(document.getElementById('genIntake').value, 10),
        gen_intake_id: document.getElementById('genIntakeId').value,
        ews_intake: parseInt(document.getElementById('ewsIntake').value, 10),
        ews_intake_id: document.getElementById('ewsIntakeId').value,
        tfw_intake: parseInt(document.getElementById('tfwIntake').value, 10),
        tfw_intake_id: document.getElementById('tfwIntakeId').value,
      };
  
      fetch('/api/add-intake', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Authorization': token
        },
        body: JSON.stringify(formData),
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
          document.getElementById('addIntakeForm').reset();
          document.getElementById('addIntakeFormContainer').classList.add('d-none');
          // Optionally refresh intakes or courses
          document.getElementById('coursesAccordionContainer').classList.remove('d-none');
        } else {
          Swal.fire('Error', data.message || 'An error occurred.', 'error');
        }
      })
      .catch(error => {
        Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
        console.error('Error:', error);
      })
      .finally(() => {
        toggleButtonState('addSubmitBtn', 'addSubmitSpinner', false);
      });
    });
  
    // Show Intakes for a specific course
    function viewIntakes(programCode, programName) {
      if (!currentInstitutionId) {
        Swal.fire('Error', 'Please select an institution first.', 'error');
        return;
      }
      const apiUrl = `/api/view-intakes/${programCode}?institute_id=${currentInstitutionId}`;
      // Hide other views
      document.getElementById('coursesAccordionContainer').classList.add('d-none');
      document.getElementById('addIntakeFormContainer').classList.add('d-none');
      document.getElementById('editIntakeFormContainer').classList.add('d-none');
      document.getElementById('coursesTableContainer')?.classList.add('d-none');
      document.getElementById('intakesTableContainer').classList.remove('d-none');
      localStorage.setItem('programCode', programCode);
      localStorage.setItem('programName', programName);
      // Retrieve the institution name from the dropdown
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
        const tbody = document.querySelector('#intakesTable tbody');
        tbody.innerHTML = '';
        if (data.status === 'success' && data.data.length > 0) {
          data.data.forEach(intake => {
            const row = `
              <tr>
                <td class="text-13 text-center align-middle">${intake.year}</td>
                <td class="text-13 text-center align-middle">${intake.year_duration}</td>
                <td class="text-13 text-center align-middle">${intake.intake_type}</td>
                <td class="text-13 text-center align-middle">${intake.starting_semester}</td>
                <td class="text-13 text-center align-middle">${intake.ending_semester || '-'}</td>
                <td class="text-13 text-center align-middle">${intake.ending_year || '-'}</td>
                <td class="text-13 text-center align-middle">${intake.program_code || '-'}</td>
                <td class="text-13 text-center align-middle">${intake.gen_intake || '-'}</td>
                <td class="text-13 text-center align-middle">${intake.ews_intake || '-'}</td>
                <td class="text-13 text-center align-middle">${intake.tfw_intake || '-'}</td>
                <td class="text-13 text-center align-middle text-primary">${intake.total_intake || '-'}</td>
                <td class="text-13 text-center align-middle" style="color: ${intake.status === 'Active' ? 'green' : 'red'};">
                  ${intake.status}
                </td>
                <td class="text-center align-middle">
                  <button class="btn btn-sm btn-outline-secondary text-13 m-2" onclick="editIntake('${intake.id.$oid}', '${intake.year}', '${intake.year_duration}', '${intake.starting_semester}', '${intake.ending_semester}', '${intake.ending_year}', '${intake.gen_intake}', '${intake.ews_intake}', '${intake.tfw_intake}')">
                    <i class="fa-regular fa-pen-to-square"></i>
                  </button>
                  <button class="btn btn-sm ${intake.status === 'Active' ? 'btn-outline-danger' : 'btn-outline-success'} text-13 m-2" onclick="toggleIntakeStatus('${intake.id.$oid}', '${intake.status}')">
                    <i class="fa-solid ${intake.status === 'Active' ? 'fa-ban' : 'fa-power-off'}"></i>
                  </button>
                </td>
              </tr>
            `;

            tbody.innerHTML += row;
          });
        } else {
          tbody.innerHTML = `<tr><td class="bg-white" colspan="100%"><img src="{{ asset('assets/web_assets/noData.png') }}" alt=""></td></tr>`;
        }
      })
      .catch(error => console.error('Error fetching intakes:', error));
    }
  
    function closeIntakesTable() {
      document.getElementById('intakesTableContainer').classList.add('d-none');
      document.getElementById('coursesAccordionContainer').classList.remove('d-none');
      localStorage.removeItem('programCode');
      localStorage.removeItem('programName');
    }
  
    // Edit Intake: populate edit form and show it.
    function editIntake(id, year, yearDuration, startingSemester, endingSemester, endingYear, genIntake, ewsIntake, tfwIntake) {
      // Hide other views
      document.getElementById('intakesTableContainer').classList.add('d-none');
      document.getElementById('addIntakeFormContainer').classList.add('d-none');
      document.getElementById('editIntakeFormContainer').classList.remove('d-none');
      window.scrollTo({ top: 0, behavior: 'smooth' });
      // Update heading using course name and institute name
        const programName = localStorage.getItem('programName') || '';
        const institutionSelect = document.getElementById('institutionSelect');
        const institutionName = institutionSelect.options[institutionSelect.selectedIndex].text.trim();
        document.getElementById('editFormHeader').textContent = `Edit Intake for ${programName} at (${institutionName})`;

      document.getElementById('editIntakeId').value = id;
      document.getElementById('editYear').value = year;
      document.getElementById('editYearDuration').value = yearDuration;
      document.getElementById('editStartingSemester').value = startingSemester;
      document.getElementById('editEndingSemester').value = endingSemester;
      document.getElementById('editEndingYear').value = endingYear;
      document.getElementById('editGenIntake').value = genIntake;
      document.getElementById('editEwsIntake').value = ewsIntake;
      document.getElementById('editTfwIntake').value = tfwIntake;
    }
  
    function closeEditIntakeForm() {
      document.getElementById('editIntakeFormContainer').classList.add('d-none');
      // Show intakes table if it was visible, otherwise return to the courses view
      if(document.getElementById('intakesTableContainer').classList.contains('d-none')) {
        document.getElementById('coursesAccordionContainer').classList.remove('d-none');
      } else {
        document.getElementById('intakesTableContainer').classList.remove('d-none');
      }
    }
  
    // Edit Intake Form Submission
    document.getElementById('editIntakeForm').addEventListener('submit', function (e) {
      e.preventDefault();
      toggleButtonState('editSubmitBtn', 'editSubmitSpinner', true);
      if (!currentInstitutionId) {
        Swal.fire('Error', 'Institution not selected.', 'error');
        toggleButtonState('editSubmitBtn', 'editSubmitSpinner', false);
        return;
      }
      const editFormData = {
        institute_id: currentInstitutionId,
        id: document.getElementById('editIntakeId').value.trim(),
        year: parseInt(document.getElementById('editYear').value.trim(), 10),
        year_duration: parseInt(document.getElementById('editYearDuration').value.trim(), 10),
        starting_semester: parseInt(document.getElementById('editStartingSemester').value.trim(), 10),
        ending_semester: parseInt(document.getElementById('editEndingSemester').value.trim(), 10),
        ending_year: parseInt(document.getElementById('editEndingYear').value.trim(), 10),
        gen_intake: parseInt(document.getElementById('editGenIntake').value.trim(), 10),
        ews_intake: parseInt(document.getElementById('editEwsIntake').value.trim(), 10),
        tfw_intake: parseInt(document.getElementById('editTfwIntake').value.trim(), 10),
      };
  
      fetch(`/api/edit-intake/${editFormData.id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Authorization': token
        },
        body: JSON.stringify(editFormData),
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
          document.getElementById('editIntakeFormContainer').classList.add('d-none');
          // Optionally, refresh the intakes list.
          document.getElementById('intakesTableContainer').classList.remove('d-none');
        } else {
          Swal.fire('Error', data.message || 'An error occurred.', 'error');
        }
      })
      .catch(error => {
        Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
        console.error('Error:', error);
      })
      .finally(() => {
        toggleButtonState('editSubmitBtn', 'editSubmitSpinner', false);
      });
    });
  
    // Toggle Intake Status with confirmation.
    function toggleIntakeStatus(intakeId, state) {
      const newState = state === 'Active' ? 'Inactive' : 'Active';
      Swal.fire({
        title: 'Are you sure?',
        text: `This will change the intake status to ${newState}.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, change it!',
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/api/toggle-intake-status/${intakeId}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Authorization': token
            },
            body: JSON.stringify({
              status: newState,
              institute_id: currentInstitutionId
            }),
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
              // Optionally, refresh intakes.
            } else {
              Swal.fire('Error', data.message || 'An error occurred.', 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
          });
        }
      });
    }
  
    // Generate Intake ID based on form data.
    function generateIntakeId(intakeType) {
      let programType = document.getElementById('programType').value.trim().toUpperCase();
      let programName = document.getElementById('programName').value.trim().toUpperCase();
      const year = document.getElementById('year').value.trim();
      const duration = document.getElementById('duration').value.trim();
      
  
      // Get the institution name from the selected option
      const institutionSelect = document.getElementById('institutionSelect');
      const institutionNameRaw = institutionSelect.options[institutionSelect.selectedIndex].text;
      const institutionName = (institutionShortCode[institutionNameRaw] !== undefined)
      ? institutionShortCode[institutionNameRaw]
      : institutionSelect.selectedIndex;

      if (!programType || !programName || !year || !intakeType || !duration || !institutionName) {
        Swal.fire('Error', 'Make sure all fields are filled before generating the ID.', 'error');
        return;
      }
  
      if (programType === 'DIPLOMA') {
        programType = 'DIP';
      }
  
      // Generate the intake ID by combining program type, program name, institution name, year, intake type, and duration
      let intakeId = `${programType}-${programName}-${institutionName}${year}${intakeType}${duration}`;
      // Remove all spaces from the generated ID
      intakeId = intakeId.replace(/\s+/g, '');
  
      if (intakeType === 'GEN') {
        document.getElementById('genIntakeId').value = intakeId;
        Swal.fire('Generated Intake ID Of GEN', intakeId, 'info');
      } else if (intakeType === 'EWS') {
        document.getElementById('ewsIntakeId').value = intakeId;
        Swal.fire('Generated Intake ID Of EWS', intakeId, 'info');
      } else if (intakeType === 'TFW') {
        document.getElementById('tfwIntakeId').value = intakeId;
        Swal.fire('Generated Intake ID Of TFW', intakeId, 'info');
      }
    }
  
    // Search functionality for courses table.
    document.getElementById('searchCourse').addEventListener('input', function () {
      const searchTerm = this.value.toLowerCase();
      const rows = document.querySelectorAll('#coursesTable tbody tr');
      rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(searchTerm) ? '' : 'none';
      });
    });
  
    // Search functionality for intakes table.
    document.getElementById('searchIntake').addEventListener('input', function () {
      const searchTerm = this.value.toLowerCase();
      const rows = document.querySelectorAll('#intakesTable tbody tr');
      rows.forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(searchTerm) ? '' : 'none';
      });
    });
  
    // Helper: Toggle button state (disable/enable)
    function toggleButtonState(buttonId, spinnerId, disable) {
      const button = document.getElementById(buttonId);
      const spinner = document.getElementById(spinnerId);
      if (disable) {
        button.setAttribute('disabled', 'disabled');
        spinner.classList.remove('d-none');
      } else {
        button.removeAttribute('disabled');
        spinner.classList.add('d-none');
      }
    }
  
    // On page load, fetch institutions.
    window.addEventListener('DOMContentLoaded', () => {
      fetchInstitutions();
    });
  </script>
</body>
</html>
