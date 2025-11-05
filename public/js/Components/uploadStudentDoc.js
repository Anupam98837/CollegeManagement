document.addEventListener("DOMContentLoaded", function() {
    if (!sessionStorage.getItem("token")) {
      // Redirect to blank path or your preferred path if token is missing.
      window.location.href = "/";
    }
  });
// Global flag to indicate update mode (data found)
let isUpdateMode = false;

let auud_email = localStorage.getItem("registeredStudentEmail");
if (auud_email != null) {
    document.getElementById('auud_email').value = auud_email;
}

/**
 * Adds a file upload field.
 *
 * @param {string} type - The label text (e.g., "Student Photo").
 * @param {string} name - The input name attribute.
 * @param {string|null} existingFile - Existing file name/path (if any).
 * @param {boolean} required - Whether the field is required when not in update mode.
 */
function addFileUploadField(type, name, existingFile = null, required = false) {
    const container = document.getElementById('Admin_student_ragister_documentUploadFields');

    // Create a new div for the file input field.
    const fileInputGroup = document.createElement('div');
    fileInputGroup.className = 'col-md-6 p-3 rounded bg-light';
    fileInputGroup.style = "background-color: #f9f9f9; border-left: 4px solid #007bff;";

    // Create the label.
    const label = document.createElement('label');
    label.className = 'form-label text-secondary d-flex align-items-center';
    label.innerHTML = `<span><i class="fa-solid fa-file-alt me-2"></i> ${type}</span> ${required ? '<span class="text-danger">*</span>' : ''}`;

    // Create the file input element.
    const input = document.createElement('input');
    input.type = 'file';
    // If an existing file is provided, hide the file input by default.
    input.className = 'form-control placeholder-14 text-13' + (existingFile ? ' d-none' : '');
    input.name = name;
    input.accept = '.jpg,.jpeg,.png,.pdf';

    // Add the instruction text.
    const instruction = document.createElement('small');
    instruction.className = 'text-muted' + (existingFile ? ' d-none' : '');
    instruction.innerText = 'Accepted formats: JPEG, JPG, PNG, PDF';

    // Only add required attribute if not in update mode.
    if (required && !isUpdateMode) {
        input.required = true;
    }

    // If an existing file exists, add view and edit buttons.
    if (existingFile) {
        // Container for file actions.
        const fileActions = document.createElement('div');
        fileActions.className = 'd-flex align-items-center mt-2';

        // "View" button.
        const fileLink = document.createElement('a');
        fileLink.href = `/assets/student_documents/${existingFile}`;
        fileLink.target = '_blank';
        fileLink.className = 'btn btn-outline-primary m-2 text-decoration-none text-13';
        fileLink.innerHTML = `<i class="fa-solid fa-eye"></i> View`;
        label.appendChild(fileLink);

        // "Edit" button.
        const editButton = document.createElement('button');
        editButton.type = 'button';
        editButton.className = 'btn btn-outline-secondary m-2 text-decoration-none text-13';
        editButton.innerHTML = `<i class="fa-regular fa-pen-to-square"></i> Edit`;
        editButton.addEventListener('click', function () {
            if (input.classList.contains('d-none')) {
                input.classList.remove('d-none');
                instruction.classList.remove('d-none');
                editButton.className = 'btn btn-outline-danger m-2 text-decoration-none text-13';
                editButton.innerHTML = `<i class="fa-solid fa-times"></i> Cancel`;
            } else {
                input.classList.add('d-none');
                instruction.classList.add('d-none');
                editButton.className = 'btn btn-outline-secondary m-2 text-decoration-none text-13';
                editButton.innerHTML = `<i class="fa-regular fa-pen-to-square"></i> Edit`;
            }
        });
        label.appendChild(editButton);
    } else {
        // If no existing file, show the input and instruction immediately.
        input.classList.remove('d-none');
        instruction.classList.remove('d-none');
    }

    // Append the created elements.
    fileInputGroup.appendChild(label);
    fileInputGroup.appendChild(input);
    fileInputGroup.appendChild(instruction);
    container.appendChild(fileInputGroup);

    return existingFile !== null;
}

