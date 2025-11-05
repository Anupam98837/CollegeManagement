<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Student Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-light">
  <div class="d-flex">
    <div>
      @include('users.student.components.sidebar')
    </div>
    <div class="w-100 main-com">
      @include('users.student.components.header')

      <!-- New Tab Container: Profile and Change Password -->
      <div id="profileTabs" class="container mt-4">
        <ul class="nav nav-tabs" id="profileTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active text-14" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profilePane" type="button" role="tab" aria-controls="profilePane" aria-selected="true">Profile</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link text-14" id="change-password-tab" data-bs-toggle="tab" data-bs-target="#changePasswordPane" type="button" role="tab" aria-controls="changePasswordPane" aria-selected="false">Change Password</button>
          </li>
        </ul>
        <div class="tab-content" id="profileTabContent">
          <div class="tab-pane fade show active" id="profilePane" role="tabpanel" aria-labelledby="profile-tab">
            <!-- Existing Profile Content Container (removed d-none) -->
            <div id="profileContent" class="container mt-4">
              <p class="mb-4 text-secondary text-14">
                <i class="fa-solid fa-angle-right"></i>
                <span class="text-primary">Profile</span>
              </p>
              <div class="row g-4 bg-white m-1 rounded">
                <!-- Profile Picture Card -->
                <div class="col-md-6">
                  <div class="card text-center border-0 bg-light" style="position: sticky; top: 20px;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                      <div class="position-relative d-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 150px;">
                        <img id="studentPhoto" src="{{ asset('assets/web_assets/default-profile.jpg') }}" alt="Profile Picture" class="rounded-circle img-fluid border border-primary p-1" style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;" onclick="viewFullImage()" />
                        <button class="btn btn-light border position-absolute" style="bottom: 0; right: 0; border-radius: 50%;" onclick="updateStudentPhoto()">
                          <i class="fa-solid fa-camera"></i>
                        </button>
                      </div>
                      <h4 id="studentName" class="fw-bold mt-3"></h4>
                      <div class="d-flex align-items-center justify-content-center">
                        <p id="studentEmail" class="text-muted mb-0"></p>
                        <!-- Added Edit Email Button -->
                        <button class="btn btn-link btn-sm" onclick="editEmail()">
                          <i class="fa-solid fa-pen"></i>
                        </button>
                      </div>
                      <!-- Phone with Edit Button -->
                      <div class="d-flex align-items-center justify-content-center">
                        <p id="studentPhone" class="text-muted mb-0"></p>
                        <button class="btn btn-link btn-sm" onclick="editPhone()">
                          <i class="fa-solid fa-pen"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Profile Details -->
                <div class="col-md-6">
                  <!-- Personal Information Card -->
                  <div class="card border-0 bg-light">
                    <div class="card-body">
                      <h5 class="card-title text-primary"><i class="fa-solid fa-user"></i> Personal Information</h5>
                      <p class="text-14"><strong>Date of Birth:</strong> <span id="dob">Loading...</span></p>
                      <!-- Address with Edit Button -->
                      <div class="d-flex align-items-center">
                        <p class="text-14 mb-0"><strong>Address:</strong> <span id="address">Loading...</span></p>
                        <button class="btn btn-link btn-sm" onclick="editAddress()">
                          <i class="fa-solid fa-pen"></i>
                        </button>
                      </div>
                      <p class="text-14"><strong>Blood Group:</strong> <span id="bloodGroup">Loading...</span>
                        <!-- Edit button visible always; if no blood group value, only this button is visible -->
                        <button class="btn btn-link btn-sm d-none" id="blood-group-edit" onclick="editBloodGroup()">
                          <i class="fa-solid fa-pen"></i>
                        </button>
                      </p>
                    </div>
                  </div>
                  <hr class="text-secondary">
                  <!-- Education Details Card -->
                  <div class="card border-0 bg-light">
                    <div class="card-body">
                      <h5 class="card-title text-primary"><i class="fa-solid fa-graduation-cap"></i> Education Details</h5>
                      <p class="text-14"><strong>Course:</strong> <span id="course">Loading...</span></p>
                      <p class="text-14"><strong>Course Type:</strong> <span id="courseType">Loading...</span></p>
                      <p class="text-14"><strong>Course Duration:</strong> <span id="courseDuration">Loading...</span></p>
                      <!-- New Institution Details -->
                      <p class="text-14"><strong>Institute:</strong> <span id="instituteName">Loading...</span></p>
                      <p class="text-14"><strong>Institute Type:</strong> <span id="instituteType">Loading...</span></p>
                      <hr>
                      <!-- Class X Details -->
                      <div id="classXData" class="d-none">
                        <p class="text-14">
                          <strong>Class X (Board):</strong> <span id="classXBoard">Loading...</span>
                        </p>
                        <p class="text-14">
                          <strong>Total Marks:</strong> <span id="classXTotalMarks">Loading...</span>
                        </p>
                        <p class="text-14">
                          <strong>Overall Percentage:</strong> <span id="classXOverallPercentage">Loading...</span>
                        </p>
                      </div>
                      <!-- Class XII Details -->
                      <div id="classXIIData" class="d-none">
                        <p class="text-14">
                          <strong>Class XII (Board):</strong> <span id="classXIIBoard">Loading...</span>
                        </p>
                        <p class="text-14">
                          <strong>Total Marks:</strong> <span id="classXIITotalMarks">Loading...</span>
                        </p>
                        <p class="text-14">
                          <strong>Overall Percentage:</strong> <span id="classXIIOverallPercentage">Loading...</span>
                        </p>
                      </div>
                      <!-- College/University Details -->
                      <div id="collegeData" class="d-none">
                        <p class="text-14">
                          <strong>College/University:</strong> <span id="college">Loading...</span>
                        </p>
                        <p class="text-14">
                          <strong>Total Marks:</strong> <span id="collegeTotalMarks">Loading...</span>
                        </p>
                        <p class="text-14">
                          <strong>Overall Percentage:</strong> <span id="collegeOverallPercentage">Loading...</span>
                        </p>
                      </div>
                    </div>
                  </div>
                  <hr class="text-secondary">
                  <!-- Parent / Guardian Details Card -->
                  <div class="card border-0 bg-light">
                    <div class="card-body">
                      <h5 class="card-title text-primary"><i class="fa-solid fa-users"></i> Parent / Guardian Details</h5>
                      <p class="text-14">
                        <strong>Father:</strong> <span id="fatherName">Loading...</span>
                        <small>(<span id="fatherOccupation">Loading...</span> | <span id="fatherPhone">Loading...</span>)</small>
                      </p>
                      <p class="text-14"><strong>Father's Address:</strong> <span id="fatherAddress">Loading...</span></p>
                      <p class="text-14">
                        <strong>Mother:</strong> <span id="motherName">Loading...</span>
                        <small>(<span id="motherOccupation">Loading...</span> | <span id="motherPhone">Loading...</span>)</small>
                      </p>
                      <p class="text-14"><strong>Mother's Address:</strong> <span id="motherAddress">Loading...</span></p>
                      <!-- Guardian Details (if available) -->
                      <div id="guardianDetailsContainer" class="d-none">
                        <p class="text-14">
                          <strong>Guardian:</strong> <span id="guardianName">Loading...</span>
                          <small>(<span id="guardianOccupation">Loading...</span> | <span id="guardianPhone">Loading...</span> | <span id="guardianEmail">Loading...</span>)</small>
                        </p>
                        <p class="text-14"><strong>Guardian's Address:</strong> <span id="guardianAddress">Loading...</span></p>
                      </div>
                    </div>
                  </div>
                  <hr class="text-secondary">
                </div><!-- end col-md-6 -->
              </div><!-- end row -->
            </div><!-- end profileContent -->
          </div>
          <div class="tab-pane fade mt-4" id="changePasswordPane" role="tabpanel" aria-labelledby="change-password-tab">
            <!-- New Change Password Form -->
            <p class="mb-4 text-secondary text-14">
              <i class="fa-solid fa-angle-right"></i>
              <span class="text-primary">Change Password</span>
            </p>
            <div class="card bg-white p-4 mt-4">
              <!-- Confirm Change Checkbox -->
              
              <form id="changePasswordForm">
                <div class="mb-3 position-relative">
                  <label for="currentPassword" class="form-label text-13">Current Password</label>
                  <input type="password" class="form-control placeholder-12 text-13" id="currentPassword" name="current_password" required>
                  <span class="position-absolute text-secondary text-13" style="right:10px; top:38px; cursor:pointer;" onclick="togglePassword('currentPassword')">
                    <i class="fa-solid fa-eye-slash" id="currentPasswordIcon"></i>
                  </span>
                </div>
                <div class="mb-3 position-relative">
                  <label for="newPassword" class="form-label text-13">New Password</label>
                  <input type="password" class="form-control placeholder-12 text-13" id="newPassword" name="new_password" required>
                  <span class="position-absolute text-secondary text-13" style="right:10px; top:38px; cursor:pointer;" onclick="togglePassword('newPassword')">
                    <i class="fa-solid fa-eye-slash" id="newPasswordIcon"></i>
                  </span>
                </div>
                <div class="mb-3 position-relative">
                  <label for="confirmNewPassword" class="form-label text-13">Confirm New Password</label>
                  <input type="password" class="form-control placeholder-12 text-13" id="confirmNewPassword" name="new_password_confirmation" required>
                  <span class="position-absolute text-secondary text-13" style="right:10px; top:38px; cursor:pointer;" onclick="togglePassword('confirmNewPassword')">
                    <i class="fa-solid fa-eye-slash" id="confirmNewPasswordIcon"></i>
                  </span>
                </div>
                <!-- Spinner container for password update -->
                <div id="passwordUpdateSpinner" class="d-none text-center mb-3">
                  <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </div>
                <div class="mb-3 form-check">
                  <input type="checkbox" class="form-check-input" id="confirmChangeCheckbox">
                  <label class="form-check-label text-13" for="confirmChangeCheckbox">Confirm Change</label>
                </div>
                <button type="submit" id="updatePasswordBtn" class="btn btn-primary text-13" disabled>Update Password</button>
              </form>
            </div>
          </div>
        </div>
      </div>
      <!-- End New Tab Container -->

      <!-- No Profile Message Container (initially hidden) -->
      <div id="noProfileMessage" class="container mt-4 d-none">
        <div class="text-center">
          <img src="{{ asset('assets/web_assets/noData.png') }}" alt="">
          <h4>Complete Your Profile</h4>
          <p>Your profile is incomplete. Please register to complete your profile.</p>
          <a href="/student/register" class="btn btn-primary">Register</a>
        </div>
      </div>
    </div><!-- end main-com -->
  </div><!-- end d-flex -->

  <script>
    // Global variable to store current address fields
    let currentAddress = {};

    // Ensure user is authenticated
    document.addEventListener("DOMContentLoaded", function() {
      if (!sessionStorage.getItem("token")) {
        window.location.href = "/";
      }
    });

    // Function to toggle password visibility
    function togglePassword(inputId) {
      const inputField = document.getElementById(inputId);
      const icon = document.getElementById(inputId + "Icon");
      if (inputField.type === "password") {
        inputField.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      } else {
        inputField.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      }
    }

    // Enable Update Password button when Confirm Change checkbox is clicked
    document.getElementById("confirmChangeCheckbox").addEventListener("change", function() {
      document.getElementById("updatePasswordBtn").disabled = !this.checked;
    });

    // Function to view the profile image in full using SweetAlert2
    function viewFullImage() {
      const imgSrc = document.getElementById("studentPhoto").src;
      Swal.fire({
        imageUrl: imgSrc,
        imageAlt: 'Student Profile Picture',
        showCloseButton: true,
        showConfirmButton: false,
        width: '300px'
      });
    }

    // Fetch student data by email on page load
    document.addEventListener('DOMContentLoaded', function () {
      const email = sessionStorage.getItem("student_email");
      if (email) {
        fetchStudentData(email);
      }
      const savedImage = localStorage.getItem('student_photo');
      if (savedImage) {
        document.getElementById("studentPhoto").src = savedImage;
      }
    });

    // Fetch student data from the API
    function fetchStudentData(email) {
      fetch('/api/get-student-by-email', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': sessionStorage.getItem('token')
        },
        body: JSON.stringify({ email })
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          populateProfile(data.data);
          document.getElementById('profileContent').classList.remove('d-none');
          document.getElementById('noProfileMessage').classList.add('d-none');
        } else {
          document.getElementById('profileContent').classList.add('d-none');
          document.getElementById('noProfileMessage').classList.remove('d-none');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        document.getElementById('profileContent').classList.add('d-none');
        document.getElementById('noProfileMessage').classList.remove('d-none');
      });
    }

    // Populate the profile page with student data
    function populateProfile(student) {
      document.getElementById("studentName").innerText = student.name;
      document.getElementById("studentEmail").innerHTML = `<i class="fa-regular fa-envelope"></i> ${student.email}`;
      document.getElementById("studentPhone").innerHTML = `<i class="fa-solid fa-phone"></i> ${student.phone}`;

      // Set address display and store individual address fields
      const addr = `${student.city || ""}, ${student.state || ""}, ${student.country || ""}, ${student.po || ""}, ${student.ps || ""}, ${student.pin || ""}`;
      document.getElementById("address").innerText = addr;
      currentAddress = {
        city: student.city || "",
        po: student.po || "",
        ps: student.ps || "",
        state: student.state || "",
        country: student.country || "",
        pin: student.pin || ""
      };

      document.getElementById("dob").innerText = student.date_of_birth || "N/A";
      document.getElementById("bloodGroup").innerText = student.blood_group || "N/A";
      if (student.blood_group && student.blood_group.trim() !== "") {
        document.getElementById("blood-group-edit").classList.add("d-none");
      } else {
        document.getElementById("blood-group-edit").classList.remove("d-none");
      }
      let courseData = JSON.parse(student.course);
      document.getElementById("course").innerText = courseData.program_name || "N/A";
      document.getElementById("courseType").innerText = ` ${courseData.program_type} (${courseData.intake_type})` || "N/A";
      document.getElementById("courseDuration").innerText = ` ${courseData.program_duration} Year`  || "N/A";

      // --- Institution Details ---
      if (student.institute) {
        try {
          let instituteData = JSON.parse(student.institute);
          document.getElementById("instituteName").innerText = instituteData.institution_name || "N/A";
          document.getElementById("instituteType").innerText = instituteData.institution_type || "N/A";
        } catch (e) {
          console.error("Error parsing institute data", e);
          document.getElementById("instituteName").innerText = "N/A";
          document.getElementById("instituteType").innerText = "N/A";
        }
      } else {
        document.getElementById("instituteName").innerText = "N/A";
        document.getElementById("instituteType").innerText = "N/A";
      }

      // Class X Details
      if (student.class_x_board || student.class_x_data) {
        document.getElementById("classXData").classList.remove("d-none");
        document.getElementById("classXBoard").innerText = student.class_x_board || "N/A";
        if (student.class_x_data) {
          try {
            let classXData = JSON.parse(student.class_x_data);
            if (Array.isArray(classXData) && classXData.length > 0) {
              let totalFullMarks = 0, totalMarksObtained = 0;
              classXData.forEach(item => {
                totalFullMarks += parseFloat(item.fullMarks);
                totalMarksObtained += parseFloat(item.marksObtained);
              });
              let overallPercentage = totalFullMarks ? ((totalMarksObtained / totalFullMarks) * 100).toFixed(2) + '%' : 'N/A';
              document.getElementById("classXTotalMarks").innerText = totalFullMarks;
              document.getElementById("classXOverallPercentage").innerText = overallPercentage;
            } else {
              document.getElementById("classXTotalMarks").innerText = "N/A";
              document.getElementById("classXOverallPercentage").innerText = "N/A";
            }
          } catch (err) {
            console.error("Error parsing class_x_data", err);
          }
        }
      } else {
        document.getElementById("classXData").classList.add("d-none");
      }

      // Class XII Details
      if (student.class_xii_board && student.class_xii_data) {
        document.getElementById("classXIIData").classList.remove("d-none");
        document.getElementById("classXIIBoard").innerText = student.class_xii_board || "N/A";
        try {
          let classXIIData = JSON.parse(student.class_xii_data);
          if (Array.isArray(classXIIData) && classXIIData.length > 0) {
            let totalFullMarks = 0, totalMarksObtained = 0;
            classXIIData.forEach(item => {
              totalFullMarks += parseFloat(item.fullMarks);
              totalMarksObtained += parseFloat(item.marksObtained);
            });
            let overallPercentage = totalFullMarks ? ((totalMarksObtained / totalFullMarks) * 100).toFixed(2) + '%' : 'N/A';
            document.getElementById("classXIITotalMarks").innerText = totalFullMarks;
            document.getElementById("classXIIOverallPercentage").innerText = overallPercentage;
          } else {
            document.getElementById("classXIITotalMarks").innerText = "N/A";
            document.getElementById("classXIIOverallPercentage").innerText = "N/A";
          }
        } catch (err) {
          console.error("Error parsing class_xii_data", err);
        }
      } else {
        document.getElementById("classXIIData").classList.add("d-none");
      }

      // College/University Details
      if (student.college_university && student.college_data) {
        document.getElementById("collegeData").classList.remove("d-none");
        document.getElementById("college").innerText = student.college_university || "N/A";
        try {
          let collegeData = JSON.parse(student.college_data);
          if (Array.isArray(collegeData) && collegeData.length > 0) {
            let totalFullMarks = 0, totalMarksObtained = 0;
            collegeData.forEach(item => {
              totalFullMarks += parseFloat(item.fullMarks);
              totalMarksObtained += parseFloat(item.marksObtained);
            });
            let overallPercentage = totalFullMarks ? ((totalMarksObtained / totalFullMarks) * 100).toFixed(2) + '%' : 'N/A';
            document.getElementById("collegeTotalMarks").innerText = totalFullMarks;
            document.getElementById("collegeOverallPercentage").innerText = overallPercentage;
          } else {
            document.getElementById("collegeTotalMarks").innerText = "N/A";
            document.getElementById("collegeOverallPercentage").innerText = "N/A";
          }
        } catch (err) {
          console.error("Error parsing college_data", err);
        }
      } else {
        document.getElementById("collegeData").classList.add("d-none");
      }

      // Parent / Guardian Details
      document.getElementById("fatherName").innerText = student.father_name || "N/A";
      document.getElementById("fatherOccupation").innerText = student.father_occupation || "N/A";
      document.getElementById("fatherPhone").innerText = student.father_phone || "N/A";
      let fatherAddress = student.father_street || "";
      if (student.father_po) fatherAddress += ", " + student.father_po;
      if (student.father_ps) fatherAddress += ", " + student.father_ps;
      if (student.father_city) fatherAddress += ", " + student.father_city;
      if (student.father_state) fatherAddress += ", " + student.father_state;
      if (student.father_country) fatherAddress += ", " + student.father_country;
      if (student.father_pincode) fatherAddress += ", " + student.father_pincode;
      document.getElementById("fatherAddress").innerText = fatherAddress;

      document.getElementById("motherName").innerText = student.mother_name || "N/A";
      document.getElementById("motherOccupation").innerText = student.mother_occupation || "N/A";
      document.getElementById("motherPhone").innerText = student.mother_phone || "N/A";
      let motherAddress = student.mother_street || "";
      if (student.mother_po) motherAddress += ", " + student.mother_po;
      if (student.mother_ps) motherAddress += ", " + student.mother_ps;
      if (student.mother_city) motherAddress += ", " + student.mother_city;
      if (student.mother_state) motherAddress += ", " + student.mother_state;
      if (student.mother_country) motherAddress += ", " + student.mother_country;
      if (student.mother_pincode) motherAddress += ", " + student.mother_pincode;
      document.getElementById("motherAddress").innerText = motherAddress;

      if (student.guardian_name) {
        document.getElementById("guardianName").innerText = student.guardian_name;
        document.getElementById("guardianOccupation").innerText = student.guardian_occupation || "N/A";
        document.getElementById("guardianPhone").innerText = student.guardian_phone || "N/A";
        document.getElementById("guardianEmail").innerText = student.guardian_email || "N/A";
        let guardianAddress = student.guardian_street || "";
        if (student.guardian_po) guardianAddress += ", " + student.guardian_po;
        if (student.guardian_ps) guardianAddress += ", " + student.guardian_ps;
        if (student.guardian_city) guardianAddress += ", " + student.guardian_city;
        if (student.guardian_state) guardianAddress += ", " + student.guardian_state;
        if (student.guardian_country) guardianAddress += ", " + student.guardian_country;
        if (student.guardian_pincode) guardianAddress += ", " + student.guardian_pincode;
        document.getElementById("guardianAddress").innerText = guardianAddress;
        document.getElementById("guardianDetailsContainer").classList.remove("d-none");
      } else {
        document.getElementById("guardianDetailsContainer").classList.add("d-none");
      }

      if (student.student_photo) {
        document.getElementById("studentPhoto").src = `{{ asset('assets/student_documents/${student.student_photo}') }}`;
      }
    }

    // Added: Function to edit the student's email
    function editEmail() {
      // Get the current email from the profile display
      const currentEmailText = document.getElementById("studentEmail").innerText;
      // Remove any icons (assuming the email text starts after an icon)
      const currentEmail = currentEmailText.replace(/^\s*\S+\s*/, '').trim();
      
      Swal.fire({
        title: 'Edit Email',
        input: 'email',
        inputLabel: 'Enter your new email',
        inputValue: currentEmail,
        showCancelButton: true,
        confirmButtonText: 'Update',
        preConfirm: (newEmail) => {
          if (!newEmail) {
            Swal.showValidationMessage('Please enter a valid email.');
          }
          return newEmail;
        }
      }).then((result) => {
        if (result.isConfirmed) {
          // Call API endpoint to update email by uid
          fetch('/api/student/change-email', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'Authorization': sessionStorage.getItem('token'),
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              uid: sessionStorage.getItem("student_uid") || "",
              new_email: result.value
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              Swal.fire('Success', 'Email updated successfully!', 'success');
              // Update sessionStorage with new email
              sessionStorage.setItem("student_email", result.value);
              // Fetch student data again using the new email
              fetchStudentData(result.value);
            } else {
              Swal.fire('Error', data.message || 'Failed to update email.', 'error');
            }
          })
          .catch(error => {
            console.error(error);
            Swal.fire('Error', 'An error occurred while updating email.', 'error');
          });
        }
      });
    }
  
    // Function to open SweetAlert2 modal for editing phone only
    function editPhone() {
      const currentPhone = document.getElementById('studentPhone').innerText.replace(/^\D+/,'').trim();
      Swal.fire({
        title: 'Edit Phone Number',
        html: `<input type="text" id="swalPhone" class="swal2-input placeholder-12 text-13" placeholder="Phone Number" value="${currentPhone}">`,
        showCancelButton: true,
        confirmButtonText: 'Update',
        preConfirm: () => {
          const phone = document.getElementById('swalPhone').value;
          if (!phone) {
            Swal.showValidationMessage('Please enter a phone number');
          }
          return { phone };
        }
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('/api/update-student', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'Authorization': sessionStorage.getItem('token'),
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              email: sessionStorage.getItem("student_email"),
              phone: result.value.phone
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              Swal.fire('Success', 'Phone number updated successfully!', 'success');
              document.getElementById('studentPhone').innerHTML = `<i class="fa-solid fa-phone"></i> ${result.value.phone}`;
            } else {
              Swal.fire('Error', data.message || 'Update failed', 'error');
            }
          })
          .catch(error => {
            console.error(error);
            Swal.fire('Error', 'An error occurred while updating', 'error');
          });
        }
      });
    }
  
    // Function to open SweetAlert2 modal for editing address (with left-aligned labels and dropdowns for state and country)
    function editAddress() {
      // Example dropdown options for state and country
      const stateOptions = `
        <option value="Andhra Pradesh">Andhra Pradesh</option>
        <option value="Arunachal Pradesh">Arunachal Pradesh</option>
        <option value="Assam">Assam</option>
        <option value="Bihar">Bihar</option>
        <option value="Chhattisgarh">Chhattisgarh</option>
        <option value="Goa">Goa</option>
        <option value="Gujarat">Gujarat</option>
        <option value="Haryana">Haryana</option>
        <option value="Himachal Pradesh">Himachal Pradesh</option>
        <option value="Jharkhand">Jharkhand</option>
        <option value="Karnataka">Karnataka</option>
        <option value="Kerala">Kerala</option>
        <option value="Madhya Pradesh">Madhya Pradesh</option>
        <option value="Maharashtra">Maharashtra</option>
        <option value="Manipur">Manipur</option>
        <option value="Meghalaya">Meghalaya</option>
        <option value="Mizoram">Mizoram</option>
        <option value="Nagaland">Nagaland</option>
        <option value="Odisha">Odisha</option>
        <option value="Punjab">Punjab</option>
        <option value="Rajasthan">Rajasthan</option>
        <option value="Sikkim">Sikkim</option>
        <option value="Tamil Nadu">Tamil Nadu</option>
        <option value="Telangana">Telangana</option>
        <option value="Tripura">Tripura</option>
        <option value="Uttar Pradesh">Uttar Pradesh</option>
        <option value="Uttarakhand">Uttarakhand</option>
        <option value="West Bengal">West Bengal</option>
      `;

      const countryOptions = `<option value="India">India</option>`;

      Swal.fire({
        title: 'Edit Address',
        html: `
          <div class="mb-2 text-start">
            <label class="form-label text-13">City</label>
            <input type="text" id="swalCity" class="form-control placeholder-12 text-13" value="${currentAddress.city}">
          </div>
          <div class="mb-2 text-start">
            <label class="form-label text-13">Post Office</label>
            <input type="text" id="swalPO" class="form-control placeholder-12 text-13" value="${currentAddress.po}">
          </div>
          <div class="mb-2 text-start">
            <label class="form-label text-13">Police Station</label>
            <input type="text" id="swalPS" class="form-control placeholder-12 text-13" value="${currentAddress.ps}">
          </div>
          <div class="mb-2 text-start">
            <label class="form-label text-13">State</label>
            <select id="swalState" class="form-select placeholder-12 text-13">
              ${stateOptions}
            </select>
          </div>
          <div class="mb-2 text-start">
            <label class="form-label text-13">Country</label>
            <select id="swalCountry" class="form-select placeholder-12 text-13">
              ${countryOptions}
            </select>
          </div>
          <div class="mb-2 text-start">
            <label class="form-label text-13">PIN</label>
            <input type="text" id="swalPin" class="form-control placeholder-12 text-13" value="${currentAddress.pin}">
          </div>
          <div class="mb-2 form-check">
            <input type="checkbox" class="form-check-input" id="confirmAddressCheckbox">
            <label class="form-check-label text-13" for="confirmAddressCheckbox">Confirm Update</label>
          </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Update',
        preConfirm: () => {
          if(!document.getElementById('confirmAddressCheckbox').checked) {
            Swal.showValidationMessage('Please confirm update by checking the box.');
          }
          const city = document.getElementById('swalCity').value;
          const po = document.getElementById('swalPO').value;
          const ps = document.getElementById('swalPS').value;
          const state = document.getElementById('swalState').value;
          const country = document.getElementById('swalCountry').value;
          const pin = document.getElementById('swalPin').value;
          if (!city || !po || !ps || !state || !country || !pin) {
            Swal.showValidationMessage('Please fill out all address fields');
          }
          return { city, po, ps, state, country, pin };
        }
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('/api/update-student', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'Authorization': sessionStorage.getItem('token'),
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              email: sessionStorage.getItem("student_email"),
              city: result.value.city,
              po: result.value.po,
              ps: result.value.ps,
              state: result.value.state,
              country: result.value.country,
              pin: result.value.pin
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              Swal.fire('Success', 'Address updated successfully!', 'success');
              currentAddress = {
                city: result.value.city,
                po: result.value.po,
                ps: result.value.ps,
                state: result.value.state,
                country: result.value.country,
                pin: result.value.pin
              };
              const updatedAddr = `${result.value.city}, ${result.value.state}, ${result.value.country}, ${result.value.po}, ${result.value.ps}, ${result.value.pin}`;
              document.getElementById('address').innerText = updatedAddr;
            } else {
              Swal.fire('Error', data.message || 'Update failed', 'error');
            }
          })
          .catch(error => {
            console.error(error);
            Swal.fire('Error', 'An error occurred while updating', 'error');
          });
        }
      });
    }
  
    // Function to open SweetAlert2 modal for editing blood group using a dropdown and a confirm checkbox
    function editBloodGroup() {
      const currentBG = document.getElementById('bloodGroup').innerText.trim();
      // Define blood group options
      const bgOptions = {
        "A+": "A+",
        "A-": "A-",
        "B+": "B+",
        "B-": "B-",
        "O+": "O+",
        "O-": "O-",
        "AB+": "AB+",
        "AB-": "AB-"
      };
      let optionsHTML = "";
      for (const [key, value] of Object.entries(bgOptions)) {
        optionsHTML += `<option value="${value}" ${currentBG === value ? 'selected' : ''}>${value}</option>`;
      }
      Swal.fire({
        title: 'Edit Blood Group',
        html: `
          <div class="mb-2 text-start">
            <label class="form-label text-13">Select Blood Group</label>
            <select id="swalBloodGroup" class="form-select placeholder-12 text-13">
              ${optionsHTML}
            </select>
          </div>
          <div class="mb-2 form-check">
            <input type="checkbox" class="form-check-input" id="confirmBGCheckbox">
            <label class="form-check-label text-13" for="confirmBGCheckbox">Confirm Update</label>
          </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Update',
        preConfirm: () => {
          if (!document.getElementById('confirmBGCheckbox').checked) {
            Swal.showValidationMessage('Please confirm update by checking the box.');
          }
          const bg = document.getElementById('swalBloodGroup').value;
          if (!bg) {
            Swal.showValidationMessage('Please select a blood group.');
          }
          return bg;
        }
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('/api/update-student', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'Authorization': sessionStorage.getItem('token'),
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              email: sessionStorage.getItem("student_email"),
              blood_group: result.value
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              Swal.fire('Success', 'Blood Group updated successfully!', 'success');
              document.getElementById('bloodGroup').innerText = result.value;
            } else {
              Swal.fire('Error', data.message || 'Update failed', 'error');
            }
          })
          .catch(error => {
            console.error(error);
            Swal.fire('Error', 'An error occurred while updating blood group.', 'error');
          });
        }
      });
    }

    // Function to update student photo (already implemented)
    function updateStudentPhoto() {
      Swal.fire({
        title: 'Upload New Profile Picture',
        html: `
          <label for="fileUpload" class="d-flex flex-column align-items-center" style="cursor: pointer;">
            <div id="previewContainer" class="p-3 bg-light rounded-circle border" style="width: 150px; height: 150px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
              <i id="uploadIcon" class="fa-solid fa-cloud-arrow-up text-primary" style="font-size: 2rem;"></i>
              <img id="imagePreview" src="" alt="Preview" class="d-none" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            </div>
            <small class="text-muted mt-2">Click to upload</small>
          </label>
          <input type="file" id="fileUpload" style="display: none;" accept="image/*">
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        didOpen: () => {
          document.getElementById('fileUpload').addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
              const reader = new FileReader();
              reader.onload = function (e) {
                document.getElementById('uploadIcon').classList.add('d-none');
                document.getElementById('imagePreview').classList.remove('d-none');
                document.getElementById('imagePreview').src = e.target.result;
                localStorage.setItem('student_photo', e.target.result);
              };
              reader.readAsDataURL(file);
            }
          });
        },
        preConfirm: () => {
          const fileInput = document.getElementById('fileUpload');
          if (!fileInput.files.length) {
            Swal.showValidationMessage('Please select an image');
            return false;
          }
          return fileInput.files[0];
        }
      }).then((result) => {
        if (result.isConfirmed) {
          let formData = new FormData();
          formData.append('student_photo', result.value);
          formData.append('email', sessionStorage.getItem("student_email"));
  
          Swal.fire({
            title: 'Updating Profile Picture',
            text: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
  
          fetch('/api/update-student-documents', {
            method: 'POST',
            headers: {
              'Authorization': sessionStorage.getItem('token'),
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              Swal.fire('Success', 'Profile picture updated successfully!', 'success');
              fetchStudentData(sessionStorage.getItem("student_email"));
            } else {
              Swal.fire('Error', 'Failed to update profile picture.', 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'An error occurred while updating.', 'error');
          });
        }
      });
    }

    // New: Change Password Form Submission with loading spinner and confirm checkbox check
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
      e.preventDefault();
      // Check if confirm change checkbox is checked (button is disabled until then)
      const currentPassword = document.getElementById('currentPassword').value;
      const newPassword = document.getElementById('newPassword').value;
      const confirmNewPassword = document.getElementById('confirmNewPassword').value;
      if(newPassword !== confirmNewPassword) {
        Swal.fire('Error', 'New password and confirmation do not match.', 'error');
        return;
      }
      document.getElementById('passwordUpdateSpinner').classList.remove('d-none');
      fetch('/api/student/change-password', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': sessionStorage.getItem('token'),
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          uid: sessionStorage.getItem("student_uid"),
          current_password: currentPassword,
          new_password: newPassword,
          new_password_confirmation: confirmNewPassword
        })
      })
      .then(response => response.json())
      .then(data => {
        document.getElementById('passwordUpdateSpinner').classList.add('d-none');
        if(data.status === 'success') {
          Swal.fire('Success', data.message, 'success');
          document.getElementById('changePasswordForm').reset();
          // Disable update button again
          document.getElementById("updatePasswordBtn").disabled = true;
        } else {
          Swal.fire('Error', data.message, 'error');
        }
      })
      .catch(error => {
        document.getElementById('passwordUpdateSpinner').classList.add('d-none');
        console.error(error);
        Swal.fire('Error', 'An error occurred while updating the password.', 'error');
      });
    });
  </script>
</body>
</html>
