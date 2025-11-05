// Check token and setup on DOMContentLoaded
document.addEventListener("DOMContentLoaded", function() {
    if (!sessionStorage.getItem("token")) {
        window.location.href = "/";
    }
    // Fetch campuses for dropdowns
    fetch('/api/get-campuses', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Authorization': `${sessionStorage.getItem('token')}`
        },
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
            const campusDropdown = document.getElementById('campusDropdown');
            const editCampusDropdown = document.getElementById('aei_editCampusDropdown');
            data.data.filter(campus => campus.status === 'Active')
            .forEach(campus => {
                const addOption = document.createElement('option');
                const editOption = document.createElement('option');
                addOption.value = campus.campus_id;
                addOption.textContent = campus.campus_name;
                editOption.value = campus.campus_id;
                editOption.textContent = campus.campus_name;
                campusDropdown.appendChild(addOption);
                editCampusDropdown.appendChild(editOption);
            });
        }
    })
    .catch(error => console.error('Error fetching campuses:', error));

    // Fetch institutions on page load
    fetchInstitutions();
});

const token = sessionStorage.getItem('token');

// --- Institution Form Handling ---

document.getElementById('showAddInstitutionForm').addEventListener('click', function () {
    // Hide the edit form and reset it
    document.getElementById('aei_editInstitutionForm').classList.add('d-none');
    document.getElementById('aei_editInstitutionForm').reset();
    // Show the add institution form
    document.getElementById('addInstitutionForm').classList.remove('d-none');
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

function hideAddInstitutionForm() {
    document.getElementById('addInstitutionForm').classList.add('d-none');
}

document.getElementById('addInstitutionForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const submitButton = document.getElementById('submitButton');
    const buttonText = document.getElementById('buttonText');
    const buttonSpinner = document.getElementById('buttonSpinner');

    // Create FormData instance
    const formData = new FormData();

    // Append form fields
    formData.append('campus_id', document.getElementById('campusDropdown').value);
    formData.append('institution_name', document.getElementById('institutionName').value);
    formData.append('institution_short_code', document.getElementById('institutionShortCode').value);
    formData.append('type', document.getElementById('type').value);
    formData.append('url', document.getElementById('url').value);
    formData.append('street', document.getElementById('street').value);
    formData.append('po', document.getElementById('po').value);
    formData.append('ps', document.getElementById('ps').value);
    formData.append('city', document.getElementById('city').value);
    formData.append('state', document.getElementById('state').value);
    formData.append('country', document.getElementById('country').value);
    formData.append('pincode', document.getElementById('pincode').value);
    formData.append('contact_no', document.getElementById('contact').value);
    formData.append('email_id', document.getElementById('email').value);

    // Append file if selected
    const imageInput = document.getElementById('institutionImage');
    if (imageInput.files.length > 0) {
        formData.append('logo', imageInput.files[0]);
    }

    // Validate required fields (adjust accordingly, image is optional here)
    if (!formData.get('campus_id') || !formData.get('institution_name') || !formData.get('type') ||
        !formData.get('street') || !formData.get('po') || !formData.get('ps') || !formData.get('city') ||
        !formData.get('state') || !formData.get('country') || !formData.get('pincode')) {
        Swal.fire('Error', 'Please fill in all required fields.', 'error');
        return;
    }

    // Show spinner
    buttonText.classList.add('d-none');
    buttonSpinner.classList.remove('d-none');
    submitButton.disabled = true;

    fetch('/api/add-institution', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': `${token}`
        },
        body: formData, // send FormData here
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
            document.getElementById('addInstitutionForm').reset();
            hideAddInstitutionForm();
            fetchInstitutions();
        } else {
            Swal.fire('Error', data.message || 'An error occurred.', 'error');
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


// --- Global Variables for Institutions Pagination ---
let allInstitutions = [];
let currentInstitutionPage = 1;
const institutionsPerPage = 20; // Adjust as needed

// Fetch institutions and setup pagination
function fetchInstitutions() {
    fetch('/api/view-institutions', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Authorization': `${token}`
        },
    })
    .then(response => {
        if (response.status === 401 || response.status === 403) {
            window.location.href = '/Unauthorised';
            throw new Error('Unauthorized Access');
        }
        return response.json();
    })
    .then(data => {
        const tableBody = document.getElementById('institutionTableBody');
        // Clear table and show loading spinner while processing
        tableBody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </td>
            </tr>
        `;
        if (data.status === 'success' && data.data.length > 0) {
            allInstitutions = data.data; // Store complete data
            currentInstitutionPage = 1;
            updateInstitutionTableAndPagination();
        } else {
            tableBody.innerHTML = `<tr><td colspan="10" class="bg-white"><p class="mb-0"><i class="fa-solid fa-info-circle me-2"></i>No data found</p></td></tr>`;
        }
    })
    .catch(error => {
        console.error('Error fetching institutions:', error);
        document.getElementById('institutionTableBody').innerHTML = `
            <tr>
                <td colspan="10" class="text-center text-danger">Failed to fetch data. Please try again.</td>
            </tr>
        `;
    });
}

// Update institution table and pagination buttons based on current page and search query
function updateInstitutionTableAndPagination(query = '') {
    const filteredData = getFilteredInstitutions(query);
    renderInstitutionsTable(getPaginatedInstitutions(filteredData));
    renderInstitutionPaginationButtons(filteredData);
}

// Filter institutions based on search query
function getFilteredInstitutions(query = '') {
    if (!query) return allInstitutions;
    const lowerQuery = query.toLowerCase();
    return allInstitutions.filter(inst => {
        return (
            String(inst.campus_id).toLowerCase().includes(lowerQuery) ||
            String(inst.institution_name).toLowerCase().includes(lowerQuery) ||
            String(inst.type).toLowerCase().includes(lowerQuery)
        );
    });
}

// Get a slice of institutions for the current page
function getPaginatedInstitutions(filteredData) {
    const start = (currentInstitutionPage - 1) * institutionsPerPage;
    return filteredData.slice(start, start + institutionsPerPage);
}

// Add this function to handle logo updates
function updateInstitutionLogo(institutionId, currentLogoPath) {
    Swal.fire({
        title: 'Update Institution Logo',
        html: `
            <div class="text-center mb-3">
                ${currentLogoPath ? 
                    `<img src="${currentLogoPath}" alt="Current Logo" class="img-fluid mb-3" style="max-height: 200px;">` : 
                    '<p>No logo currently set</p>'}
            </div>
            <input type="file" id="logoUpload" class="form-control" accept="image/*">
        `,
        showCancelButton: true,
        confirmButtonText: 'Update Logo',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const fileInput = document.getElementById('logoUpload');
            if (!fileInput.files || fileInput.files.length === 0) {
                Swal.showValidationMessage('Please select an image file');
                return false;
            }
            return fileInput.files[0];
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const formData = new FormData();
            formData.append('logo', result.value);
            
            // Show loading state
            Swal.fire({
                title: 'Uploading...',
                html: 'Please wait while we update the logo',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/api/update-institution-logo/${institutionId}`, {
                method: 'POST',  // Changed from PUT to POST
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Authorization': `${token}` // ensure token is defined globally or replace accordingly
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
                    fetchInstitutions(); // Refresh the institutions table or list
                } else {
                    Swal.fire('Error', data.message || 'Failed to update logo', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
            });
        }
    });
}


