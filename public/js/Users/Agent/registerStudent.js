document.addEventListener("DOMContentLoaded", function() {
    if (!sessionStorage.getItem("token")) {
      // Redirect to blank path or your preferred path if token is missing.
      window.location.href = "/";
    }
  });
let currentStep = 1;


        function changeStep(step) {
            const steps = document.querySelectorAll(".step");
            steps[currentStep - 1].classList.remove("active");
            currentStep += step;

            // Handle button visibility
            if (currentStep <= 1) {
                currentStep = 1;
                document.getElementById("prevBtn").style.display = "none";
            } else {
                document.getElementById("prevBtn").style.display = "block";
            }

            if (currentStep >= steps.length) {
                currentStep = steps.length;
                document.getElementById("nextBtn").style.display = "none";
                document.getElementById("submitBtn").style.display = "block";
            } else {
                document.getElementById("nextBtn").style.display = "block";
                document.getElementById("submitBtn").style.display = "none";
            }

            steps[currentStep - 1].classList.add("active");
        }

        // Initialize navigation
        document.getElementById("prevBtn").style.display = "none";
        function showIdentityInput() {
        const identityType = document.getElementById("identityType").value;
        const identityInputDiv = document.getElementById("identityInputDiv");
        const identityLabel = document.getElementById("identityLabel");

        // Update label based on the selected option
        // if (identityType === "Aadhar") {
        //     identityLabel.textContent = "last 4 digit Aadhar Number *";
        // } else if (identityType === "Voter ID") {
        //     identityLabel.textContent = "last 4 digit Voter ID Number *";
        // } else if (identityType === "PAN") {
        //     identityLabel.textContent = "last 4 digit PAN Number *";
        // }
         // Update label based on the selected option
         if (identityType === "Aadhar") {
            identityLabel.innerHTML = "Aadhar Number <span style='color: red;'>*</span>";
        } else if (identityType === "Voter ID") {
            identityLabel.innerHTML = "Voter ID Number <span style='color: red;'>*</span>";
        } else if (identityType === "PAN") {
            identityLabel.innerHTML = "PAN Number <span style='color: red;'>*</span>";
        }

        // Show the input field
        identityInputDiv.style.display = "block";
    }
    function changeStep(step) {
        const steps = document.querySelectorAll(".step");
        const progressBar = document.getElementById("progressBarAnimated");
        const totalSteps = steps.length;
        const stepLabels = [
            "Basic Details",
            "Additional Details",
            "Address",
            "Guidance Details"
        ];

        steps[currentStep - 1].classList.remove("active");
        currentStep += step;

        if (currentStep <= 1) {
            currentStep = 1;
            document.getElementById("prevBtn").style.display = "none";
        } else {
            document.getElementById("prevBtn").style.display = "block";
        }

        if (currentStep >= steps.length) {
            currentStep = steps.length;
            document.getElementById("nextBtn").style.display = "none";
            document.getElementById("submitBtn").style.display = "block";
        } else {
            document.getElementById("nextBtn").style.display = "block";
            document.getElementById("submitBtn").style.display = "none";
        }

        steps[currentStep - 1].classList.add("active");

        // Update progress bar
        const progressPercentage = (currentStep / totalSteps) * 100;
        progressBar.style.width = progressPercentage + "%";
        progressBar.setAttribute("aria-valuenow", progressPercentage);

        // Highlight the current step in step details
        document.querySelectorAll(".step-details span").forEach((label, index) => {
            label.classList.remove("text-primary");
            if (index === currentStep - 1) {
                label.classList.add("text-primary");
            }
        });
    }
    // NEW: Function to change directly to a specified step without removing any other code.
    function changeStepTo(stepNumber) {
        const steps = document.querySelectorAll(".step");
        steps.forEach((step, index) => {
            step.classList.remove("active");
            if (index === stepNumber - 1) {
                step.classList.add("active");
                currentStep = stepNumber;
            }
        });
        // Update navigation buttons
        if (currentStep <= 1) {
            document.getElementById("prevBtn").style.display = "none";
        } else {
            document.getElementById("prevBtn").style.display = "block";
        }
        if (currentStep >= steps.length) {
            document.getElementById("nextBtn").style.display = "none";
            document.getElementById("submitBtn").style.display = "block";
        } else {
            document.getElementById("nextBtn").style.display = "block";
            document.getElementById("submitBtn").style.display = "none";
        }
        // Update progress bar if exists
        if (document.getElementById("progressBarAnimated")) {
            const progressBar = document.getElementById("progressBarAnimated");
            const progressPercentage = (currentStep / steps.length) * 100;
            progressBar.style.width = progressPercentage + "%";
            progressBar.setAttribute("aria-valuenow", progressPercentage);
        }
    }
    function toggleGuardian() {
    const guardianSections = document.querySelectorAll('.admin_localGurdian_field');
    const addGuardianBtn = document.getElementById('addGuardianBtn');
    const as_localgurdian_fields = document.querySelectorAll('.as_localgurdian_field');
    console.log(as_localgurdian_fields);
    // Check if the first field is hidden
    const isHidden = guardianSections[0].style.display === 'none' || guardianSections[0].style.display === '';

    if (isHidden) {
        // Show all fields
        guardianSections.forEach(section => {
            section.style.display = 'block';
        });
        as_localgurdian_fields.forEach(input => {
            input.required = true;
        });
        addGuardianBtn.textContent = 'Remove Local Guardian';
        addGuardianBtn.classList.replace('btn-outline-primary', 'btn-outline-danger');
    } else {
        // Hide all fields
        guardianSections.forEach(section => {
            section.style.display = 'none';
        });
        as_localgurdian_fields.forEach(input => {
            input.required = false;
            input.value = null; // Clear the value

        });
        addGuardianBtn.textContent = 'Add Local Guardian';
        addGuardianBtn.classList.replace('btn-outline-danger', 'btn-outline-primary');

        // Clear input values when hiding fields
        guardianSections.forEach(section => {
            const input = section.querySelector('input');
            if (input) {
                input.value = '';
            }
        });
    }
}


    function autoFillAddress(role) {
    const street = document.getElementById(role + "Street");
    const po = document.getElementById(role + "PO");
    const ps = document.getElementById(role + "PS");
    const city = document.getElementById(role + "City");
    const state = document.getElementById(role + "State");
    const country = document.getElementById(role + "Country");
    const pincode = document.getElementById(role + "Pincode");

    if (document.getElementById(role + "AutoFill").checked) {
        // Populate fields with student's address
        street.value = document.getElementById("city").value;
        po.value = document.getElementById("po").value;
        ps.value = document.getElementById("ps").value;
        city.value = document.getElementById("city").value;
        state.value = document.getElementById("state").value;
        country.value = document.getElementById("country").value;
        pincode.value = document.getElementById("pin").value;
    } else {
        // Clear fields if unchecked
        street.value = "";
        po.value = "";
        ps.value = "";
        city.value = "";
        state.value = "";
        country.value = "";
        pincode.value = "";
    }
}
document.getElementById("as_showClassXButton").addEventListener("click", function() {
        // Get all elements with the class 'asr_classx_field'
        const classXFields = document.querySelectorAll(".asr_classx_field");

        // Loop through each element and make it visible
        classXFields.forEach(field => {
            field.style.display = "block";
        });

        // Optionally disable the button after showing fields
        this.disabled = true;
        this.textContent = "Add Class X Data ";
    });
    document.getElementById("as_addClassXIIBtn").addEventListener("click", function () {
        // Show all fields
        const fields = document.querySelectorAll(".asr_classxii_field");
        const input_fieldsXii = document.querySelectorAll(".as_classXIIExam");

        // console.log(input_fieldsXii);
        fields.forEach(field => {
            field.style.display = "block";
        });
        // Make all input fields required
        input_fieldsXii.forEach(input => {
            input.required = true;
        });

        // Disable the button after activation
        this.disabled = true;
        this.textContent = "Add Class XII Data";
    });
    document.getElementById("as_addCollegeBtn").addEventListener("click", function () {
        // Show all fields for the College section
        const collegeFields = document.querySelectorAll(".asr_college_field");
        const input_fieldscollage = document.querySelectorAll(".as_collagefield");

        collegeFields.forEach(field => {
            field.style.display = "block";
        });
        input_fieldscollage.forEach(input => {
            input.required = true;
        });

        // Disable the button after activation
        this.disabled = true;
        this.textContent = "Add College Data";
    });
    

   // Populate the institution select element.
   // Global variable to store fetched course data.
