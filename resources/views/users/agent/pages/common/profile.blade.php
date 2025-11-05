<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Agent Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-light">
  <div class="d-flex">
    @include('users.agent.components.sidebar')
    <div class="w-100 main-com">
      @include('users.agent.components.header')
      
      <!-- New Tab Container: Profile and Change Password -->
      <div id="profileTabs" class="container mt-4">
        <ul class="nav nav-tabs" id="profileTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active text-14" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profilePane" type="button" role="tab" aria-controls="profilePane" aria-selected="true">
              Profile
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link text-14" id="change-password-tab" data-bs-toggle="tab" data-bs-target="#changePasswordPane" type="button" role="tab" aria-controls="changePasswordPane" aria-selected="false">
              Change Password
            </button>
          </li>
        </ul>
        <div class="tab-content" id="profileTabContent">
          <!-- Profile Tab -->
          <div class="tab-pane fade show active" id="profilePane" role="tabpanel" aria-labelledby="profile-tab">
            <div id="profileContent" class="container mt-4">
              <p class="mb-4 text-secondary text-14">
                <i class="fa-solid fa-angle-right"></i>
                <span class="text-primary">Profile</span>
              </p>
              <div class="row g-4 bg-white m-1 rounded">
                <!-- Profile Picture Card -->
                <div class="col-md-6">
                  <div class="card text-center border-0 bg-light" style="position: sticky; top: 0;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center">
                      <div class="position-relative d-flex align-items-center justify-content-center mb-3" style="width: 150px; height: 150px;">
                        <img id="agentPhoto" src="{{ asset('assets/web_assets/default-profile.jpg') }}" alt="Profile Picture" class="rounded-circle img-fluid border border-primary p-1" style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;" onclick="viewFullImage()" />
                        {{-- <button class="btn btn-light border position-absolute" style="bottom: 0; right: 0; border-radius: 50%;" onclick="updateAgentPhoto()">
                          <i class="fa-solid fa-pen"></i>
                        </button> --}}
                      </div>
                      <h4 id="agentName" class="fw-bold mt-3"></h4>
                      <div class="d-flex align-items-center justify-content-center">
                        <p id="agentEmail" class="text-muted mb-0"></p>
                        <button class="btn btn-link btn-sm" onclick="editAgentEmail()">
                          <i class="fa-solid fa-pen"></i>
                        </button>
                      </div>
                      <div class="d-flex align-items-center justify-content-center">
                        <p id="agentPhone" class="text-muted mb-0"></p>
                        <button class="btn btn-link btn-sm" onclick="editAgentPhone()">
                          <i class="fa-solid fa-pen"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Profile Details -->
                <div class="col-md-6">
                  <div class="card border-0 bg-light">
                    <div class="card-body">
                      <h5 class="card-title text-primary"><i class="fa-solid fa-user"></i> Personal Information</h5>
                      <p class="text-14"><strong>Designation:</strong> <span id="agentDesignation">Loading...</span></p>
                      <p class="text-14"><strong>Status:</strong> <span id="agentStatus">Loading...</span></p>
                      <p class="text-14"><strong>Pan:</strong> <span id="agentPan">Loading...</span></p>
                    </div>
                  </div>
                  <hr class="text-secondary">
                  <!-- Additional Information Card (Address) -->
                  <div class="card border-0 bg-light">
                    <div class="card-body">
                      <h5 class="card-title text-primary"><i class="fa-solid fa-location-dot"></i> Address</h5>
                      <p class="text-14">
                        <span id="agentFullAddress">Loading...</span>
                        <button class="btn btn-link btn-sm" onclick="editAgentAddress()">
                          <i class="fa-solid fa-pen"></i>
                        </button>
                      </p>
                    </div>
                  </div>
                </div><!-- end col-md-6 -->
              </div><!-- end row -->
            </div><!-- end profileContent -->
          </div>
          <!-- Change Password Tab -->
          <div class="tab-pane fade mt-4" id="changePasswordPane" role="tabpanel" aria-labelledby="change-password-tab">
            <p class="mb-4 text-secondary text-14">
              <i class="fa-solid fa-angle-right"></i>
              <span class="text-primary">Change Password</span>
            </p>
            <div class="card bg-white p-4 mt-4">
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
    </div><!-- end main-com -->
  </div><!-- end d-flex -->

  <script>
    // Global variable to store current address fields and agent UID
    let currentAddress = {};
    const agentUID = sessionStorage.getItem("agent_uid");
    console.log("Agent UID:", agentUID); // Debug: Check if agentUID is set

    // Ensure agent is authenticated
    document.addEventListener("DOMContentLoaded", function() {
      if (!sessionStorage.getItem("token") || !agentUID) {
        // Uncomment the following line to redirect if not authenticated
        // window.location.href = "/";
      } else {
        fetchAgentData(agentUID);
      }
      // Load saved image from localStorage if available
      const savedImage = localStorage.getItem('agent_photo');
      if (savedImage) {
        document.getElementById("agentPhoto").src = savedImage;
      }
    });

    // Toggle password visibility
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

    // Function to view the profile image in full using SweetAlert2
    function viewFullImage() {
      const imgSrc = document.getElementById("agentPhoto").src;
      Swal.fire({
        imageUrl: imgSrc,
        imageAlt: 'Agent Profile Picture',
        showCloseButton: true,
        showConfirmButton: false,
        width: '300px'
      });
    }

    // Fetch agent data from API using agent_uid
    function fetchAgentData(uid) {
      fetch('/api/agent/details?agent_uid=' + uid, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Authorization': sessionStorage.getItem('token')
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.status === 'success') {
          populateProfile(data.data);
        } else {
          Swal.fire('Error', data.message || 'Failed to fetch agent data.', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'An error occurred while fetching data.', 'error');
      });
    }

    // Populate the profile page with agent data
    function populateProfile(agent) {
      document.getElementById("agentName").innerText = agent.name;
      document.getElementById("agentEmail").innerHTML = `<i class="fa-regular fa-envelope"></i> ${agent.email}`;
      document.getElementById("agentPhone").innerHTML = `<i class="fa-solid fa-phone"></i> ${agent.mobile}`;
      document.getElementById("agentDesignation").innerText = agent.designation || "N/A";
      document.getElementById("agentStatus").innerText = agent.status || "N/A";
      document.getElementById("agentPan").innerText = agent.pan || "N/A";
      
      const fullAddress = `${agent.street || ""}, ${agent.post_office || ""}, ${agent.police_station || ""}, ${agent.city || ""}, ${agent.state || ""}, ${agent.country || ""}, ${agent.pincode || ""}`;
      document.getElementById("agentFullAddress").innerText = fullAddress;

      currentAddress = {
        street: agent.street || "",
        post_office: agent.post_office || "",
        police_station: agent.police_station || "",
        city: agent.city || "",
        state: agent.state || "",
        country: agent.country || "",
        pincode: agent.pincode || ""
      };

      if (agent.image) {
        document.getElementById("agentPhoto").src = agent.image;
      }
    }

    // Function to update agent photo (sends image and agent_uid via FormData)
    function updateAgentPhoto() {
      // Clear any previously stored temporary image data
      window.uploadedImageBase64 = null;
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
                // Temporarily store the image data in a variable, not in localStorage yet.
                window.uploadedImageBase64 = e.target.result;
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
          // Create a FormData object and append the file and agent_uid
          let formData = new FormData();
          formData.append('agent_uid', agentUID);
          formData.append('image', result.value);

          console.log("Uploading image with agent_uid:", agentUID);
          Swal.fire({
            title: 'Updating Profile Picture',
            text: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });

          // Send FormData directly without JSON.stringify
          fetch('/api/agent/edit', {
            method: 'PUT',
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
              fetchAgentData(agentUID);
              // Only store the image in localStorage if the response is successful.
              if(window.uploadedImageBase64) {
                localStorage.setItem('agent_photo', window.uploadedImageBase64);
              }
            } else {
              Swal.fire('Error', data.message || 'Failed to update profile picture.', 'error');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'An error occurred while updating.', 'error');
          });
        }
      });
    }

    // Function to edit agent email (sends only agent_uid and email)
    function editAgentEmail() {
      const currentEmailText = document.getElementById("agentEmail").innerText;
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
          fetch('/api/agent/edit', {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'Authorization': sessionStorage.getItem('token'),
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              agent_uid: agentUID,
              email: result.value
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              Swal.fire('Success', 'Email updated successfully!', 'success');
              fetchAgentData(agentUID);
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

    // Function to edit agent phone (sends only agent_uid and mobile)
    function editAgentPhone() {
      const currentPhone = document.getElementById('agentPhone').innerText.replace(/^\D+/,'').trim();
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
          fetch('/api/agent/edit', {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'Authorization': sessionStorage.getItem('token'),
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              agent_uid: agentUID,
              mobile: result.value.phone
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              Swal.fire('Success', 'Phone number updated successfully!', 'success');
              fetchAgentData(agentUID);
            } else {
              Swal.fire('Error', data.message || 'Update failed', 'error');
            }
          })
          .catch(error => {
            console.error(error);
            Swal.fire('Error', 'An error occurred while updating phone number.', 'error');
          });
        }
      });
    }

    // Function to edit agent address (sends only agent_uid and address fields)
    function editAgentAddress() {
      const addressOptions = `
        <div class="mb-2 text-start">
          <label class="form-label text-13">Street</label>
          <input type="text" id="swalStreet" class="form-control placeholder-12 text-13" value="${currentAddress.street}">
        </div>
        <div class="mb-2 text-start">
          <label class="form-label text-13">Post Office</label>
          <input type="text" id="swalPO" class="form-control placeholder-12 text-13" value="${currentAddress.post_office}">
        </div>
        <div class="mb-2 text-start">
          <label class="form-label text-13">Police Station</label>
          <input type="text" id="swalPS" class="form-control placeholder-12 text-13" value="${currentAddress.police_station}">
        </div>
        <div class="mb-2 text-start">
          <label class="form-label text-13">City</label>
          <input type="text" id="swalCity" class="form-control placeholder-12 text-13" value="${currentAddress.city}">
        </div>
        <div class="mb-2 text-start">
          <label class="form-label text-13">State</label>
          <input type="text" id="swalState" class="form-control placeholder-12 text-13" value="${currentAddress.state}">
        </div>
        <div class="mb-2 text-start">
          <label class="form-label text-13">Country</label>
          <input type="text" id="swalCountry" class="form-control placeholder-12 text-13" value="${currentAddress.country}">
        </div>
        <div class="mb-2 text-start">
          <label class="form-label text-13">PIN</label>
          <input type="text" id="swalPin" class="form-control placeholder-12 text-13" value="${currentAddress.pincode}">
        </div>
        <div class="mb-2 form-check">
          <input type="checkbox" class="form-check-input" id="confirmAddressCheckbox">
          <label class="form-check-label text-13" for="confirmAddressCheckbox">Confirm Update</label>
        </div>
      `;
      Swal.fire({
        title: 'Edit Address',
        html: addressOptions,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Update',
        preConfirm: () => {
          if (!document.getElementById('confirmAddressCheckbox').checked) {
            Swal.showValidationMessage('Please confirm update by checking the box.');
          }
          const street = document.getElementById('swalStreet').value;
          const po = document.getElementById('swalPO').value;
          const ps = document.getElementById('swalPS').value;
          const city = document.getElementById('swalCity').value;
          const state = document.getElementById('swalState').value;
          const country = document.getElementById('swalCountry').value;
          const pin = document.getElementById('swalPin').value;
          if (!street || !po || !ps || !city || !state || !country || !pin) {
            Swal.showValidationMessage('Please fill out all address fields');
          }
          return { street, post_office: po, police_station: ps, city, state, country, pincode: pin };
        }
      }).then((result) => {
        if (result.isConfirmed) {
          fetch('/api/agent/edit', {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'Authorization': sessionStorage.getItem('token'),
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(Object.assign({ agent_uid: agentUID }, result.value))
          })
          .then(response => response.json())
          .then(data => {
            if (data.status === 'success') {
              Swal.fire('Success', 'Address updated successfully!', 'success');
              currentAddress = result.value;
              fetchAgentData(agentUID);
            } else {
              Swal.fire('Error', data.message || 'Update failed', 'error');
            }
          })
          .catch(error => {
            console.error(error);
            Swal.fire('Error', 'An error occurred while updating address.', 'error');
          });
        }
      });
    }

    // Change Password Form Submission for agent
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const currentPassword = document.getElementById('currentPassword').value;
      const newPassword = document.getElementById('newPassword').value;
      const confirmNewPassword = document.getElementById('confirmNewPassword').value;
      if (newPassword !== confirmNewPassword) {
        Swal.fire('Error', 'New password and confirmation do not match.', 'error');
        return;
      }
      document.getElementById('passwordUpdateSpinner').classList.remove('d-none');
      fetch('/api/agent/change-password', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': sessionStorage.getItem('token'),
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          agent_uid: agentUID,
          current_password: currentPassword,
          new_password: newPassword,
          new_password_confirmation: confirmNewPassword
        })
      })
      .then(response => response.json())
      .then(data => {
        document.getElementById('passwordUpdateSpinner').classList.add('d-none');
        if (data.status === 'success') {
          Swal.fire('Success', data.message, 'success');
          document.getElementById('changePasswordForm').reset();
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

    document.getElementById("confirmChangeCheckbox").addEventListener("change", function() {
      document.getElementById("updatePasswordBtn").disabled = !this.checked;
    });
  </script>
</body>
</html>
