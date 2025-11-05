<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta http-equiv="X-UA-Compatible" content="ie=edge" />
  <title>Expense Categories & Expenses</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Custom CSS -->
  <style>
    .icon-rotate { animation: rotation 2s infinite linear; }
    @keyframes rotation {
      from { transform: rotate(0deg); }
      to { transform: rotate(359deg); }
    }
    .d-none { display: none; }
  </style>
</head>
<body>
      <div class="container mt-4">
        <!-- Institution Info Card -->
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


        <p class="my-4 text-secondary text-14">
          <i class="fa-solid fa-angle-right"></i>
          <span class="text-primary">Manage Expense</span>
        </p>

        <!-- Institution Dropdown -->
        <div class="bg-white p-4 rounded mb-4" id="institutionDropdownContainer">
          <label for="institutionSelect" class="form-label text-13">Select Institution</label>
          <select id="institutionSelect" class="form-select text-13">
            <option value="" disabled selected>Loading institutions...</option>
          </select>
        </div>

        <!-- Default Message (Shown by default) -->
        <div id="default_expense_div" class="p-4 bg-white rounded text-center d-flex flex-column justify-content-center align-items-center vh-50">
          <img src="{{ asset('assets/web_assets/search.png') }}" alt="Search Icon" style="width: 300px;">
          <p class="fs-5">Select an Institution first</p>
        </div>

        <!-- Tabs Container (Hidden until an institution is selected) -->
        <div id="tabsContainer" class="d-none">
          <!-- Tabs Navigation (Default tab is now Expenses) -->
          <ul class="nav nav-tabs" id="expenseTabNavigation">
            <li class="nav-item">
              <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#expensesContent">Expenses</button>
            </li>
            <li class="nav-item">
              <button class="nav-link" data-bs-toggle="tab" data-bs-target="#expenseCategoriesContent">Expense Categories</button>
            </li>
          </ul>

          <!-- Tabs Content -->
          <div class="tab-content" id="expenseTabContent">
            <!-- Expenses Tab -->
            <div class="tab-pane fade show active" id="expensesContent">
              <!-- Header with Filters, Sort, and Add Expense Button -->
              <div id="expenseTableContainerExp" class="bg-white p-4">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                  <div class="d-flex gap-2">
                    <!-- Date Range Filters -->
                    <input type="date" id="fromDate" class="form-control form-control-sm" placeholder="From Date" onchange="updateExpensesPagination()">
                    <input type="date" id="toDate" class="form-control form-control-sm" placeholder="To Date" onchange="updateExpensesPagination()">
                    <!-- Expense Category Filter Dropdown -->
                    <select id="expenseCategoryFilter" class="form-select form-select-sm" onchange="updateExpensesPagination()">
                      <option value="All" selected>All Categories</option>
                    </select>
                    <!-- Sort Order Dropdown -->
                    <select id="expenseSortOrder" class="form-select form-select-sm" onchange="updateExpensesPagination()">
                      <option value="recent" selected>Recent</option>
                      <option value="oldest">Oldest</option>
                    </select>
                  </div>
                  <button id="addExpenseBtnExp" class="btn btn-outline-primary btn-sm">Add Expense</button>
                </div>
                <!-- Expenses Table -->
                <div class="table-responsive">
                  <table class="table" id="expenseTableExpenses">
                    <thead class="table-light">
                      <tr>
                        <th class="text-secondary text-13">Title</th>
                        <th class="text-secondary text-13">Category</th>
                        <th class="text-secondary text-13">Amount</th>
                        <th class="text-secondary text-13">Invoice No.</th>
                        <th class="text-secondary text-13">Date</th>
                        <th class="text-secondary text-13">Note</th>
                        <th class="text-secondary text-13 text-center">Attachment</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Expense rows will be appended dynamically -->
                    </tbody>
                  </table>
                </div>
                <!-- Expenses Pagination Controls -->
                <div id="expensePaginationContainer" class="mt-3 text-center"></div>
              </div>
              <!-- Add Expense Form Container (Hidden by Default) -->
              <div id="expenseFormContainerExp" class="d-none position-relative">
                <!-- Back button absolutely positioned at the top right -->
                <button id="cancelExpenseBtnExp" class="btn btn-outline-secondary btn-sm position-absolute" style="top: 10px; right: 10px;">
                  Back to List
                </button>
                <form id="expenseFormExp" class="p-3 rounded" style="background-color: #f9fafc; box-shadow: rgba(0,0,0,0.02) 0px 1px 3px 0px, rgba(27,31,35,0.15) 0px 0px 0px 1px;" enctype="multipart/form-data">
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="expenseTitle" class="form-label text-13">Title <span class="text-danger">*</span></label>
                      <input type="text" id="expenseTitle" class="form-control text-13" placeholder="Enter Expense Title" required>
                    </div>
                    <div class="col-md-6">
                      <label for="expenseCategoryInput" class="form-label text-13">Category <span class="text-danger">*</span></label>
                      <!-- Category dropdown will be populated dynamically -->
                      <select id="expenseCategoryInput" class="form-select text-13" required>
                        <option value="" disabled selected>Select Category</option>
                      </select>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="expenseAmount" class="form-label text-13">Amount <span class="text-danger">*</span></label>
                      <input type="number" id="expenseAmount" class="form-control text-13" placeholder="Enter Amount" required>
                    </div>
                    <div class="col-md-6">
                      <label for="invoiceNumber" class="form-label text-13">Invoice Number</label>
                      <input type="text" id="invoiceNumber" class="form-control text-13" placeholder="Enter Invoice Number (Optional)">
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="expenseDate" class="form-label text-13">Expense Date <span class="text-danger">*</span></label>
                      <input type="date" id="expenseDate" class="form-control text-13" required>
                    </div>
                    <div class="col-md-6">
                      <label for="expenseAttachment" class="form-label text-13">Attachment</label>
                      <input type="file" id="expenseAttachment" class="form-control text-13">
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="expenseNote" class="form-label text-13">Note</label>
                    <textarea id="expenseNote" class="form-control text-13" placeholder="Enter Note (Optional)" rows="3"></textarea>
                  </div>
                  <div class="text-end">
                    <button type="button" id="saveExpenseBtnExp" class="btn btn-outline-primary text-13">Save Expense</button>
                  </div>
                </form>
              </div>
            </div><!-- End Expenses Tab -->

            <!-- Expense Categories Tab -->
            <div class="tab-pane fade" id="expenseCategoriesContent">
              <!-- Expense Categories Table Container -->
              <div id="expenseTableContainer" class="bg-white p-4">
                <!-- Header with Search, Filter, & Add Button -->
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
                  <div class="d-flex gap-2">
                    <input type="text" id="expenseSearchInput" class="form-control form-control-sm" placeholder="Search category..." onkeyup="filterExpenseTable()">
                    <select id="expenseFilterSelect" class="form-select form-select-sm" onchange="filterExpenseTable()">
                      <option value="active" selected>Active</option>
                      <option value="inactive">Inactive</option>
                      <option value="all">All</option>
                    </select>
                  </div>
                  <button id="addExpenseBtn" class="btn btn-outline-primary btn-sm">Add</button>
                </div>
                <!-- Expense Categories Table -->
                <div class="table-responsive">
                  <table class="table" id="expenseTable">
                    <thead class="table-light">
                      <tr>
                        <th class="text-secondary text-13">Category Name</th>
                        <th class="text-secondary text-13">Description</th>
                        <th class="text-secondary text-13 text-center">Status</th>
                        <th class="text-secondary text-13 text-end">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Rows will be appended dynamically -->
                    </tbody>
                  </table>
                </div>
                <div id="expenseCategoryPaginationContainer" class="mt-3 text-center"></div>
              </div>
              <!-- Expense Category Form Container (Hidden by Default) -->
              <div id="expenseFormContainer" class="d-none position-relative">
                <!-- Back button is absolutely positioned at the top right -->
                <button id="backToTableBtn" class="btn btn-outline-secondary btn-sm position-absolute" style="top: 10px; right: 10px;">
                  Back to List
                </button>
                <form id="expenseForm" class="p-3 rounded" style="background-color: #f9fafc; box-shadow: rgba(0,0,0,0.02) 0px 1px 3px 0px, rgba(27,31,35,0.15) 0px 0px 0px 1px;">
                  <div class="row mb-3">
                    <div class="col-md-12">
                      <label for="categoryName" class="form-label text-13">
                        Category Name <span class="text-danger">*</span>
                      </label>
                      <input type="text" id="categoryName" class="form-control text-13" placeholder="Enter Expense Category Name" required>
                    </div>
                    <div class="col-md-12">
                      <label for="categoryDescription" class="form-label text-13">Description</label>
                      <textarea id="categoryDescription" class="form-control text-13" placeholder="Enter Description (Optional)" rows="3"></textarea>
                    </div>
                  </div>
                  <div class="text-end">
                    <button type="button" id="saveExpenseBtn" class="btn btn-outline-primary text-13">Save</button>
                  </div>
                </form>
              </div>              
            </div><!-- End Expense Categories Tab -->

          </div><!-- End Tabs Content -->
        </div><!-- End Tabs Container -->
      </div><!-- End Container -->

  <!-- Bootstrap Bundle JS & SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Global variables.
    const token = sessionStorage.getItem('token');
    let allExpenseCategories = []; // For dropdown updates (from expense categories API)
    let allExpenseCategoriesData = []; // For expense categories table pagination
    let currentPageExpenseCategories = 1;
    const rowsPerPageExpenseCategories = 5;
    
    let allExpensesData = [];
    let currentPageExpenses = 1;
    const rowsPerPageExpenses = 5;

    // Redirect if token is missing.
    document.addEventListener("DOMContentLoaded", () => {
      if (!token) {
        window.location.href = "/";
      }
    });

    // Show institution info if available.
    document.addEventListener("DOMContentLoaded", () => {
      const instName = sessionStorage.getItem("institution_name");
      const instType = sessionStorage.getItem("institution_type");
      const institutionInfoDiv = document.getElementById("institutionInfoDiv");
      if (instName && instType) {
        const instLogoPath = sessionStorage.getItem("institution_logo");
      const logoImg = document.getElementById("instImg");
      logoImg.src = instLogoPath || '/assets/web_assets/logo.png';
        document.getElementById("instituteName").innerHTML = `<span class="text-secondary">${instName}</span>`;
        document.getElementById("instituteType").innerHTML = `<i class="fa-solid fa-graduation-cap me-2"></i>${instType}`;
        institutionInfoDiv.classList.remove("d-none");
      }
    });

    // Fetch institutions and populate dropdown.
    function fetchInstitutions() {
      const institutionId = sessionStorage.getItem("institution_id");
      const institutionSelect = document.getElementById('institutionSelect');
      if (institutionId) {
        fetch(`/api/view-institution/${institutionId}`, {
          method: 'GET',
          headers: { 'Accept': 'application/json', 'Authorization': token }
        })
        .then(response => {
          if ([401,403].includes(response.status)) {
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
        fetch('/api/view-institutions', {
          method: 'GET',
          headers: { 'Accept': 'application/json', 'Authorization': token }
        })
        .then(response => {
          if ([401,403].includes(response.status)) {
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

    // When an institution is selected, hide default message, show tabs, and fetch data.
    document.addEventListener("DOMContentLoaded", () => {
      fetchInstitutions();
      document.getElementById('institutionSelect').addEventListener('change', function() {
        const institute_id = this.value;
        console.log("Selected Institution ID:", institute_id);
        document.getElementById("default_expense_div").classList.add("d-none");
        document.getElementById("tabsContainer").classList.remove("d-none");
        fetchExpenseCategories(institute_id);
        fetchExpenses(institute_id);
      });
    });

    // --- Expense Category (Add/Edit) Functions ---
    document.getElementById("addExpenseBtn").addEventListener("click", () => {
      clearExpenseForm();
      window.expenseEditId = null;
      toggleExpenseView(true);
    });

    document.getElementById("backToTableBtn").addEventListener("click", () => {
      toggleExpenseView(false);
    });

    document.getElementById("saveExpenseBtn").addEventListener("click", () => {
      const institution_id = document.getElementById('institutionSelect').value;
      const categoryName = document.getElementById("categoryName").value.trim();
      const categoryDescription = document.getElementById("categoryDescription").value.trim();
      if (!categoryName) {
        Swal.fire({ title: 'Error', text: 'Category Name is required.', icon: 'error', confirmButtonText: 'OK' });
        return;
      }
      if (window.expenseEditId) {
        fetch(`/api/edit-expense-category/${window.expenseEditId}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Authorization': token
          },
          body: JSON.stringify({ category_name: categoryName, description: categoryDescription }),
        })
        .then(response => {
          if ([401,403].includes(response.status)) {
            window.location.href = '/Unauthorised';
            throw new Error('Unauthorized Access');
          }
          return response.json();
        })
        .then(data => {
          if (data.status === 'success') {
            Swal.fire({ title: 'Success', text: data.message, icon: 'success', confirmButtonText: 'OK' })
            .then(() => { 
              toggleExpenseView(false);
              fetchExpenseCategories(institution_id);
              window.expenseEditId = null;
            });
          } else {
            throw new Error(data.message);
          }
        })
        .catch(error => {
          Swal.fire({ title: 'Error', text: error.message || 'An error occurred while updating the expense category.', icon: 'error', confirmButtonText: 'OK' });
          console.error('Error updating expense category:', error);
        });
      } else {
        const payload = {
          institution_id: institution_id,
          category_name: categoryName,
          description: categoryDescription
        };
        fetch("/api/expense-category", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            "Authorization": token
          },
          body: JSON.stringify(payload)
        })
        .then(response => {
          if ([401,403].includes(response.status)) {
            window.location.href = '/Unauthorised';
            throw new Error("Unauthorized Access");
          }
          return response.json();
        })
        .then(data => {
          if (data.status === "success") {
            Swal.fire({ title: 'Success', text: data.message, icon: 'success', confirmButtonText: 'OK' })
            .then(() => { 
              toggleExpenseView(false);
              fetchExpenseCategories(institution_id);
            });
          } else {
            Swal.fire({ title: 'Error', text: data.message, icon: 'error', confirmButtonText: 'OK' });
          }
        })
        .catch(error => {
          console.error("Error adding expense category:", error);
          Swal.fire({ title: 'Error', text: 'An error occurred while adding the expense category.', icon: 'error', confirmButtonText: 'OK' });
        });
      }
    });

    function clearExpenseForm() {
      document.getElementById("categoryName").value = "";
      document.getElementById("categoryDescription").value = "";
      window.expenseEditId = null;
    }

    function toggleExpenseView(showForm) {
      if (showForm) {
        document.getElementById("expenseFormContainer").classList.remove("d-none");
        document.getElementById("expenseTableContainer").classList.add("d-none");
      } else {
        document.getElementById("expenseFormContainer").classList.add("d-none");
        document.getElementById("expenseTableContainer").classList.remove("d-none");
      }
    }

    // --- Expense Category Pagination Functions ---
     allExpenseCategoriesData = [];
    currentPageExpenseCategories = 1;
    
    function fetchExpenseCategories(instituteId) {
      fetch(`/api/expense-categories?institution_id=${encodeURIComponent(instituteId)}`, {
        method: "GET",
        headers: {
          "Accept": "application/json",
          "Authorization": token
        }
      })
      .then(response => {
        if ([401,403].includes(response.status)) {
          window.location.href = '/Unauthorised';
          throw new Error("Unauthorized Access");
        }
        return response.json();
      })
      .then(data => {
        if (data.status === "success") {
          allExpenseCategoriesData = data.data;
          currentPageExpenseCategories = 1;
          updateExpenseCategoryTable();
          // Also update dropdowns for the Expenses tab add form and filter.
          allExpenseCategories = data.data;
          updateExpenseCategoryDropdowns(allExpenseCategories);
        } else {
          console.error("Error fetching expense categories", data.message);
        }
      })
      .catch(error => console.error("Error:", error));
    }
    
    function updateExpenseCategoryTable(query = '') {
      const filtered = allExpenseCategoriesData; // Filtering can be added if needed
      renderExpenseCategoryTable(getPaginatedExpenseCategories(filtered));
      renderExpenseCategoryPagination(filtered);
    }
    
    function getPaginatedExpenseCategories(data) {
      const start = (currentPageExpenseCategories - 1) * rowsPerPageExpenseCategories;
      const end = start + rowsPerPageExpenseCategories;
      return data.slice(start, end);
    }
    
    function renderExpenseCategoryTable(data) {
      const tbody = document.querySelector("#expenseTable tbody");
      tbody.innerHTML = "";
      if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="4" class="text-center">No Expense Categories Found.</td></tr>`;
        return;
      }
      // Sort: Active items first (if desired)
      data.sort((a, b) => {
        if (a.status === 'Active' && b.status !== 'Active') return -1;
        if (a.status !== 'Active' && b.status === 'Active') return 1;
        return 0;
      });
      data.forEach(expense => {
        const row = document.createElement("tr");
        row.classList.add("expense-category-row");
        row.setAttribute("data-status", (expense.status || "").toLowerCase());
        const expenseId = expense.id.$oid || expense.id;
        row.innerHTML = `
          <td class="text-13 align-middle text-start">${expense.category_name}</td>
          <td class="text-13 align-middle text-start">${expense.description || ''}</td>
          <td class="text-13 align-middle text-center">
            <span class="expense-status ${expense.status === 'Active' ? 'text-success' : 'text-danger'}">${expense.status}</span>
          </td>
          <td class="text-13 align-middle text-end">
            <button class="btn btn-outline-primary btn-sm expense-edit-btn" data-id="${expenseId}">
              <i class="fa-solid fa-pen-to-square"></i>
            </button>
            <button class="btn btn-outline-secondary btn-sm expense-toggle-btn" data-id="${expenseId}" onclick="toggleExpenseCategoryStatus('${expenseId}', '${expense.status}')" style="color: ${expense.status === 'Active' ? 'red' : 'green'}; border-color: ${expense.status === 'Active' ? 'red' : 'green'};">
              <i class="fa-solid ${expense.status === 'Active' ? 'fa-ban' : 'fa-power-off'}"></i>
            </button>
          </td>
        `;
        tbody.appendChild(row);
      });
      document.querySelectorAll(".expense-edit-btn").forEach(button => {
        button.addEventListener("click", () => {
          const expenseId = button.getAttribute("data-id");
          const row = button.closest("tr");
          const name = row.children[0].textContent.trim();
          const description = row.children[1].textContent.trim();
          document.getElementById("categoryName").value = name;
          document.getElementById("categoryDescription").value = description;
          window.expenseEditId = expenseId;
          toggleExpenseView(true);
        });
      });
    }
    
    function renderExpenseCategoryPagination(data) {
      const paginationContainer = document.getElementById("expenseCategoryPaginationContainer");
      paginationContainer.innerHTML = "";
      const totalPages = Math.ceil(data.length / rowsPerPageExpenseCategories);
      function createButton(innerHTML, page, disabled = false) {
        const btn = document.createElement("button");
        btn.innerHTML = innerHTML;
        btn.classList.add("btn", "btn-outline-primary", "mx-1", "text-13");
        btn.disabled = disabled;
        if (!disabled) {
          btn.addEventListener("click", function() {
            currentPageExpenseCategories = page;
            updateExpenseCategoryTable();
          });
        }
        return btn;
      }
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angles-left"></i>', 1, currentPageExpenseCategories === 1));
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angle-left"></i>', currentPageExpenseCategories - 1, currentPageExpenseCategories === 1));
      const pageLabel = createButton(`${currentPageExpenseCategories} / ${totalPages}`, currentPageExpenseCategories, true);
      paginationContainer.appendChild(pageLabel);
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angle-right"></i>', currentPageExpenseCategories + 1, currentPageExpenseCategories === totalPages));
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angles-right"></i>', totalPages, currentPageExpenseCategories === totalPages));
    }
    
    // --- Expenses Tab Pagination & Sorting Functions ---
     allExpensesData = [];
     currentPageExpenses = 1;
    
    function fetchExpenses(instituteId) {
      fetch(`/api/expenses?institution_id=${encodeURIComponent(instituteId)}`, {
        method: "GET",
        headers: { "Accept": "application/json", "Authorization": token }
      })
      .then(response => {
        if ([401,403].includes(response.status)) {
          window.location.href = '/Unauthorised';
          throw new Error("Unauthorized Access");
        }
        return response.json();
      })
      .then(data => {
        if (data.status === "success") {
          allExpensesData = data.data;
          currentPageExpenses = 1;
          updateExpensesPagination();
        } else {
          console.error("Error fetching expenses", data.message);
        }
      })
      .catch(error => console.error("Error:", error));
    }
    
    function updateExpensesPagination(query = '') {
      let filtered = allExpensesData;
      // Apply sort based on date column.
      const sortOrder = document.getElementById("expenseSortOrder").value;
      filtered.sort((a, b) => {
        return sortOrder === 'recent'
          ? new Date(b.expense_date) - new Date(a.expense_date)
          : new Date(a.expense_date) - new Date(b.expense_date);
      });
      // Optionally, you can filter by date range and category here.
      if(document.getElementById("fromDate").value) {
        const from = new Date(document.getElementById("fromDate").value);
        filtered = filtered.filter(expense => new Date(expense.expense_date) >= from);
      }
      if(document.getElementById("toDate").value) {
        const to = new Date(document.getElementById("toDate").value);
        filtered = filtered.filter(expense => new Date(expense.expense_date) <= to);
      }
      if(document.getElementById("expenseCategoryFilter").value !== "All") {
        const catFilter = document.getElementById("expenseCategoryFilter").value;
        filtered = filtered.filter(expense => expense.category === catFilter);
      }
      renderExpensesTable(getPaginatedExpenses(filtered));
      renderExpensePagination(filtered);
    }
    
    function getPaginatedExpenses(data) {
      const start = (currentPageExpenses - 1) * rowsPerPageExpenses;
      const end = start + rowsPerPageExpenses;
      return data.slice(start, end);
    }
    
    function renderExpensesTable(data) {
      const tbody = document.querySelector("#expenseTableExpenses tbody");
      tbody.innerHTML = "";
      if (data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center">No Expenses Found.</td></tr>`;
        return;
      }
      data.forEach(expense => {
        let attachmentUrl = expense.attachment || "";
        attachmentUrl = attachmentUrl.replace('/accounting/expense', '');
        if (attachmentUrl.charAt(0) === '/') {
          attachmentUrl = window.location.origin + attachmentUrl;
        }
        const row = document.createElement("tr");
        row.innerHTML = `
          <td class="text-13 align-middle text-start">${expense.title}</td>
          <td class="text-13 align-middle text-start">${expense.category}</td>
          <td class="text-13 align-middle">${expense.amount}</td>
          <td class="text-13 align-middle">${expense.invoice_number || 'N/A'}</td>
          <td class="text-13 align-middle">${expense.expense_date}</td>
          <td class="text-13 align-middle text-start">${expense.note || 'N/A'}</td>
          <td class="text-13 text-center">
            ${attachmentUrl != "" ?
                `<button class="btn btn-sm btn-outline-secondary ms-2" onclick="openDocument('${attachmentUrl}')"><i class="fa-solid fa-eye"></i></button>`
                :`
                N/A
                `
            }
          </td>`;
        tbody.appendChild(row);
      });
      filterExpensesTable();
    }
    
    function renderExpensePagination(data) {
      const paginationContainer = document.getElementById("expensePaginationContainer");
      paginationContainer.innerHTML = "";
      const totalPages = Math.ceil(data.length / rowsPerPageExpenses);
      function createButton(innerHTML, page, disabled = false) {
        const btn = document.createElement("button");
        btn.innerHTML = innerHTML;
        btn.classList.add("btn", "btn-outline-primary", "mx-1", "text-13");
        btn.disabled = disabled;
        if (!disabled) {
          btn.addEventListener("click", function() {
            currentPageExpenses = page;
            updateExpensesPagination();
          });
        }
        return btn;
      }
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angles-left"></i>', 1, currentPageExpenses === 1));
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angle-left"></i>', currentPageExpenses - 1, currentPageExpenses === 1));
      const pageLabel = createButton(`${currentPageExpenses} / ${totalPages}`, currentPageExpenses, true);
      paginationContainer.appendChild(pageLabel);
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angle-right"></i>', currentPageExpenses + 1, currentPageExpenses === totalPages));
      paginationContainer.appendChild(createButton('<i class="fa-solid fa-angles-right"></i>', totalPages, currentPageExpenses === totalPages));
    }

    // Update dropdowns for Expense add form and filter.
    function updateExpenseCategoryDropdowns(categories) {
      let expenseCategoryInput = document.getElementById("expenseCategoryInput");
      expenseCategoryInput.innerHTML = '<option value="" disabled selected>Select Category</option>';
      let expenseCategoryFilter = document.getElementById("expenseCategoryFilter");
      expenseCategoryFilter.innerHTML = '<option value="All" selected>All Categories</option>';
      categories.forEach(cat => {
        let optForm = document.createElement("option");
        optForm.value = cat.category_name;
        optForm.textContent = cat.category_name;
        expenseCategoryInput.appendChild(optForm);
    
        let optFilter = document.createElement("option");
        optFilter.value = cat.category_name;
        optFilter.textContent = cat.category_name;
        expenseCategoryFilter.appendChild(optFilter);
      });
    }

    function filterExpenseTable() {
      const searchValue = document.getElementById("expenseSearchInput").value.toLowerCase();
      const filterValue = document.getElementById("expenseFilterSelect").value;
      const rows = document.querySelectorAll(".expense-category-row");
      rows.forEach(row => {
        const categoryName = row.children[0].textContent.toLowerCase();
        const status = row.getAttribute("data-status");
        let matchesSearch = categoryName.indexOf(searchValue) !== -1;
        let matchesFilter = filterValue === "all" ? true : (status === filterValue);
        row.style.display = (matchesSearch && matchesFilter) ? "" : "none";
      });
    }

    // --- Expenses Tab Event Listeners ---
    document.getElementById("addExpenseBtnExp").addEventListener("click", () => {
      clearExpenseFormExp();
      toggleExpenseViewExpenses(true);
    });
    document.getElementById("cancelExpenseBtnExp").addEventListener("click", () => {
      toggleExpenseViewExpenses(false);
    });
    document.getElementById("saveExpenseBtnExp").addEventListener("click", () => {
      const saveBtn = document.getElementById("saveExpenseBtnExp");
      saveBtn.disabled = true;
      const originalText = saveBtn.innerHTML;
      saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
      
      const institution_id = document.getElementById('institutionSelect').value;
      const title = document.getElementById("expenseTitle").value.trim();
      const category = document.getElementById("expenseCategoryInput").value;
      const amount = document.getElementById("expenseAmount").value.trim();
      const invoiceNumber = document.getElementById("invoiceNumber").value.trim();
      const expenseDate = document.getElementById("expenseDate").value;
      const note = document.getElementById("expenseNote").value.trim();
      const attachmentInput = document.getElementById("expenseAttachment");
      
      if (!title || !category || !amount || !expenseDate) {
        Swal.fire({ title: 'Error', text: 'Please fill in all required fields.', icon: 'error', confirmButtonText: 'OK' });
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
        return;
      }
      
      const formData = new FormData();
      formData.append('institution_id', institution_id);
      formData.append('title', title);
      formData.append('category', category);
      formData.append('amount', amount);
      formData.append('invoice_number', invoiceNumber);
      formData.append('expense_date', expenseDate);
      formData.append('note', note);
      formData.append('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
      if (attachmentInput.files[0]) {
        formData.append('attachment', attachmentInput.files[0]);
      }
      
      fetch("/api/expense", {
        method: "POST",
        headers: { "Authorization": token },
        body: formData
      })
      .then(response => {
        if ([401,403].includes(response.status)) {
          window.location.href = '/Unauthorised';
          throw new Error("Unauthorized Access");
        }
        return response.json();
      })
      .then(data => {
        if (data.status === "success") {
          Swal.fire({ title: 'Success', text: data.message, icon: 'success', confirmButtonText: 'OK' })
          .then(() => {
            toggleExpenseViewExpenses(false);
            fetchExpenses(institution_id);
          });
        } else {
          Swal.fire({ title: 'Error', text: data.message, icon: 'error', confirmButtonText: 'OK' });
        }
      })
      .catch(error => {
        console.error("Error adding expense:", error);
        Swal.fire({ title: 'Error', text: 'An error occurred while adding the expense.', icon: 'error', confirmButtonText: 'OK' });
      })
      .finally(() => {
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
      });
    });
    
    function clearExpenseFormExp() {
      document.getElementById("expenseTitle").value = "";
      document.getElementById("expenseCategoryInput").selectedIndex = 0;
      document.getElementById("expenseAmount").value = "";
      document.getElementById("invoiceNumber").value = "";
      document.getElementById("expenseDate").value = "";
      document.getElementById("expenseAttachment").value = "";
      document.getElementById("expenseNote").value = "";
    }
    
    function toggleExpenseViewExpenses(showForm) {
      if (showForm) {
        document.getElementById("expenseFormContainerExp").classList.remove("d-none");
        document.getElementById("expenseTableContainerExp").classList.add("d-none");
      } else {
        document.getElementById("expenseFormContainerExp").classList.add("d-none");
        document.getElementById("expenseTableContainerExp").classList.remove("d-none");
      }
    }
    
    function filterExpensesTable() {
      const fromDateValue = document.getElementById("fromDate").value;
      const toDateValue = document.getElementById("toDate").value;
      const categoryFilterValue = document.getElementById("expenseCategoryFilter").value;
      const rows = document.querySelectorAll("#expenseTableExpenses tbody tr");
      rows.forEach(row => {
        let expenseDate = row.cells[4].textContent.trim();
        let expenseCategory = row.cells[1].textContent.trim();
        let dateCondition = true;
        let categoryCondition = true;
        if (fromDateValue) {
          dateCondition = new Date(expenseDate) >= new Date(fromDateValue);
        }
        if (toDateValue) {
          dateCondition = dateCondition && (new Date(expenseDate) <= new Date(toDateValue));
        }
        if (categoryFilterValue !== "All") {
          categoryCondition = (expenseCategory === categoryFilterValue);
        }
        row.style.display = (dateCondition && categoryCondition) ? "" : "none";
      });
    }
    
    // Function to open document attachments.
    function openDocument(path) {
      window.open(window.location.origin + "/" + decodeURIComponent(path), "_blank");
    }
  </script>
</body>
</html>
