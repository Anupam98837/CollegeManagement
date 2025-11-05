<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Institution Dashboard</title>

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

    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <style>
      /* General styles */
      .institution-profile {
        background-color: #f7f7f7;
        padding: 1rem;
        border: 1px solid #ddd;
        border-radius: 0.25rem;
        margin-bottom: 1rem;
      }
      .search-wrapper {
        position: relative;
        margin-bottom: 1rem;
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
    </style>
  </head>
  <body>
        <div class="container my-4">
          <!-- View for a single institution (if institution_id exists in sessionStorage) -->
          <div id="singleInstitutionDashboard" class="d-none">
            <!-- Institution Profile -->
            <div id="institutionProfileDiv"></div>

            <!-- Courses Dashboard -->
            <div class="row my-4">
              <!-- Active Courses Card -->
              {{-- <div class="col-12 col-md-6 mb-3 mb-md-0">
                <div
                  class="card h-100"
                  style="background-color: #fff; border-left: 5px solid #28a745;"
                >
                  <div class="card-body text-center">
                    <h5 class="card-title text-success">
                      <i class="fa-solid fa-thumbs-up"></i> Active Courses
                    </h5>
                    <p class="card-text" id="activeCourseCount">
                      <span
                        class="spinner-border spinner-border-sm text-success"
                        role="status"
                        aria-hidden="true"
                      ></span>
                    </p>
                  </div>
                </div>
              </div> --}}
              <!-- Total Courses Card -->
              <div class="col-12">
                <div
                  class="card h-100"
                  style="background-color: #fff; border-left: 5px solid #007bff;"
                >
                  <div class="card-body text-center">
                    <h5 class="card-title text-primary">
                      <i class="fa-solid fa-book"></i> Total Courses
                    </h5>
                    <p class="card-text" id="totalCourseCount">
                      <span
                        class="spinner-border spinner-border-sm text-primary"
                        role="status"
                        aria-hidden="true"
                      ></span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <!-- Courses Table (with search) -->
            <div class="bg-white p-4 rounded mb-4">
              <div class="search-wrapper">
                <input
                  type="text"
                  id="courseSearch"
                  class="form-control"
                  placeholder="Search courses by name or code..."
                />
                <i class="fa-solid fa-search"></i>
              </div>
              <div class="table-responsive">
                <table class="table table-striped" id="coursesTable">
                  <thead>
                    <tr>
                      <th class="text-13 text-secondary">Course Name</th>
                      <th class="text-13 text-secondary">Code</th>
                      <th class="text-13 text-secondary">Board</th>
                      <th class="text-13 text-secondary">Duration</th>
                      <th class="text-13 text-secondary">Status</th>
                      <th class="text-13 text-secondary">Created At</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- View for all institutions (if no institution_id found) -->
          <div id="fullInstitutionDashboard" class="d-none">
            <!-- Institution Count Card -->
            <div class="row mb-4">
              <div class="col-12">
                <div
                  class="card h-100"
                  style="background-color: #fff; border-left: 5px solid #007bff;"
                >
                  <div class="card-body text-center">
                    <h5 class="card-title text-primary">
                      <i class="fa-solid fa-building-columns"></i> Total Institutions
                    </h5>
                    <p class="card-text" id="totalInstitutionCount">
                      <span
                        class="spinner-border spinner-border-sm text-primary"
                        role="status"
                        aria-hidden="true"
                      ></span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
            <!-- Institutions Table (with search) -->
            <div class="bg-white p-4 rounded mb-4">
              <div class="search-wrapper">
                <input
                  type="text"
                  id="institutionSearch"
                  class="form-control"
                  placeholder="Search institutions by name or type..."
                />
                <i class="fa-solid fa-search"></i>
              </div>
              <div class="table-responsive">
                <table class="table table-striped" id="institutionsTable">
                  <thead>
                    <tr>
                      <th class="text-13 text-secondary">Institution Name</th>
                      <th class="text-13 text-secondary">Campus ID</th>
                      <th class="text-13 text-secondary">Type</th>
                      <th class="text-13 text-secondary">Created At</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- End Dashboard Sections -->
        </div>
   

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        if (!sessionStorage.getItem("token")) {
          window.location.href = "/";
        }
        // Check if an institution_id is present in sessionStorage
        const institutionId = sessionStorage.getItem("institution_id");
        if (institutionId) {
          // Show single institution dashboard view
          document.getElementById("singleInstitutionDashboard").classList.remove("d-none");
          fetchInstitutionById();
          fetchInstitutionCourses();
          document.getElementById("courseSearch").addEventListener("input", filterCourses);
        } else {
          // Show full institutions dashboard view
          document.getElementById("fullInstitutionDashboard").classList.remove("d-none");
          fetchAllInstitutions();
          document.getElementById("institutionSearch").addEventListener("input", filterInstitutions);
        }
      });

      const token = sessionStorage.getItem("token");
      let institutionData = null;
      let courses = [];
      let institutions = [];

      /* ======== Single Institution Dashboard Functions ======== */
      function fetchInstitutionById() {
        const institution_id = sessionStorage.getItem("institution_id");
        fetch(`/api/view-institution/${institution_id}`, {
          method: "GET",
          headers: {
            Accept: "application/json",
            Authorization: token,
          },
        })
          .then((response) => {
            if (response.status === 401 || response.status === 403) {
              window.location.href = "/Unauthorised";
              throw new Error("Unauthorized Access");
            }
            return response.json();
          })
          .then((data) => {
            if (data.status === "success") {
              institutionData = data.data;
              updateInstitutionProfile(institutionData);
            } else {
              console.error("Error fetching institution details:", data.message);
            }
          })
          .catch((error) => console.error("Error:", error));
      }

      function updateInstitutionProfile(inst) {
        const profileDiv = document.getElementById("institutionProfileDiv");
        const logoPath = inst.logo 
            ? `/${inst.logo}` 
            : '/assets/web_assets/logo.png';

        profileDiv.innerHTML = `
          <div class="institution-profile">
            
            <h3><img src="${logoPath}" alt="Institution Logo" width="100px" style="object-fit: contain; display: block;" />  ${inst.institution_name}</h3>
            <p><strong>Campus ID:</strong> ${inst.campus_id}</p>
            <p><strong>Type:</strong> ${inst.type}</p>
            <p><strong>Address:</strong> ${inst.street}, ${inst.city}, ${inst.state}, ${inst.country} - ${inst.pincode}</p>
            <p><strong>Contact:</strong> ${inst.contact_no} | <strong>Email:</strong> ${inst.email_id}</p>
            <p><strong>Website:</strong> <a href="${inst.url}" target="_blank">${inst.url}</a></p>
            <p><strong>Status:</strong> ${inst.status}</p>
            <p><strong>Created At:</strong> ${new Date(inst.created_at).toLocaleString()}</p>
          </div>
        `;
      }

      function fetchInstitutionCourses() {
        const institution_id = sessionStorage.getItem("institution_id");
        fetch(`/api/view-institution-courses?institution_id=${institution_id}`, {
          method: "GET",
          headers: {
            Accept: "application/json",
            Authorization: token,
          },
        })
          .then((response) => {
            if (response.status === 401 || response.status === 403) {
              window.location.href = "/Unauthorised";
              throw new Error("Unauthorized Access");
            }
            return response.json();
          })
          .then((data) => {
            if (data.status === "success") {
              courses = data.data;
              updateCourseCounts();
              populateCoursesTable();
            } else {
              console.error("Error fetching courses:", data.message);
            }
          })
          .catch((error) => console.error("Error:", error));
      }

      function updateCourseCounts() {
        const activeCourses = courses.filter((course) => course.status === "Active");
        // document.getElementById("activeCourseCount").textContent = activeCourses.length;
        document.getElementById("totalCourseCount").textContent = courses.length;
      }

      function populateCoursesTable() {
        const tbody = document.getElementById("coursesTable").querySelector("tbody");
        tbody.innerHTML = "";
        if (courses.length === 0) {
          tbody.innerHTML = `<tr><td colspan="6" class="text-center">No courses found.</td></tr>`;
          return;
        }
        courses.forEach((course) => {
          tbody.innerHTML += `
            <tr class="text-13">
              <td>${course.program_name}</td>
              <td>${course.program_code}</td>
              <td>${course.board}</td>
              <td>${course.program_duration}</td>
              <td>${course.status}</td>
              <td>${new Date(course.created_at).toLocaleString()}</td>
            </tr>
          `;
        });
      }

      function filterCourses() {
        const query = document.getElementById("courseSearch").value.toLowerCase();
        const rows = document.getElementById("coursesTable").querySelectorAll("tbody tr");
        rows.forEach((row) => {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(query) ? "" : "none";
        });
      }

      /* ======== Full Institutions Dashboard Functions ======== */
      function fetchAllInstitutions() {
        fetch("/api/view-institutions", {
          method: "GET",
          headers: {
            Accept: "application/json",
            Authorization: token,
          },
        })
          .then((response) => {
            if (response.status === 401 || response.status === 403) {
              window.location.href = "/Unauthorised";
              throw new Error("Unauthorized Access");
            }
            return response.json();
          })
          .then((data) => {
            if (data.status === "success") {
              institutions = data.data;
              updateInstitutionCount();
              populateInstitutionsTable();
            } else {
              console.error("Error fetching institutions:", data.message);
            }
          })
          .catch((error) => console.error("Error:", error));
      }

      function updateInstitutionCount() {
        const total = institutions.length;
        document.getElementById("totalInstitutionCount").textContent = total;
      }

      function populateInstitutionsTable() {
        const tbody = document.getElementById("institutionsTable").querySelector("tbody");
        tbody.innerHTML = "";
        if (institutions.length === 0) {
          tbody.innerHTML = `<tr><td colspan="4" class="text-center">No institutions found.</td></tr>`;
          return;
        }
        institutions.forEach((inst) => {
          tbody.innerHTML += `
            <tr class="text-13">
              <td>${inst.institution_name}</td>
              <td>${inst.campus_id}</td>
              <td>${inst.type}</td>
              <td>${new Date(inst.created_at).toLocaleString()}</td>
            </tr>
          `;
        });
      }

      function filterInstitutions() {
        const query = document.getElementById("institutionSearch").value.toLowerCase();
        const rows = document.getElementById("institutionsTable").querySelectorAll("tbody tr");
        rows.forEach((row) => {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(query) ? "" : "none";
        });
      }
    </script>
  </body>
</html>