let globalCourseData = [];

function populateInstituteSelect() {
  fetch('/api/view-institutions', {
    method: 'GET',
    headers: {
      'Accept': 'application/json',
      'Authorization': sessionStorage.getItem('token')
    }
  })
    .then(response => response.json())
    .then(data => {
      const select = document.getElementById('institute');
      select.innerHTML = '<option value="" disabled selected>Select Institute</option>';
      if (data.status === 'success' && data.data.length > 0) {
        data.data.forEach(inst => {
          const option = document.createElement('option');
          // Create an object containing all the necessary institute data.
          const instInfo = {
            institution_id: inst.id?.$oid || inst._id || inst.id,
            institution_name: inst.institution_name,
            institution_type: inst.type,
            institution_short_code: inst.institution_short_code
            // Add any other fields you need...
          };

          // Store the entire object as a JSON string in the option's value.
          option.value = JSON.stringify(instInfo);
          option.textContent = inst.institution_name;
          select.appendChild(option);
        });
      } else {
        select.innerHTML = '<option value="">No institutions available</option>';
      }
    })
    .catch(error => {
      console.error('Error fetching institutions:', error);
      Swal.fire('Error', 'Failed to load institutions.', 'error');
    });
}

function populateCourseDropdown(courseData) {
  const courseTypeSelect = document.getElementById("courseType");
  courseTypeSelect.innerHTML = '<option value="" disabled selected>Select Course</option>';
  
  // Get the current year as a string.
  const currentYear = new Date().getFullYear().toString();

  // Filter courseData by current year.
  const filteredData = courseData.filter(course => course.intake_year === currentYear);

  filteredData.forEach(course => {
    const option = document.createElement("option");
    
    // Create an object with necessary data.
    const courseInfo = {
      program_code: course.program_code,
      program_name: course.program_name,
      program_type: course.program_type,
      intake_type: course.intake_type,
      intake_year: course.intake_year,
      program_duration: course.program_duration,
      fee_type: course.fee_type
    };

    option.value = JSON.stringify(courseInfo); // Store as a JSON string.
    option.textContent = `${course.program_name} (${course.program_type}) (${course.intake_type})`;
    
    courseTypeSelect.appendChild(option);
  });
}