// Modify the renderInstitutionsTable function to include the edit button
function renderInstitutionsTable(institutions) {
    const tableBody = document.getElementById('institutionTableBody');
    tableBody.innerHTML = '';

    if (!institutions || institutions.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="11" class="bg-white"><p class="mb-0"><i class="fa-solid fa-info-circle me-2"></i>No data found</p></td></tr>`;
        return;
    }

    institutions.forEach(inst => {
        const isSelected = sessionStorage.getItem('institution_id') === inst.id.$oid;

        const logoPath = inst.logo 
            ? `/${inst.logo}` 
            : '/assets/web_assets/logo.png';

        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="text-center align-middle">
                <div class="position-relative d-inline-block" style="width: 100px; height: auto;">
                    <img src="${logoPath}" alt="Institution Logo" width="100px" style="object-fit: contain; display: block;" />
                    <button 
                        class="btn btn-sm btn-outline-primary position-absolute edit-btn"
                        onclick="updateInstitutionLogo('${inst.id.$oid}', '${logoPath}')"
                        style="
                            padding: 0.15rem 0.3rem; 
                            border-radius: 50%; 
                            bottom: 5px; 
                            right: 5px; 
                            display: none;
                            width: 28px;
                            height: 28px;
                            line-height: 1;
                            font-size: 14px;
                        ">
                        <i class="fa-solid fa-pen fa-xs"></i>
                    </button>
                </div>
            </td>
            <!-- rest of your columns unchanged -->
            <td class="text-13 text-center align-middle">${inst.campus_id}</td>
            <td class="text-13 text-center align-middle">${inst.institution_name}</td>
            <td class="text-13 text-center align-middle">${inst.type}</td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-outline-secondary btn-sm text-13" onclick="aaiva_viewAddress('${inst.street}', '${inst.po}', '${inst.ps}', '${inst.city}', '${inst.state}', '${inst.country}', '${inst.pincode}')">
                    <i class="fa-regular fa-eye"></i>
                </button>
            </td>
            <td class="text-13 text-center align-middle">
                ${inst.url ? `<a href="${inst.url}" target="_blank" class="text-decoration-none"><i class="fa-solid fa-link text-primary" title="Visit URL"></i></a>` : 'N/A'}
            </td>
            <td class="text-13 text-center align-middle">${inst.contact_no || 'N/A'}</td>
            <td class="text-13 text-center align-middle">${inst.email_id || 'N/A'}</td>
            <td class="text-13 text-center align-middle" style="color: ${inst.status === 'Active' ? 'green' : 'red'};">
                ${inst.status}
            </td>
            <td class="text-13 text-center align-middle">${new Date(inst.created_at).toLocaleString()}</td>
            <td class="text-center align-middle">
                <button class="btn btn-sm btn-outline-secondary text-13 m-1" onclick="editInstitution('${inst.id.$oid}', '${inst.institution_name}', '${inst.campus_id}', '${inst.type}', '${inst.street}', '${inst.po}', '${inst.ps}', '${inst.city}', '${inst.state}', '${inst.country}', '${inst.pincode}', '${inst.url || ''}', '${inst.contact_no || ''}', '${inst.email_id || ''}')">
                    <i class="fa-solid fa-pen-to-square"></i>
                </button>
                <button 
                class="btn btn-sm ${isSelected ? 'btn-outline-danger' : 'btn-outline-success'} text-13 m-1"
                onclick="${isSelected ? `unselectInstitution()` : `selectInstitution('${inst.id.$oid}', '${inst.institution_name}', '${inst.type}', '${logoPath}')`}">
                <i class="fa-solid fa-${isSelected ? 'ban' : 'check'}"></i>
                </button>

            </td>
        `;

        tableBody.appendChild(row);
    });

    // Add hover event listeners for showing/hiding edit button
    document.querySelectorAll('td .position-relative').forEach(container => {
        container.addEventListener('mouseenter', () => {
            const btn = container.querySelector('.edit-btn');
            if (btn) btn.style.display = 'block';
        });
        container.addEventListener('mouseleave', () => {
            const btn = container.querySelector('.edit-btn');
            if (btn) btn.style.display = 'none';
        });
    });
}

