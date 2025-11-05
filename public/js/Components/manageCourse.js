document.addEventListener("DOMContentLoaded", function() {
    if (!sessionStorage.getItem("token")) {
      // Redirect to blank path or your preferred path if token is missing.
      window.location.href = "/";
    }
  });
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
  
  document.addEventListener('DOMContentLoaded', function () {
    fetchCourseTypes();
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
            <td class="text-13 ext-center align-middle">${course.board}</td>
            <td class="text-13">${course.program_type}</td>
            <td class="text-13">${course.program_name}</td>
            <td class="text-13">${course.program_duration}</td>
            <td class="text-13">${course.program_code}</td>
            <td class="text-13" style="color: ${course.status === 'Active' ? 'green' : 'red'};">${course.status}</td>
            <td class="text-13">${course.created_at}</td>
            <td class="text-13">
              <button class="btn btn-sm btn-outline-secondary text-13 edit-btn m-1" 
                data-id="${course.id.$oid}"
                data-board="${course.board}"
                data-program-type="${course.program_type}"
                data-program-name="${course.program_name}"
                data-program-duration="${course.program_duration}"
              >
                <i class="fa-regular fa-pen-to-square"></i>
              </button>
              <button class="btn btn-sm ${course.status === 'Active' ? 'btn-outline-danger' : 'btn-outline-success'} text-13 m-1" 
                onclick="toggleCourseStatus('${course.id.$oid}', '${course.status}')"
              >
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
  