<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Scholarship</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Custom Admin CSS -->
  {{-- <link rel="stylesheet" href="{{ asset('css/admin/addfees.css') }}"> --}}
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
  <style>
    .rotate {
      animation: rotation 1s linear;
    }
    @keyframes rotation {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
      <div class="container my-4">
        <!-- Institution Info Card (shown if institution details exist in sessionStorage) -->
        
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

        <!-- (Note: Remove duplicate institution info card if not needed) -->
        <p class="my-4 text-secondary text-14">
          <i class="fa-solid fa-angle-right"></i>
          <span class="text-primary">Manage Scholarship</span>
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
          <div class="text-center p-4 rounded bg-white">
            <img src="{{ asset('assets/web_assets/search.png') }}" alt="No Data" class="img-fluid" style="max-width:300px;">
            <p class="mt-3 text-14 text-secondary">Select an Institute first</p>
          </div>
        </div>
        <!-- Payment Interface Container (initially hidden) -->
        <div id="paymentInterface" class="container rounded d-none mt-4 bg-white p-4">
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
            <div class="p-4 bg-light m-2 shadow-sm rounded">
              <div class="row">
                <div class="col-md-6">
                  <span class="text-primary  rounded" id="manageScholarshipText"><span class="text-success"><i class="fa-solid fa-plus"></i></span> Scholarship</span>
                </div>
                <div class="col-md-6">
                  <select id="scholarshipTypeSelect" class="form-select text-13">
                    <option value="">Select Scholarship type</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="card-body position-relative bg-white py-4">
              <!-- Spinner while loading fee details -->
              <div id="paymentLoadingSpinner" class="d-flex justify-content-center align-items-center bg-white p-1">
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
  <!-- Merged Script: All functions defined once -->
  <script>
    // --- Global Variables ---
    const token = sessionStorage.getItem('token') || '';
    var currentPaymentParams = null;
    var oneTimeTotal = 0;
    var semesterTotals = {}; // e.g. { "1": 5000, "2": 4500 }
    var scholarshipOverallDiscount = null;
    var scholarshipOneTimeDiscount = null;
    var scholarshipSemWiseDiscount = null;

    // --- Initialization ---
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

    // --- Institutions & Students ---
    function fetchInstitutions() {
      const institutionId = sessionStorage.getItem("institution_id");
      const institutionSelect = document.getElementById('institutionSelect');
      if (institutionId) {
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

    // When institution selection changes, fetch students for that institution.
    document.getElementById('institutionSelect').addEventListener('change', function() {
      const instituteId = this.value;
      if (!instituteId) {
        document.getElementById('accordionContainer').innerHTML = `
          <div class="text-center p-4 bg-white rounded">
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

    // Build the accordion for student list
    function populateAccordion(students) {
      const groups = { 'GEN': [], 'EWS': [], 'TFW': [] };
      students.forEach(student => {
        if (student.status === 'Active' && student.course) {
          try {
            const courseData = JSON.parse(student.course);
            const feeType = courseData.fee_type ? courseData.fee_type.toUpperCase() : 'GEN';
            groups[feeType] = groups[feeType] || [];
            groups[feeType].push({
              name: student.name,
              email: student.email,
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

    // Generate the HTML table for a list of students with pagination, a course dropdown beside the search bar,
    // and "First" and "Last" buttons.
    function generateStudentTable(students) {

      let html = `
        <div class="mb-3 d-flex gap-2">
          <div class="input-group flex-grow-1">
            <span class="input-group-text text-13"><i class="fa-solid fa-search"></i></span>
            <input type="text" class="form-control accordion-search text-13" placeholder="Search by name, email, phone, etc.">
          </div>
          <div>
            <select class="form-select course-filter text-13">
              <option value="">All Courses</option>`;
      // Extract unique courses from students using program_name
      const courseSet = new Set();
      students.forEach(student => {
        if(student.program_name) { courseSet.add(student.program_name); }
      });
      const courseOptions = Array.from(courseSet).sort();
      courseOptions.forEach(course => {
        html += `<option value="${course}">${course}</option>`;
      });
      html += `</select>
          </div>
        </div>`;
      
      // Pagination variables
      const rowsPerPage = 50;
      let currentPage = 1;
      let filteredStudents = students.slice(); // copy of students
      
      // Function to render table rows for a given page
      function renderPage(page) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        let rowsHtml = '';
        filteredStudents.slice(start, end).forEach(student => {
          rowsHtml += `<tr>
            <td>${student.name}</td>
            <td>${student.email}</td>
            <td>${student.phone}</td>
            <td>${student.institution_name}</td>
            <td>${student.program_name}</td>
            <td>${student.intake_type}</td>
            <td>${student.intake_year}</td>
            <td class="d-flex justify-content-end">
              <button class="btn btn-primary btn-sm text-13" onclick="FeesStructure('${student.name}','${student.institute_id}','${student.institution_name}', '${student.course_id}', '${student.program_code}','${student.program_name}', '${student.intake_type}', '${student.intake_year}', '${student.fee_type}','${student.email}','${student.uid}')">
               Manage Scholarship 
              </button>
            </td>
          </tr>`;
        });
        return rowsHtml;
      }
      
      // Function to render pagination controls with "First", "Previous", "Page X/Y", "Next", "Last"
      function renderPagination() {
        const totalPages = Math.ceil(filteredStudents.length / rowsPerPage);
        let paginationHtml = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
        
        // First button
        if (currentPage > 1) {
          paginationHtml += `<li class="page-item">
            <a class="page-link text-13" href="#" data-page="1" aria-label="First"><i class="fa-solid fa-angles-left"></i></a>
          </li>`;
        } else {
          paginationHtml += `<li class="page-item disabled">
            <span class="page-link text-13" aria-label="First"><i class="fa-solid fa-angles-left"></i></span>
          </li>`;
        }
        
        // Previous button
        if (currentPage > 1) {
          paginationHtml += `<li class="page-item">
            <a class="page-link text-13" href="#" data-page="${currentPage - 1}" aria-label="Previous"><i class="fa-solid fa-angle-left"></i></a>
          </li>`;
        } else {
          paginationHtml += `<li class="page-item disabled">
            <span class="page-link text-13" aria-label="Previous"><i class="fa-solid fa-angle-left"></i></span>
          </li>`;
        }
        
        // Page label
        paginationHtml += `<li class="page-item disabled">
          <span class="page-link text-13">Page ${currentPage} / ${totalPages}</span>
        </li>`;
        
        // Next button
        if (currentPage < totalPages) {
          paginationHtml += `<li class="page-item">
            <a class="page-link text-13" href="#" data-page="${currentPage + 1}" aria-label="Next"><i class="fa-solid fa-angle-right"></i></a>
          </li>`;
        } else {
          paginationHtml += `<li class="page-item disabled">
            <span class="page-link text-13" aria-label="Next"><i class="fa-solid fa-angle-right"></i></span>
          </li>`;
        }
        
        // Last button
        if (currentPage < totalPages) {
          paginationHtml += `<li class="page-item">
            <a class="page-link text-13" href="#" data-page="${totalPages}" aria-label="Last"><i class="fa-solid fa-angles-right"></i></a>
          </li>`;
        } else {
          paginationHtml += `<li class="page-item disabled">
            <span class="page-link text-13" aria-label="Last"><i class="fa-solid fa-angles-right"></i></span>
          </li>`;
        }
        
        paginationHtml += '</ul></nav>';
        return paginationHtml;
      }
      
      // Container for table and pagination
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
      
      // Attach event delegation for pagination clicks and filter changes
      setTimeout(() => {
        // Pagination click events
        document.querySelector('.pagination-container').addEventListener('click', function(e) {
          const link = e.target.closest('.page-link');
          if (link && link.getAttribute('data-page')) {
            e.preventDefault();
            const newPage = parseInt(link.getAttribute('data-page'));
            if (!isNaN(newPage)) {
              currentPage = newPage;
              const tbody = document.querySelector('.accordion-body .table-responsive table tbody');
              if (tbody) { tbody.innerHTML = renderPage(currentPage); }
              document.querySelector('.pagination-container').innerHTML = renderPagination();
            }
          }
        });
        
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
          tbody.innerHTML = renderPage(currentPage);
          document.querySelector('.pagination-container').innerHTML = renderPagination();
        }
        
        searchInput.addEventListener('input', applyFilters);
        courseFilter.addEventListener('change', applyFilters);
      }, 0);
      
      return html + tableHtml;
    }

    // Attach search functionality to the accordion search inputs.
    function attachAccordionSearch() {
      const searchInputs = document.querySelectorAll('.accordion-search');
      searchInputs.forEach(searchInput => {
        searchInput.addEventListener('input', function() {
          const filter = this.value.toLowerCase().trim();
          const tbody = this.closest('.accordion-body').querySelector('table tbody');
          let rows = tbody.querySelectorAll('tr');
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

    // --- Scholarship Modals & API ---
    // Populate scholarship dropdown and payment details
    function populateScholarshipDropdown(feeRecord, instituteId, instituteName, courseId, programCode, programName, intakeType, year, feeType, studentName, studentEmail,uid) {
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
        studentEmail: studentEmail,
        studentUid: uid,
      };
      // console.log("uuuuuid",uid)
      fetch('/api/scholarship/view', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': token,
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ student_uid: uid })
      })
      .then(response => response.json())
      .then(data => {
        const scholarshipSelect = document.getElementById('scholarshipTypeSelect');
        scholarshipSelect.innerHTML = '<option value="">Select Scholarship type</option>';
        if (data.status === 'success' && data.data) {
          const scholarship = data.data;
          scholarshipOverallDiscount = scholarship.overall_discount;
          scholarshipOneTimeDiscount = scholarship.one_time_discount;
          scholarshipSemWiseDiscount = scholarship.sem_wise_discount;
          if (scholarshipOverallDiscount) {
            document.getElementById('manageScholarshipText').innerHTML = '<span class="text-success"><i class="fa-solid fa-pen-to-square"></i></span> Edit Scholarship';
            populatePaymentDetails(feeRecord, instituteId, instituteName, courseId, programCode, programName, intakeType, year, feeType, studentName, studentEmail,uid, scholarshipOverallDiscount, scholarshipOneTimeDiscount, scholarshipSemWiseDiscount);
            const option = document.createElement('option');
            option.value = 'overall';
            option.text = 'Overall Scholarship';
            scholarshipSelect.appendChild(option);
          } else {
            scholarshipSelect.parentElement.style.display = '';
            if (scholarshipOneTimeDiscount && !scholarshipSemWiseDiscount) {

              populatePaymentDetails(feeRecord, instituteId, instituteName, courseId, programCode, programName, intakeType, year, feeType, studentName, studentEmail,uid, scholarshipOverallDiscount, scholarshipOneTimeDiscount, scholarshipSemWiseDiscount);
              const option1 = document.createElement('option');
              option1.value = 'one_time';
              option1.text = 'One-Time Discount';
              const option2 = document.createElement('option');
              option2.value = 'sem_wise';
              option2.text = 'Semester-Wise Discount';
              scholarshipSelect.appendChild(option1);
              scholarshipSelect.appendChild(option2);
            }
            else if (scholarshipSemWiseDiscount && !scholarshipOneTimeDiscount) {
              populatePaymentDetails(feeRecord, instituteId, instituteName, courseId, programCode, programName, intakeType, year, feeType, studentName, studentEmail,uid, scholarshipOverallDiscount, scholarshipOneTimeDiscount, scholarshipSemWiseDiscount);
              const option1 = document.createElement('option');
              option1.value = 'one_time';
              option1.text = 'One-Time Discount';
              const option2 = document.createElement('option');
              option2.value = 'sem_wise';
              option2.text = 'Semester-Wise Discount';
              scholarshipSelect.appendChild(option1);
              scholarshipSelect.appendChild(option2);
            }
            else if (scholarshipOneTimeDiscount && scholarshipSemWiseDiscount) {
              document.getElementById('manageScholarshipText').innerHTML = '<span class="text-success"><i class="fa-solid fa-pen-to-square"></i></span> Edit Scholarship';
              populatePaymentDetails(feeRecord, instituteId, instituteName, courseId, programCode, programName, intakeType, year, feeType, studentName, studentEmail,uid, scholarshipOverallDiscount, scholarshipOneTimeDiscount, scholarshipSemWiseDiscount);
              const option1 = document.createElement('option');
              option1.value = 'one_time';
              option1.text = 'One-Time Discount';
              const option2 = document.createElement('option');
              option2.value = 'sem_wise';
              option2.text = 'Semester-Wise Discount';
              scholarshipSelect.appendChild(option1);
              scholarshipSelect.appendChild(option2);
            }
            else {
              populatePaymentDetails(feeRecord, instituteId, instituteName, courseId, programCode, programName, intakeType, year, feeType, studentName, studentEmail,uid, scholarshipOverallDiscount, scholarshipOneTimeDiscount, scholarshipSemWiseDiscount);
              const options = [
                { value: 'overall', text: 'Overall Scholarship' },
                { value: 'one_time', text: 'One-Time Discount' },
                { value: 'sem_wise', text: 'Semester-Wise Discount' }
              ];
              options.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.text = opt.text;
                scholarshipSelect.appendChild(option);
              });
            }
          }
        } else {
          populatePaymentDetails(feeRecord, instituteId, instituteName, courseId, programCode, programName, intakeType, year, feeType, studentName, studentEmail,uid);
          scholarshipSelect.parentElement.style.display = '';
          const options = [
            { value: 'overall', text: 'Overall Scholarship' },
            { value: 'one_time', text: 'One-Time Discount' },
            { value: 'sem_wise', text: 'Semester-Wise Discount' }
          ];
          options.forEach(opt => {
            const option = document.createElement('option');
            option.value = opt.value;
            option.text = opt.text;
            scholarshipSelect.appendChild(option);
          });
        }
      })
      .catch(error => {
        console.error("Error fetching scholarship record:", error);
      });
    }

    // Listen for changes on the scholarship dropdown
    document.getElementById("scholarshipTypeSelect").addEventListener("change", function() {
      const selectedType = this.value;
      if (!selectedType) return;
      if (selectedType === "one_time") {
        showOneTimeScholarshipModal(scholarshipOneTimeDiscount);
      } else if (selectedType === "overall") {
        showOverallScholarshipModal(scholarshipOverallDiscount);
      } else if (selectedType === "sem_wise") {
        showSemWiseScholarshipModal(scholarshipSemWiseDiscount);
      }
    });

    function showOneTimeScholarshipModal(scholarshipOneTimeDiscount) {
  // Build header for one-time fee total
  const headerHtml = `<p class="text-13">One-Time Fees Total: <strong>₹ ${oneTimeTotal}</strong></p>`;

  // Build the input area styled similar to the other modals
  const inputHtml = `<div style="margin-bottom:10px;">
      <label class="text-start d-block text-13 mb-2">Enter discount for one-time fees (e.g., 10% or 1000):</label>
      <div class="input-group">
        <input id="discountInput" type="text" class="form-control text-13 placeholder-13" placeholder="Enter discount" value="${scholarshipOneTimeDiscount || ""}" ${scholarshipOneTimeDiscount ? 'readonly' : ''}>
        ${scholarshipOneTimeDiscount ? `<button id="editButton" type="button" class="btn btn-outline-secondary text-13"><i class="fa-solid fa-pen-to-square"></i></button>` : ''}
      </div>
      <div id="errorDiscount" style="color:red; font-size:0.8em;"></div>
    </div>`;

  // Preview container: displays the discount review
  const previewHtml = `<div id="previewContainer" style="margin-top:10px;"></div>`;

  // Global confirmation checkbox
  const globalConfirmHtml = `<div id="globalConfirmContainer" class="form-check mt-3 p-4 rounded shadow-sm text-13 d-flex bg-light justify-content-end">
      <input class="form-check-input" type="checkbox" id="globalConfirmCheckbox">
      <label class="form-check-label mx-2" for="globalConfirmCheckbox">Confirm Save</label>
    </div>`;

  // Combine all parts in order: header, input, preview, then confirm checkbox
  const html = headerHtml + inputHtml + previewHtml + globalConfirmHtml;

  Swal.fire({
    title: '<span class="text-primary text-15">One-Time Scholarship</span>',
    allowHtml: true,
    html: html,
    showCancelButton: true,
    confirmButtonText: "Save",
    preConfirm: () => {
      const discountVal = document.getElementById('discountInput').value.trim();
      if (!discountVal) {
        Swal.showValidationMessage('Please enter a discount value.');
        return false;
      }
      // Validate percentage discount
      if (discountVal.endsWith('%')) {
        const perc = parseFloat(discountVal.slice(0, -1));
        if (isNaN(perc)) {
          Swal.showValidationMessage('Invalid percentage discount.');
          return false;
        }
        if (perc > 100) {
          Swal.showValidationMessage('Discount percentage cannot exceed 100%.');
          return false;
        }
      } else {
        // Validate fixed amount discount
        const amt = parseFloat(discountVal);
        if (isNaN(amt)) {
          Swal.showValidationMessage('Invalid discount amount.');
          return false;
        }
        if (amt > oneTimeTotal) {
          Swal.showValidationMessage('Discount amount cannot exceed the total fees.');
          return false;
        }
      }
      // Check if the global confirm checkbox is checked
      const globalConfirmCheckbox = document.getElementById('globalConfirmCheckbox');
      if (!globalConfirmCheckbox.checked) {
        Swal.showValidationMessage('Please confirm Save.');
        return false;
      }
      return discountVal;
    },
    didOpen: () => {
      const discountInput = document.getElementById('discountInput');
      discountInput.addEventListener('input', function() {
        const discountVal = this.value.trim();
        let preview = '';
        if (discountVal.endsWith('%')) {
          const perc = parseFloat(discountVal.slice(0, -1));
          if (!isNaN(perc)) {
            const discountAmount = oneTimeTotal * perc / 100;
            const discountedFee = oneTimeTotal - discountAmount;
            preview = `<p class="text-13"><del>₹ ${oneTimeTotal.toFixed(2)}</del> <span class="text-primary">-₹ ${discountAmount.toFixed(2)}</span> <span>= ₹ ${discountedFee.toFixed(2)}</span></p>`;
          }
        } else {
          const amt = parseFloat(discountVal);
          if (!isNaN(amt)) {
            const discountedFee = oneTimeTotal - amt;
            preview = `<p class="text-13"><del>₹ ${oneTimeTotal.toFixed(2)}</del> <span class="text-primary">-₹ ${amt.toFixed(2)}</span> <span>= ₹ ${discountedFee.toFixed(2)}</span></p>`;
          }
        }
        document.getElementById('previewContainer').innerHTML = preview;
      });

      // Toggle edit mode if the edit button exists
      const editButton = document.getElementById('editButton');
      if (editButton) {
        editButton.addEventListener('click', function() {
          if (discountInput.hasAttribute('readonly')) {
            discountInput.removeAttribute('readonly');
            editButton.innerHTML = `<i class="fa-solid fa-floppy-disk"></i>`;
            editButton.className = "btn btn-outline-success text-13";
          } else {
            discountInput.setAttribute('readonly', true);
            editButton.innerHTML = `<i class="fa-solid fa-pen-to-square"></i>`;
            editButton.className = "btn btn-outline-secondary text-13";
          }
        });
      }
    }
  }).then((result) => {
    if (result.isConfirmed) {
      const payload = {
        student_uid: currentPaymentParams.studentUid,
        institute_id: currentPaymentParams.instituteId,
        institute_name: currentPaymentParams.instituteName,
        course_id: currentPaymentParams.courseId,
        program_code: currentPaymentParams.programCode,
        program_name: currentPaymentParams.programName,
        intake_type: currentPaymentParams.intakeType,
        year: currentPaymentParams.year,
        fee_type: currentPaymentParams.feeType,
        one_time_discount: result.value,
        overall_discount: null,
        sem_wise_discount: null
      };
      addScholarship(payload);
    }
  });
}

function showOverallScholarshipModal(existingdata) {
  // Compute overall total and find the minimum fee component (one-time and each semester)
  
  let existing;
try {
  const existingRaw = JSON.parse(existingdata);
  if (existingRaw && Object.keys(existingRaw).length > 0) {
    existing = Object.values(existingRaw).reduce((acc, val) => acc + val, 0);
    existing = existing.toFixed(2)
  } else {
    existing = '';
  }
} catch (e) {
  existing = '';
}

//   console.log(existing)
  let overallTotal = oneTimeTotal;
  let minFee = oneTimeTotal;
  let tableRows = `
    <tr>
      <td>One-Time Fees</td>
      <td>₹ ${oneTimeTotal}</td>
    </tr>`;
  
  for (let sem in semesterTotals) {
    overallTotal += semesterTotals[sem];
    minFee = Math.min(minFee, semesterTotals[sem]);
    tableRows += `
      <tr>
        <td>Semester ${sem} Fees</td>
        <td>₹ ${semesterTotals[sem]}</td>
      </tr>`;
  }

  const tableHtml = `
    <table class="table table-bordered table-sm">
      <thead class="text-13">
        <tr>
          <th>Category</th>
          <th>Fee Total (₹)</th>
        </tr>
      </thead>
      <tbody class="text-13">
        ${tableRows}
        <tr class="fw-bold">
          <td>Total</td>
          <td>₹ ${overallTotal}</td>
        </tr>
      </tbody>
    </table>
    <p class="mb-2"><small>Note: The total scholarship cannot exceed ₹ ${overallTotal}.</small></p>
  `;

  // Scholarship input field
  let inputHtml = `<div style="margin-bottom:10px;">
      <label class="text-start d-block text-13 mb-2">Enter total scholarship amount (e.g., 10% or 5000):</label>
      <div class="input-group">
        <input id="discountInput" type="text" class="form-control text-13 placeholder-13" placeholder="Enter scholarship" value="${existing || ""}" ${existing ? 'readonly' : ''}>
        ${existing ? `<button id="editButton" type="button" class="btn btn-outline-secondary text-13"><i class="fa-solid fa-pen-to-square"></i></button>` : ''}
      </div>
      <div id="errorDiscount" style="color:red; font-size:0.8em;"></div>
    </div>`;

  // Modal content
  const html = `
    ${tableHtml}
    ${inputHtml}
    <div id="previewContainer" style="margin-top:10px;"></div>
    <div id="globalConfirmContainer" class="form-check mt-3 p-4 rounded shadow-sm text-13 d-flex bg-light justify-content-end">
      <input class="form-check-input" type="checkbox" id="globalConfirmCheckbox">
      <label class="form-check-label mx-2" for="globalConfirmCheckbox">Confirm Save</label>
    </div>
  `;

  Swal.fire({
    title: '<span class="text-primary text-15">Overall Scholarship</span>',
    allowHtml: true,
    html: html,
    showCancelButton: true,
    confirmButtonText: "Save",
    preConfirm: () => {
      const discountVal = document.getElementById('discountInput').value.trim();
      if (!discountVal) {
        Swal.showValidationMessage('Please enter a scholarship value.');
        return false;
      }

      let totalDiscount;
      if (discountVal.endsWith('%')) {
        const perc = parseFloat(discountVal.slice(0, -1));
        if (isNaN(perc) || perc > 100) {
          Swal.showValidationMessage('Invalid percentage discount. Cannot exceed 100%.');
          return false;
        }
        totalDiscount = (overallTotal * perc) / 100;
      } else {
        totalDiscount = parseFloat(discountVal);
        if (isNaN(totalDiscount) || totalDiscount > overallTotal) {
          Swal.showValidationMessage(`Fixed scholarship cannot exceed the total fee amount (₹ ${overallTotal}).`);
          return false;
        }
      }

      // Check confirmation checkbox
      const globalConfirmCheckbox = document.getElementById('globalConfirmCheckbox');
      if (!globalConfirmCheckbox.checked) {
        Swal.showValidationMessage('Please confirm Save.');
        return false;
      }

      return totalDiscount;
    },
    didOpen: () => {
      const discountInput = document.getElementById('discountInput');
      discountInput.addEventListener('input', function() {
        const discountVal = this.value.trim();
        let totalDiscount = 0;
        let preview = '';

        if (discountVal.endsWith('%')) {
          const perc = parseFloat(discountVal.slice(0, -1));
          if (!isNaN(perc)) {
            totalDiscount = (overallTotal * perc) / 100;
          }
        } else {
          totalDiscount = parseFloat(discountVal);
        }

        if (!isNaN(totalDiscount) && totalDiscount <= overallTotal) {
          // Distribute discount proportionally across all components
          const oneTimeShare = (totalDiscount * oneTimeTotal) / overallTotal;
          const oneTimeDiscounted = oneTimeTotal - oneTimeShare;
          preview += `<p class="text-13">One-Time Fees: <del>₹ ${oneTimeTotal.toFixed(2)}</del> <span class="text-primary">-₹ ${oneTimeShare.toFixed(2)}</span> <span>= ₹ ${oneTimeDiscounted.toFixed(2)}</span></p>`;
          
          for (let sem in semesterTotals) {
            const semTotal = semesterTotals[sem];
            const semShare = (totalDiscount * semTotal) / overallTotal;
            const semDiscounted = semTotal - semShare;
            preview += `<p class="text-13">Semester ${sem} Fees: <del>₹ ${semTotal.toFixed(2)}</del> <span class="text-primary">-₹ ${semShare.toFixed(2)}</span> <span>= ₹ ${semDiscounted.toFixed(2)}</span></p>`;
          }
          const overallAfterDiscount = overallTotal - totalDiscount;
          preview += `<p class="text-13 fw-bold">Overall Total after discount: ₹ ${overallAfterDiscount.toFixed(2)}</p>`;
        }

        document.getElementById('previewContainer').innerHTML = preview;
      });

      // Toggle edit mode
      const editButton = document.getElementById('editButton');
      if (editButton) {
        editButton.addEventListener('click', function() {
          if (discountInput.hasAttribute('readonly')) {
            discountInput.removeAttribute('readonly');
            editButton.innerHTML = `<i class="fa-solid fa-floppy-disk"></i>`;
            editButton.className = "btn btn-outline-success text-13";
          } else {
            discountInput.setAttribute('readonly', true);
            editButton.innerHTML = `<i class="fa-solid fa-pen-to-square"></i>`;
            editButton.className = "btn btn-outline-secondary text-13";
          }
        });
      }
    }
  }).then((result) => {
    if (result.isConfirmed) {
      const totalDiscount = result.value;
      let scholarshipDistribution = {
        one_time_discount: (totalDiscount * oneTimeTotal) / overallTotal
      };

      for (let sem in semesterTotals) {
        scholarshipDistribution[`${sem}`] = (totalDiscount * semesterTotals[sem]) / overallTotal;
      }

      const payload = {
        student_uid: currentPaymentParams.studentUid,
        institute_id: currentPaymentParams.instituteId,
        institute_name: currentPaymentParams.instituteName,
        course_id: currentPaymentParams.courseId,
        program_code: currentPaymentParams.programCode,
        program_name: currentPaymentParams.programName,
        intake_type: currentPaymentParams.intakeType,
        year: currentPaymentParams.year,
        fee_type: currentPaymentParams.feeType,
        overall_discount: JSON.stringify(scholarshipDistribution),
        one_time_discount: null,
        sem_wise_discount: null
      };

      addScholarship(payload);
    }
  });
}




    // Modal for Semester-Wise Scholarship (choose same vs. individual)
    function showSemWiseScholarshipModal(existing) {
  Swal.fire({
    title: '<span class="text-primary text-15">Semester-Wise Scholarship</span>',
    allowHtml: true,
    html: `
      <p class="text-13">Select how you want to apply the discount:</p>
      <select id="semOption" class="form-select text-13">
        <option value="all">Same discount for all semesters</option>
        <option value="individual">Different discount for each semester</option>
      </select>`,
    showCancelButton: true,
    preConfirm: () => document.getElementById('semOption').value
  }).then((result) => {
    if (result.isConfirmed) {
      if (result.value === 'all') {
        showSemWiseAllModal();
      } else {
        showSemWiseIndividualModal(existing);
      }
    }
  });
}


    // Modal for same discount across all semesters
    function showSemWiseAllModal() {
  // Calculate semester fees total and build table
  let tableRows = '';
  let minSemesterFee = Infinity;
  let totalSemesterFee = 0;

  for (let sem in semesterTotals) {
    const fee = semesterTotals[sem];
    tableRows += `<tr>
                    <td>Semester ${sem}</td>
                    <td>₹ ${fee}</td>
                  </tr>`;
    totalSemesterFee += fee;
    minSemesterFee = Math.min(minSemesterFee, fee);
  }

  const tableHtml = `
    <table class="table table-bordered table-sm">
      <thead class="text-13">
        <tr>
          <th>Semester</th>
          <th>Fee Total (₹)</th>
        </tr>
      </thead>
      <tbody class="text-13">
        ${tableRows}
        <tr class="fw-bold">
          <td>Total</td>
          <td>₹ ${totalSemesterFee}</td>
        </tr>
      </tbody>
    </table>
    <p class="mb-2"><small>Note: The total discount cannot exceed ₹ ${totalSemesterFee}.</small></p>
  `;

  // Check for existing uniform discount
  let existingDiscount = "";
  if (scholarshipSemWiseDiscount) {
    try {
      const parsed = JSON.parse(scholarshipSemWiseDiscount);
      const vals = Object.values(parsed).map(obj => obj.amount);
        console.log(vals)
        existingDiscount = vals.reduce((acc, cur) => acc + parseFloat(cur), 0);
        existingDiscount = existingDiscount.toFixed(2);
    } catch (e) {
      console.error("Error parsing sem_wise discount", e);
    }
  }

  // Scholarship input field
  let inputHtml = `<div style="margin-bottom:10px;">
      <label class="text-start d-block text-13 mb-2">Enter total scholarship amount (e.g., 5000 or 10%):</label>
      <div class="input-group">
        <input id="discountInput" type="text" class="form-control text-13 placeholder-13" placeholder="Enter scholarship" value="${existingDiscount}" ${existingDiscount ? 'readonly' : ''}>
        ${existingDiscount ? `<button id="editButton" type="button" class="btn btn-outline-secondary text-13"><i class="fa-solid fa-pen-to-square"></i></button>` : ''}  
      </div>
      <div id="errorDiscount" style="color:red; font-size:0.8em;"></div>
    </div>`;

  // Modal content
  const html = `
    ${tableHtml}
    ${inputHtml}
    <div id="previewContainer" style="margin-top:10px;"></div>
    <div id="globalConfirmContainer" class="form-check mt-3 p-4 rounded shadow-sm text-13 d-flex bg-light justify-content-end">
      <input class="form-check-input" type="checkbox" id="globalConfirmCheckbox">
      <label class="form-check-label mx-2" for="globalConfirmCheckbox">Confirm Save</label>
    </div>
  `;

  Swal.fire({
    title: '<span class="text-primary text-15">Semester-Wise Scholarship (All Semesters)</span>',
    allowHtml: true,
    html: html,
    showCancelButton: true,
    confirmButtonText: "Save",
    preConfirm: () => {
      const discountVal = document.getElementById('discountInput').value.trim();
      if (!discountVal) {
        Swal.showValidationMessage('Please enter a scholarship value.');
        return false;
      }
      
      let totalDiscount;
      if (discountVal.endsWith('%')) {
        const perc = parseFloat(discountVal.slice(0, -1));
        if (isNaN(perc) || perc > 100) {
          Swal.showValidationMessage('Invalid percentage discount. Cannot exceed 100%.');
          return false;
        }
        totalDiscount = (totalSemesterFee * perc) / 100;
      } else {
        totalDiscount = parseFloat(discountVal);
        if (isNaN(totalDiscount) || totalDiscount > totalSemesterFee) {
          Swal.showValidationMessage(`Fixed scholarship cannot exceed the total semester fee (₹ ${totalSemesterFee}).`);
          return false;
        }
      }

      // Check confirmation checkbox
      const globalConfirmCheckbox = document.getElementById('globalConfirmCheckbox');
      if (!globalConfirmCheckbox.checked) {
        Swal.showValidationMessage('Please confirm Save.');
        return false;
      }
      
      return totalDiscount;
    },
    didOpen: () => {
      const discountInput = document.getElementById('discountInput');
      discountInput.addEventListener('input', function() {
        const discountVal = this.value.trim();
        let totalDiscount = 0;
        let preview = '';

        if (discountVal.endsWith('%')) {
          const perc = parseFloat(discountVal.slice(0, -1));
          if (!isNaN(perc)) {
            totalDiscount = (totalSemesterFee * perc) / 100;
          }
        } else {
          totalDiscount = parseFloat(discountVal);
        }

        if (!isNaN(totalDiscount) && totalDiscount <= totalSemesterFee) {
          for (let sem in semesterTotals) {
            const semTotal = semesterTotals[sem];
            const discountShare = (totalDiscount * semTotal) / totalSemesterFee;
            const discountedFee = semTotal - discountShare;
            preview += `<p class="text-13">Semester ${sem}: <del>₹ ${semTotal.toFixed(2)}</del> <span class="text-primary">-₹ ${discountShare.toFixed(2)}</span> <span>= ₹ ${discountedFee.toFixed(2)}</span></p>`;
          }
          const overallAfterDiscount = totalSemesterFee - totalDiscount;
          preview += `<p class="text-13 fw-bold">Overall Total after discount: ₹ ${overallAfterDiscount.toFixed(2)}</p>`;

        }

        document.getElementById('previewContainer').innerHTML = preview;
      });

      // Toggle edit mode
      const editButton = document.getElementById('editButton');
      if (editButton) {
        editButton.addEventListener('click', function() {
          if (discountInput.hasAttribute('readonly')) {
            discountInput.removeAttribute('readonly');
            editButton.innerHTML = `<i class="fa-solid fa-floppy-disk"></i>`;
            editButton.className = "btn btn-outline-success text-13";
          } else {
            discountInput.setAttribute('readonly', true);
            editButton.innerHTML = `<i class="fa-solid fa-pen-to-square"></i>`;
            editButton.className = "btn btn-outline-secondary text-13";
          }
        });
      }
    }
  }).then((result) => {
    if (result.isConfirmed) {
      const totalDiscount = result.value;
      let semDiscount = {};

      for (let sem in semesterTotals) {
        const semTotal = semesterTotals[sem];
        const discountShare = (totalDiscount * semTotal) / totalSemesterFee;
        semDiscount[sem] = { amount: discountShare.toFixed(2) };
      }

      const payload = {
        student_uid: currentPaymentParams.studentUid,
        institute_id: currentPaymentParams.instituteId,
        institute_name: currentPaymentParams.instituteName,
        course_id: currentPaymentParams.courseId,
        program_code: currentPaymentParams.programCode,
        program_name: currentPaymentParams.programName,
        intake_type: currentPaymentParams.intakeType,
        year: currentPaymentParams.year,
        fee_type: currentPaymentParams.feeType,
        sem_wise_discount: JSON.stringify(semDiscount),
        overall_discount: null,
        one_time_discount: null
      };

      addScholarship(payload);
    }
  });
}


    // Modal for individual semester discounts
    function showSemWiseIndividualModal(existing) {
  let htmlContent = '<p class="text-13">Enter discount for each semester (leave blank if none):</p>';
  for (let sem in semesterTotals) {
    let existingVal = "";
    if (existing) {
      try {
        const parsed = JSON.parse(existing);
        if (parsed[sem] && parsed[sem].amount) {
          existingVal = parsed[sem].amount;
        }
      } catch(e) {
        console.error("Error parsing individual sem discount", e);
      }
    }
    htmlContent += `
      <div style="margin-bottom:10px;">
        <label class="text-start d-block text-13 mb-2">Semester ${sem} (Total: ₹ ${semesterTotals[sem]}):</label>
        <div class="input-group">
          <input id="discount_sem_${sem}" type="text" class="form-control text-13 placeholder-13" placeholder="e.g., 10% or 1000 (optional)" value="${existingVal}" ${existingVal ? 'readonly' : ''}>
          ${existingVal ? `<button id="editButton_${sem}" type="button" class="btn btn-outline-secondary text-13"><i class="fa-solid fa-pen-to-square"></i></button>` : ''}
        </div>
        <div id="error_sem_${sem}" style="color:red; font-size:0.8em;"></div>
        <div id="preview_sem_${sem}" style="margin-top:5px;"></div>
      </div>
    `;
  }
  
  // Global confirm edit checkbox (always visible)
  htmlContent += `
    <div id="globalConfirmEdit" class="form-check mt-3 p-4 rounded shadow-sm text-13 d-flex bg-light justify-content-end">
      <input class="form-check-input" type="checkbox" id="globalConfirmCheckbox">
      <label class="form-check-label mx-2" for="globalConfirmCheckbox">Confirm Save</label>
    </div>
  `;
  
  Swal.fire({
    title: '<span class="text-primary text-15">Semester-Wise Scholarship (Individual)</span>',
    allowHtml: true,
    html: htmlContent,
    showCancelButton: true,
    confirmButtonText: "Save",
    preConfirm: () => {
      let semDiscount = {};
      let isValid = true;
      // Validate each semester input
      for (let sem in semesterTotals) {
        const inputVal = document.getElementById(`discount_sem_${sem}`).value.trim();
        const errorEl = document.getElementById(`error_sem_${sem}`);
        errorEl.innerText = ""; // Clear previous errors

        // Global confirm checkbox must be checked even if no value is entered
        const globalConfirmCheckbox = document.getElementById('globalConfirmCheckbox');
        if (!globalConfirmCheckbox.checked) {
          errorEl.innerText = "Please confirm Save.";
          isValid = false;
        }

        if (inputVal !== "") {
          if (inputVal.endsWith('%')) {
            const perc = parseFloat(inputVal.slice(0, -1));
            if (isNaN(perc)) {
              errorEl.innerText = "Invalid percentage.";
              isValid = false;
            } else if (perc > 100) {
              errorEl.innerText = "Percentage cannot exceed 100%.";
              isValid = false;
            } else {
              semDiscount[sem] = { amount: inputVal };
            }
          } else {
            const amt = parseFloat(inputVal);
            if (isNaN(amt)) {
              errorEl.innerText = "Invalid amount.";
              isValid = false;
            } else if (amt > semesterTotals[sem]) {
              errorEl.innerText = `Amount cannot exceed ₹ ${semesterTotals[sem]}.`;
              isValid = false;
            } else {
              semDiscount[sem] = { amount: inputVal };
            }
          }
        }
      }
      if (!isValid) {
        Swal.showValidationMessage('Please fix the errors in the discount values.');
        return false;
      }
      return semDiscount;
    },
    didOpen: () => {
      // Function to update preview for a specific semester
      function updatePreview(sem) {
        const semTotal = semesterTotals[sem];
        const inputVal = document.getElementById(`discount_sem_${sem}`).value.trim();
        let preview = '';
        if (inputVal !== "") {
          if (inputVal.endsWith('%')) {
            const perc = parseFloat(inputVal.slice(0, -1));
            if (!isNaN(perc)) {
              const discountAmount = semTotal * perc / 100;
              const discountedFee = semTotal - discountAmount;
              preview = `<p class="text-13"><del>₹ ${semTotal.toFixed(2)}</del> <span class="text-primary">-₹ ${discountAmount.toFixed(2)}</span> <span>= ₹ ${discountedFee.toFixed(2)}</span></p>`;
            }
          } else {
            const amt = parseFloat(inputVal);
            if (!isNaN(amt)) {
              const discountedFee = semTotal - amt;
              preview = `<p class="text-13"><del>₹ ${semTotal.toFixed(2)}</del> <span class="text-primary">-₹ ${amt.toFixed(2)}</span> <span>= ₹ ${discountedFee.toFixed(2)}</span></p>`;
            }
          }
        }
        document.getElementById(`preview_sem_${sem}`).innerHTML = preview;
      }
      
      // Attach event listeners for each semester input
      for (let sem in semesterTotals) {
        const inputEl = document.getElementById(`discount_sem_${sem}`);
        inputEl.addEventListener('input', function() {
          updatePreview(sem);
        });
        inputEl.addEventListener('change', function() {
          updatePreview(sem);
          const val = this.value.trim();
          const errorEl = document.getElementById(`error_sem_${sem}`);
          errorEl.innerText = ""; // Clear previous errors
          if (val !== "") {
            if (val.endsWith('%')) {
              const perc = parseFloat(val.slice(0, -1));
              if (isNaN(perc)) {
                errorEl.innerText = "Invalid percentage.";
              } else if (perc > 100) {
                errorEl.innerText = "Percentage cannot exceed 100%.";
              }
            } else {
              const amt = parseFloat(val);
              if (isNaN(amt)) {
                errorEl.innerText = "Invalid amount.";
              } else if (amt > semesterTotals[sem]) {
                errorEl.innerText = `Amount cannot exceed ₹ ${semesterTotals[sem]}.`;
              }
            }
          }
        });
        
        // Attach edit button functionality if it exists
        const editButton = document.getElementById(`editButton_${sem}`);
        if (editButton) {
          editButton.addEventListener('click', function() {
            if (inputEl.hasAttribute('readonly')) {
              inputEl.removeAttribute('readonly');
              editButton.innerHTML = `<i class="fa-solid fa-floppy-disk"></i>`;
              editButton.className = "btn btn-outline-success text-13";
            } else {
              inputEl.setAttribute('readonly', true);
              editButton.innerHTML = `<i class="fa-solid fa-pen-to-square"></i>`;
              editButton.className = "btn btn-outline-secondary text-13";
            }
          });
        }
        // Initial preview update for each semester
        updatePreview(sem);
      }
    }
  }).then((result) => {
    if (result.isConfirmed) {
      const payload = {
        student_uid: currentPaymentParams.studentUid,
        institute_id: currentPaymentParams.instituteId,
        institute_name: currentPaymentParams.instituteName,
        course_id: currentPaymentParams.courseId,
        program_code: currentPaymentParams.programCode,
        program_name: currentPaymentParams.programName,
        intake_type: currentPaymentParams.intakeType,
        year: currentPaymentParams.year,
        fee_type: currentPaymentParams.feeType,
        sem_wise_discount: JSON.stringify(result.value),
        overall_discount: null,
        one_time_discount: null
      };
      addScholarship(payload);
    }
  });
}

    // API call to add a scholarship
    function addScholarship(payload) {
      fetch('/api/scholarship', {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Accept": "application/json",
          "Authorization": token,
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
        },
        body: JSON.stringify(payload)
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === "success") {
          Swal.fire("Success", data.message, "success");
          refreshPaymentDetails()
        } else {
          Swal.fire("Error", data.message, "error");
        }
      })
      .catch(error => {
        console.error("Error adding scholarship:", error);
        Swal.fire("Error", "An error occurred while adding scholarship", "error");
      });
    }

    // When "Pay Fees" button is clicked.
    function FeesStructure(studentName, instituteId, instituteName, courseId, programCode, programName, intakeType, year, feeType, studentEmail,uid) {
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
          populateScholarshipDropdown(feeRecord, instituteId, instituteName, courseId, programCode, programName, intakeType, year, feeType, studentName, uid, uid);
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

    // Populate payment details and calculate fee totals.
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
  studentEmail,
  uid,
  scholarshipOverallDiscount,
  scholarshipOneTimeDiscount,
  scholarshipSemWiseDiscount
) {
  console.log(uid)
//   console.log(scholarshipOverallDiscount);
//   console.log(scholarshipOneTimeDiscount);
//   try {
//     console.log(JSON.parse(scholarshipSemWiseDiscount));
//   } catch (e) {
//     console.error("Error parsing semester-wise discount:", e);
//   }

  // Helper function: given a total and a discount string, returns discount amount and final total.

  let paidFeesData = null;
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
        student_uid: uid,
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
      paidFeesData = data.data;
    } else {
      console.error("API returned error:", data.message);
      paidFeesData = data.data;
    }
  } catch (error) {
    console.error("Error during fetch operation:", error);
  }

  console.log(paidFeesData)
  let totalOneTimePaid = 0;
if (paidFeesData && paidFeesData.one_time_fees) {
  try {
    const oneTimeObj = JSON.parse(paidFeesData.one_time_fees);
    for (let key in oneTimeObj) {
      if (oneTimeObj[key].amount) {
        totalOneTimePaid += parseFloat(oneTimeObj[key].amount) || 0;
      }
    }
  } catch (e) {
    console.error("Error parsing one_time_fees:", e);
  }
}
console.log("Total One-Time Paid Fees:", totalOneTimePaid);

// Extract semester-wise paid fees into an object:
let semesterWisePaid = {};
if (paidFeesData && paidFeesData.semester_fees) {
  try {
    const semFeesObj = JSON.parse(paidFeesData.semester_fees);
    // semFeesObj is expected to be an object with keys as semester numbers.
    for (let sem in semFeesObj) {
      let totalForSem = 0;
      const paymentsObj = semFeesObj[sem];
      // Each semester might have multiple payment entries (e.g. keys "1", "2", etc.)
      for (let key in paymentsObj) {
        if (paymentsObj[key].fee_details && paymentsObj[key].fee_details.amount) {
          totalForSem += parseFloat(paymentsObj[key].fee_details.amount) || 0;
        }
      }
      semesterWisePaid[sem] = totalForSem;
    }
  } catch (e) {
    console.error("Error parsing semester_fees:", e);
  }
}
console.log("Semester-Wise Paid Fees:", semesterWisePaid);

let totalPaidFees = totalOneTimePaid;
for (let sem in semesterWisePaid) {
  totalPaidFees += semesterWisePaid[sem];
}
console.log("Total Paid Fees:", totalPaidFees);


  const computeDiscount = (total, discountStr) => {
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
      if (discountAmount > total) discountAmount = total;
    }
    return { discountAmount, finalTotal: total - discountAmount };
  };

  // Parse fee details
  let oneTimeFees = {};
  let rawOnetimeTotal = 0;
  let oneTimeDiscountApplied = 0;
  let semesterFees = {};
  let semDiscountApplied = 0;
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

  let html = "";
  oneTimeTotal = 0;

  // Payment header
  document.getElementById('payment_details_head').innerHTML = `${studentName} | ${programName} | ${intakeType} | ${feeType}`;

  // One-Time Fees Section
  html += '<h6 class="mb-2 text-14"><i class="fa-solid fa-receipt me-1"></i> One-Time Fees</h6>';
  if (Object.keys(oneTimeFees).length > 0) {
    html += '<ul class="list-group mb-3 text-13">';
    for (let head in oneTimeFees) {
      let amount = parseFloat(oneTimeFees[head]) || 0;
      oneTimeTotal += amount;
      rawOnetimeTotal += amount;
      html += `<li class="list-group-item d-flex justify-content-between align-items-center">
                 <span>${head}</span>
                 <span class="badge bg-primary">₹ ${amount}</span>
               </li>`;
    }
    html += '</ul>';
  } else {
    html += '<p class="text-muted text-13">No one-time fees.</p>';
  }

  // Compute one-time fees discount
  
  if(totalOneTimePaid){
    oneTimeTotal = oneTimeTotal - totalOneTimePaid
  }
  let finalOneTime = oneTimeTotal;
  // If overall discount exists, apply it to one-time fees;
  // otherwise, use individual one-time discount if available.
  if (scholarshipOverallDiscount && scholarshipOverallDiscount.trim() !== "") {
    let overAllDiscount= JSON.parse(scholarshipOverallDiscount)
    // console.log(overAllDiscount);
    const discountValue = (overAllDiscount.one_time_discount).toString();
    const result = computeDiscount(oneTimeTotal, discountValue);
    oneTimeDiscountApplied = result.discountAmount;
    finalOneTime = result.finalTotal;
  } else if (scholarshipOneTimeDiscount && scholarshipOneTimeDiscount.trim() !== "") {
    // sonesole.log(scholarshipOneTimeDiscount)
    const result = computeDiscount(oneTimeTotal, scholarshipOneTimeDiscount);
    oneTimeDiscountApplied = result.discountAmount;
    finalOneTime = result.finalTotal;
  }
  html += `<div class="d-flex flex-column align-items-end mb-3 text-13">
  ${oneTimeDiscountApplied ? `
    ${totalOneTimePaid ? 
  `<div>
      <span class="text-13">
        Final One-Time Fees: 
        <del>₹ ${rawOnetimeTotal.toFixed(2)}</del>
        &nbsp;<span class="text-primary">Paid: ₹ ${totalOneTimePaid.toFixed(2)}</span>
        &nbsp;= ₹ ${oneTimeTotal.toFixed(2)}
      </span>
    </div>`
  :``}
    <div>
      <span>
        Remaining One-Time Fees: 
        <del>₹ ${oneTimeTotal.toFixed(2)}</del>
        &nbsp;<span class="text-primary">Discount: ₹ ${oneTimeDiscountApplied.toFixed(2)}</span>
        &nbsp;= ${ parseFloat(finalOneTime.toFixed(2)) > 0 
                      ? `₹ ${finalOneTime.toFixed(2)}` 
                      : `<span class="text-success"><i class="fa-solid fa-check"></i> Paid</span>` }
      </span>
    </div>
  ` : `
    <div>
      ${totalOneTimePaid ?
      `<span class=" text-13">
        Final One-Time Fees: 
        <del>₹ ${rawOnetimeTotal.toFixed(2)}</del>
        &nbsp;<span class="text-primary">Paid : ₹ ${totalOneTimePaid.toFixed(2)}</span>
        &nbsp;= ₹ ${oneTimeTotal.toFixed(2)}
      </span>`:
      `<span class="fw-bold text-13">
        Total One-Time Fees: ₹ ${oneTimeTotal.toFixed(2)}
      </span>`
      }
    </div>
  `}
</div>`;

  // Semester-Wise Fees Section
  let semesters = new Set();
  for (let feeHead in semesterFees) {
    let semObj = semesterFees[feeHead];
    Object.keys(semObj).forEach(sem => semesters.add(sem));
  }
  for (let sem in otherFees) {
    semesters.add(sem);
  }
  semesters = Array.from(semesters).sort();
  let overallMergedTotal = 0;
  let overallSemDiscount = 0;
  let mergedHtml = "";
  semesterTotals = {}; // reset

  // Parse semester-wise discount JSON if provided (for individual discounts)
  let semDiscountObj = {};
  if (scholarshipSemWiseDiscount && scholarshipSemWiseDiscount.trim() !== "") {
    try {
      semDiscountObj = JSON.parse(scholarshipSemWiseDiscount);
    } catch (e) {
      console.error("Error parsing semester-wise discount:", e);
    }
  }

  if (semesters.length > 0) {
    mergedHtml += `<div class="accordion" id="mergedSemesterAccordion">`;
    semesters.forEach((sem, index) => {
      let semTotal = 0;
      let feeListHtml = '<ul class="list-group text-13">';
      // Regular semester fees
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
      // Other fees for this semester
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
      overallMergedTotal += semTotal;
      semesterTotals[sem] = semTotal;

      // Apply discount to the semester:
      
      let rawsemTotal = 0 ;
      if(semesterWisePaid[sem]){
        rawsemTotal = semesterTotals[sem];
        semTotal = semTotal - semesterWisePaid[sem];
        semesterTotals[sem] = semTotal
      }
      console.log()
      let finalSemTotal = semTotal;
      // If overall discount exists, apply it here; else check for individual semester discount.
      if (scholarshipOverallDiscount && scholarshipOverallDiscount.trim() !== "") {
        let overAllDiscount = JSON.parse(scholarshipOverallDiscount);
        const discountValue = overAllDiscount[sem].toString();
        // Use semTotal here instead of oneTimeTotal
        const result = computeDiscount(semTotal, discountValue);
        semDiscountApplied = result.discountAmount;
        finalSemTotal = result.finalTotal;
        } else if (semDiscountObj[sem] && semDiscountObj[sem].amount && semDiscountObj[sem].amount.trim() !== "") {
        const result = computeDiscount(semTotal, semDiscountObj[sem].amount);
        semDiscountApplied = result.discountAmount;
        finalSemTotal = result.finalTotal;
        }

      overallSemDiscount += semDiscountApplied;

      feeListHtml += `<li class="list-group-item">
                        <div class="d-flex flex-column align-items-end mb-3 text-13">
                          ${semDiscountApplied ?
                          `
                          ${semesterWisePaid[sem] ? 
                        ` <div>
                              <span class="text-13">Total Semester ${sem}: 
                                <del>₹ ${rawsemTotal.toFixed(2)}</del>
                                &nbsp;<span class="text-primary">Paid: ₹ ${semesterWisePaid[sem] ? semesterWisePaid[sem].toFixed(2) : ''}</span>
                                &nbsp;= ₹ ${semTotal}
                              </span>
                            </div>`:
                        ``} 
                            <div>
                              <span>
                                Remaining One-Time Fees: 
                                <del>₹ ${semTotal.toFixed(2)}</del>
                                &nbsp;<span class="text-primary">Discount: ₹ ${semDiscountApplied.toFixed(2)}</span>
                                &nbsp;= ${ parseFloat(finalSemTotal.toFixed(2)) > 0 
                                          ? `₹ ${finalSemTotal.toFixed(2)}` 
                                          : `<span class="text-success"><i class="fa-solid fa-check"></i> Paid</span>` }
                               
                              </span>
                            </div>
                            `:
                          `
                           ${semesterWisePaid[sem] ?
                            `<span class="text-13">Total Semester ${sem}: 
                                <del>₹ ${rawsemTotal.toFixed(2)}</del>
                                &nbsp;<span class="text-primary">Paid: ₹ ${semesterWisePaid[sem] ? semesterWisePaid[sem].toFixed(2) : ''}</span>
                                &nbsp;= ₹ ${finalSemTotal}
                              </span>`:
                            `<span class="text-13">
                              Total Semester ${sem}: ₹ ${finalSemTotal.toFixed(2)}
                              </span>`
                            }
                          `}
                        </div>
                      </li>`;
      feeListHtml += '</ul>';

      mergedHtml += `<div class="accordion-item">
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
                      </div>`;
    });
    mergedHtml += `</div>`;
  } else {
    mergedHtml = '<p class="text-muted text-13">No semester-wise fees.</p>';
  }
  html += '<h6 class="mb-2 text-14"><i class="fa-solid fa-calendar-days me-1"></i> Semester-Wise Fees</h6>' + mergedHtml;

  // Compute overall totals
  const overallTotal = rawOnetimeTotal + overallMergedTotal;
  // console.log(semDiscountApplied)
  const totalDiscountApplied = oneTimeDiscountApplied + overallSemDiscount;
  console.log(totalPaidFees)
const finalGrandTotal = overallTotal - (totalDiscountApplied + totalPaidFees);


  // Display overall total and discount summary
  html += `${(totalDiscountApplied != 0) ?
              `<div class="d-flex flex-column align-items-end mt-4">
                <p class="text-13">Overall Total Fees: <del>₹ ${overallTotal.toFixed(2)}</del></p>
                <p class="text-13">Total Discount: <span class="text-primary">-₹ ${totalDiscountApplied.toFixed(2)}</span></p>
                <p class="text-13">Total Paid Fees: <span class="text-primary">-₹ ${totalPaidFees.toFixed(2)}</span></p>
                <p class="fw-bold text-13">Grand Total After Discount: ₹ ${finalGrandTotal.toFixed(2)}</p>
              </div>` 
              :
              `<div class="d-flex flex-column align-items-end mt-4">
                <p class="fw-bold text-13">Overall Total Fees: ₹ ${overallTotal.toFixed(2)}</p>
              </div>`
              }`;

  const paymentDetailsEl = document.getElementById('paymentDetails');
  if (paymentDetailsEl) {
    paymentDetailsEl.classList.remove('d-none');
    paymentDetailsEl.innerHTML = html;
  }
}

    
    // Refresh payment details (with a rotate animation)
    function refreshPaymentDetails() {
      const refreshIcon = document.querySelector('span[onclick="refreshPaymentDetails()"]');
      if (refreshIcon) {
        refreshIcon.classList.add('rotate');
        setTimeout(() => { refreshIcon.classList.remove('rotate'); }, 1000);
      }
      if (currentPaymentParams) {
        populateScholarshipDropdown(
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
          currentPaymentParams.studentEmail,
          currentPaymentParams.studentUid
        );
      }
    }

    // Close the payment interface
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
      // Clear scholarship inputs and reset global scholarship variables
      scholarshipOverallDiscount = null;
      scholarshipOneTimeDiscount = null;
      scholarshipSemWiseDiscount = null;
      const scholarshipSelect = document.getElementById('scholarshipTypeSelect');
      if (scholarshipSelect) {
        scholarshipSelect.value = "";
      }
      // Optionally, clear any scholarship-related inputs within the payment interface
      const paymentInterface = document.getElementById('paymentInterface');
      const inputs = paymentInterface.querySelectorAll('input, select, textarea');
      inputs.forEach(input => {
        if (input.type === 'checkbox' || input.type === 'radio') {
          input.checked = false;
        } else {
          input.value = '';
        }
      });
      
      // Hide the payment interface and show the student accordion
      document.getElementById('paymentInterface').classList.add('d-none');
      document.getElementById('accordionContainer').classList.remove('d-none');
    }
  });
}

  </script>
</body>
</html>