document.addEventListener('DOMContentLoaded', function() {
  // Populate the institute dropdown.
  populateInstituteSelect();

  // When the institute changes, fetch the courses/fees data.
  const instituteSelect = document.getElementById('institute');
  instituteSelect.addEventListener('change', function() {
    const instInfo = JSON.parse(this.value);
    const institutionId = instInfo.institution_id; // Extract the actual institution ID
    if (institutionId) {
      fetch(`/api/view-fees?institution_id=${institutionId}`, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Authorization': sessionStorage.getItem('token')
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
        if (data.status === "success") {
          // Store the fetched course data globally.
          globalCourseData = data.data;
          // If a fee type is already selected, trigger the change event to update the course dropdown.
          const feeTypeSelect = document.getElementById('feesType');
          if (feeTypeSelect.value) {
            feeTypeSelect.dispatchEvent(new Event('change'));
          }
        } else {
          console.error("Failed to fetch course data", data.message);
        }
      })
      .catch(error => console.error("Error fetching course data:", error));
    }
  });

  // When the fee type changes, filter and populate the course dropdown.
  const feeTypeSelect = document.getElementById('feesType');
  feeTypeSelect.addEventListener('change', function() {
    const selectedFeeType = this.value; // Expected values: 'gen', 'ews', or 'tfw'
    if (globalCourseData && globalCourseData.length > 0) {
      // Filter global course data by the selected fee type.
      const filteredCourses = globalCourseData.filter(course => course.fee_type === selectedFeeType);
      populateCourseDropdown(filteredCourses);
    } else {
      // If no course data is available, show an appropriate message.
      const courseTypeSelect = document.getElementById("courseType");
      courseTypeSelect.innerHTML = '<option value="">No courses available</option>';
    }
  });
});

  
    
    
function handleCourseTypeChange() {
    // Get selected course type
    const courseTyperaw = document.getElementById("courseType").value;
    const courseData = JSON.parse(courseTyperaw); // Convert string to JSON object
    console.log(courseData)
    const courseType = courseData.program_type;
    const as_classxbtn = document.getElementById("as_showClassXButton");
    const as_classxiibtn = document.getElementById("as_addClassXIIBtn");
    const as_collagebtn = document.getElementById("as_addCollegeBtn");
    const xiifields = document.querySelectorAll(".asr_classxii_field");
    const input_fieldsXii = document.querySelectorAll(".as_classXIIExam");
    const input_fieldscollage = document.querySelectorAll(".as_collagefield");
    const class_xii_data = document.querySelector("#class_xii_data");
    const college_data = document.querySelector("#college_data");
    const collegeFields = document.querySelectorAll(".asr_college_field");
    const addClassXIIBtn = document.getElementById("as_addClassXIIBtn")
    const addCollegeBtn = document.getElementById("as_addCollegeBtn")
    // console.log(courseTyperaw);
    // Display and set 'required' based on course type
    if (courseType === "PG") {
        addClassXIIBtn.disabled = false;
        addClassXIIBtn.innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Class XII Data';
        addCollegeBtn.disabled = false;
        addCollegeBtn.innerHTML = '<i class="fa-solid fa-pen-to-square"></i> College Data';
        as_classxbtn.style.display = "block";
        as_classxiibtn.style.display = "block";
        as_collagebtn.style.display = "block";

    } else if (courseType === "UG") {
        as_collagebtn.style.display = "none";
        addClassXIIBtn.disabled = false;
        input_fieldscollage.forEach(input => {
            input.required = false;
            input.value = null; // Clear the value
        });
        college_data.value = null;
        addClassXIIBtn.innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Class XII Data';
        collegeFields.forEach(field => {
            field.style.display = "none";
        });
        as_classxbtn.style.display = "block";
        as_classxiibtn.style.display = "block";
        
    } else if (courseType === "DIPLOMA" || courseType === "ITI") {
        as_collagebtn.style.display = "none";
        as_classxiibtn.style.display = "none";
        xiifields.forEach(field => {
            field.style.display = "none";
        });
        console.log(college_data.value)
        college_data.value = null;
        class_xii_data.value = null;
         
        input_fieldsXii.forEach(input => {
            input.required = false;
            input.value = null; // Clear the value
        });
        input_fieldscollage.forEach(input => {
            input.required = false;
            input.value = null; // Clear the value
        });
        collegeFields.forEach(field => {
            field.style.display = "none";
        });

        as_classxbtn.style.display = "block";
    }
}