{/* <button class="btn btn-sm ${inst.status === 'Active' ? 'btn-outline-danger' : 'btn-outline-success'} text-13 m-1" onclick="toggleInstitutionStatus('${inst.id.$oid}', '${inst.status}')">
<i class="fa-solid ${inst.status === 'Active' ? 'fa-ban' : 'fa-power-off'}"></i>
</button> */}

// --- Institution Pagination Code Start ---
// Render dynamic pagination buttons for institutions with arrow icons and page label (X / Y)
function renderInstitutionPaginationButtons(filteredData) {
    const paginationContainer = document.getElementById('institutionPaginationContainer');
    paginationContainer.innerHTML = '';
    const totalPages = Math.ceil(filteredData.length / institutionsPerPage);

    // Helper to create a button with uniform style
    function createButton(innerHTML, page, disabled = false) {
         const btn = document.createElement('button');
         btn.innerHTML = innerHTML;
         btn.classList.add('btn', 'btn-outline-primary', 'mx-1', 'text-13');
         btn.disabled = disabled;
         if (!disabled) {
             btn.addEventListener('click', function() {
                 changeInstitutionPage(page, document.getElementById('institutionSearch').value);
             });
         }
         return btn;
    }

    // Create First button
    paginationContainer.appendChild(createButton('<i class="fa-solid fa-angles-left"></i>', 1, currentInstitutionPage === 1));

    // Create Previous button
    paginationContainer.appendChild(createButton('<i class="fa-solid fa-angle-left"></i>', currentInstitutionPage - 1, currentInstitutionPage === 1));

    // Create page label button (disabled)
    const pageLabel = createButton(`${currentInstitutionPage} / ${totalPages}`, currentInstitutionPage, true);
    paginationContainer.appendChild(pageLabel);

    // Create Next button
    paginationContainer.appendChild(createButton('<i class="fa-solid fa-angle-right"></i>', currentInstitutionPage + 1, currentInstitutionPage === totalPages));

    // Create Last button
    paginationContainer.appendChild(createButton('<i class="fa-solid fa-angles-right"></i>', totalPages, currentInstitutionPage === totalPages));
}
// --- Institution Pagination Code End ---

