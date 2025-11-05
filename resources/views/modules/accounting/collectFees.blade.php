<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pay Fees</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Custom Admin CSS -->
  <link rel="stylesheet" href="{{ asset('css/admin/addfees.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <style>
    .rotate {
  animation: rotation 1s linear;
}

@keyframes rotation {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
  </style>
</head>
<body>
      <div class="container my-4">
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
        <!-- Institution Info Card (only shown if institution data is in sessionStorage) -->
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
        <p class="my-4 text-secondary text-14">
          <i class="fa-solid fa-angle-right"></i>
          <span class="text-primary">Collect Fees</span>
        </p>

        <!-- Institution Dropdown -->
        <div class="bg-white p-4 rounded mb-4" id="institutionDropdownContainer">
          <label for="institutionSelect" class="form-label text-13">Select Institution</label>
          <select id="institutionSelect" class="form-select text-13">
            <option value="">Loading institutions...</option>
          </select>
        </div>

        <!-- Accordion Container (Student List) -->
        <div id="accordionContainer">
          <div class="text-center p-4 bg-white rounded">
            <img src="{{ asset('assets/web_assets/search.png') }}" alt="No Data" class="img-fluid" style="max-width:300px;">
            <p class="mt-3 text-14 text-secondary">Select an Institute first</p>
          </div>
        </div>

        <!-- Payment Interface Container (initially hidden) -->
        <div id="paymentInterface" class="container d-none mt-4">
          <div class="card shadow-sm">
            <div class="card-header bg-light text-primary text-14 d-flex justify-content-between">
              <span id="payment_details_head">Payment Details</span>
              <div class="d-flex">
                <span type="button" class="text-secondary text-14 me-2 mb-3" onclick="refreshPaymentDetails()">
                  <i class="fa-solid fa-arrows-rotate"></i>
                </span>
                <button type="button" class="btn-close text-13 btn-outline-danger" onclick="closePaymentInterface()"></button>
              </div>
              
            </div>
            <div class="card-body position-relative">
              <!-- Spinner while loading fee details -->
              <div id="paymentLoadingSpinner" class="d-flex justify-content-center align-items-center">
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </div>
              <!-- Payment details will be populated here -->
              <div id="paymentDetails" class="d-none text-13"></div>
            </div>
          </div>
        </div>

      </div><!-- end container -->

  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    const token = sessionStorage.getItem('token') || '';
    // Added global variable to store payment parameters for refresh functionality
    var currentPaymentParams = null;

    // -------------------- Institutions & Students --------------------
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
    document.addEventListener('DOMContentLoaded', fetchInstitutions);

    document.getElementById('institutionSelect').addEventListener('change', function() {
      const instituteId = this.value;
      if (!instituteId) {
        document.getElementById('accordionContainer').innerHTML = `
          <div class="text-center p-4">
            <img src="{{ asset('assets/web_assets/search.png') }}" alt="No Data" class="img-fluid" style="max-width:300px;">
            <p class="mt-3 text-14 text-secondary">Select an Institute first</p>
          </div>`;
        return;
      }
      fetch('/api/view-students-by-institute?institute_id=' + instituteId, {
        method: 'GET',
        headers: { 'Accept': 'application/json', 'Authorization': token }
      })
      .then(response => response.json())
      .then(data => {
        if(data.status === "success") {
          populateAccordion(data.data);
        } else {
          document.getElementById('accordionContainer').innerHTML =
            '<div class="alert alert-danger">Error: ' + data.message + '</div>';
        }
      })
      .catch(err => {
        console.error(err);
        document.getElementById('accordionContainer').innerHTML =
          '<div class="alert alert-danger">An error occurred while fetching data.</div>';
      });
    });

    function populateAccordion(students) {
  const groups = { 'GEN': [], 'EWS': [], 'TFW': [] };
  students.forEach(student => {
    // Only process active students
    if (student.status === 'Active' && student.course) {
      try {
        const courseData = JSON.parse(student.course);
        const feeType = courseData.fee_type ? courseData.fee_type.toUpperCase() : 'GEN';
        if (groups[feeType]) {
          groups[feeType].push({
            name: student.name,
            uid: student.uid,
            phone: student.phone,
            institute_id: student.institute ? JSON.parse(student.institute).institution_id : '',
            institution_name: student.institute ? JSON.parse(student.institute).institution_name : '',
            course_id: student.course_id || "", // if available
            program_code: courseData.program_code || '',
            program_name: courseData.program_name || '',
            intake_type: courseData.intake_type || '',
            intake_year: courseData.intake_year || '',
            fee_type: courseData.fee_type || ''
          });
        } else {
          groups['GEN'].push({
            name: student.name,
            uid: student.uid,
            phone: student.phone,
            institute_id: student.institute ? JSON.parse(student.institute).institution_id : '',
            institution_name: student.institute ? JSON.parse(student.institute).institution_name : '',
            course_id: student.course_id || "",
            program_code: courseData.program_code || '',
            program_name: courseData.program_name || '',
            intake_type: courseData.intake_type || '',
            intake_year: courseData.intake_year || '',
            fee_type: courseData.fee_type || ''
          });
        }
      } catch (e) {
        console.error('Error parsing course data for student', student, e);
      }
    }
  });

  let accordionHTML = '<div class="accordion" id="studentsAccordion">';
  const feeTypes = ['GEN', 'EWS', 'TFW'];
  feeTypes.forEach((feeType, index) => {
    const studentsInGroup = groups[feeType];
    accordionHTML += `
      <div class="accordion-item">
        <h2 class="accordion-header" id="heading${feeType}">
          <button class="accordion-button ${index !== 0 ? 'collapsed' : ''} text-14" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${feeType}" aria-expanded="${index === 0 ? 'true' : 'false'}" aria-controls="collapse${feeType}">
            ${feeType} (${studentsInGroup.length})
          </button>
        </h2>
        <div id="collapse${feeType}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" aria-labelledby="heading${feeType}" data-bs-parent="#studentsAccordion">
          <div class="accordion-body text-13">
            ${studentsInGroup.length > 0 ? generateStudentTable(studentsInGroup) : `
            <div class="text-center">
              <img src="{{ asset('assets/web_assets/noData.png') }}" alt="No Data" class="img-fluid">
              <br>
              No Students Found.
            </div>`}
          </div>
        </div>
      </div>`;
  });
  accordionHTML += '</div>';
  document.getElementById('accordionContainer').innerHTML = accordionHTML;
  attachAccordionSearch();
}


    function generateStudentTable(students) {
  // Build search input and course filter dropdown
  let html = `
    <div class="mb-3 d-flex gap-2">
      <div class="input-group flex-grow-1">
        <span class="input-group-text text-13"><i class="fa-solid fa-search"></i></span>
        <input type="text" class="form-control accordion-search text-13" placeholder="Search by name, email, phone, etc.">
      </div>
      <div>
        <select class="form-select course-filter text-13">
          <option value="">All Courses</option>`;
  // Get unique courses from the students list
  const courseSet = new Set();
  students.forEach(student => {
    if (student.program_name) {
      courseSet.add(student.program_name);
    }
  });
  Array.from(courseSet).sort().forEach(course => {
    html += `<option value="${course}">${course}</option>`;
  });
  html += `</select>
      </div>
    </div>`;
  
  // Pagination variables
  const rowsPerPage = 50;
  let currentPage = 1;
  let filteredStudents = students.slice();
  
  // Function to render table rows for a given page
  function renderPage(page) {
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    let rowsHtml = '';
    filteredStudents.slice(start, end).forEach(student => {
      rowsHtml += `<tr>
        <td>${student.name}</td>
        <td>${student.phone}</td>
        <td>${student.institution_name}</td>
        <td>${student.program_name}</td>
        <td>${student.intake_type}</td>
        <td>${student.intake_year}</td>
        <td class="d-flex justify-content-end">
          <button class="btn btn-primary btn-sm text-13" onclick="payFees('${student.name}','${student.institute_id}','${student.institution_name}', '${student.course_id}', '${student.program_code}','${student.program_name}', '${student.intake_type}', '${student.intake_year}', '${student.fee_type}','${student.uid}')">
             <i class="fa-solid fa-credit-card"></i> Pay 
            </button>
        </td>
      </tr>`;
    });
    return rowsHtml;
  }
  
  // Function to render pagination controls with First/Previous/Page X/Y/Next/Last
  function renderPagination() {
    const totalPages = Math.ceil(filteredStudents.length / rowsPerPage);
    let paginationHtml = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    // First button
    if (currentPage > 1) {
      paginationHtml += `<li class="page-item">
        <a class="page-link text-13" href="#" data-page="1" aria-label="First">
          <i class="fa-solid fa-angles-left"></i>
        </a>
      </li>`;
    } else {
      paginationHtml += `<li class="page-item disabled">
        <span class="page-link text-13" aria-label="First">
          <i class="fa-solid fa-angles-left"></i>
        </span>
      </li>`;
    }
    
    // Previous button
    if (currentPage > 1) {
      paginationHtml += `<li class="page-item">
        <a class="page-link text-13" href="#" data-page="${currentPage - 1}" aria-label="Previous">
          <i class="fa-solid fa-angle-left"></i>
        </a>
      </li>`;
    } else {
      paginationHtml += `<li class="page-item disabled">
        <span class="page-link text-13" aria-label="Previous">
          <i class="fa-solid fa-angle-left"></i>
        </span>
      </li>`;
    }
    
    // Page indicator
    paginationHtml += `<li class="page-item disabled">
      <span class="page-link text-13">Page ${currentPage} / ${totalPages}</span>
    </li>`;
    
    // Next button
    if (currentPage < totalPages) {
      paginationHtml += `<li class="page-item">
        <a class="page-link text-13" href="#" data-page="${currentPage + 1}" aria-label="Next">
          <i class="fa-solid fa-angle-right"></i>
        </a>
      </li>`;
    } else {
      paginationHtml += `<li class="page-item disabled">
        <span class="page-link text-13" aria-label="Next">
          <i class="fa-solid fa-angle-right"></i>
        </span>
      </li>`;
    }
    
    // Last button
    if (currentPage < totalPages) {
      paginationHtml += `<li class="page-item">
        <a class="page-link text-13" href="#" data-page="${totalPages}" aria-label="Last">
          <i class="fa-solid fa-angles-right"></i>
        </a>
      </li>`;
    } else {
      paginationHtml += `<li class="page-item disabled">
        <span class="page-link text-13" aria-label="Last">
          <i class="fa-solid fa-angles-right"></i>
        </span>
      </li>`;
    }
    
    paginationHtml += '</ul></nav>';
    return paginationHtml;
  }
  
  // Build table HTML and pagination container
  let tableHtml = `
    <div class="table-responsive">
      <table class="table table-striped text-13">
        <tbody>
          ${renderPage(currentPage)}
        </tbody>
      </table>
    </div>
    <div class="pagination-container">
      ${renderPagination()}
    </div>
  `;
  
  const combinedHtml = html + tableHtml;
  
  // After the HTML is rendered into the DOM, attach event listeners
  setTimeout(() => {
    // Pagination click events
    const paginationContainer = document.querySelector('.pagination-container');
    if (paginationContainer) {
      paginationContainer.addEventListener('click', function(e) {
        const link = e.target.closest('.page-link');
        if (link && link.getAttribute('data-page')) {
          e.preventDefault();
          currentPage = parseInt(link.getAttribute('data-page'));
          const tbody = document.querySelector('.pagination-container').previousElementSibling.querySelector('tbody');
          if (tbody) {
            tbody.innerHTML = renderPage(currentPage);
          }
          paginationContainer.innerHTML = renderPagination();
        }
      });
    }
    
    // Search and course filter events
    const searchInput = document.querySelector('.accordion-search');
    const courseFilter = document.querySelector('.course-filter');
    function applyFilters() {
      const searchText = searchInput.value.toLowerCase().trim();
      const selectedCourse = courseFilter.value;
      filteredStudents = students.filter(student => {
        const matchSearch = student.name.toLowerCase().includes(searchText) ||
                            student.email.toLowerCase().includes(searchText) ||
                            student.phone.toLowerCase().includes(searchText) ||
                            student.institution_name.toLowerCase().includes(searchText) ||
                            student.program_name.toLowerCase().includes(searchText) ||
                            student.intake_type.toLowerCase().includes(searchText) ||
                            student.intake_year.toString().toLowerCase().includes(searchText);
        const matchCourse = selectedCourse ? (student.program_name === selectedCourse) : true;
        return matchSearch && matchCourse;
      });
      currentPage = 1;
      const tbody = searchInput.closest('.accordion-body').querySelector('.table-responsive table tbody');
      if (tbody) {
        tbody.innerHTML = renderPage(currentPage);
      }
      if (paginationContainer) {
        paginationContainer.innerHTML = renderPagination();
      }
    }
    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (courseFilter) courseFilter.addEventListener('change', applyFilters);
  }, 0);
  
  return combinedHtml;
}


    function attachAccordionSearch() {
      const searchInputs = document.querySelectorAll('.accordion-search');
      searchInputs.forEach(searchInput => {
        searchInput.addEventListener('input', function() {
          const filter = this.value.toLowerCase().trim();
          const tbody = this.closest('.accordion-body').querySelector('table tbody');
          let rows = tbody.querySelectorAll('tr');
          // Remove any existing "no-data" row first
          tbody.querySelectorAll('.no-data').forEach(r => r.remove());
          let visibleCount = 0;
          rows.forEach(row => {
            if(row.textContent.toLowerCase().includes(filter)) {
              row.style.display = "";
              visibleCount++;
            } else {
              row.style.display = "none";
            }
          });
          if (visibleCount === 0) {
            const noDataRow = `<tr class="no-data">
                                  <td colspan="9" class="text-center">
                                    <img src="{{ asset('assets/web_assets/noData.png') }}" alt="No Data" class="img-fluid" style="max-width:200px;"><br>
                                    No Students Found.
                                  </td>
                                </tr>`;
            tbody.insertAdjacentHTML('beforeend', noDataRow);
          }
        });
      });
    }

    // When "Pay Fees" button is clicked, hide accordion and show payment interface,
    // then fetch fee details via API.
    function payFees(studentName, instituteId, instituteName, courseId, programCode, programName, intakeType, year, feeType, student_uid) {
      document.getElementById('accordionContainer').classList.add('d-none');
      document.getElementById('paymentInterface').classList.remove('d-none');
      document.getElementById('paymentDetails').classList.add('d-none');
      document.getElementById('paymentLoadingSpinner').classList.remove('d-none');

      fetch('/api/search-fees', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': token,
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ 
          institute_id: instituteId,
          program_code: programCode,
          intake_type: intakeType,
          year: String(year),
          fee_type: feeType
        })
      })
      .then(response => response.json())
      .then(data => {
        document.getElementById('paymentLoadingSpinner').classList.add('d-none');
        if (data.status === 'success' && data.data && data.data.length > 0) {
          let feeRecord = data.data[0];
          // Pass feeRecord along with student details to populate the payment interface.
          getDiscount(feeRecord, instituteId, instituteName, courseId, programCode, programName, intakeType, year, feeType, studentName, student_uid);
        } else {
          document.getElementById('paymentDetails').classList.remove('d-none');
          document.getElementById('paymentDetails').innerHTML = `<p class="text-danger text-13">No fee record found for the selected criteria.</p>`;
        }
      })
      .catch(error => {
        console.error('Error fetching fee details:', error);
        document.getElementById('paymentLoadingSpinner').classList.add('d-none');
        document.getElementById('paymentDetails').classList.remove('d-none');
        document.getElementById('paymentDetails').innerHTML = `<p class="text-danger text-13">An error occurred while fetching fee details.</p>`;
      });
    }
    function getDiscount(feeRecord, instituteId, instituteName, courseId, programCode, programName, intakeType, year, feeType, studentName, studentUid) {
  // Set the current payment parameters for later use
  currentPaymentParams = {
    fee: feeRecord,
    instituteId: instituteId,
    instituteName: instituteName,
    courseId: courseId,
    programCode: programCode,
    programName: programName,
    intakeType: intakeType,
    year: year,
    feeType: feeType,
    studentName: studentName,
    student_uid: studentUid
  };
  currentPaymentParams = {
        fee: feeRecord,
        instituteId: instituteId,
        instituteName: instituteName,
        courseId: courseId,
        programCode: programCode,
        programName: programName,
        intakeType: intakeType,
        year: year,
        feeType: feeType,
        studentName: studentName,
        student_uid: studentUid
      };

  fetch('/api/scholarship/view', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': token,
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({ student_uid: studentUid })
  })
  .then(response => response.json())
  .then(data => {
    let scholarshipOverallDiscount = "";
    let scholarshipOneTimeDiscount = "";
    let scholarshipSemWiseDiscount = "";
    if (data.status === 'success' && data.data) {
      const scholarship = data.data;
      scholarshipOverallDiscount = scholarship.overall_discount || "";
      scholarshipOneTimeDiscount = scholarship.one_time_discount || "";
      scholarshipSemWiseDiscount = scholarship.sem_wise_discount || "";
    }
    // Pass the fetched discount values (or empty strings) to the populatePaymentDetails function.
    populatePaymentDetails(feeRecord, instituteId, instituteName, courseId, programCode, programName, intakeType, year, feeType, studentName, studentUid, scholarshipOverallDiscount, scholarshipOneTimeDiscount, scholarshipSemWiseDiscount);
  })
  .catch(error => {
    console.error("Error fetching scholarship record:", error);
  });
}

