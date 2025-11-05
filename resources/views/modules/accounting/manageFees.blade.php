<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>View Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/Components/manageFees.css') }}">
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
          
                <p class="my-4 text-secondary text-14"><i class="fa-solid fa-angle-right"></i> <span class="text-primary">Fees</span></p>

                 <!-- Institution Dropdown -->
                    <div class="bg-white p-4 rounded mb-4" id="institutionDropdownContainer">
                        <label for="institutionSelect" class="form-label text-13">Select Institution</label>
                        <select id="institutionSelect" class="form-select text-13">
                        <option value="" disabled selected>Loading institutions...</option>
                        </select>
                    </div>



                <div id="feesFormContainer" class="container mt-4 rounded bg-white d-none position-relative mb-5 pt-2">
                    <button class="btn btn-outline-danger position-absolute top-0 end-0 m-2 text-13" onclick="hideSettings()"><i class="fa-solid fa-x"></i></button>
                    {{-- <p class="mb-4 text-secondary text-14"><i class="fa-solid fa-angle-right"></i> <span class="text-primary">Add Fees structure</span></p>
                    <form id="feesForm" class=" p-3 rounded"  style="background-color: #f9fafc; box-shadow: rgba(0, 0, 0, 0.02) 0px 1px 3px 0px, rgba(27, 31, 35, 0.15) 0px 0px 0px 1px;">
                        
                
                        <!-- Dynamic Fields Header -->
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="text-secondary mb-0">Head of Account</h6>
                            <button type="button" id="addFeeHeadButton" class="btn btn-outline-primary btn-sm shadow-sm">
                                <i class="fa-regular fa-plus"></i> Add
                            </button>
                        </div>
                        
                
                        <!-- Dynamic Fields Container -->
                        <div id="dynamicFieldsContainer">
                            <!-- Dynamic fields will be appended here -->
                           
                        </div>

                        <div class="col-md-12 text-end">
                            <button type="button" id="submitFeesButton" class="btn btn-outline-secondary text-13 d-none">
                                Submit
                            </button>
                            <button type="button" id="canceladdFeesStructureButton" onclick="canceladdFeesStructure()" class="btn btn-outline-danger text-13 ms-2 d-none">
                                Cancel
                            </button>
                        </div>
                    </form> --}}
                    <!-- Existing Fees Table -->
                    <div id="feesTableContainer" class="mt-4" >
                    <p class="mb-4 text-secondary text-14"> <i class="fa-solid fa-angle-right"></i> <span class="text-primary">Fees structure</span></p>
                        <div id="default_feesstructure_div" class="p-4 bg-white rounded text-center d-flex flex-column justify-content-center align-items-center vh-50">
                            <img src="{{ asset('assets/web_assets/search.png') }}" alt="" style="width: 300px;">
                            <p class="fs-5">Select an Institute first</p>
                        </div>
                        <div class="table-responsive" >
                            <table class="table text-center" id="feesTable" >
                                <thead class="table-light">
                                    {{-- <tr>
                                        <th>Head of Account</th>
                                        <th>Type</th>
                                        <th>Actions</th>
                                    </tr> --}}
                                </thead>
                                <tbody>
                                    <!-- Dynamic rows will be appended here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                
                 <!-- Add Fees Form -->
                 <div id="addFeesFormContainer" class="container mt-4 mb-4 d-none">
                    <form id="addFeesForm" class="bg-white p-4 rounded">
                        <h5 class="text-primary mb-4 addfees_admin_text"></h5>
                        <input type="hidden" id="intakeYear" class="form-control text-13" readonly>
                        <!-- Fixed Inputs -->
                        <div class="row g-3 mb-2">
                            <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                                <label for="programCode" class="form-label text-13">Program Code</label>
                                <input type="text" id="programCode" class="form-control text-13" readonly>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                                <label for="intakeType" class="form-label text-13">Intake Type</label>
                                <input type="text" id="intakeType" class="form-control text-13" readonly>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                                <label for="duration" class="form-label text-13">Duration (Years)</label>
                                <input type="text" id="duration" class="form-control text-13" readonly>
                            </div>
                            <div class="col-lg-3 col-md-6 col-sm-6 col-6">
                                <label for="feeType" class="form-label text-13">Fee Type</label>
                                <input type="text" id="feeType" class="form-control text-13" readonly>
                            </div>
                        </div>


                        <!-- Semester Fields (Dynamic) -->
                        <div id="semesterFields" class="row g-3">
                            <!-- Dynamically populated fields go here -->
                        </div>
                        <!-- Total Fees, Confirmation Checkbox, Submit, and Cancel Button Alignment -->
                        <div class="mt-3 d-flex justify-content-between align-items-center">
                            <!-- Total Fees Display -->
                            <span class="fw-bold text-secondary">Total Fees: ₹ <span id="totalFeesDisplay" class="text-primary">0</span></span>

                            

                            <!-- Buttons -->
                            <div>
                                <!-- Confirmation Checkbox -->
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="confirmCheck">
                                <label class="form-check-label text-13" for="confirmCheck">
                                    I confirm the fee details
                                </label>
                            </div>
                                <button type="submit" id="saveFeesButton" class="btn btn-outline-primary text-13" disabled>Save</button>
                                <button type="button" id="cancelAddFeesButton" class="btn btn-outline-danger ms-2 text-13">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Edit Fees Form -->
                <div id="editFeesFormContainer" class="container mt-4 mb-4 d-none">
                    <form id="editFeesForm" class="bg-white p-4 rounded">
                        <h5 class="text-primary mb-4 editfees_admin_text"></h5>

                        <!-- Fixed Inputs -->
                        <div class="row g-3">
                            <div class="col-4">
                                <label for="editProgramCode" class="form-label text-13">Program Code</label>
                                <input type="text" id="editProgramCode" class="form-control text-13" readonly>
                            </div>
                            <div class="col-4">
                                <label for="editIntakeType" class="form-label text-13">Intake Type</label>
                                <input type="text" id="editIntakeType" class="form-control text-13" readonly>
                            </div>
                            <div class="col-4">
                                <label for="editFeeType" class="form-label text-13">Fee Type</label>
                                <input type="text" id="editFeeType" class="form-control text-13" readonly>
                            </div>
                        </div>

                        <!-- Dynamic Fields -->
                        <div id="editSemesterFields" class="row g-3 mt-3">
                            <!-- Existing semester fee fields will be populated here -->
                        </div>

                        <!-- Total Fees Display -->
                        <div class="mt-4 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-secondary">Total Fees: ₹ <span id="totalEditFeesDisplay" class="text-primary">0</span></span>

                            <!-- Confirmation Checkbox -->
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="editConfirmCheck">
                                <label class="form-check-label text-13" for="editConfirmCheck">
                                    I confirm the updated fee details
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-3 text-end">
                            <button type="submit" id="updateFeesButton" class="btn btn-outline-primary text-13">Update</button>
                            <button type="button" id="cancelEditFeesButton" onclick="hideEditForm()" class="btn btn-outline-danger ms-2 text-13">Cancel</button>
                        </div>
                    </form>
                </div>
                <!-- Loading Spinner -->
                <div id="coursesLoading" class="text-center my-4 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-secondary mt-2">Fetching courses...</p>
                </div>

                <!-- Table -->
                    <div  id="coursesTableContainer">
                        <!-- Settings Button -->
                        <div class="d-flex justify-content-between">
                            {{-- <select id="courseTypeDropdown" class=" text-13 text-secondary w-auto courseTypeDropdown_addfees">
                                <option value="" selected>All</option>
                                <option value="General">General</option>
                                <option value="Lateral">Lateral</option>
                            </select> --}}
                            <button id="refreshButton" class="settingsButton_addfees m4-3">
                                <i class="fa-solid fa-arrows-rotate"></i>
                            </button>
                            <div>
                                 <!-- Refresh Button -->
                            <button id="settingsButton" onclick="showSettings()" class="settingsButton_addfees"><i class="fa-solid fa-gear"></i></button>

                            </div>
                        </div>
                        <div class="table-responsive bg-white  p-4 rounded-bottom shadow-sm">
                            <div id="coursesLoading" class="d-none">Loading...</div>
                            <div id="coursesAccordion"></div>                            
                        </div>
                    </div>
            </div>
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

         // Function to fetch institutions from the API and populate the dropdown.
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

        // Enable or disable the submit button based on checkbox selection
    document.getElementById('confirmCheck').addEventListener('change', function() {
        document.getElementById('saveFeesButton').disabled = !this.checked;
    });
      // Enable or disable the submit button based on checkbox selection
      document.getElementById('editConfirmCheck').addEventListener('change', function() {
        document.getElementById('updateFeesButton').disabled = !this.checked;
    });

    document.addEventListener("DOMContentLoaded", () => {

    fetchInstitutions();
    document.getElementById('institutionSelect').addEventListener('change', function() {
    institute_id= document.getElementById('institutionSelect').value;
    console.log(institute_id)
    fetchFeesStructures(institute_id);
    fetchCourses();
    });


    const addFeeHeadButton = document.getElementById("addFeeHeadButton");
    const dynamicFieldsContainer = document.getElementById("dynamicFieldsContainer");
    const feesTable = document.getElementById("feesTable").querySelector("tbody");

    // Fetch and populate existing fee structures
    function fetchFeesStructures(instituteId) {
    // Pass the instituteId as a query parameter.
    fetch(`/api/view-fees-structure?institute_id=${encodeURIComponent(instituteId)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Authorization': `${token}`
        }
    })
    .then(response => {
        if (response.status === 401 || response.status === 403) {
            // Redirect if unauthorized.
            window.location.href = '/Unauthorised';
            throw new Error('Unauthorized Access');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === "success") {
            populateFeesTable(data.data);
        } else {
            console.error("Error fetching fees structure");
        }
    })
    .catch(error => console.error("Error:", error));
}


    function populateFeesTable(fees) {
    feesTable.innerHTML = ""; // Clear the table
    document.getElementById("default_feesstructure_div").classList.add("d-none");
    
    if (fees.length === 0) {
        // If no fees are available, display a message
        const noFeesRow = `
            <tr>
                <div class="bg-white text-center">
                    <img src="{{ asset('assets/web_assets/noData.png') }}" alt="">
                    <br>
                    No Fee Structure Found.
                </div>
            </tr>
        `;
        feesTable.innerHTML = noFeesRow;
        return;
    }
    fees.forEach(fee => {
        const row = document.createElement("tr");
        row.innerHTML = `
                    <td class="text-13 align-middle text-start">
                        ${fee.head_of_account}
                    </td>
                    <td class="text-13 align-middle text-start">
                        <div class="d-flex">
                            <select 
                                class="form-control form-control-sm updateType text-13 w-auto" 
                                data-id="${fee.id.$oid}" 
                                disabled>
                                <option value="one-time" ${fee.type === "one-time" ? "selected" : ""}>One Time</option>
                                <option value="semester-wise" ${fee.type === "semester-wise" ? "selected" : ""}>Semester Wise</option>
                                <option value="other" ${fee.type === "other" ? "selected" : ""}>Other</option>
                            </select>
                        </div>
                    </td>
                    <td class="text-13 align-middle text-end">
                        <span class="fee-status ${fee.status === 'Active' ? 'text-success' : 'text-danger'}" data-id="${fee.id.$oid}">
                            ${fee.status}
                        </span>
                    </td>
                    </td>
                `;
        // row.innerHTML = `
        //             <td class="text-13 align-middle text-start">
        //                 ${fee.head_of_account}
        //             </td>
        //             <td class="text-13 align-middle text-center">
        //                 <div class="d-flex justify-content-center align-items-center">
        //                     <select 
        //                         class="form-control form-control-sm updateType text-13 w-auto" 
        //                         data-id="${fee.id.$oid}" 
        //                         disabled>
        //                         <option value="one-time" ${fee.type === "one-time" ? "selected" : ""}>One Time</option>
        //                         <option value="semester-wise" ${fee.type === "semester-wise" ? "selected" : ""}>Semester Wise</option>
        //                         <option value="other" ${fee.type === "other" ? "selected" : ""}>Other</option>
        //                     </select>
        //                 </div>
        //             </td>
        //             <td class="text-13 align-middle text-start">
        //                 <span class="fee-status ${fee.status === 'Active' ? 'text-success' : 'text-danger'}" data-id="${fee.id.$oid}">
        //                     ${fee.status}
        //                 </span>
        //             </td>
        //             <td class="text-13 align-middle text-end">
        //                 <div class="d-flex justify-content-end align-items-center gap-2">
        //                     <button 
        //                         class="btn btn-outline-primary editButton text-13" 
        //                         data-id="${fee.id.$oid}">
        //                         <i class="fa-regular fa-pen-to-square me-1"></i>
        //                     </button>
        //                     <button 
        //                         class="btn btn-outline-success saveButton text-13 d-none " 
        //                         onclick="editFees('${fee.id.$oid}')"
        //                         data-id="${fee.id.$oid}">
        //                         <i class="fa-regular fa-save me-1"></i>
        //                     </button>
        //                     <button 
        //                         class="btn toggleStatusButton text-13" 
        //                         data-id="${fee.id.$oid}"
        //                         onclick="toggleFeeStructureStatus('${fee.id.$oid}', '${fee.status}')"
        //                         style="color: ${fee.status === 'Active' ? 'red' : 'green'}; border-color: ${fee.status === 'Active' ? 'red' : 'green'};">
        //                         <i class="fa-solid ${fee.status === 'Active' ? 'fa-ban' : 'fa-power-off'} me-1"></i> 
        //                     </button>
        //                 </div>
        //             </td>
        //         `;
                feesTable.appendChild(row);

    });

    // Add event listeners for Edit buttons
    document.querySelectorAll(".editButton").forEach(button => {
        button.addEventListener("click", event => {
            const feeId = event.target.getAttribute("data-id");
            const typeDropdown = document.querySelector(`select[data-id="${feeId}"]`);
            const saveButton = document.querySelector(`.saveButton[data-id="${feeId}"]`);
            const editButton = document.querySelector(`.editButton[data-id="${feeId}"]`);

            // Enable the dropdown and toggle buttons
            typeDropdown.disabled = false;
            saveButton.classList.remove("d-none");
            editButton.classList.add("d-none");
        });
    });
        // Add event listeners for Save buttons
        document.querySelectorAll(".saveTypeButton").forEach(button => {
            button.addEventListener("click", event => {
                const feeId = event.target.getAttribute("data-id");
                const typeDropdown = document.querySelector(`select[data-id="${feeId}"]`);
                const newType = typeDropdown.value;
                updateFeeType(feeId, newType);
            });
        });
    }


    // Add dynamic fields on button click
    addFeeHeadButton.addEventListener("click", () => {
        const newRow = document.createElement("div");
        newRow.classList.add("row", "g-3", "align-items-end", "mb-3");

        const headInput = `
            <div class="col-md-6">
                <label class="form-label text-13">Head of Account<span class="text-danger">*</span></label>
                <input type="text" class="form-control text-13" placeholder="Enter Head of Account" required>
            </div>
        `;

        const typeDropdown = `
            <div class="col-md-5">
                <label class="form-label text-13">Fees Type<span class="text-danger">*</span></label>
                <select class="form-control text-13" required>
                    <option value="" disabled selected>Select Type</option>
                    <option value="one-time">One Time</option>
                    <option value="semester-wise">Semester Wise</option>
                    <option value="other">Other</option>
                </select>
            </div>
        `;

        const removeButton = `
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-outline-danger btn-sm removeFieldButton text-13 w-100">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        `;
        document.getElementById("canceladdFeesStructureButton").classList.remove('d-none')
        document.getElementById("submitFeesButton").classList.remove('d-none')

        newRow.innerHTML = headInput + typeDropdown + removeButton;
        dynamicFieldsContainer.appendChild(newRow);

        const removeFieldButton = newRow.querySelector(".removeFieldButton");
        removeFieldButton.addEventListener("click", () => {
            dynamicFieldsContainer.removeChild(newRow);
        });
    });


// Submit new fee structures
document.getElementById("submitFeesButton").addEventListener("click", () => {
    const allFields = [];

    const rows = dynamicFieldsContainer.querySelectorAll(".row");
    const institution_id = document.getElementById('institutionSelect').value;
    console.log("Selected Institution ID:", institution_id);
    
    rows.forEach(row => {
        const headOfAccount = row.querySelector("input").value;
        const feesType = row.querySelector("select").value;

        if (headOfAccount && feesType) {
            allFields.push({
                head_of_account: headOfAccount,
                type: feesType,
            });
        }
    });

    if (allFields.length === 0) {
        Swal.fire({
            title: 'Error',
            text: 'Please add at least one fee head.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Send the institute_id as a top-level field along with the fees array.
    fetch("/api/add-fees-structure", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            "Authorization": `${token}`
        },
        body: JSON.stringify({
            institute_id: institution_id,
            fees: allFields
        }),
    })
    .then(response => {
        if (response.status === 401 || response.status === 403) {
            // Redirect if unauthorized
            window.location.href = '/Unauthorised';
            throw new Error('Unauthorized Access');
        }
        return response.json();
    })
    .then(data => {
        if (data.status === "success") {
            Swal.fire({
                title: 'Success',
                text: data.message,
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                dynamicFieldsContainer.innerHTML = ""; // Clear fields
                fetchFeesStructures(institution_id); // Refresh table
            });
            document.getElementById("canceladdFeesStructureButton").classList.add('d-none');
            document.getElementById("submitFeesButton").classList.add('d-none');
        } else {
            Swal.fire({
                title: 'Error',
                text: data.message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        console.error("Error submitting fees:", error);
        Swal.fire({
            title: 'Error',
            text: 'An error occurred while submitting the form.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
});
// institute_id= document.getElementById('institutionSelect').value;
// Fetch existing fees on page load
// fetchFeesStructures(institute_id);

});
function canceladdFeesStructure(){
    dynamicFieldsContainer.innerHTML = "";
    document.getElementById("canceladdFeesStructureButton").classList.add('d-none')
    document.getElementById("submitFeesButton").classList.add('d-none')
}

function editFees(feesId) {
    // Get the selected type value from the dropdown
    const selectedType = document.querySelector(`.updateType[data-id="${feesId}"]`).value;

    // Make sure a type is selected
    if (!selectedType) {
        Swal.fire({
            title: 'Error',
            text: 'Please select a valid type before saving.',
            icon: 'error',
            confirmButtonText: 'OK',
        });
        return;
    }

    // Call the API
    fetch(`/api/edit-fee-structure/${feesId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': `${token}`
        },
        body: JSON.stringify({ type: selectedType }),
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
            if (data.status === 'success') {
                Swal.fire({
                    title: 'Success',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK',
                }).then(() => {
                    // Optionally refresh the table or data
                    fetchFeesStructures();
                });
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: error.message || 'An error occurred while updating the fee structure.',
                icon: 'error',
                confirmButtonText: 'OK',
            });
            console.error('Error updating fees:', error);
        });
}
function toggleFeeStructureStatus(feeStructureId, currentStatus) {
    Swal.fire({
        title: `Are you sure?`,
        text: `Do you want to toggle the status of this fee structure?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: `Yes, Toggle`,
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/toggle-fee-structure/${feeStructureId}`, {
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
                        text: `Fee structure status toggled successfully!`,
                        icon: "success",
                        confirmButtonText: 'OK',
                    }).then(() => {
                        // Find the button and status text by data-id
                        const statusText = document.querySelector(`.fee-status[data-id="${feeStructureId}"]`);
                        const toggleButton = document.querySelector(`.toggleStatusButton[data-id="${feeStructureId}"]`);

                        // Update the status and button dynamically
                        if (currentStatus === 'Active') {
                            statusText.textContent = "Inactive";
                            statusText.classList.remove("text-success");
                            statusText.classList.add("text-danger");

                            toggleButton.innerHTML = `<i class="fa-solid fa-power-off me-1"></i>`;
                            toggleButton.style.color = "green";
                            toggleButton.style.borderColor = "green";
                            toggleButton.setAttribute("onclick", `toggleFeeStructureStatus('${feeStructureId}', 'Inactive')`);
                        } else {
                            statusText.textContent = "Active";
                            statusText.classList.remove("text-danger");
                            statusText.classList.add("text-success");

                            toggleButton.innerHTML = `<i class="fa-solid fa-ban me-1"></i>`;
                            toggleButton.style.color = "red";
                            toggleButton.style.borderColor = "red";
                            toggleButton.setAttribute("onclick", `toggleFeeStructureStatus('${feeStructureId}', 'Active')`);
                        }
                    });
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




// Hide the form and show the table
 function showSettings(){
    document.getElementById('coursesTableContainer').classList.add('d-none')
    document.getElementById('feesFormContainer').classList.remove('d-none')
 }
 function hideSettings(){
    document.getElementById('feesFormContainer').classList.add('d-none')
    document.getElementById('coursesTableContainer').classList.remove('d-none')
 }
 document.addEventListener('DOMContentLoaded', () => {
    const selectedYear = new Date().getFullYear(); // Default to the current year
    fetchCourses(''); // Fetch all courses
}); 


 // Handle Course Type Dropdown Change
// document.getElementById('courseTypeDropdown').addEventListener('change', function () {
//     const selectedType = this.value; // Get the selected course type from the dropdown
//     // console.log(selectedType);
//     // Fetch and filter courses based on the selected course type
//     filterTableRowsByType(selectedType);
// });
// Function to Filter Table Rows by TypeText
function filterTableRowsByType(selectedType) {
    const tableBody = document.querySelector('#coursesTable tbody'); // Select the table body
    const tableRows = tableBody.querySelectorAll('tr'); // Get all rows inside the tbody

    // Iterate through each row
    tableRows.forEach(row => {
        const typeTextCell = row.querySelector('td:nth-child(3)'); // Target the third cell (typeText column)
        if (typeTextCell) {
            const typeText = typeTextCell.textContent.trim(); // Get the text content of the cell
            if (selectedType === '' || typeText === selectedType || selectedType === 'All') {
                row.style.display = ''; // Show the row if it matches the selectedType
            } else {
                row.style.display = 'none'; // Hide the row otherwise
            }
        }
    });
}
document.getElementById('refreshButton').addEventListener('click', () => {
    const refreshIcon = document.querySelector('#refreshButton i'); // Select the icon inside the button

    // Add rotation class to the icon
    refreshIcon.classList.add('icon-rotate');

    // Simulate fetch process
    fetchCourses(); // Fetch and refresh the table with all data

    // Simulate a delay (you can remove this if `fetchCourses` is asynchronous and handle it via promises)
    setTimeout(() => {
        // Remove rotation class after refreshing
        refreshIcon.classList.remove('icon-rotate');
    }, 2000); // Adjust the timeout as per your fetch function duration
});




function fetchCourses() {
  // Retrieve the selected institution ID from the dropdown.
  const institutionId = document.getElementById('institutionSelect').value;
  if (!institutionId) {
    coursesAccordion.innerHTML = `
      <div id="search_Data_div" class="p-4 bg-white rounded text-center d-flex flex-column justify-content-center align-items-center vh-50">
        <img src="{{ asset('assets/web_assets/search.png') }}" alt="" style="width: 300px;">
        <p class="fs-5">Select an Institute first</p>
      </div>`;
    return;
  }

  // Show loading spinner and hide the accordion container
  document.getElementById('coursesLoading').classList.remove('d-none');
  document.getElementById('coursesAccordion').classList.add('d-none');

  // Use the dynamic institution ID in the API endpoint
  fetch(`/api/view-intakes-by-institution?institute_id=${institutionId}`, {
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
      const accordionContainer = document.getElementById('coursesAccordion');
      accordionContainer.innerHTML = '';

      if (data.status !== 'success' || !data.data.length) {
        accordionContainer.innerHTML =`
         <div class="bg-white text-center">
            <img src="{{ asset('assets/web_assets/noData.png') }}" alt="">
            <br>
            No course with intake found.
        </div>
        `;
        return;
      }

      // Group intakes by year (or you can group by another property if needed)
      const intakesByYear = {};
      data.data.forEach(intake => {
        if (!intakesByYear[intake.year]) {
          intakesByYear[intake.year] = [];
        }
        intakesByYear[intake.year].push(intake);
      });

      // Sort the years in descending order
      const sortedYears = Object.keys(intakesByYear).sort((a, b) => b - a);

      // Build the accordion markup
      let accordionHTML = `<div class="accordion" id="accordionCourses">`;
      sortedYears.forEach((year, index) => {
        const collapseId = `collapse-${year}`;
        const headingId = `heading-${year}`;

        // Build table rows for the intakes of this year
        let tableRows = '';
        intakesByYear[year].forEach(intake => {
          // Create a unique row ID
          const rowId = `row-${intake.program_code}-${intake.intake_type}-${intake.year}`;
          tableRows += `
            <tr id="${rowId}">
                <td class="text-13  align-middle">${intake.program_name}</td>
                <td class="text-13 text-center align-middle">${intake.program_type}</td>
                <td class="text-13 text-center align-middle">${intake.year_duration || 'N/A'} year</td>
                <td class="text-13 text-center align-middle">${intake.intake_type || 'General'}</td>
                <td class="text-13 text-center align-middle" id="gen-${rowId}">Loading...</td>
                <td class="text-13 text-center align-middle" id="ews-${rowId}">Loading...</td>
                <td class="text-13 text-center align-middle" id="tfw-${rowId}">Loading...</td>
            </tr>
            `;

          // Call checkFees for each intake (if applicable)
          checkFees(intake, rowId);
        });

        accordionHTML += `
          <div class="accordion-item">
            <h2 class="accordion-header" id="${headingId}">
              <button class="accordion-button text-13 ${index === 0 ? '' : 'collapsed'}" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="${index === 0 ? 'true' : 'false'}" aria-controls="${collapseId}">
                Intakes of ${year} 
              </button>
            </h2>
            <div id="${collapseId}" class="accordion-collapse collapse ${index === 0 ? 'show' : ''}" aria-labelledby="${headingId}" data-bs-parent="#accordionCourses">
              <div class="accordion-body">
                <div class="table-responsive">
                  <table class="table table-striped">
                    <tbody>
                      ${tableRows}
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        `;
      });
      accordionHTML += `</div>`;
      accordionContainer.innerHTML = accordionHTML;
    })
    .catch(error => {
      console.error('Error fetching intakes:', error);
      Swal.fire({
        title: 'Error',
        text: 'An error occurred while fetching intakes.',
        icon: 'error',
        confirmButtonText: 'OK'
      });
    })
    .finally(() => {
      document.getElementById('coursesLoading').classList.add('d-none');
      document.getElementById('coursesAccordion').classList.remove('d-none');
    });
}



function checkFees(intake, rowId) {
  const institutionId = document.getElementById('institutionSelect').value;
  if (!institutionId) return; // Do nothing if no institution is selected

  console.log('Checking fees for year:', intake.year);
  fetch('/api/search-fees', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Authorization': `${token}`
    },
    body: JSON.stringify({
      institute_id: institutionId,
      program_code: intake.program_code,
      intake_type: intake.intake_type || 'General',
      year: String(intake.year)
    })
  })
    .then(response => {
      if (response.status === 401 || response.status === 403) {
        window.location.href = '/Unauthorised';
        throw new Error('Unauthorized Access');
      }
      return response.json();
    })
    .then(feesData => {
      let genStatus = 'red',
          ewsStatus = 'red',
          tfwStatus = 'red';
      let genButton = createFeeButton(intake, 'GEN');
      let ewsButton = createFeeButton(intake, 'EWS');
      let tfwButton = createFeeButton(intake, 'TFW');
      console.log(feesData);
      if (feesData.status === 'success') {
        feesData.data.forEach(fee => {
          if (fee.fee_type === 'GEN') {
            genStatus = 'green';
            genButton = createEditButton(feesData.data, 'GEN', intake.program_name);
          }
          if (fee.fee_type === 'EWS') {
            ewsStatus = 'green';
            ewsButton = createEditButton(feesData.data, 'EWS', intake.program_name);
          }
          if (fee.fee_type === 'TFW') {
            tfwStatus = 'green';
            tfwButton = createEditButton(feesData.data, 'TFW', intake.program_name);
          }
        });
      }

      document.getElementById(`gen-${rowId}`).innerHTML = `<span style="color: ${genStatus}">GEN ${genButton}</span>`;
      document.getElementById(`ews-${rowId}`).innerHTML = `<span style="color: ${ewsStatus}">EWS ${ewsButton}</span>`;
      document.getElementById(`tfw-${rowId}`).innerHTML = `<span style="color: ${tfwStatus}">TFW ${tfwButton}</span>`;
    })
    .catch(error => {
      console.error('Error fetching fees:', error);
      document.getElementById(`gen-${rowId}`).innerHTML = '<span class="text-danger">Error</span>';
      document.getElementById(`ews-${rowId}`).innerHTML = '<span class="text-danger">Error</span>';
      document.getElementById(`tfw-${rowId}`).innerHTML = '<span class="text-danger">Error</span>';
    });
}


function createFeeButton(intake, feeType) {
    return `<button class="btn btn-outline-primary btn-sm text-13" onclick="populateForm('${intake.program_code}', '${intake.intake_type || 'General'}', '${intake.year_duration}', '${intake.program_name}', '${feeType}','${intake.year}')">
                <i class="fa-solid fa-plus"></i>
            </button>`;
}

function createEditButton(feesData, feeType, programName) {
    return `<button class="btn btn-outline-primary btn-sm text-13" onclick="populateEditForm(${JSON.stringify(feesData).replace(/"/g, '&quot;')}, '${feeType}', '${programName}')">
                <i class="fa-solid fa-pen"></i>
            </button>`;
}


function populateForm(programCode, intakeType, duration, programName, feeType,intakeYear) {
    document.querySelector(".addfees_admin_text").innerText = `Add Fees for ${programName}`;
    document.getElementById('addFeesFormContainer').classList.remove('d-none');
    document.getElementById('coursesTableContainer').classList.add('d-none');
    console.log(intakeYear);

    // Set program details
    document.getElementById('intakeYear').value = intakeYear;
    document.getElementById('programCode').value = programCode;
    document.getElementById('intakeType').value = intakeType;
    const adjustedDuration = intakeType === 'Lateral' ? duration - 1 : duration;
    document.getElementById('duration').value = adjustedDuration;
    document.getElementById('feeType').value = feeType;

    // Clear previous semester fields
    const semesterFields = document.getElementById('semesterFields');
    semesterFields.innerHTML = '';

    // Create hidden fields for storing one_time_fees, semester_wise_fees, and other_fees
    let oneTimeFeesField = document.getElementById('oneTimeFeesField');
    let semesterWiseFeesField = document.getElementById('semesterWiseFeesField');
    let otherFeesField = document.getElementById('otherFeesField');

    if (!oneTimeFeesField) {
        oneTimeFeesField = document.createElement('input');
        oneTimeFeesField.type = 'hidden';
        oneTimeFeesField.id = 'oneTimeFeesField';
        oneTimeFeesField.name = 'one_time_fees';
        document.getElementById('addFeesFormContainer').appendChild(oneTimeFeesField);
    }

    if (!semesterWiseFeesField) {
        semesterWiseFeesField = document.createElement('input');
        semesterWiseFeesField.type = 'hidden';
        semesterWiseFeesField.id = 'semesterWiseFeesField';
        semesterWiseFeesField.name = 'semester_wise_fees';
        document.getElementById('addFeesFormContainer').appendChild(semesterWiseFeesField);
    }

    if (!otherFeesField) {
        otherFeesField = document.createElement('input');
        otherFeesField.type = 'hidden';
        otherFeesField.id = 'otherFeesField';
        otherFeesField.name = 'other_fees';
        document.getElementById('addFeesFormContainer').appendChild(otherFeesField);
    }

    // Initialize data storage
    const oneTimeFees = {};
    const semesterWiseFees = {};
    const otherFees = {};
    const instituteId =  document.getElementById('institutionSelect').value;
    // Fetch the fee structures
    fetch(`/api/view-fees-structure?institute_id=${encodeURIComponent(instituteId)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Authorization': `${token}`
        }
    })
    .then(response => {
        if (response.status === 401 || response.status === 403) {
            // Redirect if unauthorized.
            window.location.href = '/Unauthorised';
            throw new Error('Unauthorized Access');
        }
        return response.json();
    })
        .then(data => {
            if (data.status === 'success') {
                let feeStructures = data.data.filter(fee => fee.status === 'Active');
                // Calculate total semesters
                const totalSemesters = adjustedDuration * 2;

                // Loop through semesters and generate dropdowns and buttons
                for (let i = 1; i <= totalSemesters; i++) {
                    semesterFields.innerHTML += `
                        <div class="semester-group mt-4 p-3 rounded border" style="background-color: #f9f9f9; border-left: 4px solid #007bff;">
                            <div class="row g-4 mb-3">
                                <div class="col-md-3">
                                    <h6 class="text-primary">Semester ${i}</h6>
                                </div>
                                <div class="col-md-9 d-flex align-items-center">
                                    <select 
                                        id="feesDropdown${i}" 
                                        class="form-control text-13 feesDropdown me-2" 
                                        data-semester="${i}"
                                        onchange="updateFeeTypeonChange(${i})">
                                        <option value="" disabled selected>Select Fee Structure</option>
                                        ${feeStructures
                                            .map(
                                                fee =>
                                                    `<option value="${fee.id.$oid}" data-type="${fee.type}">${fee.head_of_account}</option>`
                                            )
                                            .join('')}
                                    </select>
                                    <input 
                                        type="text" 
                                        id="feeType${i}" 
                                        class="form-control text-13 feeType me-2 d-none"
                                        placeholder="Fee Type"
                                        readonly>
                                    <input 
                                        type="number" 
                                        id="feeAmount${i}" 
                                        class="form-control text-13 feeAmount me-2 d-none" 
                                        placeholder="Enter Amount" 
                                        data-semester="${i}">
                                    <button 
                                        type="button" 
                                        class="btn btn-outline-primary btn-sm addFeeButton"
                                        data-semester="${i}">
                                        Add
                                    </button>
                                </div>
                                <div class="col-md-12 mt-3" id="semesterFeesContainer${i}">
                                    <!-- Selected fees will be added here -->
                                </div>
                                 <div class="col-md-12 mt-3 text-end">
                                    <strong class="text-secondary">Total Semester Fees: ₹ <span id="totalFeesSemester${i}" class="text-primary">0</span></strong>
                                    <span class="btn btn-primary text-13" id="addfees_samesemdata_btn_${i}" onclick="toggleSaveEditButton(${i}, this)" style="display: none;">Save</span>
                                </div>
                            </div>
                        </div>
                    `;

                }

                // Add event listeners to Add buttons
                document.querySelectorAll('.addFeeButton').forEach(button => {
                    const semester = button.getAttribute('data-semester');
                    const amountInput = document.getElementById(`feeAmount${semester}`);

                    button.addEventListener('click', event => {
                        const dropdown = document.getElementById(`feesDropdown${semester}`);
                        const feesValue = amountInput.value.trim();
                        const feeTypeInput = document.getElementById(`feeType${semester}`);
                        const feeId = dropdown.value;
                        const feeType = dropdown.selectedOptions[0].getAttribute('data-type');

                        // Show a warning if the amount field is empty or invalid
                        if (feesValue === "" || parseFloat(feesValue) <= 0) {
                            Swal.fire({
                                title: 'Warning',
                                text: 'Please enter a valid amount before adding.',
                                icon: 'warning',
                                confirmButtonText: 'OK',
                            });
                            return;
                        }

                        // Show a warning if no fee structure is selected
                        if (!feeId) {
                            Swal.fire({
                                title: 'Error',
                                text: 'Please select a fee structure before adding.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                            });
                            return;
                        }

                        const selectedFee = feeStructures.find(fee => fee.id.$oid === feeId);

                        if (feeType === 'one-time') {
                            if (!oneTimeFees[selectedFee.head_of_account]) {
                                oneTimeFees[selectedFee.head_of_account] = 0;
                            }
                            addOneTimeFee(selectedFee, totalSemesters, oneTimeFees, feeTypeInput, feesValue);
                            feeStructures = feeStructures.filter(fee => fee.id.$oid !== feeId);
                            updateDropdowns(feeId);
                        } else if (feeType === 'semester-wise') {
                            addSemesterWiseFee(selectedFee, totalSemesters, semesterWiseFees, feeTypeInput, feesValue);
                            feeStructures = feeStructures.filter(fee => fee.id.$oid !== feeId);
                            updateDropdowns(feeId);
                        } else if (feeType === 'other') {
                            addOtherFee(selectedFee, semester, otherFees, feeTypeInput, feesValue);
                        }
                        updateFeeTypeonChange(semester);
                                // **Ensure hidden fields update immediately**
                        updateHiddenFields(selectedFee, feesValue, semester);
                    });
                });



                // Function to update dropdowns after adding one-time fee
                function updateDropdowns(feeId) {
                    document.querySelectorAll('.feesDropdown').forEach(dropdown => {
                        const optionToRemove = dropdown.querySelector(`option[value="${feeId}"]`);
                        if (optionToRemove) {
                            optionToRemove.remove();
                        }
                    });
                }
            } else {
                console.error('Error fetching fee structures:', data.message);
            }
        })
        .catch(error => {
            console.error('Error fetching fee structures:', error);
        });
}
// Function to update fee type in the read-only input
function updateFeeTypeonChange(semester) {
    const dropdown = document.getElementById(`feesDropdown${semester}`);
    const feeTypeInput = document.getElementById(`feeType${semester}`);
    const feeAmountInput = document.getElementById(`feeAmount${semester}`);
    // const addFeeButton = document.querySelector(`.addFeeButton[data-semester="${semester}"]`);

    if (dropdown && dropdown.value) {
        const selectedOption = dropdown.options[dropdown.selectedIndex];
        feeTypeInput.value = selectedOption.getAttribute("data-type") || "";
        feeAmountInput.value = "";
        // Show inputs when an option is selected
        feeTypeInput.classList.remove("d-none");
        feeAmountInput.classList.remove("d-none");
    } else {
        // Hide inputs if no option is selected
        feeTypeInput.classList.add("d-none");
        feeAmountInput.classList.add("d-none");
    }
}
// Function to clear and hide fee type, fee amount, and button
function resetFeeFields(semester) {
    const feeTypeInput = document.getElementById(`feeType${semester}`);
    const feeAmountInput = document.getElementById(`feeAmount${semester}`);

    // Clear values
    feeTypeInput.value = "";
    feeAmountInput.value = "";

    // Hide inputs
    feeTypeInput.classList.add("d-none");
    feeAmountInput.classList.add("d-none");
}


// Function to add one-time fee
function addOneTimeFee(fee, totalSemesters, oneTimeFees,feeTypeInput,feesValue) {
    const firstSemesterContainer = document.getElementById(`semesterFeesContainer1`);
    if (isFeeAlreadyAdded(firstSemesterContainer, fee)) {
        showInfoMessage(`${fee.head_of_account} is already added.`);
        return;
    }

    oneTimeFees[fee.head_of_account] = 0; // Set default value for one-time fee
    addFeeField(firstSemesterContainer, fee, oneTimeFees,feeTypeInput,feesValue ,1);
    Swal.fire({
        title: 'Added',
        text: `${fee.head_of_account} added as a one-time fee.`,
        icon: 'success',
        confirmButtonText: 'OK',
    });
}

// Function to add semester-wise fee
function addSemesterWiseFee(fee, totalSemesters, semesterWiseFees,feeTypeInput,feesValue) {
    let isAlreadyAdded = false;
    console.log(feesValue)

    for (let i = 1; i <= totalSemesters; i++) {
        const semesterContainer = document.getElementById(`semesterFeesContainer${i}`);
        if (!semesterWiseFees[fee.head_of_account]) {
            semesterWiseFees[fee.head_of_account] = {};
        }
        if (isFeeAlreadyAdded(semesterContainer, fee)) {
            isAlreadyAdded = true;
            continue;
        }
        addFeeField(semesterContainer, fee, semesterWiseFees,feeTypeInput,feesValue, i);
    }

    if (isAlreadyAdded) {
        showInfoMessage(`${fee.head_of_account} was already added.`);
    } else {
        Swal.fire({
            title: 'Added',
            text: `${fee.head_of_account} added to all semesters.`,
            icon: 'success',
            confirmButtonText: 'OK',
        });
    }
}

// Function to add "Other" fee for the selected semester only
function addOtherFee(fee, semester, otherFees,feeTypeInput,feesValue) {
    const semesterContainer = document.getElementById(`semesterFeesContainer${semester}`);
    if (isFeeAlreadyAdded(semesterContainer, fee)) {
        showInfoMessage(`${fee.head_of_account} is already added for Semester ${semester}.`);
        return;
    }

    if (!otherFees[semester]) {
        otherFees[semester] = {};
    }
    otherFees[semester][fee.head_of_account] = 0; // Set default value

    addFeeField(semesterContainer, fee, otherFees,feeTypeInput,feesValue, semester);
    Swal.fire({
        title: 'Added',
        text: `${fee.head_of_account} added for Semester ${semester}.`,
        icon: 'success',
        confirmButtonText: 'OK',
    });
}

// Function to create and add fee input field
function addFeeField(container, fee, dataStore, feeTypeInput, feesValue, semester = null) {
    const feeField = document.createElement('div');
    feeField.classList.add('row','p-1', 'align-items-center', 'justify-content-between','rounded', 'bg-white');
    feeField.setAttribute('data-fee-id', fee.id.$oid);
    feeField.style.boxShadow = "rgba(0, 0, 0, 0.06) 0px 2px 4px 0px inset";

    const uniqueClassSemField = `fee-actions-sem-${semester}`; // Generate unique class per semester
    const uniqueClassSemFees = `fees-sem-${semester}`; // Unique class for fee container per semester

    feeField.innerHTML = `
        <div class="col-md-6 d-flex align-items-center justify-content-between">
            <span class="text-start fw-bold text-13 text-primary">
                ${fee.head_of_account || ''} <span class="text-danger">*</span>
            </span>
            <span class="feeTypeInput text-13 text-secondary">
                ${feeTypeInput.value || ''}
            </span>
        </div>
        <div class="col-md-6 d-flex align-items-center justify-content-between ${uniqueClassSemFees}">
            <div class="position-relative feeInputGroup">
                <span class="position-absolute start-0 top-50 translate-middle-y ps-2 text-dark fw-bold text-13 feeSymbol">₹</span>
                <input 
                    type="number" 
                    class="form-control text-13 fee-input ps-4"
                    placeholder="Enter Amount"
                    name="${fee.head_of_account.toLowerCase().replace(/\s/g, '_')}[${semester || 'one-time'}]"
                    data-fee-type="${fee.type}"
                    data-semester="${semester || ''}"
                    value="${feesValue || ''}"
                    readOnly
                    style="text-align: center;">
            </div>

            <div class="${uniqueClassSemField}">
                <button 
                    type="button" 
                    class="btn btn-outline-secondary btn-sm editFeeButton text-13 m-1">
                    <i class="fa-regular fa-pen-to-square"></i>
                </button>

                <button 
                    type="button" 
                    class="btn btn-outline-danger btn-sm disableFeeButton text-13 m-1">
                    <i class="fa-solid fa-ban"></i>
                </button>
            </div>
        </div>
    `;

    container.appendChild(feeField);

    // **Immediately update hidden fields**
    updateHiddenFields(fee, feesValue, semester);

    // Get elements
    const editButton = feeField.querySelector('.editFeeButton');
    const disableButton = feeField.querySelector('.disableFeeButton');
    const feeAmountInput = feeField.querySelector('.fee-input');
    const feeSymbol = feeField.querySelector('.feeSymbol');
    updateTotalFees(semester);

    // **Edit Button Logic**
    editButton.addEventListener('click', () => {
        if (feeAmountInput.readOnly) {
            feeAmountInput.readOnly = false;
            feeSymbol.innerHTML = '<i class="fa-solid fa-pen"></i>'; // Change ₹ to Pencil
            editButton.innerHTML = '<i class="fa-regular fa-floppy-disk"></i>';
            editButton.classList.replace('btn-outline-secondary', 'btn-success');
        } else {
            feeAmountInput.readOnly = true;
            feeSymbol.innerHTML = '₹'; // Restore ₹ symbol
            editButton.innerHTML = '<i class="fa-regular fa-pen-to-square"></i>';
            editButton.classList.replace('btn-success', 'btn-outline-secondary');

            // **Ensure hidden fields update when fee value changes**
            updateHiddenFields(fee, feeAmountInput.value, semester);
            updateTotalFees(semester);  // Update total when editing is finished
        }
    });

    // **Disable Button Logic**
    disableButton.addEventListener('click', () => {
    let currentFeeValue = feeAmountInput.value.trim(); // Store current value

    if (disableButton.classList.contains('btn-outline-danger')) {
        // **Disable the fee: Remove from JSON**
        removeFeeFromJSON(fee, semester);
        feeAmountInput.readOnly = true;
        feeAmountInput.classList.add('text-muted', 'bg-danger', 'text-white');
        feeAmountInput.setAttribute("data-disabled-value", currentFeeValue); // **Store last known value**
        feeSymbol.innerHTML = '<i class="fa-solid fa-ban text-white"></i>'; // Change ₹ to Ban icon

        disableButton.innerHTML = '<i class="fa-solid fa-power-off"></i>'; // Change icon to "Activate"
        disableButton.classList.replace('btn-outline-danger', 'btn-outline-success');
    } else {
        // **Activate the fee: Restore last known value instead of feesValue**
        let lastValue = feeAmountInput.getAttribute("data-disabled-value") || feesValue || '';

        updateHiddenFields(fee, lastValue, semester);
        feeAmountInput.value = lastValue; // Restore last value
        feeAmountInput.classList.remove('text-muted', 'bg-danger', 'text-white');
        feeSymbol.innerHTML = '₹'; // Restore ₹ symbol

        disableButton.innerHTML = '<i class="fa-solid fa-ban"></i>'; // Change icon to "Disable"
        disableButton.classList.replace('btn-outline-success', 'btn-outline-danger');
    }

    // **Always recalculate the total fees for the semester**
    updateTotalFees(semester);
});



    // **Automatically update total when the amount changes**
    feeAmountInput.addEventListener('input', () => {
        updateTotalFees(semester);
    });
}
function updateTotalFees(semester) {
    let totalElement = document.getElementById(`totalFeesSemester${semester}`);
    let totalFeesDisplay = document.getElementById('totalFeesDisplay'); // Get total fees display
    let saveButton = document.getElementById(`addfees_samesemdata_btn_${semester}`);

    totalElement.innerHTML = `<div class="spinner-border text-primary spinner-border-sm" role="status">
                                  <span class="visually-hidden">Loading...</span>
                              </div>`;

    setTimeout(() => {
        let total = 0;

        // **Get all fee inputs within the semester container**
        document.querySelectorAll(`#semesterFeesContainer${semester} .fee-input`).forEach(input => {
            if (!input.classList.contains('text-muted')) { // **Exclude disabled fees**
                total += parseFloat(input.value) || 0;
            }
        });

        // **Format number in Indian currency format (1,00,000)**
        let formattedTotal = new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(total);

        // **Update the semester total display**
        totalElement.textContent = `${formattedTotal}`;

        // **Update overall total fees**
        let grandTotal = 0;
        document.querySelectorAll('.fee-input').forEach(input => {
            if (!input.classList.contains('text-muted')) {
                grandTotal += parseFloat(input.value) || 0;
            }
        });

        // **Update the total fees beside Submit button**
        totalFeesDisplay.textContent = `${new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(grandTotal)}`;

        // **Show/Hide Save Button Based on Total Fees**
        if (total > 0) {
            saveButton.style.display = "inline-block";
        } else {
            saveButton.style.display = "none";
        }
    }, 500);
}


function toggleSaveEditButton(semester, button) {
    let semesterContainer = document.getElementById(`semesterFeesContainer${semester}`);
    let isEditMode = button.innerText === "Edit"; // Check current state

    // **Toggle Read-Only State for Inputs**
    semesterContainer.querySelectorAll('.fee-input').forEach(input => {
        input.readOnly = !isEditMode;
        // input.style.border = isEditMode ? "1px solid #ced4da" : "none";
        input.style.background = isEditMode ? "#fff" : "transparent";
        
    });

    // **Show/Hide Buttons, But Keep Space Reserved**
    document.querySelectorAll(`.fee-actions-sem-${semester}`).forEach(div => {
    div.style.display = isEditMode ? 'block' : 'none';
});


    // **Float Amount Field to Right When Buttons Are Hidden
    console.log(document.querySelectorAll(`.fees-sem-${semester}`))
    document.querySelectorAll(`.fees-sem-${semester}`).forEach(div => {
    div.style.display = "flex";
    div.style.justifyContent = "flex-end"; // Aligns all child elements to the right
    div.style.gap = "8px"; // Adds small spacing between elements (optional)
});

    // **Replace Save button with Edit button**
    if (!isEditMode) {
        button.innerText = "Edit";
        button.classList.remove("btn-primary");
        button.classList.add("btn-outline-secondary");
    } else {
        button.innerText = "Save";
        button.classList.remove("btn-outline-secondary");
        button.classList.add("btn-primary");
    }
}




// **Function to update fee value in the data store**
function updateHiddenFields(fee, feesValue, semester) {
    const feeType = fee.type;
    let oneTimeFeesField = document.getElementById('oneTimeFeesField');
    let semesterWiseFeesField = document.getElementById('semesterWiseFeesField');
    let otherFeesField = document.getElementById('otherFeesField');

    if (feeType === 'one-time') {
        let oneTimeFees = JSON.parse(oneTimeFeesField.value || '{}');
        oneTimeFees[fee.head_of_account] = feesValue;
        oneTimeFeesField.value = JSON.stringify(oneTimeFees);
    } 
    else if (feeType === 'semester-wise') {
        let semesterWiseFees = JSON.parse(semesterWiseFeesField.value || '{}');
        if (!semesterWiseFees[fee.head_of_account]) {
            semesterWiseFees[fee.head_of_account] = {};
        }
        semesterWiseFees[fee.head_of_account][semester] = feesValue;
        semesterWiseFeesField.value = JSON.stringify(semesterWiseFees);
    } 
    else if (feeType === 'other') {
        let otherFees = JSON.parse(otherFeesField.value || '{}');
        if (!otherFees[semester]) {
            otherFees[semester] = {};
        }
        otherFees[semester][fee.head_of_account] = feesValue;
        otherFeesField.value = JSON.stringify(otherFees);
    }
}

// **Function to remove fee from JSON**
function removeFeeFromJSON(fee, semester) {
    const feeType = fee.type;
    let oneTimeFeesField = document.getElementById('oneTimeFeesField');
    let semesterWiseFeesField = document.getElementById('semesterWiseFeesField');
    let otherFeesField = document.getElementById('otherFeesField');

    if (feeType === 'one-time') {
        let oneTimeFees = JSON.parse(oneTimeFeesField.value || '{}');
        delete oneTimeFees[fee.head_of_account];
        oneTimeFeesField.value = JSON.stringify(oneTimeFees);
    } 
    else if (feeType === 'semester-wise') {
        let semesterWiseFees = JSON.parse(semesterWiseFeesField.value || '{}');
        if (semesterWiseFees[fee.head_of_account]) {
            delete semesterWiseFees[fee.head_of_account][semester];
            if (Object.keys(semesterWiseFees[fee.head_of_account]).length === 0) {
                delete semesterWiseFees[fee.head_of_account];
            }
        }
        semesterWiseFeesField.value = JSON.stringify(semesterWiseFees);
    } 
    else if (feeType === 'other') {
        let otherFees = JSON.parse(otherFeesField.value || '{}');
        if (otherFees[semester]) {
            delete otherFees[semester][fee.head_of_account];
            if (Object.keys(otherFees[semester]).length === 0) {
                delete otherFees[semester];
            }
        }
        otherFeesField.value = JSON.stringify(otherFees);
    }
}



// Function to check if a fee is already added
function isFeeAlreadyAdded(container, fee) {
    return !!container.querySelector(`[data-fee-id="${fee.id.$oid}"]`);
}

// Function to show an info message
function showInfoMessage(message) {
    Swal.fire({
        title: 'Info',
        text: message,
        icon: 'info',
        confirmButtonText: 'OK',
    });
}






// Handle the "Cancel" button in the Add Fees form
document.getElementById('cancelAddFeesButton').addEventListener('click', function () {
    // Hide the Add Fees form
    document.getElementById('addFeesFormContainer').classList.add('d-none');
    // Show the courses table
    document.getElementById('coursesTableContainer').classList.remove('d-none');
});

// Attach the form submit event
document.getElementById('addFeesForm').addEventListener('submit', function (event) {
    // Prevent the default form submission behavior
    event.preventDefault();
    const intakeYear = document.getElementById('intakeYear').value;
    const programCode = document.getElementById('programCode').value;
    const intakeType = document.getElementById('intakeType').value;
    const duration = document.getElementById('duration').value;
    const feeType = document.getElementById('feeType').value;

    // Fetch the values from the hidden input fields
    const oneTimeFees = JSON.parse(document.getElementById('oneTimeFeesField').value || '{}');
    const semesterWiseFees = JSON.parse(document.getElementById('semesterWiseFeesField').value || '{}');
    const otherFees = JSON.parse(document.getElementById('otherFeesField').value || '{}');
    if (Object.keys(oneTimeFees).length === 0 && 
        Object.keys(semesterWiseFees).length === 0 && 
        Object.keys(otherFees).length === 0) {
        
        Swal.fire({
            title: 'Error',
            text: 'Please add at least one fee before submitting.',
            icon: 'error',
            confirmButtonText: 'OK',
        });

        return;
    }
    const instituteId =  document.getElementById('institutionSelect').value;

    // Construct the payload
    const payload = {
        institution_id: instituteId,
        program_code: programCode,
        intake_type: intakeType,
        intake_year: intakeYear,
        duration: duration,
        fee_type: feeType,
        one_time_fees: oneTimeFees,
        semester_wise_fees: semesterWiseFees,
        other_fees: otherFees, // Include other fees
    };

    // console.log(payload); // For debugging purposes

    const saveButton = document.getElementById('saveFeesButton');
    const spinner = document.createElement('span');
    spinner.classList.add('spinner-border', 'spinner-border-sm', 'ms-2');
    spinner.setAttribute('role', 'status');
    spinner.setAttribute('aria-hidden', 'true');

    // Disable button and show spinner
    saveButton.disabled = true;
    saveButton.textContent = 'Saving';
    saveButton.appendChild(spinner);

    // Call the API
    fetch('/api/save-fees', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': `${token}`
        },
        body: JSON.stringify(payload),
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
            if (data.status === 'success') {
                Swal.fire({
                    title: 'Success',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK',
                }).then(() => {
                    // Hide the form and show the table
                    document.getElementById('addFeesFormContainer').classList.add('d-none');
                    document.getElementById('coursesTableContainer').classList.remove('d-none');

                    // Clear the hidden fields after successful save
                    document.getElementById('oneTimeFeesField').value = '{}';
                    document.getElementById('semesterWiseFeesField').value = '{}';

                    // Optionally reset the form
                    document.getElementById('addFeesForm').reset();
                });
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            console.error('Error saving fees:', error);
            Swal.fire({
                title: 'Error',
                text: error.message || 'An error occurred while saving fees.',
                icon: 'error',
                confirmButtonText: 'OK',
            });
        })
        .finally(() => {
            // Re-enable button and remove spinner
            saveButton.disabled = false;
            saveButton.textContent = 'Save';
            if (saveButton.contains(spinner)) {
                saveButton.removeChild(spinner);
            }
        });
});


function populateEditForm(feesData, feeType, programName) {
    document.querySelector(".editfees_admin_text").innerText = `Edit Fees for ${programName}`;
    const selectedFee = feesData.find(fee => fee.fee_type === feeType);

    if (!selectedFee) {
        Swal.fire({
            title: 'Error',
            text: 'No fee data found for the selected type.',
            icon: 'error',
            confirmButtonText: 'OK',
        });
        return;
    }

    // Show the Edit Form and hide the table
    document.getElementById('editFeesFormContainer').classList.remove('d-none');
    document.getElementById('coursesTableContainer').classList.add('d-none');

    // Populate fixed inputs
    document.getElementById('editProgramCode').value = selectedFee.program_code;
    document.getElementById('editIntakeType').value = selectedFee.intake_type;
    document.getElementById('editFeeType').value = selectedFee.fee_type;

    // Get edit form container
    const editFieldsContainer = document.getElementById('editSemesterFields');
    editFieldsContainer.innerHTML = ''; // Clear existing fields

    // Create or reset the hidden fields
    let oneTimeFeesField = document.getElementById('editOneTimeFeesField');
    let semesterWiseFeesField = document.getElementById('editSemesterWiseFeesField');
    let otherFeesField = document.getElementById('editOtherFeesField'); // NEW
    let feesIdField = document.getElementById('editFeesIdField');

    if (!oneTimeFeesField) {
        oneTimeFeesField = document.createElement('input');
        oneTimeFeesField.type = 'hidden';
        oneTimeFeesField.id = 'editOneTimeFeesField';
        oneTimeFeesField.name = 'one_time_fees';
        document.getElementById('editFeesFormContainer').appendChild(oneTimeFeesField);
    }

    if (!semesterWiseFeesField) {
        semesterWiseFeesField = document.createElement('input');
        semesterWiseFeesField.type = 'hidden';
        semesterWiseFeesField.id = 'editSemesterWiseFeesField';
        semesterWiseFeesField.name = 'semester_wise_fees';
        document.getElementById('editFeesFormContainer').appendChild(semesterWiseFeesField);
    }

    if (!otherFeesField) { // NEW: Create field for other fees
        otherFeesField = document.createElement('input');
        otherFeesField.type = 'hidden';
        otherFeesField.id = 'editOtherFeesField';
        otherFeesField.name = 'other_fees';
        document.getElementById('editFeesFormContainer').appendChild(otherFeesField);
    }

    if (!feesIdField) {
        feesIdField = document.createElement('input');
        feesIdField.type = 'hidden';
        feesIdField.id = 'editFeesIdField';
        feesIdField.name = 'fees_id';
        document.getElementById('editFeesFormContainer').appendChild(feesIdField);
    }

    // Set Fees ID
    feesIdField.value = selectedFee.id.$oid;

    // Initialize data storage
    const oneTimeFees = {};
    const semesterWiseFees = {};
    const otherFees = {}; // NEW

    // One-Time Fees
    if (selectedFee.one_time_fees) {
        const parsedOneTimeFees = JSON.parse(selectedFee.one_time_fees);
        let oneTimeFeesHTML = `<h6 class="text-primary mt-3">One-Time Fees</h6><hr>`;

        Object.keys(parsedOneTimeFees).forEach(head => {
            oneTimeFees[head] = parsedOneTimeFees[head];
            oneTimeFeesHTML += `
                <div class="row p-2" style="background-color: #f9f9f9; border-left: 4px solid #007bff;">
                    <div class="col-md-6">
                        <label class="form-label text-13">${head} <span class="text-danger"> *</span></label>
                    </div>
                   <div class="col-md-6 position-relative d-flex align-items-center">
                    <span class="position-absolute start-1 top-50 translate-middle-y ps-2 text-dark fw-bold text-13">₹</span>
                        <input 
                            type="number" 
                            class="form-control text-13 text-center" 
                            name="one_time_fees[${head}]" 
                            value="${parsedOneTimeFees[head]}" 
                            placeholder="Enter ${head} amount" 
                            oninput="updateEditFeeValue(this, 'one-time', '${head}')"
                            required
                            readonly
                        >
                        <button 
                        type="button" 
                        class="btn btn-outline-primary btn-sm edit-fee-btn ms-2" 
                        onclick="toggleEditFee(this)">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </button>
                    </div>
                </div>`;
        });

        editFieldsContainer.innerHTML += oneTimeFeesHTML;
    }

    // Semester-Wise Fees
    if (selectedFee.semester_wise_fees) {
        const parsedSemesterWiseFees = JSON.parse(selectedFee.semester_wise_fees);
        let semesterWiseFeesHTML = `<h6 class="text-primary mt-3">Semester-Wise Fees</h6><hr>`;

        Object.keys(parsedSemesterWiseFees).forEach(semester => {
            const semesterFees = parsedSemesterWiseFees[semester];
            semesterWiseFeesHTML += `<h6 class="text-secondary mb-3">Semester ${semester}</h6>`;

            Object.keys(semesterFees).forEach(head => {
                if (!semesterWiseFees[semester]) semesterWiseFees[semester] = {};
                semesterWiseFees[semester][head] = semesterFees[head];

                semesterWiseFeesHTML += `
                    <div class="row p-2" style="background-color: #f9f9f9; border-left: 4px solid #007bff;">
                        <div class="col-md-6">
                            <label class="form-label text-13">Semester ${head} <span class="text-danger"> *</span></label>
                        </div>
                        <div class="col-md-6 position-relative d-flex align-items-center">
                        <span class="position-absolute start-1 top-50 translate-middle-y ps-2 text-dark fw-bold text-13">₹</span>
                            <input 
                                type="number" 
                                class="form-control text-13 text-center" 
                                name="semester_wise_fees[${semester}][${head}]" 
                                value="${semesterFees[head]}" 
                                placeholder="Enter ${head} amount" 
                                oninput="updateEditFeeValue(this, 'semester-wise', '${head}', '${semester}')"
                                required
                                readonly
                            >
                            <button 
                                type="button" 
                                class="btn btn-outline-primary btn-sm edit-fee-btn ms-2" 
                                onclick="toggleEditFee(this)">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </button>
                        </div>
                    </div>`;
            });
        });

        editFieldsContainer.innerHTML += semesterWiseFeesHTML;
    }

    // Other Fees (NEW)
    if (selectedFee.other_fees) {
        const parsedOtherFees = JSON.parse(selectedFee.other_fees);
        let otherFeesHTML = `<h6 class="text-primary mt-3">Other Fees</h6><hr>`;

        Object.keys(parsedOtherFees).forEach(semester => {
            const semesterFees = parsedOtherFees[semester];
            otherFeesHTML += `<h6 class="text-secondary mb-3">Semester ${semester}</h6>`;

            Object.keys(semesterFees).forEach(head => {
                if (!otherFees[semester]) otherFees[semester] = {};
                otherFees[semester][head] = semesterFees[head];

                otherFeesHTML += `
                    <div class="row p-2" style="background-color: #f9f9f9; border-left: 4px solid #007bff;">
                        <div class="col-md-6">
                            <label class="form-label text-13">Semester ${head} <span class="text-danger"> *</span></label>
                        </div>
                        <div class="col-md-6 position-relative d-flex align-items-center">
                        <span class="position-absolute start-1 top-50 translate-middle-y ps-2 text-dark fw-bold text-13">₹</span>
                            <input 
                                type="number" 
                                class="form-control text-13 text-center" 
                                name="other_fees[${semester}][${head}]" 
                                value="${semesterFees[head]}" 
                                placeholder="Enter ${head} amount" 
                                oninput="updateEditFeeValue(this, 'other', '${head}', '${semester}')"
                                required
                                readonly
                            >
                            <button 
                                type="button" 
                                class="btn btn-outline-primary btn-sm edit-fee-btn ms-2" 
                                onclick="toggleEditFee(this)">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </button>
                        </div>
                    </div>`;
            });
        });

        editFieldsContainer.innerHTML += otherFeesHTML;
        
    }

    // Update hidden fields
    oneTimeFeesField.value = JSON.stringify(oneTimeFees);
    semesterWiseFeesField.value = JSON.stringify(semesterWiseFees);
    otherFeesField.value = JSON.stringify(otherFees);
    updateTotalEditFees()
}
function toggleEditFee(button) {
    const input = button.previousElementSibling;
    if (input.readOnly) {
        input.readOnly = false;
        input.classList.add('border-primary');
        button.innerHTML = '<i class="fa-regular fa-floppy-disk"></i>';
        button.classList.replace('btn-outline-primary', 'btn-success');
    } else {
        input.readOnly = true;
        input.classList.remove('border-primary');
        button.innerHTML = '<i class="fa-regular fa-pen-to-square"></i>';
        button.classList.replace('btn-success', 'btn-outline-primary');
    }
    
}



// Helper function to update the hidden fields dynamically
function updateEditFeeValue(input, feeType, head, semester = null) {
    if (feeType === 'one-time') {
        const currentOneTimeFees = JSON.parse(document.getElementById('editOneTimeFeesField').value || '{}');
        currentOneTimeFees[head] = input.value;
        document.getElementById('editOneTimeFeesField').value = JSON.stringify(currentOneTimeFees);
    } 
    else if (feeType === 'semester-wise') {
        const currentSemesterWiseFees = JSON.parse(document.getElementById('editSemesterWiseFeesField').value || '{}');
        if (!currentSemesterWiseFees[semester]) {
            currentSemesterWiseFees[semester] = {};
        }
        currentSemesterWiseFees[semester][head] = input.value;
        document.getElementById('editSemesterWiseFeesField').value = JSON.stringify(currentSemesterWiseFees);
    } 
    else if (feeType === 'other') { // ✅ Now includes "other_fees"
        const currentOtherFees = JSON.parse(document.getElementById('editOtherFeesField').value || '{}');
        if (!currentOtherFees[semester]) {
            currentOtherFees[semester] = {};
        }
        currentOtherFees[semester][head] = input.value;
        document.getElementById('editOtherFeesField').value = JSON.stringify(currentOtherFees);
    }
}



function hideEditForm(){
      // Show the Edit Form and hide the table
    document.getElementById('coursesTableContainer').classList.remove('d-none');
    document.getElementById('editFeesFormContainer').classList.add('d-none');
}





document.getElementById('editFeesForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent default form submission

    // Fetch data from hidden fields
    const feesId = document.getElementById('editFeesIdField').value;
    const oneTimeFees = JSON.parse(document.getElementById('editOneTimeFeesField').value || '{}');
    const semesterWiseFees = JSON.parse(document.getElementById('editSemesterWiseFeesField').value || '{}');
    const otherFees = JSON.parse(document.getElementById('editOtherFeesField').value || '{}'); // ✅ New field added

    if (!feesId) {
        Swal.fire({
            title: 'Error',
            text: 'Fees ID is missing.',
            icon: 'error',
            confirmButtonText: 'OK',
        });
        return;
    }

    // Construct the request payload
    const payload = {
        id: feesId,
        one_time_fees: oneTimeFees,
        semester_wise_fees: semesterWiseFees,
        other_fees: otherFees, // ✅ Now included in the API request

    };

    const updateButton = document.getElementById('updateFeesButton');
    const spinner = document.createElement('span');
    spinner.classList.add('spinner-border', 'spinner-border-sm', 'ms-2');
    spinner.setAttribute('role', 'status');
    spinner.setAttribute('aria-hidden', 'true');

    // Disable button and show spinner
    updateButton.disabled = true;
    updateButton.textContent = 'Updating';
    updateButton.appendChild(spinner);

    // API Call
    fetch('/api/edit-fees', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': `${token}`
        },
        body: JSON.stringify(payload),
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
            if (data.status === 'success') {
                Swal.fire({
                    title: 'Success',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'OK',
                }).then(() => {
                    // Hide the form and show the courses table
                    document.getElementById('editFeesFormContainer').classList.add('d-none');
                    document.getElementById('coursesTableContainer').classList.remove('d-none');

                    // Optionally, refresh the fees table or reset the form
                    fetchCourses(); // Function to refresh course data
                });
            } else {
                throw new Error(data.message);
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error',
                text: error.message || 'An error occurred while updating the fees.',
                icon: 'error',
                confirmButtonText: 'OK',
            });
            console.error('Error updating fees:', error);
        })
        .finally(() => {
            // Re-enable button and remove spinner
            updateButton.disabled = false;
            updateButton.textContent = 'Update';
            if (updateButton.contains(spinner)) {
                updateButton.removeChild(spinner);
            }
        });
});
// Function to calculate total edited fees
// Function to calculate total edited fees
function updateTotalEditFees() {
    let totalEditFees = 0;

    // Iterate over all number input fields inside the edit form
    document.querySelectorAll('#editSemesterFields input[type="number"]').forEach(input => {
        if (!input.classList.contains('text-muted')) { // Exclude disabled fees
            totalEditFees += parseFloat(input.value) || 0;
        }
    });

    // Format the total in Indian currency format
    let formattedTotal = new Intl.NumberFormat('en-IN', { maximumFractionDigits: 2 }).format(totalEditFees);
    
    // Update the total fees display
    document.getElementById('totalEditFeesDisplay').textContent = formattedTotal;
}

// **Ensure total updates when input values change**
document.getElementById('editSemesterFields').addEventListener('input', function(event) {
    if (event.target.type === 'number') {
        updateTotalEditFees();
    }
});

// **Ensure total updates when the edit button toggles**
document.querySelectorAll('.edit-fee-btn').forEach(button => {
    button.addEventListener('click', function() {
        setTimeout(updateTotalEditFees, 200); // Delay update for smoother UX
    });
});


    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
