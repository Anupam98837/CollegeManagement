<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Register Dashboard</title>
    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <!-- Font Awesome CSS -->
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
      rel="stylesheet"
    />
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
      .chart-container {
        margin-top: 2rem;
      }
    </style>
  </head>
  <body>
    <div class="d-flex">
      <!-- Sidebar -->
      <div>
        @include('users.register.components.sidebar')
      </div>
      <!-- Main Content -->
      <div class="w-100 main-com">
        @include('users.register.components.header')
        
        <!-- Institution Details Card -->
        <div class="container mt-4">
          <div class="card text-center border-0">
            <div class="card-body">
              <h5 id="instituteName" class="card-title fs-3">
                <i class="fa-solid fa-school me-2 text-primary"></i>
                <span class="text-secondary">Loading Institution...</span>
              </h5>
              <p id="instituteType" class="card-text fs-4">
                <i class="fa-solid fa-graduation-cap me-2"></i>
                Loading Type...
              </p>
            </div>
          </div>
        </div>
        
        <!-- Count Cards -->
        <div class="container my-4">
          <div class="row row-cols-1 row-cols-md-3 g-3">
            <!-- Registered Students Card -->
            <div class="col">
              <div class="card h-100 text-white bg-warning">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                  <h5 class="card-title">
                    <i class="fa-solid fa-user-graduate"></i> Students
                  </h5>
                  <p id="studentCount" class="card-text">
                    <span class="spinner-border spinner-border-sm text-light" role="status"></span>
                  </p>
                </div>
              </div>
            </div>
            <!-- Intakes Card -->
            <div class="col">
              <div class="card h-100 text-white bg-primary">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                  <h5 class="card-title">
                    <i class="fa-solid fa-calendar-alt"></i> Intakes
                  </h5>
                  <p id="intakeCount" class="card-text">
                    <span class="spinner-border spinner-border-sm text-light" role="status"></span>
                  </p>
                </div>
              </div>
            </div>
            <!-- Merged Courses Card -->
            <div class="col">
              <div class="card h-100 text-white bg-info">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                  <h5 class="card-title">
                    <i class="fa-solid fa-book"></i> Merged Courses
                  </h5>
                  <p id="courseCount" class="card-text">
                    <span class="spinner-border spinner-border-sm text-light" role="status"></span>
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Charts Section -->
        <div class="container chart-container">
          <div class="row g-4">
            <!-- Grid Item 1: Student Registrations by Year -->
            <div class="col-md-6">
              <div class="card h-100">
                <div class="card-header">Student Registrations by Year</div>
                <div class="card-body">
                  <canvas id="studentsYearChart"></canvas>
                </div>
              </div>
            </div>
            <!-- Grid Item 2: Intakes by Year (record count) -->
            <div class="col-md-6">
              <div class="card h-100">
                <div class="card-header">Intakes by Year</div>
                <div class="card-body">
                  <canvas id="intakesYearChart"></canvas>
                </div>
              </div>
            </div>
            <!-- Grid Item 3: Stacked Intakes by Year (sum of values) -->
            <div class="col-md-6">
              <div class="card h-100">
                <div class="card-header">Stacked Intakes by Year (Values)</div>
                <div class="card-body">
                  <canvas id="stackedIntakesChart"></canvas>
                </div>
              </div>
            </div>
            <!-- Grid Item 4: Overall Intake Breakdown (Pie Chart) -->
            <div class="col-md-6">
              <div class="card h-100">
                <div class="card-header">Overall Intake Breakdown</div>
                <div class="card-body">
                  <canvas id="intakeBreakdownChart"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Loading Spinner -->
        <div id="loadingSpinner" class="d-flex justify-content-center align-items-center" style="height: 300px;">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Dashboard JS -->
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const token = sessionStorage.getItem("token");
        if (!token) {
          window.location.href = "/";
        }
    
        // Load institution details from sessionStorage
        const instituteName = sessionStorage.getItem("institution_name") || "Institution Unavailable";
        const instituteType = sessionStorage.getItem("institution_type") || "Type Unavailable";
        const instituteId = sessionStorage.getItem("institution_id") || null;
    
        document.getElementById("instituteName").innerHTML = `
          <i class="fa-solid fa-school me-2 text-primary"></i>
          <span class="text-secondary">${instituteName}</span>
        `;
        document.getElementById("instituteType").innerHTML = `
          <i class="fa-solid fa-graduation-cap me-2"></i>
          ${instituteType}
        `;
    
        if (instituteId) {
          fetchDashboardData(instituteId, token);
        } else {
          console.error("Institution ID not found in sessionStorage.");
        }
      });
    
      // Global arrays to hold fetched data
      let students = [];
      let intakes = [];
      let mergedCourses = [];
    
      // For intake breakdown by type using numeric values
      let intakeTotals = { GEN: 0, EWS: 0, TFW: 0 };
      // For stacked chart: Group by year with summed intake values
      let stackedData = {}; // e.g., { "2021": { GEN: 10, EWS: 5, TFW: 2 }, "2022": { GEN: 8, EWS: 3, TFW: 4 } }
    
      function fetchDashboardData(instituteId, token) {
        Promise.all([
          // Fetch registered students (GET)
          fetch(`/api/view-students-by-institute?institute_id=${instituteId}`, {
            headers: { Accept: "application/json", Authorization: token }
          }).then(res => res.json()),
          // Fetch intakes by institution (GET)
          fetch(`/api/view-intakes-by-institution?institute_id=${instituteId}`, {
            headers: { Accept: "application/json", Authorization: token }
          }).then(res => res.json()),
          // Fetch merged courses (GET)
          fetch(`/api/view-institution-courses?institution_id=${instituteId}`, {
            headers: { Accept: "application/json", Authorization: token }
          }).then(res => res.json())
        ])
        .then(results => {
          // Process students
          if (results[0].status === "success") {
            students = results[0].data;
            document.getElementById("studentCount").textContent = students.length;
          } else {
            document.getElementById("studentCount").textContent = "0";
          }
    
          // Process intakes
          if (results[1].status === "success") {
            intakes = results[1].data;
            document.getElementById("intakeCount").textContent = intakes.length;
    
            // Reset totals for intake values and grouped data
            intakeTotals = { GEN: 0, EWS: 0, TFW: 0 };
            stackedData = {};
    
            // Process each intake record. Each record should have numeric fields:
            // gen_intake, ews_intake, tfw_intake, and a 'year' field.
            intakes.forEach(intake => {
              const gen = Number(intake.gen_intake) || 0;
              const ews = Number(intake.ews_intake) || 0;
              const tfw = Number(intake.tfw_intake) || 0;
    
              // Sum overall totals for the pie chart
              intakeTotals.GEN += gen;
              intakeTotals.EWS += ews;
              intakeTotals.TFW += tfw;
    
              // Group for stacked chart by intake.year
              const year = intake.year;
              if (!stackedData[year]) {
                stackedData[year] = { GEN: 0, EWS: 0, TFW: 0 };
              }
              stackedData[year].GEN += gen;
              stackedData[year].EWS += ews;
              stackedData[year].TFW += tfw;
            });
          } else {
            document.getElementById("intakeCount").textContent = "0";
          }
    
          // Process merged courses
          if (results[2].status === "success") {
            mergedCourses = results[2].data;
            document.getElementById("courseCount").textContent = mergedCourses.length;
          } else {
            document.getElementById("courseCount").textContent = "0";
          }
    
          // Render charts
          renderStudentsYearChart();
          renderIntakesYearChart();
          renderIntakeBreakdownChart();
          renderStackedIntakesChart();
          // Hide loading spinner and show graphs container if needed
          document.getElementById("loadingSpinner").classList.add("d-none");
        })
        .catch(error => {
          console.error("Error fetching dashboard data:", error);
        });
      }
    
      // Chart: Student Registrations by Year (using created_at field)
      function renderStudentsYearChart() {
        const studentYears = {};
        students.forEach(student => {
          const year = new Date(student.created_at).getFullYear();
          studentYears[year] = (studentYears[year] || 0) + 1;
        });
        const labels = Object.keys(studentYears).sort();
        const data = labels.map(year => studentYears[year]);
    
        const ctx = document.getElementById("studentsYearChart").getContext("2d");
        new Chart(ctx, {
          type: "bar",
          data: {
            labels: labels,
            datasets: [{
              label: "Registrations",
              data: data,
              backgroundColor: "rgba(255, 159, 64, 0.6)",
              borderColor: "rgba(255, 159, 64, 1)",
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: false },
              title: { display: true, text: "Student Registrations by Year" }
            },
            scales: {
              y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
          }
        });
      }
    
      // Chart: Intakes by Year (simple count of records)
      function renderIntakesYearChart() {
        const intakeYears = {};
        intakes.forEach(intake => {
          const year = intake.year;
          intakeYears[year] = (intakeYears[year] || 0) + 1;
        });
        const labels = Object.keys(intakeYears).sort();
        const data = labels.map(year => intakeYears[year]);
    
        const ctx = document.getElementById("intakesYearChart").getContext("2d");
        new Chart(ctx, {
          type: "bar",
          data: {
            labels: labels,
            datasets: [{
              label: "Intakes",
              data: data,
              backgroundColor: "rgba(54, 162, 235, 0.6)",
              borderColor: "rgba(54, 162, 235, 1)",
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: false },
              title: { display: true, text: "Intakes by Year" }
            },
            scales: {
              y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
          }
        });
      }
    
      // Chart: Intake Breakdown Pie Chart (overall summed values)
      function renderIntakeBreakdownChart() {
        const labels = ["GEN", "EWS", "TFW"];
        const data = labels.map(label => intakeTotals[label]);
    
        const ctx = document.getElementById("intakeBreakdownChart").getContext("2d");
        new Chart(ctx, {
          type: "pie",
          data: {
            labels: labels,
            datasets: [{
              data: data,
              backgroundColor: [
                "rgba(75, 192, 192, 0.6)",
                "rgba(255, 205, 86, 0.6)",
                "rgba(255, 99, 132, 0.6)"
              ],
              borderColor: [
                "rgba(75, 192, 192, 1)",
                "rgba(255, 205, 86, 1)",
                "rgba(255, 99, 132, 1)"
              ],
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { position: "bottom" },
              title: { display: true, text: "Overall Intake Breakdown" }
            }
          }
        });
      }
    
      // Chart: Stacked Intakes by Year (summing intake values per type per year)
      function renderStackedIntakesChart() {
        const years = Object.keys(stackedData).sort();
        const genData = years.map(year => stackedData[year].GEN);
        const ewsData = years.map(year => stackedData[year].EWS);
        const tfwData = years.map(year => stackedData[year].TFW);
    
        const ctx = document.getElementById("stackedIntakesChart").getContext("2d");
        new Chart(ctx, {
          type: "bar",
          data: {
            labels: years,
            datasets: [
              {
                label: "GEN",
                data: genData,
                backgroundColor: "rgba(75, 192, 192, 0.6)"
              },
              {
                label: "EWS",
                data: ewsData,
                backgroundColor: "rgba(255, 205, 86, 0.6)"
              },
              {
                label: "TFW",
                data: tfwData,
                backgroundColor: "rgba(255, 99, 132, 0.6)"
              }
            ]
          },
          options: {
            responsive: true,
            plugins: {
              title: { display: true, text: "Stacked Intakes by Year" },
              tooltip: { mode: 'index', intersect: false },
              legend: { position: "bottom" }
            },
            scales: {
              x: { stacked: true },
              y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } }
            }
          }
        });
      }
    </script>
  </body>
</html>
