<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>

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
    <style>
      /* Adjust canvas size inside the status distribution card */
      .status-chart {
        max-height: 150px;
      }
    </style>
  </head>
  <body>
    <div class="d-flex">
      <!-- Sidebar -->
      <div>
        @include('users.admin.components.sidebar')
      </div>

      <!-- Main Content -->
      <div class="w-100 main-com">
        @include('users.admin.components.header')
        <!-- Top Count Cards -->
        <div class="container my-4">
          <di<!-- Top Count Cards -->
            <div class="container my-4">
              <div class="row row-cols-1 row-cols-md-5 g-3">
                <!-- Campuses Card -->
                <div class="col">
                  <div class="card h-100" style="background-color: #fff; border-left: 5px solid #007bff;">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                      <h5 class="card-title text-primary">
                        <i class="fa-solid fa-building"></i> Campuses
                      </h5>
                      <p class="card-text" id="campusCount">
                        <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
                      </p>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-center">
                      <a href="/academy/campus/manage" class="text-primary">
                        <i class="fa-solid fa-share-from-square"></i>
                      </a>
                    </div>
                  </div>
                </div>
                <!-- Institutions Card -->
                <div class="col">
                  <div class="card h-100" style="background-color: #fff; border-left: 5px solid #28a745;">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                      <h5 class="card-title text-success">
                        <i class="fa-solid fa-school"></i> Institutions
                      </h5>
                      <p class="card-text" id="institutionCount">
                        <span class="spinner-border spinner-border-sm text-success" role="status" aria-hidden="true"></span>
                      </p>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-center">
                      <a href="/institution/institution/manage" class="text-success">
                        <i class="fa-solid fa-share-from-square"></i>
                      </a>
                    </div>
                  </div>
                </div>
                <!-- Courses Card -->
                <div class="col">
                  <div class="card h-100" style="background-color: #fff; border-left: 5px solid #17a2b8;">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                      <h5 class="card-title text-info">
                        <i class="fa-solid fa-book"></i> Courses
                      </h5>
                      <p class="card-text" id="courseCount">
                        <span class="spinner-border spinner-border-sm text-info" role="status" aria-hidden="true"></span>
                      </p>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-center">
                      <a href="/course/course/manage" class="text-info">
                        <i class="fa-solid fa-share-from-square"></i>
                      </a>
                    </div>
                  </div>
                </div>
                <!-- Students Card -->
                <div class="col">
                  <div class="card h-100" style="background-color: #fff; border-left: 5px solid #ffc107;">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                      <h5 class="card-title text-warning">
                        <i class="fa-solid fa-user-graduate"></i> Students
                      </h5>
                      <p class="card-text" id="studentCount">
                        <span class="spinner-border spinner-border-sm text-warning" role="status" aria-hidden="true"></span>
                      </p>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-center">
                      <a href="/student/student/details" class="text-warning">
                        <i class="fa-solid fa-share-from-square"></i>
                      </a>
                    </div>
                  </div>
                </div>
                <!-- Agents Card -->
                <div class="col">
                  <div class="card h-100" style="background-color: #fff; border-left: 5px solid #dc3545;">
                    <div class="card-body d-flex flex-column justify-content-center text-center">
                      <h5 class="card-title text-danger">
                        <i class="fa-solid fa-user-tie"></i> Agents
                      </h5>
                      <p class="card-text" id="agentCount">
                        <span class="spinner-border spinner-border-sm text-danger" role="status" aria-hidden="true"></span>
                      </p>
                    </div>
                    <div class="card-footer bg-transparent border-0 text-center">
                      <a href="/agent/agent" class="text-danger">
                        <i class="fa-solid fa-share-from-square"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
        </div>

        <!-- Loading Spinner -->
        <div
          id="loadingSpinner"
          class="d-flex justify-content-center align-items-center"
          style="height: 300px;"
        >
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>

        <!-- Graphs Section -->
        <div id="graphsContainer" class="container d-none">
          <div class="row g-4">
            <!-- Overall Entity Count Bar Chart -->
            <div class="col-md-6">
              <div class="card h-100">
                <div class="card-header">Overall Entity Count</div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center">
                  <canvas id="entityBarChart"></canvas>
                </div>
              </div>
            </div>
            <!-- Status Distribution Pie Charts -->
            <div class="col-md-6">
              <div class="card h-100">
                <div class="card-header">Status Distribution</div>
                <div class="card-body">
                  <div class="row text-center">
                    <div class="col-4">
                      <h6>Campuses</h6>
                      <canvas id="campusStatusPieChart" class="status-chart"></canvas>
                    </div>
                    <div class="col-4">
                      <h6>Institutions</h6>
                      <canvas id="institutionStatusPieChart" class="status-chart"></canvas>
                    </div>
                    <div class="col-4">
                      <h6>Courses</h6>
                      <canvas id="courseStatusPieChart" class="status-chart"></canvas>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Institutions per Campus Bar Chart -->
            <div class="col-md-6">
              <div class="card h-100">
                <div class="card-header">Institutions per Campus</div>
                <div class="card-body">
                  <canvas id="institutionChart"></canvas>
                </div>
              </div>
            </div>
            <!-- Students by Institution Horizontal Bar Chart -->
            <div class="col-md-6">
              <div class="card h-100">
                <div class="card-header">Students by Institution</div>
                <div class="card-body">
                  <canvas id="studentsByInstituteChart"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- End Graphs Section -->
      </div>
      <!-- End Main Content -->
    </div>

    <!-- JavaScript: Fetch Data and Render Charts -->
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        if (!sessionStorage.getItem("token")) {
          window.location.href = "/";
        }
        fetchAllData();
      });

      const token = sessionStorage.getItem("token");

      // Global data arrays
      let campuses = [],
        institutions = [],
        courses = [],
        students = [],
        agents = [];

      // Fetch all data using view APIs
      function fetchAllData() {
        Promise.all([
          fetch("/api/get-campuses", {
            method: "GET",
            headers: { Accept: "application/json", Authorization: token },
          }).then((res) => res.json()),
          fetch("/api/view-institutions", {
            method: "GET",
            headers: { Accept: "application/json", Authorization: token },
          }).then((res) => res.json()),
          fetch("/api/view-courses", {
            method: "GET",
            headers: { Accept: "application/json", Authorization: token },
          }).then((res) => res.json()),
          fetch("/api/view-students", {
            method: "GET",
            headers: { Accept: "application/json", Authorization: token },
          }).then((res) => res.json()),
          fetch("/api/view-agents", {
            method: "GET",
            headers: { Accept: "application/json", Authorization: token },
          }).then((res) => res.json()),
        ])
          .then((results) => {
            if (results[0].status === "success") campuses = results[0].data;
            if (results[1].status === "success") institutions = results[1].data;
            if (results[2].status === "success") courses = results[2].data;
            if (results[3].status === "success") students = results[3].data;
            if (results[4].status === "success") agents = results[4].data;

            updateCountCards();
            renderEntityBarChart();
            renderCampusStatusPieChart();
            renderInstitutionStatusPieChart();
            renderCourseStatusPieChart();
            renderInstitutionChart();
            renderStudentsByInstituteChart();

            // Hide spinner and show graphs container after loading data
            document.getElementById("loadingSpinner").classList.add("d-none");
            document.getElementById("graphsContainer").classList.remove("d-none");
          })
          .catch((error) => {
            console.error("Error fetching data:", error);
          });
      }

      // Update count cards with fetched data
      function updateCountCards() {
        document.getElementById("campusCount").textContent = campuses.length;
        document.getElementById("institutionCount").textContent = institutions.length;
        document.getElementById("courseCount").textContent = courses.length;
        document.getElementById("studentCount").textContent = students.length;
        document.getElementById("agentCount").textContent = agents.length;
      }

      // Render Overall Entity Count Bar Chart
      function renderEntityBarChart() {
        const ctx = document.getElementById("entityBarChart").getContext("2d");
        new Chart(ctx, {
          type: "bar",
          data: {
            labels: [
              "Campuses",
              "Institutions",
              "Courses",
              "Students",
              "Agents",
            ],
            datasets: [
              {
                label: "Count",
                data: [
                  campuses.length,
                  institutions.length,
                  courses.length,
                  students.length,
                  agents.length,
                ],
                backgroundColor: [
                  "rgba(78, 115, 223, 0.6)",
                  "rgba(28, 200, 138, 0.6)",
                  "rgba(54, 185, 204, 0.6)",
                  "rgba(246, 194, 62, 0.6)",
                  "rgba(231, 74, 59, 0.6)",
                ],
                borderColor: [
                  "rgba(78, 115, 223, 1)",
                  "rgba(28, 200, 138, 1)",
                  "rgba(54, 185, 204, 1)",
                  "rgba(246, 194, 62, 1)",
                  "rgba(231, 74, 59, 1)",
                ],
                borderWidth: 1,
              },
            ],
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: false },
              title: {
                display: true,
                text: "Overall Entity Count",
              },
            },
            scales: {
              y: { beginAtZero: true },
            },
          },
        });
      }

      // Render Campus Status Distribution Pie Chart
      function renderCampusStatusPieChart() {
        const activeCount = campuses.filter(
          (c) => c.status === "Active"
        ).length;
        const inactiveCount = campuses.length - activeCount;
        const ctx = document
          .getElementById("campusStatusPieChart")
          .getContext("2d");
        new Chart(ctx, {
          type: "pie",
          data: {
            labels: ["Active", "Inactive"],
            datasets: [
              {
                data: [activeCount, inactiveCount],
                backgroundColor: [
                  "rgba(40, 167, 69, 0.6)",
                  "rgba(220, 53, 69, 0.6)",
                ],
                borderColor: [
                  "rgba(40, 167, 69, 1)",
                  "rgba(220, 53, 69, 1)",
                ],
                borderWidth: 1,
              },
            ],
          },
          options: {
            responsive: true,
            plugins: { legend: { display: false } },
          },
        });
      }

      // Render Institution Status Distribution Pie Chart
      function renderInstitutionStatusPieChart() {
        const activeCount = institutions.filter(
          (inst) => inst.status === "Active"
        ).length;
        const inactiveCount = institutions.length - activeCount;
        const ctx = document
          .getElementById("institutionStatusPieChart")
          .getContext("2d");
        new Chart(ctx, {
          type: "pie",
          data: {
            labels: ["Active", "Inactive"],
            datasets: [
              {
                data: [activeCount, inactiveCount],
                backgroundColor: [
                  "rgba(40, 167, 69, 0.6)",
                  "rgba(220, 53, 69, 0.6)",
                ],
                borderColor: [
                  "rgba(40, 167, 69, 1)",
                  "rgba(220, 53, 69, 1)",
                ],
                borderWidth: 1,
              },
            ],
          },
          options: {
            responsive: true,
            plugins: { legend: { display: false } },
          },
        });
      }

      // Render Course Status Distribution Pie Chart
      function renderCourseStatusPieChart() {
        const activeCount = courses.filter(
          (course) => course.status === "Active"
        ).length;
        const inactiveCount = courses.length - activeCount;
        const ctx = document
          .getElementById("courseStatusPieChart")
          .getContext("2d");
        new Chart(ctx, {
          type: "pie",
          data: {
            labels: ["Active", "Inactive"],
            datasets: [
              {
                data: [activeCount, inactiveCount],
                backgroundColor: [
                  "rgba(40, 167, 69, 0.6)",
                  "rgba(220, 53, 69, 0.6)",
                ],
                borderColor: [
                  "rgba(40, 167, 69, 1)",
                  "rgba(220, 53, 69, 1)",
                ],
                borderWidth: 1,
              },
            ],
          },
          options: {
            responsive: true,
            plugins: { legend: { display: false } },
          },
        });
      }

      // Render Institutions per Campus Bar Chart
      function renderInstitutionChart() {
        const campusMap = {};
        campuses.forEach((campus) => {
          campusMap[campus.campus_id] = campus.campus_name;
        });
        const group = {};
        institutions.forEach((inst) => {
          const campusId = inst.campus_id;
          group[campusId] = (group[campusId] || 0) + 1;
        });
        const labels = [];
        const data = [];
        for (const campusId in group) {
          labels.push(campusMap[campusId] || campusId);
          data.push(group[campusId]);
        }
        const ctx = document.getElementById("institutionChart").getContext("2d");
        new Chart(ctx, {
          type: "bar",
          data: {
            labels: labels,
            datasets: [
              {
                label: "Institutions",
                data: data,
                backgroundColor: "rgba(255, 193, 7, 0.6)",
                borderColor: "rgba(255, 193, 7, 1)",
                borderWidth: 1,
              },
            ],
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: false },
              title: {
                display: true,
                text: "Institutions per Campus",
              },
            },
            scales: {
              y: {
                beginAtZero: true,
                ticks: { stepSize: 1 },
              },
            },
          },
        });
      }

      // Render Students by Institution Chart (Horizontal Bar)
      function renderStudentsByInstituteChart() {
        const studentGroup = {};
        students.forEach((student) => {
          let instName = "N/A";
          if (student.institute) {
            try {
              const parsed = JSON.parse(student.institute);
              instName = parsed.institution_name || "N/A";
            } catch (err) {
              // Fallback if JSON parse fails
            }
          }
          studentGroup[instName] = (studentGroup[instName] || 0) + 1;
        });
        const labels = Object.keys(studentGroup);
        const data = Object.values(studentGroup);
        const ctx = document
          .getElementById("studentsByInstituteChart")
          .getContext("2d");
        new Chart(ctx, {
          type: "bar",
          data: {
            labels: labels,
            datasets: [
              {
                label: "Students",
                data: data,
                backgroundColor: "rgba(103, 58, 183, 0.6)",
                borderColor: "rgba(103, 58, 183, 1)",
                borderWidth: 1,
              },
            ],
          },
          options: {
            responsive: true,
            indexAxis: "y",
            plugins: {
              legend: { display: false },
              title: {
                display: true,
                text: "Students by Institution",
              },
            },
            scales: {
              x: {
                beginAtZero: true,
                ticks: { stepSize: 1 },
              },
            },
          },
        });
      }
    </script>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
