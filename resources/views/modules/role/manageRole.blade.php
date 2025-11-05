<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Manage Institute Role</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/Components/manageRole.css') }}">
</head>
<body>

            <div class="container mt-4">
                <p class="mb-4 text-secondary text-14"><i class="fa-solid fa-angle-right"></i> <span class="text-primary admin_manage_role_text">Institute Role</span></p>
                
                <!-- Dropdown for Selecting Institution -->
                <div class="bg-white p-4 rounded">
                    <div class="row g-3 align-items-center">
                        <div class="col-12">
                            <select id="institutionDropdown" class="form-control text-13" required>
                                <option value="" disabled selected>Choose Institution</option>
                            </select>
                        </div>
                        <div class="col-md-1 col-sm-2 text-end">
                            <button type="button" id="manageRoleButton" style="display: none;" class="btn btn-outline-primary text-13 w-100" disabled>
                                <span id="manageRoleText"><i class="fa-solid fa-magnifying-glass"></i></span>
                                <span id="manageRoleSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </div>
                </div>
                
                
                <!-- Institution Role Management Table (Initially Hidden) -->
                <div id="roleTableContainer" class="mt-4 bg-white p-4 rounded d-none mb-5">
                    <p class="text-secondary text-14"><i class="fa-solid fa-angle-right"></i> <span class="text-primary">Institution Roles</span></p>
                    <!-- Search Bar & Add Role Button -->
                    <div class="row w-100 mb-3 align-items-center justify-content-between">
                        <div class="col-md-6 position-relative">
                            <input type="text" id="searchInput" class="form-control placeholder-14 text-13 ps-5" 
                                placeholder="Search roles by name, email, or designation..." onkeyup="filterRoles()">
                            <i class="fa-solid fa-search position-absolute text-secondary" 
                            style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
                        </div>
                        <div class="col-md-2 text-end">
                            <button class="btn btn-outline-primary text-13 w-100" onclick="createUser(institutionDropdown.value, '')">
                                <i class="fa-solid fa-plus"></i> Add Role
                            </button>
                        </div>
                    </div>
                   
                    <div class="table-responsive">
                        <table class="tableshowrole text-center">
                            <thead>
                                {{-- <tr>
                                    <th class="text-14 text-secondary">Role Name</th>
                                    <th class="text-14 text-secondary">Users</th>
                                </tr> --}}
                            </thead>
                            <tbody id="roleTableBody">
                                <!-- Dynamic role data will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Message to Select an Institution (Visible by Default) -->
                <div id="selectInstitutionMessage" class="mt-4 bg-white p-4 rounded text-center text-secondary text-14">
                    <div id="search_Data_div" class="p-4 bg-white rounded text-center d-flex flex-column justify-content-center align-items-center vh-50">
                        <img src="{{ asset('assets/web_assets/search.png') }}" alt="" style="width: 300px;">
                        <p class="fs-5">Select an Institute first</p>
                      </div>
                </div>
            </div>

            <div class="container mt-4 d-none mb-4" id="createRoleModal">
                <p class="mb-4 text-secondary text-14">Manage Role <i class="fa-solid fa-angle-right"></i> <span class="text-primary admin_manage_role_text">Add Role</span></p>
                <form id="createRoleForm" class="bg-white p-4 rounded">
                    <input type="hidden" id="institutionId">
        
                    <!-- Form Fields -->
                    <div class="row g-3">
                        <!-- Role Designation -->
                        <div class="col-md-6">
                            <label for="roleDesignation" class="form-label text-13">Designation <span class="text-danger">*</span></label>
                            <select id="roleDesignation" name="designation" class="form-control placeholder-14 text-13" required>
                                <option value="" disabled selected>Select Designation</option>
                                <option value="Principal">Principal</option>
                                <option value="Register">Register</option>
                                <option value="Accountant">Accountant</option>
                                <option value="Faculty">Faculty</option>
                            </select>
                        </div>
                        <!-- Name -->
                        <div class="col-md-6">
                            <label for="roleName" class="form-label text-13">Name <span class="text-danger">*</span></label>
                            <input type="text" id="roleName" name="name" class="form-control placeholder-14 text-13" placeholder="Enter Name" required>
                        </div>
                    </div>
        
                    <div class="row g-3 mt-2">
                        <!-- Organization Email -->
                        <div class="col-md-6">
                            <label for="roleOrgEmail" class="form-label text-13">Organization Email <span class="text-danger">*</span></label>
                            <input type="email" id="roleOrgEmail" name="org_email" class="form-control placeholder-14 text-13" placeholder="Enter Organization Email" required>
                        </div>
                        <!-- Personal Email -->
                        <div class="col-md-6">
                            <label for="rolePersonalEmail" class="form-label text-13">Personal Email</label>
                            <input type="email" id="rolePersonalEmail" name="personal_email" class="form-control placeholder-14 text-13" placeholder="Enter Personal Email">
                        </div>
                    </div>
        
                    <div class="row g-3 mt-2">
                        <!-- Official Phone -->
                        <div class="col-md-6">
                            <label for="roleOfficialPhone" class="form-label text-13">Official Phone No <span class="text-danger">*</span></label>
                            <input type="number" id="roleOfficialPhone" name="official_phone" class="form-control placeholder-14 text-13" placeholder="Enter Official Phone No" required>
                        </div>
                        <!-- WhatsApp Number -->
                        <div class="col-md-6">
                            <label for="roleWhatsappNo" class="form-label text-13">WhatsApp No</label>
                            <input type="number" id="roleWhatsappNo" name="whatsapp_no" class="form-control placeholder-14 text-13" placeholder="Enter WhatsApp No">
                        </div>
                    </div>
        
                    <div class="row g-3 mt-2">
                        <!-- Personal Phone -->
                        <div class="col-md-6">
                            <label for="rolePersonalPhone" class="form-label text-13">Personal Phone</label>
                            <input type="number" id="rolePersonalPhone" name="personal_phone" class="form-control placeholder-14 text-13" placeholder="Enter Personal Phone">
                        </div>
                        <!-- Personal WhatsApp -->
                        <div class="col-md-6">
                            <label for="rolePersonalWhatsapp" class="form-label text-13">
                                Personal WhatsApp 
                            </label>
                            <input type="number" id="rolePersonalWhatsapp" name="personal_whatsapp" class="form-control placeholder-14 text-13" placeholder="Enter Personal WhatsApp">
                            <div><input type="checkbox" id="sameAsPersonalPhone" class="ms-2">
                                <small class="text-secondary">Same as Personal Phone</small></div>
                        </div>
                    </div>
        
                    <!-- Submit Button -->

                    <div class="mt-4 text-end">
                        <div class="mt-3 mb-2">
                            <input type="checkbox" id="confirmCheckbox" class="me-2">
                            <label for="confirmCheckbox" class="text-secondary">I confirm the details are correct</label>
                        </div>
                        <button type="submit" id="submitButton" class="btn btn-outline-primary text-13" disabled>
                            <span id="buttonText">Assign Role</span>
                            <span id="buttonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                        <button type="button" class="btn btn-outline-danger text-13 ms-2" onclick="cancelAddRole()">Cancel</button>
                    </div>
                </form>
            </div>
            <div class="container mt-4 d-none mb-4" id="editRoleModal">
                <p class="mb-4 text-secondary text-14">
                    Manage Role <i class="fa-solid fa-angle-right"></i>
                    <span class="text-primary admin_manage_role_text">Edit Role</span>
                </p>
                
                <form id="editRoleForm" class="bg-white p-4 rounded">
                    <input type="hidden" id="editInstitutionId">
                    <input type="hidden" id="editRoleId"> <!-- For edit -->
            
                    <!-- Form Fields -->
                    <div class="row g-3">
                        <!-- Role Designation -->
                        <div class="col-md-6">
                            <label for="editRoleDesignation" class="form-label text-13">Designation <span class="text-danger">*</span></label>
                            <select id="editRoleDesignation" name="designation" class="form-control placeholder-14 text-13" required>
                                <option value="" disabled selected>Select Designation</option>
                                <option value="Principal">Principal</option>
                                <option value="Register">Register</option>
                                <option value="Accountant">Accountant</option>
                                <option value="Faculty">Faculty</option>
                            </select>
                        </div>
                        <!-- Name -->
                        <div class="col-md-6">
                            <label for="editRoleName" class="form-label text-13">Name <span class="text-danger">*</span></label>
                            <input type="text" id="editRoleName" name="name" class="form-control placeholder-14 text-13" placeholder="Enter Name" required>
                        </div>
                    </div>
            
                    <div class="row g-3 mt-2">
                        <!-- Organization Email -->
                        <div class="col-md-6">
                            <label for="editRoleOrgEmail" class="form-label text-13">Organization Email <span class="text-danger">*</span></label>
                            <input type="email" id="editRoleOrgEmail" name="org_email" class="form-control placeholder-14 text-13" placeholder="Enter Organization Email" required>
                        </div>
                        <!-- Personal Email -->
                        <div class="col-md-6">
                            <label for="editRolePersonalEmail" class="form-label text-13">Personal Email</label>
                            <input type="email" id="editRolePersonalEmail" name="personal_email" class="form-control placeholder-14 text-13" placeholder="Enter Personal Email">
                        </div>
                    </div>
            
                    <div class="row g-3 mt-2">
                        <!-- Official Phone -->
                        <div class="col-md-6">
                            <label for="editRoleOfficialPhone" class="form-label text-13">Official Phone No <span class="text-danger">*</span></label>
                            <input type="number" id="editRoleOfficialPhone" name="official_phone" class="form-control placeholder-14 text-13" placeholder="Enter Official Phone No" required>
                        </div>
                        <!-- WhatsApp Number -->
                        <div class="col-md-6">
                            <label for="editRoleWhatsappNo" class="form-label text-13">WhatsApp No</label>
                            <input type="number" id="editRoleWhatsappNo" name="whatsapp_no" class="form-control placeholder-14 text-13" placeholder="Enter WhatsApp No">
                        </div>
                    </div>
            
                    <div class="row g-3 mt-2">
                        <!-- Personal Phone -->
                        <div class="col-md-6">
                            <label for="editRolePersonalPhone" class="form-label text-13">Personal Phone</label>
                            <input type="number" id="editRolePersonalPhone" name="personal_phone" class="form-control placeholder-14 text-13" placeholder="Enter Personal Phone">
                        </div>
                        <!-- Personal WhatsApp -->
                        <div class="col-md-6">
                            <label for="editRolePersonalWhatsapp" class="form-label text-13">Personal WhatsApp</label>
                            <input type="number" id="editRolePersonalWhatsapp" name="personal_whatsapp" class="form-control placeholder-14 text-13" placeholder="Enter Personal WhatsApp">
                            <div>
                                <input type="checkbox" id="editSameAsPersonalPhone" class="ms-2">
                                <small class="text-secondary">Same as Personal Phone</small>
                            </div>
                        </div>
                    </div>
            
                    <!-- Submit Button -->
                    <div class="mt-4 text-end">
                        <div class="mt-3 mb-2">
                            <input type="checkbox" id="editConfirmCheckbox" class="me-2">
                            <label for="editConfirmCheckbox" class="text-secondary">I confirm the details are correct</label>
                        </div>
                        <button type="submit" id="editSubmitButton" class="btn btn-outline-primary text-13" disabled>
                            <span id="editButtonText">Update Role</span>
                            <span id="editButtonSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        </button>
                        <button type="button" class="btn btn-outline-danger text-13 ms-2" onclick="cancelEditRole()">Cancel</button>
                    </div>
                </form>
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
        const token = sessionStorage.getItem('token');

        function filterRoles() {
    let searchInput = document.getElementById("searchInput").value.toLowerCase();
    let tableRows = document.querySelectorAll("#roleTableBody tr");

    tableRows.forEach(row => {
        let name = row.cells[0]?.textContent.toLowerCase() || "";
        let email = row.cells[1]?.textContent.toLowerCase() || "";
        let designation = row.closest("tr").previousElementSibling?.cells[0]?.textContent.toLowerCase() || "";

        if (name.includes(searchInput) || email.includes(searchInput) || designation.includes(searchInput)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
}

        document.getElementById("confirmCheckbox").addEventListener("change", function () {
            document.getElementById("submitButton").disabled = !this.checked;
        });
        document.getElementById("editConfirmCheckbox").addEventListener("change", function () {
            document.getElementById("submitButton").disabled = !this.checked;
        });
        document.getElementById("sameAsPersonalPhone").addEventListener("change", function () {
                const personalPhoneInput = document.getElementById("rolePersonalPhone");
                const personalWhatsAppInput = document.getElementById("rolePersonalWhatsapp");

                if (this.checked) {
                    personalWhatsAppInput.value = personalPhoneInput.value;
                    personalWhatsAppInput.setAttribute("readonly", true);
                } else {
                    personalWhatsAppInput.value = "";
                    personalWhatsAppInput.removeAttribute("readonly");
                }
        });
        document.addEventListener("DOMContentLoaded", function () {
            fetchInstitutions();

            document.getElementById("institutionDropdown").addEventListener("change", function () {
                if(this.value){
                    fetchInstitutionRoles(this.value);
                }
            });
        });

        function fetchInstitutions() {
            fetch("/api/view-institutions", {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `${token}`

                }
            })
            .then(response => {
                    if (response.status === 401 || response.status === 403) {
                        // ✅ Redirect if unauthorized
                        window.location.href = '/Unauthorised';
                        throw new Error('Unauthorized Access');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === "success") {
                        const dropdown = document.getElementById("institutionDropdown");
                        data.data
                            .filter(inst => inst.status === "Active")
                            .forEach(inst => {
                                let option = document.createElement("option");
                                option.value = inst.id.$oid;
                                option.textContent = inst.institution_name;
                                dropdown.appendChild(option);
                            });
                    }
                })
                .catch(error => console.error("Error fetching institutions:", error));
        }

        function fetchInstitutionRoles(institutionId) {
    fetch(`/api/institution/${institutionId}/roles`, {
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
        const roleTableBody = document.getElementById("roleTableBody");
        const roleTableContainer = document.getElementById("roleTableContainer");
        const selectInstitutionMessage = document.getElementById("selectInstitutionMessage");

        roleTableBody.innerHTML = "";

        if (data.status === "success" && data.data.length > 0) {
            selectInstitutionMessage.classList.add("d-none");
            roleTableContainer.classList.remove("d-none");

            const roleGroups = {};
            data.data.forEach(user => {
                if (!roleGroups[user.designation]) {
                    roleGroups[user.designation] = { active: [], inactive: [] };
                }
                if (user.status === "Active") {
                    roleGroups[user.designation].active.push(user);
                } else {
                    roleGroups[user.designation].inactive.push(user);
                }
            });

            // Sort roles in the specific order: Principal, Register, Accountant, Faculty
            const roleOrder = ["Principal", "Register", "Accountant", "Faculty"];
            const sortedRoles = Object.keys(roleGroups)
                .filter(r => roleOrder.includes(r))
                .sort((a, b) => roleOrder.indexOf(a) - roleOrder.indexOf(b));

            sortedRoles.forEach(role => {
                let row = `
                    <tr style="background-color: #dee2e6; box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);">
                        <td class="text-13 text-start">${role}</td>
                        <td colspan="6" class="text-end">
                            <button class="btn btn-outline-secondary btn-sm me-2" onclick="fetchOldData('${institutionId}', '${role}')">Old Data</button>
                            <button class="btn btn-outline-primary btn-sm" onclick="createUser('${institutionId}', '${role}')">Create</button>
                        </td>
                    </tr>
                `;

                if (roleGroups[role].active.length === 0) {
                    row += `
                        <tr>
                            <td colspan="7" class="text-center text-secondary">No current active ${role}</td>
                        </tr>
                    `;
                }

                roleGroups[role].active.forEach(user => {
                    row += `
                        <tr style="background-color: #ffffff; transition: 0.3s ease-in-out;">
                            <td class="text-13 text-start">${user.name}</td>
                            <td class="text-13 text-start">${user.org_email}</td>
                            <td class="text-13 text-center">
                                <div class="position-relative d-flex align-items-center" style="width: 180px;">
                                    <input type="password" id="passwordField_${user.id.$oid}" class="form-control text-13" value="${user.plain_password}" readonly style="max-width: 150px;">
                                    <span class="position-absolute end-0 me-2 toggle-password" onclick="togglePassword('${user.id.$oid}')">
                                        <i class="fa-solid fa-eye-slash"></i>
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge text-success">${user.status}</span>
                            </td>
                            <td class="text-13 text-center">
                                <span>${new Date(user.created_at).toLocaleString()}</span>
                            </td>
                            <td class="text-13 text-end">
                                <button class="btn btn-sm btn-outline-primary text-13 m-1" onclick="viewUserDetails(
                                    '${user.id.$oid}',
                                    '${user.name}',
                                    '${user.designation}',
                                    '${user.org_email}',
                                    '${user.personal_email}',
                                    '${user.official_phone}',
                                    '${user.whatsapp_no}',
                                    '${user.personal_phone}',
                                    '${user.personal_whatsapp}',
                                    '${user.status}',
                                    '${new Date(user.created_at).toLocaleString()}'
                                )"><i class="fa-solid fa-eye"></i></button>
                                <button class="btn btn-sm btn-outline-secondary text-13 m-1" onclick="editUser(
                                    '${user.id.$oid}',
                                    '${user.institution_id}',
                                    '${user.name}',
                                    '${user.designation}',
                                    '${user.org_email}',
                                    '${user.personal_email}',
                                    '${user.official_phone}',
                                    '${user.whatsapp_no}',
                                    '${user.personal_phone}',
                                    '${user.personal_whatsapp}'
                                )"><i class="fa-solid fa-pen-to-square"></i></button>
                                <button id="toggleUserBtn_${user.id.$oid}" class="btn btn-sm btn-outline-danger text-13 m-1" onclick="toggleUserStatus(
                                    '${user.id.$oid}',
                                    '${user.status}',
                                    '${institutionId}',
                                    '${user.designation}'
                                )"><i class="fa-solid fa-ban"></i></button>
                            </td>
                        </tr>
                    `;
                });

                roleTableBody.innerHTML += row;
            });
        } else {
            roleTableContainer.classList.add("d-none");
            selectInstitutionMessage.classList.remove("d-none");
            selectInstitutionMessage.innerHTML = `
                <i class="fa-solid fa-exclamation-circle"></i> No active users found for this institution.
                <br>
                <button class="btn btn-outline-primary mt-3" onclick="createUser('${institutionId}', '')">
                    <i class="fa-solid fa-plus"></i> Add Role
                </button>
            `;
        }
    })
    .catch(error => {
        console.error("Error fetching institution roles:", error);
        Swal.fire("Error", "Failed to fetch roles. Please try again.", "error");
    });
}


// Function to toggle password visibility
function togglePassword(userId) {
    const passwordField = document.getElementById(`passwordField_${userId}`);
    const toggleIcon = passwordField.nextElementSibling.querySelector("i");

    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIcon.classList.remove("fa-eye-slash");
        toggleIcon.classList.add("fa-eye");
    } else {
        passwordField.type = "password";
        toggleIcon.classList.remove("fa-eye");
        toggleIcon.classList.add("fa-eye-slash");
    }
}



function viewUserDetails(id, name, designation, org_email, personal_email, official_phone, whatsapp_no, personal_phone, personal_whatsapp, status, created_at, updated_at = null) {
    Swal.fire({
        title: `${name} - ${designation}`,
        html: `            
            <b>Personal Email:</b> ${personal_email || "N/A"} <br>
            <b>Official Phone:</b> ${official_phone} <br>
            <b>WhatsApp:</b> ${whatsapp_no || "N/A"} <br>
            <b>Personal Phone:</b> ${personal_phone || "N/A"} <br>
            <b>Personal WhatsApp:</b> ${personal_whatsapp || "N/A"} <br>
            <b>Created At:</b> ${created_at} <br>
            <b>Updated At:</b> ${updated_at ? updated_at : "Never Updated"}             
        `,
        icon: "info",
        confirmButtonText: "Close",
    });
}


function fetchOldData(institutionId, role) {
    fetch(`/api/institution/${institutionId}/roles`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `${token}`

                }
            })
            .then(response => {
                if (response.status === 401 || response.status === 403) {
                    // ✅ Redirect if unauthorized
                    window.location.href = '/Unauthorised';
                    throw new Error('Unauthorized Access');
                }
                return response.json();
            })
        .then(data => {
            const roleTableBody = document.getElementById("roleTableBody");
            const roleTableContainer = document.getElementById("roleTableContainer");
            const selectInstitutionMessage = document.getElementById("selectInstitutionMessage");

            roleTableBody.innerHTML = ""; // Clear existing data

            if (data.status === "success") {
                // Filter inactive users based on role
                const inactiveUsers = data.data.filter(user => user.designation === role && user.status !== "Active");

                if (inactiveUsers.length > 0) {
                    selectInstitutionMessage.classList.add("d-none");
                    roleTableContainer.classList.remove("d-none");

                    let row = `
                        <tr style="background-color: #ffc107; box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);">
                            <td class="text-13 text-start">Inactive - ${role}</td>
                            <td colspan="6" class="text-end">
                                <button class="btn btn-danger btn-sm me-2" onclick="fetchInstitutionRoles('${institutionId}')">
                                    <i class="fa-solid fa-times"></i> Back
                                </button>
                            </td>
                        </tr>
                    `;

                    inactiveUsers.forEach(user => {
                        row += `
                            <tr style="background-color: #ffffff; transition: 0.3s ease-in-out;">
                                <td class="text-13 text-start">${user.name}</td>
                                <td class="text-13 text-start">${user.org_email}</td>
                                <td class="text-13 text-center">
                                    <button class="btn btn-sm btn-outline-primary text-13" onclick="viewUserDetails('${user.id.$oid}', '${user.name}', '${user.designation}', '${user.org_email}', '${user.personal_email}', '${user.official_phone}', '${user.whatsapp_no}', '${user.personal_phone}', '${user.personal_whatsapp}', '${user.status}', '${new Date(user.created_at).toLocaleString()}', '${new Date(user.updated_at ).toLocaleString()}')">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </td>
                                
                                <td class=" text-center">
                                    <span class="badge text-danger">${user.status}</span>
                                </td>
                                <td class="text-13 text-center">
                                    <span>${new Date(user.created_at).toLocaleString()}</span>
                                </td>
                                <td class="text-13 text-end">
                                    <button class="btn btn-sm btn-outline-secondary text-13 m-1" 
                                         onclick="editUser('${user.id.$oid}', '${user.institution_id}', '${user.name}', '${user.designation}', '${user.org_email}', '${user.personal_email}', '${user.official_phone}', '${user.whatsapp_no}', '${user.personal_phone}', '${user.personal_whatsapp}')">
                                         <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button id="activateUserBtn_${user.id.$oid}" 
                                            class="btn btn-sm btn-outline-success text-13"
                                            onclick="toggleUserStatus('${user.id.$oid}', '${user.status}','${institutionId}','${user.designation}')">
                                        <i class="fa-solid fa-power-off"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });

                    roleTableBody.innerHTML = row;
                } else {
                    roleTableContainer.classList.add("d-none");
                    selectInstitutionMessage.classList.remove("d-none");
                    selectInstitutionMessage.innerHTML = ` 
                        <i class="fa-solid fa-exclamation-circle"></i> No inactive users found for ${role}.
                        <br>
                        <button class="btn btn-outline-primary mt-3" onclick="fetchInstitutionRoles('${institutionId}')">
                            <i class="fa-solid fa-times"></i> Back
                        </button>
                    `;
                }
            }
        })
        .catch(error => {
            console.error("Error fetching inactive users:", error);
            Swal.fire("Error", "Failed to fetch inactive users. Please try again.", "error");
        });
}



function createUser(institutionId, role) {
    // Hide the role table
    document.getElementById("roleTableContainer").classList.add("d-none");
    document.getElementById("selectInstitutionMessage").classList.add("d-none")

    // Show the form
    document.getElementById("createRoleModal").classList.remove("d-none");

    // Set institution ID
    document.getElementById("institutionId").value = institutionId;

    // Set the designation dropdown value
    let roleDropdown = document.getElementById("roleDesignation");
    let found = false;
    for (let i = 0; i < roleDropdown.options.length; i++) {
        if (roleDropdown.options[i].value === role) {
            roleDropdown.selectedIndex = i;
            found = true;
            break;
        }
    }

    // If the role is not found, reset dropdown to default
    if (!found) {
        roleDropdown.selectedIndex = 0;
    }
}


document.getElementById("createRoleForm").addEventListener("submit", function (event) {
    event.preventDefault();

    const submitButton = document.querySelector("#createRoleForm button[type='submit']");
    const buttonText = document.getElementById("buttonText");
    const buttonSpinner = document.getElementById("buttonSpinner");

    // Disable button and show spinner
    submitButton.disabled = true;
    buttonText.classList.add("d-none");
    buttonSpinner.classList.remove("d-none");

    // Collect form data
    const formData = {
        institution_id: document.getElementById("institutionId").value,
        designation: document.getElementById("roleDesignation").value,
        name: document.getElementById("roleName").value,
        org_email: document.getElementById("roleOrgEmail").value,
        personal_email: document.getElementById("rolePersonalEmail").value,
        official_phone: document.getElementById("roleOfficialPhone").value,
        whatsapp_no: document.getElementById("roleWhatsappNo").value,
        personal_phone: document.getElementById("rolePersonalPhone").value,
        personal_whatsapp: document.getElementById("rolePersonalWhatsapp").value
    };

    // Validate required fields
    if (!formData.name || !formData.org_email || !formData.official_phone) {
        Swal.fire("Error", "Please fill all required fields!", "error");
        submitButton.disabled = false;
        buttonText.classList.remove("d-none");
        buttonSpinner.classList.add("d-none");
        return;
    }

    // API call to assign role
    fetch("/api/assign-role", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': `${token}`
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        if (response.status === 401 || response.status === 403) {
            // ✅ Redirect if unauthorized
            window.location.href = '/Unauthorised';
            throw new Error('Unauthorized Access');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === "success") {
            Swal.fire("Success", "Role assigned successfully!", "success");
            document.getElementById("createRoleForm").reset(); // Reset form
            fetchInstitutionRoles(formData.institution_id);
            document.getElementById("createRoleModal").classList.add("d-none"); // Hide form
            document.getElementById("roleTableContainer").classList.remove("d-none"); // Show table
        } else {
            Swal.fire("Error", data.message, "error");
        }
    })
    .catch(error => {
        console.error("Error:", error);
        Swal.fire("Error", "Something went wrong. Please try again.", "error");
    })
    .finally(() => {
        // Enable button and hide spinner
        submitButton.disabled = false;
        buttonText.classList.remove("d-none");
        buttonSpinner.classList.add("d-none");
    });
});
function cancelAddRole() {
    Swal.fire({
        title: "Are you sure?",
        text: "Any unsaved data will be lost.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, Cancel",
        cancelButtonText: "No, Keep Editing"
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("createRoleForm").reset(); // Reset the form fields
            document.getElementById("createRoleModal").classList.add("d-none"); // Hide the form
            document.getElementById("roleTableContainer").classList.remove("d-none"); // Show the role table
        }
    });
}



function editUser(id, institution_id, name, designation, org_email, personal_email, official_phone, whatsapp_no, personal_phone, personal_whatsapp) {
    // Show the edit form and hide the role table
    document.getElementById("roleTableContainer").classList.add("d-none");
    document.getElementById("selectInstitutionMessage").classList.add("d-none");
    document.getElementById("editRoleModal").classList.remove("d-none");

    // Set form fields with existing data
    document.getElementById("editRoleId").value = id;
    document.getElementById("editInstitutionId").value = institution_id; // Set institution ID
    document.getElementById("editRoleName").value = name;
    document.getElementById("editRoleOrgEmail").value = org_email;
    document.getElementById("editRolePersonalEmail").value = personal_email || "";
    document.getElementById("editRoleOfficialPhone").value = official_phone;
    document.getElementById("editRoleWhatsappNo").value = whatsapp_no || "";
    document.getElementById("editRolePersonalPhone").value = personal_phone || "";
    document.getElementById("editRolePersonalWhatsapp").value = personal_whatsapp || "";

    // Set designation dropdown
    let roleDropdown = document.getElementById("editRoleDesignation");
    for (let i = 0; i < roleDropdown.options.length; i++) {
        if (roleDropdown.options[i].value === designation) {
            roleDropdown.selectedIndex = i;
            break;
        }
    }

    // Enable submit button only when checkbox is checked
    document.getElementById("editConfirmCheckbox").addEventListener("change", function () {
        document.getElementById("editSubmitButton").disabled = !this.checked;
    });

    document.getElementById("editRoleForm").setAttribute("onsubmit", `updateRole(event, '${id}')`);
}


function updateRole(event, id) {
    event.preventDefault();

    const submitButton = document.getElementById("editSubmitButton");
    const buttonText = document.getElementById("editButtonText");
    const buttonSpinner = document.getElementById("editButtonSpinner");

    submitButton.disabled = true;
    buttonText.classList.add("d-none");
    buttonSpinner.classList.remove("d-none");

    const formData = {
        institution_id: document.getElementById("editInstitutionId").value, // Include institution_id
        designation: document.getElementById("editRoleDesignation").value,
        name: document.getElementById("editRoleName").value,
        org_email: document.getElementById("editRoleOrgEmail").value,
        personal_email: document.getElementById("editRolePersonalEmail").value,
        official_phone: document.getElementById("editRoleOfficialPhone").value,
        whatsapp_no: document.getElementById("editRoleWhatsappNo").value,
        personal_phone: document.getElementById("editRolePersonalPhone").value,
        personal_whatsapp: document.getElementById("editRolePersonalWhatsapp").value
    };

    fetch(`/api/edit-role/${id}`, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': `${token}`

        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        if (response.status === 401 || response.status === 403) {
            // ✅ Redirect if unauthorized
            window.location.href = '/Unauthorised';
            throw new Error('Unauthorized Access');
        }
        return response.json();
    })
    .then(data => {
        Swal.fire("Success", "Role updated successfully!", "success");
        document.getElementById("editRoleForm").reset();
        fetchInstitutionRoles(formData.institution_id); // Ensure institution_id is passed
        document.getElementById("editRoleModal").classList.add("d-none");
        document.getElementById("roleTableContainer").classList.remove("d-none");
    })
    .catch(error => Swal.fire("Error", "Something went wrong. Please try again.", "error"))
    .finally(() => {
        submitButton.disabled = false;
        buttonText.classList.remove("d-none");
        buttonSpinner.classList.add("d-none");
    });
}


function cancelEditRole() {
    document.getElementById("editRoleForm").reset();
    document.getElementById("editRoleModal").classList.add("d-none");
    document.getElementById("roleTableContainer").classList.remove("d-none");
}


function toggleUserStatus(userId, currentStatus, institutionId, designation) {
    const action = currentStatus === "Active" ? "deactivate" : "activate";

    if (action === "activate" && (designation === "Principal" || designation === "Register")) {
        // First, check if an active Principal or Registrar already exists
        fetch(`/api/institution/${institutionId}/roles`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': `${token}`

                }
            })
            .then(response => {
                if (response.status === 401 || response.status === 403) {
                    // ✅ Redirect if unauthorized
                    window.location.href = '/Unauthorised';
                    throw new Error('Unauthorized Access');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === "success") {
                    const activeUsers = data.data.filter(user => user.designation === designation && user.status === "Active");

                    if (activeUsers.length > 0) {
                        Swal.fire({
                            title: "Warning",
                            text: `An active ${designation} already exists. Please deactivate them first before assigning a new one.`,
                            icon: "warning",
                            confirmButtonText: "OK"
                        });
                        return; // Stop execution
                    } else {
                        proceedWithToggle(userId, action, institutionId);
                    }
                } else {
                    Swal.fire("Error", "Failed to verify existing active roles.", "error");
                }
            })
            .catch(error => {
                console.error("Error fetching roles:", error);
                Swal.fire("Error", "Something went wrong. Please try again.", "error");
            });
    } else {
        proceedWithToggle(userId, action, institutionId);
    }
}

function proceedWithToggle(userId, action, institutionId) {
    Swal.fire({
        title: `Are you sure?`,
        text: `Do you want to ${action} this user?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: `Yes, ${action}`,
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/toggle-user-status/${userId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Authorization': `${token}`
                }
            })
            .then(response => {
                if (response.status === 401 || response.status === 403) {
                    // ✅ Redirect if unauthorized
                    window.location.href = '/Unauthorised';
                    throw new Error('Unauthorized Access');
                }
                return response.json();
            })
            .then(data => {
                if (data.status === "success") {
                    Swal.fire({
                        title: "Success",
                        text: `User status updated to ${data.new_status}`,
                        icon: "success",
                        timer: 1500,
                        showConfirmButton: false
                    });
                    fetchInstitutionRoles(institutionId);
                } else {
                    Swal.fire("Error", data.message, "error");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                Swal.fire("Error", "Something went wrong. Please try again.", "error");
            });
        }
    });
}



    </script>
</body>
</html>
