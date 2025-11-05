<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Fees Dashboard</title>

    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <!-- Font Awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <style>
      /* Custom text sizes */
      .text-13 {
        font-size: 13px;
      }
      .text-14 {
        font-size: 14px;
      }
      /* Accordion header and body font sizes */
      .accordion-button {
        font-size: 1rem;
      }
      .accordion-body {
        font-size: 0.9rem;
      }
      /* Scrollable container for the accordion list */
      .accordion-scroll {
        height: 400px;
        overflow-y: auto;
      }
      /* Force the charts to fill their container */
      .status-chart {
        width: 100% !important;
        height: 100% !important;
      }
      /* Search input wrapper for icon positioning */
      .search-wrapper {
        position: relative;
      }
      .search-wrapper .fa-search {
        position: absolute;
        top: 50%;
        left: 15px;
        transform: translateY(-50%);
        pointer-events: none;
      }
      .search-wrapper input {
        padding-left: 2.5rem;
      }
      /* Institution info banner */
      .institute-banner {
        background-color: #f7f7f7;
        padding: 0.75rem 1rem;
        border: 1px solid #ddd;
        border-radius: 0.25rem;
        margin-bottom: 1rem;
      }
    </style>
  </head>
  <body>
    <div class="d-flex">
      <!-- Sidebar -->
      <div>
        @include('users.accountant.components.sidebar')
      </div>

      <!-- Main Content -->
      <div class="w-100 main-com">
        @include('users.accountant.components.header')

        <div class="container my-4">
          <!-- Institution Info Card -->
        <div id="institutionInfoDiv" class="bg-white p-2 rounded d-none">
            <div class="card text-center border-0">
              <div class="card-body">
                <h5 class="card-title fs-3" id="instituteName">
                  <i class="fa-solid fa-school me-2 text-primary"></i>
                  <span class="text-secondary">Loading Institution...</span>
                </h5>
                <p class="card-text fs-4" id="instituteType">
                  <i class="fa-solid fa-graduation-cap me-2"></i>
                  Loading Type...
                </p>
              </div>
            </div>
          </div>

          <!-- Top Row: 4 Summary Cards -->
          <div class="row my-4">
            <!-- Total Fees Structures -->
            <div class="col-12 col-md-3 mb-3 mb-md-0">
              <div class="card h-100 text-white bg-primary">
                <div class="card-body d-flex flex-column justify-content-center text-center">
                  <h5 class="card-title text-14">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Total Fees Structures
                  </h5>
                  <p class="card-text text-14" id="totalFeesCount">
                    <span class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></span>
                  </p>
                </div>
              </div>
            </div>
            <!-- Active Fees -->
            <div class="col-12 col-md-3 mb-3 mb-md-0">
              <div class="card h-100 text-white bg-success">
                <div class="card-body d-flex flex-column justify-content-center text-center">
                  <h5 class="card-title text-14">
                    <i class="fa-solid fa-check"></i> Active Fees
                  </h5>
                  <p class="card-text text-14" id="activeFeesCount">
                    <span class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></span>
                  </p>
                </div>
              </div>
            </div>
            <!-- Inactive Fees -->
            <div class="col-12 col-md-3 mb-3 mb-md-0">
              <div class="card h-100 text-white bg-danger">
                <div class="card-body d-flex flex-column justify-content-center text-center">
                  <h5 class="card-title text-14">
                    <i class="fa-solid fa-times"></i> Inactive Fees
                  </h5>
                  <p class="card-text text-14" id="inactiveFeesCount">
                    <span class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></span>
                  </p>
                </div>
              </div>
            </div>
            <!-- Total Students -->
            <div class="col-12 col-md-3">
              <div class="card h-100 text-white bg-info">
                <div class="card-body d-flex flex-column justify-content-center text-center">
                  <h5 class="card-title text-14">
                    <i class="fa-solid fa-users"></i> Total Students
                  </h5>
                  <p class="card-text text-14" id="studentCount">
                    <span class="spinner-border spinner-border-sm text-light" role="status" aria-hidden="true"></span>
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- New Row: Payment Status Graphs -->
          <div class="row mb-4">
            <!-- Left Column: Pie Chart (Percentages by Student Count) -->
            <div class="col-12 col-md-6 mb-4 mb-md-0">
              <div class="card">
                <div class="card-header">
                  <h5 class="text-14 mb-0">Fees Payment Status (%) (By Student)</h5>
                </div>
                <div class="card-body" style="height: 400px;">
                  <canvas id="paymentStatusChart" class="status-chart"></canvas>
                </div>
              </div>
            </div>
            <!-- Right Column: Bar Chart (Counts) -->
            <div class="col-12 col-md-6">
              <div class="card">
                <div class="card-header">
                  <h5 class="text-14 mb-0">Fee Payment Summary (Counts)</h5>
                </div>
                <div class="card-body" style="height: 400px;">
                  <canvas id="paymentSummaryBarChart" class="status-chart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Bottom Row: Accordion & Fees Distribution Pie Chart -->
          <div class="row">
            <!-- Left Column: Accordion Grouped by Fee Type -->
            <div class="col-12 col-md-6 mb-4 mb-md-0">
              <div class="card h-100">
                <div class="card-header">
                  <h5 class="text-14 mb-0">Fees Structures Grouped by Type</h5>
                </div>
                <div class="card-body">
                  <!-- Search Box -->
                  <div class="mb-3 search-wrapper">
                    <input
                      type="text"
                      id="accordionSearchFees"
                      class="form-control placeholder-14 text-13"
                      placeholder="Search fees by head or type..."
                    />
                    <i class="fa-solid fa-search text-secondary"></i>
                  </div>
                  <!-- Scrollable Accordion Container -->
                  <div class="accordion accordion-scroll" id="feesAccordion">
                    <!-- Dynamically generated accordion items -->
                  </div>
                </div>
              </div>
            </div>
            <!-- Right Column: Fees Distribution Pie Chart -->
            <div class="col-12 col-md-6">
              <div class="card h-100">
                <div class="card-header">
                  <h5 class="text-14 mb-0">Fees Distribution by Type</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center" style="height: 400px;">
                  <canvas id="feesPieChart" class="status-chart"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div> <!-- End container -->
      </div> <!-- End main-com -->
    </div> <!-- End d-flex -->

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // On DOM load, check token and initialize dashboard.
      document.addEventListener("DOMContentLoaded", function () {
        if (!sessionStorage.getItem("token")) {
          window.location.href = "/";
        }
        initializeFeesDashboard();
        fetchStudentCount();
        fetchPaymentSummary();
      });
      document.addEventListener("DOMContentLoaded", () => {
      const instName = sessionStorage.getItem("institution_name");
      const instType = sessionStorage.getItem("institution_type");
      const institutionInfoDiv = document.getElementById("institutionInfoDiv");
      if (instName && instType) {
        document.getElementById("instituteName").innerHTML = `<i class="fa-solid fa-school me-2 text-primary"></i><span class="text-secondary">${instName}</span>`;
        document.getElementById("instituteType").innerHTML = `<i class="fa-solid fa-graduation-cap me-2"></i>${instType}`;
        institutionInfoDiv.classList.remove("d-none");
      }
    });

      const token = sessionStorage.getItem("token");
      let feesData = [];
      // Group fees by type: "one-time", "semester-wise", "other"
      const feesGroups = { "one-time": [], "semester-wise": [], "other": [] };

      function initializeFeesDashboard() {
        fetchFeesData();
      }

      // Fetch fees data; if institute_id exists, call /api/view-fees-structure; otherwise, /api/view-all-fees-structure.
      function fetchFeesData() {
        let url = "";
        const institutionId = sessionStorage.getItem("institution_id");
        if (institutionId) {
          url = "/api/view-fees-structure?institute_id=" + institutionId;
        } else {
          url = "/api/view-all-fees-structure";
        }
        fetch(url, {
          method: "GET",
          headers: {
            "Accept": "application/json",
            "Authorization": token,
          }
        })
          .then(response => {
            if (response.status === 401 || response.status === 403) {
              window.location.href = "/Unauthorised";
              throw new Error("Unauthorized Access");
            }
            return response.json();
          })
          .then(data => {
            if (data.status === "success") {
              feesData = data.data;
              updateFeesCounts();
              groupFeesByType();
              populateFeesAccordion();
              renderFeesPieChart();
            } else {
              console.error("Error fetching fees data:", data.message);
            }
          })
          .catch(error => console.error("Error:", error));
      }

      function updateFeesCounts() {
        const activeFees = feesData.filter(fee => fee.status === "Active");
        const inactiveFees = feesData.filter(fee => fee.status !== "Active");
        document.getElementById("totalFeesCount").textContent = feesData.length;
        document.getElementById("activeFeesCount").textContent = activeFees.length;
        document.getElementById("inactiveFeesCount").textContent = inactiveFees.length;
      }

      function groupFeesByType() {
        // Reset groups
        feesGroups["one-time"] = [];
        feesGroups["semester-wise"] = [];
        feesGroups["other"] = [];
        feesData.forEach(fee => {
          const type = fee.type; // Expected: "one-time", "semester-wise", or "other"
          if (feesGroups.hasOwnProperty(type)) {
            feesGroups[type].push(fee);
          } else {
            feesGroups["other"].push(fee);
          }
        });
      }

      function populateFeesAccordion() {
        const accordion = document.getElementById("feesAccordion");
        accordion.innerHTML = "";
        for (const [type, group] of Object.entries(feesGroups)) {
          if (group.length === 0) continue;
          const item = document.createElement("div");
          item.classList.add("accordion-item");
          item.innerHTML = `
            <h2 class="accordion-header" id="heading-${type}">
              <button class="accordion-button ${type === "one-time" ? "" : "collapsed"} text-14" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-${type}" aria-expanded="${type === "one-time" ? "true" : "false"}" aria-controls="collapse-${type}">
                ${type.toUpperCase()} Fees (<span>${group.length}</span>)
              </button>
            </h2>
            <div id="collapse-${type}" class="accordion-collapse collapse ${type === "one-time" ? "show" : ""}" aria-labelledby="heading-${type}" data-bs-parent="#feesAccordion">
              <div class="accordion-body text-13" id="list-${type}">
                <!-- Fee items for ${type} will be inserted here -->
              </div>
            </div>
          `;
          accordion.appendChild(item);
          const listContainer = document.getElementById(`list-${type}`);
          group.forEach(fee => {
            listContainer.innerHTML += `
              <div class="mb-2 p-2 border rounded fee-item" data-fee-head="${fee.head_of_account.toLowerCase()}">
                <strong>${fee.head_of_account} (${fee.institution_name || "N/A"})</strong>
                <span style="color: ${fee.status === 'Active' ? 'green' : 'red'};"> (${fee.status})</span><br>
                <span>Type: ${fee.type}</span><br>
                <span>Created: ${new Date(fee.created_at).toLocaleString()}</span>
              </div>
            `;
          });
        }
      }

      function renderFeesPieChart() {
        const labels = [];
        const dataArr = [];
        const bgColors = [];
        for (const [type, group] of Object.entries(feesGroups)) {
          if (group.length === 0) continue;
          labels.push(type.toUpperCase());
          dataArr.push(group.length);
          if (type === "one-time") bgColors.push("rgba(54, 162, 235, 0.6)");
          else if (type === "semester-wise") bgColors.push("rgba(255, 206, 86, 0.6)");
          else if (type === "other") bgColors.push("rgba(153, 102, 255, 0.6)");
          else bgColors.push("rgba(201, 203, 207, 0.6)");
        }
        const ctx = document.getElementById("feesPieChart").getContext("2d");
        new Chart(ctx, {
          type: "pie",
          data: {
            labels: labels,
            datasets: [
              {
                data: dataArr,
                backgroundColor: bgColors,
                borderColor: bgColors.map(color => color.replace("0.6", "1")),
                borderWidth: 1,
              },
            ],
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: true },
            },
          },
        });
      }

      // Attach search event for fees accordion.
      document.getElementById("accordionSearchFees").addEventListener("input", function () {
        const query = this.value.toLowerCase();
        const feeItems = document.querySelectorAll(".fee-item");
        feeItems.forEach(item => {
          const feeHead = item.getAttribute("data-fee-head");
          if (feeHead.includes(query)) {
            item.style.display = "";
          } else {
            item.style.display = "none";
          }
        });
      });

      // Fetch student count.
      function fetchStudentCount() {
        let url = "";
        const institutionId = sessionStorage.getItem("institution_id");
        if (institutionId) {
          url = "/api/view-students-by-institute?institute_id=" + institutionId;
        } else {
          url = "/api/view-students";
        }
        fetch(url, {
          method: "GET",
          headers: {
            "Accept": "application/json",
            "Authorization": token,
          }
        })
          .then(response => {
            if (response.status === 401 || response.status === 403) {
              window.location.href = "/Unauthorised";
              throw new Error("Unauthorized Access");
            }
            return response.json();
          })
          .then(data => {
            if (data.status === "success") {
              const count = data.data.length;
              document.getElementById("studentCount").textContent = count;
            } else {
              console.error("Error fetching student count:", data.message);
              document.getElementById("studentCount").textContent = 0;
            }
          })
          .catch(error => console.error("Error:", error));
      }

      // Fetch payment summary data and render charts.
      function fetchPaymentSummary() {
        let url = "";
        const institutionId = sessionStorage.getItem("institution_id");
        if (institutionId) {
          url = "/api/fees-payment-summary?institute_id=" + institutionId;
        } else {
          url = "/api/fees-payment-summary";
        }
        fetch(url, {
          method: "GET",
          headers: {
            "Accept": "application/json",
            "Authorization": token,
          }
        })
          .then(response => {
            if (response.status === 401 || response.status === 403) {
              window.location.href = "/Unauthorised";
              throw new Error("Unauthorized Access");
            }
            return response.json();
          })
          .then(data => {
            if (data.status === "success") {
              // Render both the pie chart (percentages by student) and the bar chart (counts).
              renderPaymentSummaryChart(data.summary);
              renderPaymentSummaryBarChart(data.summary);
            } else {
              console.error("Error fetching payment summary:", data.message);
            }
          })
          .catch(error => console.error("Error:", error));
      }

      // Render pie chart showing percentages based on student count.
      function renderPaymentSummaryChart(summary) {
        const ctx = document.getElementById("paymentStatusChart").getContext("2d");
        new Chart(ctx, {
          type: "pie", // or "doughnut"
          data: {
            labels: [
              "One Time Fees Paid (%) (by Student)",
              "Semester Fees Paid (%) (by Student)",
              "Overall Fees Paid (%) (by Student)"
            ],
            datasets: [{
              data: [
                summary.one_time_full_paid_percentage_student,
                summary.semester_full_paid_percentage_student,
                summary.overall_full_paid_percentage_student
              ],
              backgroundColor: [
                "rgba(54, 162, 235, 0.6)",  // blue
                "rgba(255, 206, 86, 0.6)",   // yellow
                "rgba(75, 192, 192, 0.6)"    // green
              ],
              borderColor: [
                "rgba(54, 162, 235, 1)",
                "rgba(255, 206, 86, 1)",
                "rgba(75, 192, 192, 1)"
              ],
              borderWidth: 1,
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: { display: true },
              tooltip: {
                callbacks: {
                  label: function(context) {
                    return context.label + ": " + context.parsed + "%";
                  }
                }
              }
            }
          }
        });
      }

      // Render bar chart showing counts using student count as maximum.
      function renderPaymentSummaryBarChart(summary) {
        const ctx = document.getElementById("paymentSummaryBarChart").getContext("2d");
        new Chart(ctx, {
          type: "bar",
          data: {
            labels: ["Only One Time Fees Paid", "Overall Fees Paid"],
            datasets: [{
              label: "Number of Students",
              data: [
                summary.one_time_full_paid_count,
                summary.overall_full_paid_count
              ],
              backgroundColor: [
                "rgba(54, 162, 235, 0.6)",  // blue for one time fees
                "rgba(75, 192, 192, 0.6)"   // green for overall fees
              ],
              borderColor: [
                "rgba(54, 162, 235, 1)",
                "rgba(75, 192, 192, 1)"
              ],
              borderWidth: 1,
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              y: {
                beginAtZero: true,
                max: summary.student_count // Maximum equals the total student count.
              }
            },
            plugins: {
              legend: { display: true },
              tooltip: {
                callbacks: {
                  label: function(context) {
                    return context.label + ": " + context.parsed.y;
                  }
                }
              }
            }
          }
        });
      }
    </script>
  </body>
</html>