// Helper function to change institution page
function changeInstitutionPage(page, query = '') {
    currentInstitutionPage = page;
    const filteredData = getFilteredInstitutions(query);
    renderInstitutionsTable(getPaginatedInstitutions(filteredData));
    renderInstitutionPaginationButtons(filteredData);
}

// Filter institutions based on search event and update pagination accordingly
document.getElementById('institutionSearch').addEventListener('input', function () {
    const query = this.value;
    currentInstitutionPage = 1;
    updateInstitutionTableAndPagination(query);
});

// --- Address View ---
function aaiva_viewAddress(street, po, ps, city, state, country, pincode) {
    const addressHtml = `
        <ul style="text-align: left; list-style: none; padding: 0;">
            <li><strong>Street:</strong> ${street || 'N/A'}</li>
            <li><strong>PO:</strong> ${po || 'N/A'}</li>
            <li><strong>PS:</strong> ${ps || 'N/A'}</li>
            <li><strong>City:</strong> ${city || 'N/A'}</li>
            <li><strong>State:</strong> ${state || 'N/A'}</li>
            <li><strong>Country:</strong> ${country || 'N/A'}</li>
            <li><strong>Pincode:</strong> ${pincode || 'N/A'}</li>
        </ul>
    `;
    Swal.fire({
        title: 'Full Address',
        html: addressHtml,
        icon: 'info',
        confirmButtonText: 'Close',
    });
}

// --- Edit Institution Functionality ---
function editInstitution(id, institutionName, campusId, type, street, po, ps, city, state, country, pincode, url, contactNo, emailId) {
    // Hide add form and show edit form
    document.getElementById('addInstitutionForm').classList.add('d-none');
    document.querySelector('.admin_add_Institution_text').textContent = 'Edit Institution';
    document.getElementById('aei_editInstitutionForm').classList.remove('d-none');
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // Populate the edit form
    document.getElementById('aei_editInstitutionId').value = id;
    document.getElementById('aei_editInstitutionName').value = institutionName;
    document.getElementById('aei_editCampusDropdown').value = campusId;
    document.getElementById('aei_editType').value = type;
    document.getElementById('aei_editStreet').value = street;
    document.getElementById('aei_editPo').value = po;
    document.getElementById('aei_editPs').value = ps;
    document.getElementById('aei_editCity').value = city;
    document.getElementById('aei_editState').value = state;
    document.getElementById('aei_editCountry').value = country;
    document.getElementById('aei_editPincode').value = pincode;
    document.getElementById('aei_editUrl').value = url || '';
    document.getElementById('aei_editContact').value = contactNo || '';
    document.getElementById('aei_editEmail').value = emailId || '';

    // Remove duplicate listener and add submission handler
    const editForm = document.getElementById('aei_editInstitutionForm');
    editForm.removeEventListener('submit', submitEditInstitution);
    editForm.addEventListener('submit', submitEditInstitution);
}

