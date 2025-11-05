<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fees Report</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- DataTables CSS (optional, not used for manual pagination) -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" />
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Custom Admin CSS -->
  <link rel="stylesheet" href="{{ asset('css/admin/addfees.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    .rotate {
      animation: rotation 1s linear;
    }
    @keyframes rotation {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    .export-container {
      margin-top: 10px;
      margin-bottom: 10px;
    }
    .default-placeholder {
      padding: 50px 0;
    }
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
          <span class="text-primary">Fees Report</span>
        </p>
        <!-- Card container similar to Students page -->
        <div class="bg-white p-4 rounded">
          <!-- Date Range Filter -->
          <div class="row mb-3">
            <div class="col-md-5">
              <label for="startDate" class="form-label text-13">Start Date</label>
              <input type="date" id="startDate" class="form-control text-13">
            </div>
            <div class="col-md-5">
              <label for="endDate" class="form-label text-13">End Date</label>
              <input type="date" id="endDate" class="form-control text-13">
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <button id="viewReportBtn" class="btn btn-primary text-13">
                <i class="fa-solid fa-chart-line me-2"></i>View Report
              </button>
            </div>
          </div>
          <!-- Report Summary -->
          <div id="reportSummary" class="mb-4"></div>
          <!-- Detailed Report Button Container -->
          <div id="detailedBtnContainer" class="mb-4"></div>
          <!-- Search Bar and Export Button Container (hidden by default) -->
          <div id="reportSearchContainer" class="row mt-2 d-flex d-none">
            <div class="col-md-6 position-relative">
              <input type="text" id="reportSearch" class="form-control placeholder-14 text-13 ps-5" placeholder="Search by Student or Institute">
              <i class="fa-solid fa-search position-absolute text-secondary" style="top: 50%; left: 15px; transform: translateY(-50%);"></i>
            </div>
            <div class="col-md-6 text-end">
              <button id="exportCsvBtn" class="btn btn-success btn-sm text-13">Export CSV</button>
            </div>
          </div>
          <!-- Report Table Container -->
          <div id="reportContainer" class="table-responsive default-placeholder">
            <div class="text-center">
              <img src="{{ asset('assets/web_assets/search.png') }}"  alt="Default" class="img-fluid" style="max-width: 300px;">
              <p class=" mt-3">Select a date range to view report</p>
            </div>
          </div>
          <!-- Pagination Container -->
          <div id="paginationContainer" class="d-flex justify-content-center gap-2"></div>
        </div><!-- end card -->
      </div><!-- end container -->

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Bootstrap Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Global variables for Fees Report
    let feesReportList = [];
    let summaryReport = {};
    let currentPage = 1;
    const rowsPerPage = 10; // Adjust as needed
    // Global variable for currently selected columns
    let currentSelectedColumns = [];

    // Column definitions for detailed report selection (order updated)
    const allColumns = [
      { key: "institution_name", label: "Institute Name" },
      { key: "student_name", label: "Student Name" },
    //   { key: "category", label: "Category" },
      { key: "semester", label: "Semester" },
      { key: "required", label: "Total Fees" },
      { key: "scholarship", label: "Scholarship" },
      { key: "finalPending", label: "Final Fees" },
      { key: "paid", label: "Paid (₹)" },
      { key: "pending", label: "Pending (₹)" },
      { key: "program_name", label: "Program Name" },
      { key: "intake", label: "Intake" },
      { key: "fee_type", label: "Fee Type" },
      { key: "payment_created_at", label: "Payment Date" }
    ];

    // Display institution info from sessionStorage
    document.addEventListener("DOMContentLoaded", function() {
      if (!sessionStorage.getItem("token")) {
        window.location.href = "/";
      }
      const instName = sessionStorage.getItem("institution_name");
      const instType = sessionStorage.getItem("institution_type");
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
        document.getElementById("institutionInfoDiv").classList.remove("d-none");
      }
      document.getElementById('viewReportBtn').addEventListener('click', fetchFeesReport);
      document.getElementById('exportCsvBtn').addEventListener('click', exportTableToCSV);
    });

    // Fetch Fees Report data from API
    function fetchFeesReport() {
      const startDate = document.getElementById('startDate').value;
      const endDate = document.getElementById('endDate').value;
      if (!startDate || !endDate) {
        Swal.fire('Error', 'Please select both start and end dates.', 'error');
        return;
      }
      let url = `/api/fees-report?start_date=${startDate}&end_date=${endDate}`;
      const instituteId = sessionStorage.getItem("institution_id");
      if (instituteId) {
        url += `&institute_id=${instituteId}`;
      }
      fetch(url, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Authorization': sessionStorage.getItem('token') || ''
        }
      })
      .then(response => {
        if (!response.ok) throw new Error(`Status: ${response.status}`);
        return response.json();
      })
      .then(data => {
        if (data.status === 'success') {
          feesReportList = data.data;
          summaryReport = data.summary;
          renderReportSummary(summaryReport);
          // Show the "Show Detailed Report" button
          document.getElementById('detailedBtnContainer').innerHTML = `<button id="detailedReportBtn" class="btn btn-secondary text-13">
            <i class="fa-solid fa-table me-2"></i>Show Detailed Report
          </button>`;
          document.getElementById('detailedReportBtn').addEventListener('click', openDetailedReportModal);
          // Clear previous table and pagination; hide search container
          document.getElementById('reportContainer').innerHTML = "";
          document.getElementById('paginationContainer').innerHTML = "";
    } else {
          Swal.fire('Error', data.message, 'error');
        }
      })
      .catch(error => {
        console.error("Error fetching report:", error);
        Swal.fire('Error', 'Failed to fetch report.', 'error');
      });
    }

    // Render report summary section (including institute name from sessionStorage)
    function renderReportSummary(summary) {
        console.log(summary)
      let html = `<div class="alert alert-info text-13">
        <h5>Report Summary</h5>`;
      const instName = sessionStorage.getItem("institution_name");
      if (summary.institution_name) {
        html += `<p><strong>Institute Name:</strong> ${summary.institution_name}</p>`;
      }
      if (summary.institute_id) {
        html += `<p><strong>Institute ID:</strong> ${summary.institute_id}</p>`;
      }
      if (summary.date_range) {
        html += `<p><strong>Date Range:</strong> ${summary.date_range.start_date} to ${summary.date_range.end_date}</p>`;
      }
      if (summary.collected_fees_per_institute) {
        html += `<h6 class="text-14 text-secondary">Collected Fees per Institute:</h6><ul>`;
        for (const [instId, total] of Object.entries(summary.collected_fees_per_institute)) {
          html += `<li class="text-13"><strong>${instId}:</strong> ${parseFloat(total).toFixed(2)}</li>`;
        }
        html += `</ul>`;
      }
      if (summary.grand_total_collected !== undefined) {
        html += `<p class="text-13"><strong>Grand Total Collected Fees:</strong> ${summary.grand_total_collected}</p>`;
      }
      html += `</div>`;
      document.getElementById('reportSummary').innerHTML = html;
    }

    // Open SweetAlert modal for detailed report column selection
    function openDetailedReportModal() {
      let checkboxHtml = '<table class="table table-striped text-13"><tbody>';
      allColumns.forEach(col => {
        checkboxHtml += `<tr>
          <td class="text-start">${col.label}</td>
          <td class="text-end">
            <input type="checkbox" class="form-check-input" value="${col.key}" id="chk_${col.key}" checked>
          </td>
        </tr>`;
      });
      checkboxHtml += '</tbody></table>';
      
      Swal.fire({
        title: 'Select Columns to Display',
        html: checkboxHtml,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: 'Show Report',
        preConfirm: () => {
          const selected = [];
          allColumns.forEach(col => {
            if (document.getElementById('chk_' + col.key).checked) {
              selected.push(col.key);
            }
          });
          if (selected.length === 0) {
            Swal.showValidationMessage('Please select at least one column.');
          }
          return selected;
        }
      }).then((result) => {
        if (result.isConfirmed) {
          currentSelectedColumns = result.value;
          // Hide the "Show Detailed Report" button
          document.getElementById('detailedBtnContainer').innerHTML = "";
          renderDetailedReportTable(currentSelectedColumns);
          // Show search bar and export button container
          document.getElementById('reportSearchContainer').classList.remove('d-none');
        }
      });
    }

    // Get current page slice
    function getPaginatedData(data) {
      const start = (currentPage - 1) * rowsPerPage;
      return data.slice(start, start + rowsPerPage);
    }

    // Filter fees report data by search query (search in student_name and institution_name)
    function getFilteredFees(query = '') {
      query = query.trim().toLowerCase();
      if (!query) return feesReportList;
      return feesReportList.filter(item => {
        const name = item.student_name ? item.student_name.toLowerCase() : "";
        const inst = item.institution_name ? item.institution_name.toLowerCase() : "";
        return name.includes(query) || inst.includes(query);
      });
    }

    // Render the detailed report table based on selected columns
    function renderDetailedReportTable(selectedColumns) {
      let filtered = getFilteredFees(document.getElementById('reportSearch').value);
      let paginated = getPaginatedData(filtered);
      let html = `<table class="table table-striped">
        <thead>
          <tr>`;
      allColumns.forEach(col => {
        if (selectedColumns.includes(col.key)) {
          html += `<th class="text-13 text-secondary" style="white-space: nowrap;"><span>${col.label}</span></th>`;
        }
      });
      html += `</tr></thead>
        <tbody>`;
      if (paginated.length === 0) {
        html += `<tr><td colspan="${selectedColumns.length}" class="text-center text-13">
                <div class="text-center">
                    <img src="{{ asset('assets/web_assets/noData.png') }}"  alt="Default" class="img-fluid" style="max-width: 300px;">
                <p class=" mt-3">No records found.</p>
            </div>
            </td></tr>`;
      } else {
        paginated.forEach(item => {
          let totalFees = parseFloat(item.required);
          let scholarshipAmount = 0;
          if (item.scholarship) {
            try {
              let val = item.scholarship;
              if (typeof val === 'string') {
                val = JSON.parse(val);
              }
              if (typeof val === 'object' && val !== null && val.one_time_discount !== undefined) {
                scholarshipAmount = parseFloat(val.one_time_discount);
              } else {
                scholarshipAmount = parseFloat(val);
              }
            } catch (e) {
              scholarshipAmount = 0;
            }
          }
          let finalFees = totalFees - scholarshipAmount;
          if (finalFees < 0) finalFees = 0;
          let paid = parseFloat(item.paid);
          let pending = finalFees - paid;
          if (pending < 0) pending = 0;
          html += `<tr>`;
          selectedColumns.forEach(colKey => {
            switch (colKey) {
              case "institution_name":
                html += `<td class="text-13" style="white-space: nowrap;">${item.institution_name || 'N/A'}</td>`;
                break;
              case "student_name":
                html += `<td class="text-13" style="white-space: nowrap;">${item.student_name || 'N/A'}</td>`;
                break;
              case "category":
                html += `<td class="text-13">${item.category || 'N/A'}</td>`;
                break;
              case "semester":
                html += `<td class="text-13">${item.semester ? item.semester : 'One Time'}</td>`;
                break;
              case "required":
                html += `<td class="text-13">${totalFees.toFixed(2)}</td>`;
                break;
              case "scholarship":
                html += `<td class="text-13">${scholarshipAmount ? scholarshipAmount.toFixed(2) : 'N/A'}</td>`;
                break;
              case "finalPending":
                html += `<td class="text-13">${finalFees.toFixed(2)}</td>`;
                break;
              case "paid":
                html += `<td class="text-13">${paid.toFixed(2)}</td>`;
                break;
              case "pending":
                html += `<td class="text-13">${pending.toFixed(2)}</td>`;
                break;
              case "program_name":
              html += `<td class="text-13">${(() => {
                    let name = (item.course_details && item.course_details.program_name) || 'N/A';
                    let match = name.match(/\(([^)]+)\)/);
                    return match ? match[1] : name;
                    })()}</td>`;
                break;
              case "intake":
                html += `<td class="text-13">${(item.course_details && item.course_details.intake_type && item.course_details.intake_year) ? item.course_details.intake_type + ' - ' + item.course_details.intake_year : 'N/A'}</td>`;
                break;
              case "fee_type":
                html += `<td class="text-13">${(item.course_details && item.course_details.fee_type) || 'N/A'}</td>`;
                break;
              case "payment_created_at":
                html += `<td class="text-13">${item.payment_created_at || 'N/A'}</td>`;
                break;
              default:
                break;
            }
          });
          html += `</tr>`;
        });
      }
      html += `</tbody></table>`;
      document.getElementById('reportContainer').innerHTML = html;
      renderPagination(filtered, selectedColumns);
    }

    // Render pagination controls with "First" and "Last" buttons and page count in between
    function renderPagination(data, selectedColumns) {
      const totalPages = Math.ceil(data.length / rowsPerPage);
      let html = '';
      html += `<button class="btn btn-outline-primary text-13 mx-1" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(1)"><i class="fa-solid fa-angles-left"></i></button>`;
      html += `<button class="btn btn-outline-primary text-13 mx-1" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})"><i class="fa-solid fa-angle-left"></i></button>`;
      html += `<span class="mx-2 text-13 align-self-center">${currentPage} / ${totalPages}</span>`;
      html += `<button class="btn btn-outline-primary text-13 mx-1" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})"><i class="fa-solid fa-angle-right"></i></button>`;
      html += `<button class="btn btn-outline-primary text-13 mx-1" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${totalPages})"><i class="fa-solid fa-angles-right"></i></button>`;
      document.getElementById('paginationContainer').innerHTML = html;
    }

    // Change page event handler
    function changePage(page) {
      currentPage = page;
      renderDetailedReportTable(currentSelectedColumns);
    }

    // Event listener for search bar in detailed report
    document.getElementById('reportSearch').addEventListener('input', function() {
      currentPage = 1;
      renderDetailedReportTable(currentSelectedColumns);
    });

    // Export CSV using entire filtered dataset (ignoring pagination)
    function exportTableToCSV() {
      const query = document.getElementById('reportSearch').value;
      const filtered = getFilteredFees(query);
      let csv = [];
      // Generate header row (remove " (₹)" if present)
      let headerRow = [];
      allColumns.forEach(col => {
        if (currentSelectedColumns.includes(col.key)) {
          let headerText = col.label.replace(" (₹)", "");
          headerRow.push('"' + headerText.replace(/"/g, '""') + '"');
        }
      });
      csv.push(headerRow.join(","));
      // Process each record in filtered dataset
      filtered.forEach(item => {
        let rowData = [];
        let totalFees = parseFloat(item.required);
        let scholarshipAmount = 0;
        if (item.scholarship) {
          try {
            let val = item.scholarship;
            if (typeof val === 'string') {
              val = JSON.parse(val);
            }
            if (typeof val === 'object' && val !== null && val.one_time_discount !== undefined) {
              scholarshipAmount = parseFloat(val.one_time_discount);
            } else {
              scholarshipAmount = parseFloat(val);
            }
          } catch (e) {
            scholarshipAmount = 0;
          }
        }
        let finalFees = totalFees - scholarshipAmount;
        if (finalFees < 0) finalFees = 0;
        let paid = parseFloat(item.paid);
        let pending = finalFees - paid;
        if (pending < 0) pending = 0;
        currentSelectedColumns.forEach(colKey => {
          let cell = "";
          switch (colKey) {
            case "institution_name":
              cell = item.institution_name || 'N/A';
              break;
            case "student_name":
              cell = item.student_name || 'N/A';
              break;
            case "category":
              cell = item.category || 'N/A';
              break;
            case "semester":
              cell = item.semester ? item.semester : 'One Time';
              break;
            case "required":
              cell = totalFees.toFixed(2);
              break;
            case "scholarship":
              cell = scholarshipAmount ? scholarshipAmount.toFixed(2) : 'N/A';
              break;
            case "finalPending":
              cell = finalFees.toFixed(2);
              break;
            case "paid":
              cell = paid.toFixed(2);
              break;
            case "pending":
              cell = pending.toFixed(2);
              break;
            case "program_name":
              cell = (item.course_details && item.course_details.program_name) || 'N/A';
              break;
            case "intake":
              cell = (item.course_details && item.course_details.intake_type && item.course_details.intake_year) ? item.course_details.intake_type + ' - ' + item.course_details.intake_year : 'N/A';
              break;
            case "fee_type":
              cell = (item.course_details && item.course_details.fee_type) || 'N/A';
              break;
            case "payment_created_at":
              cell = item.payment_created_at || 'N/A';
              break;
            default:
              cell = "";
          }
          let cellStr = (cell !== undefined && cell !== null) ? cell.toString() : "";
          rowData.push('"' + cellStr.replace(/"/g, '""') + '"');
        });
        csv.push(rowData.join(","));
      });
      let csvString = csv.join("\n");
      let blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
      let url = URL.createObjectURL(blob);
      let a = document.createElement("a");
      a.href = url;
      a.download = "fees_report.csv";
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
    }
  </script>
</body>
</html>