function addRow(tableId, classType) {
    const tableBody = document.getElementById(tableId);
    const newRow = document.createElement('tr');

    newRow.innerHTML = `
        <td><input type="text" class="form-control text-13 placeholder-14" placeholder="Enter Subject" required></td>
        <td><input type="number" class="form-control text-13 placeholder-14" placeholder="Enter Full Marks" required></td>
        <td><input type="number" class="form-control text-13 placeholder-14" placeholder="Enter Marks Obtained" required></td>
        <td><input type="text" class="form-control text-13 placeholder-14" placeholder="%" readonly></td>
        <td><button type="button" class="btn btn-outline-danger text-10 placeholder-14" onclick="removeRow(this, '${classType}')">Remove</button></td>
    `;

    const inputs = newRow.querySelectorAll('input');
    const marksField = inputs[2];
    const fullMarksField = inputs[1];
    const percentageField = inputs[3];

    // Calculate percentage dynamically
    marksField.addEventListener('input', () => {
        calculatePercentage(fullMarksField, marksField, percentageField);
        updateHiddenInput(classType);
    });
    fullMarksField.addEventListener('input', () => {
        calculatePercentage(fullMarksField, marksField, percentageField);
        updateHiddenInput(classType);
    });

    tableBody.appendChild(newRow);
    updateHiddenInput(classType);
}

function removeRow(button, classType) {
    button.closest('tr').remove();
    updateHiddenInput(classType);
}

function calculatePercentage(fullMarksField, marksField, percentageField) {
    const fullMarks = parseFloat(fullMarksField.value);
    const marksObtained = parseFloat(marksField.value);

    if (fullMarks && marksObtained) {
        const percentage = ((marksObtained / fullMarks) * 100).toFixed(2);
        percentageField.value = `${percentage}%`;
    } else {
        percentageField.value = '';
    }
}

