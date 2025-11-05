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
      /* Scrollable container if needed */
      .accordion-scroll {
        max-height: 400px;
        overflow-y: auto;
      }
      /* Force the charts to fill their container */
      .status-chart {
        width: 100% !important;
        height: 100% !important;
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

        <div class="container my-4">
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

          <!-- Fees Dashboard Summary Cards -->
          <div class="row my-4" id="feesDashboardRow">
            <!-- Total Fees Records -->
            <div class="col-12 col-md-3 mb-3 mb-md-0">
              <div class="card h-100" style="background-color: #fff; border-left: 5px solid #007bff;">
                <div class="card-body text-center">
                  <h5 class="card-title text-primary">
                    <i class="fa-solid fa-folder-open"></i> Total Fees Records
                  </h5>
                  <p class="card-text" id="totalRecords">
                    <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
                  </p>
                </div>
              </div>
            </div>
            <!-- One Time Fees Paid -->
            <div class="col-12 col-md-3 mb-3 mb-md-0">
              <div class="card h-100" style="background-color: #fff; border-left: 5px solid #28a745;">
                <div class="card-body text-center">
                  <h5 class="card-title text-success">
                    <i class="fa-solid fa-money-bill-wave"></i> One Time Fees Paid
                  </h5>
                  <p class="card-text" id="oneTimeFeesPaid">
                    <span class="spinner-border spinner-border-sm text-success" role="status" aria-hidden="true"></span>
                  </p>
                </div>
              </div>
            </div>
            <!-- Semester Fees Paid -->
            <div class="col-12 col-md-3 mb-3 mb-md-0">
              <div class="card h-100" style="background-color: #fff; border-left: 5px solid #ffc107;">
                <div class="card-body text-center">
                  <h5 class="card-title text-warning">
                    <i class="fa-solid fa-receipt"></i> Semester Fees Paid
                  </h5>
                  <p class="card-text" id="semesterFeesPaid">
                    <span class="spinner-border spinner-border-sm text-warning" role="status" aria-hidden="true"></span>
                  </p>
                </div>
              </div>
            </div>
            <!-- Grand Total Income -->
            <div class="col-12 col-md-3">
              <div class="card h-100" style="background-color: #fff; border-left: 5px solid #dc3545;">
                <div class="card-body text-center">
                  <h5 class="card-title text-danger">
                    <i class="fa-solid fa-chart-line"></i> Grand Total Income
                  </h5>
                  <p class="card-text" id="grandTotalIncome">
                    <span class="spinner-border spinner-border-sm text-danger" role="status" aria-hidden="true"></span>
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Optional Dashboard Chart (only if no institute id) -->
          <div class="row my-4 d-none" id="dashboardChartContainer">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h5 class="text-14 mb-0">Dashboard Summary by Institute</h5>
                </div>
                <div class="card-body" style="height: 400px;">
                  <canvas id="dashboardBarChart" class="status-chart"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div> <!-- End container -->

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // On DOM load: Check token, load institution info (if available), load dashboard & student count.
      document.addEventListener("DOMContentLoaded", function () {
        if (!sessionStorage.getItem("token")) {
          window.location.href = "/";
        }
        initializeDashboard();
      });

      // Set institution banner (if present)
      document.addEventListener("DOMContentLoaded", () => {
        const instName = sessionStorage.getItem("institution_name");
        const instType = sessionStorage.getItem("institution_type");
        const institutionInfoDiv = document.getElementById("institutionInfoDiv");
        if (instName && instType) {
          const instLogoPath = sessionStorage.getItem("institution_logo");
      const logoImg = document.getElementById("instImg");
      logoImg.src = instLogoPath || '/assets/web_assets/logo.png';
          document.getElementById("instituteName").innerHTML =
            `<span class="text-secondary">${instName}</span>`;
          document.getElementById("instituteType").innerHTML =
            `<i class="fa-solid fa-graduation-cap me-2"></i>${instType}`;
          institutionInfoDiv.classList.remove("d-none");
        }
      });

      const token = sessionStorage.getItem("token");

      function initializeDashboard() {
        fetchDashboardData();
      }

      // Fetch dashboard data from the new API endpoint.
      function fetchDashboardData() {
        let url = "";
        const institutionId = sessionStorage.getItem("institution_id");
        if (institutionId) {
          url = "/api/fees-dashboard?institute_id=" + institutionId;
        } else {
          url = "/api/fees-dashboard";
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
              const institutionId = sessionStorage.getItem("institution_id");
              if (institutionId) {
                // When an institute is specified, data.data is an object
                updateDashboardCards(data.data);
                document.getElementById("dashboardChartContainer").classList.add("d-none");
              } else {
                // When no institute id, data.data is expected to be an array of institute dashboards.
                let aggRecords = 0, aggOneTime = 0, aggSemester = 0, aggGrandTotal = 0;
                data.data.forEach(item => {
                  aggRecords += Number(item.record_count);
                  aggOneTime += Number(item.total_one_time_paid);
                  aggSemester += Number(item.total_semester_paid);
                  aggGrandTotal += Number(item.grand_total_income);
                });
                updateDashboardCards({
                  record_count: aggRecords,
                  total_one_time_paid: aggOneTime,
                  total_semester_paid: aggSemester,
                  grand_total_income: aggGrandTotal
                });
                renderDashboardBarChart(data.data);
                document.getElementById("dashboardChartContainer").classList.remove("d-none");
              }
            } else {
              console.error("Error fetching dashboard data:", data.message);
            }
          })
          .catch(error => console.error("Error:", error));
      }

      // Update the summary cards with dashboard data.
      function updateDashboardCards(dashboardData) {
        document.getElementById("totalRecords").textContent = dashboardData.record_count;
        document.getElementById("oneTimeFeesPaid").textContent = dashboardData.total_one_time_paid;
        document.getElementById("semesterFeesPaid").textContent = dashboardData.total_semester_paid;
        document.getElementById("grandTotalIncome").textContent = dashboardData.grand_total_income;
      }

      // Render a bar chart for the aggregated dashboard data (only when no institute id provided).
      function renderDashboardBarChart(dataArray) {
        // Data arrays for institute names and their corresponding grand total incomes.
        const labels = [];
        const incomeData = [];
        dataArray.forEach(item => {
          labels.push(item.institution_name || item.institute_id);
          incomeData.push(item.grand_total_income);
        });
        const ctx = document.getElementById("dashboardBarChart").getContext("2d");
        new Chart(ctx, {
          type: "bar",
          data: {
            labels: labels,
            datasets: [{
              label: "Grand Total Income",
              data: incomeData,
              backgroundColor: "rgba(75, 192, 192, 0.6)",
              borderColor: "rgba(75, 192, 192, 1)",
              borderWidth: 1,
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
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