function submitEditInstitution(e) {
    e.preventDefault();
    const editSubmitButton = document.getElementById('aei_editSubmitButton');
    const editButtonText = document.getElementById('aei_editButtonText');
    const editButtonSpinner = document.getElementById('aei_editButtonSpinner');

    const updatedData = {
        id: document.getElementById('aei_editInstitutionId').value,
        campus_id: document.getElementById('aei_editCampusDropdown').value,
        institution_name: document.getElementById('aei_editInstitutionName').value,
        type: document.getElementById('aei_editType').value,
        street: document.getElementById('aei_editStreet').value,
        po: document.getElementById('aei_editPo').value,
        ps: document.getElementById('aei_editPs').value,
        city: document.getElementById('aei_editCity').value,
        state: document.getElementById('aei_editState').value,
        country: document.getElementById('aei_editCountry').value,
        pincode: document.getElementById('aei_editPincode').value,
        url: document.getElementById('aei_editUrl').value,
        contact_no: document.getElementById('aei_editContact').value,
        email_id: document.getElementById('aei_editEmail').value,
    };

    editButtonText.classList.add('d-none');
    editButtonSpinner.classList.remove('d-none');
    editSubmitButton.disabled = true;

    fetch(`/api/edit-institution/${updatedData.id}`, {
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
    .then((data) => {
        if (data.status === 'success') {
            Swal.fire('Success', data.message, 'success');
            document.querySelector('.admin_add_Institution_text').textContent = 'Add Institution';
            fetchInstitutions(); // Refresh list with updated data
            cancelEditInstitution();
        } else {
            Swal.fire('Error', data.message || 'An error occurred while updating the campus.', 'error');
        }
    })
    .catch((error) => {
        console.error('Error:', error);
        Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
    })
    .finally(() => {
        editButtonText.classList.remove('d-none');
        editButtonSpinner.classList.add('d-none');
        editSubmitButton.disabled = false;
    });
}

function cancelEditInstitution() {
    document.getElementById('aei_editInstitutionForm').classList.add('d-none');
    document.querySelector('.admin_add_Institution_text').textContent = 'Add Institution';
    document.getElementById('aei_editInstitutionForm').reset();
}

function toggleInstitutionStatus(institutionId, currentStatus) {
    const newStatus = currentStatus === 'Active' ? 'Inactive' : 'Active';
    Swal.fire({
        title: 'Change Institution Status',
        text: `Are you sure you want to change the status to ${newStatus}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, change it!',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/toggle-institution-status/${institutionId}`, {
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
                    fetchInstitutions();
                } else {
                    Swal.fire('Error', data.message || 'An error occurred.', 'error');
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
            });
        }
    });
}

/**
 * Selects an institution and stores it in sessionStorage.
 */
function selectInstitution(institutionId, institutionName, institutionType, institutionLogo) {
    sessionStorage.setItem("institution_id", institutionId);
    sessionStorage.setItem("institution_name", institutionName);
    sessionStorage.setItem("institution_type", institutionType);
    sessionStorage.setItem("institution_logo", institutionLogo);

    Swal.fire('Selected', `Institution "${institutionName}" selected.`, 'success')
      .then(() => location.reload()); 
}

  
  /**
   * Clears the selected institution.
   */
  function unselectInstitution() {
    sessionStorage.removeItem("institution_id");
    sessionStorage.removeItem("institution_name");
    sessionStorage.removeItem("institution_type");
    sessionStorage.removeItem("institution_logo");

    Swal.fire('Unselected', `No institution is selected now.`, 'info')
      .then(() => location.reload());
}

  
// --- Institution Pagination Code End ---
