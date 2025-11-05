document.addEventListener("DOMContentLoaded", function() {
    if (!sessionStorage.getItem("token")) {
      // Redirect to blank path or your preferred path if token is missing.
      window.location.href = "/";
    }
  });
document.addEventListener('DOMContentLoaded', function () {
    const token = sessionStorage.getItem('token');
    const auud_email = sessionStorage.getItem("student_email");

    if (auud_email) {
        document.getElementById('auud_email').value = auud_email;
        searchStudentData(auud_email);
    }
});

function searchStudentData(email) {
    const searchButtonText = document.getElementById('searchButtonText');
    const searchButtonSpinner = document.getElementById('searchButtonSpinner');

    searchButtonText.classList.add('d-none');
    searchButtonSpinner.classList.remove('d-none');

    fetch('/api/get-student-by-email', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': `${sessionStorage.getItem('token')}`
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
        const uploadButton = document.querySelector('.Admin_student_ragister_documentUploadFields_btn');
        const registerContainer = document.getElementById('registerMessageContainer'); // Correctly selecting the new div

        if (data.status === 'success') {
            uploadForm.classList.remove('d-none');
            container.innerHTML = '';

            let hasExistingFiles = false;

            hasExistingFiles |= addFileUploadField('Student Photo', 'student_photo', data.data.student_photo);
            hasExistingFiles |= addFileUploadField(`${data.data.identity_type} (Student)`, 'student_identity', data.data.student_identity);
            hasExistingFiles |= addFileUploadField('Father Photo', 'father_photo', data.data.father_photo);
            hasExistingFiles |= addFileUploadField('Father Identity', 'father_identity', data.data.father_identity);
            hasExistingFiles |= addFileUploadField('Mother Photo', 'mother_photo', data.data.mother_photo);
            hasExistingFiles |= addFileUploadField('Mother Identity', 'mother_identity', data.data.mother_identity);

            if (data.data.guardian_name) {
                hasExistingFiles |= addFileUploadField('Local Guardian Photo', 'guardian_photo', data.data.guardian_photo);
                hasExistingFiles |= addFileUploadField('Local Guardian Identity', 'guardian_identity', data.data.guardian_identity);
            }

            hasExistingFiles |= addFileUploadField('Class X Marksheet', 'class_x_marksheet', data.data.class_x_marksheet);
            if (data.data.class_xii_exam_name) {
                hasExistingFiles |= addFileUploadField('Class XII Marksheet', 'class_xii_marksheet', data.data.class_xii_marksheet);
            }
            if (data.data.college_name) {
                hasExistingFiles |= addFileUploadField('College Marksheet', 'college_marksheet', data.data.college_marksheet);
            }

            document.getElementById('asud_emailHidden').value = email;

            if (hasExistingFiles) {
                uploadButton.textContent = 'Update Documents';
                uploadButton.dataset.action = 'update';
            } else {
                uploadButton.textContent = 'Upload Documents';
                uploadButton.dataset.action = 'upload';
            }
        } else {
            // uploadForm.classList.add('d-none');
            container.innerHTML = '';
            uploadForm.classList.add('d-none');
            registerContainer.classList.remove('d-none'); // Show the message

        }
    })
    .catch(error => {
        const uploadForm = document.getElementById('uploadDocumentForm');
        const registerContainer = document.getElementById('registerMessageContainer'); // Correctly selecting the new div
        uploadForm.classList.add('d-none');
        console.error('Error:', error);
        registerContainer.classList.remove('d-none'); // Show the message
    })
    .finally(() => {
        searchButtonText.classList.remove('d-none');
        searchButtonSpinner.classList.add('d-none');
    });
}

function addFileUploadField(type, name, existingFile = null) {
    const container = document.getElementById('Admin_student_ragister_documentUploadFields');
    const fileInputGroup = document.createElement('div');
    fileInputGroup.className = 'col-md-6 p-3  rounded  bg-light ';
    fileInputGroup.style = "background-color: #f9f9f9; border-left: 4px solid #007bff;";


    const label = document.createElement('label');
    label.className = 'form-label text-secondary d-flex align-items-center';
    label.innerHTML = `<span><i class="fa-solid fa-file-alt me-2"></i> ${type}</spam>`;

    const input = document.createElement('input');
    input.type = 'file';
    input.className = 'form-control placeholder-14 text-13 d-none'; // Initially hidden for updates
    input.name = name;
    input.accept = '.jpg,.jpeg,.png,.pdf';

    const instruction = document.createElement('small');
    instruction.className = 'text-muted d-none'; // Hide instruction initially
    instruction.innerText = 'Accepted formats: JPEG, JPG, PNG, PDF';

    if (existingFile) {
        const fileActions = document.createElement('div');
        fileActions.className = 'd-flex align-items-center mt-2';

        const fileLink = document.createElement('a');
        fileLink.href = `/assets/student_documents/${existingFile}`;
        fileLink.target = '_blank';
        fileLink.className = 'btn btn-outline-primary m-2  text-decoration-none text-13';
        fileLink.innerHTML = `<i class="fa-solid fa-eye"></i> View`;
        label.appendChild(fileLink);

        const editButton = document.createElement('button');
        editButton.type = 'button';
        editButton.className = 'btn btn-outline-secondary m-2 text-decoration-none text-13';
        editButton.innerHTML = `<i class="fa-regular fa-pen-to-square"></i> Edit`;
        editButton.addEventListener('click', function () {
            if (input.classList.contains('d-none')) {
                input.classList.remove('d-none');
                instruction.classList.remove('d-none');
                editButton.className = 'btn btn-outline-danger m-2 text-decoration-none text-13';
                editButton.innerHTML = `<i class="fa-solid fa-times"></i> Cancel`; // Change to cancel
            } else {
                input.classList.add('d-none');
                instruction.classList.add('d-none');
                editButton.className = 'btn btn-outline-secondary m-2 text-decoration-none text-13';
                editButton.innerHTML = `<i class="fa-regular fa-pen-to-square"></i> Edit`; // Change back to edit
            }
        });
        label.appendChild(editButton);
    } else {
        input.classList.remove('d-none'); // Always visible for first-time uploads
        instruction.classList.remove('d-none');
    }

    fileInputGroup.appendChild(label);
    fileInputGroup.appendChild(input);
    fileInputGroup.appendChild(instruction);
    container.appendChild(fileInputGroup);

    return existingFile !== null;
}

document.getElementById('uploadDocumentForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const uploadButton = e.target.querySelector('button[type="submit"]');
    const formData = new FormData(this);
    const action = uploadButton.dataset.action;

    uploadButton.disabled = true;
    uploadButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...`;

    const apiUrl = action === 'update' ? '/api/update-student-documents' : '/api/upload-student-documents';

    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': `${sessionStorage.getItem('token')}`
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
            searchStudentData(document.getElementById('auud_email').value);
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
