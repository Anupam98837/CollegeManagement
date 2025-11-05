document.addEventListener("DOMContentLoaded", function() {
    if (!sessionStorage.getItem("token")) {
        // Redirect if token is missing.
        window.location.href = "/";
    }
    // Fetch campuses on page load
    fetchCampuses();
});


// Global variables for campus data and pagination settings
let allCampuses = [];
let currentPage = 1;
const rowsPerPage = 5; // Updated to 10 per page
const token = sessionStorage.getItem('token');

// Event listener for showing the Add Campus form when the button is clicked
document.getElementById('showAddCampusForm').addEventListener('click', function () {
    // Hide the Edit Campus Form if it's visible and reset it
    document.getElementById('editCampusForm').classList.add('d-none');
    document.getElementById('editCampusForm').reset();

    // Show the Add Campus Form
    document.getElementById('addCampusForm').classList.remove('d-none');
    
    // Update the breadcrumb text (if needed)
    document.querySelector('.admin_add_campus_text').textContent = 'Add Campus';

    // Optionally scroll to the top so the form is in view
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

function hideAddCampusForm() {
    document.getElementById('addCampusForm').classList.add('d-none');
}

// Fetch and display all campuses from the API
function fetchCampuses() {
    fetch('/api/get-campuses', {
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
        if (data.status === 'success') {
            // Store full campus data in the global variable
            allCampuses = data.data;
            currentPage = 1; // Reset page when new data arrives
            updateTableAndPagination();
        } else {
            console.error('Error fetching campuses:', data.message);
        }
    })
    .catch(error => {
        console.error('Error fetching campuses:', error);
    });
}

// Update table and pagination controls based on current data and page
function updateTableAndPagination(query = '') {
    const filteredData = getFilteredData(query);
    renderTable(getPaginatedData(filteredData));
    renderPaginationButtons(filteredData);
}

// Returns filtered campus data based on search query (or all if query is empty)
function getFilteredData(query = '') {
    if (!query) {
        return allCampuses;
    }
    const lowerQuery = query.toLowerCase();
    return allCampuses.filter(campus =>
        Object.values(campus).some(val =>
            String(val).toLowerCase().includes(lowerQuery)
        )
    );
}

// Returns a slice of data for the current page
function getPaginatedData(filteredData) {
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    return filteredData.slice(start, end);
}

// Populate campuses into the table for the current page
function renderTable(campuses) {
    const campusTableBody = document.getElementById('campusTableBody');
    campusTableBody.innerHTML = ''; // Clear the table body

    if (!campuses || campuses.length === 0) {
        campusTableBody.innerHTML = `
            <tr>
                <td class="bg-white" colspan="100%">
                    <p class="mb-0">
                        <i class="fa-solid fa-info-circle me-2"></i>No data found
                    </p>
                </td>
            </tr>`;
        return;
    }

    campuses.forEach(campus => {
        const row = document.createElement('tr');

        row.innerHTML = `
        <td class="text-13 text-center align-middle">${campus.campus_name}</td>
        <td class="text-13 text-center align-middle">${campus.campus_id}</td>
        <td class="text-13 text-center align-middle" style="color: ${campus.status === 'Active' ? 'green' : 'red'};">
            ${campus.status}
        </td>
        <td class="text-13 text-center align-middle">${new Date(campus.created_at).toLocaleString()}</td>
        <td class="text-center align-middle">
            <button class="btn btn-sm btn-outline-secondary text-13 m-1" onclick="editCampus('${campus.campus_id}', '${campus.campus_name}')">
                <i class="fa-regular fa-pen-to-square"></i>
            </button>
           
        </td>`;    
        campusTableBody.appendChild(row);
    });
}

{/* <button class="btn btn-sm ${campus.status === 'Active' ? 'btn-outline-danger' : 'btn-outline-success'} text-13 m-1" onclick="toggleStatus('${campus.campus_id}', '${campus.status}')">
<i class="fa-solid ${campus.status === 'Active' ? 'fa-ban' : 'fa-power-off'}"></i>
</button> */}

// Render pagination buttons using uniform style with arrow icons and a page label.
function renderPaginationButtons(filteredData) {
    const paginationContainer = document.getElementById('paginationContainer');
    // Clear existing buttons
    paginationContainer.innerHTML = '';

    const totalPages = Math.ceil(filteredData.length / rowsPerPage);

    // Helper to create a button with uniform style
    function createButton(innerHTML, page, disabled = false) {
        const btn = document.createElement('button');
        btn.innerHTML = innerHTML;
        btn.classList.add('btn', 'btn-outline-primary', 'mx-1', 'text-13');
        btn.disabled = disabled;
        if (!disabled) {
            btn.addEventListener('click', function() {
                changePage(page, document.getElementById('searchCampus').value);
            });
        }
        return btn;
    }

    // Create First button
    paginationContainer.appendChild(createButton('<i class="fa-solid fa-angles-left"></i>', 1, currentPage === 1));

    // Create Previous button
    paginationContainer.appendChild(createButton('<i class="fa-solid fa-angle-left"></i>', currentPage - 1, currentPage === 1));

    // Create page label button (disabled)
    const pageLabel = createButton(`${currentPage} / ${totalPages}`, currentPage, true);
    paginationContainer.appendChild(pageLabel);

    // Create Next button
    paginationContainer.appendChild(createButton('<i class="fa-solid fa-angle-right"></i>', currentPage + 1, currentPage === totalPages));

    // Create Last button
    paginationContainer.appendChild(createButton('<i class="fa-solid fa-angles-right"></i>', totalPages, currentPage === totalPages));
}

// Helper function to change pages
function changePage(page, query = '') {
    currentPage = page;
    const filteredData = getFilteredData(query);
    renderTable(getPaginatedData(filteredData));
    renderPaginationButtons(filteredData);
}

// Filter campuses based on search input and update pagination accordingly
document.getElementById('searchCampus').addEventListener('input', function () {
    const query = this.value;
    currentPage = 1; // Reset to first page on search
    updateTableAndPagination(query);
});

// Submit Add Campus form
document.getElementById('addCampusForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const submitButton = document.getElementById('submitButton');
    const buttonText = document.getElementById('buttonText');
    const buttonSpinner = document.getElementById('buttonSpinner');

    const campusName = document.getElementById('campusName').value;
    const campusId = document.getElementById('campusId').value;

    if (!campusName || !campusId) {
        Swal.fire('Error', 'All fields are required.', 'error');
        return;
    }

    // Show loading spinner
    buttonText.classList.add('d-none');
    buttonSpinner.classList.remove('d-none');
    submitButton.disabled = true;

    fetch('/api/add-campus', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': `${token}`
        },
        body: JSON.stringify({
            campus_name: campusName,
            campus_id: campusId
        })
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
            document.getElementById('addCampusForm').reset();
            hideAddCampusForm();
            fetchCampuses();
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

// Edit Campus Action
function editCampus(campusId, campusName) {
    // Hide Add Campus Form and show Edit Campus Form
    document.getElementById('addCampusForm').classList.add('d-none');
    document.getElementById('editCampusForm').classList.remove('d-none');
    document.querySelector('.admin_add_campus_text').textContent = 'Edit Campus';
    // Populate the edit form with the provided values
    document.getElementById('editCampusId').value = campusId;
    document.getElementById('editCampusName').value = campusName;
}

// Submit Edit Campus form
document.getElementById('editCampusForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const editSubmitButton = document.getElementById('editSubmitButton');
    const editButtonText = document.getElementById('editButtonText');
    const editButtonSpinner = document.getElementById('editButtonSpinner');
    window.scrollTo({ top: 0, behavior: 'smooth' });

    const campusId = document.getElementById('editCampusId').value;
    const updatedCampusName = document.getElementById('editCampusName').value;

    if (!updatedCampusName) {
        Swal.fire('Error', 'Campus name is required.', 'error');
        return;
    }

    // Show loading spinner
    editButtonText.classList.add('d-none');
    editButtonSpinner.classList.remove('d-none');
    editSubmitButton.disabled = true;

    fetch(`/api/edit-campus/${campusId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': `${token}`
        },
        body: JSON.stringify({ campus_name: updatedCampusName })
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
            document.getElementById('editCampusForm').classList.add('d-none');
            document.getElementById('editCampusForm').reset();
            fetchCampuses();
        } else {
            Swal.fire('Error', data.message || 'An error occurred while updating the campus.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
    })
    .finally(() => {
        editButtonText.classList.remove('d-none');
        editButtonSpinner.classList.add('d-none');
        editSubmitButton.disabled = false;
    });
});

// Cancel Edit Functionality
function cancelEdit() {
    document.getElementById('editCampusForm').classList.add('d-none');
    document.querySelector('.admin_add_campus_text').textContent = 'Add Campus';
    document.getElementById('editCampusForm').reset();
}

// Toggle campus status action
function toggleStatus(campusId, state) {
    const newState = state === 'Active' ? 'Inactive' : 'Active';

    Swal.fire({
        title: 'Are you sure?',
        text: `This will change the campus status to ${newState}.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, change it!',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/toggle-campus-status/${campusId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Authorization': `${token}`
                },
                body: JSON.stringify({ status: newState }),
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
                    fetchCampuses();
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
