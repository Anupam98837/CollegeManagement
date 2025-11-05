<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Courses</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
      <div class="container mt-4">
        
        <!-- Add Course Form Container (Hidden by Default) -->
        <div id="addCourseFormContainer" class="mb-4 d-none">
          <!-- Header / Navigation -->
          <p class="mb-4 text-secondary text-14">
            Course <i class="fa-solid fa-angle-right"></i>
            <span class="text-primary admin_add_Courses_text">Add Courses</span>
          </p>
          <form id="addCourseForm" class="bg-white p-4 rounded position-relative">
            <!-- Close Button -->
            <button type="button" class="btn btn-danger text-13 position-absolute top-0 end-0 m-3" onclick="hideAddCourseForm()">
              <i class="fa-solid fa-xmark"></i>
            </button>
            @csrf
            <!-- Form Fields -->
            <div class="row g-3">
              <div class="col-md-6">
                <label for="board" class="form-label text-13">Board <span class="text-danger">*</span></label>
                <!-- Board dropdown will be populated dynamically -->
                <select id="board" name="board" class="form-control placeholder-14 text-13" required>
                  <option value="" disabled selected>Select Board</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="programType" class="form-label text-13">Program Type <span class="text-danger">*</span></label>
                <select id="programType" name="program_type" class="form-control placeholder-14 text-13" required>
                  <option value="" disabled selected>Select Program Type</option>
                </select>
              </div>
            </div>
            <div class="row g-3 mt-2">
              <div class="col-md-4">
                <label for="programName" class="form-label text-13">Program Name <span class="text-danger">*</span></label>
                <select id="programName" name="program_name" class="form-control placeholder-14 text-13" required>
                  <option value="" disabled selected>Select Program Name</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="programDuration" class="form-label text-13">Program Duration <span class="text-danger">*</span></label>
                <input type="text" id="programDuration" name="program_duration" class="form-control placeholder-14 text-13" placeholder="Enter Program Duration" required>
              </div>
              <div class="col-md-4">
                <label for="programCode" class="form-label text-13">Program Code <span class="text-danger">*</span></label>
                <input type="text" id="programCode" name="program_code" class="form-control placeholder-14 text-13" placeholder="Enter Program Code" required>
              </div>
            </div>
            <button type="submit" id="submitButton" class="btn btn-outline-primary text-13 mt-4">
              <span id="buttonText">Add Course</span>
              <span id="buttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
          </form>
        </div>

        <!-- Edit Course Form (Hidden by Default) -->
        <form id="auc_editCourseForm" class="bg-white p-4 rounded d-none">
          <!-- Header / Navigation -->
          <p class="mb-4 text-secondary text-14">
            <i class="fa-solid fa-angle-right"></i>
            <span class="text-primary admin_add_Courses_text">Edit Courses</span>
          </p>
          @csrf
          <!-- Hidden Field for Course ID -->
          <input type="hidden" id="auc_editCourseId" name="course_id">
          <!-- Form Fields -->
          <div class="row g-3">
            <div class="col-md-6">
              <label for="auc_editBoard" class="form-label text-13">Board <span class="text-danger">*</span></label>
              <!-- Board dropdown will be populated dynamically -->
              <select id="auc_editBoard" name="board" class="form-control placeholder-14 text-13" required>
                <option value="" disabled selected>Select Board</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="auc_editProgramType" class="form-label text-13">Program Type <span class="text-danger">*</span></label>
              <select id="auc_editProgramType" name="program_type" class="form-control placeholder-14 text-13" required>
                <option value="" disabled selected>Select Program Type</option>
              </select>
            </div>
          </div>
          <div class="row g-3 mt-2">
            <div class="col-md-6">
              <label for="auc_editProgramName" class="form-label text-13">Program Name <span class="text-danger">*</span></label>
              <select id="auc_editProgramName" name="program_name" class="form-control placeholder-14 text-13" required>
                <option value="" disabled selected>Select Program Name</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="auc_programDuration" class="form-label text-13">Program Duration <span class="text-danger">*</span></label>
              <input type="text" id="auc_programDuration" name="program_duration" class="form-control placeholder-14 text-13" placeholder="Enter Program Duration" required>
            </div>
          </div>
          <!-- Submit and Cancel Buttons -->
          <div class="mt-4">
            <button type="submit" id="auc_editSubmitButton" class="btn btn-outline-primary text-13">
              <span id="auc_editButtonText">Update Course</span>
              <span id="auc_editButtonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
            </button>
            <button type="button" onclick="cancelEditCourse()" class="btn btn-outline-danger text-13 ms-2">Cancel</button>
          </div>
        </form>

        <!-- View Courses Table -->
        <p class="mb-4 text-secondary text-14">
          <i class="fa-solid fa-angle-right"></i>
          <span class="text-primary">All Courses</span>
        </p>
        <div class="bg-white p-4 rounded">
          <div class="row mb-3 align-items-center justify-content-between">
            <div class="col-md-6 position-relative">
              <input type="text" id="searchCourse" class="form-control placeholder-14 text-13 ps-5" placeholder="Search by Board, Program Type, Program Name, or Program Code">
              <i class="fa-solid fa-search position-absolute text-secondary" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
            </div>
            <div class="text-end col-md-6">
              <button id="showAddCourseForm" class="btn btn-outline-primary text-13">
                <i class="fa-solid fa-plus"></i> Course
              </button>
            </div>
          </div>
          <div class="table-responsive">
            <table id="coursesTable" class="table table-striped table-hover text-center">
              <thead>
                <tr>
                  <th class="text-secondary text-13">Board</th>
                  <th class="text-secondary text-13">Program_Type</th>
                  <th class="text-secondary text-13">Program_Name</th>
                  <th class="text-secondary text-13">Duration(Year)</th>
                  <th class="text-secondary text-13">Program_Code</th>
                  <th class="text-secondary text-13">Status</th>
                  <th class="text-secondary text-13">Created_At</th>
                  <th class="text-secondary text-13">Actions</th>
                </tr>                
              </thead>
              <tbody>
                <!-- Courses will be populated dynamically here -->
              </tbody>
            </table>
          </div>
        </div>
      </div><!-- /.container -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    const token = sessionStorage.getItem('token');

    // --- FETCH COURSE TYPES FOR DROPDOWNS ---
    function fetchCourseTypes() {
      fetch('/api/view-course-type', {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Authorization': `${token}`
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          const programTypeDropdown = document.getElementById('programType');
          const editProgramTypeDropdown = document.getElementById('auc_editProgramType');

          // Clear existing options
          programTypeDropdown.innerHTML = '<option value="" disabled selected>Select Program Type</option>';
          editProgramTypeDropdown.innerHTML = '<option value="" disabled selected>Select Program Type</option>';

          // Filter only active courses
          const activeCourses = data.data.filter(course => course.status === "Active");

          activeCourses.forEach(course => {
            const option = document.createElement('option');
            option.value = course.course_type.toUpperCase();
            option.textContent = course.course_type.toUpperCase();

            // Append to both Add & Edit dropdowns
            programTypeDropdown.appendChild(option);
            editProgramTypeDropdown.appendChild(option.cloneNode(true));
          });
        } else {
          console.error('Failed to fetch course types:', data.message);
        }
      })
      .catch(error => {
        console.error('Error fetching course types:', error);
      });
    }

    // --- FETCH BOARDS FOR BOARD DROPDOWNS ---
    function fetchBoards() {
      fetch('/api/view-boards', {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Authorization': `${token}`
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          const boardDropdown = document.getElementById('board');
          const editBoardDropdown = document.getElementById('auc_editBoard');

          // Clear existing options and add default prompt
          boardDropdown.innerHTML = '<option value="" disabled selected>Select Board</option>';
          editBoardDropdown.innerHTML = '<option value="" disabled selected>Select Board</option>';

          // Loop through each board in the response and create an option element
          data.data.forEach(board => {
            const option = document.createElement('option');
            option.value = board.board_name;
            option.textContent = board.board_name;
            boardDropdown.appendChild(option);
            // Clone option for the edit dropdown
            editBoardDropdown.appendChild(option.cloneNode(true));
          });

          // Append "Other" option to allow user to add a new board
          const otherOption = document.createElement('option');
          otherOption.value = 'other';
          otherOption.textContent = 'Other';
          boardDropdown.appendChild(otherOption);
          editBoardDropdown.appendChild(otherOption.cloneNode(true));
        } else {
          console.error('Failed to fetch boards:', data.message);
        }
      })
      .catch(error => {
        console.error('Error fetching boards:', error);
      });
    }

    // --- HANDLE "Other" SELECTION ---
    function handleOtherSelection(dropdownId) {
      const dropdown = document.getElementById(dropdownId);
      if (dropdown.value === 'other') {
        Swal.fire({
          title: 'Enter Board Name',
          input: 'text',
          inputLabel: 'Board Name',
          inputAttributes: {
            style: 'text-transform: uppercase;'
          },
          inputPlaceholder: 'Enter new board name',
          showCancelButton: true,
          confirmButtonText: 'Submit'
        }).then((result) => {
          if (result.isConfirmed && result.value) {
            // Convert entered board name to uppercase
            const newBoard = result.value.toUpperCase();
            // Call API to add board with uppercase value
            fetch('/api/add-board', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Authorization': `${token}`
              },
              body: JSON.stringify({ board: newBoard })
            })
            .then(response => response.json())
            .then(data => {
              if (data.status === 'success') {
                Swal.fire('Success', data.message, 'success');
                // Refresh board dropdowns
                fetchBoards();
                // After refresh, set the dropdown value to the newly added board
                setTimeout(() => {
                  dropdown.value = newBoard;
                }, 500);
              } else {
                Swal.fire('Error', data.message || 'Error adding board', 'error');
                dropdown.value = '';
              }
            })
            .catch(error => {
              Swal.fire('Error', 'Something went wrong.', 'error');
              console.error('Error:', error);
            });
          } else {
            // If cancelled or no value entered, reset the dropdown selection
            dropdown.value = '';
          }
        });
      }
    }

    // Add event listeners to handle "Other" option selection
    document.addEventListener('DOMContentLoaded', function () {
      fetchCourseTypes();
      fetchBoards();

      document.getElementById('board').addEventListener('change', function () {
        if (this.value === 'other') {
          handleOtherSelection('board');
        }
      });
      document.getElementById('auc_editBoard').addEventListener('change', function () {
        if (this.value === 'other') {
          handleOtherSelection('auc_editBoard');
        }
      });
    });

    // --- SEARCH FUNCTIONALITY FOR COURSES ---
    document.getElementById('searchCourse').addEventListener('input', function () {
      const searchTerm = this.value.toLowerCase();
      const tableRows = document.querySelectorAll('#coursesTable tbody tr');
      tableRows.forEach(row => {
        const rowText = row.textContent.toLowerCase();
        row.style.display = rowText.includes(searchTerm) ? '' : 'none';
      });
    });

    const programs = {
      UG: [
        "Civil Engineering (BTech.CE)", 
        "Computer Science and Engineering Technology (BTech.CSET)", 
        "Electronics and Communication Engineering (BTech.ECE)", 
        "Electrical Engineering (BTech.EE)", 
        "Mechanical Engineering (BTech.ME)", 
        "Computer Science and Engineering (BTech.CSE)", 
        "Information Technology (BTech.IT)", 
        "Bachelor of Business Administration (BBA)", 
        "Bachelor of Computer Applications (BCA)", 
        "BSc in Hospitality and Hotel Administration (BSc.HHA)", 
        "BSc in Culinary Science (BSc.Culinary)", 
        "BSc in Information Technology (BSc.IT)", 
        "BSc in Multimedia, Animation & Graphics (BSc.MAG)", 
        "BSc in Cyber Security (BSc.Cyber)", 
        "BBA in Heritage Tourism (BBA.HT)", 
        "BBA in Supply Chain Management (BBA.SCM)"
      ],
      PG: [
        "Master of Business Administration (MBA)", 
        "MSc in Hospitality Management (MSc.HM)", 
        "Master of Tourism & Travel Management (MTTM)"
      ],    
      DIPLOMA: [
        "Civil Engineering (CE)", 
        "Computer Science and Engineering Technology (CSET)", 
        "Electronics and Communication Engineering (ECE)", 
        "Electrical Engineering (EE)", 
        "Mechanical Engineering (ME)"
      ],
      ITI: [
        "ITI in Electrician", 
        "ITI in Fitter", 
        "ITI in Welder", 
        "ITI in Computer Operator & Programming Assistant (COPA)", 
        "ITI in Draughtsman Civil", 
        "ITI in Draughtsman Mechanical", 
        "ITI in Electronics Mechanic", 
        "ITI in Refrigeration and Air Conditioning Technician", 
        "ITI in Mechanic Diesel Engine", 
        "ITI in Plumber", 
        "ITI in Turner", 
        "ITI in Machinist"
      ]
    };

    document.getElementById('programType').addEventListener('change', function () {
      const programName = document.getElementById('programName');
      programName.innerHTML = '<option value="" disabled selected>Select Program Name</option>';
      const selectedProgramType = this.value.toUpperCase();
      if (programs[selectedProgramType]) {
        programs[selectedProgramType].forEach(program => {
          const option = document.createElement('option');
          option.value = program;
          option.textContent = program;
          programName.appendChild(option);
        });
      }
    });

    document.getElementById('auc_editProgramType').addEventListener('change', function () {
      const programName = document.getElementById('auc_editProgramName');
      programName.innerHTML = '<option value="" disabled selected>Select Program Name</option>';
      const selectedProgramType = this.value;
      if (programs[selectedProgramType]) {
        programs[selectedProgramType].forEach(program => {
          const option = document.createElement('option');
          option.value = program;
          option.textContent = program;
          programName.appendChild(option);
        });
      }
    });

    // --- SHOW / HIDE ADD COURSE FORM FUNCTIONS ---
    document.getElementById('showAddCourseForm').addEventListener('click', function () {
      // Hide the edit form and reset it (if open)
      document.getElementById('auc_editCourseForm').classList.add('d-none');
      document.getElementById('auc_editCourseForm').reset();
      // Show the add course form container
      document.getElementById('addCourseFormContainer').classList.remove('d-none');
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    function hideAddCourseForm() {
      document.getElementById('addCourseFormContainer').classList.add('d-none');
    }

    // --- ADD COURSE FORM SUBMISSION ---
    document.getElementById('addCourseForm').addEventListener('submit', function (e) {
      e.preventDefault();
      const submitButton = document.getElementById('submitButton');
      const buttonText = document.getElementById('buttonText');
      const buttonSpinner = document.getElementById('buttonSpinner');

      const formData = {
        board: document.getElementById('board').value,
        program_type: document.getElementById('programType').value,
        program_name: document.getElementById('programName').value,
        program_duration: document.getElementById('programDuration').value,
        program_code: document.getElementById('programCode').value,
      };

      if (Object.values(formData).includes('')) {
        Swal.fire('Error', 'All required fields must be filled out.', 'error');
        return;
      }

      buttonText.classList.add('d-none');
      buttonSpinner.classList.remove('d-none');
      submitButton.disabled = true;

      fetch('/api/add-course', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Authorization': `${token}`
        },
        body: JSON.stringify(formData)
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
          document.getElementById('addCourseForm').reset();
          fetchCourses();
        } else {
          Swal.fire('Error', data.errors.program_code[0] || 'An error occurred.', 'error');
        }
      })
      .catch(error => {
        Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
        console.error('Error:', error);
      })
      .finally(() => {
        buttonText.classList.remove('d-none');
        buttonSpinner.classList.add('d-none');
        submitButton.disabled = false;
      });
    });

    // --- FETCH & DISPLAY COURSES ---
    function fetchCourses() {
      fetch('/api/view-courses', {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Authorization': `${token}`
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
        const coursesTableBody = document.querySelector('#coursesTable tbody');
        coursesTableBody.innerHTML = '';
        if (data.status === 'success' && data.data.length > 0) {
          data.data.forEach(course => {
            const row = `<tr>
            <td class="text-13 text-center align-middle">${course.board}</td>
            <td class="text-13 text-center align-middle">${course.program_type}</td>
            <td class="text-13 text-center align-middle">${course.program_name}</td>
            <td class="text-13 text-center align-middle">${course.program_duration}</td>
            <td class="text-13 text-center align-middle">${course.program_code}</td>
            <td class="text-13 text-center align-middle" style="color: ${course.status === 'Active' ? 'green' : 'red'};">
                ${course.status}
            </td>
            <td class="text-13 text-center align-middle">${course.created_at}</td>
            <td class="text-13 text-center align-middle">
                <button class="btn btn-sm btn-outline-secondary text-13 m-1 edit-btn"
                    data-id="${course.id.$oid}"
                    data-board="${course.board}"
                    data-program-type="${course.program_type}"
                    data-program-name="${course.program_name}"
                    data-program-duration="${course.program_duration}">
                    <i class="fa-regular fa-pen-to-square"></i>
                </button>
                <button class="btn btn-sm text-13 m-1 ${course.status === 'Active' ? 'btn-outline-danger' : 'btn-outline-success'}"
                    onclick="toggleCourseStatus('${course.id.$oid}', '${course.status}')">
                    <i class="fa-solid ${course.status === 'Active' ? 'fa-ban' : 'fa-power-off'}"></i>
                </button>
            </td>
        </tr>`;

            coursesTableBody.innerHTML += row;
          });
          document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function () {
              const id = this.getAttribute('data-id');
              const board = this.getAttribute('data-board');
              const programType = this.getAttribute('data-program-type');
              const programName = this.getAttribute('data-program-name');
              const programDuration = this.getAttribute('data-program-duration');
              editCourse(id, board, programType, programName, programDuration);
            });
          });
        } else {
          coursesTableBody.innerHTML = `<tr><td class="bg-white" colspan="100%"><img src="{{ asset('assets/web_assets/noData.png') }}" alt="No Data Found"></td></tr>`;
        }
      })
      .catch(error => {
        console.error('Error fetching courses:', error);
      });
    }

    // --- EDIT COURSE ---
    function editCourse(id, board, programType, programName, programDuration) {
      // Hide Add Course Form and show Edit Course Form
      document.getElementById('addCourseFormContainer').classList.add('d-none');
      document.querySelector('.admin_add_Courses_text').textContent = 'Edit Courses';
      document.getElementById('auc_editCourseForm').classList.remove('d-none');
      window.scrollTo({ top: 0, behavior: 'smooth' });

      // Populate edit form fields
      document.getElementById('auc_editCourseId').value = id;
      document.getElementById('auc_editBoard').value = board;
      document.getElementById('auc_editProgramType').value = programType;
      document.getElementById('auc_programDuration').value = programDuration;

      // Update the Program Name dropdown based on Program Type
      const editProgramNameDropdown = document.getElementById('auc_editProgramName');
      editProgramNameDropdown.innerHTML = '<option value="" disabled>Select Program Name</option>';
      if (programs[programType]) {
        programs[programType].forEach(program => {
          const option = document.createElement('option');
          option.value = program;
          option.textContent = program;
          editProgramNameDropdown.appendChild(option);
        });
      }

      // If current programName is not in dropdown, add it
      let programExists = Array.from(editProgramNameDropdown.options).some(
        option => option.value === programName
      );
      if (!programExists && programName) {
        const customOption = document.createElement('option');
        customOption.value = programName;
        customOption.textContent = programName;
        customOption.selected = true;
        editProgramNameDropdown.appendChild(customOption);
      } else {
        editProgramNameDropdown.value = programName;
      }

      // Replace the submit button listener for the edit form
      const submitButton = document.getElementById('auc_editSubmitButton');
      const newSubmitButton = submitButton.cloneNode(true);
      submitButton.parentNode.replaceChild(newSubmitButton, submitButton);

      newSubmitButton.addEventListener('click', function (e) {
        e.preventDefault();

        const updatedData = {
          board: document.getElementById('auc_editBoard').value,
          programType: document.getElementById('auc_editProgramType').value,
          programName: document.getElementById('auc_editProgramName').value,
          programDuration: document.getElementById('auc_programDuration').value,
        };

        const editSubmitButton = document.getElementById('auc_editSubmitButton');
        const editButtonText = document.getElementById('auc_editButtonText');
        const editButtonSpinner = document.getElementById('auc_editButtonSpinner');
        editButtonText.classList.add('d-none');
        editButtonSpinner.classList.remove('d-none');
        editSubmitButton.disabled = true;

        fetch(`/api/edit-course/${id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': `${token}`
          },
          body: JSON.stringify(updatedData),
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
            cancelEditCourse();
            fetchCourses();
          } else {
            Swal.fire('Error', data.message || 'An error occurred.', 'error');
          }
        })
        .catch(error => {
          Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
          console.log('Error:', error);
        })
        .finally(() => {
          editButtonText.classList.remove('d-none');
          editButtonSpinner.classList.add('d-none');
          editSubmitButton.disabled = false;
        });
      });
    }

    function cancelEditCourse() {
      // Hide Edit Course Form and Show Add Course Form
      document.getElementById('auc_editCourseForm').classList.add('d-none');
      document.querySelector('.admin_add_Courses_text').textContent = 'Add Courses';
      document.getElementById('auc_editCourseForm').reset();

      // Reset Program Name dropdown in the edit form
      const editProgramNameDropdown = document.getElementById('auc_editProgramName');
      editProgramNameDropdown.innerHTML = '<option value="" disabled selected>Select Program Name</option>';
    }

    function toggleCourseStatus(courseId, currentStatus) {
      const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';
      Swal.fire({
        title: 'Change Course Status',
        text: `Are you sure you want to change the status to ${newStatus}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, change it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/api/toggle-course-status/${courseId}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Authorization': `${token}`
            },
            body: JSON.stringify({ status: newStatus }),
          })
          .then(response => {
            if (response.status === 401 || response.status === 403) {
              window.location.href = '/Unauthorised';
              throw new Error('Unauthorized Access');
            }
            return response.json();
          })
          .then((data) => {
            if (data.status === 'success') {
              Swal.fire('Updated!', data.message, 'success');
              fetchCourses();
            } else {
              Swal.fire('Error', data.message || 'An error occurred.', 'error');
            }
          })
          .catch((error) => {
            Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
            console.error('Error:', error);
          });
        }
      });
    }

    fetchCourses();
  </script>
</body>
</html>
