<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Course Dashboard</title>

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
      /* Search input wrapper for icon positioning */
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
          <!-- Institution Info Banner (shown if institution details exist in sessionStorage) -->
         
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


          <!-- Top Row: 3 Cards for Course Counts -->
          <div class="row my-4">
            <!-- Active Courses Card -->
            <div class="col-12 col-md-4 mb-3 mb-md-0">
              <div class="card h-100" style="background-color: #fff; border-left: 5px solid #28a745;">
                <div class="card-body d-flex flex-column justify-content-center text-center">
                  <h5 class="card-title text-success">
                    <i class="fa-solid fa-thumbs-up"></i> Active Courses
                  </h5>
                  <p class="card-text" id="activeCourseCount">
                    <span class="spinner-border spinner-border-sm text-success" role="status" aria-hidden="true"></span>
                  </p>
                </div>
              </div>
            </div>
            <!-- Inactive Courses Card -->
            <div class="col-12 col-md-4 mb-3 mb-md-0">
              <div class="card h-100" style="background-color: #fff; border-left: 5px solid #dc3545;">
                <div class="card-body d-flex flex-column justify-content-center text-center">
                  <h5 class="card-title text-danger">
                    <i class="fa-solid fa-thumbs-down"></i> Inactive Courses
                  </h5>
                  <p class="card-text" id="inactiveCourseCount">
                    <span class="spinner-border spinner-border-sm text-danger" role="status" aria-hidden="true"></span>
                  </p>
                </div>
              </div>
            </div>
            <!-- Total Courses Card -->
            <div class="col-12 col-md-4">
              <div class="card h-100" style="background-color: #fff; border-left: 5px solid #007bff;">
                <div class="card-body d-flex flex-column justify-content-center text-center">
                  <h5 class="card-title text-primary">
                    <i class="fa-solid fa-book"></i> Total Courses
                  </h5>
                  <p class="card-text" id="totalCourseCount">
                    <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
                  </p>
                </div>
              </div>
            </div>
          </div>

          <!-- Courses Accordion Container -->
          <div class="bg-white p-4 rounded mb-4">
            <!-- Search Box for Courses Accordion -->
            <div class="search-wrapper">
              <input
                type="text"
                id="accordionSearchCourse"
                class="form-control"
                placeholder="Search courses by name or code..."
              />
              <i class="fa-solid fa-search"></i>
            </div>
            <!-- Accordion: Courses grouped by course type -->
            <div class="accordion accordion-scroll" id="courseAccordion">
              <!-- Accordion items will be generated dynamically -->
            </div>
          </div>
        </div>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      // Check for token and initialize dashboard
      document.addEventListener("DOMContentLoaded", function () {
        if (!sessionStorage.getItem("token")) {
          window.location.href = "/";
        }
        initializeDashboard();
        // If institution details exist, display the banner
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
        document.getElementById("accordionSearchCourse").addEventListener("input", filterCourseAccordion);
      });

      const token = sessionStorage.getItem("token");
      let courses = [];
      // Group courses by program_type (keys: UG, PG, DIPLOMA, ITI, OTHER)
      let courseGroups = { UG: [], PG: [], DIPLOMA: [], ITI: [], OTHER: [] };

      function initializeDashboard() {
        fetchCourses();
      }

      function fetchCourses() {
        // If an institution_id is in sessionStorage, fetch its courses; otherwise fetch all courses.
        let url = "";
        const institution_id = sessionStorage.getItem("institution_id");
        if (institution_id) {
          url = "/api/view-institution-courses?institution_id=" + institution_id;
        } else {
          url = "/api/view-courses";
        }
        fetch(url, {
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
              groupCoursesByType();
              populateCourseAccordion();
            } else {
              console.error("Error fetching courses:", data.message);
            }
          })
          .catch((error) => console.error("Error:", error));
      }

      function updateCourseCounts() {
        const activeCourses = courses.filter(course => course.status === "Active");
        const inactiveCourses = courses.filter(course => course.status !== "Active");
        const total = courses.length;
        document.getElementById("activeCourseCount").textContent = activeCourses.length;
        document.getElementById("inactiveCourseCount").textContent = inactiveCourses.length;
        document.getElementById("totalCourseCount").textContent = total;
      }

      function groupCoursesByType() {
        // Reset courseGroups
        courseGroups = { UG: [], PG: [], DIPLOMA: [], ITI: [], OTHER: [] };
        courses.forEach(course => {
          let type = course.program_type ? course.program_type.toUpperCase() : "OTHER";
          if (courseGroups.hasOwnProperty(type)) {
            courseGroups[type].push(course);
          } else {
            courseGroups.OTHER.push(course);
          }
        });
      }

      function populateCourseAccordion() {
        const accordion = document.getElementById("courseAccordion");
        accordion.innerHTML = "";
        // Define a custom order: PG always on top, then the rest.
        const groupOrder = ["PG", "UG", "DIPLOMA", "ITI", "OTHER"];
        groupOrder.forEach(type => {
          if (courseGroups[type] && courseGroups[type].length > 0) {
            // Expand PG group by default.
            const isFirst = type === "PG";
            const item = document.createElement("div");
            item.classList.add("accordion-item");
            item.innerHTML = `
              <h2 class="accordion-header" id="heading${type}">
                <button class="accordion-button ${isFirst ? "" : "collapsed"} text-13" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${type}" aria-expanded="${isFirst ? "true" : "false"}" aria-controls="collapse${type}">
                  ${type} Courses (<span id="count${type}">${courseGroups[type].length}</span>)
                </button>
              </h2>
              <div id="collapse${type}" class="accordion-collapse collapse ${isFirst ? "show" : ""}" aria-labelledby="heading${type}" data-bs-parent="#courseAccordion">
                <div class="accordion-body p-2" id="list${type}">
                  <!-- Table for ${type} courses will be injected here -->
                </div>
              </div>
            `;
            accordion.appendChild(item);

            // Build table HTML for this course group.
            let tableHTML = `
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th class="text-13 text-secondary">Course Name</th>
                      <th class="text-13 text-secondary">Code</th>
                      <th class="text-13 text-secondary">Board</th>
                      <th class="text-13 text-secondary">Duration</th>
                      <th class="text-13 text-secondary">Status</th>
                    </tr>
                  </thead>
                  <tbody>
            `;
            courseGroups[type].forEach(course => {
              tableHTML += `
                <tr class="course-row text-13" data-course-name="${course.program_name.toLowerCase()}" data-course-code="${course.program_code.toLowerCase()}">
                  <td>${course.program_name}</td>
                  <td>${course.program_code}</td>
                  <td>${course.board}</td>
                  <td>${course.program_duration} Year</td>
                  <td style="color: ${course.status === 'Active' ? 'green' : 'red'};">${course.status}</td>
                </tr>
              `;
            });
            tableHTML += `
                  </tbody>
                </table>
              </div>
            `;
            document.getElementById(`list${type}`).innerHTML = tableHTML;
          }
        });
      }

      function filterCourseAccordion() {
        const query = document.getElementById("accordionSearchCourse").value.toLowerCase();
        const rows = document.querySelectorAll("tr.course-row");
        rows.forEach(row => {
          const name = row.getAttribute("data-course-name");
          const code = row.getAttribute("data-course-code");
          row.style.display = (name.includes(query) || code.includes(query)) ? "" : "none";
        });
      }
    </script>
  </body>
</html>