document.getElementById('auud_searchStudentForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const email = document.getElementById('auud_email').value;
    const searchButton = e.target.querySelector('button[type="submit"]');
    const searchButtonText = document.getElementById('searchButtonText');
    const searchButtonSpinner = document.getElementById('searchButtonSpinner');

    // Show loading spinner.
    searchButton.disabled = true;
    searchButtonText.classList.add('d-none');
    searchButtonSpinner.classList.remove('d-none');
    const token = sessionStorage.getItem('token');

    fetch('/api/get-student-by-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `${token}`
        },
        body: JSON.stringify({ email })
    })
    .then(response => {
        if (response.status === 401 || response.status === 403) {
            window.location.href = '/Unauthorised';
            throw new Error('Unauthorized Access');
        }
        return response.json();
    })
    .then(data => {
        const uploadForm = document.getElementById('uploadDocumentForm');
        const container = document.getElementById('Admin_student_ragister_documentUploadFields');
        const uploadButton = uploadForm.querySelector('button[type="submit"]');

        if (data.status === 'success') {
            // Set update mode to true so fields are not required.
            isUpdateMode = true;
            document.getElementById('search_Data_div').classList.add('d-none')
            // Show the upload form and clear previous fields.
            uploadForm.classList.remove('d-none');
            container.innerHTML = '';

            // Populate fields. In update mode, required attribute is not added.
            addFileUploadField('Student Photo', 'student_photo', data.data.student_photo, true);
            addFileUploadField(`${data.data.identity_type} (Student)`, 'student_identity', data.data.student_identity, true);

            // Parent documents.
            addFileUploadField('Father Photo', 'father_photo', data.data.father_photo, true);
            addFileUploadField('Father Identity', 'father_identity', data.data.father_identity, true);
            addFileUploadField('Mother Photo', 'mother_photo', data.data.mother_photo, true);
            addFileUploadField('Mother Identity', 'mother_identity', data.data.mother_identity, true);

            // Local Guardian documents (if applicable).
            if (data.data.guardian_name) {
                addFileUploadField('Local Guardian Photo', 'guardian_photo', data.data.guardian_photo, true);
                addFileUploadField('Local Guardian Identity', 'guardian_identity', data.data.guardian_identity, true);
            }

            // Marksheet documents.
            addFileUploadField('Class X Marksheet', 'class_x_marksheet', data.data.class_x_marksheet, true);
            if (data.data.class_xii_exam_name) {
                addFileUploadField('Class XII Marksheet', 'class_xii_marksheet', data.data.class_xii_marksheet, true);
            }
            if (data.data.college_name) {
                addFileUploadField('College Marksheet', 'college_marksheet', data.data.college_marksheet, true);
            }

            // Set hidden email value.
            document.getElementById('asud_emailHidden').value = email;

            // Change the upload button text and dataset to update mode.
            uploadButton.textContent = 'Update Documents';
            uploadButton.dataset.action = 'update';
        } else {
            // If no student data is found, treat this as a new upload.
            isUpdateMode = false;
            uploadForm.classList.remove('d-none');
            container.innerHTML = '';

            // Populate fields without existing files.
            addFileUploadField('Student Photo', 'student_photo', null, true);
            addFileUploadField('Student Identity', 'student_identity', null, true);
            addFileUploadField('Father Photo', 'father_photo', null, true);
            addFileUploadField('Father Identity', 'father_identity', null, true);
            addFileUploadField('Mother Photo', 'mother_photo', null, true);
            addFileUploadField('Mother Identity', 'mother_identity', null, true);
            // Optionally add Local Guardian and marksheet fields as needed

            document.getElementById('asud_emailHidden').value = email;

            // Set the upload button dataset for new upload.
            uploadButton.textContent = 'Upload Documents';
            uploadButton.dataset.action = 'upload';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Failed to fetch student data. Please try again.', 'error');
        // Ensure the form stays hidden in case of error.
        document.getElementById('uploadDocumentForm').classList.add('d-none');
        document.getElementById('Admin_student_ragister_documentUploadFields').innerHTML = '';
    })
    .finally(() => {
        // Hide loading spinner.
        searchButton.disabled = false;
        searchButtonText.classList.remove('d-none');
        searchButtonSpinner.classList.add('d-none');
    });
});

document.getElementById('uploadDocumentForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const uploadButton = e.target.querySelector('button[type="submit"]');
    const formData = new FormData(this);
    const action = uploadButton.dataset.action; // "update" or "upload"

    // Show loading spinner on the button.
    uploadButton.disabled = true;
    uploadButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...`;
    const token = sessionStorage.getItem('token');

    // Change API endpoint based on whether we're updating or uploading new documents.
    const apiUrl = action === 'update' ? '/api/update-student-documents' : '/api/upload-student-documents';

    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': `${token}`
        },
        body: formData
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
            document.getElementById('uploadDocumentForm').reset();
            document.getElementById('Admin_student_ragister_documentUploadFields').innerHTML = '';
            localStorage.removeItem("registeredStudentEmail");
            document.getElementById('uploadDocumentForm').classList.add('d-none');
        } else {
            Swal.fire('Error', data.message || 'Failed to process request.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'An error occurred while processing. Please try again.', 'error');
    })
    .finally(() => {
        uploadButton.disabled = false;
        uploadButton.innerHTML = action === 'update' ? 'Update Documents' : 'Upload Documents';
    });
});
