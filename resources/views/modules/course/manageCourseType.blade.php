<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Course Type Management</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pap6Yey8o7Am3xqJ+OcPplznlyJjCQQ1XbJ3D9D0AcvUJChzjUMvUOxGx6M0+gUv5jqsmzUJl1PSZJW7KZf+3w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Custom CSS (if any) -->
  <link rel="stylesheet" href="{{ asset('css/admin/addfees.css') }}">
</head>
<body>
      <div class="container mt-4">
        
        <!-- Add Course Type Form (Hidden by Default) -->
        <div id="addFormContainer" class="mb-4 d-none">
          <!-- Header / Navigation -->
          <p class="mb-4 text-secondary text-14">
            <i class="fa-solid fa-angle-right"></i>
            <span class="text-primary admin_add_Courses_text">Add Course Type</span>
          </p>
          <form id="addCourseTypeForm" class="bg-white p-4 rounded position-relative">
            <!-- Close Button -->
            <button type="button" class="btn btn-danger text-13 position-absolute top-0 end-0 m-3" onclick="hideAddCourseTypeForm()">
              <i class="fa-solid fa-xmark"></i>
            </button>
            @csrf
            <div class="row g-3">
              <div class="col-md-12">
                <label for="course_type" class="form-label text-13">Course Type <span class="text-danger">*</span></label>
                <input type="text" id="course_type" class="form-control placeholder-14 text-13" placeholder="Enter course type" required>
              </div>
              <div class="col-md-12">
                <label class="form-label text-13">Required Qualification:</label>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="required_qualification_x" value="yes">
                  <label class="form-check-label text-13" for="required_qualification_x">X</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="required_qualification_xii" value="yes">
                  <label class="form-check-label text-13" for="required_qualification_xii">XII</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="required_qualification_college" value="yes">
                  <label class="form-check-label text-13" for="required_qualification_college">College</label>
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-outline-primary text-13 mt-4" id="addSubmitBtn">
              <span id="buttonText">Add Course Type</span>
              <span id="buttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
          </form>
        </div>

        <!-- Edit Course Type Form (Hidden by default) -->
        <div id="editFormContainer" class="mb-4 d-none">
          <!-- Header / Navigation -->
          <p class="mb-4 text-secondary text-14">
            <i class="fa-solid fa-angle-right"></i>
            <span class="text-primary admin_add_Courses_text">Edit Course Type</span>
          </p>
          <form id="editCourseTypeForm" class="bg-white p-4 rounded">
            @csrf
            <!-- Hidden Field for Course Type ID -->
            <input type="hidden" id="editCourseTypeId">
            <div class="row g-3">
              <div class="col-md-12">
                <label for="edit_course_type" class="form-label text-13">Course Type <span class="text-danger">*</span></label>
                <input type="text" id="edit_course_type" class="form-control placeholder-14 text-13" placeholder="Enter course type" required>
              </div>
              <div class="col-md-12">
                <label class="form-label text-13">Required Qualification:</label>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="edit_required_qualification_x" value="yes">
                  <label class="form-check-label text-13" for="edit_required_qualification_x">X</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="edit_required_qualification_xii" value="yes">
                  <label class="form-check-label text-13" for="edit_required_qualification_xii">XII</label>
                </div>
                <div class="form-check">
                  <input type="checkbox" class="form-check-input" id="edit_required_qualification_college" value="yes">
                  <label class="form-check-label text-13" for="edit_required_qualification_college">College</label>
                </div>
              </div>
            </div>
            <div class="mt-4">
              <button type="submit" class="btn btn-outline-primary text-13" id="editSubmitBtn">
                <span id="editButtonText">Update Course Type</span>
                <span id="editButtonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
              </button>
              <button type="button" id="cancelEditBtn" class="btn btn-outline-danger text-13 ms-2">Cancel</button>
            </div>
          </form>
        </div>

        <!-- View Course Types Table -->
        <p class="mb-4 text-secondary text-14">
          <i class="fa-solid fa-angle-right"></i>
          <span class="text-primary">All Course Types</span>
        </p>
        <div class="bg-white p-4 rounded">
          <!-- Search Bar and Show Add Form Button -->
          <div class="row mb-3 align-items-center justify-content-between">
            <div class="col-md-6 position-relative">
              <input type="text" id="searchCourseType" class="form-control placeholder-14 text-13 ps-5" placeholder="Search by Course Type or Qualification">
              <i class="fa-solid fa-search position-absolute text-secondary" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
            </div>
            <div class="col-md-6 text-end">
              <button id="showAddCourseTypeForm" class="btn btn-outline-primary text-13"><i class="fa-solid fa-plus"></i> Course Type</button>
            </div>
          </div>
          <div class="table-responsive">
            <table id="courseTypesTable" class="table table-striped table-hover text-center">
              <thead>
                <tr>
                  <th class="text-secondary text-13">Course Type</th>
                  <th class="text-secondary text-13">Qualification X</th>
                  <th class="text-secondary text-13">Qualification XII</th>
                  <th class="text-secondary text-13">Qualification College</th>
                  <th class="text-secondary text-13">Status</th>
                  <th class="text-secondary text-13">Created At</th>
                  <th class="text-secondary text-13">Actions</th>
                </tr>                
              </thead>
              <tbody>
                <!-- Dynamic rows will be inserted here -->
              </tbody>
            </table>
          </div>
        </div>
      </div><!-- /.container -->

  <!-- JavaScript Section -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener("DOMContentLoaded", function() {
    if (!sessionStorage.getItem("token")) {
      // Redirect to blank path or your preferred path if token is missing.
      window.location.href = "/";
    }
  });
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const token = sessionStorage.getItem('token');


    // --- SHOW / HIDE ADD FORM FUNCTIONS ---
    document.getElementById('showAddCourseTypeForm').addEventListener('click', function () {
      // Hide the edit form and reset it (if open)
      document.getElementById('editFormContainer').classList.add('d-none');
      document.getElementById('editCourseTypeForm').reset();
      // Show the add form container
      document.getElementById('addFormContainer').classList.remove('d-none');
      // Optionally scroll to top so the form is visible
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    function hideAddCourseTypeForm() {
      document.getElementById('addFormContainer').classList.add('d-none');
    }

    // --- FETCH & DISPLAY COURSE TYPES ---
    function fetchCourseTypes() {
      fetch('/api/view-course-type', {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Authorization': token
        }
      })
      .then(response => response.json())
      .then(data => {
        const tbody = document.querySelector('#courseTypesTable tbody');
        if(data.status === 'success') {
          if(data.data.length === 0) {
            tbody.innerHTML = `<tr><td class="bg-white" colspan="100%"><img src="{{ asset('assets/web_assets/noData.png') }}" alt="No Data"></td></tr>`;
          } else {
            tbody.innerHTML = '';
            data.data.forEach(course => {
              // Use course.id.$oid if available
              const courseId = course.id.$oid || course.id;
              const tr = document.createElement('tr');
              tr.innerHTML = `
                  <td class="text-13 text-center align-middle">${course.course_type}</td>
                  <td class="text-13 text-center align-middle">${course.required_qualification_x ? course.required_qualification_x : 'No'}</td>
                  <td class="text-13 text-center align-middle">${course.required_qualification_xii ? course.required_qualification_xii : 'No'}</td>
                  <td class="text-13 text-center align-middle">${course.required_qualification_college ? course.required_qualification_college : 'No'}</td>
                  <td class="text-13 text-center align-middle ${course.status === 'Active' ? 'text-success' : 'text-danger'}">${course.status}</td>
                  <td class="text-13 text-center align-middle">${course.created_at}</td>
                  <td class="text-13 text-center align-middle">
                    <button class="btn btn-sm btn-outline-secondary text-13 m-1 edit-btn" 
                            data-id="${courseId}"
                            data-course-type="${course.course_type}"
                            data-reqx="${course.required_qualification_x || ''}"
                            data-reqxii="${course.required_qualification_xii || ''}"
                            data-reqcollege="${course.required_qualification_college || ''}">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </button>
                    <button class="btn btn-sm text-13 m-1 ${course.status === 'Active' ? 'btn-outline-danger' : 'btn-outline-success'} toggle-btn" 
                            data-id="${courseId}" 
                            data-status="${course.status}">
                      <i class="fa-solid ${course.status === 'Active' ? 'fa-ban' : 'fa-power-off'}"></i>
                    </button>
                  </td>
              `;

              tbody.appendChild(tr);
            });
          }
          // Add event listeners for Edit and Toggle buttons
          document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
              // Retrieve data from button attributes
              const id = this.getAttribute('data-id');
              const courseType = this.getAttribute('data-course-type');
              const reqX = this.getAttribute('data-reqx');      // expected "yes" or empty
              const reqXII = this.getAttribute('data-reqxii');    // expected "yes" or empty
              const reqCollege = this.getAttribute('data-reqcollege'); // expected "yes" or empty

              // Call the openEditForm function with the data
              openEditForm(id, courseType, reqX, reqXII, reqCollege);
            });
          });
          document.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
              const id = this.getAttribute('data-id');
              const currentStatus = this.getAttribute('data-status');
              confirmToggle(id, currentStatus);
            });
          });
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      })
      .catch(err => {
        console.error(err);
        Swal.fire('Error', 'An error occurred while fetching course types.', 'error');
      });
    }

    // Call fetchCourseTypes on page load
    document.addEventListener('DOMContentLoaded', fetchCourseTypes);

    // --- ADD COURSE TYPE FORM SUBMISSION ---
    document.getElementById('addCourseTypeForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const addSubmitBtn = document.getElementById('addSubmitBtn');
      const buttonText = document.getElementById('buttonText');
      const buttonSpinner = document.getElementById('buttonSpinner');

      // Disable button and show spinner
      addSubmitBtn.disabled = true;
      buttonText.classList.add('d-none');
      buttonSpinner.classList.remove('d-none');

      const courseType = document.getElementById('course_type').value.trim();
      const reqX = document.getElementById('required_qualification_x').checked ? "yes" : "";
      const reqXII = document.getElementById('required_qualification_xii').checked ? "yes" : "";
      const reqCollege = document.getElementById('required_qualification_college').checked ? "yes" : "";
      const payload = {
        course_type: courseType.toUpperCase(),
        required_qualification_x: reqX,
        required_qualification_xii: reqXII,
        required_qualification_college: reqCollege
      };

      fetch('/api/add-course-type', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Authorization': token

        },
        body: JSON.stringify(payload)
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          Swal.fire('Success', data.message, 'success');
          document.getElementById('addCourseTypeForm').reset();
          hideAddCourseTypeForm()
          fetchCourseTypes();
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      })
      .catch(err => {
        console.error(err);
        Swal.fire('Error', 'An error occurred while adding course type.', 'error');
      })
      .finally(() => {
        // Re-enable button and hide spinner
        addSubmitBtn.disabled = false;
        buttonText.classList.remove('d-none');
        buttonSpinner.classList.add('d-none');
      });
    });

    // --- OPEN EDIT FORM (Using Data Attributes) ---
    function openEditForm(id, courseType, reqX, reqXII, reqCollege) {
      // Populate the edit form with the provided values
      document.getElementById('editCourseTypeId').value = id;
      document.getElementById('edit_course_type').value = courseType;
      document.getElementById('edit_required_qualification_x').checked = reqX === 'yes';
      document.getElementById('edit_required_qualification_xii').checked = reqXII === 'yes';
      document.getElementById('edit_required_qualification_college').checked = reqCollege === 'yes';

      // Hide the add form and show the edit form container
      document.getElementById('addFormContainer').classList.add('d-none');
      document.getElementById('editFormContainer').classList.remove('d-none');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // --- CANCEL EDIT ---
    document.getElementById('cancelEditBtn').addEventListener('click', function() {
      // Hide edit form and show add form container
      document.getElementById('editFormContainer').classList.add('d-none');
      document.getElementById('editCourseTypeForm').reset();
    });

    // --- EDIT COURSE TYPE FORM SUBMISSION ---
    document.getElementById('editCourseTypeForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const editSubmitBtn = document.getElementById('editSubmitBtn');
      const editButtonText = document.getElementById('editButtonText');
      const editButtonSpinner = document.getElementById('editButtonSpinner');

      // Disable button and show spinner
      editSubmitBtn.disabled = true;
      editButtonText.classList.add('d-none');
      editButtonSpinner.classList.remove('d-none');

      const id = document.getElementById('editCourseTypeId').value;
      const courseType = document.getElementById('edit_course_type').value.trim();
      const reqX = document.getElementById('edit_required_qualification_x').checked ? "yes" : "";
      const reqXII = document.getElementById('edit_required_qualification_xii').checked ? "yes" : "";
      const reqCollege = document.getElementById('edit_required_qualification_college').checked ? "yes" : "";
      const payload = {
        course_type: courseType,
        required_qualification_x: reqX,
        required_qualification_xii: reqXII,
        required_qualification_college: reqCollege
      };

      fetch('/api/edit-course-type/' + id, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Authorization': token

        },
        body: JSON.stringify(payload)
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          Swal.fire('Success', data.message, 'success');
          // After successful edit, hide edit form and reset it
          document.getElementById('editFormContainer').classList.add('d-none');
          document.getElementById('editCourseTypeForm').reset();
          fetchCourseTypes();
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      })
      .catch(err => {
        console.error(err);
        Swal.fire('Error', 'An error occurred while updating course type.', 'error');
      })
      .finally(() => {
        // Re-enable button and hide spinner
        editSubmitBtn.disabled = false;
        editButtonText.classList.remove('d-none');
        editButtonSpinner.classList.add('d-none');
      });
    });

    // --- TOGGLE COURSE TYPE STATUS ---
    function confirmToggle(id, currentStatus) {
      const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';
      Swal.fire({
        title: 'Change Course Type Status',
        text: `Are you sure you want to change the status to ${newStatus}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, change it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          toggleCourseTypeStatus(id, currentStatus);
        }
      });
    }

    function toggleCourseTypeStatus(id, currentStatus) {
      const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';
      const payload = { status: newStatus };

      fetch('/api/toggle-course-type/' + id, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Authorization': token

        },
        body: JSON.stringify(payload)
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          Swal.fire('Success', data.message, 'success');
          fetchCourseTypes();
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      })
      .catch(err => {
        console.error(err);
        Swal.fire('Error', 'An error occurred while toggling course type status.', 'error');
      });
    }

    // --- SEARCH FUNCTIONALITY ---
    document.getElementById('searchCourseType').addEventListener('input', function() {
      const query = this.value.trim().toLowerCase();
      const rows = document.querySelectorAll('#courseTypesTable tbody tr');
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.indexOf(query) > -1 ? '' : 'none';
      });
    });
  </script>
</body>
</html>