function computeDiscount(total, discountStr) {
  let discountAmount = 0;
  if (!discountStr) return { discountAmount, finalTotal: total };
  discountStr = discountStr.trim();
  if (discountStr.endsWith('%')) {
    const perc = parseFloat(discountStr.slice(0, -1));
    if (!isNaN(perc)) {
      discountAmount = total * perc / 100;
    }
  } else {
    discountAmount = parseFloat(discountStr) || 0;
    if (discountAmount > total) {
      discountAmount = total;
    }
  }
  return { discountAmount, finalTotal: total - discountAmount };
}


    // Added: Refresh functionality in populatePaymentDetails
    async function populatePaymentDetails(
      fee,
      instituteId,
      instituteName,
      courseId,
      programCode,
      programName,
      intakeType,
      year,
      feeType,
      studentName,
      studentUid,
      scholarshipOverallDiscount,
      scholarshipOneTimeDiscount,
      scholarshipSemWiseDiscount
    ) {
      // Store parameters for refresh

      let feesData = null;
      
      // Fetch fee details from the API
      try {
        const response = await fetch('/api/get-fees', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': token,
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          },
          body: JSON.stringify({
            student_uid: studentUid,
            institute_id: instituteId,
            program_code: programCode,
            intake_type: intakeType,
            intake_year: year,
            fee_type: feeType,
          }),
        });
        if (!response.ok) {
          throw new Error(`Network response was not ok (Status: ${response.status})`);
        }
        const data = await response.json();
        if (data.status === 'success') {
          feesData = data.data;
        } else {
          console.error("API returned error:", data.message);
          feesData = data.data;
        }
      } catch (error) {
        console.error("Error during fetch operation:", error);
      }
      
      // Parse fee details from the provided fee object
      let oneTimeFees = {};
      let semesterFees = {};
      let otherFees = {};
      
      try {
        oneTimeFees = fee.one_time_fees ? JSON.parse(fee.one_time_fees) : {};
      } catch (e) {
        console.error("Error parsing one-time fees:", e);
      }
      try {
        semesterFees = fee.semester_wise_fees ? JSON.parse(fee.semester_wise_fees) : {};
      } catch (e) {
        console.error("Error parsing semester-wise fees:", e);
      }
      try {
        otherFees = fee.other_fees ? JSON.parse(fee.other_fees) : {};
      } catch (e) {
        console.error("Error parsing other fees:", e);
      }
      
      // Build the HTML structure
      let html = "";
      // Added refresh button at the top of payment details
      // html += '';
      let rawOnetimeTotal = 0;
      let onetimeDiscount = 0
      let rawSemTotal = 0;
      let semWiseDiscount = 0;
      let oneTimeTotal = 0;
      // Parse paid fees from feesData (if available)
      let paid_onetimefees = (() => { try { return JSON.parse(feesData.one_time_fees); } catch (e) { return ""; } })();
      let paid_semesterfees = (() => { try { return JSON.parse(feesData.semester_fees); } catch (e) { return ""; } })();
      let Paid_allfees = (() => { try { return JSON.parse(feesData.overall_total_fees); } catch (e) { return ""; } })();

      // console.log("hellloefdf",paid_semesterfees[1].fee_details.amount)
      // Set payment header
      document.getElementById('payment_details_head').innerHTML = `${studentName} | ${programName} | ${intakeType} | ${feeType}`;
      
      // One-Time Fees Section
      html += '<h6 class="mb-2 text-14"><i class="fa-solid fa-receipt me-1"></i> One-Time Fees</h6>';
      if (Object.keys(oneTimeFees).length > 0) {
        html += '<ul class="list-group mb-3 text-13">';
        for (let head in oneTimeFees) {
          let amount = parseFloat(oneTimeFees[head]) || 0;
          oneTimeTotal += amount;
          html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                     <span>${head}</span>
                     <span class="badge bg-primary">₹ ${amount}</span>
                   </li>`;
        }
      let discountAmount = 0;
      let finalOneTime = (oneTimeTotal).toFixed(2);
      rawOnetimeTotal = (oneTimeTotal).toFixed(2);
      if (scholarshipOverallDiscount && scholarshipOverallDiscount.trim() !== "") {
        let overAllDiscount= JSON.parse(scholarshipOverallDiscount)
        const discountValue = (overAllDiscount.one_time_discount).toString();
        const result = computeDiscount(oneTimeTotal, discountValue);
        oneTimeDiscountApplied = result.discountAmount;
        discountAmount = (result.discountAmount).toFixed(2);
        onetimeDiscount = discountAmount;
        oneTimeTotal = (result.finalTotal).toFixed(2);
      } else if (scholarshipOneTimeDiscount && scholarshipOneTimeDiscount.trim() !== "") {
        const result = computeDiscount(oneTimeTotal, scholarshipOneTimeDiscount);
        oneTimeDiscountApplied = result.discountAmount;
        discountAmount = (result.discountAmount).toFixed(2);
        onetimeDiscount = discountAmount;
        oneTimeTotal = (result.finalTotal).toFixed(2);
      }
        html += `<li class="list-group-item d-flex flex-column">
                  <div class="d-flex justify-content-end">
                    ${discountAmount
                  ?
                  `<span>Total One-Time Fees: <del>₹ ${finalOneTime}</del>  <span class="text-primary">-₹ ${discountAmount}</span>  <strong>= ₹ ${oneTimeTotal}</strong></span>
`
                  :`<span>Total One-Time Fees: <strong> ₹ ${oneTimeTotal.toFixed(2)}</strong></span>` 
                  }
                  </div>
                  <div class="mt-2">
                    ${ (paid_onetimefees && typeof paid_onetimefees === 'object' && Object.keys(paid_onetimefees).length > 0) ? (() => {
                        let totalPaid = Object.values(paid_onetimefees).reduce((sum, payment) => sum + parseFloat(payment.amount || 0), 0);
                        let breakdown = Object.entries(paid_onetimefees).map(([key, payment]) => {
                            return `<div class="d-flex justify-content-end align-items-center mb-1">
                                      <span class="me-2">Payment ${key}: <span class="text-primary">₹ ${payment.amount}</span> Date: ${payment.date}</span>
                                      <button class="btn btn-outline-primary btn-sm" onclick='showInvoice("one_time", {
                                            student_uid: "${studentUid}",
                                            student_name: "${studentName}",
                                            institute_id: "${instituteName}",
                                            course_id: "${programName}",
                                            intake_type: "${fee.intake_type || "General"}",
                                            intake_year: "${fee.intake_year || "2025"}",
                                            fee_type: "${fee.fee_type || "gen"}",
                                            fee_head: "One-Time Fees",
                                            transactionId : "${payment.transaction_id}",
                                            fee_detail: JSON.stringify({amount: "${payment.amount}"})
                                      })'><i class="fa-solid fa-print"></i></button>
                                    </div>`;
                        }).join('');
                        let remaining = oneTimeTotal - totalPaid;
                        return breakdown + (remaining > 0 
                                ? `<div class="d-flex justify-content-end align-items-center mt-2">
                                    <span class="me-2">Remaining Fees: <strong>₹ ${remaining.toFixed(2)}</strong></span>
                                  </div>`
                                : `<div class="d-flex justify-content-end align-items-center mt-2">
                                    <span class="text-success"><i class="fa-solid fa-check"></i> Fully paid</span>
                                  </div>`
                              );
                          })() : `<div class="d-flex justify-content-end align-items-center mt-2">
                                    <span class="me-2">Remaining Fees: <strong>₹ ${oneTimeTotal}</strong></span>
                                  </div>` }
                  </div>
                  <div class="d-flex justify-content-end mt-2">
                    ${ ((!paid_onetimefees) || (typeof paid_onetimefees !== 'object') || (Object.keys(paid_onetimefees).length === 0) 
                        || (oneTimeTotal - Object.values(paid_onetimefees).reduce((sum, payment) => sum + parseFloat(payment.amount || 0), 0)) > 0)
                        ? `<button class="btn btn-outline-success btn-sm" onclick='initiatePayment("one_time", {
                                student_uid: "${studentUid}",
                                institute_id: "${instituteId}",
                                course_id: "${courseId}",
                                program_code: "${programCode}",
                                intake_type: "${fee.intake_type || "General"}",
                                intake_year: "${fee.intake_year || "2025"}",
                                fee_type: "${fee.fee_type || "gen"}",
                                fee_detail: JSON.stringify({amount: "${ oneTimeTotal - ((paid_onetimefees && typeof paid_onetimefees === 'object') ? Object.values(paid_onetimefees).reduce((sum, payment) => sum + parseFloat(payment.amount || 0), 0) : 0)}"})
                        })'><i class="fa-solid fa-credit-card"></i> Pay</button>`
                        : "" }
                  </div>
                </li>`;


        html += '</ul>';
      } else {
        html += '<p class="text-muted text-13">No one-time fees.</p>';
      }

      // Semester-Wise Fees Section (Merged with Other Fees)
      let semesters = new Set();
      for (let feeHead in semesterFees) {
        let semObj = semesterFees[feeHead];
        Object.keys(semObj).forEach((sem) => semesters.add(sem));
      }
      for (let sem in otherFees) {
        semesters.add(sem);
      }
      semesters = Array.from(semesters).sort();

      let overallMergedTotal = 0;
      let mergedHtml = "";
      if (semesters.length > 0) {
        mergedHtml += `<div class="accordion" id="mergedSemesterAccordion">`;
        semesters.forEach((sem, index) => {
          let semTotal = 0;
          let feeListHtml = '<ul class="list-group text-13">';
          
          // Regular semester fees for this semester
          for (let feeHead in semesterFees) {
            let semObj = semesterFees[feeHead];
            if (sem in semObj) {
              let amt = parseFloat(semObj[sem]) || 0;
              semTotal += amt;
              feeListHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${feeHead} (Regular)</span>
                                <span class="badge bg-success">₹ ${amt}</span>
                              </li>`;
            }
          }
          // Other fees for this semester (if any)
          if (otherFees[sem]) {
            let feeObj = otherFees[sem];
            for (let feeHead in feeObj) {
              let amt = parseFloat(feeObj[feeHead]) || 0;
              semTotal += amt;
              feeListHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>${feeHead} (Other)</span>
                                <span class="badge bg-info">₹ ${amt}</span>
                              </li>`;
            }
          }
          
         // Build the semester fee list item
         let paidPayments = [];
            if (paid_semesterfees[sem] && typeof paid_semesterfees[sem] === 'object') {
              // Assuming paid_semesterfees[sem] is an object with incremental keys for each partial payment
              paidPayments = Object.values(paid_semesterfees[sem]);
            }
            let totalPaid = 0;
            let partPaymentHtml = "";
            paidPayments.forEach((payment, index) => {
              const paymentAmount = parseFloat(payment.fee_details.amount) || 0;
              totalPaid += paymentAmount;
              partPaymentHtml += `
                <div class="d-flex justify-content-end align-items-center mb-1">
                  
                  <span class="me-2">Payment ${index + 1}: <span class="text-primary">₹ ${paymentAmount}</span> Date: ${payment.fee_details.date}</span>
                  <button class="btn btn-outline-primary btn-sm" onclick='showInvoice("semester", {
                      student_uid: "${studentUid}",
                      student_name: "${studentName}",
                      institute_id: "${instituteName}",
                      course_id: "${programName}",
                      intake_type: "${fee.intake_type || "General"}",
                      intake_year: "${fee.intake_year || "2025"}",
                      fee_type: "${fee.fee_type || "gen"}",
                      fee_head: "Semester ${sem}",
                      transactionId : "${payment.transaction_id}",
                      fee_detail: JSON.stringify({amount: "${paymentAmount}"})
                  })'><i class="fa-solid fa-print"></i></button>
                </div>`;
            });

            // Apply discount on the semester total
            let semDiscountApplied = 0;
            let finalSemTotal = (semTotal).toFixed(2);
            rawSemTotal += parseFloat(semTotal.toFixed(2));
            if (scholarshipOverallDiscount && scholarshipOverallDiscount.trim() !== "") {
              let overAllDiscount = JSON.parse(scholarshipOverallDiscount);
              const discountValue = overAllDiscount[sem].toString();
              // Use semTotal here instead of oneTimeTotal
              const result = computeDiscount(semTotal, discountValue);
              semDiscountApplied = (result.discountAmount).toFixed(2);
              semWiseDiscount += parseFloat(semDiscountApplied);
              semTotal = (result.finalTotal).toFixed(2);
            } else if (scholarshipSemWiseDiscount && scholarshipSemWiseDiscount.trim() !== "") {
              let semDiscountObj = {};
              try {
                semDiscountObj = JSON.parse(scholarshipSemWiseDiscount);
              } catch(e) {
                console.error("Error parsing semester-wise discount:", e);
              }
              if (semDiscountObj[sem] && semDiscountObj[sem].amount && semDiscountObj[sem].amount.trim() !== "") {
                const result = computeDiscount(semTotal, semDiscountObj[sem].amount);
                semDiscountApplied = (result.discountAmount).toFixed(2);
                semWiseDiscount += parseFloat(semDiscountApplied);
                semTotal = (result.finalTotal).toFixed(2);
              }
            }

            const remaining = semTotal - totalPaid;

            feeListHtml += `
              <li class="list-group-item">
                <div class="d-flex justify-content-end align-items-center">
                  ${semDiscountApplied
                  ?
                  `<span>Total One-Time Fees: <del>₹ ${finalSemTotal}</del>  <span class="text-primary">-₹ ${semDiscountApplied}</span>  <strong>= ₹ ${semTotal}</strong></span>`
                  :`<span>Total One-Time Fees: <strong> ₹ ${semTotal}</strong></span>` 
                  }
                </div>
                ${ partPaymentHtml ? `<div class="mt-2">${partPaymentHtml}</div>` : "" }
                <div class="d-flex justify-content-end align-items-center mt-2">
                  ${ remaining > 0 ? `
                    <span class="me-2">Remaining Fees: <strong>₹ ${remaining.toFixed(2)}</strong></span>
                    <button class="btn btn-outline-success btn-sm" onclick='initiatePayment("semester", {
                      student_uid: "${studentUid}",
                      institute_id: "${instituteId}",
                      course_id: "${courseId}",
                      program_code: "${programCode}",
                      intake_type: "${fee.intake_type || "General"}",
                      intake_year: "${fee.intake_year || "2025"}",
                      fee_type: "${fee.fee_type || "gen"}",
                      semester_head: "${sem}",
                      fee_detail: JSON.stringify({amount: "${remaining}"})
                    })'><i class="fa-solid fa-credit-card"></i> Pay</button>
                  ` : `<span class="text-success"><i class="fa-solid fa-check"></i> Fully paid</span>` }
                </div>
              </li>
            `;


          feeListHtml += '</ul>';
          overallMergedTotal += semTotal;
          
          mergedHtml += `
            <div class="accordion-item">
              <h2 class="accordion-header" id="headingMerged${index}">
                <button class="accordion-button ${index === 0 ? '' : 'collapsed'} text-14" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMerged${index}" aria-expanded="${index === 0 ? 'true' : 'false'}" aria-controls="collapseMerged${index}">
                  Semester ${sem}
                </button>
              </h2>
              <div id="collapseMerged${index}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" aria-labelledby="headingMerged${index}" data-bs-parent="#mergedSemesterAccordion">
                <div class="accordion-body text-13">
                  ${feeListHtml}
                </div>
              </div>
            </div>
          `;
        });
        mergedHtml += `</div>`;
      } else {
        mergedHtml = '<p class="text-muted text-13">No semester-wise fees.</p>';
      }
      html += '<h6 class="mb-2 text-14"><i class="fa-solid fa-calendar-days me-1"></i> Semester-Wise Fees</h6>' + mergedHtml;

      // Overall Total Fees Section (One-Time + Semester)
      const overallTotal = parseFloat(rawOnetimeTotal) + parseFloat(rawSemTotal);
      const oneTimeDiscount = onetimeDiscount;
      const semDiscount = semWiseDiscount;
      const TotalDiscount =  parseFloat(onetimeDiscount) + parseFloat(semWiseDiscount);
      const FinalTotal =  parseFloat(overallTotal) - parseFloat(TotalDiscount);
      

// Compute already paid amounts for one-time fees
let paidOneTimeTotal = 0;
if (paid_onetimefees && typeof paid_onetimefees === 'object') {
  paidOneTimeTotal = Object.values(paid_onetimefees).reduce((sum, payment) => {
    return sum + parseFloat(payment.amount || 0);
  }, 0);
}

// Compute already paid amounts for semester fees
let paidSemesterTotal = 0;
if (paid_semesterfees && typeof paid_semesterfees === 'object') {
  for (let sem in paid_semesterfees) {
    let payments = paid_semesterfees[sem];
    if (payments && typeof payments === 'object') {
      paidSemesterTotal += Object.values(payments).reduce((sum, payment) => {
        return sum + parseFloat(payment.fee_details.amount || 0);
      }, 0);
    }
  }
}


let totalPaid = paidOneTimeTotal + paidSemesterTotal;
let remainingTotal = FinalTotal - totalPaid;

html += `<div class="d-flex flex-column align-items-end mt-4">
          <span>Overall Total Fees: <del>₹ ${overallTotal.toFixed(2)}</del>  <span class="text-primary">-₹ ${TotalDiscount}</span>  <strong>= ₹ ${FinalTotal.toFixed(2)}</strong></span>
          <span>Overall Total Paid: <strong>= ₹ ${totalPaid.toFixed(2)}</strong></span>
          <span>Overall Remaining Fees: <strong>= ₹ ${remainingTotal.toFixed(2)}</strong></span>
        </div>`;

      
      const paymentDetailsEl = document.getElementById('paymentDetails');
      if (paymentDetailsEl) {
        paymentDetailsEl.classList.remove('d-none');
        paymentDetailsEl.innerHTML = html;
      }
    }

    // Added: Function to refresh payment details using stored parameters.
    function refreshPaymentDetails() {
  const refreshIcon = document.querySelector('span[onclick="refreshPaymentDetails()"]');
  if (refreshIcon) {
    refreshIcon.classList.add('rotate');
    setTimeout(() => {
      refreshIcon.classList.remove('rotate');
    }, 1000);
  }
  
  if (currentPaymentParams) {
    getDiscount(
      currentPaymentParams.fee,
      currentPaymentParams.instituteId,
      currentPaymentParams.instituteName,
      currentPaymentParams.courseId,
      currentPaymentParams.programCode,
      currentPaymentParams.programName,
      currentPaymentParams.intakeType,
      currentPaymentParams.year,
      currentPaymentParams.feeType,
      currentPaymentParams.studentName,
      currentPaymentParams.student_uid
    );
  }
}


    function closePaymentInterface() {
  Swal.fire({
    title: 'Are you sure?',
    text: "Do you want to close the payment interface?",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, close it!',
    cancelButtonText: 'No, keep it open'
  }).then((result) => {
    if (result.isConfirmed) {
      document.getElementById('paymentInterface').classList.add('d-none');
      document.getElementById('accordionContainer').classList.remove('d-none');
    }
  });
}

// Helper function to format date and time
function getFormattedDateTime() {
  const now = new Date();
  const year = now.getFullYear();
  const month = String(now.getMonth() + 1).padStart(2, '0'); // months are 0-indexed
  const day = String(now.getDate()).padStart(2, '0');
  const datePart = `${year}-${month}-${day}`;

  let hours = now.getHours();
  const minutes = String(now.getMinutes()).padStart(2, '0');
  const ampm = hours >= 12 ? 'PM' : 'AM';
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  const timePart = `${hours}:${minutes} ${ampm}`;

  return `${datePart} : ${timePart}`;
}


    // Function to initiate a payment.
    function initiatePayment(paymentCategory, paymentData) {
  let feeFieldData = {};
  try {
    feeFieldData = JSON.parse(paymentData.fee_detail) || {};
  } catch (e) {
    feeFieldData = {};
  }
  const fullAmount = parseFloat(feeFieldData.amount) || 0;
  
  Swal.fire({
  title: 'Enter Payment Amount',
  input: 'number',
  inputLabel: 'Payment Amount (₹)',
  inputValue: fullAmount,
  inputAttributes: { 
    max: fullAmount,
    min: 1
  },
  showCancelButton: true,
  inputValidator: (value) => {
    if (!value || parseFloat(value) <= 0) {
      return 'Please enter a valid amount';
    }
    if (parseFloat(value) > fullAmount) {
      return `The amount cannot exceed ₹ ${fullAmount}`;
    }
  }
}).then((amountResult) => {
    if (amountResult.isConfirmed) {
      const paymentAmount = parseFloat(amountResult.value);
      
      Swal.fire({
        title: 'Choose Payment Method',
        input: 'select',
        inputOptions: {
          'cash': 'Cash',
          'card': 'Card',
          'upi': 'UPI'
        },
        inputPlaceholder: 'Select a payment method',
        showCancelButton: true
      }).then((methodResult) => {
        if (methodResult.isConfirmed && methodResult.value) {
          const paymentMethod = methodResult.value;
          feeFieldData.payment_method = paymentMethod;
          feeFieldData.date = getFormattedDateTime();
          feeFieldData.amount = paymentAmount;
          paymentData.fee_detail = JSON.stringify(feeFieldData);
          
          let body = {
            student_uid: paymentData.student_uid,
            institute_id: paymentData.institute_id,
            course_id: paymentData.course_id,
            program_code: paymentData.program_code,
            intake_type: paymentData.intake_type,
            intake_year: paymentData.intake_year,
            fee_type: paymentData.fee_type
          };
          if (paymentAmount < fullAmount) {
            body.part_payment = JSON.stringify({
              amount: paymentAmount,
              payment_method: paymentMethod,
              date: feeFieldData.date
            });
          }
          if (paymentCategory === 'one_time') {
            body.one_time_fees = paymentData.fee_detail;
          } else if (paymentCategory === 'semester') {
            body.semester_head = paymentData.semester_head;
            body.semester_fees = paymentData.fee_detail;
          } else if (paymentCategory === 'all_semester') {
            body.all_semester_fees = paymentData.fee_detail;
          } else if (paymentCategory === 'overall_total') {
            body.overall_total_fees = paymentData.fee_detail;
          }
          
          console.log(body);
          fetch('/api/pay-fees', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'Authorization': token,
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(body)
          })
          .then(response => response.json())
          .then(data => {
             if (data.status === 'success') {
                Swal.fire('Success', data.message, 'success');
                refreshPaymentDetails();
             } else {
                Swal.fire('Error', data.message || 'Payment failed.', 'error');
             }
          })
          .catch(error => {
             console.error('Error processing payment:', error);
             Swal.fire('Error', 'An error occurred while processing the payment.', 'error');
          });
        }
      });
    }
  });
}


    function showInvoice(paymentCategory, paymentData) {
      console.log(paymentData.transactionId)
      let feeDetail = {};
      try {
        feeDetail = paymentData.fee_detail ? JSON.parse(paymentData.fee_detail) : {};
      } catch (e) {
        console.error("Error parsing fee_detail:", e);
      }
      
      const invoiceHTML = `
      <html>
      <head>
        <title>Invoice</title>
        <style>
          body { font-family: Arial, sans-serif; margin: 20px; }
          .invoice-container { max-width: 800px; margin: auto; border: 1px solid #ccc; padding: 20px; }
          .invoice-header { text-align: center; margin-bottom: 20px; }
          .invoice-details { margin-bottom: 20px; }
          .invoice-footer { text-align: center; margin-top: 20px; }
          table { width: 100%; border-collapse: collapse; }
          table, th, td { border: 1px solid #ccc; }
          th, td { padding: 8px; text-align: left; }
          .print-btn { padding: 10px 20px; font-size: 14px; cursor: pointer; }
        </style>
      </head>
      <body>
        <div class="invoice-container">
          <div class="invoice-header">
            <h1>Invoice</h1>
            <p>Date: ${paymentData.paymentdate || new Date().toLocaleDateString()}</p>
          </div>
          <div class="invoice-details">
            <p><strong>Student UID:</strong> ${paymentData.student_uid || 'N/A'}</p>
            <p><strong>Student Name:</strong> ${paymentData.student_name || 'N/A'}</p>
            <p><strong>Institution:</strong> ${paymentData.institute_id || 'N/A'}</p>
            <p><strong>Course:</strong> ${paymentData.course_id || 'N/A'}</p>
            <p><strong>Intake Type:</strong> ${paymentData.intake_type || 'N/A'}</p>
            <p><strong>Intake Year:</strong> ${paymentData.intake_year || 'N/A'}</p>
            <p><strong>Fee Type:</strong> ${paymentData.fee_type || 'N/A'}</p>
            <p><strong>Transection ID:</strong> ${paymentData.transactionId || 'N/A'}</p>
            ${ paymentData.semester_head ? `<p><strong>Semester:</strong> ${paymentData.semester_head}</p>` : '' }
          </div>
          <div class="invoice-table">
            <table>
              <thead>
                <tr>
                  <th>Description</th>
                  <th>Amount (₹)</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>${paymentData.fee_head || 'N/A'}</td>
                  <td>${feeDetail.amount || '0.00'}</td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="invoice-footer">
            <button class="print-btn" onclick="window.print()">Print / Download Invoice</button>
          </div>
        </div>
      </body>
      </html>
      `;
      
      const invoiceWindow = window.open('', 'PrintInvoice', 'height=600,width=800');
      invoiceWindow.document.write(invoiceHTML);
      invoiceWindow.document.close();
      invoiceWindow.focus();
    }
  </script>
</body>
</html>