function updateHiddenInput(classType) {
    let tableId, hiddenInputId;

    // Map the tableId and hiddenInputId based on classType
    if (classType === 'class_x') {
        tableId = 'classXMarksTable';
        hiddenInputId = 'class_x_data';
    } else if (classType === 'class_xii') {
        tableId = 'classXIIMarksTable';
        hiddenInputId = 'class_xii_data';
    } else if (classType === 'college') {
        tableId = 'collegeMarksTable';
        hiddenInputId = 'college_data';
    }

    const tableRows = document.getElementById(tableId).querySelectorAll('tr');
    const data = [];

    tableRows.forEach(row => {
        const inputs = row.querySelectorAll('input');
        const subject = inputs[0].value;
        const fullMarks = inputs[1].value;
        const marksObtained = inputs[2].value;
        const percentage = inputs[3].value;

        if (subject && fullMarks && marksObtained) {
            data.push({ subject, fullMarks, marksObtained, percentage });
        }
    });

    document.getElementById(hiddenInputId).value = JSON.stringify(data);
}

document.querySelector(".admin_ragister_student_form").addEventListener("submit", function (event) {
    event.preventDefault(); // Prevent the default form submission behavior

    // NEW: Remove any existing error messages
    document.querySelectorAll(".error-message").forEach(msg => msg.remove());

     // Validate required fields
     let missingFields = [];
     const requiredFields = this.querySelectorAll("[required]");
     console.log("Checking required fields...");
     requiredFields.forEach(field => {
         const fieldValue = field.value.trim();
         console.log(`Field "${field.name}" (${field.id}): "${fieldValue}"`);
         if (!fieldValue) {
             const label = document.querySelector(`label[for="${field.id}"]`);
             const labelText = label ? label.textContent.replace("*", "").trim() : field.name;
             // NEW: Push the field element and its label text
             missingFields.push({ field: field, labelText: labelText });
         }
     });
 
     if (missingFields.length > 0) {
         console.log("Missing fields:", missingFields.map(item => item.labelText));
         // NEW: Redirect to the step of the first missing field
         const missingField = missingFields[0];
         let stepElement = missingField.field.closest(".step");
         if (stepElement && stepElement.id) {
             const stepNumber = parseInt(stepElement.id.split("-")[1]);
             changeStepTo(stepNumber);
         }
         // NEW: Append an error message below each missing field
         missingFields.forEach(item => {
             let parent = item.field.parentElement;
             if (!parent.querySelector(".error-message")) {
                 const errorMessage = document.createElement("div");
                 errorMessage.className = "error-message text-danger text-10 mt-1";
                 errorMessage.textContent = "This field is required.";
                 parent.appendChild(errorMessage);
             }
         });
         Swal.fire("Missing Fields", "Please fill in the required fields highlighted.", "error");
         return;
     }
 
    

    const formData = new FormData(this); // Collect form data
    const token = sessionStorage.getItem('token');
    const designation = sessionStorage.getItem('designation');

    // Iterate over formData entries and log each key and value.

  
    // Make the API call
    fetch("/api/agent/register-student", {
        method: "POST",
        body: formData,
        headers: {
            "Accept": "application/json",
            "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
            "Authorization": sessionStorage.getItem('token'),
            "Designation": sessionStorage.getItem('designation'),
            "Agent-Name": sessionStorage.getItem('agent_name'),
            "Agent-ID": sessionStorage.getItem('agent_id'),
            "Agent-Email": sessionStorage.getItem('agent_email'),
            "Agent-Phone": sessionStorage.getItem('agent_phone')
        },
    }) 
    .then(response => {
        if (response.status === 401 || response.status === 403) {
            // ✅ Redirect if unauthorized
            window.location.href = '/Unauthorised';
            throw new Error('Unauthorized Access');
        }
        return response.json();
    })
        .then((data) => {
            if (data.status === "success") {
                // Show success message
                Swal.fire("Success", data.message, "success");
                // Reset the form and progress
                // localStorage.setItem("registeredStudentEmail", formData.get("email"));
                this.reset();
                resetSteps();
                // window.location.href = '/student/documents/upload';
            } else {
                // Show error message
                Swal.fire("Error", data.message || "Something went wrong", "error");
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            Swal.fire("Error", "Failed to connect to the server", "error");
        });
});

// Reset the steps and progress bar after successful submission
function resetSteps() {
    const steps = document.querySelectorAll(".step");
    steps.forEach((step) => step.classList.remove("active"));
    steps[0].classList.add("active");

    const progressBar = document.getElementById("progressBarAnimated");
    progressBar.style.width = "25%";
    progressBar.setAttribute("aria-valuenow", "25");

    document.getElementById("prevBtn").style.display = "none";
    document.getElementById("nextBtn").style.display = "block";
    document.getElementById("submitBtn").style.display = "none";
}
